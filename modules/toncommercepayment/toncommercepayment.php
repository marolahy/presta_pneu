<?php
if (!defined('_PS_VERSION_'))
  exit;
if(!class_exists('Curl'))
    require_once dirname(__FILE__).'/classes/Curl.php';
class Toncommercepayment extends PaymentModule
{
    protected $_html = '';
    protected $_postErrors = array();
    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;
    protected $toncommerce_products = array();
    const API_BASE_URL = 'https://api.toncommerce.net/';
    public function __construct()
    {
        $this->name = 'toncommercepayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6.99.99');
        $this->author = 'Bezama Marolahy Randriamifidy';
        $this->controllers = array('validation');
        $this->is_eu_compatible = 1;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ton Commerce Payment Module');
        $this->description = $this->l('Ton Commerce Payment Module');
        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }
    }
    public function install()
    {
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_payment_cart` (
            `id_toncommerce_payment_cart` int(11) NOT NULL AUTO_INCREMENT,
            `id_cart` int(11),
            `id_payment` int(11),
            `token` VARCHAR(255),
            `cart_content` TEXT,
            PRIMARY KEY  (`id_toncommerce_payment_cart`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_payment_order` (
            `id_toncommerce_order` int(11) NOT NULL AUTO_INCREMENT,
            `id_order` int(11),
            `id_payment` int(11),
            `active` int(1),
            PRIMARY KEY  (`id_toncommerce_order`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $this->updateToncommerceOrderStates();
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        if (!parent::install() || !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn')) {
            return false;
        }
        return true;
    }
     public function uninstall()
    {
        $sql = array();
        $sql = array();
        $sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_payment_cart`;';
        $sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_payment_order`;';
        Configuration::deleteByName('TONCOMMERCE_OS_attente');
        Configuration::deleteByName('TONCOMMERCE_OS_recu');
        Configuration::deleteByName('TONCOMMERCE_OS_partiellement_rembourse');
        Configuration::deleteByName('TONCOMMERCE_OS_rembourse');
        Configuration::deleteByName('TONCOMMERCE_OS_opposition');
        Configuration::deleteByName('TONCOMMERCE_OS_preparation');
        Configuration::deleteByName('TONCOMMERCE_OS_livre');
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        return parent::uninstall();
    }
        protected function saveOrderState($config, $color, $names, $setup)
    {
        $state_id = Configuration::get($config);

        if ((bool)$state_id == true) {
            $order_state = new OrderState($state_id);
        } else {
            $order_state = new OrderState();
        }

        $order_state->name = $names;
        $order_state->color = $color;

        foreach ($setup as $param => $value) {
            $order_state->{$param} = $value;
        }

        if ((bool)$state_id == true) {
            return $order_state->save();
        } elseif ($order_state->add() == true) {
            Configuration::updateValue($config, $order_state->id);
            //@copy($this->local_path . 'logo.gif', _PS_ORDER_STATE_IMG_DIR_ . (int)$order_state->id . '.gif');

            return true;
        }
        return false;
    }

    public function updateToncommerceOrderStates()
    {
        $waiting_state_config = 'TONCOMMERCE_OS_attente';
        $waiting_state_color = '#4169E1';
        $waiting_state_names = [];

        $setup = [
            'delivery' => false,
            'hidden' => false,
            'invoice' => false,
            'logable' => false,
            'module_name' => $this->name,
            'send_email' => false,
        ];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $waiting_state_names[(int)$language['id_lang']] = 'en attente de paiement';
            } else {
                $waiting_state_names[(int)$language['id_lang']] = 'Waiting for payment';
            }
        }

        $this->saveOrderState($waiting_state_config, $waiting_state_color, $waiting_state_names, $setup);

        


        $total_state_config = 'TONCOMMERCE_OS_partiellement_rembourse';
        $total_state_color = '#EF4E15';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'Partiellement remboursé';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'Partially refunded';
            }
        }

        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);
        $total_state_config = 'TONCOMMERCE_OS_rembourse';
        $total_state_color = '#ECCD15';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'Totalement remboursé';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'Totally refunded';
            }
        }

        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);
        $total_state_config = 'TONCOMMERCE_OS_opposition';
        $total_state_color = '#DB014A';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'En opposition';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'Payment in challenge';
            }
        }

        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);

         $total_state_config = 'TONCOMMERCE_OS_annule';
        $total_state_color = '#423C10';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'annuler';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'Livraison';
            }
        }

        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);


        $setup = [
            'delivery' => true,
            'hidden' => false,
            'invoice' => true,
            'logable' => false,
            'module_name' => $this->name,
            'send_email' => false,
        ];
        $total_state_config = 'TONCOMMERCE_OS_livre';
        $total_state_color = '#EC2ECC';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'Livrer';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'Livraison';
            }
        }
        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);

        $total_state_config = 'TONCOMMERCE_OS_expedie';
        $total_state_color = '#0392EE';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'expedier';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'expedier';
            }
        }

        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);


        $total_state_config = 'TONCOMMERCE_OS_preparation';
        $total_state_color = '#A3AC9D';
        $total_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $total_state_names[(int)$language['id_lang']] = 'preparation';
            } else {
                $total_state_names[(int)$language['id_lang']] = 'expedier';
            }
        }


        $this->saveOrderState($total_state_config, $total_state_color, $total_state_names, $setup);

        $partial_state_config = 'TONCOMMERCE_OS_recu';
        $partial_state_color = '#EC2ff5';
        $partial_state_names = [];

        foreach (Language::getLanguages(false) as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $partial_state_names[(int)$language['id_lang']] = 'Paiement reçus';
            } else {
                $partial_state_names[(int)$language['id_lang']] = 'Payed';
            }
        }

        $this->saveOrderState($partial_state_config, $partial_state_color, $partial_state_names, $setup);



        return true;
    }
    protected function checkProducts($products)
    {
       foreach($products as $product){
          $query = "SELECT reference FROM `"._DB_PREFIX_."toncommerce_product_mapping` WHERE id_product = ".$product['id_product'];
          $row = Db::getInstance()->getRow($query);
          if( $row ){
            return true;
          }
        }
        return false;
    }
    public function checkIfHaveTonCommerceProduct( $cart )
    {
        $products = $cart->getProducts();
        foreach ($products as $product) {
          $this->toncommerce_products[] = array('id_product'=>$product['id_product'],'id_attribute'=>$product['id_product_attribute']);
        }
        return $this->checkProducts($this->toncommerce_products);

    }
    public function hookPayment($params)
    {
      if( !$this->context->customer->isLogged())
        return ;
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        //if (!$this->checkIfHaveTonCommerceProduct($params['cart']))
        //    return;

        $this->smarty->assign(array(
            'this_path' => $this->_path,
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }
    public function hookDisplayPaymentEU($params){

    }
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
     public function getProduct($reference)
    {
        $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'articles/'.$reference);
        $response = Tools::jsonDecode($curl->response);
        if($response->statut === "ok" ){
            return $response->article;
        }
        return false;
    }
     public function getExternalPaymentOption()
    {
        $offlineOption = new PaymentOption();
        $offlineOption->setCallToActionText($this->l('Toncommerce Payment'))
                      ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                      ->setAdditionalInformation($this->context->smarty->fetch('module:toncommercepayment/views/templates/front/payment_infos.tpl'))
                      ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.jpg'));
        return $offlineOption;
    }
    public function getOfflinePaymentOption()
    {
        $offlineOption = new PaymentOption();
        $offlineOption->setCallToActionText($this->l('Toncommerce Payment'))
                      ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                      ->setAdditionalInformation($this->context->smarty->fetch('module:toncommercepayment/views/templates/front/payment_infos.tpl'))
                      ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.jpg'));
        return $offlineOption;
    }
    public function getEmbeddedPaymentOption($params)
    {
        $embeddedOption = new PaymentOption();
        $embeddedOption->setCallToActionText($this->l('Ton commerce Payment'))
                       ->setForm($this->generateForm($params))
                       ->setAdditionalInformation($this->context->smarty->fetch('module:toncommercepayment/views/templates/front/payment_infos.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/payment.jpg'));

        return $embeddedOption;
    }

    
    
}
