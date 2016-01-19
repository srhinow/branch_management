<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Stores',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'feBmHooks'                 => 'system/modules/branch_management/classes/feBmHooks.php',
	'beCSVExport'               => 'system/modules/branch_management/classes/beCSVExport.php',

	// Models
	'Stores\BmStoresModel'      => 'system/modules/branch_management/models/BmStoresModel.php',

	// Modules
	'Stores\ModuleBmSearchList' => 'system/modules/branch_management/modules/ModuleBmSearchList.php',
	'Stores\ModuleBmSearchForm' => 'system/modules/branch_management/modules/ModuleBmSearchForm.php',
	'Stores\ModuleBmDetails'    => 'system/modules/branch_management/modules/ModuleBmDetails.php',
	'Stores\ModuleBmSearchMap'  => 'system/modules/branch_management/modules/ModuleBmSearchMap.php',
	'Stores\ModuleBm'           => 'system/modules/branch_management/modules/ModuleBm.php',
	'Stores\ModuleBmEditEntry'  => 'system/modules/branch_management/modules/ModuleBmEditEntry.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_bm_setup'             => 'system/modules/branch_management/templates/backend',
	'item_html_list'          => 'system/modules/branch_management/templates/items',
	'mod_bm_search_list'      => 'system/modules/branch_management/templates/modules',
	'mod_bm_details'          => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_shortform' => 'system/modules/branch_management/templates/modules',
	'mod_bm_edit_entry'       => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_map'       => 'system/modules/branch_management/templates/modules',
	'mod_bm_search_form'      => 'system/modules/branch_management/templates/modules',
	'mod_bm_entries_empty'    => 'system/modules/branch_management/templates/modules',
));
