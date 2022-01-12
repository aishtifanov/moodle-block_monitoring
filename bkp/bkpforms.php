<?php // $Id: bkpforms.php,v 1.18 2008/09/12 12:06:44 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
	$nm = required_param('nm', PARAM_INT);         // Month
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $rzd = required_param('rzd', PARAM_INT);       // Razd id
    $currenttab = optional_param('id', '1', PARAM_INT);       // Tab id;
    $reported = required_param('reported', PARAM_INT);       // Report 0,1
  	$levelmonit = optional_param('level', 'region');       // Level
//    $first = optional_param('first', 0);       // Rayon id
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

	if($frm)  {
		if($frm->break == $break) { $redirect = true; }else { $redirect = false; }
	}

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

    $school = get_record('monit_school', 'id', $sid);
	$strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strrazdel = get_string('namerazdel','block_monitoring');
	$strlevel = get_string($levelmonit, 'block_monitoring');
	$nameofpokazatel = get_string('nameofpokazatel','block_monitoring');
	$valueofpokazatel = get_string('valueofpokazatel','block_monitoring');
    $no = get_string('no');
    $yes = get_string('yes');
	$strformname = get_record_sql("select name from {$CFG->prefix}monit_form where id=$fid");
	$strformname= $strformname->name;

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	switch ($levelmonit)	{
		case 'region':
					    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
						$breadcrumbs .= " -> $strlevel";
		break;

		case 'rayon':
					    $yeareport = get_string('yeareport', 'block_monitoring');
						$rayon = get_record('monit_rayon', 'id', $rid);
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=rayon&rid=1&sid=0&nm=$nm&yid=$yid&id=1\">$rayon->name</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=rayon&rid=1&sid=0&nm=$nm&yid=$yid&id=1\">$yeareport</a>";
	  	break;
		case 'school':
					    $yeareport = get_string('yeareport', 'block_monitoring');
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=school&rid=$rid&sid=$sid&fid=$fid&nm=$nm&yid=$yid&id=$currenttab\">$school->name</a>";
						$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=school&rid=$rid&sid=$sid&fid=$fid&nm=$nm&yid=$yid&id=$currenttab\">$yeareport</a>";
	  	break;
  	}

	$breadcrumbs .= " -> $strformname";

    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);
    $cerr = 0;
    if ($frm)  {

    if(!$frm || $cerr!=0)  {
		$strrazdname = get_record_sql("select name from {$CFG->prefix}monit_razdel where id=$rzd");
		$str = str_replace('$year1', $year1, $strrazdname->name);
		$str = str_replace('$year', $year, $str);
		print "<center><b>$strrazdel</b><br>$str</center><br>";
		$bkp->datemodified = get_date_from_month_year($nm, $yid);
		$bkp->rayonid = $rid;
		$bkp->shortname = $rzd;
		$bkp->status = 1;
		if($sid != 0)  {
			$bkp->schoolid = $sid;
			$table_verify = get_record_sql("select id, status from {$CFG->prefix}monit_school_listforms where (schoolid=$sid) and (datemodified=$bkp->datemodified) and (shortname=$rzd)");
//print_r($table_verify);
			if(!$table_verify){
				$table_verify = get_record_sql("select id, status from {$CFG->prefix}monit_school_listforms where (schoolid=$sid) and (datemodified=$bkp->datemodified) and (shortname=$rzd)");
			}  else  {
			$status = $table_verify->status;
		}  else  {
			$table_verify = get_record_sql("select * from {$CFG->prefix}monit_rayon_listforms where (rayonid=$rid) and (datemodified=$bkp->datemodified) and (shortname=$rzd)");
			if(!$table_verify) {
				$listformid = insert_record("monit_rayon_listforms", $bkp);
			} else  {
			$status = $table_verify->status;
		}

		$table_verify = get_record_sql("select id from {$CFG->prefix}monit_bkp_table_$rzd where listformid=$listformid");

		if(!$table_verify){
			$tbl->listformid = $listformid;
			insert_record('monit_bkp_table_'.$rzd, $tbl);
 		}

		$table_fields = get_records_sql("SELECT * FROM {$CFG->prefix}monit_razdel_field WHERE razdelid=$rzd");

		foreach ($table_fields as $sql) {
			$firsts[$sql->id] = $sql->name_field;
		}

        if($sid != 0) {
	        $type_education = get_record_sql("select stateinstitution from {$CFG->prefix}monit_school where id=$sid");
			$sqls_off = get_records_sql("SELECT idfield FROM {$CFG->prefix}monit_options WHERE idtypeschool=$type_education->stateinstitution");

			foreach ($sqls_off as $sql) {
				if(isset($firsts[$sql->idfield])) {
					$seconds[$sql->idfield] = $firsts[$sql->idfield];
				} else {
					$seconds[$sql->idfield] = '';
				}
			}
			$result = array_diff($firsts, $seconds);
		}

		$table->head  = array(get_string('symbolnumber', 'block_monitoring'), "<center>$nameofpokazatel</center>", $valueofpokazatel);
		$table->align = array("center", "left", "center");
		$table->width = '100%';
//		$table->size = array ('5%', '70%', '10%');
		$table->class = 'moutable';

        $datas = array();
   		$n = 1;

		foreach ($table_fields as $table_field) {
			if ($admin_is || $region_operator_is) {
				$link = 'changrazd.php?id='.$table_field['id']."&amp;sid=$sid&amp;rzd=$rzd&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;rid=$rid&amp;level=$levelmonit";
				$strlinkupdate = "      <a title=\"$title\" href=\"$link\">";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/t/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				$datas[$table_field['name_field']] = $table_field['name'].$strlinkupdate;
				if(isset($result[$table_field['id']])) {
					if($result[$table_field['id']]) {
						$datas_no[$table_field['name_field']] = $table_field['name'];
					}
				}
			}  else  {
				$link = 'changrazd.php?id='.$table_field['id']."&amp;sid=$sid&amp;rzd=$rzd&amp;fid=$fid&amp;nm=$nm&amp;yid=$yid&amp;rid=$rid&amp;level=$levelmonit";
//				$strlinkupdate = "      <a title=\"$title\" href=\"$link\">";
//				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/t/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				$datas[$table_field['name_field']] = $table_field['name'].$strlinkupdate;
				if(isset($result[$table_field['id']])) {
					if($result[$table_field['id']]) {
						$datas_no[$table_field['name_field']] = $table_field['name'];
					}
				}
			}
		}

		$tbl_datas = get_record_sql("select * from {$CFG->prefix}monit_bkp_table_$rzd where listformid=$listformid");

		$tbl_datas = (array)$tbl_datas;
		$metacolumns = $db->MetaColumns($CFG->prefix."monit_bkp_table_$rzd");

		foreach ($metacolumns as $metacolumn) {
			$metacolumn = (array)$metacolumn;

			if(isset($datas[$metacolumn['name']])) {
				$hide_e = '';
				if(isset($datas_no[$metacolumn['name']])) {
						$hide_e = '';
					} else {
						$hide_e = '</font></i>';
					}
				}
				if ($metacolumn['name'] != 'id' &&  $metacolumn['name'] != 'listformid')  {
					if(!$frm)  {
						}
					}  else  {
						$frm = (array)$frm;
						if($datas[$metacolumn['name']]) {
						}
					if(($s=='0')&&($s==0))  {
						$s = '';
					}

					$name = str_replace('$years', $years, $datas[$metacolumn['name']]);
					$name = str_replace('$year1', $year1, $name);
					$name = str_replace('$year', $year, $name);
//print_r($metacolumn);
					if($metacolumn['type'] !='char')  {
						}  else  {

						if($metacolumn['type'] == 'int')  {
							if($status == 1 && $metacolumn['max_length'] == '15') {
							} else {
								} else {
								}
							}
						}  else  {
							}  else  {
									$table->data[] = array(translitfield($metacolumn['name'], 2), '<b>'.$name.'<b>', "<input $bc type='hidden' name=".$metacolumn['name'].' size="10" value="'.$s.'">');
								} else {
									if($metacolumn['max_length'] == '15') {
										$table->data[] = array(translitfield($metacolumn['name'], 2), '<b>'.$name.'<b>', "<input style='background-color:#00FF00' readonly='true' $bc type='text' name=".$metacolumn['name'].' size="10" value="'.$s.'">');
									} else {
										$table->data[] = array(translitfield($metacolumn['name'], 2), $name, "<input $bc type='text' name=".$metacolumn['name'].' size="10" value="'.$s.'">');
									}
								}
							}
						}
					}  else  {
						 case '':	$check0 = 'selected';
						 			break;
						 case $no:	$check1 = 'selected';
						 			break;
						 case $yes:	$check2 = 'selected';
					 				break;
						}
						$table->data[] = array(translitfield($metacolumn['name'], 2), $name, $chbox);
					}
//					$n++;
				}
			}
		}

		print_simple_box_start("center", '100%');

		echo "<form name='form_bkp' method='post' action='bkpforms.php'>";
		print_color_table($table);
		include("button.html");

		print_simple_box_end();
    	print_footer();

    } else  {
    	if($reported!=1) {
				if($sid != 0)  {
					$sql = "update {$CFG->prefix}monit_school_listforms set status=2 where (schoolid=$sid) and (datemodified=$datemodified) and (shortname=$rzd)";
					execute_sql($sql, false);
				}  else  {
					$sql = "update {$CFG->prefix}monit_rayon_listforms set status=2 where (rayonid=$rid) and (datemodified=$datemodified) and (shortname=$rzd)";
					execute_sql($sql, false);
				}

				$table_fields = get_records_sql("select name_field, calcfunc from {$CFG->prefix}monit_razdel_field where razdelid=$rzd");

				$sql = '';
				$field = '';
				$value = '';
				$metacolumns = $db->MetaColumns("$CFG->prefix"."monit_bkp_table_$rzd");
				foreach ($metacolumns as $metacolumn) {
					$metacolumn = (array)$metacolumn;
					$data[$metacolumn['name']] = $metacolumn['type'];
					if(isset($metacolumn['max_lenght'])) {
						$dat[$metacolumn['name']] = $metacolumn['max_lenght'];
					}
				}

				foreach ($table_fields as $table_field) {
						$pos=strpos($calcfunc, 'd-11');
						if($pos===false) {
							$calcfunc = substr($calcfunc, $pos+1, strlen($calcfunc));
							$temp = explode("~", $calcfunc);
							if(isset($temp)) {
								$sqls = get_records_sql("select id from {$CFG->prefix}monit_razdel where formid=$fid");
								foreach ($sqls as $sql) {
								for($i = 1; $i < count($temp); $i++) {
									$fields = '`'.str_replace(',', '`,`', $temp[$i]).'`';
									$sql = get_record_sql("select $fields from {$CFG->prefix}monit_bkp_table_$idrazdel[$i] where listformid=$sql->id");
									$sql = (array)$sql;
									$fields = explode(",", $temp[$i]);
									for($j = 0; $j < count($fields); $j++) {
								}
							}
			                unset($temp);
						}

						$calcfunc = str_replace('~', ',', $calcfunc);

						$frm[$table_field->name_field] = calcfield($frm, $calcfunc);

 	           $sql = '';
				foreach ($table_fields as $table_field) {
					if(($data[$field] == 'int')||($data[$field] == 'double'))  {
							$value=$frm[$field].',';
						}
					}  else  {
								$value="'$no',";
							} else {
									$value="'$yes',";
								} else {
									$value="'-',";
								}
							}
							}  else {
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

				$sql = "update {$CFG->prefix}monit_bkp_table_$rzd set ".$sql." where listformid=$frm[listformid]";

				execute_sql($sql, false);
			}
		}  else  {
 			$frm = (array)$frm;

		if(!$redirect) {
		} else {
			redirect("$CFG->wwwroot/blocks/monitoring/bkp/bkpmain.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;level=$frm[levelmonit]&amp;id=$currenttab&amp;fid=$fid", get_string('succesavedata', 'block_monitoring'));
		}
	}

function calcfield($data, $field)
{
    $listfield = explode(',', $field);
	for($i=0; $i < count($listfield); $i++) {
		if($s[0] == '-') {
			$result = $result - $data[$s];
		} else {
		}
	}
	return $result;

?>