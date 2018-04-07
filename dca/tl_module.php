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
 * Palettes
 */
use Contao\Backend;

$GLOBALS['TL_DCA']['tl_module']['palettes']['bm_search_form'] 		= '{title_legend},name,headline,type;{config_legend},jumpTo;{template_legend:hide},mod_bm_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['bm_search_list'] 		= '{title_legend},name,headline,type;{config_legend},jumpTo,numberOfItems,perPage;{template_legend:hide},item_template,imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['bm_search_map'] 		= '{title_legend},name,headline,type;{config_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['bm_details'] 		= '{title_legend},name,headline,type;{config_legend},jumpTo;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['item_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['item_template'],
	'default'                 => 'item_list_html',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('BmModule', 'getItemTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['mod_bm_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['mod_bm_template'],
	'default'                 => 'mod_bn_search_form',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('BmModule', 'getModulTemplates'),
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

/**
 * Class BmModule
 */
class BmModule extends Backend
{
	/**
	 * Return all news templates as array
	 * @return array
	 */
	public function getItemTemplates()
	{
		return $this->getTemplateGroup('item_');
	}
	
	/**
	 * Return all news templates as array
	 * @return array
	 */
	public function getModulTemplates()
	{
		return $this->getTemplateGroup('mod_bn_');
	}
}
