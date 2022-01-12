<?php // $Id: formc.php,v 1.2 2012/10/18 10:40:41 shtifanov Exp $
	require_once("../../../config.php");
	require_once('../lib.php');
	require_once($CFG->libdir.'/uploadlib.php');
	require_once($CFG->dirroot.'/mod/hotpot/db/update_to_v2.php');

   	$yid = get_current_edu_year_id();

	$workforms = get_string('workforms', 'block_monitoring');

	$admin_is = isadmin();
	if (!$admin_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $workforms";
    print_header_mou("$SITE->shortname: $workforms", $SITE->fullname, $breadcrumbs);

	print_simple_box_start("center", "%100");

	if ($frm = data_submitted()) {
		$um = new upload_manager('userfile',false,false,null,false,0);
		$f = 0;
		if ($um->preprocess_files()) {
			$filename = $um->files['userfile']['tmp_name'];
			$text = file($filename);
//			$text = my_file_get_contents($filename);
			echo "<center>";
			if($text==''){
				error(get_string('errorfile', 'block_cdoadmin'), "formc.php");
			}

			$textlib = textlib_get_instance();
			$size = sizeof($text);

			for($i=0; $i < $size; $i++)  {
				$text[$i] = $textlib->convert($text[$i], 'win1251');
				// echo $text[$i];
				switch ($i) {
				case 0:
				break;
				case 1:
				break;
				default:
						list($listfieldrzd->name_field, $listfieldrzd->name, $listfieldrzd->help, $type, $listfieldrzd->edizm, $listfieldrzd->calcfunc) = explode(";", $text[$i]);
						if($listfieldrzd->name_field == 'c') {						
							list($temp, $criteria->name, $criteria->number, $criteria->edizm, $criteria->indicator, $criteria->formula, $criteria->ordering, $criteria->gradelevel) = explode(";", $text[$i]);	
							
							$criteria->number = trim($criteria->number);
							$criteria->yearid = $yid;
							$criteria->weight = 1;
                            
                            // print_object($criteria);
                            						
							if(insert_record('monit_rating_criteria', $criteria))   {
						 		 notify(get_string('addinrazdelfield','block_monitoring', $criteria->name), 'green', 'left');
							} else {
								error(get_string('errorinaddingrazdelfield','block_monitoring', $listfieldrzd->name), "$CFG->wwwroot/blocks/monitoring/form/formc.php");
							}
						
							unset($criteria);		
						}						
      			}
			}

			redirect("$CFG->wwwroot/blocks/monitoring/index.php", get_string('completeimportform', 'block_monitoring'), 30);
		}
	}  else  {
 		print_heading(get_string('loadform', 'block_monitoring').' (for rating criteria)', 'center', 3);
   	    echo "<table cellspacing='0' cellpadding='10' align='center' class='generaltable generalbox'><tr><td align=center>";
		echo '<form method="post" enctype="multipart/form-data" action="formc.php">'.
		'<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">'.
		'<input type="file" name="userfile" size="30">'.
		'<p><input type="submit" name="load" value="'.get_string('upload', 'block_monitoring').'">';
		echo '</form></td></tr></table>';
	}
	print_simple_box_end();

    print_footer();
?>
