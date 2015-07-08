<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/Explorer2/classes/class.ilExplorerSelectInputGUI.php");
include_once("./Services/TEP/classes/class.ilTEPOrgUnitExplorerGUI.php");

/**
 * Select org unit input GUI
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version $Id$
 * @ilCtrl_IsCalledBy ilTEPOrgUnitSelectionInputGUI: ilFormPropertyDispatchGUI
 *
 * @ingroup ServicesTEP
 */
class ilTEPOrgUnitSelectionInputGUI extends ilExplorerSelectInputGUI
{
	protected $org_unit_map; // [array]
	protected $rcrsv; // [bool]
	protected $show_rcrsv; // [bool]
	
	/**
	 * Constructor
	 *
	 * @param	string	$a_title	Title
	 * @param	string	$a_postvar	Post Variable
	 */
	// gev-patch start
	function __construct(array $a_org_units, $a_postvar, $a_multi = false, $a_show_recursive = true, $a_root_node_ref_id = null)
	// gev-patch end
	{
		global $lng, $ilCtrl, $tree;
				
		$lng->loadLanguageModule("orgu");
		
		$this->multi_nodes = $a_multi;
		$this->org_unit_map = $a_org_units;
		// gev-patch start
		$this->show_rcrsv = (bool)$a_show_recursive; 
		//gev-patch end
		
		$ilCtrl->setParameterByClass("ilformpropertydispatchgui", "postvar", $a_postvar);
		
		$id = "ousel".md5($a_postvar);
		
		$this->explorer_gui = new ilTEPOrgUnitExplorerGUI($id, array("ilformpropertydispatchgui", "ilteporgunitselectioninputgui"), $this->getExplHandleCmd(), $tree, $a_root_node_ref_id);
		$this->explorer_gui->setTypeWhiteList(array( "orgu" ));
		$this->explorer_gui->setSelectMode($a_postvar."_sel", $this->multi_nodes);
		$this->explorer_gui->setSkipRootNode(true);		
		$this->explorer_gui->setSelectableOrgUnitIds(array_keys($a_org_units));

		parent::__construct($lng->txt("objs_orgu"), $a_postvar, $this->explorer_gui, $this->multi_nodes);
		// $this->setType("orgu_select");		
	}
	
	public function setRecursive($a_value)
	{
		$this->rcrsv = (bool)$a_value;
	}
	
	function getTitleForNodeId($a_id)
	{
		return $this->org_unit_map[$a_id];
	}
	
	function render($a_mode = "property_form")
	{
		global $lng;
		
		$res = "";
		
		/*
		if(!is_array($this->getValue()) || !sizeof($this->getValue()))
		{
			$res .= $lng->txt("tep_filter_orgu_all");
		}
		*/ 

		// gev-patch start
		if (!$this->show_rcrsv) {
			return parent::render();
		}
		// gev-patch end
		$res .= parent::render();
		
		$rcrsv = new ilCheckboxInputGUI("", $this->getPostVar()."_rcrsv");
		$rcrsv->setOptionTitle($lng->txt("tep_filter_orgu_rcrsv"));
		$rcrsv->setValue(1);
		$rcrsv->setChecked($this->rcrsv);
				
		return $res.$rcrsv->getTableFilterHTML();
	}
	
	function handleExplorerCommand() {
		
	}
}
