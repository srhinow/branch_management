<?php

/**
 * PHP version 5
 * @copyright  Sven Rhinow Webentwicklung 2015 <http://www.sr-tag.de>
 * @author     Sven Rhinow
 * @package    branch_management
 * @license    LGPL
 * @filesource
 */

/**
 * Class beCSVExport
 */
class beCSVExport extends Backend
{
	/**
	 * Export Members as CSV-File
	 */
	public function csvStoresExport()
	{
		$this->import('Session');

		$sessionFilter = $this->Session->get('filter');

		if ($this->Input->post('FORM_SUBMIT') == 'export_stores')
		{
		    $this->import('Database');

		    //set handle from file
		    $seperators = array('comma'=>',','semicolon'=>';','tabulator'=>"\t",'linebreak'=>"\n");
		    $listFields = $this->Database->listFields('tl_bm_stores');
		    $fieldnames = array();
		    $ignorefields = array('id','tstamp','PRIMARY');

		    foreach($listFields as $k => $field)
		    {
		    	if(in_array($field['name'],$ignorefields) || $field['type'] == 'index') continue;
		    	$fieldnames[] = $field['name'];	
		    }
	    

		    $headnames = $fieldnames;

		    // get records
		    $arrExport = array();
		    
		    $selectfields = implode(', ',$fieldnames);
		    
		    $memObj = $this->Database->prepare('SELECT '.$selectfields.' FROM `tl_bm_stores` ORDER BY `filialname` ASC')->execute();

		    if($memObj->numRows < 1)
		    {
				$_SESSION['TL_ERROR'][] = 'keine Daten zum exportieren vorhanden';
				$this->reload();
		    }

		    while($memObj->next())
		    {

		    	$arrExport[] = $memObj->row();

		    }

		    // start output
		    $exportFile =  'contao_stores_' . date("Ymd-Hi");
		    
		    $output = '"' . implode('"'.$seperators[$this->Input->post('separator')].'"', array_values($headnames)).'"' . "\n";

		    foreach ($arrExport as $export)
		    {
			    $output .= '"' . implode('"'.$seperators[$this->Input->post('separator')].'"', str_replace("\"", "\"\"", $export)).'"' . "\n";
		    }
		    // print_r($output);
		    // exit();
		    ob_end_clean();
		    header('Content-Type: application/csv');
		    header('Content-Transfer-Encoding: binary');
		    header('Content-Disposition: attachment; filename="' . $exportFile .'.csv"');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Expires: 0');
		    echo $output;
		    exit();

		}

    	// Return the form
		return '
		    <div id="tl_buttons">
		    <a href="'.ampersand(str_replace('&key=csvStoresExport', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
		    </div>

		    <h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_bm_stores']['h2_exportcsv'].'</h2>'.$this->getMessages().'

		    <form action="'.ampersand($this->Environment->request, true).'" id="tl_bbk_csvexport" class="tl_form" method="post">
		    <div class="tl_formbody_edit">
			<input type="hidden" name="FORM_SUBMIT" value="export_stores" />
			<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'" />
			<input type="hidden" name="pid" value="'.$this->Input->get('id').'" />
			<fieldset class="tl_tbox block">
			    <div class="w50">
			    <h3><label for="ctrl_bbk">CSV-Trenner</label></h3>
			    <select name="separator" id="separator" class="tl_select" onfocus="Backend.getScrollOffset();">
				<option value="semicolon">'.$GLOBALS['TL_LANG']['MSC']['semicolon'].' (;)</option>
				<option value="comma">'.$GLOBALS['TL_LANG']['MSC']['comma'].' (,)</option>
				<option value="tabulator">'.$GLOBALS['TL_LANG']['MSC']['tabulator'].'</option>
				<option value="linebreak">'.$GLOBALS['TL_LANG']['MSC']['linebreak'].'</option>
			    </select>'.(($GLOBALS['TL_LANG']['MSC']['separator'][1] != '') ? '<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MSC']['separator'][1].'</p>' : '').'
			    </div>
			</fieldset>
		    </div>

		    <div class="tl_formbody_submit">

		    <div class="tl_submit_container">
		      <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_bm_stores']['button_exportcsv']).'" />
		    </div>

		    </div>
		    </form>';
	}

