<?php
/**
* change user profile
*
* @author Peter Gabriel <pgabriel@databay.de>
* @version $Id$
*
* @package ilias
*/
require_once "./include/inc.header.php";

$tpl->addBlockFile("CONTENT", "content", "tpl.usr_profile.html");
$tpl->addBlockFile("BUTTONS", "buttons", "tpl.buttons.html");

//display buttons
$tpl->setCurrentBlock("btn_cell");
$tpl->setVariable("BTN_LINK","usr_profile.php");
$tpl->setVariable("BTN_TXT",$lng->txt("personal_profile"));
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock("btn_cell");
$tpl->setVariable("BTN_LINK","usr_password.php");
$tpl->setVariable("BTN_TXT",$lng->txt("chg_password"));
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock("btn_cell");
$tpl->setVariable("BTN_LINK","usr_agreement.php");
$tpl->setVariable("BTN_TXT",$lng->txt("usr_agreement"));
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock("btn_row");
$tpl->parseCurrentBlock();

//if data are posted
if ($_GET["cmd"] == "save")
{
	$ilias->account->setFirstName($_POST["usr_fname"]);
	$ilias->account->setLastName($_POST["usr_lname"]);
	$ilias->account->setGender($_POST["usr_gender"]);
	$ilias->account->setTitle($_POST["usr_title"]);
	$ilias->account->setEmail($_POST["usr_email"]);
	$ilias->account->setLanguage($_POST["usr_language"]);

	//set user skin
	if ($_POST["usr_skin"] != "")
	{
		$ilias->account->setPref("skin", $_POST["usr_skin"]);

		//set user style
		if ($_POST["usr_style"] != "")
		{
			$ilias->account->setPref("style", $_POST["usr_style"]);
		}
	}

	//update userdata
	if ($ilias->account->update() == false)
	{
		$tpl->setCurrentBlock("message");
		$tpl->setVariable("MSG", $lng->txt($ilias->account->getErrorMsg()));
		$tpl->parseCurrentBlock();
	}
	else
	{
		$tpl->setVariable("RELOAD","<script language=\"Javascript\">\ntop.location.href = \"./start.php\";\n</script>\n");
	}
}

//get all languages
$languages = $lng->getInstalledLanguages();

//go through languages
foreach ($languages as $lang_key)
{
	$tpl->setCurrentBlock("sel_lang");
	$tpl->setVariable("LANG", $lng->txt("lang_".$lang_key));
	$tpl->setVariable("LANGSHORT", $lang_key);

	if ($ilias->account->prefs["language"] == $lang_key)
	{
		$tpl->setVariable("SELECTED_LANG", "selected");
	}

	$tpl->parseCurrentBlock();
}

//what gui's are available for ilias?
$ilias->getSkins();

foreach ($ilias->skins as $row)
{
	$tpl->setCurrentBlock("selectskin");

	if ($ilias->account->skin == $row["name"])
	{
		$tpl->setVariable("SKINSELECTED", "selected");
	}

	$tpl->setVariable("SKINVALUE", $row["name"]);
	$tpl->setVariable("SKINOPTION", $row["name"]);
	$tpl->parseCurrentBlock();
}

//what styles are available for current skin
$ilias->getStyles($ilias->account->skin);

foreach ($ilias->styles as $row)
{
	$tpl->setCurrentBlock("selectstyle");

	if ($ilias->account->prefs["style"] == $row["name"])
	{
		$tpl->setVariable("STYLESELECTED", "selected");
	}

	$tpl->setVariable("STYLEVALUE", $row["name"]);
	$tpl->setVariable("STYLEOPTION", $row["name"]);
	$tpl->parseCurrentBlock();
}

$tpl->setCurrentBlock("content");
$tpl->setVariable("FORMACTION", "usr_profile.php?cmd=save");

$tpl->setVariable("TXT_PAGEHEADLINE",$lng->txt("profile"));

$tpl->setVariable("TXT_USR_DATA", $lng->txt("userdata"));
$tpl->setVariable("TXT_NICKNAME", $lng->txt("username"));
$tpl->setVariable("TXT_SALUTATION", $lng->txt("salutation"));
$tpl->setVariable("TXT_SALUTATION_M", $lng->txt("salutation_m"));
$tpl->setVariable("TXT_SALUTATION_F",$lng->txt("salutation_f"));
$tpl->setVariable("TXT_FIRSTNAME",$lng->txt("firstname"));
$tpl->setVariable("TXT_LASTNAME",$lng->txt("lastname"));
$tpl->setVariable("TXT_TITLE",$lng->txt("title"));
$tpl->setVariable("TXT_INSTITUTION",$lng->txt("institution"));
$tpl->setVariable("TXT_STREET",$lng->txt("street"));
$tpl->setVariable("TXT_ZIP",$lng->txt("zipcode"));
$tpl->setVariable("TXT_CITY",$lng->txt("city"));
$tpl->setVariable("TXT_COUNTRY",$lng->txt("country"));
$tpl->setVariable("TXT_PHONE",$lng->txt("phone"));
$tpl->setVariable("TXT_EMAIL",$lng->txt("email"));
$tpl->setVariable("TXT_STATUS",$lng->txt("status"));
$tpl->setVariable("TXT_GUEST",$lng->txt("guest"));
$tpl->setVariable("TXT_STUDENT",$lng->txt("student"));
$tpl->setVariable("TXT_EMPLOYEE",$lng->txt("employee"));
$tpl->setVariable("TXT_SYS_GRP",$lng->txt("system_grp"));
$tpl->setVariable("TXT_LANGUAGE",$lng->txt("language"));
$tpl->setVariable("TXT_USR_SKIN",$lng->txt("usr_skin"));
$tpl->setVariable("TXT_USR_STYLE",$lng->txt("usr_style"));

//values
$tpl->setVariable("NICKNAME", $ilias->account->getLogin());
$tpl->setVariable("SELECTED_".strtoupper($ilias->account->getGender()), "selected");
$tpl->setVariable("FIRSTNAME", $ilias->account->getFirstname());
$tpl->setVariable("LASTNAME", $ilias->account->getLastname());
$tpl->setVariable("TITLE", $ilias->account->getTitle());
$tpl->setVariable("INSTITUTION", $ilias->account->getInstitution());
$tpl->setVariable("STREET", $ilias->account->getStreet());
$tpl->setVariable("ZIPCODE", $ilias->account->getZipCode());
$tpl->setVariable("CITY", $ilias->account->getCity());
$tpl->setVariable("COUNTRY", $ilias->account->getCountry());
$tpl->setVariable("PHONE", $ilias->account->getPhone());
$tpl->setVariable("EMAIL", $ilias->account->getEmail());


$obj = getObject($rbacadmin->getDefaultRole($_SESSION["AccountId"]));
$tpl->setVariable("SYS_GRP",$obj["title"]);

//button
$tpl->setVariable("TXT_SAVE",$lng->txt("save"));

$tpl->parseCurrentBlock();

$tpl->show();
?>