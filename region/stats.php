<?php // $Id: stats.php,v 1.7 2010/09/24 12:35:05 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

	define("NUMBER_OF_FORM", 6);
	define("NUMBER_OF_STATUS", 7);
	 
    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $levelmonit  = optional_param('level', 'rayon');
    $nm  = optional_param('nm', date('n'), PARAM_INT);  // Month number

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($rid !=0 && !$admin_is && !$region_operator_is && $rayon_operator_is)  {
    	$rayon = get_record('monit_rayon', 'id', $rid);
        if ($rayon->id != $rayon_operator_is)  {
	        error(get_string('accessdenied','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/index.php");
        }
    }

    if ($levelmonit == 'region' && !$admin_is && !$region_operator_is && $rayon_operator_is)  {
        error(get_string('accessdenied','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/index.php");
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    $strstats = get_string('statsreport', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strstats";
    print_header_mou("$site->shortname: $strstats", $site->fullname, $breadcrumbs);


    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    $datefrom = get_date_from_month_year($nm, $yid);

	if ($admin_is || $region_operator_is) 	{
	    $toprow = array();
	    $toprow[] = new tabobject('region', "stats.php?rid=$rid&amp;level=region&amp;nm=$nm&amp;yid=$yid",
	                get_string('statsregion', 'block_monitoring'));
	    $toprow[] = new tabobject('rayon', "stats.php?rid=$rid&amp;level=rayon&amp;nm=$nm&amp;yid=$yid",
	                get_string('statsrayon', 'block_monitoring'));
	    $tabs = array($toprow);

	    print_tabs($tabs, $levelmonit, NULL, NULL);
    }

	print_tabs_years($yid, "stats.php?rid=$rid&amp;level=$levelmonit&amp;yid=");

	print_tabs_all_months($nm, "stats.php?rid=$rid&amp;level=$levelmonit&amp;yid=$yid&amp;nm=");


    $datefrom = get_date_from_month_year($nm, $yid);
	$currday = get_rus_format_date(time());

    if ($year = get_record('monit_years', 'id', $yid))  {
		$curryear = get_string('uchyear', 'block_monitoring', $year->name);
  	} else {
       	$curryear = date('Y');
    }

    $strnumberf = get_string('symbolnumber', 'block_monitoring');
    $strnameofpokazatel = get_string('nameofpokazatel', 'block_monitoring');


    switch ($levelmonit)	{
		case 'region':
                      	$title = get_string('statsdata_region', 'block_monitoring'). get_string('nm_'.$nm, 'block_monitoring') . ' ' . $curryear;
                        print_heading($title, 'center', 3);

						$table->head[0] = get_string('table', 'block_monitoring');
						$table->align[0] = 'left';
						for ($i=1; $i<=NUMBER_OF_STATUS; $i++)  {
							$table->head[$i] = get_string('status'.$i, 'block_monitoring');
							$table->align[$i] = 'center';
                        }
						$table->class = 'moutable';

					    $allcount = count_records('monit_school', 'isclosing', 0, 'yearid', $yid);

						// $rkps = array('rkp_u', 'rkp_du', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
						// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
						// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
                        $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');

                        $i = -1;
						foreach($rkps as $rkp)	{

  						    $table->data[++$i][0] = get_string('name_'.$rkp, 'block_monitoring');

                            $count_except_rkp_u = 0;
							for ($status=1; $status<=NUMBER_OF_STATUS; $status++)  {
								$strsql = "SELECT id, status, shortname,  datemodified
										   FROM {$CFG->prefix}monit_school_listforms
								  		   WHERE (status = $status) and (shortname='$rkp') and (datemodified=$datefrom)";

							    $countforms = 0;
							    if ($stat = get_records_sql($strsql)) 	{
							    	$countforms = count ($stat);
                                }
				    			$proc = number_format($countforms/$allcount*100, 2, ',', '');
								$table->data[$i][$status] = "$countforms<br>($proc%)";
                                $count_except_rkp_u += $countforms;
                            }

                            $countforms = $allcount - $count_except_rkp_u;
			    			$proc = number_format($countforms/$allcount*100, 2, ',', '');
							$table->data[$i][1] = "$countforms<br>($proc%)";

                        }

						$table->data[++$i][0] = '<b>'.get_string('statstotal', 'block_monitoring').'<b>';

						for($j=1; $j<=NUMBER_OF_STATUS; $j++)  {
							$sum = 0;
							for($k=0; $k<NUMBER_OF_FORM; $k++)  {
								$sum += $table->data[$k][$j];
							}
			    			$proc = number_format($sum/($allcount*NUMBER_OF_FORM)*100, 2, ',', '');
							$table->data[$i][$j] = "<b>$sum<br>($proc%)<b>";
						}

                        print_color_table($table);
                        unset($table);
		break;

		case 'rayon':   if ($admin_is || $region_operator_is) 	{
							echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_rayons("stats.php?level=rayon&amp;nm=$nm&amp;rid=", $rid);
							echo '</table>';
						} else {
							$rid = $rayon_operator_is;
						}

						if ($rid != 0) {
                        	$rayon = get_record('monit_rayon', 'id', $rid);
   	                      	$title = get_string('statsdata_rayon', 'block_monitoring', $rayon->name). get_string('nm_'.$nm, 'block_monitoring') . ' ' . $curryear;
   	                        print_heading($title, 'center', 3);

							$table->head[0] = get_string('table', 'block_monitoring');
							$table->align[0] = 'left';
							for ($i=1; $i<=NUMBER_OF_STATUS; $i++)  {
								$table->head[$i] = get_string('status'.$i, 'block_monitoring');
								$table->align[$i] = 'center';
	                        }
							$table->class = 'moutable';

						    $allcount = count_records('monit_school', 'rayonid', $rid, 'yearid', $yid, 'isclosing', 0);

							// $rkps = array('rkp_u', 'rkp_du', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
							// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
							// $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
                            $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');

	                        $i = -1;
							foreach($rkps as $rkp)	{

  							    $table->data[++$i][0] = get_string('name_'.$rkp, 'block_monitoring');

	                            $count_except_rkp_u = 0;
								for ($status=1; $status<=NUMBER_OF_STATUS; $status++)  {
									$strsql = "SELECT id, rayonid, status, shortname,  datemodified
											   FROM {$CFG->prefix}monit_school_listforms
									  		   WHERE (rayonid = $rid) and (status = $status) and (shortname='$rkp') and (datemodified=$datefrom)";

								    $countforms = 0;
								    if ($stat = get_records_sql($strsql)) 	{
								    	$countforms = count ($stat);
	                                }
					    			$proc = number_format($countforms/$allcount*100, 2, ',', '');
									$table->data[$i][$status] = "$countforms<br>($proc%)";
	                                $count_except_rkp_u += $countforms;
	                            }

	                            $countforms = $allcount - $count_except_rkp_u;
				    			$proc = number_format($countforms/$allcount*100, 2, ',', '');
								$table->data[$i][1] = "$countforms<br>($proc%)";

	                        }

							$table->data[++$i][0] = '<b>'.get_string('statstotal', 'block_monitoring').'<b>';

							for($j=1; $j<=NUMBER_OF_STATUS; $j++)  {
								$sum = 0;
								for($k=0; $k<NUMBER_OF_FORM; $k++)  {
									$sum += $table->data[$k][$j];
								}
				    			$proc = number_format($sum/($allcount*NUMBER_OF_FORM)*100, 2, ',', '');
								$table->data[$i][$j] = "<b>$sum<br>($proc%)<b>";
							}

	                        print_color_table($table);
	                        unset($table);
                       }
		break;

    }

    print_footer();



?>


