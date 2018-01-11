<?php
class Ftp extends ObjectModel
{
	public static $definition = array(
		'table' => 'manage_provider_supplier_configuration',
		'primary' => 'id_configuration',
		'fields' => array(
			'id_supplier' => 		array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
			'host' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
			'password' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
			'filename' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
			'username' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
		),
		);
}