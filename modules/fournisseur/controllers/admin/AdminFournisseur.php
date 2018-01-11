<?php
include_once(dirname(__FILE__).'/../../models/Ftp.php');
class AdminFournisseurController extends ModuleAdminController
{
	public function __construct()
	{
		$this->table = 'supplier';
		$this->tab = 'ftp';
		$this->bootstrap = true;
		$this->className = 'Supplier';
		$this->addRowAction('edit');
		$this->toolbar_btn = array();
		$this->_select = 'COUNT(DISTINCT ps.`id_product`) AS products';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (a.`id_supplier` = ps.`id_supplier`)';
		$this->_where = 'AND a.id_supplier IN ( SELECT id_supplier FROM '._DB_PREFIX_.'supplier )';
		$this->_group = 'GROUP BY a.`id_supplier`';
		$this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');
		$this->fields_list = array(
			'id_supplier' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'logo' => array('title' => $this->l('Logo'), 'width' => 150, 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
			'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
			'products' => array('title' => $this->l('Number of products'), 'width' => 70, 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true)
		);
		parent::__construct();

		$this->available_tabs_lang = array (
			'Ftp' => $this->l('Remote Configuration'),
			'Calcul' => $this->l('Regles de calcul'),
			'Csv' => $this->l('Configuration CSV'),
			'Rules' => $this->l('Regle d\'import'),
			'Taxe' => $this->l('Gestion des Taxes')
		);

		$this->available_tabs = array('Remote Configuration' => 0, 'Calcul' => 1,'Csv'=>2,'Rules'=>3,'Taxe'=>4);
		$this->override_folder = '';
	}
	public function setMedia()
	{
    	//
    	$this->addJquery();
    	$this->addJqueryUI('ui.tabs');
    	parent::setMedia();
	}
	public function postProcess()
	{
		if(Tools::isSubmit('addcart_rule'))
                {
					//insertion csv configuration
					$csv = Tools::getValue('csv');
					Db::getInstance()->delete('manage_provider_csv_configuration','id_supplier ='.Tools::getValue('id_supplier'));
					foreach($csv as $key => $value )
					{
						$this->__insertcsv(
					array(
					'id_supplier' => Tools::getValue('id_supplier'),
					'cle'=> $key,
					'valeur'=>$value,
				));
					}
					//insertion delimiteur dans fichier csv
					Db::getInstance()->delete('manage_provider_csv','id_supplier ='.Tools::getValue('id_supplier'));
						$this->__insertcsvdelimit(
					array(
					'id_supplier' => Tools::getValue('id_supplier'),
					'separateur_ligne'=> Tools::getValue('separateur_linge'),
					'separateur_colonne'=>Tools::getValue('separateur_colonne'),
					'separateur_text'=>Tools::getValue('separateur_text'),
				));
					
					//insertion categorie
					$categorie = Tools::getValue('Categorie');
					Db::getInstance()->delete('manage_provider_categories','id_supplier ='.Tools::getValue('id_supplier'));
					foreach($categorie as $key => $value )
					{
						if($value=="")
							continue;
						$this->__insertcategorie(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'column'=>$value,
				'flag'=>$key,
				));
					}
					//insertion remote configuration et recuperation des valeurs
					if(Tools::getValue('ftp_type') == 'ftp' )
						$this->__insertsupplier(
							array(
							'id_supplier' => Tools::getValue('id_supplier'),
							'type'=>Tools::getValue('ftp_type'),
							'host'=> $Hostname=Tools::getValue('ftp_hostname'),
							'password' => $Password=Tools::getValue('ftp_password'),
							'username' =>$Username=Tools::getValue('ftp_username'),
							'filename' =>$Filename=Tools::getValue('ftp_filename'),
							)
						);
				else
				//insertion remote configuration url et recuperation des valeurs
				$this->__insertsupplier(
						array(
						'id_supplier' => Tools::getValue('id_supplier'),
						'type'=>Tools::getValue('ftp_type'),
						'host'=>'',
						'password'=> '',
						'username'=> '',
						'filename'=> $Hostname=Tools::getValue('ftp_url'),
						)
					);
					//insertion manage_provider_product
				$this->__insertproduct(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'reference'=> Tools::getValue(''),
				'price' =>Tools::getValue(''),
				'actual_price' =>Tools::getValue(''),
				'id_categorie_default' =>Tools::getValue('id_category'),
				'date_add' =>Tools::getValue(''),
				'date_upd' =>Tools::getValue(''),
				));
					
					//insertion rules
				$this-> __insertrules(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'flag'=>Null,
				'rules'=>Tools::getValue(''),
				));
					//insertion action
					
