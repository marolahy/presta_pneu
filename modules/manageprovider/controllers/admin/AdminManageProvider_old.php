<?php
/**
 * Tab Example - Controller Admin Example
 *
 * @category   	Module / checkout
 * @author     	PrestaEdit <j.danse@prestaedit.com>
 * @copyright  	2012 PrestaEdit
 * @version   	1.0	
 * @link       	http://www.prestaedit.com/
 * @since      	File available since Release 1.0
*/

class AdminManageProviderController extends ModuleAdminController
{

	protected $_category;
	/**
	 * @var string name of the tab to display
	 */
	protected $tab_display;

	/**
	 * The order in the array decides the order in the list of tab. If an element's value is a number, it will be preloaded.
	 * The tabs are preloaded from the smallest to the highest number.
	 * @var array Product tabs.
	 */
	protected $available_tabs = array();

	protected $default_tab = 'Ftp';

	protected $available_tabs_lang = array();

	protected $position_identifier = 'id_supplier';

	protected $submitted_tabs;
	public function initToolbar()
	{
switch ($this->display)
		{
			case 'add':
				
			case 'edit':
				// Default save button - action dynamically handled in javascript
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				//no break
			case 'view':
				// Default cancel button - like old back link
				$back = Tools::safeOutput(Tools::getValue('back', ''));
				if (empty($back))
					$back = self::$currentIndex.'&token='.$this->token;
				if (!Validate::isCleanHtml($back))
					die(Tools::displayError());
				if (!$this->lite_display)
					$this->toolbar_btn['back'] = array(
						'href' => $back,
						'desc' => $this->l('Back to list')
					);
				break;
			case 'options':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
				break;
			case 'view':
				break;
			default: // list
				if ($this->allow_export)
					$this->toolbar_btn['export'] = array(
						'href' => self::$currentIndex.'&amp;export'.$this->table.'&amp;token='.$this->token,
						'desc' => $this->l('Export')
				);
		}
		$this->addToolBarModulesListButton();
	}
	public function __construct()
	{
		$this->table = 'supplier';
		$this->className = 'Supplier';
		$this->addRowAction('edit');
		$this->toolbar_btn = array();
		$this->_select = 'COUNT(DISTINCT ps.`id_product`) AS products';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (a.`id_supplier` = ps.`id_supplier`)';
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
			'Ftp' => $this->l('FTP'),
			'Calcul' => $this->l('Regles de calcul'),
			'Csv' => $this->l('Configuration CSV'),
			'Rules' => $this->l('Regle d\'import'),
			'Taxe' => $this->l('Gestion des Taxes')
		);

