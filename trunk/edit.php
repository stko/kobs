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
	$usrID=$_REQUEST["usrID"];
	if (!isset($usrID) || !is_numeric($usrID)) {
		$usrID=0; //default
	}

	$locID=$_REQUEST["locID"];
	if (!isset($locID) || !is_numeric($locID)) {
		$locID=0; //default
	}

	$date1=$_REQUEST["date1"];
	if (!isset($date1)) {
		$date1="2000-1-1"; //default
	}

	$traTyp=$_REQUEST["traTyp"];
	if (!isset($traTyp) ) {
		$traTyp="1:0"; //default
	}

	$duration=$_REQUEST["duration"];
	if (!isset($duration) || !is_numeric($duration)) {
		$duration=0; //default
	}


	
	
	  $_referer = $_SERVER["HTTP_REFERER"];
	  if (strpos($_referer,$_SERVER['PHP_SELF'])>0){
		$_referer="trmanage.php";
	  }

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
			if($action=="copy" || $action=="edit"){
				if($locID!=0 && $usrID!=0 && $duration>0){

					$locationXML = simplexml_load_file($klobs_location_file);
					$locationHash=array();
					foreach ($locationXML->ort as $ort){
						$locationHash[ (string) $ort->ort_id ] = $ort->name;
					}


					if ($action=="edit"){
						$SQLString =  "UPDATE ".$klobs_training_table." SET ";
					}else{
						$SQLString =  "INSERT IGNORE INTO ".$klobs_training_table." SET ";
					}
					$SQLString.=  "deleted = 0 ,";
					$SQLString.=  "changedate = Now(), ";
					$SQLString.=  "changeby = ".$a_user_id.", ";
					$SQLString.=  "locationId = ".mysql_real_escape_string($locID)." , ";
					$SQLString.=  "location = \"".$locationHash[mysql_real_escape_string($locID)]."\" , ";
					$timestamp=strtotime ($date1);
					$my_t=getdate($timestamp);
					$SQLString.= "timestamp = ".$timestamp." , ";
					$SQLString.=  "year = ".$my_t["year"]." , ";
					$SQLString.=  "mon = ".$my_t["mon"]." , ";
					$SQLString.=  "mday = ".$my_t["mday"]." , ";
					$SQLString.=  "wday = ".$my_t["wday"]." , ";
					$SQLString.=  "date = \"".date("Y-m-d",$timestamp)."\" , ";
					$SQLString.=  "usr_id = \"".mysql_real_escape_string($usrID)."\" , ";
					list($typ,$subTyp)=split(":",$traTyp);
					$SQLString.=  "typ = ".mysql_real_escape_string($typ)." , ";
					$SQLString.=  "subtyp = ".mysql_real_escape_string($subTyp)." , ";
					$SQLString.=  "trainerid = ".$a_user_id." , ";
					$SQLString.=  "starttime = \"00:00\" , ";
					$SQLString.=  "duration = \"".mysql_real_escape_string($duration)."\" , ";
					$SQLString.=  "starttimeint = 0 ";
					if ($action=="edit"){
						$SQLString.=  "WHERE tra_id = ".$tra_id."";
					}
					$db_result = $g_db->query($SQLString);
	//				WHERE tra_id = ".$tra_id."";
				}
			}
		}
	}
//header("Location: ".$_referer); /* Redirect browser */
header("Location: trmanage.php?year=".$_REQUEST["year"]."&month=".$_REQUEST["month"]); /* Redirect browser */


?>


