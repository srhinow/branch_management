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
 * @package    bn_libraries
 */
class ModuleBmDetails extends \ModuleBm
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bm_details';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### Fialal-DETAILS ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['store']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('store', \Input::get('auto_item'));
		}

		// Do not index or cache the page if no news item has been specified
		if (!\Input::get('store'))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		global $objPage;

		$session = $this->Session->get('bmfilter')?: array();


		// Get the total number of items
		$objStore = \BmStoresModel::findByIdOrAlias(\Input::get('store'));

		if ($objStore === null)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->articles = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}

		$storeData = $objStore->row();

		// Kategorie
		if((int) $storeData['category'] > 0 )
		{
			$catObj = $this->Database->prepare('SELECT * FROM `tl_bm_stores_categories` WHERE `id`=? ')
			->limit(1)
			->execute($storeData['category']);

			if($catObj->numRows > 0) $storeData['category'] = $catObj->name;
		}


		// Website
		 $storeData['website_href'] = (substr($storeData['website'],0,4) != 'http') ? 'http://'.$storeData['website'] : $storeData['website'];

		// facebook
		$storeData['facebook_href'] = (substr($storeData['facebook'],0,4) != 'http') ? 'http://'.$storeData['facebook'] : $storeData['facebook'];

		// twitter
		$storeData['twitter_href'] = (substr($storeData['twitter'],0,4) != 'http') ? 'http://'.$storeData['twitter'] : $storeData['twitter'];

		// gplus
		$storeData['gplus_href'] = (substr($storeData['gplus'],0,4) != 'http') ? 'http://'.$storeData['gplus'] : $storeData['gplus'];

		// blog
		$storeData['blog_href'] = (substr($storeData['blog'],0,4) != 'http') ? 'http://'.$storeData['blog'] : $storeData['blog'];

		// Email
		$this->import('String');
		$storeData['email'] = $this->String->encodeEmail($storeData['email']);

		//for smartphone phonenumber clean for href
		$replaces = array(' ', ',','/','(',')','[',']','{','}','+');
		$storeData['phone_href'] = str_replace($replaces,'',$storeData['telefon']);

		// Open-Status
		$storeData['open_status'] = $this->getCurrentOpenStatus($objLibrary);
		$GLOBALS['TL_JAVASCRIPT'][] = '.'.BN_PATH.'/assets/js/bn_fe.js';

		// Google-Maps url-search-string
		$storeData['gmapsplace'] = ampersand($storeData['strasse'].' '.$storeData['hausnummer'].', '.$storeData['plz'].' '.$storeData['ort']);

		//Bilder vorbereiten
		$scaletype = 'proportional';
		$img_height = 250;
		$img_width = 250;
		$storeData['images'] = array();
		for($ic = 0; $ic <= 5; $ic++)
		{
			if(strlen($storeData['image_'.$ic]) > 0) 
			{
				$storeData['images'][] = array
				(
					'thumb' => \Image::get($storeData['image_'.$ic],$img_width, $img_height,$scaletype),
					'src' => $storeData['image_'.$ic]
				);
			}
		}

		$this->Template->data = $storeData;
		$this->Template->articles = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
	}
}
