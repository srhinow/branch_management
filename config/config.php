<?php

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2015 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */
/**
 * -------------------------------------------------------------------------
 * MODULE CONSTANTS
 * -------------------------------------------------------------------------
 */
	@define('BM_VERSION', '1.0');
	@define('BM_BUILD', '1');
	@define('BM_PATH', '/system/modules/branch_management');

	$GLOBALS['BM']['BM_IMAGE_PATH'] = '/files/Stores';
	$GLOBALS['BM']['BM_IMAGE_UPLOAD_TYPES'] = array('jpg','jpeg');

/**
 * -------------------------------------------------------------------------
 * Front END MODULES
 * -------------------------------------------------------------------------
 */
array_insert($GLOBALS['FE_MOD'], 2, array
(
	'Stores' => array
	(
		'bm_search_form'    => 'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchForm',
		'bm_search_list'    => 'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchList',
		'bm_search_map'    => 'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchMap',
		'bm_details'    => 'Srhinow\BranchManagement\Modules\Frontend\ModuleBmDetails',
		'bm_edit_entry'    => 'Srhinow\BranchManagement\Modules\Frontend\ModuleBmEditEntry',
	)
));

/**
 * -------------------------------------------------------------------------
 * MODELS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_MODELS']['tl_bm_stores'] = Srhinow\BranchManagement\Models\BmStoresModel::class;

/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */

$bmBE = array
(
	'bm' => array
	(
		'stores_categories' => array
		(
			'tables' => array('tl_bm_stores_categories'),
			'icon'   => 'system/modules/branch_management/assets/icons/category.png'
		),
		'stores' => array 
		(
			'tables' => array('tl_bm_stores'),
			'stylesheet' => 'system/modules/branch_management/assets/css/be.css',
			'icon'  => 'system/modules/branch_management/assets/icons/entity.png',
			'csvStoreExport' => array('beCSVExport', 'csvStoresExport')
		),
	)
);

array_insert($GLOBALS['BE_MOD'], 1, $bmBE);

/**
 * Setup Modules
 */
$GLOBALS['BN_SETUP_MOD'] = array
(
	'config' => array
	(
		'bn_settings' => array
		(
			'tables'					=> array('tl_bn_settings'),
			'href'						=> '&table=tl_bn_settings&act=edit&id=1',
			// 'callback' 					=> 'ModuleBnProperties',			
			'icon'						=> 'system/modules/branch_management/assets/icons/process.png',
		),
	)
);

// Enable tables in iao_setup
if ($_GET['do'] == 'tl_bm_setup')
{
	foreach ($GLOBALS['BM_SETUP_MOD'] as $strGroup=>$arrModules)
	{
		foreach ($arrModules as $strModule => $arrConfig)
		{
			if (is_array($arrConfig['tables']))
			{

				$GLOBALS['BE_MOD']['bn']['tl_bm_setup']['tables'] = array_merge($GLOBALS['BE_MOD']['bn']['tl_bm_setup']['tables'], $arrConfig['tables']);

			}
		}
	}
}

$GLOBALS['BE_MOD']['accounts']['member']['stylesheet'] = 'system/modules/branch_management/assets/css/be.css';
$GLOBALS['BE_MOD']['accounts']['member']['csvMemberExport'] = array('beCSVExport', 'csvMemberExport');

/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Srhinow\BranchManagement\Hooks\feBmHooks', 'replaceBmInsertTags');
