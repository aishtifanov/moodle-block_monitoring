<?php // $Id: journals.php,v 1.13 2012/03/05 10:50:56 shtifanov Exp $

	require_once("../../../config.php");
	require_once('../lib.php');
	// require_once($CFG->libdir.'/tablelib.php');

	$rid = optional_param('rid', 0, PARAM_INT);
	$stid = optional_param('stid', 0, PARAM_INT);
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
//    $yid = required_param('yid', PARAM_INT);       		// Year id
	$nm = optional_param('nm',  date('n'), PARAM_INT);

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$strjournal = get_string('journalreports', 'block_monitoring');
    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strjournal";
    print_header_mou("$site->shortname: $strjournal", $site->fullname, $breadcrumbs);


    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }

	print_heading($strjournal, "center");

	print_tabs_years($yid, "journals.php?yid=");

	print_simple_box_start("center", "100%");

/*
	$tables = get_records_sql("select * from {$CFG->prefix}monit_form");
	$list_form[0] = get_string('all');

	foreach ($tables as $table){
		$list_form[$table->id] = get_string($table->levelmonit,'block_monitoring').':  '.$table->name;
	}
*/

	$tables = get_records_sql("select * from {$CFG->prefix}monit_rayon");
	// $list_region[0] = get_string('all');
	$list_rayon[0] = get_string('selectarayon', 'block_monitoring').'...';

	foreach ($tables as $table){
		$list_rayon[$table->id] = $table->name;
    }
    unset($tables);

    $tables = get_records_sql("select * from {$CFG->prefix}monit_status where isqueue=0");
	$list_status[0] = get_string('selectstatus', 'block_monitoring').'...';

    foreach ($tables as $table){
        // if ($table->id == 1) continue;
		$list_status[$table->id] = $table->name;
	}
    unset($tables);

	$list_month = array();
    for ($i=9; $i<=12; $i++)   {
       $list_month[$i] = get_string('nm_'.$i, 'block_monitoring');
  	}
    for ($i=1; $i<=8; $i++)   {
       $list_month[$i] = get_string('nm_'.$i, 'block_monitoring');
  	}

	echo "<form name='form_report' method='post' action='journals.php'>";
	echo "<input type=hidden name=yid value=$yid>";
	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
