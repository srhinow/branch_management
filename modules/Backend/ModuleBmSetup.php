<?php
namespace Srhinow\BranchManagement\Modules\Backend;

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2018 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */

use Contao\BackendModule;
use Contao\BackendUser as User;
use Contao\Environment;

/**
 * Class ModuleBMSetup
 */
class ModuleBMSetup extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_bm_setup';

	/**
	 * Isotope modules
	 * @var array
	 */
	protected $arrModules = array();


	/**
	 * Generate the module
	 * @return string
	 */
	public function generate()
	{

		foreach ($GLOBALS['BM_SETUP_MOD'] as $strGroup => $arrModules)
		{

			foreach ($arrModules as $strModule => $arrConfig)
			{

				if (User::getInstance()->hasAccess($strModule, 'bm_modules'))
				{

					if (is_array($arrConfig['tables']))
					{
						$GLOBALS['BE_MOD']['bn']['tl_bm_setup']['tables'] = array_merge($GLOBALS['BE_MOD']['bn']['tl_bm_setup']['tables'],$arrConfig['tables']);
						// print_r($GLOBALS['BE_MOD']['bn']['tl_bm_setup']['tables']);
					}

					$this->arrModules[$GLOBALS['TL_LANG']['IMD'][$strGroup]][$strModule] = array
					(
						'name' => ($GLOBALS['TL_LANG']['IMD'][$strModule][0] ? $GLOBALS['TL_LANG']['IMD'][$strModule][0] : $strModule),
						'description' => $GLOBALS['TL_LANG']['IMD'][$strModule][1],
						'icon' => $arrConfig['icon'],
						'href' => $arrConfig['href']
					);
				}
			}
		}

		// Open module
		if (\Input::get('mod') != '')
		{	
			return $this->getBNModule($this->Input->get('mod'));
		}

		// Table set but module missing, fix the saveNcreate link
		elseif ($this->Input->get('table') != '')
		{
			foreach ($GLOBALS['BM_SETUP_MOD'] as $arrGroup)
			{
				foreach( $arrGroup as $strModule => $arrConfig )
				{
					if (is_array($arrConfig['tables']) && in_array($this->Input->get('table'), $arrConfig['tables']))
					{
						$url = $this->addToUrl('mod=' . $strModule);
						if($arrConfig['href']) $url = $this->addToUrl($arrConfig['href']);
						$this->redirect($url);
					}
				}
			}
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$this->Template->modules = $this->arrModules;
		$this->Template->script = $this->Environment->script;
		$this->Template->welcome = sprintf($GLOBALS['TL_LANG']['BN']['config_module'], BM_VERSION . '.' . BM_BUILD);
	}


	/**
	 * Open an bn module and return it as HTML
	 * @param string
	 * @return mixed
	 */
	protected function getBNModule($module)
	{
		$arrModule = array();
// print_r($GLOBALS['BM_SETUP_MOD']);
		// print $module;
		foreach ($GLOBALS['BM_SETUP_MOD'] as $arrGroup)
		{
			if (!empty($arrGroup) && in_array($module, array_keys($arrGroup)))
			{
				$arrModule =& $arrGroup[$module];
			}
		}

		// Check whether the current user has access to the current module
		if (!$this->User->isAdmin && !$this->User->hasAccess($module, 'bm_modules'))
		{
			log('invoice_and_offer module "' . $module . '" was not allowed for user "' . $this->User->username . '"', 'ModuleIsotopeSetup getIsotopeModule()', TL_ERROR);
			$this->redirect($this->Environment->script.'?act=error');
		}

		$strTable = $this->Input->get('table');

		
		if ($strTable == '' && $arrModule['callback'] == '')
		{
			// print $this->addToUrl('table='.$arrModule['tables'][0]);
			$this->redirect($this->addToUrl('table='.$arrModule['tables'][0]));
		}

		$id = (!$this->Input->get('act') && $this->Input->get('id')) ? $this->Input->get('id') : $this->Session->get('CURRENT_ID');

		// Add module style sheet
		if (isset($arrModule['stylesheet']))
		{
			$GLOBALS['TL_CSS'][] = $arrModule['stylesheet'];
		}

		// Add module javascript
		if (isset($arrModule['javascript']))
		{
			$GLOBALS['TL_JAVASCRIPT'][] = $arrModule['javascript'];
		}

		// Redirect if the current table does not belong to the current module
		if ($strTable != '')
		{
			if (!in_array($strTable, (array) $arrModule['tables']))
			{
				log('Table "' . $strTable . '" is not allowed in BN module "' . $module . '"', 'ModuleIsotopeSetup getIsotopeModule()', TL_ERROR);
				$this->redirect('contao/main.php?act=error');
			}

			// Load the language and DCA file
			$this->loadLanguageFile($strTable);
			$this->loadDataContainer($strTable);

			// Include all excluded fields which are allowed for the current user
			if ($GLOBALS['TL_DCA'][$strTable]['fields'])
			{
				foreach ($GLOBALS['TL_DCA'][$strTable]['fields'] as $k=>$v)
				{
					if ($v['exclude'])
					{
						if ($this->User->hasAccess($strTable.'::'.$k, 'alexf'))
						{
							$GLOBALS['TL_DCA'][$strTable]['fields'][$k]['exclude'] = false;
						}
					}
				}
			}

			// Fabricate a new data container object
			if (!strlen($GLOBALS['TL_DCA'][$strTable]['config']['dataContainer']))
			{
				$this->log('Missing data container for table "' . $strTable . '"', 'Backend getBackendModule()', TL_ERROR);
				trigger_error('Could not create a data container object', E_USER_ERROR);
			}

			$dataContainer = 'DC_' . $GLOBALS['TL_DCA'][$strTable]['config']['dataContainer'];
			$dc = new $dataContainer($strTable, $arrModule);
		}

		// AJAX request
		if ($_POST && Environment::get('isAjaxRequest'))
		{
			$this->objAjax->executePostActions($dc);
		}

		// Call module callback
		elseif ($this->classFileExists($arrModule['callback']))
		{
			$objCallback = new $arrModule['callback']($dc);
			return $objCallback->generate();
		}

		// Custom action (if key is not defined in config.php the default action will be called)
		elseif ($this->Input->get('key') && isset($arrModule[$this->Input->get('key')]))
		{
			$objCallback = new $arrModule[$this->Input->get('key')][0]();
			return $objCallback->$arrModule[$this->Input->get('key')][1]($dc, $strTable, $arrModule);
		}

		// Default action
		elseif (is_object($dc))
		{
			$act = $this->Input->get('act');

			if (!strlen($act) || $act == 'paste' || $act == 'select')
			{
				$act = ($dc instanceof listable) ? 'showAll' : 'edit';
			}

			switch ($act)
			{
				case 'delete':
				case 'show':
				case 'showAll':
				case 'undo':
					if (!$dc instanceof listable)
					{
						$this->log('Data container ' . $strTable . ' is not listable', 'Backend getBackendModule()', TL_ERROR);
						trigger_error('The current data container is not listable', E_USER_ERROR);
					}
					break;

				case 'create':
				case 'cut':
				case 'cutAll':
				case 'copy':
				case 'copyAll':
				case 'move':
				case 'edit':
					if (!$dc instanceof editable)
					{
						log('Data container ' . $strTable . ' is not editable', 'Backend getBackendModule()', TL_ERROR);
						trigger_error('The current data container is not editable', E_USER_ERROR);
					}
					break;
			}

			return $dc->$act();
		}

		return null;
	}
}

