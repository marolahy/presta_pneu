<?php

require_once dirname(__FILE__).'/../../config/config.inc.php';

require_once dirname(__FILE__).'/classes/Api.php';



if (substr(_COOKIE_KEY_, 34, 8) != Tools::getValue('token') && php_sapi_name()!='cli' ) {

    die;

}
$monfichier = dirname(__FILE__).'/compteur.txt';
$runScript = false;
if(file_exists($monfichier)){
	$compteur = file_get_contents($monfichier);
	$mycompteur = json_decode($compteur,true);
	if(!is_array($mycompteur)){

		die("en cours d'utilsition");
	}
	if( time() > ($mycompteur['last_running']['start'] + (10*60)) ){
		$runScript = true;
	}
	if($mycompteur['last_running']['finish'] != '0'){
		echo 'atooo';
		$runScript = true;
		$f = fopen($monfichier, 'w');
		$mycompteur = array();
		$mycompteur['last_running'] = array("start"=> time() ,"finish"=>0);
		fwrite($f,json_encode($mycompteur));
		fclose($f);
	}
}else{
	$f = fopen($monfichier, 'w');
	$mycompteur = array();
	$mycompteur['last_running'] = array("start"=> time() ,"finish"=>0);
	fwrite($f,json_encode($mycompteur));
	fclose($f);
	$runScript = true;
}
$toncommerce = new TonCommerceApi();
$reference = $toncommerce->getAllReferenceProduct();
$prestashop_references = $toncommerce->getAllReferenceFromPrestashop();
if(count($reference['all']) == count($prestashop_references )){
	$runScript = true;
}
if(!$runScript ){
	echo 'pas $runScript eeee';
	die();
}


//premier lancement inserer les mentions generale
$f = fopen($monfichier, 'w');
$mycompteur = array();
$mycompteur['last_running'] = array("start"=> time() ,"finish"=>0);
fwrite($f,json_encode($mycompteur));
fclose($f);	
if($toncommerce->isFirstRunning()){
	$toncommerce->importCGV();
	$toncommerce->importMentionLegale();
	$toncommerce->updateCountryActive();
	$toncommerce->importProduct();
}

if(count($reference['all']) == count($prestashop_references )){
	//$toncommerce->updateStock();
	var_dump('update atooo');
	$toncommerce->updateOrderStatus();
	$toncommerce->updateProduct();
}else{
	
	$reference_to_deletes = array_diff($prestashop_references,$reference['all']);
	var_dump($reference_to_deletes);
	foreach ($reference_to_deletes as $ref) {
		$toncommerce->deleteProduct($ref);
	}
	$reference_to_insert = array_diff($reference['all'],$prestashop_references);
	var_dump($reference_to_insert);
	foreach ($reference_to_insert as $ref) {
		$cat_id = $toncommerce->getCategorieByRef($ref,$reference['products']);
		if($cat_id)
			$toncommerce->insertProduct($ref,$cat_id);
	}

}
$mycompteur['last_running']['finish'] = time();
$f = fopen($monfichier, 'w');
fwrite($f,json_encode($mycompteur));
fclose($f);
//unlink($monfichier);
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