		$this->available_tabs = array('Ftp' => 0, 'Calcul' => 1,'Csv'=>2,'Rules'=>3,'Taxe'=>4);
		$this->override_folder = dirname(__FILE__).'/../../views/templates/admin/';
	}


	public function renderForm()
	{
		$back = Tools::safeOutput(Tools::getValue('back', ''));
		if (empty($back))
			$back = self::$currentIndex.'&token='.$this->token;
		if (!method_exists($this, 'initForm'.$this->tab_display))
			return;
		$this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;
		$this->tpl_form_vars['currentIndex'] = self::$currentIndex;
		//$this->fields_form = array();
		$this->display = 'edit';
		$this->tpl_form_vars['token'] = $this->token;
		$this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		$this->tpl_form_vars['post_data'] = Tools::jsonEncode($_POST);
		$this->tpl_form_vars['save_error'] = !empty($this->errors);
		if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_supplier'))
			$this->errors[] = 'Unable to load object';
		else
		{
			$this->_displayDraftWarning($this->object->active);

			// if there was an error while saving, we don't want to lose posted data
			if (!empty($this->errors))
				$this->copyFromPost($this->object, $this->table);

			//$this->initPack($this->object);
			$this->{'initForm'.$this->tab_display}($this->object);
			$this->tpl_form_vars['object'] = $this->object;
			if ($this->ajax)
				if (!isset($this->tpl_form_vars['custom_form']))
					throw new PrestaShopException('custom_form empty for action '.$this->tab_display);
				else
					return $this->tpl_form_vars['custom_form'];
		}
		//$this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
		$parent  = parent::renderForm();
		return $parent;
		
		/*

		
		
		
		//$this->initPack($this->object);
		
		$this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
		 */
	}

	protected function _displayDraftWarning($active)
	{
		$content = '<div class="warn draft" style="'.($active ? 'display:none' : '').'">
				<p>
				<span style="float: left">
				'.$this->l('Your product will be saved as a draft.').'
				</span>
				<span style="float:right"><a href="#" class="button" style="display: block" onclick="submitAddProductAndPreview()" >'.$this->l('Save and preview.').'</a></span>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
				<br class="clear" />
				</p>
	 		</div>';
			$this->tpl_form_vars['draft_warning'] = $content;
	}
	public function initFormFtp($obj)
	{
		$dataObj = $obj;
		$data = $this->createTemplate($this->tpl_form);
		//

		/*
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Supplier Configuration'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'name' => 'ftphost',
					'label' =>  $this->l('FTP HOST'),
					'size' => 40,
					'desc' => $this->l('Enter your ftp host for these supplier')
				),
				array(
					'type' => 'text',
					'name' => 'ftpusername',
					'label' =>  $this->l('FTP USERNAME'),
					'size' => 40,
					'desc' => $this->l('Enter your ftp host for these supplier')
				),
				array(
					'type' => 'text',
					'name' => 'ftppassword',
					'label' =>  $this->l('FTP PASSWORD'),
					'size' => 40,
					'desc' => $this->l('Enter your ftp host for these supplier')
				),
				array(
					'type' => 'text',
					'name' => 'filename',
					'label' =>  $this->l('FILENAME TO GET PRODUCT'),
					'size' => 40,
					'desc' => $this->l('Enter filename to get product')
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);*/
	}
	public function initFormCalcul($obj)
	{
		
	}
	public function initFormCsv($obj)
	{
		
	}
	public function initFormRules($obj)
	{
		
	}
	public function initFormTaxe($obj)
	{
		
	}
	public function initProcess()
	{

		if (!$this->action)
			parent::initProcess();
		else
			$this->id_object = (int)Tools::getValue($this->identifier);

		if (isset($this->available_tabs[Tools::getValue('key_tab')]))
			$this->tab_display = Tools::getValue('key_tab');

		// Set tab to display if not decided already
		if (!$this->tab_display && $this->action)
			if (in_array($this->action, array_keys($this->available_tabs)))
				$this->tab_display = $this->action;

		// And if still not set, use default
		if (!$this->tab_display)
		{
			if (in_array($this->default_tab, $this->available_tabs))
				$this->tab_display = $this->default_tab;
			else
				$this->tab_display = key($this->available_tabs);
		}
	}
	/**
	 * postProcess handle every checks before saving products information
	 *
	 * @return void
	 */
	public function postProcess()
	{
		if (!$this->redirect_after)
			parent::postProcess();

		if ($this->display == 'edit' || $this->display == 'add')
		{
			$this->addjQueryPlugin(array(
				'autocomplete',
				'tablednd',
				'thickbox',
				'ajaxfileupload',
				'date'
			));

			$this->addJqueryUI(array(
				'ui.core',
				'ui.widget',
				'ui.accordion',
				'ui.slider',
				'ui.datepicker'
			));

			$this->addJS(array(
				_PS_JS_DIR_.'productTabsManager.js',
				_PS_JS_DIR_.'admin-products.js',
				_PS_JS_DIR_.'attributesBack.js',
				_PS_JS_DIR_.'price.js',
				_PS_JS_DIR_.'tiny_mce/tiny_mce.js',
				_PS_JS_DIR_.'tinymce.inc.js',
				_PS_JS_DIR_.'fileuploader.js',
				_PS_JS_DIR_.'admin-dnd.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.edit.js',
				_PS_JS_DIR_.'admin-categories-tree.js',
				_PS_JS_DIR_.'jquery/ui/jquery.ui.progressbar.min.js',
				_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js'
			));

			$this->addCSS(array(
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css',
				_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
			));
		}
	}
	public function initContent($token = null)
	{
		if ($this->display == 'edit' || $this->display == 'add')
		{
			$this->fields_form = array();

			if (method_exists($this, 'initForm'.$this->tab_display))
				$this->tpl_form = strtolower($this->tab_display).'.tpl';

			if ($this->ajax)
				$this->content_only = true;
			else
			{
				$product_tabs = array();

				// tab_display defines which tab to display first
				if (!method_exists($this, 'initForm'.$this->tab_display))
					$this->tab_display = $this->default_tab;

				$advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
				$stock_management_active = Configuration::get('PS_STOCK_MANAGEMENT');

				foreach ($this->available_tabs as $product_tab => $value)
				{
					// if it's the warehouses tab and advanced stock management is disabled, continue
					if ($advanced_stock_management_active == 0 && $product_tab == 'Warehouses')
						continue;

					$product_tabs[$product_tab] = array(
						'id' => $product_tab,
						'selected' => (strtolower($product_tab) == strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module'.$this->tab_display_module == Tools::strtolower($product_tab))),
						'name' => $this->available_tabs_lang[$product_tab],
						'href' => $this->context->link->getAdminLink('AdminProducts').'&amp;id_product='.(int)Tools::getValue('id_product').'&amp;action='.$product_tab,
					);
				}
				$this->tpl_form_vars['product_tabs'] = $product_tabs;
			}
		}

		// @todo module free
		$this->tpl_form_vars['vat_number'] = file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php');

		parent::initContent();
	}

	
}