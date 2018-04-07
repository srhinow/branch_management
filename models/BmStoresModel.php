<?php
namespace Srhinow\BranchManagement\Models;

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2018 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */


use Contao\Model;


/**
 * Reads and writes store
 *
 * @package   Models
 * @author    Leo Feyer <https://github.com/leofeyer>
 * @copyright Leo Feyer 2005-2014
 */
class BmStoresModel extends Model
{
	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_bm_stores';

	/**
	 * Find published news items by their parent ID and ID or alias
	 *
	 * @param mixed $varId      The numeric ID or alias name
	 * @param array $arrOptions An optional options array
	 *
	 * @return \Model|null The NewsModel or null if there are no news
	 */
	public static function findStoreByIdOrAlias($varId, array $arrOptions=array())
	{
		$t = static::$strTable;

		return static::findOneBy( $varId, $arrOptions);
	}	

	/**
	 * Find all store items
	 *
	 * @param integer $intPid     The news archive ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of models or null if there are no news
	 */
	public static function findStores($intLimit=0, $intOffset=0, array $geodata=array(), $distance=1, array $arrIds=array(), array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = null;

		if(strlen($geodata['lat']) && strlen($geodata['lon']) && (int) $distance > 0)
		{
			$arrColumns = array("ACOS( SIN(RADIANS($t.lat)) * SIN(RADIANS(".$geodata['lat'].")) + COS(RADIANS($t.lat)) * COS(RADIANS(".$geodata['lat'].")) * COS(RADIANS($t.lon) - RADIANS(".$geodata['lon']."))) * 6380 <= ".$distance);
			// $arrOptions['order'] = "$t.distance DESC";
		}
		if(is_array($arrIds) && count($arrIds) > 0)
		{
			$arrColumns = array("$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")");
		}

		if (!isset($arrOptions['order']))
		{
			$arrOptions['order'] = "$t.ort ASC";
		}
		
		$arrOptions['limit']  = $intLimit;
		$arrOptions['offset'] = $intOffset;


		return static::findBy($arrColumns, null, $arrOptions);
	}
	/**
	 * Count all store items
	 *
	 * @param integer $intPid     The news archive ID
	 * @param array   $arrOptions An optional options array
	 *
	 * @return \Model\Collection|null A collection of models or null if there are no news
	 */
	public static function countStoreEntries(array $geodata=array(), $distance=1, array $arrOptions=array())
	{
		$t = static::$strTable;
		$arrColumns = null;

		if(strlen($geodata['lat']) && strlen($geodata['lon']) && (int) $distance > 0)
		{
			$arrColumns = array("ACOS( SIN(RADIANS($t.lat)) * SIN(RADIANS(".$geodata['lat'].")) + COS(RADIANS($t.lat)) * COS(RADIANS(".$geodata['lat'].")) * COS(RADIANS($t.lon) - RADIANS(".$geodata['lon']."))) * 6380 <= ".$distance);			
		}

		return static::countBy($arrColumns, null, $arrOptions);
	}
}
