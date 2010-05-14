<?php
/******************************************************************************
 * trainingsdaten editieren
 *
 * Copyright    : (c) 2010 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : kobs.googlecode.com
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 * Icons taken from http://www.freeiconsweb.com/16x16_Computer_File_icons.htm
 * Uebergaben:
 *
 *****************************************************************************/

	require("../../system/common.php");
	include("./config.php");
	require("../../system/login_valid.php");

	$a_user_id = $g_current_user->getValue("usr_id");
	/*

	//-Anmelden per HTTP & gültiger Trainer- Rolle
	require("./klobslogin.php");
	*/
	// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------

	// Ist der User Mitglied der $klobs_trainer- Rolle?
	$sql    = "SELECT ". TBL_USERS.".usr_login_name
	FROM ". TBL_USERS . " , ". TBL_MEMBERS . ", ". TBL_ROLES . "
	WHERE ". TBL_USERS.".usr_id = ". TBL_MEMBERS . ".mem_usr_id
	AND ". TBL_MEMBERS . ".mem_rol_id = ". TBL_ROLES . ".rol_id
	AND ". TBL_ROLES . ".rol_name = \"".$klobs_trainer."\"
	AND ". TBL_USERS.".usr_id = ".$a_user_id."";



	$dates_result = $g_db->query($sql);
	$row = $g_db->fetch_array($dates_result);

	if (count($row)<2){
		echo "Keine Zugriffsberechtigung auf diesen Bereich...<br>\n";
		exit;
	}
	$action=$_REQUEST["action"];
	$tra_id=$_REQUEST["tra_id"];
	  $_referer = $_SERVER["HTTP_REFERER"];

	if (isset($tra_id) && is_numeric($tra_id)) {
		if (isset($action)){
			if($action=="del"){
				$sql = "UPDATE  " . $klobs_training_table . "
				SET deleted = 1,
				changedate = Now(),
				changeby = ".$a_user_id."
				WHERE tra_id = ".$tra_id."";
				$db_result = $g_db->query($sql);
			}
			if($action=="undel"){
				$sql = "UPDATE  " . $klobs_training_table . "
				SET deleted = 0,
				changedate = Now(),
				changeby = ".$a_user_id."
				WHERE tra_id = ".$tra_id."";
				$db_result = $g_db->query($sql);
			}
		}

	}
header("Location: ".$_referer); /* Redirect browser */


?>