	/**
	 * Export Libraries as CSV-File
	 */
	public function csvLibraryExport()
	{
		$this->import('Session');
		
		$sessionFilter = $this->Session->get('filter');


		if ($this->Input->post('FORM_SUBMIT') == 'export_libraries')
		{
		    $this->import('Database');

		    //set handle from file
		    $seperators = array('comma'=>',','semicolon'=>';','tabulator'=>"\t",'linebreak'=>"\n");
		    $listFields = $this->Database->listFields('tl_bn_libraries');
		    $fieldnames = array();
		    $ignorefields = array('id','tstamp','PRIMARY','libImages','import_from','oldpwd');

		    // ignore-fields filtern
		    foreach($listFields as $k => $field)
		    {
		    	if(in_array($field['name'],$ignorefields) || $field['type'] == 'index') continue;
		    	$fieldnames[] = $field['name'];	
		    }

		    $headnames = $fieldnames;
		    
		    // get records
		    $arrExport = array();
		    
		    $selectfields = implode(', ',$fieldnames);
		    
		    $libObj = $this->Database->prepare('SELECT '.$selectfields.' FROM `tl_bn_libraries` ')->execute();

		    if($libObj->numRows < 1)
		    {
				$_SESSION['TL_ERROR'][] = 'keine Daten zum exportieren vorhanden';
				$this->reload();
		    }

		    // leistungen als Array holen
		    $leistungenObj = $this->Database->prepare('SELECT * FROM `tl_bn_leistungen`')->execute();
		    while($leistungenObj->next()) $leistungenArr[$leistungenObj->id] = $leistungenObj->name;

		    // medien als Array holen
		    $medienObj = $this->Database->prepare('SELECT * FROM `tl_bn_medien`')->execute();
		    while($medienObj->next()) $medienArr[$medienObj->id] = html_entity_decode($medienObj->name);

		    while($libObj->next())
		    {
		    	$rowArr = $libObj->row();
		    	
		    	// medien
		    	$mArray = array();		    	
		    	$medien = unserialize($libObj->medien);
		    	if(is_array($medien) && count($medien)>0)
		    	{
		    		foreach($medien as $mname) $mArray[] = $medienArr[$mname];
					
					$rowArr['medien'] = implode(', ', $mArray);		    		

		    	}else $rowArr['medien'] = '';

		    	// leistungen
		    	$lArray = array();		    	
		    	$leistungen = unserialize($libObj->leistungen);
		    	if(is_array($leistungen) && count($leistungen)>0)
		    	{
		    		foreach($leistungen as $lname) $lArray[] = $leistungenArr[$lname];
					
					$rowArr['leistungen'] = implode(', ', $lArray);		    		

		    	}else $rowArr['leistungen'] = '';

		    	//oeffnungszeiten
		    	$datesArr = array
		    	(
		    		'mo_1_von','mo_1_bis','mo_2_von','mo_2_bis',
		    		'di_1_von','di_1_bis','di_2_von','di_2_bis',
		    		'mi_1_von','mi_1_bis','mi_2_von','mi_2_bis',
		    		'do_1_von','do_1_bis','do_2_von','do_2_bis',
		    		'fr_1_von','fr_1_bis','fr_2_von','fr_2_bis',
		    		'sa_1_von','sa_1_bis','sa_2_von','sa_2_bis',
		    		'so_1_von','so_1_bis','so_2_von','so_2_bis',
		    	);
		    	foreach($datesArr as $date) $rowArr[$date] = ($rowArr[$date] > 0)? date('H:i',$rowArr[$date]): '';


		    	$arrExport[] = $rowArr;
		    }
		    


		    // $arrExport = $libObj->fetchAllAssoc();

		    // start output
		    $exportFile =  'bn_libraries' . date("Ymd-Hi");
		    
		    $output = '"' . implode('"'.$seperators[$this->Input->post('separator')].'"', array_values($headnames)).'"' . "\n";

		    foreach ($arrExport as $export)
		    {
			    $output .= '"' . implode('"'.$seperators[$this->Input->post('separator')].'"', str_replace("\"", "\"\"", $export)).'"' . "\n";
		    }
		    ob_end_clean();
		    header('Content-Type: application/csv');
		    header('Content-Transfer-Encoding: binary');
		    header('Content-Disposition: attachment; filename="' . $exportFile .'.csv"');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Expires: 0');
		    echo $output;
		    exit();

		}

    	// Return the form
		return '
		    <div id="tl_buttons">
		    <a href="'.ampersand(str_replace('&key=csvLibraryExport', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
		    </div>

		    <h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_bn_libraries']['h2_exportcsv'].'</h2>'.$this->getMessages().'

		    <form action="'.ampersand($this->Environment->request, true).'" id="tl_bbk_csvexport" class="tl_form" method="post">
		    <div class="tl_formbody_edit">
			<input type="hidden" name="FORM_SUBMIT" value="export_libraries" />
			<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'" />
			<input type="hidden" name="pid" value="'.$this->Input->get('id').'" />
			<fieldset class="tl_tbox block">
			    <div class="w50">
			    <h3><label for="ctrl_bbk">'.$GLOBALS['TL_LANG']['MSC']['separator'][0].'</label></h3>
			    <select name="separator" id="separator" class="tl_select" onfocus="Backend.getScrollOffset();">
				<option value="semicolon">'.$GLOBALS['TL_LANG']['MSC']['semicolon'].' (;)</option>
				<option value="comma">'.$GLOBALS['TL_LANG']['MSC']['comma'].' (,)</option>
				<option value="tabulator">'.$GLOBALS['TL_LANG']['MSC']['tabulator'].'</option>
				<option value="linebreak">'.$GLOBALS['TL_LANG']['MSC']['linebreak'].'</option>
			    </select>'.(($GLOBALS['TL_LANG']['MSC']['separator'][1] != '') ? '<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MSC']['separator'][1].'</p>' : '').'
			    </div>
			</fieldset>
		    </div>

		    <div class="tl_formbody_submit">

		    <div class="tl_submit_container">
		      <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_bn_libraries']['button_exportcsv']).'" />
		    </div>

		    </div>
		    </form>';
	}

}
