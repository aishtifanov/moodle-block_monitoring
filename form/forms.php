<?php // $Id: forms.php,v 1.11 2010/10/29 11:58:26 Oleg Exp $

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

	$csv_delimiter = ';';

	if (!empty($frm) ) {
		$um = new upload_manager('userfile',false,false,null,false,0);
		$f = 0;
		if ($um->preprocess_files()) {
			$filename = $um->files['userfile']['tmp_name'];

			$text = file($filename);
			if($text == FALSE){
				error(get_string('errorfile', 'block_cdoadmin'), "$CFG->wwwroot/blocks/cdoadmin/loadtest.php");
			}
			$size = sizeof($text);

			$textlib = textlib_get_instance();
  			for($i=0; $i < $size; $i++)  {
				$text[$i] = $textlib->convert($text[$i], 'win1251');
            }
            unset ($textlib);

		    $required = array('table' => 1, 'name' => 1, 'shortname' => 1, 'help' => 1, 'period' => 1,
							   'levelmonit' => 1, 'reported' => 1, 'name_field' => 1, 'calcfunc' => 1,
							   'edizm' => 1, 'fullname'  => 1);

            // --- get and check header (field names) ---
            $header = split($csv_delimiter, $text[0]);
            // check for valid field names
            foreach ($header as $i => $h) {
                $h = trim($h);
                $header[$i] = $h;
                if (!isset($required[$h])) {
                    error(get_string('invalidfieldname', 'error', $h), "$CFG->wwwroot/blocks/monitoring/form/forms.php");
                }
                if (isset($required[$h])) {
                    $required[$h] = 0;
                }
            }


  			for($i=1; $i < $size; $i++)  {
	            $line = split($csv_delimiter, $text[$i]);
 	  	        foreach ($line as $key => $value) {
  	                $record[$header[$key]] = trim($value);
   	 	        }

   	 	        if (isset($record['table']))	{
   	 	        	switch ($record['table'])	{
   	 	        		case 'monit_form':
                                // print_object($record);
                                $form = new stdClass();
								if (!empty($record['name'])) $form->name = $record['name'];
								if (!empty($record['fullname'])) $form->fullname = $record['fullname'];
								if (!empty($record['period'])) $form->period = $record['period'];
								if (!empty($record['levelmonit'])) $form->levelmonit = $record['levelmonit'];
								if (!empty($record['reported'])) $form->reported = $record['reported'];
								else $form->reported = 0;

								if(!$table = get_record('monit_form', 'name', $form->name))  {
									if($idform = insert_record("monit_form", $form))	{
								 		 notify(get_string('addinforms','block_monitoring', $form->name), 'green', 'left');
									} else {
										error(get_string('errorinaddingform','block_monitoring', $form->name), "$CFG->wwwroot/blocks/monitoring/form/forms.php");
									}
								} else {
									$idform = $table->id;
								}
                                // print $idform;

   	 	        		break;
   	 	        		case 'monit_razdel':
                                // print_object($record);
                                $rzd = new stdClass();
								$rzd->formid = $idform;
								if (!empty($record['name'])) $rzd->name = $record['name'];
								if (!empty($record['shortname'])) $rzd->shortname = $record['shortname'];
								if (!empty($record['help'])) $rzd->help = $record['help'];
								if (!empty($record['reported'])) $rzd->reported = $record['reported'];
								else $rzd->reported = 0;

								if(!$table = get_record('monit_razdel', 'formid', $rzd->formid, 'name', $rzd->name, 'shortname', $rzd->shortname))  {
								    $rzd->yearid = get_current_edu_year_id();
									if($idrzd =  insert_record("monit_razdel", $rzd))   {
								 		 notify(get_string('addinrazdel','block_monitoring', $rzd->name), 'green', 'left');
									} else {
										error(get_string('errorinaddingrazdel','block_monitoring', $rzd->name), "$CFG->wwwroot/blocks/monitoring/form/forms.php");
									}
								} else {
								    $idrzd = $table->id;
								}
                                
                                // print $idrzd;

								if ($rzd->reported == 0)  {
									if (isset($rzd->shortname))		{
										$sqlcreate = "CREATE TABLE `{$CFG->prefix}monit_form_{$rzd->shortname}` (";
									} else {
										$sqlcreate = "CREATE TABLE `{$CFG->prefix}monit_bkp_table_$idrzd` (";
									}
									$sqlcreate .= '`id` int(10) NOT NULL auto_increment,'.
	 											  '`listformid` int(10) NOT NULL,';
								}

   	 	        		break;
   	 	        		case 'monit_razdel_field':
								if(!hotpot_db_table_exists("monit_bkp_table_$idrzd"))  {
								    $listfieldrzd = new stdClass();
									$listfieldrzd->razdelid = $idrzd;
									if (!empty($record['name_field'])) $listfieldrzd->name_field = $record['name_field'];
									if (!empty($record['name'])) $listfieldrzd->name = $record['name'];
									if (!empty($record['help'])) $listfieldrzd->help = $record['help'];
									if (!empty($record['edizm'])) $listfieldrzd->edizm = $record['edizm'];
									if (!empty($record['calcfunc'])) $listfieldrzd->calcfunc = $record['calcfunc'];


									if(insert_record("monit_razdel_field", $listfieldrzd))   {
								 		 notify(get_string('addinrazdelfield','block_monitoring', $listfieldrzd->name), 'green', 'left');
									} else {
										error(get_string('errorinaddingrazdelfield','block_monitoring', $listfieldrzd->name), "$CFG->wwwroot/blocks/monitoring/form/forms.php");
									}
									// ;
			//						$table = get_record_sql("select id from {$CFG->prefix}monit_razdel_field where name='$listfieldrzd->name' and razdelid=$idrzd");
			//						$table = get_record_sql("select id from {$CFG->prefix}monit_razdel_field where razdelid=$idrzd");
			//						print_r($table);
			//						$idtable = $table->id;
									if ($rzd->reported == 0)  {
									    if (isset($listfieldrzd->edizm))   $type = get_typefield ($listfieldrzd->edizm);
									    else $type = '';
									    if (!empty($type)) {
											$sqlcreate .= '`'.$listfieldrzd->name_field.'` '.$type.' default NULL,';
										}
									}
								}  else  {
									 redirect("$CFG->wwwroot/blocks/monitoring/form/forms.php", get_string('errorimportform', 'block_monitoring'), 30);
								}
   	 	        		break;
   	 	        	}
   	 	        }
            }


			if ($form->reported == 0)  {
				$sqlcreate .= 'PRIMARY KEY  (`id`)'.
						') ENGINE=InnoDB DEFAULT CHARSET=utf8;';

				if (!execute_sql($sqlcreate, false)) {
					print $sqlcreate;
				} else {
				    // print $sqlcreate;
				}
			}
			redirect("$CFG->wwwroot/blocks/monitoring/index.php", get_string('completeimportform', 'block_monitoring'), 30);
		}
	}  else  {
 		print_heading(get_string('loadform', 'block_monitoring'), 'center', 3);
   	    echo "<table cellspacing='0' cellpadding='10' align='center' class='generaltable generalbox'><tr><td align=center>";
		echo '<form method="post" enctype="multipart/form-data" action="forms.php">'.
		'<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">'.
		'<input type="file" name="userfile" size="30">'.
		'<p><input type="submit" name="load" value="'.get_string('upload', 'block_monitoring').'">';
		echo '</form></td></tr></table>';
	}
	print_simple_box_end();

    print_footer();


function get_typefield ($edizm)
{
    if (empty($edizm)) return '';

	switch($edizm) {
		case 'man': case'item': case 'unit':
			$value = 'smallint UNSIGNED';
		break;	
		case 'trub': case 'rub': case 'ball': case 'proc':
			$value = 'double';
		break;
		case 'bool':
			$value = 'tinyint(1)';
		break;
		case 'data':
			$value = 'date';
		break;
		case 'time':
			$value = 'int(10)';
		break;
		case 'text':
			$value = 'text';
		break;
		case 'link':
			$value = 'varchar(255)';
		break;
		case 'null':
			$value = '';
		break;
		default:
			$value = 'integer';
		break;

	}

	return $value;
}

?>
