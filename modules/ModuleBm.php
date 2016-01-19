<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package ModuleBm
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Stores;


/**
 * Class ModuleBn
 *
 * Parent class for news modules.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
abstract class ModuleBm extends \Module
{

	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseStore($objStore, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->item_template);
		$objTemplate->setData($objStore->row());
		$objTemplate->class = (($objStore->cssClass != '') ? ' ' . $objStore->cssClass : '') . $strClass;
		$objTemplate->openStatus = $this->getCurrentOpenStatus($objStore);
		// $objTemplate->distance = $this->getCurrentOpenStatus($objStore);

		//Detail-Url
		if($this->jumpTo)
		{
			$objDetailPage = \PageModel::findByPk($this->jumpTo);
			$objTemplate->detailUrl = ampersand( $this->generateFrontendUrl($objDetailPage->row(),'/'.$objStore->alias) );			
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @return array
	 */
	protected function parseStores($objStores)
	{
		$limit = $objStores->count();
		// print $limit;
		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrStores = array();

		while ($objStores->next())
		{
			$distance = $this->getDistance($objStores);
			$objStores->distance = $distance;

			if(strlen($distance)>0)
			{
				$arrStores[$distance] = array
				(
					'data' => $objStores->row(),
					'distance' => $distance,
					'html' => $this->parseStore($objStores, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count)
				);
			}
			else
			{
				$arrStores[] = array
				(
					'data' => $objStores->row(),
					'distance' => $distance,
					'html' => $this->parseStore($objStores, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count)
				);
			}
		}

		ksort($arrStores);
		return $arrStores;
	}

	/**
	* get status if library current open
	* @param object
	* @return string
	*/
	protected function getCurrentOpenStatus($objStore)
	{
		$status = '';
		$currTime = time();
		$wdArr = array(1=>'mo', 2=>'di', 3=>'mi', 4=>'do', 5=>'fr', 6=>'sa', 7=>'so');
		$wd = $wdArr[date('N')];
		for($i=1 ; $i <= 2 ; $i++)
		{
			if($status == 'open') continue;

			$wd_von = $wd.'_'.$i.'_von';
			$wd_vonTime = mktime(date('H', $objStore->$wd_von) , date('i',$objStore->$wd_von) , date('s',$objStore->$wd_von) , date('n') , date('j') , date('Y'));

			$wd_bis = $wd.'_'.$i.'_bis';
			$wd_bisTime = mktime(date('H',$objStore->$wd_bis) , date('i',$objStore->$wd_bis) , date('s',$objStore->$wd_bis) , date('n') , date('j') , date('Y'));

			if((int) $objStore->$wd_von == 0 || (int) $objStore->$wd_bis == 0) $status =  'close' ;
			
			if(((int) $objStore->$wd_von != 0) && !in_array($status,array('-','open')) )
			{
				$status = ($wd_vonTime <= $currTime && $wd_bisTime >= $currTime) ? 'open' : 'close';
			}
		}

		return $status;
	}

	/**
	* return lat and lon from given location in the session
	* @param string
	* @return array
	*/
	public function getGeoDataFromCurrentPosition($searchVal='')
	{
		$session = $this->Session->get('bmfilter');
		$plzcity = (strlen($searchVal)>0)?$searchVal : $session['plzcity'];
		$plzcity = trim($plzcity);
		$geodata = array();
		
		if(strlen($plzcity) < 1) return $geodata;

		require_once(TL_ROOT.'/'.BM_PATH.'/libs/google_maps_api.php');
		
		$geo = new \googleGeoData();

    	$json = $geo->getGeoData($plzcity);

    	// now, process the JSON string
    	$data = json_decode($json);

	    if(strtolower($data->status) == 'ok')
	    {		

			if (count($data->results[0]->geometry->location) > 0) 
			{	
				$geodata['lat'] = $data->results[0]->geometry->location->lat;
				$geodata['lon'] = $data->results[0]->geometry->location->lng;
				$geodata['plzcity'] = $data->results[0]->formatted_address;
			}
	    }
   		// print_r($data);
   		// exit();
	    return $geodata;
	}

	/**
	* return lat and lon from given location in the session
	* @param string
	* @return array
	*/
	public function getGeoDataFromAddress($address='')
	{
		$address = trim($address);
		$geodata = array();
		
		if(strlen($address) < 1) return $geodata;

		require_once(TL_ROOT.'/'.BM_PATH.'/libs/google_maps_api.php');
		
		$geo = new \googleGeoData();

    	$json = $geo->getGeoData($address);

    	// now, process the JSON string
    	$data = json_decode($json);

	    if(strtolower($data->status) == 'ok')
	    {
			if (count($data->results[0]->geometry->location) > 0) 
			{
				$geodata['lat'] = $data->results[0]->geometry->location->lat;
				$geodata['lon'] = $data->results[0]->geometry->location->lng;
			}
	    }
   		// print_r($data);
   		// exit();
	    return $geodata;
	}

	/**
	* get the distance from search-location and library
	* @param object
	* @return string
	*/
	public function getDistance($objStore)
	{
		$session = $this->Session->get('bmfilter');
		$distance = '';
		if(strlen($session['geo_lat']) > 0 && strlen($session['geo_lon']) > 0 && strlen($session['distance']) > 0)
		{
			$resultObj = $this->Database->prepare("SELECT 
				ACOS(SIN(RADIANS(lat)) * SIN(RADIANS(?)) + COS(RADIANS(lat)) * COS(RADIANS(?)) * COS(RADIANS(lon)- RADIANS(?))) * 6380 AS `distance` 
				FROM `tl_bm_stores`
				WHERE `tl_bm_stores`.`id` = ?
				AND ACOS( SIN(RADIANS(lat)) * SIN(RADIANS(?)) + COS(RADIANS(lat)) * COS(RADIANS(?)) * COS(RADIANS(lon) - RADIANS(?))) * 6380 <= ?")
			->limit(1)
			->execute(
					$session['geo_lat'], $session['geo_lat'], $session['geo_lon'],
					$objStore->id,
					$session['geo_lat'], $session['geo_lat'], $session['geo_lon'],$session['distance']
				);

			if($resultObj->numRows > 0) $distance = $resultObj->distance;
		}

		return $distance;
	}


	/**
	* pruft ob eine Filiale zu einer bestimmten Kategorie gehoert
	* @param object
	* @param integer
	* @return bool
	*/
	public function hasCategory($objStore)
	{
		$session = $this->Session->get('bmfilter');
		$has = false;

		if($session['category'] == $objStore->category) $has = true;

		return $has;
	}
}
