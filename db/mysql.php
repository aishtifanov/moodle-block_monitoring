<?PHP  
//$Id: mysql.php,v 1.1.1.1 2007/12/12 07:26:01 Shtifanov Exp $
//
// This file keeps track of upgrades to Moodle's
// blocks system.
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// Versions are defined by backup_version.php
//
// This file is tailored to MySQL

function monitoring_upgrade($oldversion=0) {

    global $CFG;
    
    $result = true;

    if ($oldversion < 2006061500 and $result) {

	}
    //Finally, return result
    return $result;
}
