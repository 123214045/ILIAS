<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author		Björn Heyser <bheyser@databay.de>
 * @version		$Id$
 *
 * @package		Modules/Test
 */
class ilTestTabsManager
{
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	
	public function __construct()
	{
		$this->tabs = isset($GLOBALS['DIC']) ? $GLOBALS['DIC']['ilTabs'] : $GLOBALS['ilTabs'];
	}
}