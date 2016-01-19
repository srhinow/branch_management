<?php

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    bn_libraries (www.bibliotheken-niedersachsen.de/)
 * @license    commercial
 * @filesource
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Stores;


/**
 * Class ModuleBnSearchList
 *
 * Front end module "bn search list".
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    bm_libraries
 */
class ModuleBmSearchList extends \ModuleBm
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bm_search_list';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### Filial-SUCH-LISTE ###';
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

		$offset = intval($this->skipFirst);
		$limit = null;
		$this->Template->libraries = array();

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		$session = $this->Session->get('bmfilter')?: array();

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

		if ((int) $intTotal < 1)
		{
			$this->Template = new \FrontendTemplate('mod_bm_entries_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyBmList'];
			return;
		}

		$total = $intTotal - $offset;

		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{


			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = \Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;

				$objPage->noSearch = 1;
				$objPage->cache = 0;

				$objTarget = \PageModel::findByPk($objPage->id);
				if ($objTarget !== null)
				{
					$reloadUrl = ampersand($this->generateFrontendUrl( $objTarget->row() ) );
				}

				$this->redirect($reloadUrl);
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = intval($this->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, $GLOBALS['TL_CONFIG']['maxPaginationLinks'], $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}


		// Get the items
		if (isset($limit))
		{

			$storesObj = \BmStoresModel::findStores($limit, $offset, $geodata, $session['distance'], $idArr);
		}
		else
		{

			$storesObj = \BmStoresModel::findStores(0, $offset, $geodata, $session['distance'], $idArr);
		}

		// No items found
		if ($storesObj === null)
		{
			$this->Template = new \FrontendTemplate('mod_bm_entries_empty');
			$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyBmList'];
		}
		else
		{
			$this->Template->stores = $this->parseStores($storesObj);
		}

		$GLOBALS['TL_JAVASCRIPT'][] = '.'.BM_PATH.'/assets/js/bm_fe.js';
		$this->Template->isDistance = (count($session) > 0 && strlen($session['plzcity']) >0 ) ? true : false;
		$this->Template->filterActive = \Input::get('s') ? true : false;
		$this->Template->totalItems = $intTotal;


	}
}
