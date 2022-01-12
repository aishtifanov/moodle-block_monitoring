<?php // $Id: form.php,v 1.4 2008/05/20 08:29:56 Shtifanov Exp $
	require_once("../../../config.php");
	require_once('../lib.php');
	require_once($CFG->libdir.'/uploadlib.php');
	require_once($CFG->dirroot.'/mod/hotpot/db/update_to_v2.php');

	$frm = data_submitted(); /// load up any submitted data

	$workforms = get_string('workforms', 'block_monitoring');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	if (!$admin_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $workforms";
    print_header_mou("$site->shortname: $workforms", $site->fullname, $breadcrumbs);

	print_simple_box_start("center", "%100");


	if (!empty($frm) ) {
		$um = new upload_manager('userfile',false,false,null,false,0);
		$f = 0;
		if ($um->preprocess_files()) {
			$filename = $um->files['userfile']['tmp_name'];
			$text = file($filename);
//			$text = my_file_get_contents($filename);
			echo "<center>";
			if($text==''){
				error(get_string('errorfile', 'block_cdoadmin'), "$CFG->wwwroot/blocks/cdoadmin/loadtest.php");
			}

			$textlib = textlib_get_instance();
			$size = sizeof($text);

			for($i=0; $i < $size; $i++)  {				$text[$i] = $textlib->convert($text[$i], 'win1251');
				switch ($i) {
				case 0:
					list($form->name, $form->fullname, $form->period, $form->levelmonit, $form->reported, $temp) = explode(";", $text[$i]);
					$table = get_record_sql("select id from {$CFG->prefix}monit_form where name='$form->name'");
					if(!$table)  {						insert_record("monit_form", $form);					}
					$table = get_record_sql("select id from {$CFG->prefix}monit_form where name='$form->name'");
					$idform = $table->id;
//					print '<br>'.$idform.'<br>';
				break;
				case 1:
					list($rzd->name, $rzd->shortname, $rzd->help, $temp, $rzd->reported, $temp) = explode(";", $text[$i]);
					$rzd->formid = $idform;
					$table = get_record_sql("select id from {$CFG->prefix}monit_razdel where name='$rzd->name' and formid=$idform");
					if(!$table)  {
						insert_record("monit_razdel", $rzd);
//					print_r($rzd);
//						print "fdgdsfgdsfgsdfgsdfgsdfgdsfgsdfgsdfgsdfg";
					}
					$table = get_record_sql("select id from {$CFG->prefix}monit_razdel where name='$rzd->name' and formid=$idform");

					$idrzd = $table->id;
//					print 'idrzd='.$idrzd."select id from {$CFG->prefix}monit_razdel where name='$rzd->name' and formid=$idform";
					$sql = "CREATE TABLE `{$CFG->prefix}monit_bkp_table_$idrzd` (".
							'`id` int(10) NOT NULL auto_increment,'.
							'`listformid` int(10) NOT NULL,';
//							print $sql;
				break;
				default:
					if(!hotpot_db_table_exists("monit_bkp_table_$idrzd"))  {
						list($listfieldrzd->name_field, $listfieldrzd->name, $listfieldrzd->help, $type, $listfieldrzd->edizm, $listfieldrzd->calcfunc) = explode(";", $text[$i]);
						$listfieldrzd->calcfunc = trim($listfieldrzd->calcfunc);
						$listfieldrzd->edizm = trim($listfieldrzd->edizm);
						$listfieldrzd->razdelid = $idrzd;
						$table = get_record_sql("select id from {$CFG->prefix}monit_razdel_field where name_field='$listfieldrzd->name_field' and razdelid=$idrzd");
						if(!$table)  {
							insert_record("monit_razdel_field", $listfieldrzd);
						}
						$sql.= '`'.$listfieldrzd->name_field.'` '.$type.' default NULL,';
					}  else  {						redirect("$CFG->wwwroot/blocks/monitoring/form/form.php", get_string('errorimportform', 'block_monitoring'));					}
      			}
			}

			$sql.= 'PRIMARY KEY  (`id`)'.
					') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
//print "$sql";
			execute_sql($sql, false);
			redirect("$CFG->wwwroot/blocks/monitoring/frontpage.php", get_string('completeimportform', 'block_monitoring'));
		}
	}  else  { 		print_heading(get_string('loadform', 'block_monitoring').'(old)', 'center', 3);
   	    echo "<table cellspacing='0' cellpadding='10' align='center' class='generaltable generalbox'><tr><td align=center>";
		echo '<form method="post" enctype="multipart/form-data" action="form.php">'.
		'<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">'.
		'<input type="file" name="userfile" size="30">'.
		'<p><input type="submit" name="load" value="'.get_string('upload', 'block_monitoring').'">';
		echo '</form></td></tr></table>';
	}
	print_simple_box_end();

    print_footer();
?>
