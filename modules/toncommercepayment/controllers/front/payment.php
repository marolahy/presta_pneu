<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

if(!class_exists('Curl'))
    require_once dirname(__FILE__).'/../../classes/Curl.php';

class ToncommercepaymentPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;
	const API_BASE_URL = 'https://api.toncommerce.net/';

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;
		        $query = 'SELECT * FROM `'._DB_PREFIX_.'toncommerce_payment_cart` WHERE id_cart = '.$cart->id;
        $row = Db::getInstance()->getRow($query);
        $id_commande = 0;
        $validate_data = array();
        $validate_data['total'] = $cart->getOrderTotal();
        $products = $cart->getProducts();
        $i = 0;
        $token = "";
        foreach ($products as  $value) {
            $validate_data['product'][$i]['id_product'] = $value['id_product'];
            $validate_data['product'][$i]['cart_quantity'] = $value['cart_quantity'];
            $validate_data['product'][$i]['id_product_attribute'] = $value['id_product_attribute'];
            $validate_data['product'][$i]['id_shop'] = $value['id_shop'];
            $i++;
        }
        if($row){
            $token = $row['token'];
          if($row['cart_content'] == Tools::jsonEncode($validate_data)){
              $id_commande = $row['id_payment'];
              
          }else{
            $response = $this->getIdCommande($cart);
            if($response->statut === "ok"){         
              Db::getInstance()->update('toncommerce_payment_cart',array(
                  'id_cart'=>$cart->id,
                  'id_payment'=>$response->commande->id,
                  'cart_content'=>Tools::jsonEncode($validate_data)
                ),"id_cart = ".$cart->id);
                $id_commande = $response->commande->id;
              }
          }
        }else{
            $ma_response = $this->getIdCommande($cart);
            $response = $ma_response->response;
            if($response->statut === "ok"){ 
                Db::getInstance()->insert('toncommerce_payment_cart',array(
                      'id_cart'=>$cart->id,
                      'id_payment'=>$response->commande->id,
                      'token' => $ma_response->token,
                      'cart_content'=>Tools::jsonEncode($validate_data)
                    ));
                    $id_commande = $response->commande->id;
                    $token = $ma_response->token;
            }
        }
        /*$base_dir = $this->context->link->getModuleLink('toncommercepayment', 'validation');
        $this->context->smarty->assign([
              'id_commande' => $id_commande,
              'token' => $token,
              'cart_id' => $cart->id,
              'base_dir'=>$base_dir,
          ]);*/
   Tools::redirect($this->context->link->getModuleLink('toncommercepayment', 'validation',['cart_id'=>$cart->id,
    'token'=>$token,'paiement_type'=>Tools::getValue('type','virement')]));
		//$this->setTemplate('payment_form.tpl');
	}

	protected function getIdCommande($params){
        $token = md5(uniqid(mt_rand(), true));
        $address_invoice  = new Address($params->id_address_invoice);
        $data['client_email'] = $this->context->customer->email;
        $data['facturation_nom'] = $this->context->customer->lastname;
        $data['facturation_societe'] = $address_invoice->company;
        $data['facturation_prenom'] = $this->context->customer->firstname;
        $data['facturation_adresse'] = $address_invoice->address1.' '.$address_invoice->address2;
        $data['facturation_codepostal'] = $address_invoice->postcode;
        $data['facturation_ville'] = $address_invoice->city;
        $data['facturation_pays'] = $this->getIdCountry($address_invoice->country);
        $address_delivery = new Address($params->id_address_delivery);
        $data['expedition_nom'] = $address_delivery->lastname;
        $data['expedition_prenom'] = $address_delivery->firstname;
        $data['expedition_societe'] = $address_delivery->company;
        $data['expedition_adresse'] = $address_delivery->address1.' '.$address_delivery->address2;
        $data['expedition_codepostal'] = $address_delivery->postcode;
        $data['expedition_ville'] = $address_delivery->city;
        $data['expedition_pays'] = $this->getIdCountry($address_delivery->country);
        $data['emballage_cadeau'] = 'non';
        $data['articles'] = $this->generateArticle($params->getProducts());
        $data['url_confirmation_paiement'] = $this->context->link->getModuleLink('toncommercepayment', 'validation', 
            array(
                'cart_id'=>$params->id,
                'token'=>$token,
                ),

             true);
        $data['url_retour_client'] = $this->context->link->getPageLink('history',true);
        $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->post(self::API_BASE_URL.'commandes',array("json"=>Tools::jsonEncode($data)));
        $response = Tools::jsonDecode($curl->response);
        $ma_response = new stdClass;
        $ma_response->token = $token;
        $ma_response->response = $response;
        return $ma_response;

    }
    protected function generateArticle($products)
    {
      $request = array();
      foreach($products as $product){
          $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_mapping` WHERE id_product = ".$product['id_product'];
          $row = Db::getInstance()->getRow($query);
          if( $row ){
            if($product['id_product_attribute'] == 0 )
              $request[] = array('reference'=>$row['reference'], 
                            'declinaison'=>$this->getIdDefaultAttribute($row['reference']),
                             'quantite'=>$product['quantity'],
                             'prix_ht_boutique'=>$product['price']);
            else {
              $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_attribute_mapping` WHERE id_product = ".$product['id_product']." AND id_attribute = ".$product['id_product_attribute'];
                $row_attribute = Db::getInstance()->getRow($query);
                if( $row_attribute )
                  $request[] = array('reference'=>$row['reference'], 
                            'declinaison'=>$row_attribute['id_declinaison'],
                             'quantite'=>$product['quantity'],
                             'prix_ht_boutique'=>$product['price']);

              }
            }
        }
        return $request;
    }
    protected function getIdCountry( $country )
    {
      $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'pays');
        $response = Tools::jsonDecode($curl->response);
        foreach($response->pays as $pays){
            if(strtolower($pays->nom) === strtolower( $country ))
                return (string)$pays->id;
        }
        return "1";
    }
    public function getIdDefaultAttribute( $reference ){
       $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'articles/'.$reference);
        $response = Tools::jsonDecode($curl->response);
        if($response->statut === "ok" ){
            return $response->article->declinaisons[0]->id;
        }
        return 0;;
    }
    public function getProduct($reference='')
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
     

}
