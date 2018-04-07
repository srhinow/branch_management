<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2018 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Srhinow',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'beCSVExport'                                                  => 'system/modules/branch_management/classes/beCSVExport.php',
	'feBmHooks'                                                    => 'system/modules/branch_management/classes/feBmHooks.php',

	// Modules
	'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchMap'  => 'system/modules/branch_management/modules/Frontend/ModuleBmSearchMap.php',
	'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchList' => 'system/modules/branch_management/modules/Frontend/ModuleBmSearchList.php',
	'Srhinow\BranchManagement\Modules\Frontend\ModuleBmSearchForm' => 'system/modules/branch_management/modules/Frontend/ModuleBmSearchForm.php',
	'Srhinow\BranchManagement\Modules\Frontend\ModuleBm'           => 'system/modules/branch_management/modules/Frontend/ModuleBm.php',
	'Srhinow\BranchManagement\Modules\Frontend\ModuleBmDetails'    => 'system/modules/branch_management/modules/Frontend/ModuleBmDetails.php',

	// Models
	'Srhinow\BranchManagement\Models\BmStoresModel'                => 'system/modules/branch_management/models/BmStoresModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_bm_search_shortform' => 'system/modules/branch_management/templates/modules',
	'mod_bm_details'          => 'system/modules/branch_management/templates/modules',
	'mod_bm_entries_empty'    => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_map'       => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_form'      => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_list'      => 'system/modules/branch_management/templates/modules',
	'mod_bm_edit_entry'       => 'system/modules/branch_management/templates/modules',
	'item_html_list'          => 'system/modules/branch_management/templates/items',
	'be_bm_setup'             => 'system/modules/branch_management/templates/backend',
));
