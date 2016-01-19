<?php

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    bn_libraries
 * @license    commercial
 * @filesource
 */

/**
 * Table tl_bm_stores
 */
$GLOBALS['TL_DCA']['tl_bm_stores'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'onsubmit_callback' => array
		(
			array('tl_bm_stores', 'setFirstGeoLatLon'),
			// array('tl_bm_stores', 'setAllGeoLatLon')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('filialname'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,limit;search',
			// 'child_record_class'      => 'no_padding'
		),
		'label' => array
		(
			'fields'                  => array('filialname', 'zweigstellenname', 'plz', 'ort'),
			'format'                  => '%s - %s (%s %s)',
// 			'label_callback'          => array('tl_bbk', 'listEntries'),
		),
		'global_operations' => array
		(
			'csvStoreExport' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores']['csvStoreExport'],
				'href'                => 'key=csvStoreExport',
				'class'               => 'export_csv',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('addContacts','addImages','addOpenDates'),
		'default'                     => '{main_legend},filialname,alias,category;
										  {address_legend},strasse,hausnummer,plz,ort;
										  {geodata_legend},lat,lon,setnewgeo;
										  {contact_legend},addContacts;
										  {openingtimes_legend},addOpenDates;
										  {images_legend},addImages;
										  {extend_legend:hide},memo',
	),

	// Subpalettes
	'subpalettes' => array
	(
		'addContacts' => 	'telefon,fax,email,website,facebook,gplus,twitter,blog',
		'addImages' => 	'imageUpload',
		'addOpenDates' => 'mo_1_von,mo_1_bis,mo_2_von,mo_2_bis,di_1_von,di_1_bis,di_2_von,di_2_bis,mi_1_von,mi_1_bis,mi_2_von,mi_2_bis,do_1_von,do_1_bis,do_2_von,do_2_bis,fr_1_von,fr_1_bis,fr_2_von,fr_2_bis,sa_1_von,sa_1_bis,sa_2_von,sa_2_bis,so_1_von,so_1_bis,so_2_von,so_2_bis,sonst_oeffnungszeiten'
	),
	
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'sorting'                 => true,
			'flag'                    => 5,
			'eval'						=> array('rgxp' => 'datim')
		),
		'filialname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['filialname'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>128, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'libdetails', 'tl_class'=>'long'),
			'sql'					  => "varchar(128) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128),
			'sql'					=> "varchar(64) NOT NULL default ''",
			'save_callback' => array
			(
				array('tl_bm_stores', 'generateAlias')
			)

		),
		'category' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['category'],
		    'exclude'                 => false,
		    'filter'                  => true,
		    'sorting'                 => true,
			'inputType'               => 'select',
			'foreignKey'			  => 'tl_bm_stores_categories.title',
			'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>true, 'chosen'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'libdetails', 'tl_class'=>'w50'),
   			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'strasse' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['strasse'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>128, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'sql'					  => "varchar(128) NOT NULL default ''"
		),
		'hausnummer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['hausnummer'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>28, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'sql'					  => "varchar(28) NOT NULL default ''"
		),
		'plz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['plz'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>32, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'sql'					  => "varchar(32) NOT NULL default ''"
		),
		'ort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['ort'],
			'exclude'                 => true,
			'filter'                  => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'lat' => array
		(
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['lat'],
			'search'				=> true,
			'eval'                    => array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'inputType'   => 'text',
			'sql'	=> 'double NOT NULL'
		),
		'lon' => array
		(
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['lon'],
		    'search'				=> true,
			'eval'                    => array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'w50'),
			'inputType'   => 'text',
			'sql'	=> 'double NOT NULL'
		),
		'setnewgeo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['setnewgeo'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'sql'                     => "char(1) NOT NULL default ''",
			'eval'                    => array('tl_class'=>'clr'),
			'save_callback' => array
			(
				array('tl_bm_stores', 'setNewGeoLatLon')
			),
		),
		'addContacts' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['addContacts'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'telefon' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['telefon'],
			'exclude'                 => true,
			'search'                  => false,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>64, 'rgxp'=>'phone', 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(64) NOT NULL default ''"
		),
		'fax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['fax'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>64, 'rgxp'=>'phone', 'decodeEntities'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(64) NOT NULL default ''"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['email'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'email', 'maxlength'=>128, 'decodeEntities'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'website' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['website'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'blog' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['blog'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'facebook' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['facebook'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'gplus' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['gplus'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'twitter' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['twitter'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'maxlength'=>255, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'contact', 'tl_class'=>'w50'),
			'sql'					  => "varchar(255) NOT NULL default ''"
		),
		'imageUpload' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['imageUpload'],
			'input_field_callback'    => array('tl_bm_stores', 'imageUpload')
		),
		'addImages' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['addImages'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'image_1' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['image_1'],
		    'sql'					  => "blob NULL",
		),
		'image_2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['image_2'],
		    'sql'					  => "blob NULL",
		),
		'image_3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['image_3'],
		    'sql'					  => "blob NULL",
		),
		'image_4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['image_4'],
		    'sql'					  => "blob NULL",
		),
		'image_5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['image_5'],
		    'sql'					  => "blob NULL",
		),
		'addOpenDates' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['addOpenDates'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'mo_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mo_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mo_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mo_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mo_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mo_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mo_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mo_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'di_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['di_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'di_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['di_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'di_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['di_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'di_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['di_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mi_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mi_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mi_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mi_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mi_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mi_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'mi_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['mi_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'do_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['do_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'do_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['do_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'do_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['do_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'do_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['do_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'fr_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['fr_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'fr_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['fr_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'fr_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['fr_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'fr_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['fr_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'sa_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['sa_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'sa_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['sa_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'sa_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['sa_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'sa_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['sa_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'so_1_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['so_1_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'so_1_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['so_1_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'so_2_von' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['so_2_von'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'so_2_bis' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['so_2_bis'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'maxlength'=>10, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NULL"
		),
		'sonst_oeffnungszeiten' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['sonst_oeffnungszeiten'],
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('style'=>'height:60px;', 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'oeffnungszeiten', 'tl_class'=>'clr long'),
			'sql'					  => "text NULL"
		),
		'memo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores']['memo'],
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('style'=>'height:60px;', 'tl_class'=>'clr'),
			'sql'					  => "text NULL"
		),
	)
);


/**
 * Class tl_bm_stores
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_bm_stores extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Autogenerate an article alias if it has not been set yet
	 * @param mixed
	 * @param object
	 * @return string
	 */
	public function generateAlias($varValue, DataContainer $dc)
	{
		$autoAlias = false;

		// Generate alias if there is none
		if (!strlen($varValue))
		{
			$autoAlias = true;
			$varValue = standardize($dc->activeRecord->filialname);
		}

		$objAlias = $this->Database->prepare("SELECT id FROM tl_bm_stores WHERE id=? OR alias=?")
								   ->execute($dc->id, $varValue);

		// Check whether the page alias exists
		if ($objAlias->numRows > 1)
		{
			if (!$autoAlias)
			{
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}

			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}
	/**
	 * Check permissions to edit table tl_bm_stores
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (!is_array($this->User->calendars) || empty($this->User->calendars))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->calendars;
		}

		$id = strlen($this->Input->get('id')) ? $this->Input->get('id') : CURRENT_ID;

		// Check current action
		switch ($this->Input->get('act'))
		{
			case 'create':
				if (!strlen($this->Input->get('pid')) || !in_array($this->Input->get('pid'), $root))
				{
					$this->log('Not enough permissions to create Event Reservation in channel ID "'.$this->Input->get('pid').'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'edit':
			case 'show':
			case 'copy':
			case 'delete':
			case 'toggle':
				$objRecipient = $this->Database->prepare("SELECT pid FROM tl_bm_stores WHERE id=?")
											   ->limit(1)
											   ->execute($id);

				if ($objRecipient->numRows < 1)
				{
					$this->log('Invalid Event Reservation ID "'.$id.'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				if (!in_array($objRecipient->pid, $root))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' recipient ID "'.$id.'" of calendar event ID "'.$objRecipient->pid.'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'select':
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				if (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access calendar event ID "'.$id.'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$objRecipient = $this->Database->prepare("SELECT id FROM tl_bm_stores WHERE pid=?")
											 ->execute($id);

				if ($objRecipient->numRows < 1)
				{
					$this->log('Invalid Event Reservation ID "'.$id.'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$session = $this->Session->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objRecipient->fetchEach('id'));
				$this->Session->setData($session);
				break;

			default:
				if (strlen($this->Input->get('act')))
				{
					$this->log('Invalid command "'.$this->Input->get('act').'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				elseif (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access Event Reservation ID "'.$id.'"', 'tl_bm_stores checkPermission', TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	/**
	 * upload images like Frontend-html
	 * @param \DataContainer
	 * @return string
	 */
	public function imageUpload(DataContainer $dc)
	{
		$dc->blnUploadable =true;
		$filecount = 5;
		$html = '';
		$bnImages = array();

		for($i=1 ; $i <= $filecount ; $i++)
		{
			$bnImages[$i] = $dc->activeRecord->{'image_'.$i};
		}

		// set new value if uploadet file
		if (Input::post('FORM_SUBMIT') == 'tl_bm_stores')
		{
			//Bilder loeschen;
			$bnImages = $this->removeImages($bnImages);

			//Bilder hochladen
			for($c=1; $c <= $filecount; $c++)
			{
				$bnImages[$c] = $this->uploadImage($_FILES['image_'.$c], $c, $bnImages);
				

			}

			//Luecken schliessen und bei umsortierung auch umbennen
			$bnImages = $this->sortAndRename($bnImages);

			$set = array();
			foreach($bnImages as $k => $path) $set['image_'.$k] = $path;

			$this->Database->prepare('UPDATE `tl_bm_stores` %s WHERE `id`=?')
			->set($set)
			->limit(1)
			->execute(\Input::get('id'));

		}

		for($i=1 ; $i <= $filecount ; $i++)
		{
			$html .= '
					<div class="w50">
					    <div class="form-group">
							<label>Bild '.$i.' (hochladen):</label>
							<input type="file" name="image_'.$i.'" value="">
							<p class="help-block">Es sind folgende Dateitypen erlaubt jpg,jpeg</p>
						</div><!-- form-group -->
					</div><!-- w50 -->
					<div class="w50" style="height:auto;">
					    <div class="form-group">
							<label>Bild '.$i.' (Vorschau):</label>';
			if($bnImages[$i] == ''):
				$html .= '<p>Es ist kein Bild gespeichert</p>';
			else:
				$html .= '<p>
						<img src="'.$bnImages[$i].'" width="300" height="225" alt="" class="img-thumbnail"><br>
						<label><input type="checkbox" name="delete_file[]" value="'.$bnImages[$i].'"> löschen</label>
					</p>';

			endif;
			$html .= '</div><!-- form-group -->
					</div><!-- w50 -->
				<div class="clr"></div>';
		}

		return $html;
	}

	private function removeImages($bnImages)
	{
		//testen ob das Bild mit dieser Nummer geloescht werden soll
		$deleteFileArr = \Input::post('delete_file');
		if(is_array($deleteFileArr) && count($deleteFileArr) > 0)
		{
			foreach($deleteFileArr as $delFile)
			{
				if(!file_exists(TL_ROOT . '/' . $delFile) || !in_array($delFile,$bnImages)) continue;

				$this->import('Files');
				$this->Files->delete($delFile);

				foreach($bnImages as $k => $v) 
					if($v == $delFile) $bnImages[$k] = '';
			}
		}

		return $bnImages;
	}

	private function uploadImage($file, $nr, $bnImages)
	{
		global $erroMsg;
		$returnPath = '';
		$this->import('Files');

		//hole zur aktuellen Buecherei den User
		// $memberObj = $this->Database->prepare('SELECT * FROM `tl_member` WHERE `library_id`=?')
		// 							->limit(1)
		// 							->execute(\Input::get('id'));

		// hochgeladenes Bild verarbeiten
		if(is_uploaded_file($file['tmp_name']) && $file['error'] == 0)
		{

			$file['name'] = utf8_romanize($file['name']);
			$fileInfos = pathinfo($file['name']);
			$fileInfos['extension'] = strtolower($fileInfos['extension']);
			$destFilePath = TL_ROOT.$GLOBALS['BN']['BN_IMAGE_PATH'].'/user_'.\Input::get('id');
			$error = false;

			// Add the user ID if the directory exists
			if ( is_dir($destFilePath) )
			{
				//Dateityp testen
				if(!in_array($fileInfos['extension'], $GLOBALS['BN']['BN_IMAGE_UPLOAD_TYPES']))
				{
					$erroMsg = (sprintf($GLOBALS['TL_LANG']['ERR']['wrong_filetyp'] , $file['name'], implode(', ',$GLOBALS['BN']['BN_IMAGE_UPLOAD_TYPES'])));
					$error = true;
					unset($_FILES['image_'.$nr]);
				}

				if(!$error)
				{
					$newFileName = 'image_'.$nr.'.'.$fileInfos['extension'];
					$destFilePath = $GLOBALS['BN']['BN_IMAGE_PATH'].'/user_'.\Input::get('id');

					if($this->Files->move_uploaded_file($file['tmp_name'], $destFilePath.'/'.$newFileName) )
					{
						$this->Files->chmod($destFilePath.'/'.$newFileName, $GLOBALS['TL_CONFIG']['defaultFileChmod']);
						$returnPath = $destFilePath.'/'.$newFileName;
					}

				}
			}
		}
		else
		{
			$returnPath = $bnImages[$nr];
		}

		return $returnPath;
	}

	private function sortAndRename($bnImages)
	{
		$this->import('Files');
		$newArr = array();
		$c = 1;
		foreach($bnImages as $k => $img)
		{
			if($img == '') continue;
			$oldInfos = pathinfo($img);

			$newPath = $oldInfos['dirname'].'/image_'.$c.'.'.$oldInfos['extension'];
			$this->Files->rename($img,$newPath);
			$newArr[$c] = $newPath;
			$c++;
		}
		for($c2 = $c; $c2<=5; $c2++) $newArr[$c2] ='';

		return $newArr;
	}

	public function setFirstGeoLatLon(DataContainer $dc) 
	{

		if((int) $dc->activeRecord->lat == 0 || (int) $dc->activeRecord->lon == 0)
		{
			$this->setGeoLatLon($dc);
		}
	}

	public function setNewGeoLatLon($varValue, DataContainer $dc) 
	{

		if((int) $varValue == 1)
		{
			$this->setGeoLatLon($dc);
		}
	}

	/**
	* set the geolocation if its empty
	* @param \DataContainer
	*/
	public function setGeoLatLon(DataContainer $dc)
	{

		$addressStr = urlencode($dc->activeRecord->strasse.' '.$dc->activeRecord->hausnummer.', '.$dc->activeRecord->plz.' '.$dc->activeRecord->ort.', Deutschland');

		require_once(TL_ROOT.'/'.BM_PATH.'/libs/google_maps_api.php');

		    $geo = new googleGeoData();
			$json = $geo->getGeoData($addressStr);

			$data = json_decode($json);

		    if(strtolower($data->status) == 'ok')
		    {
				if (count($data->results[0]->geometry->location) > 0)
				{
					$set = array
					(
						'lat' => $data->results[0]->geometry->location->lat,
						'lon' => $data->results[0]->geometry->location->lng,
						'setnewgeo' => ''
					);

					$this->Database->prepare('UPDATE `tl_bm_stores` %s WHERE id=?')->set($set)->execute($dc->id);
				}
		    }
	}

	/**
	* set all geolocation if its empty
	* @param \DataContainer
	*/
	public function setAllGeoLatLon(DataContainer $dc)
	{
		$resObj = $this->Database->prepare('SELECT * FROM `tl_bm_stores` WHERE lat=0 OR lon=0')->execute();

		if($resObj->numRows > 1)
		{
			require_once(TL_ROOT.'/'.BN_PATH.'/libs/google_maps_api.php');

			while($resObj->next())
			{
			    $addressStr = urlencode($resObj->strasse.' '.$resObj->hausnummer.', '.$resObj->plz.' '.$resObj->ort.', Deutschland');

			    $geo = new googleGeoData();
    			$json = $geo->getGeoData($addressStr);

    			$data = json_decode($json);

			    if(strtolower($data->status) == 'ok')
			    {
					if (count($data->results[0]->geometry->location) > 0)
					{
						$set = array
						(
							'lat' => $data->results[0]->geometry->location->lat,
							'lon' => $data->results[0]->geometry->location->lng
						);

						$this->Database->prepare('UPDATE `tl_bm_stores` %s WHERE id=?')->set($set)->execute($resObj->id);
					}
			    }
			}
		}
	}
	/**
	 * Return the "toggle visibility" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_bm_stores::accepted', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['accepted'] ? '' : 1);

		if (!$row['accepted'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}


	/**
	 * Disable/enable a user group
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible)
	{
		// Check permissions to edit
		$this->Input->setGet('id', $intId);
		$this->Input->setGet('act', 'toggle');
		$this->checkPermission();

		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_bm_stores::accepted', 'alexf'))
		{
			$this->log('Not enough permissions to publish/unpublish event reservation ID "'.$intId.'"', 'tl_bm_stores toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_bm_stores', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_bm_stores']['fields']['accepted']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_bm_stores']['fields']['accepted']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_bm_stores SET modify=". time() .", accepted='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$this->createNewVersion('tl_bm_stores', $intId);
	}

	public function replacePlaceHolder($dbObj,$text)
	{

	     preg_match_all('/\#\#([^\#]+)\#\#/', $text, $tags);
	     for($c=0;$c<count($tags[0]);$c++)
	     {
		 switch($tags[1][$c])
		 {

		 case 'date':
		 case 'startDate':
		     $text = str_replace($tags[0][$c],$this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'],$dbObj->startDate),$text);
		 break;
		 case 'endDate':
		     $text = str_replace($tags[0][$c],$this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'],$dbObj->endDate),$text);
		 break;

		 default:
		     $text = str_replace($tags[0][$c],$dbObj->$tags[1][$c],$text);
		 }
             }
             return $text;
	}

	/**
	 * fill Text before
	 * @param object
	 * @throws Exception
	 */
	public function fillEmailFields($varValue, DataContainer $dc)
	{


		$result = $this->Database->prepare('SELECT * FROM `tl_bbk_properties`')
						->limit(1)
						->execute();
		switch($varValue)
		{
		case '1':
		    $subject = $result->confirmed_subject;
		    $html = $result->confirmed_html_email;
		    $text = $result->confirmed_text_email;
		break;
		case '2':
		    $subject = $result->rejected_subject;
		    $html = $result->rejected_html_email;
		    $text = $result->rejected_text_email;
		break;
		default:
		    $subject = '';
		    $html = '';
		    $text = '';
		}

		//Insert Invoice-Entry
		$postenset = array(
		    'subject' => $subject,
		    'html_email' => $html,
		    'text_email' => $text,
		    'accepted' => $varValue
		);

		$this->Database->prepare('UPDATE `tl_bm_stores` %s WHERE `id`=?')
			       ->set($postenset)
			       ->execute($dc->id);
                $this->reload();
		return $varValue;
	}

}

?>
