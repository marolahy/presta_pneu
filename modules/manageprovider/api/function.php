<?php
/**
 *
 *
 */
function getCurrentMid(){
	$euro  = 0;
	$xml = simplexml_load_file('http://xchangemadagascar.com/coursservices.php');
    $cours = current($xml);
    foreach ($cours as $key => $value) {
            if( $value->sigle == "EUR" ){
                $euro = (float)$value->MidCloture > 0 ? (float)$value->MidCloture : (float)$value->MidOuverture;
			break;
            }
    }
    return $euro;
}

function deleteProductIfNotExists( $id_supplier )
{
  //
    $query = 'DELETE FROM '._DB_PREFIX_.'product WHERE reference NOT IN( '
             .'SELECT reference FROM '._DB_PREFIX_.'manage_provider_product` WHERE id_supplier='.$id_supplier.') '
             .'AND id_supplier='.$id_supplier;
    if ( !Db::getInstance()->execute( $query ))
                return false;

    $query = 'DELETE FROM '._DB_PREFIX_.'product WHERE reference NOT IN( '
             .'SELECT reference FROM '._DB_PREFIX_.'manage_provider_product` WHERE id_supplier='.$id_supplier.') '
             .'AND id_supplier='.$id_supplier;
    if ( !Db::getInstance()->execute( $query ))
                return false;
    return true;
}

function insertToProvider( $id_supplier, $param )
{

}

function updateProduct( $id_supplier )
{


}

function addCategories( $categorieName )
{

}

function evalAndCalcPrix( $id_supplier )
{

} 