<?php
namespace Srhinow\BranchManagement\Modules\Frontend;

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2018 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */

use Contao\Input;
use Contao\Session;
use Srhinow\BranchManagement\Models\BmStoresModel;

/**
 * Class ModuleBnSearchMap
 */
class ModuleBmSearchMap extends ModuleBm
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bm_search_map';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### Filial-KARTE-SUCHE ###';						
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['s']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('s', \Input::get('auto_item'));
		}	
				
		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$session = Session::getInstance()->get('bnfilter')?: array();
		
		if(strlen($session['geo_lat'])>0 && strlen($session['geo_lon'])>0)
		{
			$geodata = array
			(
				'lat'=>$session['geo_lat'], 
				'lon'=>$session['geo_lon']
			);
		}
		else
		{
			$geodata = $this->getGeoDataFromCurrentPosition();	
		}


		// Get the total number of items
		$intTotal = BmStoresModel::countStoreEntries($geodata,$session['distance']);

		// Filter anwenden um die Gesamtanzahl zuermitteln
		if($intTotal > 0)		
		{
			$libsObj = BmStoresModel::findStores($intTotal, 0, $geodata, $session['distance']);
			
			$counter = 0;			
			$libs = array();

			while($libsObj->next())
			{
			    //Detail-Url
				if($this->jumpTo)
				{
					$objDetailPage = \PageModel::findByPk($this->jumpTo);	
				}

				//wenn alle Filter stimmen -> Werte setzen
				$libs[] = array
				(
					'lat' => $libsObj->lat,
					'lon' => $libsObj->lon,
					'name' => $libsObj->bibliotheksname,
					'plz' => $libsObj->plz,
					'ort' => $libsObj->ort,
					'strasse' => $libsObj->strasse,
					'hnr' => $libsObj->hausnummer,
					'detailUrl' => ampersand( $this->generateFrontendUrl($objDetailPage->row(),'/lib/'.$libsObj->id) ),
					'openstatus' => $this->getCurrentOpenStatus($libsObj)
				);

				$counter++; 
			}

		}
		if((int) $intTotal > $counter) $intTotal = $counter;
		
		// //set fallback (Hannover)
		if( $geodata['lat'] == '' ) $geodata['lat'] = 52.4544218;
		if( $geodata['lon'] == '') $geodata['lon'] = 9.918507699999999;

		$GLOBALS['TL_JAVASCRIPT'][] = '.'.BM_PATH.'/assets/js/bn_fe.js';
		
		$this->Template->libs = $libs;
		$this->Template->filterActive = Input::get('s') ? true : false;
		$this->Template->geodata = $geodata;
		$this->Template->zoomlevel = count($session) == 0 ? 7 : 9;
		$this->Template->totalItems = $intTotal;
		// $this->Template->showAllUrl = $this->generateFrontendUrl($objPage->row());
	}

}
