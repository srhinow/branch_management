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
 * Class ModuleBnEditEntry
 *
 * Front end module in memberzone "bn edit entry".
 * @copyright  Sven Rhinow Webentwicklung 2014 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    bn_libraries
 */
class ModuleBmEditEntry extends \ModuleBm
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bm_edit_entry';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### Filial-ANGABEN_BEARBEITEN ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Do not index or cache the page if user not logged in
		$this->import('FrontendUser','User');
		if (!FE_USER_LOGGED_IN)
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

		if(\Input::post('FORM_SUBMIT') == 'tl_bn_edit_entry' && (int) $this->User->library_id > 0)
		{
			$bnImageArr = $this->getBnImages();

			$set = array
			(
				'bibliotheksname' => \Input::post('bibliotheksname'),
				'zweigstellenname' => \Input::post('zweigstellenname'),
				'traeger' => \Input::post('traeger'),
				'medienbestand' => \Input::post('medienbestand'),
				'strasse' => \Input::post('strasse'),
				'hausnummer' => \Input::post('hausnummer'),
				'plz' => \Input::post('plz'),
				'ort' => \Input::post('ort'),
				'gemeinde' => \Input::post('gemeinde'),
				'landkreis' => \Input::post('landkreis'),
				'leiter_name' => \Input::post('leiter_name'),
				'telefon' => \Input::post('telefon'),
				'fax' => \Input::post('fax'),
				'email' => \Input::post('email'),
				'website' => \Input::post('website'),
				'blog' => \Input::post('blog'),
				'facebook' => \Input::post('facebook'),
				'twitter' => \Input::post('twitter'),
				'gplus' => \Input::post('gplus'),
				'onleihe' => \Input::post('onleihe'),
				'webkatalog' => \Input::post('webkatalog'),
				'medien' => \Input::post('medien'),
				'leistungen' => \Input::post('leistungen'),
				'sonst_oeffnungszeiten' => \Input::post('sonst_oeffnungszeiten'),
				'sonstmedien' => \Input::post('sonstmedien'),
				'sonstleistungen' => \Input::post('sonstleistungen'),
				'image_1' => $bnImageArr[1],
				'image_2' => $bnImageArr[2],
				'image_3' => $bnImageArr[3],
				'image_4' => $bnImageArr[4],
				'image_5' => $bnImageArr[5],
			);

			$opendates = $this->getOpenDatesForDB();
			$set = array_merge($set,$opendates);

			$geoData = $this->getcurrentGeoData();
			$set = array_merge($set,$geoData);

			$this->Database->prepare('UPDATE `tl_bn_libraries` %s WHERE `id`=?')->set($set)->execute($this->User->library_id);

			\Input::setPost('FORM_SUBMIT','');
			$this->reload();
		}

		// Get the total number of items
		$objLibrary = \BnLibrariesModel::findLibByIdOrAlias($this->User->library_id);

		if ($objLibrary === null)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->articles = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}

		$libData = $objLibrary->row();
	
		// print_r($libData);

		// Leitungen
		$leitungen = array();
		$leitungenObj = $this->Database->prepare('SELECT * FROM `tl_bn_leitung` ORDER BY `sorting`')
			->execute();

		if($leitungenObj->numRows > 0)
		{
			while($leitungenObj->next())
			{
				$leitungen[$leitungenObj->id] = $leitungenObj->name;
			}
		}
		$libData['leitungenArr'] = $leitungen;

		// Traeger
		$traeger = array();
		$traegerObj = $this->Database->prepare('SELECT * FROM `tl_bn_traeger` ORDER BY `sorting`')
			->execute();

		if($traegerObj->numRows > 0)
		{
			while($traegerObj->next())
			{
				$traeger[$traegerObj->id] = $traegerObj->name;
			}
		}
		$libData['traegerArr'] = $traeger;

		// Medien
		$medien = array();
		$medienObj = $this->Database->prepare('SELECT * FROM `tl_bn_medien` ORDER BY `sorting`')
			->execute();

		if($medienObj->numRows > 0)
		{
			while($medienObj->next())
			{
				$medien[$medienObj->id] = $medienObj->name;
			}
		}
		$libData['medienArr'] = $medien;

		// Leistungen
		$leistungen = array();
		$leistungenObj = $this->Database->prepare('SELECT * FROM `tl_bn_leistungen` ORDER BY `sorting`')
			->execute();

		if($leistungenObj->numRows > 0)
		{
			while($leistungenObj->next())
			{
				$leistungen[$leistungenObj->id] = $leistungenObj->name;
			}
		}
		$libData['leistungenArr'] = $leistungen;


		// Email
		$this->import('String');
		$libData['email'] = $this->String->encodeEmail($libData['email']);

		// Open-Status
		$libData['open_status'] = $this->getCurrentOpenStatus($objLibrary);
		$GLOBALS['TL_JAVASCRIPT'][] = '.'.BN_PATH.'/assets/js/bn_fe.js';

		// Google-Maps url-search-string
		$libData['gmapsplace'] = ampersand($libData['strasse'].' '.$libData['hausnummer'].', '.$libData['plz'].' '.$libData['ort'].', Niedersachsen');

		// media
		$medienIds = unserialize($libData['medien']);
		if(is_array($medienIds) && count($medienIds)>0)
		{
			$medienArr = array();

			$medienObj = $this->Database->prepare("SELECT * FROM `tl_bn_medien` WHERE id IN(".implode(',', array_map('intval', $medienIds)).")")->execute();

			if($medienObj->numRows > 0)
			{
				while($medienObj->next()) $medienArr[] = $medienObj->name;
				$libData['medien'] = $medienArr;
			}
		}

		// leistungen
		$leistungenIds = unserialize($libData['leistungen']);
		if(is_array($leistungenIds) && count($leistungenIds)>0)
		{
			$leistungenArr = array();

			$leistungenObj = $this->Database->prepare("SELECT * FROM `tl_bn_leistungen` WHERE id IN(".implode(',', array_map('intval', $leistungenIds)).")")->execute();

			if($leistungenObj->numRows > 0)
			{
				while($leistungenObj->next()) $leistungenArr[] = $leistungenObj->name;
				$libData['leistungen'] = $leistungenArr;
			}
		}
		$this->Template->data = $libData;
		$this->Template->articles = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
	}

	public function getOpenDatesForDB()
	{
		$returnDates = array();
		$dayrows = array
		(
			'mo_1_von','mo_1_bis','mo_2_von','mo_2_bis',
			'di_1_von','di_1_bis','di_2_von','di_2_bis',
			'mi_1_von','mi_1_bis','mi_2_von','mi_2_bis',
			'do_1_von','do_1_bis','do_2_von','do_2_bis',
			'fr_1_von','fr_1_bis','fr_2_von','fr_2_bis',
			'sa_1_von','sa_1_bis','sa_2_von','sa_2_bis',
			'so_1_von','so_1_bis','so_2_von','so_2_bis',
		);

		foreach($dayrows as $day)
		{
			$field = \Input::post($day);
			$returnDates[$day] = ($field == '')? 0 : $this->mkTimestampFromTime($field);
		}

		return $returnDates;
	}
	public function getcurrentGeoData()
	{
		$geodata = array();
		$address = '';
		
		$oldAddressDataObj = \BnLibrariesModel::findLibByIdOrAlias($this->User->library_id);
		$oldAddressData = $oldAddressDataObj->row();
		$geodata = array('lat' => $oldAddressData['lat'], 'lon'=>$oldAddressData['lon']);

		if(
			$oldAddressData['strasse'] != \Input::post('strasse') ||
			$oldAddressData['hausnummer'] != \Input::post('hausnummer') ||
			$oldAddressData['plz'] != \Input::post('plz') ||
			$oldAddressData['ort'] != \Input::post('ort') ||
			$oldAddressData['gemeinde'] != \Input::post('gemeinde') ||
			$oldAddressData['landkreis'] != \Input::post('landkreis') 
		)
		{
			$adddress = \Input::post('strasse').' '.\Input::post('hausnummer').', '.\Input::post('plz').' '.\Input::post('ort').', '.\Input::post('landkreis').', Niedersachsen';
			$newGeoData = $this->getGeoDataFromAddress($adddress);
			if(is_array($newGeoData) || count($newGeoData) > 0 ) $geodata = $newGeoData;
		}
		//___________ HIER GEHTS WEITER ________________________
		// print_r($geodata);
		// exit();
		//______________________________________________________
		return $geodata;
	}

	public function mkTimestampFromTime($timestr)
	{
		$timestr = str_replace('.',':',$timestr);

		$objDate = new \Date($timestr, $GLOBALS['TL_CONFIG']['timeFormat']);

		return $objDate->tstamp;
	}

	public function getBnImages()
	{
		$libObj = $this->Database->prepare('SELECT * FROM `tl_bn_libraries` WHERE id=?')->limit(1)->execute($this->User->library_id);

		//defaults setzen
		$bnImages = array
		(
			1 => $libObj->image_1,
			2 => $libObj->image_2,
			3 => $libObj->image_3,
			4 => $libObj->image_4,
			5 => $libObj->image_5
		);
		//Bilder loeschen;
		$bnImages = $this->removeImages($bnImages);

		//Bilder hochladen
		for($c=1; $c <= 5; $c++)
		{
			$bnImages[$c] = $this->uploadImage($_FILES['image_'.$c], $c, $bnImages);
		}

		//Luecken schliessen und bei umsortierung auch umbennen
		$bnImages = $this->sortAndRename($bnImages);

		return $bnImages;
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
		$this->import('FrontendUser','User');
		$this->import('Files');


		// hochgeladenes Bild verarbeiten
		if(is_uploaded_file($file['tmp_name']) && $file['error'] == 0)
		{


			$file['name'] = utf8_romanize($file['name']);
			$fileInfos = pathinfo($file['name']);
			$fileInfos['extension'] = strtolower($fileInfos['extension']);
			$destFilePath = TL_ROOT.$GLOBALS['BN']['BN_IMAGE_PATH'].'/user_'.$this->User->library_id;

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
					$destFilePath = $GLOBALS['BN']['BN_IMAGE_PATH'].'/user_'.$this->User->library_id;
					
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
}
