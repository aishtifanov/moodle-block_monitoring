<?php // $Id: forms.php,v 1.3 2009/02/25 08:23:49 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
	$nm = required_param('nm', PARAM_INT);         // Month
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $rzd = required_param('rzd', PARAM_INT);       // Razd id
    $reported = required_param('reported', PARAM_INT);       // Report 0,1
    $first = optional_param('first', 0);       // Rayon id
    $break = get_string('break','block_monitoring');

	$frm = data_submitted(); /// load up any submitted data
	/*
    $years = years();
    $year = date("Y") - 1;
    $year1 = date("Y");
   */
    $oyears = get_record('monit_years', 'id', $yid);
    $years = $oyears->name;
    $ayears = explode("/", $years);
	$year = $ayears[0];
	$year1 = $ayears[1];

	if($frm)  {		$levelmonit = $frm->levelmonit;
		if($frm->break == $break) { $redirect = true; } else { $redirect = false; }
	}

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('college', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $school = get_record('monit_college', 'id', $sid);
    $rzd_name = get_record('monit_razdel', 'id', $rzd);
	$strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('college', 'block_monitoring');
    $strschools = get_string('colleges', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strrazdel = get_string('namerazdel','block_monitoring');
	// $strlevel = get_string($levelmonit, 'block_monitoring');
	$nameofpokazatel = get_string('nameofpokazatel','block_monitoring');
	$valueofpokazatel = get_string('valueofpokazatel','block_monitoring');
    $no = get_string('no');
    $yes = get_string('yes');
	$strformname = get_record_sql("select name from {$CFG->prefix}monit_form where id=$fid");
	$strformname= $strformname->name;


    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";

		    $yeareport = get_string('yeareport', 'block_monitoring');
			$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/college/colleges.php?rid=$rid\">$strschools</a>";
			$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/college/listforms.php?rid=$rid&sid=$sid&nm=$nm&yid=$yid\">$school->name</a>";
			$breadcrumbs .= " -> $rzd_name->name";

//	$breadcrumbs .= " -> $strformname";

    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);
    $cerr = 0;
    if ($frm)  {		$cerr=find_form_errors($frm, $err, "monit_college_table_$rzd");    }

    if(!$frm || $cerr!=0)  {
//		$strrazdname = get_record_sql("select name from {$CFG->prefix}monit_razdel where id=$rzd");
		$strrazdname = str_replace('$year1', $year1, $rzd_name->name);
		$strrazdname = str_replace('$year', $year, $rzd_name->name);
		print "<center><b>$strrazdel</b><br>$strrazdname</center><br>";
		$bkp->datemodified = get_date_from_month_year($nm, $yid);
		$bkp->rayonid = $rid;
		$bkp->shortname = $rzd;
		$bkp->status = 1;
		if($sid != 0)  {
			$bkp->collegeid = $sid;
			$table_verify = get_record_sql("select id, status from {$CFG->prefix}monit_college_listforms where (collegeid=$sid) and (datemodified=$bkp->datemodified) and (shortname=$rzd) and (rayonid=$rid)");

			if(!$table_verify){				$listformid = insert_record("monit_college_listforms", $bkp);
				$table_verify = get_record_sql("select id, status from {$CFG->prefix}monit_college_listforms where (collegeid=$sid) and (datemodified=$bkp->datemodified) and (shortname=$rzd)");
			}  else  {				$listformid = $table_verify->id;			}
			$status = $table_verify->status;
		}  else  {
			$table_verify = get_record_sql("select * from {$CFG->prefix}monit_rayon_listforms where (rayonid=$rid) and (datemodified=$bkp->datemodified) and (shortname=$rzd)");
			if(!$table_verify){
				$listformid = insert_record("monit_rayon_listforms", $bkp);
			} else  {				$listformid = $table_verify->id;			}
		}

		$table_verify = get_record_sql("select id from {$CFG->prefix}monit_college_table_$rzd where listformid=$listformid");

		if(!$table_verify){
			$tbl->listformid = $listformid;
			insert_record('monit_college_table_'.$rzd, $tbl);
 		}

        $type_education = get_record_sql("select typeinstitution from {$CFG->prefix}monit_college where id=$sid");
		$table_fields = get_records_sql("SELECT * FROM {$CFG->prefix}monit_razdel_field WHERE razdelid=$rzd");
		$sqls_off = get_records_sql("SELECT idfield FROM {$CFG->prefix}monit_options WHERE idtypeschool=$type_education->typeinstitution");

		foreach ($table_fields as $sql) {
			$firsts[$sql->id] = $sql->name_field;
		}
		$result = $firsts;
		if ($sqls_off)	{
			foreach ($sqls_off as $sql) {
				$seconds[$sql->idfield] = $firsts[$sql->idfield];
			}
			$result = array_diff ($firsts, $seconds);
			if(array_count_values($result) == 0) {
				$result = $firsts;
			}
		}

		$table->head  = array(get_string('symbolnumber', 'block_monitoring'), "<center>$nameofpokazatel</center>", $valueofpokazatel);
		$table->align = array("center", "left", "center");
		$table->width = '100%';
//		$table->size = array ('5%', '70%', '10%');
		$table->class = 'moutable';

        $datas = array();
   		$n = 1;
		foreach ($table_fields as $table_field) {			$table_field = (array)$table_field;
			if ($admin_is) {				$title = get_string('editfield', 'block_monitoring');
				$link = 'changrazd.php?id='.$table_field['id']."&amp;sid=$sid&amp;rzd=$rzd&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;rid=$rid&amp;level=$levelmonit";
				$strlinkupdate = "      <a title=\"$title\" href=\"$link\">";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/t/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				$datas[$table_field['name_field']] = $table_field['name'].$strlinkupdate;
				if($result[$table_field['id']]) {
					$datas_no[$table_field['name_field']] = $table_field['name'];
				}
			}  else  {				if($result[$table_field['id']]) {					$datas[$table_field['name_field']] = $table_field['name'];
				}			}
		}


		$tbl_datas = get_record_sql("select * from {$CFG->prefix}monit_college_table_$rzd where listformid=$listformid");

		$tbl_datas = (array)$tbl_datas;
		$metacolumns = $db->MetaColumns($CFG->prefix."monit_college_table_$rzd");
		foreach ($metacolumns as $metacolumn) {
			$metacolumn = (array)$metacolumn;
			if(isset($datas[$metacolumn['name']])) {				if(isset($datas_no[$metacolumn['name']])) {					$hide_s = '';
					$hide_e = '';
				} else {					$hide_s = '<i><font color="#FF0000">';
					$hide_e = '</font></i>';
				}
				if ($metacolumn['name'] != 'id' &&  $metacolumn['name'] != 'listformid')  {
					if(!$frm)  {						if($datas[$metacolumn['name']]) {							$s = $tbl_datas[$metacolumn['name']];
						}
					}  else  {
						$frm = (array)$frm;
						if($datas[$metacolumn['name']]) {							$s = $frm[$metacolumn['name']];
						}					}
					if(($s=='0')&&($s==0))  {
						$s = '';
					}


					$name = str_replace('$years', $years, $datas[$metacolumn['name']]);
					$name = str_replace('$year1', $year1, $name);
					$name = str_replace('$year', $year, $name);

					if($metacolumn['type'] != 'char')  {						if (isset($err[$metacolumn['name']])){							$bc = 'style="border-color:#FF0000"';
						}  else  {							$bc = '';						}

						if($metacolumn['type'] == 'int')  {
							if($status == 1 && $metacolumn['max_length'] == '15') {								$table->data[] = array($hide_s.translitfield($metacolumn['name'], 2).$hide_e, '<b>'.$name.'<b>', "<input $bc type='hidden' name=".$metacolumn['name'].' size="100" value="'.$s.'">');
							} else {								if($metacolumn['max_length'] == '15') {									$table->data[] = array($hide_s.translitfield($metacolumn['name'], 2).$hide_e, '<b>'.$name.'<b>', "<input style='background-color:#00FF00' readonly='true' $bc type='text' name=".$metacolumn['name'].' size="100" value="'.$s.'">');
								} else {									$table->data[] = array($hide_s.translitfield($metacolumn['name'], 2).$hide_e, $name, "<input $bc type='text' name=".$metacolumn['name'].' size="100" value="'.$s.'">');
								}
							}
						}  else  {							if($metacolumn['type'] == 'tinyint')  {								if($s == 1)  { $check0 = 'checked'; }  else  { $check0 = ''; }								$table->data[] = array(translitfield($metacolumn['name'], 2), $name, "<input $bc type='checkbox' $check0 name=".$metacolumn['name'].' value="'.$s.'">');
							}  else  {								if($status == 1 && $metacolumn['max_length'] == '15') {
									$table->data[] = array(translitfield($metacolumn['name'], 2), '<b>'.$name.'<b>', "<input $bc type='hidden' name=".$metacolumn['name'].' size="100" value="'.$s.'">');
								} else {
									if($metacolumn['max_length'] == '15') {
										$table->data[] = array(translitfield($metacolumn['name'], 2), '<b>'.$name.'<b>', "<input style='background-color:#00FF00' readonly='true' $bc type='text' name=".$metacolumn['name'].' size="10" value="'.$s.'">');
									} else {
										$table->data[] = array(translitfield($metacolumn['name'], 2), $name, "<input $bc type='text' name=".$metacolumn['name'].' size="100" value="'.$s.'">');
									}
								}

							}
						}
					}  else  {						$check0=$check1=$check2='';						switch ($s)  {
						 case '':	$check0 = 'selected';
						 			break;
						 case $no:	$check1 = 'selected';
						 			break;
						 case $yes:	$check2 = 'selected';
					 				break;
						}						$chbox = '<select name='.$metacolumn['name']."><option value=0 $check0>-</option><option value=1 $check1>".get_string('no')."</option><option value=2 $check2>".get_string('yes').'</option></select>';
						$table->data[] = array(translitfield($metacolumn['name'], 2), $name, $chbox);
					}
//					$n++;
				}
			}
		}

		print_simple_box_start("center", '100%');

		echo "<form name='form_bkp' method='post' action='forms.php'>";
		print_color_table($table);
		include("button.html");

		print_simple_box_end();
    	print_footer();

    } else  {		$datemodified = get_date_from_month_year($nm, $yid);
    	if($reported!=1) {	    	if($cerr==0)  {	 			$frm = (array)$frm;
				if($sid != 0)  {
					$sql = "update {$CFG->prefix}monit_college_listforms set status=2 where (collegeid=$sid) and (datemodified=$datemodified) and (shortname=$rzd)";
					execute_sql($sql, false);
				}  else  {
					$sql = "update {$CFG->prefix}monit_rayon_listforms set status=2 where (rayonid=$rid) and (datemodified=$datemodified) and (shortname=$rzd)";
					execute_sql($sql, false);
				}

				$table_fields = get_records_sql("select name_field, calcfunc from {$CFG->prefix}monit_razdel_field where razdelid=$rzd");

				$sql = '';
				$field = '';
				$value = '';
				$metacolumns = $db->MetaColumns("$CFG->prefix"."monit_college_table_$rzd");
				foreach ($metacolumns as $metacolumn) {
					$metacolumn = (array)$metacolumn;
					$data[$metacolumn['name']] = $metacolumn['type'];
					$dat[$metacolumn['name']] = $metacolumn['max_lenght'];
				}

				foreach ($table_fields as $table_field) {					if(trim($table_field->calcfunc) != '') {   	                $frm[$table_field->name_field] = calcfield($frm, $table_field->calcfunc);
					}				}

 	           $sql = '';
				foreach ($table_fields as $table_field) {					$field=$table_field->name_field;
					if(($data[$field] == 'int')||($data[$field] == 'double'))  {						if($frm[$field] == '')  {							$value='0,';						}  else  {
							$value=$frm[$field].',';
						}
					}  else  {						if($data[$field] == 'char')  {							if($frm[$field] == 1) {
								$value="'$no',";
							} else {								if($frm[$field] == 2) {
									$value="'$yes',";
								} else {
									$value="'-',";
								}
							}						} else {							if($data[$field] == 'tinyint')  {								if(isset($frm[$field])) {									$value='1,';								}  else  {									$value='0,';								}
							}  else {								if($frm[$field] == '')  {
									$value="'',";
								}  else  {
									$value="'".$frm[$field]."',";
								}
							}
						}
					}
					if(($field != '') && ($value != '')) {
						$sql.="`$field`=".$value;
					}
				}
				$sql = substr($sql, 0, strlen($sql) - 1);

				$sql = "update {$CFG->prefix}monit_college_table_$rzd set ".$sql." where listformid=$frm[listformid]";

				execute_sql($sql, false);
			}
		}  else  {			fillalldata($fid, $frm->listformid, $rid);
 			$frm = (array)$frm;		}

		if(!$redirect) {
			redirect("$CFG->wwwroot/blocks/monitoring/college/forms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;level=$frm[levelmonit]&amp;rzd=$rzd&amp;fid=$fid&amp;reported=$reported", get_string('succesavedata', 'block_monitoring'));
		} else {
			redirect("$CFG->wwwroot/blocks/monitoring/college/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;level=$frm[levelmonit]&amp;reported=$reported", get_string('succesavedata', 'block_monitoring'));
		}
    }


function calcfield($data, $field)
{	$result = 0;
    $listfield = explode(',', $field);
	for($i=0; $i < count($listfield); $i++) {		$s = trim($listfield[$i]);
		if($s[0] == '-') {			$s = substr($s, 1);
			$result = $result - $data[$s];
		} else {			$result = $result + $data[$s];
		}
	}
	return $result;}

?>