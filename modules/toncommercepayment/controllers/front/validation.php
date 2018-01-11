<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ToncommercepaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
                $cart = new Cart(Tools::getValue('cart_id',0));
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        $token = Tools::getValue('token','');
        $cart_id = Tools::getValue('cart_id',0);
        $query = "SELECT * FROM "._DB_PREFIX_."toncommerce_payment_cart WHERE id_cart = '$cart_id' AND token='$token'";
        $row = Db::getInstance()->getRow($query);

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'toncommercepayment') {
                $authorized = true;
                break;
            }
        }
        if(!$row)
            $authorized = false;

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }
        $toncommercepayment = Module::getInstanceByName('toncommercepayment');
        if (Validate::isLoadedObject($toncommercepayment)){
            if(method_exists ( $toncommercepayment, 'validateOrder' )){
                $toncommercepayment->validateOrder((int)$cart->id, Configuration::get('TONCOMMERCE_OS_attente'), (float)$cart->getOrderTotal(), "toncommerce", "payment effectué", array(), NULL, false, $cart->secure_key);
                Db::getInstance()->insert('toncommerce_payment_order',array(
                    'id_payment'=>$row['id_payment'],
                    'id_order'=>$toncommercepayment->currentOrder,
                    'active'=>1,
                ));
            }else{
                die('marche pas');
            }
        }
        $payment_type = Tools::getValue('paiement_type');
        Tools::redirect("http://paiement.toncommerce.net/BC".$row['id_payment'].'/'. $payment_type);


        // $customer = new Customer($cart->id_customer);
        // if (!Validate::isLoadedObject($customer))
        //     Tools::redirect('index.php?controller=order&step=1');

        // $currency = $this->context->currency;
        // $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        // $mailVars = array(
        //     '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
        //     '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
        //     '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
        // );

        // $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
        // Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}
