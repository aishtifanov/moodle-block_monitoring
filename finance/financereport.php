<?php // $Id: financereport.php,v 1.11 2009/02/25 08:23:49 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);          // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $levelmonit  = required_param('level');

    $nm  = optional_param('nm', '1', PARAM_INT);       // Month number
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id
    $vkladka = optional_param('vkladka', 'finstatus0');       // Vkladka name

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


    if ($sid!=0)	{    	$school = get_record('monit_school', 'id', $sid);
   	    $strschool = $school->name;
    }	else  {   	    $strschool = get_string('school', 'block_monitoring');    }


    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
	$strrayon = get_string('rayon', 'block_monitoring');
	$strrayons = get_string('rayons', 'block_monitoring');

    $strschools = get_string('schools', 'block_monitoring');
	$strreports = get_string('financereport', 'block_monitoring');

    $strlevel = get_string($levelmonit, 'block_monitoring');


	switch ($levelmonit)	{
		case 'region':
					if ($admin_is || $region_operator_is) 	{
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> $strlevel";
					    print_header_mou("$site->shortname: $strlevel", $site->fullname, $breadcrumbs);
					    $region = get_record('monit_region', 'id', 1);
						print_heading($strreports.': '.$region->name, "center", 3);
					} else {
					    print_footer();
					 	exit();
					}
		break;

		case 'rayon':
					if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
						$breadcrumbs .= " -> $strlevel";
					    print_header_mou("$site->shortname: $strlevel", $site->fullname, $breadcrumbs);

						echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						listbox_rayons("financereport.php?level=rayon&amp;sid=0&amp;rid=", $rid);
						echo '</table>';

						if ($rid == 0) {
						    print_footer();
						 	exit();
						}

						if ($rayon_operator_is && $rayon_operator_is != $rid)  {							notify(get_string('selectownrayon', 'block_monitoring'));
						    print_footer();
							exit();						}
					    $rayon = get_record('monit_rayon', 'id', $rid);

						print_heading($strreports.': '.$rayon->name, "center", 3);
					}

		break;
		case 'school':
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
							$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
						}
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;sid=$sid\">$strschools</a>";
						$breadcrumbs .= " -> $strlevel";
					    print_header_mou("$site->shortname: $strlevel", $site->fullname, $breadcrumbs);

						if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
							echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_rayons("financereport.php?level=school&amp;sid=0&amp;&amp;yid=$yidrid=", $rid);
							listbox_schools("financereport.php?level=school&amp;rid=$rid&amp;&amp;yid=$yidsid=", $rid, $sid, $yid);
							echo '</table>';
						}

						if ($rid == 0 ||  $sid == 0) {
					        print_footer();
						    exit();
						}

					    $school = get_record('monit_school', 'id', $sid);
						print_heading($strreports.': '.$school->name, "center", 3);

		break;
    }


	print_tabs_typeforms($levelmonit, 'financereport', $nm, $yid, $rid, $sid);


   $toprow = array();
   for ($i=0; $i<=6; $i++)  {   	   $str = 'finstatus'.$i;
	   $toprow[] = new tabobject($str, $CFG->wwwroot."/blocks/monitoring/finance/financereport.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;vkladka=$str&amp;level=$levelmonit",
                 get_string($str, 'block_monitoring'));
   }
   $tabs = array($toprow);
   print_tabs($tabs, $vkladka, NULL, NULL);


    $str1 = get_string('predmet', 'block_monitoring');
    // $strname = get_string('territory', 'block_monitoring');
	$str2 = get_string('dateopen','block_monitoring');
 	$str3 = get_string('dateclose','block_monitoring');
	$str4 = get_string('summa', 'block_monitoring');
	$str5 = get_string('documentation','block_monitoring');
	$str6 = get_string('action','block_monitoring');

    $table->head  = array ($str1, $str2, $str3, $str4, $str5, $str6);
    $table->align = array ("center", "center", "center",  "center");
    $table->class = 'feedbackbox';

    $finstatus = substr($vkladka, 9);

	switch ($levelmonit)	{
		case 'region': $strsql = "SELECT *  FROM {$CFG->prefix}monit_form_rkp_f
						           WHERE levelmonit='region' AND finstatus=$finstatus";
		break;

		case 'rayon':  $strsql = "SELECT *  FROM {$CFG->prefix}monit_form_rkp_f
						           WHERE levelmonit='rayon' AND rayonid=$rid AND finstatus=$finstatus";
		break;

		case 'school': $strsql = "SELECT *  FROM {$CFG->prefix}monit_form_rkp_f
						           WHERE levelmonit='school' AND rayonid=$rid AND schoolid=$sid AND finstatus=$finstatus";
		break;
    }

    if ($finreports = get_records_sql($strsql))  {
	    foreach($finreports as $finreport)  {	    	switch ($vkladka) {	    		case 'finstatus0': case 'finstatus1': case 'finstatus2':
	    			$str1 = $finreport->concurs_name;
	    			if (isset($finreport->concurs_open)) {
		    			$str2 = convert_date($finreport->concurs_open, 'en', 'ru');
		    		} else {		    			$str2 = '-';		    		}
		   			if (isset($finreport->concurs_close)) {
	    				$str3 = convert_date($finreport->concurs_close, 'en', 'ru');
	    			} else {		    			$str3 = '-';	    			}

	    			// $str4 = $finreport->????????????

		   			if (isset($finreport->concurs_link)) {
		    			$str5 = $finreport->concurs_link;
		    		} else {
		    			$str5 = '-';		    		}

	 				$title = get_string('editcontract','block_monitoring');
					$strlinkupdate = "<a title=\"$title\" href=\"financehtmlform.php?mode=edit&amp;level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;frid={$finreport->id}\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";

					$title = get_string('deletecontract','block_monitoring');
				    $strlinkupdate .= "<a title=\"$title\" href=\"delcontract.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;frid={$finreport->id}&amp;vkladka=$vkladka\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";

					$table->data[] = array ($str1, $str2, $str3, '', $str5, $strlinkupdate);

	    		break;
	    		case 'finstatus3': case 'finstatus4': case 'finstatus5':
	    		break;
	    	}	    }
    }

   	print_color_table($table);

    print '<p><p><div align=center>';
	$options = array();
    $options['mode'] = 'new';
    $options['frid'] = 0;
    $options['rid'] = $rid;
    $options['sid'] = $sid;
    $options['level'] = $levelmonit;
    $options['finstatus'] = $finstatus;
   	$options['sesskey'] = $USER->sesskey;
    print_single_button("financehtmlform.php", $options, get_string('add'));
    print '</div>';
    print_footer();

?>


