<?php
/*
* 2013 Lagence720	
*
* Copyright reserved
*/

if (!defined('_PS_VERSION_'))
	exit;
class ManageProvider extends Module
{
	public function __construct()
	{
		$this->name = 'manageprovider';
		$this->tab = 'manage_provide_feature';
		$this->version = 0.1;
		$this->author = 'Lagence720';
		$this->need_instance = 0;

		$this->displayName = $this->l('Supplier import  management and customisation');
		$this->description = $this->l('Provide a Supplier import product program to your shop.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all products points and provider history.');

		parent::__construct();
	}
	public function installDB()
	{
		/**
		 * Creation de la table pour sauvegarder la base produit
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_product` (
			`id_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL,
			`reference` VARCHAR(255),
			`price` INT UNSIGNED DEFAULT NULL,
			`actual_price` INT UNSIGNED DEFAULT NULL,
			`id_categorie_default` INT NOT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
				return false;
		$query = '
		CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'manage_provider_tax` (
			`id_manage_tax` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL,
			`id_tax_group` INT UNSIGNED NOT NULL,
			PRIMARY KEY (`id_manage_tax`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
				return false;

		/***
		 * Stockage de la configuration du fichier csv
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'manage_provider_supplier_configuration` (
			`id_configuration` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL,
			`type` VARCHAR(255),
			`host` VARCHAR(255),
			`password` VARCHAR(255),
			`username` VARCHAR(255),
			`filename` VARCHAR(255),
			PRIMARY KEY (`id_configuration`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute($query ))
			return false;
		/***
		/***
		 * Stockage de la configuration du fichier csv delimiteur
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_csv` (
			`id_configuration` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL,
			`separateur_ligne` VARCHAR (255),
			`separateur_colonne` VARCHAR(255),
			`separateur_text` VARCHAR(255),
			PRIMARY KEY (`id_configuration`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute($query ))
			return false;
		/***
		/***
		 * Stockage de la configuration du fichier csv
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_csv_configuration` (
			`id_configuration` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL,
			`cle` VARCHAR(255),
			`valeur` VARCHAR(255),
			PRIMARY KEY (`id_configuration`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		/**
		 * Regle d'importation pour chaque fournisseur
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'manage_provider_import_rules` (
			`id_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL ,
			`flag` INT UNSIGNED DEFAULT NULL,
			`rules` TEXT,
			PRIMARY KEY (`id_rule`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		/**
		 * Stockage des categories
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'manage_provider_categories` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT UNSIGNED NOT NULL ,
			`flag` INT UNSIGNED DEFAULT NULL,
			`column` INT,
			PRIMARY KEY (`id`),
			INDEX index_search_per_name (`column`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		/**
		 * Status de last update
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_import_status` (
			`id_status` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT NOT NULL,
			`last_update` DATETIME NOT NULL,
			PRIMARY KEY (`id_status`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		/**
		 * Logs d'importations
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_import_logs` (
			`id_log` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT NOT NULL,
			`date` DATE,
			`message` TEXT,
			PRIMARY KEY (`id_log`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		/**
		 * action d'importations
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_import_action` (
			`id_action` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT NOT NULL,
			`day` VARCHAR(255),
			`date` DATETIME,
			PRIMARY KEY (`id_action`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;	
		/**
		 * maping
		 */
		$query = '
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'manage_provider_maping` (
			`id_maping` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_supplier` INT NOT NULL,
			`id_category` INT,
			`CSV_maping` VARCHAR(255),
			PRIMARY KEY (`id_maping`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		if ( !Db::getInstance()->execute( $query ))
			return false;
		return true;
	}
	
	public function uninstallDB()
	{
		//Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_supplier_configuration`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_csv`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_product`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_csv_configuration`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_categories`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_import_logs`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_import_rules`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_import_status`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_import_action`;');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'manage_provider_maping`;');
		return true;
	}
	public function install()
	{
		
		if(!$this->installDB())
			return false;
		$tab = new Tab();
		foreach (Language::getLanguages() as $language)
			$tab->name[$language['id_lang']] = 'Gestion des fournisseurs';
		$tab->module = 'manageprovider';
		$tab->class_name = 'AdminManageProvider';
		$tab->id_parent = Tab::getIdFromClassName('AdminParentPreferences'); // Root tab
		$tab->add();
		
		return parent::install();
		 
	}
	public function uninstall(){
		$this->uninstallDB();
		return parent::uninstall();
	}
	

}


