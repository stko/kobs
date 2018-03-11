<?php
/******************************************************************************
 * Mitgliederdaten für Klobs als XML- Stream abrufen
 *
 * Copyright    : (c) 2010 Shojikido Brake
 * Homepage     : http://www.shojikido.de
 * projectpage  : kobs.googlecode.com
* Module-Owner : Steffen Köhler
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 *****************************************************************************/

require("../../system/common.php");
include("./config.php");


//-Anmelden per HTTP & gültiger Trainer- Rolle
require("./klobslogin.php");

// OK, gültiger Benutzername & Passwort --------------------------------------------------------------------


//Beginn der Ausgabe

header ("content-type: text/plain; charset=iso-8859-1"); 
header('Content-Disposition: attachment; filename="'.$g_organization.'_DB_Backup_'.date("c").'.sql"');

passthru("/usr/bin/mysqldump -u".$g_adm_usr." -p".$g_adm_pw." -h ".$g_adm_srv." ".$g_adm_db,$fb);


?>