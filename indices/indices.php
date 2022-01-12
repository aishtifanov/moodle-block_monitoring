<?php // $Id: indices.php,v 1.23 2011/01/26 09:01:13 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once($CFG->libdir.'/tablelib.php');

    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);          // School id
    $report = optional_param('report', 0);
    $levelmonit  = optional_param('level', 'region');

    $nm = date('n');
	$yid = get_current_edu_year_id();

    require_once('lib_indices_'.$levelmonit.'.php');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    $strindices = get_string('indices', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strindices";
    print_header_mou("$site->shortname: $strindices", $site->fullname, $breadcrumbs);

    $currenttab = $levelmonit;
    include('tabsindices.php');

	$currday = get_rus_format_date(time());
	$curryear = date('Y');

    $strnumberf = get_string('symbolnumber', 'block_monitoring');
    $strnameofpokazatel = get_string('nameofpokazatel', 'block_monitoring');

	$reportsmenu = array();
    $reportsmenu[0] = get_string('selectareport','block_monitoring').' ...';

	$strsql = "SELECT * FROM {$CFG->prefix}monit_form
 		 		   WHERE (reported=1) and (period='month') and (levelmonit='$levelmonit')";
	if($vid_reports = get_records_sql($strsql))	{
     	 foreach ($vid_reports as $vr) 	{
		      	 $reportsmenu[$vr->id] = $vr->name;
	  	 }
	}

    switch ($levelmonit)	{
		case 'region':
						echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						echo '<tr> <td>'.get_string('summaryreport', 'block_monitoring').': </td><td>';
						popup_form("indices.php?level=region&amp;sid=0&amp;rid=0&amp;report=", $reportsmenu, 'switchreport', $report, '', '', '', false);
						echo '</td></tr>';
  						echo '</table>';


                        if ($report != 0) 	{

						    @set_time_limit(0);
						    @raise_memory_limit("192M");
						    if (function_exists('apache_child_terminate')) {
						        @apache_child_terminate();
						    }

                        	$vid_report = get_record('monit_form', 'id', $report);
   	                        print_heading($vid_report->fullname.' '.$curryear.' году', 'center', 3);

   	                        $straction = get_string('actions', 'block_monitoring');
   	                        if ($vid_report->name == $straction)  {

								$str1 = get_string('symbolnumber', 'block_monitoring');
								$str2 = get_string('contolmeasure', 'block_monitoring');
								$str3 = get_string('contoldate', 'block_monitoring');
								$str4 = get_string('factdate', 'block_monitoring');
								$str5 = get_string('rekvizitsnormakt', 'block_monitoring');
								$str6 = get_string('hyperlinknormakt', 'block_monitoring');

   	                            $table->head  = array ($str1, $str2, $str3, $str4, $str5, $str6);
							    $table->align = array ('left', 'left', 'center', 'center');
							    $table->size = array ('5%', '50%', '10%', '10%');
							    $table->class = 'moutable';
							    // $table->headerstyle = 'moutable';

     	                        $rprt_razdel =  get_record('monit_razdel', 'formid', $report);
                                if ($rprt_fields =  get_records('monit_razdel_field', 'razdelid', $rprt_razdel->id))	{

								    $datefrom = get_date_from_month_year($nm, $yid);

									$strsql = "SELECT * FROM {$CFG->prefix}monit_region_listforms
							  		 		   WHERE (shortname='rkp_d') and (datemodified=$datefrom)";
								    $rec = get_record_sql($strsql);
								    if ($rec) 	{
								        $rec_d = get_record('monit_form_rkp_d', 'listformid', $rec->id);
                                        $arrec = (array)$rec_d;
		                                foreach ($rprt_fields as $rfld)  {
		                                	if (!empty($arrec[$rfld->name_field]))  {
			                                	list($fd_fact,$fd_rekv,$fd_link) = explode("|", $arrec[$rfld->name_field]);
			                                	$currvalue = get_rus_format_date($fd_fact);
			                                } else {
			                                	$currvalue = $fd_rekv = $fd_link = '-';
			                                }
			                                $strfld = translitfield($rfld->name_field);

											$table->data[] = array ($strfld, $rfld->name, '-', $currvalue, $fd_rekv, $fd_link);
		                                }
		                            } else {
   		                                foreach ($rprt_fields as $rfld)  {
 			                                $strfld = translitfield($rfld->name_field);
											$table->data[] = array ($strfld, $rfld->name, '-', '-', '-', '-');
   		                                }
		                            }

		                        }
                               print_color_table($table);
                               unset($table);

   	                       } else {

   	                          $strcurrent = get_string('current', 'block_monitoring');
   	                          $strobligation = get_string('obligation', 'block_monitoring');
	  	                      if ($vid_report->name == $strcurrent)  {
	          					 $num_razd = 0;
   	                         	 $strplanvalue = get_string('indicesprev', 'block_monitoring');
								 $strfactvalue = get_string('indicescurr', 'block_monitoring');
	  	                      }	else {
                            	 $num_razd = 1;
   	                         	 $strplanvalue = get_string('planvalue', 'block_monitoring', $curryear);
								 $strfactvalue = get_string('factvalue', 'block_monitoring', $currday);
							  }


                              if ($rprt_razdels =  get_records('monit_razdel', 'formid', $report))	{
                                foreach ($rprt_razdels as $rrazd)  {
                                	$strrazd = $num_razd.'. '.$rrazd->name;
		   	                        print_heading($strrazd, 'center', 4);
	   	                            $table->head  = array ($strnumberf, $strnameofpokazatel, $strplanvalue, $strfactvalue);
 								    $table->align = array ('left', 'left', 'center', 'center');
 								    $table->size = array ('5%', '50%', '10%', '10%');
 								    $table->class = 'moutable';
 								    $table->headerstyle = 'moutable';

                                    if ($rprt_fields =  get_records('monit_razdel_field', 'razdelid', $rrazd->id))	 {

                                        // print_r($rprt_fields);
		                            	foreach ($rprt_fields as $rfld)  {

                                            if (isset($rfld->name_field))	{
				                                $strfld = translitfield($rfld->name_field);
												$num_razd = substr($strfld, 0, 1);
												if (is_numeric($num_razd)) $strrazd = $num_razd.'. '.$rrazd->name;
				                            } else {
				                            	$strfld = '';
				                            }

	                                		for ($i=0; $i<=1; $i++)	{
		                                		$value[$i] = '-';
		                                	}

		                                	if (function_exists($rfld->calcfunc)) {
		                                		$namefunc = $rfld->calcfunc;
		                                		for ($i=0; $i<=1; $i++)	{
			                                		$value[$i] = $namefunc($i);
			                                		$value[$i] = switch_edizm ($rfld, $value[$i]);
			                                	}
                                            }
               	  	                      	if ($vid_report->name == $strobligation)  {
               	  	                      	    $value[1] = '-';
               	  	                      	}

											$table->data[] = array ($strfld, $rfld->name, $value[1], $value[0]);
           								}
				                    }

                                    print_color_table($table);
                                    unset($table);
									$num_razd++;
                                }
                              }
                            }
                        }


		break;

		case 'rayon':   echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						listbox_rayons("indices.php?level=rayon&amp;sid=0&amp;rid=", $rid);
						echo '<tr> <td>'.get_string('summaryreport', 'block_monitoring').': </td><td>';
						popup_form("indices.php?level=rayon&amp;sid=0&amp;rid=$rid&amp;report=", $reportsmenu, 'switchreport', $report, '', '', '', false);
						echo '</td></tr>';
						echo '</table>';

						if ($rid!=0 && $report!=0) {
                        	$vid_report = get_record('monit_form', 'id', $report);
                        	$rayon = get_record('monit_rayon', 'id', $rid);
                        	$title = $vid_report->fullname . ' <br>' . $rayon->name .' в '.$curryear.' году по состоянию на ' . get_rus_format_date(time());
   	                        print_heading($title, 'center', 3);
	                        $strplanvalue = get_string('indicesprev', 'block_monitoring');
							$strfactvalue = get_string('indicescurr', 'block_monitoring');
						    $datefrom[0] = get_date_from_month_year($nm, $yid);
						    $datefrom[1] = get_date_from_month_year($nm-1, $yid);

                            if ($rprt_razdels =  get_records('monit_razdel', 'formid', $report))	{

                                foreach ($rprt_razdels as $rrazd)  {

	   	                            $table->head  = array ($strnumberf, $strnameofpokazatel, $strplanvalue, $strfactvalue);
 								    $table->align = array ('left', 'left', 'center', 'center');
 								    $table->size = array ('5%', '50%', '10%', '10%');
 								    $table->class = 'moutable';
 								    $table->headerstyle = 'moutable';

                                    if ($rprt_fields =  get_records('monit_razdel_field', 'razdelid', $rrazd->id))	 {

		                            	foreach ($rprt_fields as $rfld)  {

                                    		// print_r($rfld); echo '<hr>';
                                     		if ($rfld->name_field == 'f0_10m') continue;

                                            if (isset($rfld->name_field))	{
				                                $strfld = translitfield($rfld->name_field);
												$num_razd = substr($strfld, 0, 1);
												if (is_numeric($num_razd)) $strrazd = $num_razd.'. '.$rrazd->name;
				                            } else {
				                            	$strfld = '';
				                            }

	                                		for ($i=0; $i<=1; $i++)	{
		                                		$value[$i] = '-';
		                                	}

		                                	if (function_exists($rfld->calcfunc)) {
		                                		$namefunc = $rfld->calcfunc;
		                                		for ($i=0; $i<=1; $i++)	{
			                                		$value[$i] = $namefunc($i);
			                                		$value[$i] = switch_edizm ($rfld, $value[$i]);
			                                	}
                                            }

											$table->data[] = array ($strfld, $rfld->name, $value[1], $value[0]);
           								}
				                    }
		   	                        print_heading($strrazd, 'center', 4);
	                                print_color_table($table);
 	                                unset($table);
				                }
		                    }
						}

		break;

		case 'school':  echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
						listbox_rayons("indices.php?level=school&amp;sid=0&amp;yid=$yid&amp;rid=", $rid);
						listbox_schools("indices.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);

						echo '<tr> <td>'.get_string('summaryreport', 'block_monitoring').': </td><td>';
						popup_form("indices.php?level=school&amp;sid=$sid&amp;rid=$rid&amp;report=", $reportsmenu, 'switchreport', $report, '', '', '', false);
						echo '</td></tr>';

						echo '</table>';
						if ($rid!=0 && $sid!=0 && $report!=0) {
                        	$vid_report = get_record('monit_form', 'id', $report);
                        	$school = get_record('monit_school', 'id', $sid, 'rayonid', $rid);
                        	$title = $vid_report->fullname . ' ' . $school->name .' в '.$curryear.' году по состоянию на ' . get_rus_format_date(time());
   	                        print_heading($title, 'center', 3);
	                        $strfirstvalue = get_string('indicesfirst', 'block_monitoring');
	                        $strplanvalue = get_string('indicesprev', 'block_monitoring');
							$strfactvalue = get_string('indicescurr', 'block_monitoring');
                            
						    $datefrom[0] = get_date_from_month_year($nm, $yid);
						    $datefrom[1] = get_date_from_month_year($nm-1, $yid);

                            if ($rprt_razdels =  get_records('monit_razdel', 'formid', $report))	{

                                foreach ($rprt_razdels as $rrazd)  {

	   	                            $table->head  = array ($strnumberf, $strnameofpokazatel, $strfirstvalue, $strplanvalue, $strfactvalue);
 								    $table->align = array ('left', 'left', 'center', 'center', 'center');
 								    $table->width = '100%';
 								    $table->size = array ('5%', '50%', '10%', '10%', '10%');
 								    $table->class = 'moutable';
 								    $table->headerstyle = 'moutable';

                                    if ($rprt_fields =  get_records('monit_razdel_field', 'razdelid', $rrazd->id))	 {

                                        // $i - это относительный номер месяца
                                        // $i = 0 - это текущий месяц
                                        // $i = 1 - это предыдущий месяц
                                        for ($i=0; $i<=1; $i++)	{
											$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
									  		 		   WHERE (shortname='$rrazd->shortname') and (datemodified={$datefrom[$i]}) and (schoolid=$sid)";
									    	if ($rschlist[$i] = get_record_sql($strsql))	{
										        $r_form_rkp[$i] = get_record('monit_form_'.$rrazd->shortname, 'listformid', $rschlist[$i]->id);
		                                        $ar_form_rkp[$i] = (array)$r_form_rkp[$i];
		                                    }
		                                }

			                           	foreach ($rprt_fields as $rfld)  {
                                            // print_r($rfld); echo '<hr>';
                                            if ($rfld->name_field == 'f0_2u' || $rfld->name_field == 'f0_10u') continue;
                                            if (isset($rfld->name_field))	{
				                                $strfld = translitfield($rfld->name_field);
												$num_razd = substr($strfld, 0, 1);
	                                			if (is_numeric($num_razd)) $strrazd = $num_razd.'. '.$rrazd->name;
				                            } else {
				                            	$strfld = '';
				                            }

	                                		for ($i=-1; $i<=1; $i++)	{
		                                		$value[$i] = '-';
		                                	}

		                                	if (!empty($rfld->calcfunc) && function_exists($rfld->calcfunc)) {
		                                		$namefunc = $rfld->calcfunc;
		                                		for ($i=-1; $i<=1; $i++)	{
			                                		$value[$i] = $namefunc($i);
			                                		$value[$i] = switch_edizm ($rfld, $value[$i], $i);
			                                	}
                                            }

 		                                	if (empty($rfld->calcfunc) && isset($rfld->name_field))	{
		                                		for ($i=0; $i<=1; $i++)	{
		                                			if (!empty($ar_form_rkp[$i][$rfld->name_field]))  {
						                                $value[$i] = $ar_form_rkp[$i][$rfld->name_field];
						                                $value[$i] = switch_edizm ($rfld, $value[$i],$i);
						                            }
				                                }
				                                $value[-1] = get_from_osh1($rfld);
                                            }

											$table->data[] = array ($strfld, $rfld->name, $value[-1], $value[1], $value[0]);
				                        }
				                    }
		   	                        print_heading($strrazd, 'center', 4);
	                                print_color_table($table);
 	                                unset($table);
				                }
		                    }
						}
		break;
    }

    print_footer();



function get_from_osh1 (&$rfld)
{
  global $CFG, $sid, $yid;

  $ret = '-';
  $datefrom = get_date_from_month_year(1, $yid);

  $strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
   		     WHERE (shortname='77') and (datemodified=$datefrom) and (schoolid=$sid)";

  if ($rec = get_record_sql($strsql))	{
	  switch($rfld->name_field)	{
	  	case 'f0_1u': $strsql = "select listformid, `f-r4-18-4` as rez from {$CFG->prefix}monit_bkp_table_77 where listformid={$rec->id}";
                      if ($field = get_record_sql($strsql))	{
                      	 $ret = $field->rez;
                      }
 	  	break;
	  	case 'f0_2u': $strsql = "select listformid, `f-r4-18-3` as rez from {$CFG->prefix}monit_bkp_table_77 where listformid={$rec->id}";
                      if ($field = get_record_sql($strsql))	{
                      	 $ret = $field->rez;
                      }
	  	break;
	  	case 'f0_9u': $strsql = "select listformid, `f-r4-14-4` as rez from {$CFG->prefix}monit_bkp_table_77 where listformid={$rec->id}";
                      if ($field = get_record_sql($strsql))	{
                      	 $ret = $field->rez;
                      }
	  	break;
	  	case 'f0_10u': $strsql = "select listformid, `f-r4-15-4` as rez from {$CFG->prefix}monit_bkp_table_77 where listformid={$rec->id}";
                      if ($field = get_record_sql($strsql))	{
                      	 $ret = $field->rez;
                      }
	  	break;
	  	case 'f0_11u': $strsql = "select listformid, `f-r4-16-4` as rez from {$CFG->prefix}monit_bkp_table_77 where listformid={$rec->id}";
                      if ($field = get_record_sql($strsql))	{
                      	 $ret = $field->rez;
                      }
 	 	break;
	  }
  }

  return $ret;
}



?>


