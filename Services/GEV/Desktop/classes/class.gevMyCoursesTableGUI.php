<?php

/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */#

/**
* Table showing courses of a user for Generali.
*
* @author	Richard Klees <richard.klees@concepts-and-training.de>
* @version	$Id$
*/

require_once("Services/CaTUIComponents/classes/class.catAccordionTableGUI.php");
require_once("Services/Utilities/classes/class.ilUtil.php");
require_once("Services/GEV/Utils/classes/class.gevUserUtils.php");
require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");
require_once("Services/Calendar/classes/class.ilDatePresentation.php");

class gevCoursesTableGUI extends catAccordionTableGUI {
	private $title_enabled = false;
	
	public function __construct($a_user_id, $a_parent_obj, $a_parent_cmd="", $a_template_context="") {
		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);
		
		global $ilCtrl, $lng;
		
		$this->lng = &$lng;
		$this->ctrl = &$ilCtrl;
		
		$user_util = gevUserUtils::getInstance($a_user_id);
		$this->user_id = $a_user_id;
		
		$this->setEnableTitle(true);
		$this->setTitle("gev_my_courses");
		$this->setSubtitle("gev_my_courses_desc");
		$this->setTopCommands(false);
		$this->setEnableHeader(true);
		
		$this->setRowTemplate("tpl.gev_my_courses_row.html", "Services/GEV/Desktop");
		
		$this->addColumn("", "expand", "20px");
		$this->addColumn($this->lng->txt("title"), "title");
		$this->addColumn($this->lng->txt("status"), "status");
		$this->addColumn($this->lng->txt("gev_learning_type"), "type");
		$this->addColumn($this->lng->txt("gev_location"), "location");
		$this->addColumn($this->lng->txt("date"), "date");
		$this->addColumn($this->lng->txt("points"), "points");
		$this->addColumn("&euro;", "fee");
		$this->addColumn('<img src="'.ilUtil::getImagePath("gev_action.png").'" />', "actions", "20px");

		$this->cancel_img = '<img src="'.ilUtil::getImagePath("gev_cancel.png").'" />';

		$this->setData($user_util->getBookedAndWaitingCourseInformation());
	}
	
	protected function fillRow($a_set) {
		$this->tpl->setVariable("ACCORDION_BUTTON_CLASS", $this->getAccordionButtonExpanderClass());
		$this->tpl->setVariable("ACCORDION_ROW", $this->getAccordionRowClass());
		$this->tpl->setVariable("COLSPAN", $this->getColspan());
		
		if ($a_set["start_date"] == null ) {
			$date = $this->lng->txt("gev_table_no_entry");
		}
		else {
			$date = ilDatePresentation::formatDate($a_set["start_date"]).
					" - ".
					ilDatePresentation::formatDate($a_set["end_date"]);
		}
		
		if ($a_set["cancel_date"] == null) {
			$cancel_date = $this->lng->txt("gev_unlimited");
		}
		else {
			$cancel_date = ilDatePresentation::formatDate($a_set["cancel_date"]);
		}
		
		$this->ctrl->setParameterByClass("gevMyCoursesGUI", "crs_id", $a_set["obj_id"]);
		$this->ctrl->setParameterByClass("gevMyCoursesGUI", "usr_id", $this->user_id);
		$action = '<a href="'.$this->ctrl->getLinkTargetByClass("gevMyCoursesGUI", "cancelBooking").'">'.
				  $this->cancel_img."</a>";
		$this->ctrl->clearParametersByClass("gevMyCoursesGUI");
		
		$this->tpl->setVariable("TITLE", $a_set["title"]);
		$this->tpl->setVariable("STATUS", "TBD");
		$this->tpl->setVariable("TYPE", $a_set["type"]);
		$this->tpl->setVariable("LOCATION", $a_set["location"]);
		$this->tpl->setVariable("DATE", $date);
		$this->tpl->setVariable("POINTS", $a_set["credit_points"]);
		$this->tpl->setVariable("FEE", $a_set["fee"]);
		$this->tpl->setVariable("ACTIONS", $action);
		$this->tpl->setVariable("TARGET_GROUP", $a_set["target_group"]);
		$this->tpl->setVariable("GOALS", $a_set["goals"]);
		$this->tpl->setVariable("CONTENTS", $a_set["contents"]);
		$this->tpl->setVariable("CRS_LINK", gevCourseUtils::getLinkTo($a_set["obj_id"]));
		$this->tpl->setVariable("CANCEL_DATE", $cancel_date);
	}
	
	/*protected function compileCourseData() {
		// TODO: implement this correctly
		
		return 
		array( array("Gewerbliche Sachversicherung", )
			 , 
			);
	}*/
}

?>