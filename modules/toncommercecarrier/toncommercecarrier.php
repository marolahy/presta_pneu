<?php

if (!defined('_PS_VERSION_'))
	exit;
if(!class_exists('Curl'))
	require_once dirname(__FILE__).'/classes/Curl.php';
class ToncommerceCarrier extends CarrierModule
{
	const PREFIX = 'toncommercecarrier_';
	 const API_BASE_URL = 'https://api.toncommerce.net/';
 
	protected $_hooks = array(
		'actionCarrierUpdate', 
	);
	protected $_carriers = array(
		'Ton Commerce Transporteur' => 'toncommercecarrier',
	);
	 
	public function __construct()
	{
		$this->name = 'toncommercecarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '0.0.1';
		$this->author = 'Bezama Marolahy Randriamifidy';
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('Ton Commerce Transporteur');
		$this->description = $this->l('Ton Commerce Transporteur module.');
	}
	public function getQuantityWithoutDeclinaison($reference ){
        $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'articles/'.$reference.'/stock');
        $response = Tools::jsonDecode($curl->response);
        if($response->statut === "ok" ){
            return (int)$response->article->declinaisons[0]->stock;
        }
        return false;
    }

    public function getQuantityWithDeclinaison($reference,$declinaison_id ){
        $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'articles/'.$reference.'/stock');
        $response = Tools::jsonDecode($curl->response);
        if($response->statut === "ok" ){
            foreach ($response->article->declinaisons as $declinaison) {
                if( (int)$declinaison->id === (int)$declinaison_id )
                    return (int)$declinaison->stock;
            }
        }
        return false;
    }

	public function getFraisdePort($products,$id_address_delivery)
	{
	    $curl = new Curl();
	    $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
	    $curl->setopt(CURLOPT_RETURNTRANSFER,true);
	    $request =  array("pays"=>$this->getIdPays($id_address_delivery));
	    $request['articles'] = array();
	    foreach($products as $product){
	        $query = "SELECT reference FROM `"._DB_PREFIX_."toncommerce_product_mapping` WHERE id_product = ".$product['id_product'];
	        $row = Db::getInstance()->getRow($query);
	        if( $row ){
	        	
	            $request['articles'][] = array("reference"=>$row['reference'],'quantite'=>$product['quantity'],'prix_ht_boutique'=>Product::getPriceStatic($product['id_product'],false));
	            if($product['id_attribute'] == 0){
	                StockAvailable::setQuantity($product['id_product'], 0, $this->getQuantityWithoutDeclinaison($row['reference']));
	                StockAvailable::updateQuantity($product['id_product'], 0, 0);
	            }else{
	                $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_attribute_mapping` WHERE id_product = ".$product['id_product']." AND id_attribute = ".$product['id_attribute'];
	                $row_attribute = Db::getInstance()->getRow($query);
	                if( $row_attribute ){
	                    StockAvailable::setQuantity($product['id_product'], $product['id_attribute'], $this->getQuantityWithDeclinaison($row['reference'],$row_attribute['id_declinaison']));
	                    StockAvailable::updateQuantity($product['id_product'], $product['id_attribute'], 0);
	                }

	            }
	        }

	    }
	    //without article return 0

	    if(count($request['articles'])===0)
	        return 0;
	    $curl->post(self::API_BASE_URL.'port',array("json"=>Tools::jsonEncode($request)));
	    $response = Tools::jsonDecode($curl->response);
	    if($response->statut === "ok" ){
	        return (float)$response->port->prix_ht;
	    }
	    return 0;
	}
	public function install()
	{
		if (parent::install()) {
			foreach ($this->_hooks as $hook) {
				if (!$this->registerHook($hook)) {
					return false;
				}
			}
	 
			if (!$this->createCarriers()) { 
				return false;
			}
	 
			return true;
		}
	 
		return false;
	}
	protected function createCarriers()
	{
		foreach ($this->_carriers as $key => $value) {
			//Create new carrier
			$carrier = new Carrier();
			$carrier->name = $key;
			$carrier->active = true;
			$carrier->deleted = false;
			$carrier->shipping_handling = false;
			$carrier->range_behavior = 0;
			$carrier->delay[Configuration::get('PS_LANG_DEFAULT')] = $key;
			$carrier->shipping_external = true;
			$carrier->is_module = true;
			$carrier->external_module_name = $this->name;
			$carrier->need_range = true;
			$carrier->position = 0;
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_ .'carrier SET position = position + 1');
			if ($carrier->add()) {
				$groups = Group::getGroups(true);
				foreach ($groups as $group) {
					Db::getInstance()->insert('carrier_group', array(
						'id_carrier' => (int) $carrier->id,
						'id_group' => (int) $group['id_group']
					));
				}
	 			Db::getInstance()->execute('UPDATE '._DB_PREFIX_ .'carrier SET position = 0 WHERE  id_carrier = '.$carrier->id);
				$rangePrice = new RangePrice();
				$rangePrice->id_carrier = $carrier->id;
				$rangePrice->delimiter1 = '0';
				$rangePrice->delimiter2 = '1000000';
				$rangePrice->add();
	 
				$rangeWeight = new RangeWeight();
				$rangeWeight->id_carrier = $carrier->id;
				$rangeWeight->delimiter1 = '0';
				$rangeWeight->delimiter2 = '1000000';
				$rangeWeight->add();
	 
				$zones = Zone::getZones(true);
				foreach ($zones as $z) {
					Db::getInstance()->insert( 'carrier_zone',
						array('id_carrier' => (int) $carrier->id, 'id_zone' => (int) $z['id_zone']));
					Db::getInstance()->insert( 'delivery',
						array('id_carrier' => $carrier->id, 'id_range_price' => (int) $rangePrice->id, 'id_range_weight' => NULL, 'id_zone' => (int) $z['id_zone'], 'price' => '0'));
					Db::getInstance()->insert( 'delivery',
						array('id_carrier' => $carrier->id, 'id_range_price' => NULL, 'id_range_weight' => (int) $rangeWeight->id, 'id_zone' => (int) $z['id_zone'], 'price' => '0'));
				}
				Configuration::updateValue(self::PREFIX . $value, $carrier->id);
				Configuration::updateValue(self::PREFIX . $value . '_reference', $carrier->id);
			}
		}
	 	
		return true;
	}
    public function getIdPays($id_address_delivery){
        $curl = new Curl();
        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));
        $curl->setopt(CURLOPT_RETURNTRANSFER,true);
        $curl->get( self::API_BASE_URL.'pays');
        $response = Tools::jsonDecode($curl->response);
        if($response->statut === "ok" ){
           $query = "SELECT country_lang.name  FROM `"._DB_PREFIX_."address` as address "
                     ." LEFT JOIN `"._DB_PREFIX_."country_lang` as country_lang ON "
                     ." country_lang.id_lang = ".Configuration::get('PS_LANG_DEFAULT')
                     ." AND country_lang.id_country = address.id_country "
                     ." WHERE id_address = ".$id_address_delivery;
            $row = Db::getInstance()->getRow($query);
            if($row){
                foreach($response->pays as $pays){
                    if(strtolower($pays->nom) === strtolower($row['name']))
                        return (string)$pays->id;
                }
            }else{
                return "1";
            }
        }
        return "1";
    }
	protected function deleteCarriers()
	{
		foreach ($this->_carriers as $value) {
			$tmp_carrier_id = Configuration::get(self::PREFIX . $value);
			$carrier = new Carrier($tmp_carrier_id);
			$carrier->delete();
		}
	 
		return TRUE;
	}
 
	public function uninstall()
	{
		if (parent::uninstall()) {
			foreach ($this->_hooks as $hook) {
				if (!$this->unregisterHook($hook)) {
					return false;
				}
			}
	 
			if (!$this->deleteCarriers()) {
				return false;
			}
	 
			return true;
		}
	 
		return false;
	}

	public function getOrderShippingCost($params, $shipping_cost)
	{
		return $shipping_cost;
	}
	 
	public function getOrderShippingCostExternal($params)
	{
		return $this->getOrderShippingCost($params, 0);
	}
	public function hookActionCarrierUpdate($params)
	{
		if ($params['carrier']->id_reference == Configuration::get(self::PREFIX . 'toncommercecarrier')) {
			Configuration::updateValue(self::PREFIX . 'toncommercecarrier', $params['carrier']->id);
		}
	}
	public function getPackageShippingCost($cart,$shipping_cost,$products)
	{
		$toncommerce_products = array();
		foreach ($products as $product) {
			$toncommerce_products[] = array('quantity'=>$product['cart_quantity'],'id_product'=>$product['id_product'],'id_attribute'=>$product['id_product_attribute']);
		}
		return $this->getFraisdePort($toncommerce_products,$cart->id_address_delivery);

	}

}