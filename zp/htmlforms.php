<?php // $Id: htmlforms.php,v 1.6 2012/09/07 10:18:43 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);       // Rayon id
    $fid = required_param('fid', PARAM_INT);       // Form id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm = required_param('nm', PARAM_INT);         // Month
    $shortname = required_param('sn');       // Shortname form
	$action   = optional_param('action',   '-');
	$copynext = optional_param('copynext', '-');
	$copyprev = optional_param('copyprev', '-');
    $sid = optional_param('sid', 0, PARAM_INT);            // School id

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('regionzp');
	$rayon_operator_is  = ismonitoperator('rayonzp', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$itogmark = 0;
	
    // check security
    $datefrom = get_date_from_month_year($nm, $yid);
    $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
	   		   WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified=$datefrom)";
	
	$redirlink = "listforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid";
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

    $rayon = get_record('monit_rayon', 'id', $rid);

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strrating = get_string('zp', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	// $strformname = get_string('name_'.$shortname,'block_monitoring');
    if ($razdel = get_record_select ('monit_razdel', "shortname = '$shortname'", 'id, name')) {
        $strformname = $razdel->name;
    } else {
        $strformname = get_string('name_'.$shortname,'block_monitoring');
    }


    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/zp/listforms.php?rid=$rid&yid=$yid\">$rayon->name</a>";
	$breadcrumbs .= " -> $strformname";

    print_header_mou("$SITE->shortname: $strformname", $SITE->fullname, $breadcrumbs);


	if ($rid == 0 ) exit();

	/// A form was submitted so process the input
	if ($rec = data_submitted())  {
        
        // print_object($rec);
        foreach ($rec as $fld => $value)   {
            if (strpos($value, ','))    {
                $rec->{$fld} = str_replace(',', '.', $value); 
            }
        }    
        // print_object($rec);        
             
	    $errcount = find_form_errors($rec, $err, 'monit_form_'.$shortname);

		if ($errcount == 0)  {
			
			// print_r($REGIONCRITERIA). '<hr>'; exit();
			
            // print $fid. '<br>';
		    if ($fid == 0)  { // insert new records
			   $rkp->rayonid = $rid;
		       $rkp->status = 2;
		       $rkp->shortname = $shortname;
		       // $rkp->shortrusname =  $strformname;
		       // $rkp->fullname = ??????????
		       $rkp->datemodified = get_date_from_month_year($nm, $yid);

   			   $strsql = "SELECT id, rayonid, shortname, datemodified FROM {$CFG->prefix}monit_rayon_listforms
	 		   		      WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified={$rkp->datemodified})";

	 		   if ($recsss = get_record_sql($strsql)) 	{
	 		   	  error(get_string('errorinduplicatedformcreate','block_monitoring'), $redirlink);
	 		   }
                
               calculate_zp_form($rec);
               // print_object($rec);                
		       if (!$idnew = insert_record('monit_rayon_listforms', $rkp))	{
					error(get_string('errorincreatinglist','block_monitoring'), $redirlink);
			   }

		       $rec->listformid = $idnew;

               // print_r($rec);
		       if (!insert_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorincreatingform','block_monitoring'), $redirlink);
			   }
			   
		        // notice(get_string('succesavedata','block_monitoring'), );
			   redirect($redirlink, get_string('succesavedata','block_monitoring'), 30);

		    } else {  // update records

		       $rec->listformid = $fid;
		       $df = get_record_sql("SELECT id, listformid FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid");
		       $rec->id = $df->id;
               
               foreach ($rec as $fld => $value)   {
                    if (empty($value))    {
                        $rec->{$fld} = 0;
                    }
               } 

               calculate_zp_form($rec);
               // print_object($rec);
		       if (!update_monit_record('monit_form_'.$shortname, $rec))	{
		       		print_r($rec);
					error(get_string('errorinupdatingform','block_monitoring'), $redirlink);
			   }
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
    /*
    $streduname = '2005/2006';
    if ($yid == 1)	{
    	$streduname = '2006/2007';
    } else {
    	$yearedu = get_record('monit_years', 'id', $yid-1);
		$streduname = $yearedu->name;	
    }
    
    $strnamemonth = get_string('periodreport', 'block_monitoring', $streduname);
    */
    
	print_heading($strrating.': '.$rayon->name, "center", 4);
    $strnamemonth = get_string('nm_'.$nm, 'block_monitoring');
	print_heading($strformname . ': '. $strnamemonth, "center", 3);

    print_simple_box_start("center");
    // include("$shortname.php");
    // include("end_of_forms.html");
    print_zp_htmlforms($rid, $yid, $fid, $nm, $shortname);
    
  	print_simple_box_end();

    print_footer();


function calculate_zp_form(&$rec)   
{
    $rec->r1_4 = $rec->f1_5 + $rec->f1_6;
    $rec->r1_3 = $rec->f1_3 + $rec->f1_4; // + $rec->f1_3_1 
    // $rec->r1_0 = $rec->f1_1 + $rec->f1_1_1 + $rec->f1_2 + $rec->f1_2_1 + $rec->r1_3 + $rec->r1_4;     
    $rec->r1_0 = $rec->f1_1 + $rec->f1_2 + $rec->r1_3 + $rec->r1_4;

    $rec->r2_4 = $rec->f2_5 + $rec->f2_6;
    $rec->r2_3 = $rec->f2_3 + $rec->f2_4; // + $rec->f2_3_1 
    // $rec->r2_0 = $rec->f2_1 + $rec->f2_1_1 + $rec->f2_2 + $rec->f2_2_1 + $rec->r2_3 + $rec->r2_4;
    $rec->r2_0 = $rec->f2_1 + $rec->f2_2 + $rec->r2_3 + $rec->r2_4;
    
    $rec->r3_4 = $rec->f3_4 + $rec->f3_5;
    // $rec->r3_0 = $rec->f3_1 + $rec->f3_1_1 + $rec->f3_2 + $rec->f3_2_1 + $rec->f3_3 + $rec->r3_4;     
    $rec->r3_0 = $rec->f3_1 + $rec->f3_2 +  $rec->f3_3 + $rec->r3_4;
         
    $rec->r4_4 = $rec->f4_4 + $rec->f4_5;
    // $rec->r4_0 = $rec->f4_1 + $rec->f4_1_1 + $rec->f4_2 + $rec->f4_2_1 + $rec->f4_3 + $rec->r4_4;     
    $rec->r4_0 = $rec->f4_1 + $rec->f4_2 + $rec->f4_3 + $rec->r4_4;
    
    $rec->r5_4 = $rec->f5_4 + $rec->f5_5;
    // $rec->r5_0 = $rec->f5_1 + $rec->f5_1_1 + $rec->f5_2 + $rec->f5_2_1 + $rec->f5_3 + $rec->r5_4;     
    $rec->r5_0 = $rec->f5_1 + $rec->f5_2 + $rec->f5_3 + $rec->r5_4;
}


function print_zp_htmlforms($rid, $yid, $fid, $nm, $shortname)
{
	global $CFG, $USER, $rec;
	
	?>	
	<form name="bkp_zp" method="post" action="htmlforms.php">
	<input type="hidden" name="rid" value="<?php echo $rid ?>" />
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
  		$fields = get_records_select ('monit_razdel_field', "razdelid=$razdel->id", 'name_field');
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

					case 'man': 
            		case 'item':
            		case 'ball':
            		case 'proc':
								echo '<tr valign="top">';
								if (isset($field->name_field)) {
									$_num = translitfield($field->name_field) . '.';
								} else {
									$_num = $num_I.$num_II . '.';
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
								}
								$stredizm = get_string($field->edizm, 'block_monitoring');
								echo '>&nbsp;' . $stredizm;
								echo '</td></tr>';
								$num_II++;
            		break;
            		
            		case 'trub': case 'rub':
								echo '<tr valign="top">';
								// $_num = translitfield('f'.$field->name_field);
                                $_num = translitfield($field->name_field) . '.';
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$fieldname</td>";
							    echo "<td align=left> <input type=text name=$field->name_field size=12 maxlength=20 ";
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
	 $options = array('rid' => $rid, 'yid' => $yid, 'fid' => $fid,  'nm' => $nm,  'sesskey' => $USER->sesskey);
    print_single_button("listforms.php", $options, get_string("revert"));
    echo '</td></tr></table>';
  }
}


?>