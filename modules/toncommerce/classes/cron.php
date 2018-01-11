<?php

require_once dirname(__FILE__).'/../../config/config.inc.php';

require_once dirname(__FILE__).'/classes/Api.php';



if (substr(_COOKIE_KEY_, 34, 8) != Tools::getValue('token') && php_sapi_name()!='cli' ) {

    die;

}

$toncommerce = new TonCommerceApi();
//premier lancement inserer les mentions generale	

if($toncommerce->isFirstRunning()){
	$toncommerce->importCGV();
	$toncommerce->importMentionLegale();
	$toncommerce->updateCountryActive();
	$toncommerce->importProduct();
}
$reference = $toncommerce->getAllReferenceProduct();
if(count($reference['all']) == $toncommerce->getNbreProductAdded()){
	$toncommerce->updateStock();
	$toncommerce->updateOrderStatus();
	$toncommerce->updateProduct();
}else{
	$prestashop_references = $toncommerce->getAllReferenceFromPrestashop();
	$reference_to_deletes = array_diff($prestashop_references,$reference['all']);
	foreach ($reference_to_deletes as $ref) {
		$toncommerce->deleteProduct($ref);
	}
	$reference_to_insert = array_diff($reference['all'],$prestashop_references);
	foreach ($reference_to_insert as $ref) {
		$cat_id = $toncommerce->getCategorieByRef($ref,$reference['products']);
		if($cat_id)
			$toncommerce->insertProduct($ref,$cat_id);
	}

}
//Import des id de ton commerce 

/*
$monfichier = dirname(__FILE__).'/compteur.txt';

$toncommerce = new TonCommerceApi();

if(file_exists($monfichier)){

	$compteur = file_get_contents($monfichier);

	$mycompteur = json_decode($compteur,true);

	if(!array_key_exists('commande',$mycompteur)){

		$start =time();

		$mycompteur['commande'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->updateOrderStatus();

		$mycompteur['commande'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);



	}else{

		if($mycompteur['commande']["finish"]!=0){

			$start = time();

			$mycompteur['commande'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->updateOrderStatus();

			$mycompteur['commande'] = array("start"=> $start ,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f,json_encode($mycompteur));

			fclose($f);

		}

	}

	if(!array_key_exists('cgv',$mycompteur)){

			$start = time();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->importCGV();

			$toncommerce->importMentionLegale();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);



	}else{

		$time = (int)$mycompteur['cgv']["finish"];

		$nextUpgrade= $time + (24 * 60 * 60);

		if($mycompteur['cgv']["finish"]!=0 && $nextUpgrade < time() ){

			$start = time();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->importCGV();

			$toncommerce->importMentionLegale();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f,json_encode($mycompteur));

			fclose($f);

		}

	}

	if(!array_key_exists('product',$mycompteur)){

		$start = time();

		$mycompteur['product'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->importProduct();

		$mycompteur['product'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);



	}else{

		$time = (int)$mycompteur['product']["finish"];

		$nextUpgrade= $time + (6 * 60 * 60);

		if($mycompteur['product']["finish"]!=0 && $nextUpgrade < time() ){

			$start =time();

			$mycompteur['product'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->importProduct();

			$mycompteur['product'] = array("start"=> $start,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f,json_encode($mycompteur));

			fclose($f);

		}

	}



}else{

	$f = fopen($monfichier, 'w');

	fwrite($f, '[]');

	fclose($f);

	$compteur = file_get_contents($monfichier);

	$mycompteur = json_decode($compteur,true);

	$toncommerce->updateCountryActive();

	if(!array_keys($mycompteur,'commande')){

		$start =time();

		$mycompteur['commande'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->updateOrderStatus();

		$mycompteur['commande'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);



	}else{

		if($mycompteur['commande']["finish"]!=0){

			$start = time();

			$mycompteur['commande'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->updateOrderStatus();

		$mycompteur['commande'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f,json_encode($mycompteur));

		fclose($f);

		}

	}

	if(!array_keys($mycompteur,'cgv')){

		$start =time();

		$mycompteur['cgv'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->importCGV();

		$toncommerce->importMentionLegale();

		$mycompteur['cgv'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);



	}else{

		$time = (int)$mycompteur['cgv']["finish"];

		$nextUpgrade= $time + (24 * 60 * 60);

		if($mycompteur['cgv']["finish"]!=0 && $nextUpgrade < time() ){

			$start =time();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->importCGV();

			$toncommerce->importMentionLegale();

			$mycompteur['cgv'] = array("start"=> $start ,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f,json_encode($mycompteur));

			fclose($f);

		}

	}

	if(!array_keys($mycompteur,'product')){

		$start =time();

		$mycompteur['product'] = array("start"=> $start ,"finish"=>0);

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);

		$toncommerce->importProduct();

		$mycompteur['product'] = array("start"=> $start ,"finish"=>time());

		$f = fopen($monfichier, 'w');

		fwrite($f, json_encode($mycompteur));

		fclose($f);



	}else{

		$time = (int)$mycompteur['product']["finish"];

		$nextUpgrade= $time + (6 * 60 * 60);

		if($mycompteur['product']["finish"]!=0 && $nextUpgrade < time() ){

			$start =time();

			$mycompteur['product'] = array("start"=> $start ,"finish"=>0);

			$f = fopen($monfichier, 'w');

			fwrite($f, json_encode($mycompteur));

			fclose($f);

			$toncommerce->importProduct();

			$mycompteur['product'] = array("start"=> $start ,"finish"=>time());

			$f = fopen($monfichier, 'w');

			fwrite($f,json_encode($mycompteur));

			fclose($f);

		}

	}

}

*/