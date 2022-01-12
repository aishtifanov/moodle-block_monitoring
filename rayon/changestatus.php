<?php // $Id: changestatus.php,v 1.5 2009/02/25 08:23:50 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $nm = required_param('nm', PARAM_INT);        // Month number
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $shortname = required_param('sn');       // Shortname form

	$confirm = optional_param('confirm', 0, PARAM_INT);
    $status = optional_param('status', 0, PARAM_INT);

    if ($confirm == 0 && $status == 0)  {
	    $status3 = optional_param('status3', '');
        if (!empty($status3)) $status = 3;
        else {		    $status5 = optional_param('status5', '');
        	if (!empty($status5)) $status = 5;
	        else {			    $status6 = optional_param('status6', '');
 		       	if (!empty($status6)) $status = 6;
 		    }
        }
	}

    if (!$site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	// $rec = data_submitted();
	// print_r($rec);

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $strformname";
    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	if ($fid == 0) {		notice(get_string('changestatusnew','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");	}

	if ($confirm == 1) {
	    $datefrom = get_date_from_month_year($nm, $yid);	    $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
 		   		   WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified=$datefrom)";
	    if ($rec = get_record_sql($strsql))	{
             // print_r($rec);
           $rec->status = $status;
	       if (!update_record('monit_rayon_listforms', $rec))	{
				error(get_string('errorinupdatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
		   }
		   redirect("$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid", get_string('succesupdatedata','block_monitoring'), 1);
		}
	}


    if ($status == 4)	{		$s1 = get_string('changestatuscoordination', 'block_monitoring', $strformname);    } else {
		print_heading(get_string('changestatus', 'block_monitoring') .' :: ' .$strformname);
		//  $s1 = get_string('changestatuscheckfull', 'block_monitoring', ' школе &laquo;'. $school->name.'&raquo;');
		$s1 = get_string('changestatuscheckfull', 'block_monitoring'). ' ' . $strformname . " на '". get_string('status'.$status, 'block_monitoring') . "'?";
	}

	notice_yesno($s1, "changestatus.php?rid=$rid&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&amp;status=$status&amp;confirm=1", "listrayonforms.php?yid=$yid&amp;rid=$rid");

	print_footer();

?>


