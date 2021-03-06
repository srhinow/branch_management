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
 * Table tl_member
 */
use Contao\Database;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_member']['list']['label']['fields'] = array('icon', 'store_name', 'lastname', 'username');

/**
* 
*/
// array_insert($GLOBALS['TL_DCA']['tl_member']['list']['global_operations'],0,array(
// 		'csvMemberExport' => array
// 			(
// 				'label'               => &$GLOBALS['TL_LANG']['tl_member']['csvMemberExport'],
// 				'href'                => 'key=csvMemberExport',
// 				'class'               => 'export_csv',
// 				'attributes'          => 'onclick="Backend.getScrollOffset();"'
// 			)
// 		)
// );
// Palettes
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace('{address_legend:hide},', '{address_legend:hide},store_id,fillfields,', $GLOBALS['TL_DCA']['tl_member']['palettes']['default']);


// Fields
$GLOBALS['TL_DCA']['tl_member']['fields']['firstname']['eval']['mandatory'] = false;
$GLOBALS['TL_DCA']['tl_member']['fields']['lastname']['eval']['mandatory'] = false;
// unset($GLOBALS['TL_DCA']['tl_member']['fields']['company']);
$GLOBALS['TL_DCA']['tl_member']['fields']['street']['eval']['tl_class'] = 'long';
$GLOBALS['TL_DCA']['tl_member']['fields']['country']['default'] = 'de';
$GLOBALS['TL_DCA']['tl_member']['fields']['language']['default'] = 'de';
$GLOBALS['TL_DCA']['tl_member']['fields']['store_id'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['store_id'],
    'exclude'                 => false,
    'filter'                  => true,
    'sorting'                 => true,
    'inputType'               => 'select',
    // 'foreignKey'			=> 'tl_bm_stores.filialsname',
    'options_callback'        => array('BmMember', 'getStoresOptions'),
    'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address','tl_class'=>'long'),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_member']['fields']['fillfields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['fillfields'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''",
	'save_callback' => array
	(
		array('tl_bzn_member', 'fillLibraryFields')
	),
);
$GLOBALS['TL_DCA']['tl_member']['fields']['store_name'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['store_name'],
	'exclude'                 => true,
	'search'                  => true,
	'sorting'                => true,
	'flag'					=> 11,
	'inputType'               => 'text',
	'eval'                    => array( 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'address', 'tl_class'=>'long'),
    'sql'                     => "varchar(255) NOT NULL default ''"	
);




/**
 * Class tl_bbk_member
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class BmMember extends \tl_member
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function get_states(DataContainer $dc)
	{
		$varValues = array();

		$all = Database::getInstance()->prepare('SELECT s.* FROM `state` s')->execute();
		
		while($all->next())
		{
            $varValues[$all->id] = $all->name;
		}
		return $varValues;
	}

    /**
     * get custom view from library-item-options
     * @param $dc
     * @return array
     */
	public function getStoresOptions($dc)
	{
            $varValue = array();
            $groupID = 1;

            $all = Database::getInstance()->prepare('SELECT * FROM `tl_bm_stores`  ORDER BY `ort` ASC')
				  ->execute(1);

            while($all->next())
            {
				$varValue[$all->id] = $all->plz.' '.$all->ort.' ('.$all->filialsname.' '.$all->zweigstellenname.')';
            }

	    return $varValue;
	}
}

