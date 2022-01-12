<?php // $Id: changrazd.php,v 1.9 2008/09/12 08:44:27 Shtifanov Exp $
    require_once("../../../config.php");
    require_once('../lib.php');

    $id = required_param('id', PARAM_INT);       // id
    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
	$nm = required_param('nm', PARAM_INT);         // Month
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $rzd = required_param('rzd', PARAM_INT);       // Razd id
  	$levelmonit = optional_param('level', 'region');       // Level


	$frm = data_submitted(); /// load up any submitted data

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	if (!$admin_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$title = get_string('editfield', 'block_monitoring');
    $school = get_record('monit_school', 'id', $sid);
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strrazdel = get_string('namerazdel','block_monitoring');
	$nameofpokazatel = get_string('nameofpokazatel','block_monitoring');
	$valueofpokazatel = get_string('valueofpokazatel','block_monitoring');
	$save = get_string('savechanges');
	$cancel = get_string('cancel');

	$strformname = get_record_sql("select name from {$CFG->prefix}monit_form where id=$fid");
	$strformname= $strformname->name;

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	if($sid != 0)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid\">$strschools</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?rid=$rid&amp;sid=$sid&amp;fid=$fid&amp;yid=$yid\">$school->name</a>";
	}  else  {
	    $yeareport = get_string('yeareport', 'block_monitoring');
		$rayon = get_record('monit_rayon', 'id', $rid);
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=rayon&rid=1&sid=0&nm=1&id=1&yid=$yid\">$rayon->name</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=rayon&rid=1&sid=0&nm=1&id=1&yid=$yid\">$yeareport</a>";
	}

	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpforms.php?sid=$sid&amp;rzd=$rzd&amp;fid=$fid&amp;nm=1&amp;yid=$yid&amp;rid=$rid&amp;level=$levelmonit\">$strformname</a>";
	$breadcrumbs .= " -> $title";

    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);
    if(!$frm) {
		$text = get_record_sql("select name from {$CFG->prefix}monit_razdel_field where id=$id");

		echo"<form name='changfield' method='post' action='changrazd.php?id=$id&amp;sid=$sid&amp;rzd=$rzd&amp;fid=$fid&amp;nm=1&amp;yid=$yid&amp;rid=$rid&amp;level=$levelmonit'>".
			"<div align='left'>".
			"<input type='text' name='editfield' size=150 value='".$text->name."'><br><br>".
			"<input type='submit' name='save' value='".$save."'>".
			"<input type='submit' name='cancel' value='".$cancel."'>".
			"</div></form>";
	} else {//		print_r($frm);
//print $frm->editfield;
		if($frm->save) {			get_record_sql("update {$CFG->prefix}monit_razdel_field set name = '$frm->editfield' where id=$id");
		}
		redirect("$CFG->wwwroot/blocks/monitoring/bkp/bkpforms.php?rid=$rid&amp;sid=$sid&amp;nm=1&amp;yid=$yid&amp;level=$levelmonit&amp;fid=$fid&amp;rzd=$rzd", get_string('succesavedata', 'block_monitoring'));	}

	print_simple_box_start("center", "%100");
	print_simple_box_end();
?>