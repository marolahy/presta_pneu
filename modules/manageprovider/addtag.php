<?php
define ("_PS_ADMIN_DIR_", 1);
require(dirname(__FILE__).'/../../config/config.inc.php');
/*
function getPrice( $id_supplier, $id_category , $priceCSVHT, $weight, $id_shop = 1 )
    {
        $prix  = 0 ;
        $query = 'SELECT * FROM `'._DB_PREFIX_.'manageprice`  WHERE id_supplier='.$id_supplier.' AND id_category='.$id_category.' AND id_shop='.$id_shop;
        $result = Db::getInstance()->getRow( $query );
        $priceHt = $priceCSVHT;
        $prixPoid = $weight  * (float)$result['poid'] ;
        $price =  $priceHt + $prixPoid;
        //prendre douane;
        if($result['type_douane'] == 0)
            $douane = $price * (float)$result['douane'];
        else 
            $douane = (float)$result['douane'];
        if($result['type_marge'] == 0)
            $marge = $price * (float)$result['marge'];
        else 
            $marge = (float)$result['marge'];
        return ($douane  + $marge + $price ) +  $result['frais'];
    }
    */
    
$data = fopen(dirname(__FILE__).'/prix_elf.csv',r);
$i=0;
while (($col = fgetcsv($data, 1000, ",")) !== FALSE) {
    if ($i == 1) 
        continue;
    if(count($col)< 2)
        continue;
    $dataProduct = array(
        'price' => (float)$col[1],
    );
	 $dataProductShop = array(   
	    'price' => (float)$col[1],
	);
     echo 'id_product = ' . (int)$col[0]." AND id_shop = 1\n";
	 Db::getInstance()->update('product_shop', $dataProductShop, 'id_product = ' . (int)$col[0].' AND id_shop = 1');
     Db::getInstance()->update('product', $dataProduct, 'id_product = ' . (int)$col[0]);
}
/*
$datas = Db::getInstance()->ExecuteS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 2");
foreach($datas as $product){
	Tag::addTags(2,$product['id_product'],"$product[name],Madagascar,La reunion,Vente,en ligne");
}*/