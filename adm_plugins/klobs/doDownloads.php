<?php
/******************************************************************************
 * Downloads anhand von Datumsfeldern erlauben
 *
 * Copyright    : (c) 2010-2018 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : https://github.com/stko/kobs
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

	require("../../adm_program/system/common.php");
	include("./config.php");
	include("./downloadconfig.php");
	require("../../adm_program/system/login_valid.php");

	$a_user_id = $gCurrentUser->getValue("usr_id");

 	$filename=escapeshellcmd($_REQUEST["filename"]);

	if (!isset($filename) || $filename=="") {
		showError( "interner Fehler...<br>Der Dateiname fehlt");
	}

	$filename=$klobs_download_path.$filename;
	if (!file_exists($filename)) {
		showError( "interner Fehler...<br>Datei '$filename' nicht vorhanden???");
	}



	$sql = "SELECT
		". TBL_USERS. ".usr_id as usr_id,
		abo_date.usd_value as abo_date,
		last_name.usd_value as last_name,
		first_name.usd_value as first_name
		FROM ". TBL_USERS . "
		LEFT JOIN ". TBL_USER_DATA. " as last_name
		ON last_name.usd_usr_id = usr_id
		AND last_name.usd_usf_id = ". $gProfileFields->getProperty("LAST_NAME",  "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = usr_id
		AND first_name.usd_usf_id = ". $gProfileFields->getProperty("FIRST_NAME",  "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as abo_date
		ON abo_date.usd_usr_id = usr_id
		AND abo_date.usd_usf_id = ". $gProfileFields->getProperty($klobs_download_date, "usf_id"). "
		WHERE usr_valid = 1
		AND usr_id = ".$a_user_id;


	$result_user = $gDb->query($sql);
	$row = $result_user->fetch();
	if (count($row)<1){
		showError( "interner Fehler<br>Bitte verfluchen Sie den Programmierer..");
	}
	$actDate=$row["abo_date"];
	if (!isset($actDate) || $actDate==""){
		showError("Leider ist f&uuml;r Dich im System kein Abo eingetragen...");
	}
	if (strtotime($actDate)<time()){
		showError("Leider ist dein Abo schon am $actDate abgelaufen...");
	}
	// define some personal output values
	$firstName=escapeshellcmd(utf8_encode($row["first_name"]));
	$lastName=utf8_decode(utf8_encode($row["last_name"]));
	$thisDate = date('d.m.Y');


	//start output
	header("Pragma: public");
	header("Expires: 0"); // set expiration time
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	// browser must download file from server instead of cache

	// force download dialog
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");

	// use the Content-Disposition header to supply a recommended filename and
	// force the browser to display the save dialog.
	header("Content-Disposition: attachment; filename=".basename($filename).";");

	
	// The Content-transfer-encoding header should be binary, since the file will be read
	// directly from the disk and the raw bytes passed to the downloading computer.
	// The Content-length header is useful to set for downloads. The browser will be able to
	// show a progress meter as a file downloads. The content-lenght can be determines by
	// filesize function returns the size of a file.
	
	header("Content-Transfer-Encoding: binary");
	if (0){ // switch between stamped and unsigned PDF- Output
		// The file length can only be used if the file is not generated on the fly
		header("Content-Length: ".filesize($filename));
		@readfile($filename);
	} else {
		$command=	"convert -size 1500x2100 xc:white -stroke black -strokewidth 1  -pointsize 20 -draw \" gravity SouthEast text 50,50 'Persönliche Ausgabe von ".$firstName." ".$lastName." (".$thisDate.") - Weitergabe verboten, sonst 40€ in die Vereinskasse'\" pdf:- | pdftk ".$filename." background - output -";
		passthru($command);
	}
	exit(0);

	function showError($info){
		print '
		<html>
		<head>
		<title>Downloads</title>

		</head>

		<body>
		'.$info.'<br>


		</body></html>';
		exit;
	}

?>
