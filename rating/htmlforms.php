<?php // $Id: htmlforms.php,v 1.16 2012/11/14 10:58:53 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('lib_rating.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm = required_param('nm', PARAM_INT);         // Month
    $shortname = required_param('sn');       // Shortname form
	$action   = optional_param('action',   '-');
	$copynext = optional_param('copynext', '-');
	$copyprev = optional_param('copyprev', '-');

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$itogmark = 0;
	
    // check security
    $datefrom = get_date_from_month_year($nm, $yid);
    $strsql = "SELECT * FROM {$CFG->prefix}monit_rating_listforms
	   		   WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified=$datefrom)";
	
	$redirlink = "$CFG->wwwroot/blocks/monitoring/rating/listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid";
   	// print $strsql; echo '<hr>';
    if ($rec = get_record_sql($strsql))	{
    	// print_r($rec); echo '<hr>';
    	$currstatus = $rec->status;
        if ($currstatus == 4 && $school_operator_is && !$admin_is  && !$region_operator_is && !$rayon_operator_is )  {
	        error(get_string('accessdenied','block_monitoring'), $redirlink);
        }

        if ($currstatus >= 5 && ($rayon_operator_is || $school_operator_is) && !$admin_is  && !$region_operator_is)  {
	        error(get_string('accessdenied','block_monitoring'), $redirlink);
        }

        if ($rec->id != $fid)  {
	        error(get_string('accessdenied','block_monitoring'), $redirlink);
        }
    }

    $school = get_record('monit_school', 'id', $sid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strrating = get_string('rating', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
    
    $razdel = get_record_select('monit_razdel', "shortname = '$shortname'", 'id, name');
	// $strformname = get_string('name_'.$shortname,'block_monitoring');
    $strformname = $razdel->name;

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rating/listforms.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$school->name</a>";
	$breadcrumbs .= " -> $strformname";

    print_header_mou("$SITE->shortname: $strformname", $SITE->fullname, $breadcrumbs);


	if ($rid == 0 ||  $sid == 0) exit();

	$rec = array();
	$REGIONCRITERIA = new stdClass();
	init_region_criteria($yid);
	$timedenied = time();
	if (!$admin_is && !$region_operator_is)	{
		if ($timedenied > $REGIONCRITERIA->timeaccessdenied)	{  
			//    $str1 = $strreports.': '.$school->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
			notice(get_string('accessdenied', 'block_monitoring'), $CFG->wwwroot.'/blocks/monitoring/index.php');
		}
	}	
		
	/// A form was submitted so process the input
	if ($rec = data_submitted())  {
	   
        // print_object($rec);
        foreach ($rec as $fld => $value)   {
            if (strpos($value, ','))    {
                $rec->{$fld} = str_replace(',', '.', $value); 
            }
        }    
        // print_object($rec);        
        $err = array();
	    $errcount = find_form_errors($rec, $err, 'monit_form_'.$shortname);

		if ($errcount == 0)  {
			
			// print_r($REGIONCRITERIA). '<hr>'; exit();
			
            // print $fid. '<br>';
		    if ($fid == 0)  { // insert new records
               $rkp = new stdClass();
			   $rkp->rayonid = $rid;
		       $rkp->schoolid = $sid;
		       $rkp->status = 2;
		       $rkp->shortname = $shortname;
		       // $rkp->shortrusname =  $strformname;
		       // $rkp->fullname = ??????????
		       $rkp->datemodified = get_date_from_month_year($nm, $yid);

   			   $strsql = "SELECT id, rayonid, schoolid, shortname, datemodified FROM {$CFG->prefix}monit_rating_listforms
	 		   		      WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified={$rkp->datemodified})";

	 		   if ($recsss = get_record_sql($strsql)) 	{
	 		   	  error(get_string('errorinduplicatedformcreate','block_monitoring'), $redirlink);
	 		   }

		       if (!$idnew = insert_record('monit_rating_listforms', $rkp))	{
					error(get_string('errorincreatinglist','block_monitoring'), $redirlink);
			   }

		       $rec->listformid = $idnew;


		       if (!$idform = insert_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorincreatingform','block_monitoring'), $redirlink);
			   }
			   
			   $totalmark = calculate_school_mark($yid, $rid, $sid, $idform, $shortname);
			   echo $totalmark . '!!!';
		        // notice(get_string('succesavedata','block_monitoring'), );
			   redirect($redirlink, get_string('succesavedata','block_monitoring'), 30);

		    } else {  // update records

		       $rec->listformid = $fid;
		       $df = get_record_sql("SELECT id, listformid FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid");
		       $rec->id = $df->id;

               // print_object($rec);
		       if (!update_monit_record('monit_form_'.$shortname, $rec))	{
		       		print_r($rec);
					error(get_string('errorinupdatingform','block_monitoring'), $redirlink);
			   }
			   $totalmark = calculate_school_mark($yid, $rid, $sid, $rec->id, $shortname);
			   echo $totalmark; 
		       // notice(get_string('succesupdatedata','block_monitoring'), $redirlink);
		       redirect($redirlink, get_string('succesupdatedata','block_monitoring'), 30);
		    }
		}
	}

    if ($fid != 0)  {
    	$rec = get_record('monit_form_'.$shortname, 'listformid', $fid);
       // print_r($rec);
    }

    // print_r($rec);
    $streduname = '2005/2006';
    if ($yid == 1)	{
    	$streduname = '2006/2007';
    } else {
    	$yearedu = get_record('monit_years', 'id', $yid-1);
		$streduname = $yearedu->name;	
    }
    $strnamemonth = get_string('periodreport', 'block_monitoring', $streduname);
	print_heading($strrating.': '.$school->name, "center", 3);
	print_heading($strformname.'.<br>'.$strnamemonth, "center", 4);

    print_simple_box_start("center");
    // include("$shortname.php");
    // include("end_of_forms.html");
    print_rating_htmlforms($rid, $sid, $yid, $fid, $nm, $shortname);
    
  	print_simple_box_end();

    print_footer();




function print_rating_htmlforms($rid, $sid, $yid, $fid, $nm, $shortname)
{
	global $CFG, $USER, $rec;
	
	?>	
	<form name="bkp_zp" method="post" action="htmlforms.php">
	<input type="hidden" name="rid" value="<?php echo $rid ?>" />
	<input type="hidden" name="sid" value="<?php echo $sid ?>" />
	<input type="hidden" name="fid" value="<?php echo $fid ?>" />
	<input type="hidden" name="nm" value="<?php echo $nm ?>" />
	<input type="hidden" name="yid" value="<?php echo $yid ?>" />
	<input type="hidden" name="sn" value="<?php echo $shortname ?>" />
	<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
	<!-- <table class="formtable" cellpadding="5"> -->
	<table border="1" cellspacing="2" cellpadding="5" align="center" bordercolor=black>
	<tr>
		<th valign="top" nowrap="nowrap" ><?php print_string('symbolnumber', 'block_monitoring') ?></th>
		<th valign="top" nowrap="nowrap" ><?php print_string('nameofpokazatel', 'block_monitoring') ?></th>
		<th valign="top" nowrap="nowrap" ><?php print_string('valueofpokazatel', 'block_monitoring') ?></th>
	</tr>
	<?php

  get_name_otchet_year ($yid, $a, $b);
	
  $razdel = get_record ('monit_razdel', 'shortname', $shortname);
  
  if (isset($rec)) {
	  $arrec = (array)$rec;
  }
  // print_r($razdel);
  if ($razdel)	{
  		$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id, 'timecalculated, id');
 	    // print_r($fields);
  		if ($fields) {
  		    $num_I = 0;  $num_II = 1;
            foreach ($fields as $field)  {
            	eval("\$fieldname = \"$field->name\";");
            	switch ($field->edizm)	{
            		case 'null': $num_I++; $num_II = 1;
            					 echo '<tr valign="top">';
								 echo "<TD>$num_I</TD>";
   								 echo "<TD><B>$fieldname</B></TD>";
							     echo '<TD></TD></tr>';
            		break;

                    case 'days':
                    case 'hour':
					case 'man': 
            		case 'item':
            		case 'ball':
            		case 'proc':
                    case 'year':
								echo '<tr valign="top">';
								if (isset($field->name_field)) {
									$_num = translitfield('f'.$field->name_field);
								} else {
									$_num = $num_I.$num_II;
								}
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$fieldname</td>";
							    echo "<td align=left> <input type=text name=$field->name_field size=5 maxlength=7 ";
								if (isset($err[$field->name_field])) {
									echo 'style="border-color:#FF0000"';
								}
								echo 'value=';
								if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
									echo $arrec[$field->name_field];
								} else {
								    echo '0';
                                 /*   
                                    // DEBUGING
                                    if ($field->name_field == 'fn_1' || $field->name_field == 'fo_1')   {
                                        echo '100';
                                    } else {
                                        echo rand(2, 99);
                                    }
                                 */   
                                }
                                //////////////////////////
								$stredizm = get_string($field->edizm, 'block_monitoring');
								echo '>&nbsp;' . $stredizm;
								echo '</td></tr>';
								$num_II++;
            		break;
            		
            		case 'trub':
								echo '<tr valign="top">';
								$_num = translitfield('f'.$field->name_field);
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$fieldname</td>";
							    echo "<td align=left> <input type=text name=$field->name_field size=5 maxlength=12 ";
								if (isset($err[$field->name_field])) {
									echo 'style="border-color:#FF0000"';
								}
								echo 'value=';
								if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
									echo $arrec[$field->name_field];
								}
								echo '>&nbsp;' . get_string($field->edizm, 'block_monitoring');
								echo '</td></tr>';
								$num_II++;
            		break;
            		
            		case 'bool': 
					  		    $yes = get_string('yes');
					  		    $no = get_string('no');
								$_num = translitfield('f'.$field->name_field);
								echo '<tr valign="top">';
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$fieldname</td>";
							    echo "<td align=left><select size=1 name={$field->name_field}>";
								if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field] == 0) {
							 	   echo '<option selected value="0">--</option>';
							 	}  else {
							 	   echo '<option value="0">--</option>';
							 	}

								if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field] == 1) {
						   		   echo '<option selected value="1">'.$yes.'</option>';
						   		}  else {
						   		   echo '<option value="1">'.$yes.'</option>';
						   		}
								if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field] == -1) {
						   		   echo '<option selected value="-1">'.$no.'</option>';
						   		}  else {
						   		   echo '<option value="-1">'.$no.'</option>';
						   		}
								echo '</select>';
								echo '</td></tr>';
								$num_II++;
            		break;
            		
            		
            		default:   notify('Unknown edizm:'. $field->edizm);
            		
            	}
            }
  		}
  }
  echo '</table>';
  if (!isregionviewoperator() && !israyonviewoperator())  {  
 	 echo '<table border=0 align=center><tr valign="top">';
     echo '<td align=center><input type="submit" value="';
	 print_string('savechanges');
	 echo '" /></td></form><td align="center">';
	 $options = array('rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
					 'fid' => $fid,  'nm' => $nm,  'sesskey' => $USER->sesskey);
    print_single_button("listforms.php", $options, get_string("revert"));
    echo '</td></tr></table>';
  }
}


