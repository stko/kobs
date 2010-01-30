<?php
/******************************************************************************
 * Downloads anhand von Datumsfeldern erlauben
 *
 * Copyright    : (c) 2009 - 2010  Shojikido-Karate Brake
 * Homepage     : http://www.shojikido.de
 * Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

	require("../../system/common.php");
	include("./config.php");
	include("./downloadconfig.php");
	require("../../system/login_valid.php");
	$a_user_id = $g_current_user->getValue("usr_id");


	$filename=$_REQUEST["filename"];

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
		AND last_name.usd_usf_id = ". $g_current_user->getProperty("Nachname", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as first_name
		ON first_name.usd_usr_id = usr_id
		AND first_name.usd_usf_id = ". $g_current_user->getProperty("Vorname", "usf_id"). "
		LEFT JOIN ". TBL_USER_DATA. " as abo_date
		ON abo_date.usd_usr_id = usr_id
		AND abo_date.usd_usf_id = ". $g_current_user->getProperty($klobs_download_date, "usf_id"). "
		WHERE usr_valid = 1
		AND usr_id = ".$a_user_id;


	$result_user = $g_db->query($sql);
	$row = $g_db->fetch_array($result_user);
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
	//Beginn der Ausgabe

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

	/*
	The Content-transfer-encoding header should be binary, since the file will be read
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to
	show a progress meter as a file downloads. The content-lenght can be determines by
	filesize function returns the size of a file.
	*/
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));

	@readfile($filename);
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