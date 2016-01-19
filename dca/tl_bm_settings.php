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
 * Table tl_bm_settings
 */
$GLOBALS['TL_DCA']['tl_bm_settings'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => false,
		'onload_callback' => array
		(
			// array('tl_bm_settings', 'create_property_entry')
		),		
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index'
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 11,
			'flag'                    => 12
		),	
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{group_legend},group_libraries;{new_register_legend:hide},send_to,send_to_bcc,newuser_subject,newuser_html_email,newuser_text_email',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_calendar.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'group_libraries' => array
	    (
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['group_libraries'],
			'exclude'                 => true,
			'inputType'               => 'radio',
			'foreignKey'              => 'tl_member_group.name',
			'eval'                    => array('mandatory'=>true,'multiple'=>false),
			'sql'					  => "int(10) unsigned NOT NULL default '0'",
	    ),				    	    	
	    'send_to' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['send_to'],
		    'exclude'                 => true,
		    'search'                  => true,
		    'filter'                  => true,
		    'inputType'               => 'text',
		    'eval'                    => array('maxlength'=>255, 'decodeEntities'=>true, 'tl_class'=>'clr w50'),
		    'sql'					  => "varchar(255) NOT NULL default ''",
	    ),
	    'send_to_bcc' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['send_to_bcc'],
		    'exclude'                 => true,
		    'search'                  => true,
		    'filter'                  => true,
		    'inputType'               => 'text',
		    'eval'                    => array('rgxp'=>'email', 'maxlength'=>128, 'decodeEntities'=>true, 'tl_class'=>'w50'),
		    'sql'					  => "varchar(255) NOT NULL default ''",
	    ),	    
	    'newuser_subject' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['newuser_subject'],
		    'exclude'                 => true,
		    'search'                  => true,
		    'sorting'                 => true,
		    'flag'                    => 11,
		    'inputType'               => 'text',
		    'default'		 		  => &$GLOBALS['TL_LANG']['tl_bm_settings']['confirmed_subject_default'],
		    'eval'                    => array( 'maxlength'=>255, 'tl_class'=>'clr long'),
		    'sql'					  => "varchar(255) NOT NULL default ''",
	    ),
	    'newuser_html_email' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['newuser_html_email'],
		    'exclude'                 => true,
		    'flag'                    => 11,
		    'inputType'               => 'textarea',
		    'eval'                    => array('rte'=>'tinyMCE', 'helpwizard'=>true,'style'=>'height:60px;', 'tl_class'=>'clr'),
		    'sql'					  => "text NULL",
	    ),
	    'newuser_text_email' => array
	    (
		    'label'                   => &$GLOBALS['TL_LANG']['tl_bm_settings']['newuser_text_email'],
		    'exclude'                 => true,
		    'search'                  => true,
		    'inputType'               => 'textarea',
		    'eval'                    => array('decodeEntities'=>true),
		    'sql'					  => "text NULL",
	    ),

	)
);


/**
 * Class tl_bm_settings
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_bm_settings extends Backend
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
     * create an entry if id=1 not exists
     * @return none
     */
  //    public function create_property_entry()
  //    {
		// print_r($GLOBALS['BE_MOD']['content']);
		// exit();
		// if(\Input::get('act') != 'edit' && \Input::get('id') != 1) 
		// {
		// 	$testObj = $this->Database->execute('SELECT * FROM `tl_bm_settings`');

		// 	if($testObj->numRows == 0)
		// 	{
		// 		$this->Database->execute('INSERT INTO `tl_bm_settings`(`id`) VALUES(1)');
		// 	}

		// 	$url = $this->addToUrl('act=edit&id=1');
		// 	// return $this->objDc->edit(1);
		// 	$this->redirect($url);
		// }
  //    }

}

