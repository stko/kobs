<?php
/******************************************************************************
 * QRCode Karte selber drucken
 *
 * Copyright    : (c) 2010-2018 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : https://github.com/stko/kobs
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
 * Uebergaben:
 *
 *****************************************************************************/

	require_once("../../adm_program/system/common.php");
	include("./config.php");
	require_once("../../adm_program/system/login_valid.php");
	require_once("qrcards/card.php");

	$a_user_id = $gCurrentUser->getValue("usr_id");

	$isTrainer=$gCurrentUser->editUsers();


	if (isset($_REQUEST["showID"]) ) {
		$showID=$_REQUEST["showID"]; //default
	}
	if (!isset($showID) || !is_numeric($showID) || !$isTrainer) {
		$showID=$a_user_id; //default
	}


	// create dedicated user data
	$showUser=new User($gDb,$gProfileFields,$showID);



	// define some personal output values
	$mitgliedsNr=utf8_encode($showUser->getValue("MITGLIEDSNUMMER"));
	$firstName=$showUser->getValue("FIRST_NAME");
	$lastName=$showUser->getValue("LAST_NAME");
	if (strlen($mitgliedsNr)<1){
		showError( "$mitgliedsNr Sie sind kein eingetragenes Vereinsmitglied und bekommen daher auch keinen Ausweis");
	}
	$thisDate = date('d.m.Y');
	$bDay=explode(".",$showUser->getValue("BIRTHDAY"));
	$birthday=$bDay[0] . "." . $bDay[1] . "." . $bDay[2];

	$printText=array();
	$printText[0]= $firstName . " " . $lastName; // Name
	$printText[1]= $birthday . " " . $mitgliedsNr; // Description Text
	// Construct the QRCode out "normalized" first name, last name and birthday
	$normFirstName = mb_convert_encoding($firstName, "ISO-8859-1", "UTF-8"); //convert name from internal format to UTF8
	$normLastName = mb_convert_encoding($lastName, "ISO-8859-1", "UTF-8"); //convert name from internal format to UTF8
	$normBirthday = $showUser->getValue("BIRTHDAY"); // use international YY-MM-DD format for birthday
	//---------------------------------------------------------
	// The choosen key to generate a (hopefully) unique QRCode for a person is as follows
	//
	//  code1 = first name (in UTF8) + last name (in UTF8) + birthday (in YY-MM-DD format)
	//
	//  code2 = md5sum(code1)
	//
	//  code3 = last 10 characters of code2 (to not make the QRCode too big)
	//
	//  final QRCode = UPPERString ( code3) (for better reability on print)
	//
	//---------------------------------------------------------

	$printText[3]= strtoupper(substr(md5($normFirstName.$normLastName.$normBirthday),-10)); // QRCode Description Text
	$printText[2]= "KLOBS".$printText[3]; // QRCode content
	$printText[4]= $firstName . " " . $lastName . "(".$thisDate.")"; // QRCode add. Text

	// get the picture
	if(strlen($showUser->getValue("usr_photo")) > 0)
	{
		$printText[5]= $showUser->getValue("usr_photo");
	}
	else
	{
		// es wurde kein Bild gefunden, dann ein Dummy-Bild zurueckgeben
		$printText[5]=  imagecreatefrompng(THEME_SERVER_PATH. "/images/no_profile_pic.png");
	}


	// Generate the Card
	

	printCard($printText);


/* just debug code
	for ($i=0;$i<5;$i++){
		print ("$i: ".$printText[$i]."<br>\n");
	}

*/



/*
	header("Content-Type: image/jpeg");


	if(strlen($g_current_user->getValue("usr_photo")) > 0)
	{
		echo $g_current_user->getValue("usr_photo");
		//print ("Bild gefunden...<br>\n");
	}
	else
	{
		// es wurde kein Bild gefunden, dann ein Dummy-Bild zurueckgeben
		$no_profile_pic = imagecreatefrompng(THEME_SERVER_PATH. "/images/no_profile_pic.png");
		echo imagepng($no_profile_pic);

		//print ("KEIN! Bild gefunden...<br>\n");
	}

*/

	exit(0);

	function showError($info){
		print '
		<html>
		<head>
		<title>Karte Drucken</title>

		</head>

		<body>
		'.$info.'<br>


		</body></html>';
		exit;
	}

?>
