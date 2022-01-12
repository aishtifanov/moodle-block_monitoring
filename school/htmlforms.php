<?php // $Id: htmlforms.php,v 1.26 2010/01/27 13:47:31 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm = required_param('nm', PARAM_INT);         // Month
    $shortname = required_param('sn');       // Shortname form
	$action   = optional_param('action',   '-');
	$copynext = optional_param('copynext', '-');
	$copyprev = optional_param('copyprev', '-');


    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    // check security
    $datefrom = get_date_from_month_year($nm, $yid);
    $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	   		   WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified=$datefrom)";
   	// print $strsql; echo '<hr>';
    if ($rec = get_record_sql($strsql))	{
    	// print_r($rec); echo '<hr>';
    	$currstatus = $rec->status;
        if ($currstatus == 4 && $school_operator_is && !$admin_is  && !$region_operator_is && !$rayon_operator_is )  {
	        error(get_string('accessdenied','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
        }

        if ($currstatus >= 5 && ($rayon_operator_is || $school_operator_is) && !$admin_is  && !$region_operator_is)  {
	        error(get_string('accessdenied','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
        }

        if ($rec->id != $fid)  {
	        error(get_string('accessdenied','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
        }

    }

    $school = get_record('monit_school', 'id', $sid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$school->name</a>";
	$breadcrumbs .= " -> $strformname";
	$CFG->javascript = $CFG->dirroot.'/blocks/monitoring/lib_js.php';
    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	// echo $copynext.'<br>'.$copyprev;
	// print_r($CFG->javascript);

   // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	if ($rid == 0 ||  $sid == 0) exit();

    if ($action == 'copy' && $copynext == '-')  {
        /*
    	if ($nm == 9)  {
   	        $prev_school = get_record('monit_school', 'uniqueconstcode', $school->uniqueconstcode, 'yearid', $yid - 1);
  	   	    $new_sid = $prev_school->id;
 	   	    $datefromnew = get_date_from_month_year(8, $yid - 1);
  	   	} else {
  	   	    $new_sid = $sid;
	   	    $datefromnew = get_date_from_month_year($nm - 1, $yid);
	   	}
        */
        switch ($nm)    {
            case 9:
       	        $prev_school = get_record('monit_school', 'uniqueconstcode', $school->uniqueconstcode, 'yearid', $yid - 1);
      	   	    $new_sid = $prev_school->id;
     	   	    $datefromnew = get_date_from_month_year(12, $yid - 1);
            break;
            case 12:
      	   	    $new_sid = $sid;
    	   	    $datefromnew = get_date_from_month_year(9, $yid);
            break;
            case 6:
      	   	    $new_sid = $sid;
    	   	    $datefromnew = get_date_from_month_year(12, $yid);
            break;
        }

	    $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
 		   		   WHERE (schoolid=$new_sid) and (shortname='$shortname') and (datemodified=$datefromnew)";
        // echo $strsql;           
	    if ($listform = get_record_sql($strsql))	{
	    	$rec = get_record('monit_form_'.$shortname, 'listformid', $listform->id);
	   	    switch ($shortname)	{
	      	   case 'rkp_u':
			   break;

	       	   case 'rkp_du':
	   	    	if (isset($rec->fd_u_1) && !empty($rec->fd_u_1)) {
			    	list($rec->fd_u_1_fact, $rec->fd_u_1_rekv, $rec->fd_u_1_link) = explode("|", $rec->fd_u_1);
			    }
	   	    	if (isset($rec->fd_u_2) && !empty($rec->fd_u_2)) {
		 		   	list($rec->fd_u_2_fact, $rec->fd_u_2_rekv, $rec->fd_u_2_link) = explode("|", $rec->fd_u_2);
		 		}
				break;
		   }
	       // print_r($rec);
		    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
			print_heading($strreports.': '.$school->name, "center", 3);
			print_heading($strformname.': '.$strnamemonth, "center", 4);

		   print_simple_box_start("center");
		   include("$shortname.html");
		   include("end_of_forms.html");
		   print_simple_box_end();
		   print_footer();
           exit();
   	    } else {
   	    	notice(get_string('preddatanotfound', 'block_monitoring'));
   	    }
	}

    if ($action == 'copy' && $copynext != '-')  {
   	    $datefromnew = get_date_from_month_year($nm + 1, $yid);
	    $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
 		   		   WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified=$datefromnew)";
	    if ($listform = get_record_sql($strsql))	{
	    	$rec = get_record('monit_form_'.$shortname, 'listformid', $listform->id);
	   	    switch ($shortname)	{
	      	   case 'rkp_u':
			   break;

	       	   case 'rkp_du':
	   	    	if (isset($rec->fd_u_1) && !empty($rec->fd_u_1)) {
			    	list($rec->fd_u_1_fact, $rec->fd_u_1_rekv, $rec->fd_u_1_link) = explode("|", $rec->fd_u_1);
			    }
	   	    	if (isset($rec->fd_u_2) && !empty($rec->fd_u_2)) {
		 		   	list($rec->fd_u_2_fact, $rec->fd_u_2_rekv, $rec->fd_u_2_link) = explode("|", $rec->fd_u_2);
		 		}
				break;
		   }
	       // print_r($rec);
		    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
			print_heading($strreports.': '.$school->name, "center", 3);
			print_heading($strformname.': '.$strnamemonth, "center", 4);

		   print_simple_box_start("center");
		   include("$shortname.html");
		   include("end_of_forms.html");
		   print_simple_box_end();
		   print_footer();
           exit();
   	    } else {
   	    	notice(get_string('nextdatanotfound', 'block_monitoring'));
   	    }
	}


	/// A form was submitted so process the input
	if ($rec = data_submitted())  {

	    if ($shortname == 'rkp_du')	 {
	    	$errcount = find_errors_in_links($rec, $err);
	    } else {
		    $errcount = find_form_errors($rec, $err, 'monit_form_'.$shortname);
	    }

		if ($errcount == 0)  {
            // print $fid. '<br>';
		    if ($fid == 0)  { // insert new records
			   $rkp->rayonid = $rid;
		       $rkp->schoolid = $sid;
		       $rkp->status = 2;
		       $rkp->shortname = $shortname;
		       $rkp->shortrusname =  $strformname;
		       // $rkp->fullname = ??????????
		       $rkp->datemodified = get_date_from_month_year($nm, $yid);


   			   $strsql = "SELECT id, rayonid, schoolid, shortname, datemodified FROM {$CFG->prefix}monit_school_listforms
	 		   		      WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified={$rkp->datemodified})";

	 		   if ($recsss = get_record_sql($strsql)) 	{
	 		   	  error(get_string('errorinduplicatedformcreate','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
	 		   }


		       if (!$idnew = insert_record('monit_school_listforms', $rkp))	{
					error(get_string('errorincreatinglist','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }

		       $rec->listformid = $idnew;

	      	   switch ($shortname)	{
	       	   	    case 'rkp_u':
					   if ($rec->f1_5_day!=0 &&  $rec->f1_5_month !=0 && $rec->f1_5_year !=0)  {
							 $rec->f1_5g1 = get_timestamp_from_date($rec->f1_5_day, $rec->f1_5_month, $rec->f1_5_year);
					   }
				       if ($rec->f5_1_day!=0 &&  $rec->f5_1_month !=0 && $rec->f5_1_year !=0)  {
							 $rec->f5_1g1 = get_timestamp_from_date($rec->f5_1_day, $rec->f5_1_month, $rec->f5_1_year);
				       }

					break;

	       	   	    case 'rkp_du':
		      	   	   if ($rec->fd_u_1_factday!=0 &&  $rec->fd_u_1_factmonth !=0 && $rec->fd_u_1_factyear !=0)	 {
						   $rec->fd_u_1_fact = get_timestamp_from_date($rec->fd_u_1_factday, $rec->fd_u_1_factmonth, $rec->fd_u_1_factyear);
					   }
	  	    	   	   if ($rec->fd_u_2_factday!=0 &&  $rec->fd_u_2_factmonth !=0 && $rec->fd_u_2_factyear !=0)	 {
						   $rec->fd_u_2_fact = get_timestamp_from_date($rec->fd_u_2_factday, $rec->fd_u_2_factmonth, $rec->fd_u_2_factyear);
				 	  }
				       if (isset($rec->fd_u_1_fact)) $rec->fd_u_1 = $rec->fd_u_1_fact . '|' . $rec->fd_u_1_rekv . '|' . $rec->fd_u_1_link;
				       if (isset($rec->fd_u_2_fact)) $rec->fd_u_2 = $rec->fd_u_2_fact . '|' . $rec->fd_u_2_rekv . '|' . $rec->fd_u_2_link;
					break;

					case 'bkp_f':
					    $arrec = (array)$rec;
						foreach ($arrec as $key => $value)	{
							if (empty($value)) $arrec[$key] = 0;
						}
						$rec->f1f = $arrec['f1_1f'] + $arrec['f1_2f'] + $arrec['f1_3f'] + $arrec['f1_4f'];
						$rec->f2_1f = $arrec['f2_1_1f'] + $arrec['f2_1_2f'] + $arrec['f2_1_3f'];
						$rec->f2_2f = $arrec['f2_2_1f'] + $arrec['f2_2_2f'] + $arrec['f2_2_3f'];
						$rec->f2_3f = $arrec['f2_3_1f'] + $arrec['f2_3_2f'] + $arrec['f2_3_3f'];
						$rec->f2_4f = $arrec['f2_4_1f'] + $arrec['f2_4_2f'] + $arrec['f2_4_3f'];
						$rec->f2_5f = $arrec['f2_5_1f'] + $arrec['f2_5_2f'] + $arrec['f2_5_3f'];
						$rec->f2_6f = $arrec['f2_6_1f'] + $arrec['f2_6_2f'] + $arrec['f2_6_3f'];
						$rec->f2f = 0;
						for ($i=1; $i<=6; $i++) {
							$varname = 'f2_'.$i.'f';
							if (isset($rec->{$varname}) && !empty($rec->{$varname}))	{
								$rec->f2f += $rec->{$varname};								
							}
						}
						
					break;
			   }


               // print_r($rec);
		       if (!insert_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorincreatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }
		        // notice(get_string('succesavedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
				redirect("$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid", get_string('succesavedata','block_monitoring'), 0);

		    } else {  // update records

		       $rec->listformid = $fid;
		       $df = get_record_sql("SELECT id, listformid FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid");
		       $rec->id = $df->id;

	      	   switch ($shortname)	{
	       	   	    case 'rkp_u':
					   if ($rec->f1_5_day!=0 &&  $rec->f1_5_month !=0 && $rec->f1_5_year !=0)  {
							 $rec->f1_5g1 = get_timestamp_from_date($rec->f1_5_day, $rec->f1_5_month, $rec->f1_5_year);
					   }
				       if ($rec->f5_1_day!=0 &&  $rec->f5_1_month !=0 && $rec->f5_1_year !=0)  {
							 $rec->f5_1g1 = get_timestamp_from_date($rec->f5_1_day, $rec->f5_1_month, $rec->f5_1_year);
				       }
					break;

	       	   	    case 'rkp_du':
		      	   	   if ($rec->fd_u_1_factday!=0 &&  $rec->fd_u_1_factmonth !=0 && $rec->fd_u_1_factyear !=0)	 {
						   $rec->fd_u_1_fact = get_timestamp_from_date($rec->fd_u_1_factday, $rec->fd_u_1_factmonth, $rec->fd_u_1_factyear);
					   }
	  	    	   	   if ($rec->fd_u_2_factday!=0 &&  $rec->fd_u_2_factmonth !=0 && $rec->fd_u_2_factyear !=0)	 {
						   $rec->fd_u_2_fact = get_timestamp_from_date($rec->fd_u_2_factday, $rec->fd_u_2_factmonth, $rec->fd_u_2_factyear);
				 	  }
				       if (isset($rec->fd_u_1_fact)) $rec->fd_u_1 = $rec->fd_u_1_fact . '|' . $rec->fd_u_1_rekv . '|' . $rec->fd_u_1_link;
				       if (isset($rec->fd_u_2_fact)) $rec->fd_u_2 = $rec->fd_u_2_fact . '|' . $rec->fd_u_2_rekv . '|' . $rec->fd_u_2_link;
					break;

					case 'bkp_f':
					    $arrec = (array)$rec;
						foreach ($arrec as $key => $value)	{
							if (empty($value)) $arrec[$key] = 0;
						}
						$rec->f1f = $arrec['f1_1f'] + $arrec['f1_2f'] + $arrec['f1_3f'] + $arrec['f1_4f'];
						$rec->f2_1f = $arrec['f2_1_1f'] + $arrec['f2_1_2f'] + $arrec['f2_1_3f'];
						$rec->f2_2f = $arrec['f2_2_1f'] + $arrec['f2_2_2f'] + $arrec['f2_2_3f'];
						$rec->f2_3f = $arrec['f2_3_1f'] + $arrec['f2_3_2f'] + $arrec['f2_3_3f'];

						/*
						bcscale(6);
						$rec->f2_3f = bcadd($arrec['f2_3_1f'], $arrec['f2_3_2f'], 6);
						$rec->f2_3f = bcadd($rec->f2_3f, $arrec['f2_3_3f'], 6);
						echo $arrec['f2_3_1f']. ' ' . $arrec['f2_3_2f']. ' '. $arrec['f2_3_3f'];
						echo '<br>';
						echo $rec->f2_3f;
						*/

						$rec->f2_4f = $arrec['f2_4_1f'] + $arrec['f2_4_2f'] + $arrec['f2_4_3f'];
						$rec->f2_5f = $arrec['f2_5_1f'] + $arrec['f2_5_2f'] + $arrec['f2_5_3f'];
						$rec->f2_6f = $arrec['f2_6_1f'] + $arrec['f2_6_2f'] + $arrec['f2_6_3f'];
						$rec->f2f = 0;
						for ($i=1; $i<=6; $i++) {
							$varname = 'f2_'.$i.'f';
							if (isset($rec->{$varname}) && !empty($rec->{$varname}))	{
								$rec->f2f += $rec->{$varname};								
							}
						}
					break;
			   }

               // print_r($rec);
		       if (!update_monit_record('monit_form_'.$shortname, $rec))	{
		       		print_r($rec);
					error(get_string('errorinupdatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
			   }
		       notice(get_string('succesupdatedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/school/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid");
		    }

		}

	}

    if ($fid != 0)  {
    	$rec = get_record('monit_form_'.$shortname, 'listformid', $fid);
   	    switch ($shortname)	{
      	   case 'rkp_u':
		   break;

       	   case 'rkp_du':
   	    	if (isset($rec->fd_u_1) && !empty($rec->fd_u_1)) {
		    	list($rec->fd_u_1_fact, $rec->fd_u_1_rekv, $rec->fd_u_1_link) = explode("|", $rec->fd_u_1);
		    }
   	    	if (isset($rec->fd_u_2) && !empty($rec->fd_u_2)) {
	 		   	list($rec->fd_u_2_fact, $rec->fd_u_2_rekv, $rec->fd_u_2_link) = explode("|", $rec->fd_u_2);
	 		}
			break;
	   }
       // print_r($rec);
    }

    // print_r($rec);
    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
	print_heading($strreports.': '.$school->name, "center", 3);
	print_heading($strformname.': '.$strnamemonth, "center", 4);

    print_simple_box_start("center");
  	print_heading(get_string('msgaboutcopydata','block_monitoring'), "center", 4);
	?>
		<form name="copypredform" method="post" action="htmlforms.php">
		<input type="hidden" name="rid" value="<?php echo $rid ?>" />
		<input type="hidden" name="sid" value="<?php echo $sid ?>" />
		<input type="hidden" name="fid" value="<?php echo $fid ?>" />
		<input type="hidden" name="yid" value="<?php echo $yid ?>" />
		<input type="hidden" name="nm" value="<?php echo $nm ?>" />
		<input type="hidden" name="sn" value="<?php echo $shortname ?>" />
		<input type="hidden" name="action" value="copy" />
		<table align="center">
		<tr>
		<td align="center">
		<input type="submit" name=copypred value="Предыдущего отчетного месяца" />
		</td>
		</tr>
		</table>
		</form>
<?php
/*
		<td align="center">
		<input type="submit" name=copynext value="<?php print_string('copynext', 'block_monitoring') ?>" />
		</td>
*/
    include("$shortname.html");
    include("end_of_forms.html");

  	print_simple_box_end();

	if ($shortname == 'bkp_zp')	{
		print_simple_box_start("center");

        $strimglink = "bkp_zp_graph.php?fid=$fid";
        echo "<center><a target=_blank href=\"$strimglink\"> <img src=\"$strimglink\" alt=graph /> </a> </center>";
/*  	echo '<center><img src="'.$CFG->wwwroot.'/blocks/monitoring/school/bkp_zp_graph.php?fid='.$fid.'" alt=graph /></center>';
  	    echo '<center><a target=_blank href="'.$CFG->wwwroot.'/blocks/monitoring/school/bkp_zp_graph.php?fid='.$fid.'"></a></center>';
*/
      	print_simple_box_end();
    }

    if ($fid != 0 && ($admin_is  || $region_operator_is || $rayon_operator_is) )  {
   		if (!isregionviewoperator() && !israyonviewoperator())  {
	    print_simple_box_start("center");

		print_heading(get_string('changestatus', 'block_monitoring'), "center", 4);
		?>
		<form name="statuses" method="post" action="changestatus.php">
		<input type="hidden" name="rid" value="<?php echo $rid ?>" />
		<input type="hidden" name="sid" value="<?php echo $sid ?>" />
		<input type="hidden" name="fid" value="<?php echo $fid ?>" />
		<input type="hidden" name="yid" value="<?php echo $yid ?>" />
		<input type="hidden" name="nm" value="<?php echo $nm ?>" />
		<input type="hidden" name="sn" value="<?php echo $shortname ?>" />
		<table border="0" cellspacing="2" cellpadding="5" align="center">
		<tr>
		<td align="center" bgcolor="<?php print_string('status6color', 'block_monitoring') ?>" >
		<input type="submit" name=status6 value="<?php print_string('status6', 'block_monitoring') ?>" />
		</td>
		<td align="center" bgcolor="<?php print_string('status3color', 'block_monitoring') ?>" >
		<input type="submit" name=status3 value="<?php print_string('status3', 'block_monitoring') ?>" />
		</td>
		<?php
	    if ($admin_is  || $region_operator_is)  {
		?>
		<td align="center" bgcolor="<?php print_string('status5color', 'block_monitoring') ?>" >
		<input type="submit" name=status5 value="<?php print_string('status5', 'block_monitoring') ?>" />
		</td>
		<?php
         }
		?>

		</tr>
		</table>
		</form>
		<?php

		  	print_simple_box_end();
		}
	}

    print_footer();


// Find input error in forms
function find_errors_in_links(&$rec, &$err)
{
	global $CFG, $db;

    if (isset($rec->fd_u_1_link))	{
	  // $a = get_headers($rec->fd_u_1_link);
       // $a=check_url($rec->fd_u_1_link);
      // $a=check_url_file($rec->fd_u_1_link);
      //print $a;
      // if (!$a)      $err['fd_u_1_link']=0;
      // else print_r($a);
    }

    if (isset($rec->fd_u_2_link))	{

    }
    // return count($err);
    return 0;
}


?>


