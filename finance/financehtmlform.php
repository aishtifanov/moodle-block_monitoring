<?php // $Id: financehtmlform.php,v 1.9 2009/02/25 08:23:49 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $mode = required_param('mode', PARAM_ALPHA);    // new, add, edit, update
    $frid = required_param('frid', PARAM_INT);       // Finance report id
    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);       // School id
    $levelmonit = optional_param('level', 'region');       // Level num
	$finstatus = optional_param('finstatus', 0, PARAM_INT);       // Finance status
    $nm  = optional_param('nm', '1', PARAM_INT);       // Month number
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

	$strformname = get_string('name_rkp_f','block_monitoring');
    $strlevel = get_string($levelmonit, 'block_monitoring');

    if ($mode === "new" || $mode === "add" )	{
         $straddfinance = get_string('addfinance','block_monitoring');
    }
	else 	{
	     $straddfinance = get_string('updatefinance','block_monitoring');
	 }


    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
	switch ($levelmonit)	{
		case 'region':
					if ($admin_is || $region_operator_is) 	{
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $straddfinance";
					    print_header_mou("$site->shortname: $straddfinance", $site->fullname, $breadcrumbs);
					    $region = get_record('monit_region', 'id', 1);
						print_heading($strformname.': '.$region->name, "center", 3);
					} else {					    print_footer();
					 	exit();
					}
		break;

		case 'rayon':
					if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
					    $strrayons = get_string('rayons', 'block_monitoring');
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $straddfinance";
					    print_header_mou("$site->shortname: $straddfinance", $site->fullname, $breadcrumbs);

					    $rayon = get_record('monit_rayon', 'id', $rid);
						print_heading($strformname.': '.$rayon->name, "center", 3);
					} else {
					    print_footer();
					 	exit();
					}
		break;
		case 'school':
					    $strrayon = get_string('rayon', 'block_monitoring');
					    $strrayons = get_string('rayons', 'block_monitoring');
					    $strschool = get_string('school', 'block_monitoring');
					    $strschools = get_string('schools', 'block_monitoring');
					    $strreports = get_string('reportschool', 'block_monitoring');
					    $strrep = get_string('reports', 'block_monitoring');


					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka\">$strlevel</a>";
						$breadcrumbs .= " -> $straddfinance";
					    print_header_mou("$site->shortname: $straddfinance", $site->fullname, $breadcrumbs);

					    $school = get_record('monit_school', 'id', $sid);
						print_heading($strformname.': '.$school->name, "center", 3);

		break;
    }


    switch ($mode)	{
    	case 'new':
    	break;
	 	case 'add':
				if ($rec = data_submitted())  {
				    // print_r($rec);

			        $errcount = find_finance_form_errors($rec, $err);
			        if ($errcount == 0)	{
			            if (isset($rec->concurs_open) && !empty($rec->concurs_open))  {
				            $rec->concurs_open = convert_date($rec->concurs_open);
				        }
			            if (isset($rec->concurs_close) && !empty($rec->concurs_close))  {
				            $rec->concurs_close = convert_date($rec->concurs_close);
				        }
			            if (isset($rec->contract_open) && !empty($rec->contract_open))  {
				            $rec->contract_open = convert_date($rec->contract_open);
				        }
			            if (isset($rec->contract_close) && !empty($rec->contract_close))  {
				            $rec->contract_close = convert_date($rec->contract_close);
				        }

						if ($idnewf = insert_record('monit_form_rkp_f', $rec))	{
				            notify(get_string('financeformadded','block_monitoring').'(form)');
						} else {
							error(get_string('errorinaddingfinanceform','block_monitoring').'(form)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
						}

			            $arrec = (array)$rec;
			            for ($i=1; $i<=3; $i++)	{
							if (isset($arrec['dirnames'.$i]) && !empty($arrec['dirnames'.$i]))  {
								$rec_f->rkp_f_id = $idnewf;
								$rec_f->directionid = $arrec['dirnames'.$i];
								if  (isset($arrec['summa'.$i]) && !empty($arrec['summa'.$i]))  {
									$rec_f->summa = $arrec['summa'.$i];
									if (insert_record('monit_form_rkp_f_dir', $rec_f))	{
							            notify(get_string('financeformadded','block_monitoring').'(direction)');
									} else {
										error(get_string('errorinaddingfinanceform','block_monitoring').'(direction)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
									}
								}
							}
			            }


			            for ($i=1; $i<=3; $i++)	{
							if (isset($arrec['numpay'.$i]) && !empty($arrec['numpay'.$i]))  {
								$rec_f->rkp_f_id = $idnewf;
								$rec_f->number = $arrec['numpay'.$i];
								if  (isset($arrec['datepay'.$i]) && !empty($arrec['datepay'.$i]))  {
									$rec_f->paydate = convert_date($arrec['datepay'.$i]);
									if  (isset($arrec['sumpay'.$i]) && !empty($arrec['sumpay'.$i]))  {
										$rec_f->summa = $arrec['sumpay'.$i];
										if (insert_record('monit_form_rkp_f_pay', $rec_f))	{
								            notify(get_string('financeformadded','block_monitoring').'(payment)');
										} else {
											error(get_string('errorinaddingfinanceform','block_monitoring').'(payment)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
										}
									}
								}
							}
			            }
			            // exit();
			           redirect("$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka", get_string("changessaved"));
			        }
    			    else $mode = "new";
				}

		break;

		case 'edit':
					if ($frid > 0) 	{
					    $rec = get_record('monit_form_rkp_f', 'id', $frid);

			            if (isset($rec->concurs_open) && !empty($rec->concurs_open))  {
				            $rec->concurs_open = convert_date($rec->concurs_open, 'en', 'ru');
				        }
			            if (isset($rec->concurs_close) && !empty($rec->concurs_close))  {
				            $rec->concurs_close = convert_date($rec->concurs_close, 'en', 'ru');
				        }
			            if (isset($rec->contract_open) && !empty($rec->contract_open))  {
				            $rec->contract_open = convert_date($rec->contract_open, 'en', 'ru');
				        }
			            if (isset($rec->contract_close) && !empty($rec->contract_close))  {
				            $rec->contract_close = convert_date($rec->contract_close, 'en', 'ru');
				        }

				        if ($dirsums = get_records('monit_form_rkp_f_dir', 'rkp_f_id', $frid))	{
				        	$arrec = (array)$rec;
				        	$i=1;
				        	foreach ($dirsums as $ds)	{
				        		$arrec['dirnames'.$i] = $ds->directionid;
				        		$arrec['summa'.$i] = $ds->summa;
				        		$i++;

				        	}
				        	$rec=(object)$arrec;
				        }

				        if ($paysums = get_records('monit_form_rkp_f_pay', 'rkp_f_id', $frid))	{
				        	$arrec = (array)$rec;
				        	$i=1;
				        	foreach ($paysums as $ps)	{
				        		$arrec['numpay'.$i] = $ps->number;
				        		$arrec['datepay'.$i] = convert_date($ps->paydate, 'en', 'ru');
				        		$arrec['sumpay'.$i] = $ps->summa;
				        		$i++;
				        	}
				        	$rec=(object)$arrec;
				        }
					}

		break;

		case 'update':
				if ($rec = data_submitted())  {
				    // print_r($rec);

			        $errcount = find_finance_form_errors($rec, $err);
			        if ($errcount == 0)	{
			            if (isset($rec->concurs_open))	{
			            	if(!empty($rec->concurs_open))  {
					            $rec->concurs_open = convert_date($rec->concurs_open);
					        } else {
					        	unset($rec->concurs_open);
					        }
				        }

			            if (isset($rec->concurs_close))  {
			                if (!empty($rec->concurs_close))  {
					            $rec->concurs_close = convert_date($rec->concurs_close);
					        } else {
					        	unset($rec->concurs_close);
					        }
				        }

			            if (isset($rec->contract_open))   {
			                if (!empty($rec->contract_open))  {
					            $rec->contract_open = convert_date($rec->contract_open);
					        } else {
					        	unset($rec->contract_open);
					        }
				        }
			            if (isset($rec->contract_close))   {
			            	if(!empty($rec->contract_close))  {
					            $rec->contract_close = convert_date($rec->contract_close);
					        } else {
					        	unset($rec->contract_close);
					        }
				        }

						$rec->id = $frid;
						if (update_record('monit_form_rkp_f', $rec))	{
				            notify(get_string('financeformupdateded','block_monitoring').'(form)');
						} else {
							error(get_string('errorinupdatingfinanceform','block_monitoring').'(form)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
						}


			            if ($recs_fs = get_records('monit_form_rkp_f_dir', 'rkp_f_id', $frid))	{
	            			delete_records('monit_form_rkp_f_dir', 'rkp_f_id', $frid);
	            		}

			            $arrec = (array)$rec;
			            for ($i=1; $i<=3; $i++)	{
							if (isset($arrec['dirnames'.$i]) && !empty($arrec['dirnames'.$i]))  {
								$rec_f->rkp_f_id = $frid;
								$rec_f->directionid = $arrec['dirnames'.$i];
								if  (isset($arrec['summa'.$i]) && !empty($arrec['summa'.$i]))  {
									$rec_f->summa = $arrec['summa'.$i];
									if (insert_record('monit_form_rkp_f_dir', $rec_f))	{
							            notify(get_string('financeformadded','block_monitoring').'(direction)');
									} else {
										error(get_string('errorinaddingfinanceform','block_monitoring').'(direction)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
									}
								}
							}
			            }


			            if ($recs_fs = get_records('monit_form_rkp_f_pay', 'rkp_f_id', $frid))	{
	            			delete_records('monit_form_rkp_f_pay', 'rkp_f_id', $frid);
                        }

			            for ($i=1; $i<=3; $i++)	{
							if (isset($arrec['numpay'.$i]) && !empty($arrec['numpay'.$i]))  {
								$rec_f->rkp_f_id = $frid;
								$rec_f->number = $arrec['numpay'.$i];
								if  (isset($arrec['datepay'.$i]) && !empty($arrec['datepay'.$i]))  {
									$rec_f->paydate = convert_date($arrec['datepay'.$i]);
									if  (isset($arrec['sumpay'.$i]) && !empty($arrec['sumpay'.$i]))  {
										$rec_f->summa = $arrec['sumpay'.$i];
										if (insert_record('monit_form_rkp_f_pay', $rec_f))	{
								            notify(get_string('financeformadded','block_monitoring').'(payment)');
										} else {
											error(get_string('errorinaddingfinanceform','block_monitoring').'(payment)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
										}
									}
								}
							}
			            }

                        /*
			            $arrec = (array)$rec;

			            if ($recs_fs = get_records('monit_form_rkp_f_dir', 'rkp_f_id', $frid))	{
			            	$i=1;
				            foreach ($recs_fs as $rec_f)	{
								if (isset($arrec['dirnames'.$i]) && !empty($arrec['dirnames'.$i]))  {
									$rec_f->directionid = $arrec['dirnames'.$i];
									if  (isset($arrec['summa'.$i]) && !empty($arrec['summa'.$i]))  {
										$rec_f->summa = $arrec['summa'.$i];
										if (update_record('monit_form_rkp_f_dir', $rec_f))	{
								            notify(get_string('financeformupdateded','block_monitoring').'(direction)');
										} else {
											error(get_string('errorinupdatingfinanceform','block_monitoring').'(direction)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
										}
									}
								}
								$i++;
							}
			            }

			            if ($recs_fs = get_records('monit_form_rkp_f_pay', 'rkp_f_id', $frid))	{
			            	$i=1;
				            foreach ($recs_fs as $rec_f)	{
								if (isset($arrec['numpay'.$i]) && !empty($arrec['numpay'.$i]))  {
									$rec_f->rkp_f_id = $idnewf;
									$rec_f->number = $arrec['numpay'.$i];
									if  (isset($arrec['datepay'.$i]) && !empty($arrec['datepay'.$i]))  {
										$rec_f->paydate = convert_date($arrec['datepay'.$i]);
										if  (isset($arrec['sumpay'.$i]) && !empty($arrec['sumpay'.$i]))  {
											$rec_f->summa = $arrec['sumpay'.$i];
											if (update_record('monit_form_rkp_f_pay', $rec_f))	{
									            notify(get_string('financeformupdateded','block_monitoring').'(payment)');
											} else {
												error(get_string('errorinupdatingfinanceform','block_monitoring').'(payment)', "$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka");
											}
										}
									}
								}
								$i++;
							}
			            }
			            */
			            // exit();
			           redirect("$CFG->wwwroot/blocks/monitoring/finance/financereport.php?level=$levelmonit&amp;nm=$nm&amp;rid=$rid&amp;sid=$sid&amp;vkladka=$vkladka", get_string("changessaved"));
			        }
				}
		break;
	}




	// print_heading($strformname, "center", 4);

    print_simple_box_start("center");

/*
    print_string('levelmonit', 'block_monitoring');
    echo '&nbsp;';

    $levelmenu = array();
    $levemenu[0] = get_string('levelregion','block_monitoring');
    $levemenu[1] = get_string('levelrayon','block_monitoring');
    $levemenu[2] = get_string('levelschool','block_monitoring');

    popup_form("financehtmlform.php?lid=", $levemenu, 'switchlevel', $levelmonit, '', '', '', false);

    if ($levelmonit == 1)	{
 	   $rayonmenu = array();
	   $rayonmenu[0] = get_string('selectarayon', 'block_monitoring').'...';

	   if($allrayons = get_records_sql("SELECT * FROM {$CFG->prefix}monit_rayon ORDER BY number"))   {
	 	  foreach ($allrayons as $rayon) 	{
	       	$rayonmenu[$rayon->id] = $rayon->name;
	   	 }
	   }

	   echo '&nbsp;&nbsp;&nbsp;';
       popup_form("financehtmlform.php?lid=$levelmonit&amp;rid=", $rayonmenu, 'switchrayon', $rid, '', '', '', false);
    }
*/
?>
<form name="rkp_f" method="post" action="financehtmlform.php">
<input type="hidden" name="rid" value="<?php echo $rid ?>" />
<input type="hidden" name="sid" value="<?php echo $sid ?>" />
<input type="hidden" name="frid" value="<?php echo $frid ?>" />
<input type="hidden" name="rayonid" value="<?php echo $rid ?>" />
<input type="hidden" name="schoolid" value="<?php echo $sid ?>" />
<input type="hidden" name="level" value="<?php echo $levelmonit ?>" />
<input type="hidden" name="levelmonit" value="<?php echo $levelmonit ?>" />
<input type="hidden" name="finstatus" value="<?php echo $finstatus ?>" />
<input type="hidden" name="vkladka" value="<?php echo $vkladka ?>" />
<input type="hidden" name="nm" value="<?php echo $nm ?>" />
<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">

<?php
    if ($mode === 'new')  {
	    echo '<input type="hidden" name="mode" value="add" />';
	} else  {
	    echo '<input type="hidden" name="mode" value="update" />';
	}

    if (isset($rec->contract_type))  {
    	if ($rec->contract_type == 0) {
	    	$checked0 = 'CHECKED=true';
 		   	$checked1 = '';
    	}	else  {
	    	$checked0 = '';
 		   	$checked1 = 'CHECKED=true';
	    }
    } else {
    	if ($finstatus>=0 && $finstatus<=2) {
    		$checked0 = 'CHECKED=true';
	    	$checked1 = '';
	    } else 	{
 	 	  	$checked0 = '';
  	 	 	$checked1 = 'CHECKED=true';
	    }
	}
	echo "<INPUT type=radio $checked0 value=0 name=contract_type>". get_string('conkurs','block_monitoring');
	echo "&nbsp;&nbsp;&nbsp;<INPUT type=radio $checked1 value=1 name=contract_type>". get_string('dogovor','block_monitoring');

    print_heading(get_string('conkurs_lot','block_monitoring'), 'left', 4);

    print_string('predmetconkursa', 'block_monitoring');
	if (isset($err['concurs_name'])) formerr($err['concurs_name']);
    echo '<br><TEXTAREA name=concurs_name rows=3 cols=150>';
    if (isset($rec->concurs_name)) print $rec->concurs_name;
    echo '</TEXTAREA><p>';

    echo '<table border=0 width="100%"><tr><td>';
    print_string('directionconkurs', 'block_monitoring');
    echo '</td><td NOWRAP>';
    print_string('summatr', 'block_monitoring');
    echo '</td></tr>';

    $dirsmenu = array();
    $dirsmenu[0] = get_string('selectadirnames','block_monitoring').' ...';

    $directionames = get_records('monit_direction');
    foreach($directionames as $name)  {
    	 $dirsmenu[$name->id] = $name->name;
    }
    echo '<tr><td align=left>';
    if (isset($rec->dirnames1))  {
    	$recdn1 = $rec->dirnames1;
    } else {
    	$recdn1 = 0;
    }
   	if (isset($err['dirnames1'])) formerr($err['dirnames1']);
    choose_from_menu($dirsmenu, 'dirnames1', $recdn1, '');
//    echo '<br>';
    if (isset($rec->dirnames2))  {
    	$recdn2 = $rec->dirnames2;
    } else {
    	$recdn2 = 0;
    }
    choose_from_menu($dirsmenu, 'dirnames2', $recdn2, '');
//    echo '<br>';
    if (isset($rec->dirnames3))  {
    	$recdn3 = $rec->dirnames3;
    } else {
    	$recdn3 = 0;
    }
    choose_from_menu($dirsmenu, 'dirnames3', $recdn3, '');
    echo '</td><td align=left>';
    echo '<input type="text" name="summa1" size="10" maxlength="10" ';
    if (isset($err['summa1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->summa1) && $rec->summa1 > 0) p('value='.$rec->summa1);
    echo '><br><input type="text" name="summa2" size="10" maxlength="10" ';
    if (isset($err['summa2'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->summa2) && $rec->summa2 > 0) p('value='.$rec->summa2);
    echo '><br><input type="text" name="summa3" size="10" maxlength="10"';
    if (isset($err['summa3'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->summa3) && $rec->summa3 > 0) p('value='.$rec->summa3);
    echo '></tr></table><p>';


    echo  get_string('dateopen', 'block_monitoring') .'&nbsp;' . get_string('formatdate', 'block_monitoring').'&nbsp;';
    echo  '<INPUT maxLength=10 size=10 name=concurs_open ';
    if (isset($err['concurs_open'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->concurs_open)) p(' value='.$rec->concurs_open);
    echo '><p>';
    echo  get_string('dateclose', 'block_monitoring').'&nbsp;'. get_string('formatdate', 'block_monitoring').'&nbsp;';
    echo  '<INPUT maxLength=10 size=10 name=concurs_close ';
    if (isset($err['concurs_close'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->concurs_close)) p('value='.$rec->concurs_close);
    echo '><p>';

    print_string('linktodoc', 'block_monitoring');
    echo '<br><TEXTAREA name=concurs_link rows=3 cols=150>';
    if (isset($rec->concurs_link)) print $rec->concurs_link;
    echo '</TEXTAREA><p><hr>';

    print_heading(get_string('contract','block_monitoring'), 'left', 4);

    print_string('contractrecvizits', 'block_monitoring');

    echo '<br><TEXTAREA name=contract_name rows=3 cols=150>';
    if (isset($rec->contract_name)) print $rec->contract_name;
    echo '</TEXTAREA><p>';

    print_string('summatr', 'block_monitoring');
    echo '&nbsp;<input type="text" name="summacontract" size="10" maxlength="10"';
    if (isset($err['summacontract'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->summacontract) && $rec->summacontract > 0) p('value='.$rec->summacontract);
    echo '><p>';

    echo  get_string('datesigning', 'block_monitoring') .'&nbsp;' . get_string('formatdate', 'block_monitoring').'&nbsp;';
    echo  '<INPUT maxLength=10 size=10 name=contract_open';
    if (isset($err['contract_open'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->contract_open)) p(' value='.$rec->contract_open);
    echo '><p>';

    echo  get_string('dateexecution', 'block_monitoring').'&nbsp;'. get_string('formatdate', 'block_monitoring').'&nbsp;';
    echo  '<INPUT maxLength=10 size=10 name=contract_close';
    if (isset($err['contract_close'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->contract_close)) p(' value='.$rec->contract_close);
    echo '><p>';

    print_string('numberakts', 'block_monitoring');
    echo '<br><TEXTAREA name=contract_fin_docs rows=3 cols=150>';
    if (isset($rec->contract_fin_docs)) print $rec->contract_fin_docs;
    echo '</TEXTAREA><p>';

    print_string('payments', 'block_monitoring');
    echo '<table border=0"><tr><td align=center>';
    print_string('number', 'block_monitoring');
    echo '</td><td align=center>';
    print_string('date', 'block_monitoring');
    echo '</td><td align=center>';
    print_string('summa', 'block_monitoring');
    echo '</td></tr>';
    echo '<tr><td align=left>';
    echo '<input type="text" name="numpay1" size="10" maxlength="10"';
    if (isset($err['numpay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->numpay1) && $rec->numpay1 > 0) p('value='.$rec->numpay1);
    echo '><br><input type="text" name="numpay2" size="10" maxlength="10"';
    if (isset($err['numpay2'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->numpay2) && $rec->numpay2 > 0) p('value='.$rec->numpay2);
    echo '><br><input type="text" name="numpay3" size="10" maxlength="10"';
    if (isset($err['numpay3'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->numpay3) && $rec->numpay3 > 0) p('value='.$rec->numpay3);
    echo '></td><td>';
    echo '<input type="text" name="datepay1" size="10" maxlength="10"';
    if (isset($err['datepay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->datepay1)) p(' value='.$rec->datepay1);
    echo '><br><input type="text" name="datepay2" size="10" maxlength="10"';
    if (isset($err['datepay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->datepay2)) p(' value='.$rec->datepay2);
    echo '><br><input type="text" name="datepay3" size="10" maxlength="10"';
    if (isset($err['datepay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->datepay2)) p(' value='.$rec->datepay2);
    echo '></td><td>';
    echo '<input type="text" name="sumpay1" size="10" maxlength="10"';
    if (isset($err['sumpay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->sumpay1) && $rec->sumpay1 > 0) p('value='.$rec->sumpay1);
    echo '><br><input type="text" name="sumpay2" size="10" maxlength="10"';
    if (isset($err['sumpay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->sumpay2) && $rec->sumpay2 > 0) p('value='.$rec->sumpay2);
    echo '><br><input type="text" name="sumpay3" size="10" maxlength="10"';
    if (isset($err['sumpay1'])) echo 'style="border-color:#FF0000"';
    if (isset($rec->sumpay2) && $rec->sumpay2 > 0) p('value='.$rec->sumpay2);
    echo '></td></tr></table><p>';

    $statusmenu = array();
    for($i=0; $i<=6; $i++)  {
    	 $statusmenu[$i] = get_string('finstatus'.$i, 'block_monitoring');;
    }
    if (isset($rec->finstatus))  $finstatus = $rec->finstatus;
    choose_from_menu($statusmenu, 'statusmenu', $finstatus);

	echo '<p><input type="submit" value="'. get_string('savechanges'). '">';

    echo '</form>';

    $options = array();
    $options['rid'] = $rid;
    $options['sid'] = $sid;
    $options['level'] = $levelmonit;
   	$options['sesskey'] = $USER->sesskey;
    print_single_button("financereport.php", $options, get_string("revert"));

  	print_simple_box_end();

    print_footer();



// Find input error in forms
function find_finance_form_errors(&$rec1, &$err)
{
    $rec = (array)$rec1;
    $numericfield = array('summa1', 'summa2', 'summa3', 'sumpay1', 'sumpay2', 'sumpay3', 'summacontract');
    $datefield = array('concurs_open', 'concurs_close', 'contract_open', 'contract_close', 'datepay1', 'datepay2', 'datepay3');

    foreach ($numericfield as $field)  {
	    if (isset($rec[$field]) && !empty($rec[$field]))	{
	   		if (!is_numeric($rec[$field])) {
 	      		$err[$field] = 0;
  	     	}
   		 }
   	}

    foreach ($datefield as $field)  {
	    if (isset($rec[$field]) && !empty($rec[$field]))	{
	   		if (!is_date($rec[$field])) {
 	      		$err[$field] = 0;
  	     	}
   		 }
   	}

    if (empty($rec['concurs_name'])) {
         $err['concurs_name'] = get_string("missingname");
	}

    if (empty($rec['dirnames1'])) {
         $err['dirnames1'] = get_string("missingname");
	}

    if (empty($rec['summa1'])) {
         $err['summa1'] = 0;
	}

   return count($err);
}



?>


