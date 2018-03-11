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

	require_once("../../system/common.php");
	include("./config.php");
	require_once("../../system/login_valid.php");

	$a_user_id = $gCurrentUser->getValue("usr_id");


	// Darf der angemeldete User Mitglieder editieren?
	if(!$gCurrentUser->editUsers()){
	    $gMessage->show("Du hast leider nicht die notwendigen Rechte, um Trainingsdaten editieren zu d&uuml;rfen..");
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
				$db_result = $gDb->query($sql);
			}
			if($action=="undel"){
				$sql = "UPDATE  " . $klobs_training_table . "
				SET deleted = 0,
				changedate = Now(),
				changeby = ".$a_user_id."
				WHERE tra_id = ".$tra_id."";
				$db_result = $gDb->query($sql);
			}
			if($action=="copy" || $action=="edit"){
				if($locID!=0 && $usrID!=0 && $duration>0){

					$locationXML = simplexml_load_file($klobs_location_file);
					$locationHash=array();
					foreach ($locationXML->ort as $ort){
						$locationHash[  '\''.(string) $ort->ort_id .'\''] = $ort->name;
					}


					if ($action=="edit"){
						$SQLString =  "UPDATE ".$klobs_training_table." SET ";
					}else{
						$SQLString =  "INSERT IGNORE INTO ".$klobs_training_table." SET ";
					}
					$SQLString.=  "deleted = 0 ,";
					$SQLString.=  "changedate = Now(), ";
					$SQLString.=  "changeby = ".$a_user_id.", ";
					$SQLString.=  "locationId = ".$gDb->escapeString($locID)." , ";
					$SQLString.=  "location = '".$locationHash[(string) $gDb->escapeString($locID)]."' , ";
					$timestamp=strtotime ($date1);
					$my_t=getdate($timestamp);
					$SQLString.= "timestamp = ".$timestamp." , ";
					$SQLString.=  "year = ".$my_t["year"]." , ";
					$SQLString.=  "mon = ".$my_t["mon"]." , ";
					$SQLString.=  "mday = ".$my_t["mday"]." , ";
					$SQLString.=  "wday = ".$my_t["wday"]." , ";
					$SQLString.=  "date = '".date("Y-m-d",$timestamp)."' , ";
					$SQLString.=  "usr_id = ".$gDb->escapeString($usrID)." , ";
					list($typ,$subTyp)=explode(":",$traTyp);
					$SQLString.=  "typ = ".$gDb->escapeString($typ)." , ";
					$SQLString.=  "subtyp = ".$gDb->escapeString($subTyp)." , ";
					$SQLString.=  "trainerid = ".$a_user_id." , ";
					$SQLString.=  "starttime = '00:00' , ";
					$SQLString.=  "duration = ".$gDb->escapeString($duration)." , ";
					$SQLString.=  "starttimeint = 0 ";
					if ($action=="edit"){
						$SQLString.=  "WHERE tra_id = ".$tra_id."";
					}
					$db_result = $gDb->query($SQLString);
	//				WHERE tra_id = ".$tra_id."";
				}
			}
		}
	}
//header("Location: ".$_referer); /* Redirect browser */
header("Location: trmanage.php?year=".$_REQUEST["year"]."&month=".$_REQUEST["month"]); /* Redirect browser */
//echo $SQLString;
//echo "<br>".$gDb->showError();
?>


