<?php // $Id: options.php,v 1.4 2008/09/12 11:02:31 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../../course/lib.php');
    require_once('../lib.php');
	require_once($CFG->libdir.'/tablelib.php');


    $currenttab = optional_param('ct', 1);          // Current tab
    $idf = optional_param('idf', 0);          // id form
    $idr = optional_param('idr', 0);          // id razdel
    $id_ts = optional_param('id_ts', 0);          // id type school

	require_login();
	$frm = data_submitted(); /// load up any submitted data

    $years =  current_edu_year();
    $year = date("Y");

    if($frm) {        $id = explode(',', $frm->id_useron);

		for($i=0; $i < count($id); $i++) {			if($id[$i] != '') {				$bkpstatus = get_record_sql("select id from {$CFG->prefix}monit_options where (idtypeschool=$id_ts) and (idfield=$id[$i])");
				if(!$bkpstatus) {                   $ins->idtypeschool = $id_ts;
                   $ins->idfield = $id[$i];
                   insert_record("monit_options", $ins);
				}			}
		}

        $id = explode(',', $frm->id_useroff);
		for($i=0; $i < count($id); $i++) {
			if($id[$i] != '') {
				delete_records('monit_options', 'idtypeschool', $id_ts, 'idfield', $id[$i]);
			}
		}
//		$type_schools = get_records_sql("select id, name from {$CFG->prefix}monit_school_type");
		$type_schools = get_records_sql("select id, name from {$CFG->prefix}monit_school_category where visible=1");
    }

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');

	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}
	$options = get_string('options', 'block_monitoring');
    $optionsrzd = get_string('optionsrzd', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/frontpage.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $options";
    print_header_mou("$site->shortname: $options", $site->fullname, $breadcrumbs);

	print_simple_box_start("center", "100%");

    include('taboptionsbkp.php');

	switch ($currenttab) {
		case 1:
			echo "<script type='text/javascript'>\n<!-- Begin\n";
			echo "function usersAdd(source, destination) {\n";
			echo " if(source.options.selectedIndex != -1) {\n";
			echo "  opt = document.createElement('OPTION');\n";
			echo "  destination.add(opt);\n";
			echo "  opt.innerText=source.options(source.options.selectedIndex).text;\n";
			echo "  opt.value=source.options(source.options.selectedIndex).value;\n";
			echo "  source.remove(source.options.selectedIndex);\n";
			echo " }\n";
			echo "}\n";

			echo "function usersAddAll(source, destination) {\n";
			echo " for (i=0;i<source.options.length;i++) {\n";
			echo "  opt = document.createElement('OPTION');\n";
			echo "  destination.add(opt);\n";
			echo "  opt.innerText=source.options(i).text;\n";
			echo "  opt.value=source.options(i).value;\n";
			echo " }\n";
			echo " i = source.options.length;\n";
			echo " while(i!=0) {\n";
			echo "  source.remove(0);\n";
			echo "  i--;\n";
			echo " }\n";
			echo "}\n";

			echo "function usersDelAll(source, destination) {\n";
			echo " for (i=0;i<destination.options.length;i++) {\n";
			echo "  opt = document.createElement('OPTION');\n";
			echo "  source.add(opt);\n";
			echo "  opt.innerText=destination.options(i).text;\n";
			echo "  opt.value=destination.options(i).value;\n";
			echo " }\n";
			echo " i = destination.options.length;\n";
			echo " while(i!=0) {\n";
			echo "  destination.remove(0);\n";
			echo "  i--;\n";
			echo " }\n";
			echo "}\n";

			echo "function usersDel(source, destination) {\n";
			echo " if(destination.options.selectedIndex != -1) {\n";
			echo "  opt = document.createElement('OPTION');\n";
			echo "  source.add(opt);\n";
			echo "  opt.innerText=destination.options(destination.options.selectedIndex).text;\n";
			echo "  opt.value=destination.options(destination.options.selectedIndex).value;\n";
			echo "  destination.remove(destination.options.selectedIndex);\n";
			echo " }\n";
			echo "}\n";

			echo "function post(edit, destination, edit1, source) {\n";
			echo " val = ''\n";
			echo " for (i=0;i<destination.options.length;i++) {\n";
			echo "  val = val + destination.options(i).value + ',';\n";
			echo " }\n";
			echo " edit.value = val;\n";
			echo " val = ''\n";
			echo " for (i=0;i<source.options.length;i++) {\n";
			echo "  val = val + source.options(i).value + ',';\n";
			echo " }\n";
			echo " edit1.value = val;\n";
			echo "}\n";

			echo "\n-->\n</script>\n";
			echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
			echo '<tr> <td align=right>';
			print_string('forms', 'block_monitoring');
			echo '</td><td>';
			$sqls = get_records_sql("select id, name from {$CFG->prefix}monit_form");

			$list_forms = array();
			$list_forms[0] = get_string('selectform', 'block_monitoring'). ' ...';
			foreach ($sqls as $sql) {
				$list_forms[$sql->id] = $sql->name;
			}

			$stradres = "options.php?id_ts=$id_ts&amp;idf=";
				$stradres = "options.php?idf=";

			popup_form("$stradres", $list_forms, "formstats_0", $idf, "", "", "", false);


			echo '</td></tr>';
			echo '<tr><td align=right>';
			print_string('typeou', 'block_monitoring');
			echo '</td><td>';
            $type_schools = get_records_sql("select id, name from {$CFG->prefix}monit_school_category where visible=1");
//		$type_schools = get_records_sql("select id, name from {$CFG->prefix}monit_school_category");
			$list_type = array();
			$list_type[0] = get_string('selecttypeschool', 'block_monitoring'). ' ...';

			foreach ($type_schools as $type_school) {
				$list_type[$type_school->id] = $type_school->name;
			}

			$stradres = "options.php?idf=$idf&amp;ct=$currenttab&amp;id_ts=";
			popup_form($stradres, $list_type, "formstats_1", $id_ts, "", "", "", false);

			echo '</td></tr>';
			if(!$idf) {				echo '</table>';
			} else {				echo '<tr><td align=right>';					print_string('rzd', 'block_monitoring');
				echo '</td><td>';
				$sqls = get_records_sql("select id, name from {$CFG->prefix}monit_razdel where formid=$idf");

				$list_forms = array();
				$list_forms[0] = get_string('selectrzd', 'block_monitoring'). ' ...';
				foreach ($sqls as $sql) {					$sql->name = str_replace('$years', $years, $sql->name);
					$sql->name = str_replace('$year', $year, $sql->name);

					if (strlen($sql->name) > 160) {
						$list_forms[$sql->id] = substr ($sql->name, 0, 160) . ' ...';
					} else {						$list_forms[$sql->id] = $sql->name;
					}
				}


				$stradres = "options.php?id_ts=$id_ts&amp;idf=$idf&amp;idr=";

				popup_form($stradres, $list_forms, "formstats_2", $idr, "", "", "", false);

				echo '</td></tr></table>';
			}

			if(($idr)&&($id_ts)) {				$listfield = get_string('listfield', 'block_monitoring');
				$listfieldon = get_string('listfieldon', 'block_monitoring');
				$listfieldoff = get_string('listfieldoff', 'block_monitoring');				echo "<form name='form_options' method='post' action='options.php?id_ts=$id_ts&amp;idf=$idf&amp;idr=$idr&amp;ct=$currenttab'>";
				echo "<table cellspacing='0' cellpadding='10' align='center' class='generaltable generalbox'>".
					"<tr><td colspan=3 align=center>$listfield</td></tr><tr><td>$listfieldon</td><td></td><td>".
                    "$listfieldoff</td><tr><td width='40%'>".
					"<select name='source_users' size='15'>";

				$sqls_on = get_records_sql("SELECT id, name_field FROM {$CFG->prefix}monit_razdel_field WHERE razdelid=$idr");
				$sqls_off = get_records_sql("SELECT idfield FROM {$CFG->prefix}monit_options WHERE idtypeschool=$id_ts");

				foreach ($sqls_on as $sql) {					$firsts[$sql->id] = $sql->name_field;
				}				foreach ($sqls_off as $sql) {					$seconds[$sql->idfield] = $firsts[$sql->idfield];
				}

				$result = array_diff ($firsts, $seconds);
				if(array_count_values($result) == 0) {					$result = $firsts;				}
				foreach ($result as $first => $key) {					echo "<option value=\"$first\">".translitfield($firsts[$first], 2)."</option>";
				}
				echo"</select></td><td width='20%' valign='center' align='center'>".
					"<input type='button' name='useraddall' value='- - >>' onclick='usersAddAll(source_users, destination_users)'><p>".
					"<input type='button' name='useradd' value='- - >' onclick='usersAdd(source_users, destination_users)'><p>".
					"<input type='button' name='userdelall' value='<<- -' onclick='usersDelAll(source_users, destination_users)'><p>".
					"<input type='button' name='userdel' value='< - -' onclick='usersDel(source_users, destination_users)'><p>".
					"</td><td width='40%'><select name='destination_users' size='15'>";

				foreach ($seconds as $second => $key) {
					if($key <> '')  {						echo "<option value=\"$second\">".translitfield($seconds[$second], 2)."</option>";
					}
				}
				echo'</select></td><tr><td align=right colspan=3>'.
					'<input type="submit" name="sendemail" onclick="post(id_useron, destination_users, id_useroff, source_users)" value="'.get_string('savechanges').'">'.
					'<input type="submit" name="cancel" value="'.get_string('cancel').'">'.
					'<input type="hidden" name="id_useron">'.
					'<input type="hidden" name="id_useroff">'.
					'</td></tr></table></form>';			}
		break;
		case 2:

		break;
	}
?>

<?php
	print_footer($site);
?>