				$action = Tools::getValue('update');
				Db::getInstance()->delete('manage_provider_supplier_configuration','id_supplier ='.$id_supplier);
				foreach($action as $key => $value )
					{
					$value=''.date("Y/m/d h:m:s").'';
					$this-> __insertaction(
						array(
							'id_supplier' => Tools::getValue('id_supplier'),
							'day'=>$key,
							'date'=>$value,
				
					));
					}
					//insertion status
					$date = ''.date("Y/m/d h:m:s").'';	
				$this-> __insertstatus(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'last_update'=>$date,
				));
					//insertion logs
					$date = ''.date('Y/m/d').' ';
				$this->__insertlog(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'date' => $date,
				'message'=>Tools::getValue(''),
				));	
					//insertion tax
				$this->__insertTax(
				array(
				'id_supplier' => Tools::getValue('id_supplier'),
				'id_tax_group'=>Tools::getValue('id_tax_rules'),
				));
				// insertion maping
				$id_category_mapped = Tools::getValue('id_category_mapped');
				Db::getInstance()->delete('manage_provider_maping','id_supplier ='.$param['id_supplier']);
				foreach ($id_category_mapped as $key => $value) {
						# code...
					$this->__insertmaping(
					array(
					'id_supplier' => Tools::getValue('id_supplier'),
					'id_category'=>$value,
					'CSV_maping' =>Tools::getValue('maping'),
					));	
				}	
			}

					
		return parent::postProcess();
	}
	private function __insertcsv($param=array() )
	{
		Db::getInstance()->insert('manage_provider_csv_configuration',$param);
	}
	private function __insertcsvdelimit($param=array() )
	{
		Db::getInstance()->insert('manage_provider_csv',$param);
	}
	
	private function __insertcategorie($param=array() )
	{
		Db::getInstance()->insert('manage_provider_categories',$param);
	}
	
	private function __insertsupplier($param=array() )
	{
		Db::getInstance()->insert('manage_provider_supplier_configuration',$param);
	}
	
	private function __insertaction($param=array() )
	{
		Db::getInstance()->insert('manage_provider_import_action',$param);
		
	}
	private function __insertrules( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_import_rules','id_supplier ='.$param['id_supplier']);
		Db::getInstance()->insert('manage_provider_import_rules',$param);
	}
	
	private function __insertTax( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_tax','id_supplier ='.$param['id_supplier']);
		Db::getInstance()->insert('manage_provider_tax',$param);
	}
	
	private function __insertlog( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_import_logs','id_supplier ='.Tools::getValue('id_supplier'));
		Db::getInstance()->insert('manage_provider_import_logs',$param);
	}
	
	private function __insertstatus( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_import_status','id_supplier ='.$param['id_supplier']);
		Db::getInstance()->insert('manage_provider_import_status',$param);
	}
	
	private function __insertproduct( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_product','id_supplier ='.$param['id_supplier']);
		Db::getInstance()->insert('manage_provider_product',$param);
	}
	private function __insertmaping( $param = array() )
	{
		Db::getInstance()->delete('manage_provider_maping','id_supplier ='.$param['id_supplier']);
		Db::getInstance()->insert('manage_provider_maping',$param);
	}
	protected function afterUpdate($current_object)
	{
		
	}
	
	public function ajaxProcess()
	{
		parent::ajaxProcess();
		
	}
	public function renderForm()
	{
		$renderForm = parent::renderForm();
		$current_object = $this->loadObject(true);
		$name = Supplier::getNameById(Tools::getValue('id_supplier'));
		$this->initToolbar();
		$this->context->smarty->assign(
			array(
				'show_toolbar' => true,
				'toolbar_btn' => $this->toolbar_btn,
				'toolbar_scroll' => $this->toolbar_scroll,
				'taxe_group' => TaxRulesGroup::getAssociatedTaxRatesByIdCountry($this->context->country->id),
				'title' => array($this->l('Manage Supplier Import : '), $name),
				'defaultDateFrom' => date('Y-m-d H:00:00'),
				'defaultDateTo' => date('Y-m-d H:00:00', strtotime('+1 month')),
				'currentIndex' => self::$currentIndex,
				'currentObject'=>$current_object,
				'currentToken' => $this->token,
				'currentTab' => $this,
				'categories' => $this->getCategorieValue(Tools::getValue('id_supplier')),
				'id_supplier' => Tools::getValue('id_supplier'),
			)
		);
		$current_object = $this->loadObject(true);
		$this->context->smarty->assign('form_tpl', $renderForm);
		return $this->createTemplate('form.tpl')->fetch();
		return $renderForm;
	}
	/**
	 * get taxe 
	 */
	private function __getTaxe( $id_supplier)
	{

	}
	public function getFtpValue( $id_supplier, $field=null )
	{
		$ftp = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'manage_provider_supplier_configuration where id_supplier='.$id_supplier);	
		if(is_null($field))
			return null;
		return $ftp[$field];
	}
	
	public function getCsvValue( $id_supplier, $field )
	{
		$csv = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'manage_provider_csv_configuration where id_supplier='.$id_supplier." AND cle='$field'");	
		return $csv["valeur"];
	}
	public function getCsvdelimitValue( $id_supplier, $field=null )
	{
		$csv_config = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'manage_provider_csv where id_supplier='.$id_supplier);	
		if(is_null($field))
			return null;
		return $csv_config[$field];
	}
	public function getCategorieValue( $id_supplier )
	{
		$categorie = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'manage_provider_categories where id_supplier='.$id_supplier) ;	
		return $categorie;
	}
	public function getActionValue( $id_supplier, $field=null )
	{
		$action = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'manage_provider_import_action where id_supplier='.$id_supplier.' AND day=\''.$field.'\'');
		if($action)
			return true;
		return false;
	}
	public function getCategories()
	{
		$cat = new Category(2,Context::getContext()->language->id);
		$category = $cat->recurseLiteCategTree();
		return $this->getCategoriesTree( $category['children'] );
		
	}
	public function getCategoriesTree( $node )
	{
		$output = "";
		
		foreach($node as $key => $value )
		{
				$output .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
				$output  .=  $this->getCategoriesTree( $value['children'] );
		}
		return $output;
	}
}