/*
    echo '<tr><td align=right>';
	print_string('reports', 'block_monitoring');
	echo '</td><td>';
	choose_from_menu ($list_form, "idf", "$idf");
    echo '</td></tr>';
*/
    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
	    echo '<tr><td align=right>';
		print_string('rayon', 'block_monitoring');
	    echo '</td><td>';
		choose_from_menu ($list_rayon, "rid", $rid, '');
	    echo '</td></tr>';
	} else if ($rayon_operator_is) {
		$rid = $rayon_operator_is;
	}

    echo '<tr><td align=right>';
	print_string('status', 'block_monitoring');
    echo '</td><td>';
	choose_from_menu ($list_status, "stid", $stid, '');
    echo '</td></tr>';
    echo '<tr><td align=right>';
	print_string('selectmonth', 'block_monitoring');
    echo '</td><td>';
	choose_from_menu ($list_month, "nm", $nm, '');
    echo '</td></tr>';
    echo '<tr><td align=right>';
    echo '</td><td align=center>';
    $search = get_string('search');
    print "<input type=submit value=$search>";
    echo '</td></tr></table>';
	echo "</form>";

	// echo "$rid - $stid	- $nm";

    if($rid != 0 && $stid !=0)    {
    	$strsql = "SELECT id, rayonid, name
     	    	   FROM {$CFG->prefix}monit_school
 		   		   WHERE rayonid=$rid AND isclosing=0 AND yearid=$yid
 		   		   ORDER BY name";
 		if ($schools = get_records_sql($strsql))	{

		    $strstatus = get_string('status', 'block_monitoring');
		    // $strname = get_string('territory', 'block_monitoring');
			$strtable = get_string('table','block_monitoring');
		 	$strperiod = get_string('period','block_monitoring');
			$straction = get_string("action","block_monitoring");

	        $datefrom = get_date_from_month_year($nm, $yid);

            foreach ($schools as $school)	{
                $schoolink = "<b><a href=listforms.php?yid=$yid&amp;rid=$rid&amp;sid={$school->id}>{$school->name}</a></b>";
                echo "<hr>";
                print_heading($schoolink, "center", 4);

                // $strrkps = "'rkp_u','rkp_du','rkp_prm_u','bkp_pred','bkp_dolj','bkp_f','bkp_zp'";
                if ($stid == 1) 	{
                    $strsql = "SELECT id, schoolid, status, shortname, shortrusname, datemodified
			     	    	   FROM {$CFG->prefix}monit_school_listforms
	 				   		   WHERE (schoolid={$school->id}) and (datemodified=$datefrom) and (shortname like '_kp%')";
                } else {
                    $strsql = "SELECT id, schoolid, status, shortname, shortrusname, datemodified
			     	    	   FROM {$CFG->prefix}monit_school_listforms
	 				   		   WHERE (schoolid={$school->id}) and (status=$stid) and (datemodified=$datefrom) and (shortname like '_kp%')";
	 			}

                // echo $strsql.'<br>';
                $listforms = get_records_sql($strsql);
			    if ($listforms || ($stid == 1))	{

				    $table->head  = array ($strstatus, $strtable, $straction);
				    $table->align = array ("center", "center", "center");
					$table->width = '60%';
				    $table->size = array ('7%', '10%', '5%');
					$table->class = 'moutable';

                    // print_r($listforms);

                    if ($stid == 1)  {
                        // $rkps = array('rkp_u', 'rkp_du', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
                        // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
                        // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
                        $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
                        $newlistforms = array();

                        $i=0;
                        foreach($rkps as $rkp)	{
                        	$flag = true;
							if ($listforms) foreach ($listforms as $listform)	{
								if ($rkp == $listform->shortname)  {
									$flag = false;
								}
							}
							if ($flag)  {
							 	$newlistforms[$i]->id = 0;
							    $newlistforms[$i]->schoolid = $school->id;
							    $newlistforms[$i]->status = 1;
						        $newlistforms[$i]->shortname = $rkp;
						        $newlistforms[$i]->shortrusname = get_string('name_'.$rkp, "block_monitoring");;
						        $i++;
							}
						}
						unset ($listforms);
						$listforms = $newlistforms;
						if ($i == 0) {
							notify(get_string('errorselectstatus', 'block_monitoring', $list_status[$stid]));
							continue;
						}
						/*
						echo '<hr>';
                        print_r($listforms);
                        */
                    }


  			  	 	foreach ($listforms as $rec)	{
  			  	 		
  			  	 		// if ($rec->shortname

						$links = array();

                        $sid = $school->id;
                        $rkp = $rec->shortname;
				    	$fid = $rec->id;
						$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
						$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");
						$strformrkpu = $rec->shortrusname;// get_string($rec->name,"block_monitoring");
						$currstatus = $rec->status;
                        if ($curryearid == $yid)	{
							if ($currstatus != 4 || ($admin_is  || $region_operator_is || $rayon_operator_is))  {
						 		$links['edit']->url = "htmlforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;fid=";
					 			$links['edit']->title = get_string('editschool','block_monitoring');
						 		$links['edit']->pixpath = "{$CFG->pixpath}/i/edit.gif";
						 	}

							if ($currstatus != 1 && $currstatus != 4)  {
						 		$links['status4']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status=4&amp;fid=";
						 		$links['status4']->title = get_string('sendtocoordination', 'block_monitoring');
						 		$links['status4']->pixpath = "{$CFG->pixpath}/s/yes.gif";
					        }

							if ($currstatus > 1 && ($admin_is  || $region_operator_is || $rayon_operator_is)) {
						 		$links['status6']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status6=6&amp;fid=";
						 		$links['status6']->title = get_string('status6', 'block_monitoring');
						 		$links['status6']->pixpath = "{$CFG->pixpath}/i/tick_green_big.gif";

						 		$links['status3']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status3=3&amp;fid=";
						 		$links['status3']->title = get_string('status3', 'block_monitoring');
						 		$links['status3']->pixpath = "{$CFG->pixpath}/i/return.gif";
						 	}

							if ($currstatus >= 6 && ($admin_is  || $region_operator_is)) {
						 		$links['status5']->url = "changestatus.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;status5=5&amp;fid=";
						 		$links['status5']->title = get_string('status5', 'block_monitoring');
						 		$links['status5']->pixpath = "{$CFG->wwwroot}/blocks/monitoring/i/archive.gif";
						 	}
                        }
				 		$links['excel']->url = "to_excel.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$rkp&amp;action=excel&amp;fid=";
				 		$links['excel']->title = get_string('downloadexcel');
				 		$links['excel']->pixpath = "{$CFG->pixpath}/f/xlsx.gif";

					    $strlinkupdate = '';
					    foreach ($links as $key => $link)	{

							$strlinkupdate .= "<a title=\"$link->title\" href=\"$link->url$fid\">";
							$strlinkupdate .= "<img src=\"{$link->pixpath}\" alt=\"$link->title\" /></a>&nbsp;";
					    }

					    $table->data[] = array ($strformrkpu_status, $strformrkpu, $strlinkupdate);
						$table->bgcolor[] = array ($strcolor);
						unset($links);
					}
					print_color_table($table);
					unset($table);
				} else {
					notify(get_string('errorselectstatus', 'block_monitoring', $list_status[$stid]));
				}
			}
		}
	} else {
		// notice(get_string('errorselectrayon', 'block_monitoring'));
	}
    print_footer();
?>