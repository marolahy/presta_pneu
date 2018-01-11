<?php
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_categories` (
    `id_toncommerce_category` int(11) NOT NULL AUTO_INCREMENT,
    `id_category_from_ton_commerce` int(11),
    `load_product` boolean,
    PRIMARY KEY  (`id_toncommerce_category`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_product_mapping` (
    `id_toncommerce_product` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(11),
    `reference` VARCHAR(255),
    PRIMARY KEY  (`id_toncommerce_product`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'toncommerce_image_mapping` (
    `id_toncommerce_image` int(11) NOT NULL AUTO_INCREMENT,
    `id_image` int(11),
    `id_image_website` int(11),
    PRIMARY KEY  (`id_toncommerce_image`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
