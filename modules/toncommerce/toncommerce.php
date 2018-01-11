<?php

if (!defined('_PS_VERSION_'))

{

  exit;

}

if(!class_exists("TonCommerceApi"))

    require_once dirname(__FILE__).'/classes/Api.php';



class Toncommerce extends Module{

	public function __construct(){

    $this->name = 'toncommerce';

    $this->tab = 'merchandizing';

    $this->version = '1.0.0';

    $this->author = 'Bezama Marolahy Randriamifidy';

    $this->need_instance = 0;

    $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);

    $this->bootstrap = true;



    parent::__construct();



    $this->displayName = $this->l('TonCommerce Tools import product');

    $this->description = $this->l('TonCommerce Tools import product');



    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

  }

    public function install()

    {

        Configuration::updateValue('TONCOMMERCE_API_KEY', null);

        Configuration::updateValue('TONCOMMERCE_ACCOUNT_URL', null);

        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_categories` (

            `id_toncommerce_category` int(11) NOT NULL AUTO_INCREMENT,

            `id_category_from_ton_commerce` int(11),

            `load_product` boolean,

            PRIMARY KEY  (`id_toncommerce_category`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';



        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_categories_mapping` (

            `id_toncommerce_category` int(11) NOT NULL AUTO_INCREMENT,

            `id_category_from_ton_commerce` int(11),

            `id_category` int(11),

            PRIMARY KEY  (`id_toncommerce_category`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';







        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_product_mapping` (

            `id_toncommerce_product` int(11) NOT NULL AUTO_INCREMENT,

            `id_product` int(11),

            `reference` VARCHAR(255),

            `prix` DOUBLE, 

            `commission` DOUBLE, 

            `date_add` DATE,

            `date_upt` DATE,

            PRIMARY KEY  (`id_toncommerce_product`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_product_attribute_mapping` (

            `id_toncommerce_product_attribute` int(11) NOT NULL AUTO_INCREMENT,

            `reference_declinaison` VARCHAR(255),
            `id_product` INT(11),

            `id_attribute` int(11),

            `id_declinaison` int(11), 

            `date_add` DATE,

            `date_upt` DATE,

            PRIMARY KEY  (`id_toncommerce_product_attribute`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_stock` (

            `id_toncommerce_stock` int(11) NOT NULL AUTO_INCREMENT,

            `reference_declinaison` int(11),

            `stock` int(11),

            PRIMARY KEY  (`id_toncommerce_stock`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';



        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_image_mapping` (

            `id_toncommerce_image` int(11) NOT NULL AUTO_INCREMENT,

            `id_image` int(11),

            `id_image_website` int(11),

            PRIMARY KEY  (`id_toncommerce_image`)

        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';



        //$sql[] = 'ALTER TABLE ' . _DB_PREFIX_ . 'product ADD `is_toncommerce` TINYINT(1)';





        foreach ($sql as $query) {

            if (Db::getInstance()->execute($query) == false) {

                return false;

            }

        }

        @copy(__DIR__.'/override/controllers/front/PdfInvoiceController.php', _PS_ROOT_DIR_.'/override/controllers/front/');



        foreach ($sql as $query) {

            if (Db::getInstance()->execute($query) == false) {

                return false;

            }

        }

         return parent::install() && $this->registerHook('backOfficeHeader')  && $this->registerHook('actionProductSave') 

        && $this->registerHook('actionCartSave') && $this->registerHook('actionBeforeCartUpdateQty');







    }

    public function hookactionBeforeCartUpdateQty($params){

        /*

        $id_product = $params['id_product'];

        $id_product_attribute = $params['id_product_attribute'];

        $quantity = $params['quantity'];

        $operator = $params['operator'];

        $id_customization =  $params['id_customization'];

        $cart = $params['cart'];

        $id_address_delivery = $params['id_address_delivery'];

        $shop = $params['shop'];

        $auto_add_cart_rule = $params['auto_add_cart_rule'];

        $result = $cart->containsProduct($id_product, $id_product_attribute, (int)$id_customization, (int)$id_address_delivery);

        $api = new ToncommerceApi();

        if($result){

            if ($operator == 'up') {

                $new_qty = (int)$result['quantity'] + (int)$quantity;

                $this->checkTonCommerceQuantity($id_product,$id_product_attribute,$new_qty);

            }



        }else{

            $this->checkTonCommerceQuantity($id_product,$id_product_attribute,$quantity);

        }

        */

    }

    public function checkTonCommerceQuantity($id_product,$id_attribute,$quantity){

        if(!$id_product)

            return ;



        $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_mapping` WHERE id_product = ".$id_product;

        $row = Db::getInstance()->getRow( $query );

        if(!$row)

            return ;

        $api = new ToncommerceApi();

        $article = $api->getProduct($row['reference']);

        $test = false;

        if($id_attribute > 0 ){

            $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_attribute_mapping` WHERE id_product ='".$id_product."' AND id_attribute ='".$id_attribute."'";

             $product_attribute = Db::getInstance()->getRow($query);

             if(!$product_attribute)

                return false;

             $qty =  $api->getQuantityWithDeclinaison($article->reference,$product_attribute['id_declinaison']);

             StockAvailable::setQuantity($id_product, $product_attribute['id_attribute'],$qty);

             StockAvailable::updateQuantity($id_product, $product_attribute['id_attribute'],0);

            if( (int)$qty < (int)$quantity ){

                $test = true;

            }



        }else{

            $qty = $api->getQuantityWithoutDeclinaison($article->reference);

            StockAvailable::setQuantity($id_product, 0,$qty);

            StockAvailable::updateQuantity($id_product, 0,0);

            if( (int)$qty < (int)$quantity ){

                $test = true;

            }

        }

        if( $test ===true ){

            header('X-PHP-Response-Code: 404', true, 404);

            die(json_encode(array( 

                    "hasError"=>true,

                    "errors"=>array(Tools::displayError("There are not enough products in stock")),

                )

            ));

        }

        return ; 

    }

    public function hookactionCartSave( $params ){

        if(is_null($params['cart']))

            return ;

        $cart = $params['cart'];

        $products =  $cart->getProducts();

        $api = new ToncommerceApi();

        $test2 = false;

        foreach($products as $product){

            $this->checkTonCommerceQuantity($product['id_product'],$product['id_product_attribute'],$product['cart_quantity']);

        }



        /*

        route delete 0.0.0.0

        route add 0.0.0.0 mask 0.0.0.0 192.168.50.2

        

        */

        /*

        route delete 0.0.0.0

        route add 0.0.0.0 mask 0.0.0.0 192.168.50.2

        

        */

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

    public function hookActionProductSave($params)

    {

        

        $id_product = $params['id_product'];

        $price = $params['product']->price;

        $query = 'SELECT *  FROM `'._DB_PREFIX_.'toncommerce_product_mapping` WHERE id_product=\''.$id_product.'\'';

        $row = Db::getInstance()->getRow($query);

        if( $row ){

            $api = new ToncommerceApi();

            $article = $api->getProduct($row['reference']);

            if( $article){

                $test = ( $price >= ( (float)$article->prix_ht - (float)$article->com_ht) 

                    && $price <= ((float)$article->prix_ht + (float)$article->com_ht) );

                if( !$test  ) {

                    $message  = "le prix doit etre comprise entre ".((float)$article->prix_ht + (float)$article->com_ht) ." et ".((float)$article->prix_ht - (float)$article->com_ht);

                        header('X-PHP-Response-Code: 404', true, 404);

                    $params['product']->price = (float)$article->prix_ht;

                    $params['product']->save();

                    $this->context->controller->errors[] = Tools::displayError($message);

                }

                

            }

        }

        

    }



     public function hookBackOfficeHeader()

    {

        if (Tools::getValue('configure') == $this->name) {

            

            $this->context->controller->addCSS($this->_path.'views/css/jquery.treegrid.css');

            $this->context->controller->addjQuery();

            $this->context->controller->addJS($this->_path.'views/js/jquery.treegrid.min.js');

            $this->context->controller->addJS($this->_path.'views/js/function.js');

        }

    }







    public function uninstall()

    {

        Configuration::deleteByName('TONCOMMERCE_API_KEY');

        Configuration::deleteByName('TONCOMMERCE_ACCOUNT_URL');

        $sql = array();

        /*$sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_categories`;';

        $sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_product_mapping`;';

        $sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_image_mapping`;';

        $sql[] = 'DROP TABLE IF  EXISTS `'._DB_PREFIX_.'toncommerce_product_attribute_mapping`;';

        

        foreach ($sql as $query) {

            if (Db::getInstance()->execute($query) == false) {

                return false;

            }

        }*/

        return parent::uninstall();

    }

    public function saveCategories()

    {

        $categories = Tools::getValue('activeCategories');

        Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'toncommerce_categories`');

        foreach ($categories as $key => $value) {

            Db::getInstance()->insert('toncommerce_categories',array(

                'id_category_from_ton_commerce' => $value,

                'load_product' => true,

            ));

        }

    }

    public  function constructDataTable($data,$parent_id){

        $output = [];

        foreach ($data as $value) {

            if($value->parent == $parent_id){

                $output = array_merge($output,[$value]);

                $output = array_merge( $output,$this->constructDataTable($data,$value->id) );

            }

        }

        return $output;

    }

    public function getMinParent($data){

        $i = 1000;

        foreach ($data as $value) {

            if((int)$value->parent < $i )

                $i = (int)$value->parent;

            }

        return $i;

    }

     public function getContent(){

        if (((bool)Tools::isSubmit('submitToncommerceModule')) == true) {

            $this->postProcess();

        }

        if (((bool)Tools::isSubmit('submitToncommerceCategories')) == true) {

            $query = 

            $this->saveCategories();

        }

        

        $api_key=Configuration::get('TONCOMMERCE_API_KEY', '');

        $api = new ToncommerceApi();

        $categories = $api->getCategories();



        if (((bool)Tools::isSubmit('submitSaveAndEraseCategories')) == true) {

            $mycat = $this->constructDataTable($categories,$this->getMinParent($categories));

            $this->insertCategory($mycat);

        }

        $categories_to_check = $api->getCategoriestoImport();

        $categories_to_print = $categories === false ? false : array();

        $i = 0;

        foreach ($categories as $value) {

            if(in_array($value->id, $categories_to_check)){

                $std = new StdClass;

                $std->id = $value->id;

                $std->nom = $value->nom;

                $std->checked = true;

                $std->nb_articles = (int)$value->nb_articles;

                $std->parent = (int)$value->parent;

                $categories_to_print[$i] = $std;

            }

            else{

                $std = new StdClass;

                $std->id = $value->id;

                $std->nom = $value->nom;

                $std->checked = false;

                $std->parent = (int)$value->parent;

                $std->nb_articles = (int)$value->nb_articles;

                $categories_to_print[$i] = $std;

            }

            $i++;

        }

        $min_parent = $this->getMinParent($categories_to_print);

        $categories_to_print = $this->constructDataTable($categories_to_print,$min_parent);

        $app_baseurl='https://api.toncommerce.net/';

        $cron_url = Tools::getHttpHost(true, true).__PS_BASE_URI__.

            'modules/toncommerce/cron.php?token='.substr(_COOKIE_KEY_, 34, 8);

        $currentIndex = $this->context->link->getAdminLink('AdminModules', false)

            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');

        $tpl_array=array('api_key' => $api_key,

                'url' => $app_baseurl,

                'categories'=>$categories_to_print,

                'url_to_submit' => $currentIndex,

                'cron_url'=>Tools::safeOutput($cron_url)

                );

        $this->context->smarty->assign($tpl_array, $this->_path);



        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');



        return $output.$this->renderForm();

    }





    protected function renderForm()

    {

        $helper = new HelperForm();



        $helper->show_toolbar = false;

        $helper->table = $this->table;

        $helper->module = $this;

        $helper->default_form_language = $this->context->language->id;

        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);



        $helper->identifier = $this->identifier;

        $helper->submit_action = 'submitToncommerceModule';

        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)

            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');



        $helper->tpl_vars = array(

            'fields_value' => $this->getConfigFormValues(),

            'languages' => $this->context->controller->getLanguages(),

            'id_language' => $this->context->language->id,

        );



        return $helper->generateForm(array($this->getConfigForm()));

    }

    protected function getConfigForm()

    {

        return array(

            'form' => array(

                'legend' => array(

                'title' => $this->l('Settings'),

                'icon' => 'icon-cogs',

                ),

                'input' => array(

                    array(

                        'col' => 4,

                        'type' => 'text',

                        'prefix' => '<i class="icon icon-user"></i>',

                        'desc' => $this->l('Your toncommerce Api Key'),

                        'name' => 'TONCOMMERCE_API_KEY',

                        'label' => $this->l('API KEY'),

                    ),

                    ),

                'submit' => array(

                    'title' => $this->l('Save'),

                ),

            ),

        );

    }



    protected function getConfigFormValues()

    {

        $app_url=$this->context->link->getPageLink('index', true);

        if (Configuration::get('TONCOMMERCE_API_KEY', '')!='') {

            $admin_key=Configuration::get('TONCOMMERCE_API_KEY', '');

        }

        if (Configuration::get('TONCOMMERCE_ACCOUNT_URL', '')!='') {

            $app_url=Configuration::get('TONCOMMERCE_ACCOUNT_URL', '');

        }

        return array(

            'TONCOMMERCE_API_KEY' =>$admin_key,

            'TONCOMMERCE_ACCOUNT_URL' => $app_url,

        );

    }



    protected function postProcess()

    {

            Configuration::updateGlobalValue('TONCOMMERCE_API_KEY', Tools::getValue('TONCOMMERCE_API_KEY'));

    }

    public function hookDisplayAdminProductsExtra()

    {

    	//return $this->display(__FILE__, 'views/template/admin/productTab.tpl');



    }

    public function getCategoriestoImport(){

        $query = 'SELECT * FROM `'._DB_PREFIX_.'toncommerce_categories`';

        $categories = array();

        $rows = Db::getInstance()->executeS($query);

        foreach ($rows as $row) {

            $categories[] = $row['id_category_from_ton_commerce'];

        }

        return $categories;

    }

    public function insertCategory($categories){



        $category_to_import = $this->getCategoriestoImport();



        foreach ($categories as $value) {

            if(!in_array($value->id,$category_to_import))

                    continue;

        $parent_id = $this->getParentId($categories,$value->id);

        foreach ($categories as $toncommerce_category) {

            if(!in_array($toncommerce_category->id,$parent_id) )

                continue;

            $sql= 'SELECT * FROM '._DB_PREFIX_.'toncommerce_categories_mapping WHERE id_category_from_ton_commerce='.$toncommerce_category->id;

            $row = Db::getInstance()->getRow($sql);

            $id_lang = Context::getContext()->language->id;

            if( $row ){

              $category = new Category($row['id_category'],$id_lang);

              $category->name = $toncommerce_category->nom;

              $category->description = $toncommerce_category->meta_desc;

              $category->meta_description = $toncommerce_category->meta_desc;

              $category->meta_title = $toncommerce_category->meta_title;

              $category->meta_keywords = $toncommerce_category->meta_keywords;

              $category->link_rewrite = $toncommerce_category->var_url;

              $category->save();



            }else{

              if($toncommerce_category->parent == 0 ){

                $category = new Category(null,$id_lang);

                $category->id_parent = Category::getRootCategory()->id;

                $category->name = $toncommerce_category->nom;

                $category->description = $toncommerce_category->meta_desc;

                $category->meta_description = $toncommerce_category->meta_desc;

                $category->meta_title = $toncommerce_category->meta_title;

                $category->meta_keywords = $toncommerce_category->meta_keywords;

                $category->link_rewrite = $toncommerce_category->var_url;

                $category->save();

                Db::getInstance()->insert('toncommerce_categories_mapping',array(

                    'id_category_from_ton_commerce' => $toncommerce_category->id,

                    'id_category' => $category->id,

                  ));

              }else{ 



                $sql= 'SELECT * FROM '._DB_PREFIX_.'toncommerce_categories_mapping WHERE id_category_from_ton_commerce='.$toncommerce_category->parent;

                $data = Db::getInstance()->getRow($sql);

                if($data){

                  $id_parent = $data['id_category'];

                }else{

                  $id_parent = Category::getRootCategory()->id;

                }

                $category = new Category(null,$id_lang);

                $category->id_parent = $id_parent;

                $category->name = $toncommerce_category->nom;

                $category->description = $toncommerce_category->meta_desc;

                $category->meta_description = $toncommerce_category->meta_desc;

                $category->meta_title = $toncommerce_category->meta_title;

                $category->meta_keywords = $toncommerce_category->meta_keywords;

                $category->link_rewrite = $toncommerce_category->var_url;

                $category->save();

                Db::getInstance()->insert('toncommerce_categories_mapping',array(

                    'id_category_from_ton_commerce' => $toncommerce_category->id,

                    'id_category' => $category->id,

                  ));

              }

                

            }

            }

                

        }



    }

    public function getParentId($data, $category_id)

    {

        $output = [];

        foreach ($data as $value) {

            if($value->id == $category_id){

                $output = array_merge($output,[$category_id]);

                if($value->parent != 0 )

                $output = array_merge( $output,$this->getParentId($data,$value->parent) );

            }

        }

        return $output;

    }



}