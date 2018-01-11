<?php

require_once dirname(__FILE__).'/../../config/config.inc.php';

require_once dirname(__FILE__).'/classes/Api.php';


$toncommerce = new TonCommerceApi();
echo '<pre>';
$toncommerce->updateProduct();
echo '</pre>';
//premier lancement inserer les mentions generale	