<?php

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    bm_libraries (www.bibliotheken-niedersachsen.de/)
 * @license    commercial
 * @filesource
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Stores;


/**
 * Class ModuleBnSearchRegion
 *
 * Front end module "bn search list".
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 */
class ModuleBmSearchForm extends \ModuleBm
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bm_search_form';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### Filial-REGION-SUCHE ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		// Ajax Requests abfangen
		if(\Input::get('id') && $this->Environment->get('isAjaxRequest')){
		     $this->generateAjax();
		     exit;
		}

        // template overwrite
		if (strlen($this->mod_bm_template)) $this->strTemplate = $this->mod_bm_template;

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
		$this->import('FrontendUser','User');

		$this->Template->session = false;
		$session = $this->Session->get('bmfilter')?: array();

		//Formular verarbeiten wenn es gesendet wurde
		if($this->Input->post('FORM_SUBMIT') == 'tl_bm_search_form')
		{
			if($this->Input->post('filter_reset') == 1)
			{
				$session = array();

			}
			else
			{
				$geodata = $this->getGeoDataFromCurrentPosition(urlencode(\Input::post('plzcity') ));

				$session = array
				(
					'plzcity' => $geodata['plzcity'],
					'distance' => $this->Input->post('distance'),
					'only_open' => $this->Input->post('only_open'),
					'category' => $this->Input->post('category'),
					'geo_lat' => $geodata['lat'],
					'geo_lon' => $geodata['lon']
				);



			}

			$this->Session->set('bmfilter', $session);
			$this->Input->setPost('FORM_SUBMIT','');

			if($this->jumpTo)
			{
				$objDetailPage = \PageModel::findByPk($this->jumpTo);
				$listUrl = ampersand( $this->generateFrontendUrl($objDetailPage->row()) );
				$this->redirect($listUrl);
			}
			$this->reload();
		}

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
		$intTotal = \BmStoresModel::countStoreEntries($geodata,$session['distance']);

		// Filter anwenden um die Gesamtanzahl zuermitteln
		if($intTotal > 0)
		{
			$filterStoresObj = \BmStoresModel::findStores($intTotal, 0, $geodata, $session['distance']);

			$counter = 0;
			$idArr = array();

			while($filterStoresObj->next())
			{
				
				// aktuell offen
				if($session['only_open'] && $this->getCurrentOpenStatus($filterStoresObj) != 'open') continue;
				
				// gehoert einer bestimmten Kategorie an
				if(strlen($session['category']) > 0 && !$this->hasCategory($filterStoresObj)) continue;

				//wenn alle Filter stimmen -> Werte setzen
				$idArr[] = $filterStoresObj->id;
				$counter++;
			}
			if((int) $intTotal > $counter) $intTotal = $counter;
		}
		$this->Template->total = (int) $intTotal;

		if(count($session) > 0)
		{
			$this->Template->session = true;
			$this->Template->plzcity = $session['plzcity'];
			$this->Template->distance = $session['distance'];
			$this->Template->only_open = $session['only_open'];
			$this->Template->cid = $session['category'];
		}
		else
		{
			//default-Werte setzen
			$this->Template->distance = 15;
		}

		$GLOBALS['TL_CSS'][] = '.'.BM_PATH.'/assets/css/jquery-ui-autocomplete.min.css';
		$GLOBALS['TL_CSS'][] = '.'.BM_PATH.'/assets/css/bm_autocomplete.css';
		$GLOBALS['TL_JAVASCRIPT'][] = '.'.BM_PATH.'/assets/js/jquery-ui-autocomplete.min.js';
		$GLOBALS['TL_BODY'][] = '<script>
		(function($){

			$(document).ready(function(){

			    $("#plzcity").autocomplete({
			        source: document.URL+"?action=ajax&id='.$this->id.'",
			        minLength: 2,
			        response: function( event, ui ) {
			        	console.log(ui.content);
			        }
			    });

			});

		})(jQuery);
		</script>';

		//Kategorien-Optionen
		$categoriesArr = array();
		$cObj = $this->Database->prepare('SELECT `id`,`title` FROM `tl_bm_stores_categories` ORDER BY `sorting` ASC')->execute();
		if($cObj->numRows > 0)
		{
			while($cObj->next())
			{
				$categoriesArr[$cObj->id] = $cObj->title;
			}
		}
		$this->Template->categories = $categoriesArr;

	}

	public function generateAjax()
	{

		$plzort = trim($_GET['term']);

	   if((strlen($plzort) >= 2) && (strlen($plzort) <= 10))
	   {
			//prüfen ob es eine Zahl ist (plz)
			if (is_numeric($plzort))
			{
			    $resObj = $this->Database->prepare("SELECT `zc_zip`, `zc_location_name` FROM `zip_coordinates` WHERE `zc_zip` LIKE ?")
						  ->limit(50)
						  ->execute($plzort.'%');
			}
			else{
			    $resObj = $this->Database->prepare("SELECT `zc_zip`, `zc_location_name` FROM `zip_coordinates` WHERE `zc_location_name` LIKE ?")
						  ->limit(50)
						  ->execute($plzort.'%');

			}

			if ($resObj->numRows > 0)
			{
			    while($resObj->next())
			    {
			        $items[] = $resObj->zc_zip .' '. $resObj->zc_location_name;
			    }
			    $items[] = $_GET['term'];
			    header('Content-type: application/json');
			    echo json_encode($items);
			    exit();
			}
		}
	}

}
