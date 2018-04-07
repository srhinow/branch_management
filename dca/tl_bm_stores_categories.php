<?php
namespace Srhinow\BranchManagement\Dca;

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2018 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */

/**
 * Table tl_bm_stores_categories
 */
use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_bm_stores_categories'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		// 'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'					  => 1,
		),
		'label' => array
		(
			'fields'                  => array('title', 'alias'),
			'format'                  => '%s <span style="color:#b3b3b3; padding-left:3px;">[%s]</span>',
		),
		'global_operations' => array
		(
			'back' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
				'href'                => 'mod=&table=',
				'class'               => 'header_back',
				'attributes'          => 'onclick="Backend.getScrollOffset();"',
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"',
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
			)
		)
	),
	// Palettes
	'palettes' => array
	(
		'default' => 'title,alias,sorting'
	),

	// Subpalettes
	'subpalettes' => array
	(

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
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50','unique'=>true),
			'sql'					=> "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128),
			'sql'					=> "varchar(64) NOT NULL default ''",
			'save_callback' => array
			(
				array('BmStoresCategories', 'generateAlias')
			)

		),
		'sorting' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_bm_stores_categories']['sorting'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>3, 'tl_class'=>'w50'),
			'sql'					=> "varchar(255) NOT NULL default ''"
		),

	)
);


/**
 * Class BmStoresCategories
 */
class BmStoresCategories extends Backend
{
	
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
			$varValue = standardize($dc->activeRecord->title);
		}

		$objAlias = Database::getInstance()->prepare("SELECT id FROM tl_bm_stores_categories WHERE id=? OR alias=?")
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
}
