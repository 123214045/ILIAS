<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Course booking GUI base class
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @ingroup ServicesCourseBooking
 * @ilCtrl_Calls ilCourseBookingGUI: ilCourseBookingAdminGUI
 */
class ilCourseBookingGUI
{
	/**
	 * Execute request command
	 * 
	 * @throws new ilException
	 * @return boolean
	 */
	public function executeCommand()
	{
		global $ilCtrl, $tpl;

		$next_class = $ilCtrl->getNextClass($this);
		if(!$next_class)
		{
			$next_class = "ilcoursebookingadmingui";
		}
		
		$tpl->getStandardTemplate();
		
		switch($next_class)
		{			
			case 'ilcoursebookingadmingui':								
				$ref_id = $_GET["ref_id"];
				if(!$ref_id)
				{
					throw new ilException("ilCourseBookingGUI - no ref_id");
				}
				$ilCtrl->saveParameterByClass("ilCourseBookingAdminGUI", "ref_id", $ref_id);			
				
				require_once "Modules/Course/classes/class.ilObjCourse.php";
				$course = new ilObjCourse($ref_id);
				
				$this->setCoursePageTitleAndLocator($course);
												
				require_once "Services/CourseBooking/classes/class.ilCourseBookingAdminGUI.php";
				$gui = new ilCourseBookingAdminGUI($course);													
				$ilCtrl->forwardCommand($gui);
				break;

			
			default:				
				throw new ilException("ilCourseBookingGUI - cannot be called directly");
		}
		
		$tpl->show();
	}
	
	/**
	 * Set page title, description and locator
	 * 
	 * @param ilObjCourse $a_course
	 */
	protected function setCoursePageTitleAndLocator(ilObjCourse $a_course)
	{
		global $tpl, $ilLocator, $lng;
		
		// see ilObjectGUI::setTitleAndDescription()
				
		$tpl->setTitle($a_course->getPresentationTitle());
		$tpl->setDescription($a_course->getLongDescription());
		$tpl->setTitleIcon(ilUtil::getImagePath("icon_crs_b.png"),
			$lng->txt("obj_crs"));

		include_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
		$lgui = ilObjectListGUIFactory::_getListGUIByType("crs");
		$lgui->initItem($a_course->getRefId(), $a_course->getId());
		$tpl->setAlertProperties($lgui->getAlertProperties());	

		// see ilObjectGUI::setLocator()

		$ilLocator->addRepositoryItems($a_course->getRefId());
		$tpl->setLocator();
	}
}