function calculate_school_mark($yid, $rid, $sid, $id, $shortname)
{
	global $db, $CFG;
	 
 	// echo $yid . '<hr>';
 
	$arr_df = array();
	if ($df = get_record_sql("SELECT * FROM {$CFG->prefix}monit_form_$shortname WHERE id=$id"))	{
		$arr_df = (array)$df;
		// print_r($arr_df); echo '<hr>';   	
	}
    
    $totalmark = 0;
    init_rating_parameters($yid, $shortname, $select, $order);    
    $select .=  " AND edizm <> 'null'";

    $strsql = "SELECT id, number, formula, edizm, indicator, ordering FROM {$CFG->prefix}monit_rating_criteria
    		   WHERE $select
			   ORDER BY $order";
	if ($criterias = get_records_sql($strsql)) 	{
		
		$criteriaids = array();
   		foreach($criterias as $criteria)	{
   			$criteriaids[] = $criteria->id;
	  	}
   		$criterialist = implode(',', $criteriaids);

		$strsql = "UPDATE {$CFG->prefix}monit_rating_school mark=0 WHERE (yearid=$yid) AND (schoolid=$sid) AND (criteriaid in ($criterialist))";
		$db->Execute($strsql);		
		// delete_records('monit_rating_school', 'schoolid', $sid);
		// set_field('monit_rating_school', 'mark', 0, 'schoolid', $sid);
		// print_object($criterias); exit();
        $totalmark = calculating_rating_school($yid, $rid, $sid, $shortname, $arr_df, $criterias);
        
	}

    update_rating_total($yid, $rid, $sid, $shortname, $totalmark);
	
	return $totalmark;
}



?>