<?php // $Id: htmlrayonforms.php,v 1.10 2009/02/25 08:23:50 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $nm = required_param('nm', PARAM_INT);       // Month number
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $shortname = required_param('sn');       // Shortname form
	$action   = optional_param('action', '');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $rayon = get_record('monit_rayon', 'id', $rid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
    $strreports = get_string('reportrayon', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid\">$rayon->name</a>";
	$breadcrumbs .= " -> $strformname";
    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	if ($rid == 0) {
	    print_footer();
	 	exit();
	}


	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}

    if ($action == 'copypred')  {
   	    $datefromnew = get_date_from_month_year($nm - 1, $yid);
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
 		   		   WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified=$datefromnew)";
	    if ($listform = get_record_sql($strsql))	{
	    	$rec = get_record('monit_form_'.$shortname, 'listformid', $listform->id);
	       // print_r($rec);
			print_heading($strreports.': '.$rayon->name, "center", 3);
		    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
			print_heading($strformname.': '.$strnamemonth, "center", 4);
		    print_simple_box_start("center");
		    include("$shortname.html");
		    print_simple_box_end();
		    print_footer();
            exit();
   	    } else {
   	    	notice(get_string('preddatanotfound', 'block_monitoring'));
   	    }
	}


	if ($rec = data_submitted())  {

		    if ($fid == 0)  { // insert new records
		       $rkp->rayonid = $rid;
		       $rkp->status = 2;
		       $rkp->shortname = $shortname;
		       $rkp->shortrusname =  $strformname;
		       // $rkp->fullname = ??????????
		       $rkp->datemodified = get_date_from_month_year($nm, $yid);

   			   $strsql = "SELECT id, rayonid, shortname, datemodified FROM {$CFG->prefix}monit_rayon_listforms
	 		   		      WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified={$rkp->datemodified})";

	 		   if ($recsss = get_record_sql($strsql)) 	{
	 		   	  	error(get_string('errorinduplicatedformcreate','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
	 		   }


		       if (!$idnew = insert_record('monit_rayon_listforms', $rkp))	{
					error(get_string('errorincreatinglist','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }

		       $rec->listformid = $idnew;
		       if (!insert_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorincreatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }
		       notice(get_string('succesavedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");

		    } else {  // update records
			   $rec->listformid = $fid;
		       $df = get_record_sql("SELECT id, listformid FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid");
		       $rec->id = $df->id;

		       if (!update_monit_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorinupdatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }
		       notice(get_string('succesupdatedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/listrayonforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
		    }
	}

    if ($fid != 0)  {
    	$rec = get_record('monit_form_'.$shortname, 'listformid', $fid);
    }


	print_heading($strreports.': '.$rayon->name, "center", 3);
    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
	print_heading($strformname.': '.$strnamemonth, "center", 4);


    print_simple_box_start("center");

	?>
		<form name="copypredform" method="post" action="htmlrayonforms.php">
		<input type="hidden" name="rid" value="<?php echo $rid ?>" />
		<input type="hidden" name="fid" value="<?php echo $fid ?>" />
		<input type="hidden" name="nm" value="<?php echo $nm ?>" />
		<input type="hidden" name="yid" value="<?php echo $yid ?>" />
		<input type="hidden" name="sn" value="<?php echo $shortname ?>" />
		<input type="hidden" name="action" value="copypred" />
		<table align="center">
		<tr>
		<td align="center">
		<input type="submit" name=copypred value="<?php print_string('copypred', 'block_monitoring') ?>" />
		</td>
		</tr>
		</table>
		</form>

<?php

    include("$shortname.html");

  	print_simple_box_end();

    print_footer();

?>


