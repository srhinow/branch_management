<?php
namespace Srhinow\BranchManagement\Hooks;

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2018 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */
use Contao\Frontend;
use Srhinow\BranchManagement\Models\BmStoresModel;

class feBmHooks extends Frontend
{
    public function __construct()
    {
		// Set the item from the auto_item parameter
		if (!isset($_GET['store']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('store', \Input::get('auto_item'));
		}

		parent::__construct();
    }


    /**
    * replace bn-specific inserttag if get-paramter isset
    * bn::colname::alternative from objPage
    * @param string
    * @return string
    */
    public function replaceBmInsertTags($strTag)
	{
	    if (substr($strTag,0,4) == 'bn::')
	    {
	        global $objPage;
	        $storeIdAlias = \Input::get('store');
	        $split = explode('::',$strTag);


	        switch($split[1]){
	        	case 'fullname':
	        		if(!$storeIdAlias) return $objPage->$split[2];

	   				$objStore = BmStoresModel::findByIdOrAlias($storeIdAlias);

	        		$fullname = $objStore->filialname;
	        		return $fullname;
	        	break;
	        	case 'printbutton':

	        		return (!$storeIdAlias) ? '' : '<a href="javascript:window.print()" class="printbutton"><i class="fa fa-print"></i></a>';

	        	break;
	        	default:
                    $objStore = BmStoresModel::findByIdOrAlias($storeIdAlias);
	        		return $objStore->{$split[1]};
	        }

	    }

	    return false;
	}
}
