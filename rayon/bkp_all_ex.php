<?php // $Id: bkp_all_ex.php,v 1.10 2011/01/26 09:01:13 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('../indices/lib_indices_school.php');

	require_once("$CFG->libdir/excel/Worksheet.php");
	require_once("$CFG->libdir/excel/Workbook.php");
	require_once("$CFG->libdir/textlib.class.php");

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm = required_param('nm', PARAM_INT);       // Month number
    $shortname = required_param('sn');       // Shortname form
    $mode = optional_param('mode', 'one');  // MODE: one, all

    $year = date ('Y');
    // echo $year;
    // print_header_mou("---", '--', '---');
    if ($mode == 'one')		{
		$filename = $shortname.'_'.$rid.'_'.$nm.'_'.$year;
        print_excel_header($filename);
		create_excel_workbook();
		print_excel_form($shortname, $rid, $yid, $nm);
		close_excel_workbook();
        exit();
	}  else  if ($mode == 'all')  {
		$filename = 'all_'.$rid.'_'.$nm.'_'.$year;
        print_excel_header($filename);
		create_excel_workbook();
        // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
        // $rkps = array('rkp_u', 'rkp_prm_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp', 'bkp_kbo');
        $rkps = array('rkp_u', 'bkp_pred', 'bkp_dolj', 'bkp_f', 'bkp_zp');
	    foreach($rkps as $rkp)	{
			print_excel_form($rkp, $rid, $yid, $nm);
		}
	 	print_excel_form_rkp_du($rid, $yid, $nm);
		close_excel_workbook();
	}



function print_excel_header($filename)
{
    

	// HTTP headers
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename.xls\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}


function create_excel_workbook()
{
	global $WORKBOOK;

    $WORKBOOK = new Workbook("-");
}


function close_excel_workbook()
{
   global $WORKBOOK;

   $WORKBOOK->close();
   unset($WORKBOOK);
}


function print_excel_form($shortname, $rid, $yid, $nm)
{
    global $CFG, $WORKBOOK, $sid;

    $txtl = new textlib();

    $rayon = get_record('monit_rayon', 'id', $rid);

    $razdel = get_record ('monit_razdel', 'shortname', $shortname, 'reported', 0);

	$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id);

    
    $strtitles = array();

    $numtitles = 1;
    $widthDATA = 5;
    $width0 = 5; 
	$width1 = 35;
	switch ($shortname)	{
		case 'bkp_pred': $width0 = 3; $width1 = 28; $numtitles = 3;
						$strtitles[0] =  $txtl->convert(strip_tags(get_string('kolphislicosndolj', 'block_monitoring')), 'utf-8', 'windows-1251');
					    $strtitles[1] =  $txtl->convert(strip_tags(get_string('kolphislicsovdolj', 'block_monitoring')), 'utf-8', 'windows-1251');
						$strtitles[2] =  $txtl->convert(strip_tags(get_string('kolphisliccondolj', 'block_monitoring')), 'utf-8', 'windows-1251');

		break;
		case 'bkp_dolj': $width0 = 3; $width1 = 55; $numtitles = 2;
						$strtitles[0] =  $txtl->convert(strip_tags(get_string('kolphisperson', 'block_monitoring')), 'utf-8', 'windows-1251');
					    $strtitles[1] =  $txtl->convert(strip_tags(get_string('kolstavok', 'block_monitoring')), 'utf-8', 'windows-1251');
		break;
		case 'bkp_f':  $width0 = 5; $width1 = 35;
		break;
		case 'bkp_zp': $width0 = 4; $width1 = 15;
		break;
		case 'rkp_u':  $width0 = 5; $width1 = 55; $widthDATA = 12;
		break;
		case 'rkp_prm_u':  $width0 = 5; $width1 = 60;
		break;
		case 'rkp_kbo':  $width0 = 5; $width1 = 60;
		break;

	}

    $strwin1251 =  $txtl->convert($razdel->name, 'utf-8', 'windows-1251');
    $myxls =& $WORKBOOK->add_worksheet($strwin1251);
//    $myxls->fit_to_pages(1, 5);

	$formath1 =& $WORKBOOK->add_format();
	$formath2 =& $WORKBOOK->add_format();
	$formatv2 =& $WORKBOOK->add_format();
	$formatp =& $WORKBOOK->add_format();

	$formath1->set_size(10);
    $formath1->set_align('left');
    $formath1->set_align('vcenter');
	$formath1->set_color('black');
	$formath1->set_bold(1);
	$formath1->set_italic();
	$formath1->set_text_wrap();
	// $formath1->set_border(2);

	$formath2->set_size(7);
    $formath2->set_align('center');
    $formath2->set_align('vcenter');
	$formath2->set_color('black');
	$formath2->set_bold(1);
	$formath2->set_italic();
	$formath2->set_border(1);
	$formath2->set_text_wrap();

	$formatv2->set_size(7);
    $formatv2->set_align('center');
    $formatv2->set_align('vcenter');
	$formatv2->set_color('black');
	$formatv2->set_bold(1);
	$formatv2->set_italic();
	$formatv2->set_border(1);
	$formatv2->set_text_wrap();
	$formatv2->rotation = 90;

	$formatp->set_size(7);
    $formatp->set_align('left');
    $formatp->set_align('vcenter');
	$formatp->set_color('black');
	$formatp->set_bold(0);
	$formatp->set_border(1);
	$formatp->set_text_wrap();

    $count_field = count($fields) + 2;

    $strtitle = get_string('titleforreportexcelrayon', 'block_monitoring', $razdel->name);
    $strtitle .= "\"$rayon->name\" за ";
	$strtitle .= get_string('nm_'.$nm, 'block_monitoring') . ' ';
    if ($year = get_record('monit_years', 'id', $yid))  {
		$strtitle .= get_string('uchyear', 'block_monitoring', $year->name);
  	}

	// Print names of all the fields
	$myxls->set_column(0,0,$width0);

	$myxls->set_column(1,1,$width1);
//	$myxls->set_column(2,2,3);
	$myxls->set_row(0, 20);
	$myxls->set_row(1, 100);

	$strwin1251 = $txtl->convert($strtitle, 'utf-8', 'windows-1251');

    $myxls->write_string(0, 0, $strwin1251, $formath1);

	$strwin1251 = $txtl->convert(get_string('symbolnumber','block_monitoring'), 'utf-8', 'windows-1251');
    $myxls->write_string(1, 0, $strwin1251, $formath2);

	$strwin1251 = $txtl->convert(get_string('nameofpokazatel','block_monitoring'), 'utf-8', 'windows-1251');
    $myxls->write_string(1, 1, $strwin1251, $formath2);

    if ($numtitles > 1) {
        $myxls->write_blank(2, 0, $formath2);
		$myxls->merge_cells(1, 0, 2, 0);
		$myxls->merge_cells(1, 1, 2, 1);
	}

 	    // print_r($fields);
  		if ($fields) {
  		    $num_I = 0;
  		    $num_II = 1;
  		    if ($numtitles > 1)  $i = 3;
  		    else                 $i = 2;
  		    $j = 0;
            foreach ($fields as $field)  {
            	if ($field->edizm == 'null') {
					 $num_I++;
					 // if (isset($field->name_field) && !empty($field->name_field))  {
					 if ($shortname == 'bkp_f'	|| $shortname == 'bkp_zp')  {
					 	  $strvalue = translitfield($field->name_field);
					 } else {
		           		  $strvalue = $num_I;
		           	 }
		           	 $num_II = 1;
            	} else {
					// if (isset($field->name_field) && !empty($field->name_field)) {
					if ($shortname == 'bkp_f'	|| $shortname == 'bkp_zp')  {
						$strvalue = translitfield($field->name_field);
					} else {
						$strvalue = $num_I.'.'.$num_II;
					}
					$num_II++;
            	}

				 $strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
      	         $myxls->write_string($i, $j, $strwin1251, $formatp);

           		 $strvalue = $field->name;
				 $strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
      	         $myxls->write_string($i, $j+1, $strwin1251, $formatp);

      	         $i++;

            }
  		}


	$datefromcurr = get_date_from_month_year($nm, $yid);


	$strsql = "SELECT *  FROM {$CFG->prefix}monit_school
			   WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
			   ORDER BY number";
 	if ($schools = get_records_sql($strsql))	{
 	  
        $numcolumn = count($schools)*$numtitles;
       	$myxls->merge_cells(0, 0, 0, $numcolumn+1);

	    for ($i=1; $i<$count_field; $i++)	{
	       for ($j=2; $j<=$numcolumn+1; $j++)	{
				$myxls->write_blank($i, $j, $formatp);
	 		}
	    }

	    $i = 1; $j = 2;
	    foreach ($schools as $school)  {

 	      	 $sid = $school->id;
             
             for ($t=0; $t<$numtitles; $t++)  {
				$myxls->set_column($j+$t, $j+$t, $widthDATA);
			 }

			 $strwin1251 = $txtl->convert($school->name, 'utf-8', 'windows-1251');
  	         $myxls->write_string($i, $j, $strwin1251, $formatv2);

         	 $myxls->merge_cells($i, $j, $i, $j+($numtitles-1));

   		     $i++;

		     if ($numtitles > 1) {
	             for ($t=0; $t<$numtitles; $t++)  {
	  	         	$myxls->write_string($i, $j+$t, $strtitles[$t], $formatv2);
				 }
             }


			$strsql = "SELECT id, schoolid, shortname, datemodified
					   FROM {$CFG->prefix}monit_school_listforms
			 		   WHERE (schoolid = {$school->id}) and (shortname='$shortname') and (datemodified=$datefromcurr)";

	 	    if ($listform = get_record_sql($strsql)) 	{

	 	        if ($rec = get_record('monit_form_'.$shortname, 'listformid', $listform->id)) 	{

	      		    $arrec = (array)$rec;

	      		    switch ($shortname)	{
						case 'bkp_pred':
						      		    $i_formula = 0;
						      		    $SUM_1 = $SUM_2 = $SUM_3 = 0;
							            foreach ($fields as $field)  {
						 		           	switch ($field->edizm)	{
						   		         		case 'null':  $i++;
						   		         		 			  $num_II = 1;
						   		         		 			  $i_formula = $i;
						     		       		break;
						       		     		case 'man': case 'item':
						  							    $i++; $num_II++;

														if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
															$myxls->write($i, $j, $arrec[$field->name_field], $formatp);
														    $SUM_1 += $arrec[$field->name_field];
														} else {
															$myxls->write($i, $j, '0', $formatp);
														}

														$fieldname_sov = $field->name_field . '_sov';
														if (isset($arrec[$fieldname_sov]) &&  $arrec[$fieldname_sov]> 0) {
															$myxls->write($i, $j+1, $arrec[$fieldname_sov], $formatp);
															$SUM_2 += $arrec[$fieldname_sov];
														} else {
															$myxls->write($i, $j+1, '0', $formatp);
														}


														$fieldname_con = $field->name_field . '_con';
														if (isset($arrec[$fieldname_con]) &&  $arrec[$fieldname_con]> 0) {
															$myxls->write($i, $j+2, $arrec[$fieldname_con], $formatp);
															$SUM_3 += $arrec[$fieldname_con];
														} else {
															$myxls->write($i, $j+2, '0', $formatp);
														}

						        		   		break;
						           		 	}
							            }
							            if ($i_formula > 0) {
							              // $myxls->write($i_formula, $j, "=SUM(R[1]C:R[$num_II]C)", $formath1);
							              // $a = "=SUM(C5:C20)";
							              $myxls->write($i_formula, $j,   $SUM_1, $formath2);
							              $myxls->write($i_formula, $j+1, $SUM_2, $formath2);
							              $myxls->write($i_formula, $j+2, $SUM_3, $formath2);
							            }
						break;
						case 'bkp_dolj':
						      		    $i_formula = 0;
						      		    $SUM_1 = $SUM_2 = 0;
						      		    $ALLSUM_1 = $ALLSUM_2 = 0;
							            foreach ($fields as $field)  {
						 		           	switch ($field->edizm)	{
						   		         		case 'null':
															  if ($i_formula > 0 && $SUM_1 >0 && $SUM_2 > 0) {
													               $myxls->write($i_formula, $j,   $SUM_1, $formath2);
													               $myxls->write($i_formula, $j+1, $SUM_2, $formath2);
													               $ALLSUM_1 += $SUM_1;
													               $ALLSUM_2 += $SUM_2;
													               $SUM_1 = $SUM_2 = 0;
												              }
						   		         					  $i++;
						   		         		 			  $num_II = 1;
						   		         		 			  $i_formula = $i;

						     		       		break;
						       		     		case 'man': case 'item':
						  							    $i++; $num_II++;

														if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
															$myxls->write($i, $j, $arrec[$field->name_field], $formatp);
														    $SUM_1 += $arrec[$field->name_field];
														} else {
															$myxls->write($i, $j, '0', $formatp);
														}

														$fieldname_sov = $field->name_field . '_st';
														if (isset($arrec[$fieldname_sov]) &&  $arrec[$fieldname_sov]> 0) {
															$myxls->write($i, $j+1, $arrec[$fieldname_sov], $formatp);
															$SUM_2 += $arrec[$fieldname_sov];
														} else {
															$myxls->write($i, $j+1, '0', $formatp);
														}
						        		   		break;
						           		 	}
							            }
										if ($i_formula > 0) {
								             $myxls->write($i_formula, $j,   $SUM_1, $formath2);
								             $myxls->write($i_formula, $j+1, $SUM_2, $formath2);
							            }
										$ALLSUM_1 += $SUM_1;
						                $ALLSUM_2 += $SUM_2;
							            $myxls->write(3, $j,   $ALLSUM_1, $formath2);
							            $myxls->write(3, $j+1, $ALLSUM_2, $formath2);
						break;
						case 'bkp_f':
						case 'bkp_zp':
						case 'bkp_kbo':
						case 'rating_1':						
						case 'rating_2':						
							            foreach ($fields as $field)  {
						 		           	switch ($field->edizm)	{
						   		         		case 'null':
														if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
															$myxls->write($i, $j, $arrec[$field->name_field], $formath2);
														} else {
															$myxls->write($i, $j, '0', $formath2);
														}
														$i++;
						     		       		break;
												case 'man': 
							            		case 'item':
							            		case 'ball':
							            		case 'proc':
							            		case 'trub':							            		
            									case 'bool':													
														if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
															$myxls->write($i, $j, $arrec[$field->name_field], $formatp);
														} else {
															$myxls->write($i, $j, '0', $formatp);
														}
														$i++;
												break;		
							            	}
							            }

						break;
						case 'rkp_u':
						case 'rkp_prm_u':
							            $sid = $school->id;
							            $whati = date('n') - $nm;
                                        // echo $whati . '<br>';

							            foreach ($fields as $field)  {

							           	   if ($field->edizm != 'null') 	{
							           	   		$value = '-';
							           	        if (!empty($field->calcfunc) && function_exists($field->calcfunc)) {
		                          	        		$namefunc = $field->calcfunc;
			                                		$value = $namefunc($whati, 'xls');
			                                		$value = switch_edizm ($field, $value, $whati, true);
			                                	}
	 		                                	if (empty($field->calcfunc) && isset($field->name_field))	{
													if (!empty($arrec[$field->name_field])) {
						                                $value = $arrec[$field->name_field];
						                                $value = switch_edizm ($field, $value, $whati, true);
						                            }
				                                }
                                                // print_r($field);
                                                 
												$strwin1251 = $txtl->convert($value, 'utf-8', 'windows-1251');

												if ($field->edizm == 'link')
  	         											$myxls->write_string($i, $j, $strwin1251, $formatp);
  	         									else
  	         											$myxls->write($i, $j, $strwin1251, $formatp);
                                           }
										   $i++;
                                        }
						break;
					}
		        }
            }

            $i = 1;
			$j+= $numtitles;
        }
	}
}


function print_excel_form_rkp_du($rid, $yid, $nm)
{
    global $CFG, $WORKBOOK, $sid;

    $levelmonit = 'school';

    $rkp = 'rkp_du';

    $datefrom = get_date_from_month_year($nm, $yid);

    $txtl = new textlib();

    $razdel = get_record('monit_razdel', 'shortname', $rkp, 'reported', 0);
//    print_r($razdel);
//    print '<hr>';
    $fields = get_records('monit_razdel_field', 'razdelid', $razdel->id);
//    print_r($fields);

	// Creating a worksheet
	$strwin1251 =  $txtl->convert($razdel->name, 'utf-8', 'windows-1251');
    $myxls =& $WORKBOOK->add_worksheet($strwin1251);
    $myxls->fit_to_pages(1, 5);

	$formath1 =& $WORKBOOK->add_format();
	$formath2 =& $WORKBOOK->add_format();
	$formatp =& $WORKBOOK->add_format();

	$formath1->set_size(10);
    $formath1->set_align('center');
    $formath1->set_align('vcenter');
	$formath1->set_color('black');
	$formath1->set_bold(1);
	$formath1->set_italic();
	$formath1->set_text_wrap();
	// $formath1->set_border(2);

	$formath2->set_size(8);
    $formath2->set_align('center');
    $formath2->set_align('vcenter');
	$formath2->set_color('black');
	$formath2->set_bold(1);
	//$formath2->set_italic();
	$formath2->set_border(2);
	$formath2->set_text_wrap();

	$formatp->set_size(8);
    $formatp->set_align('left');
    $formatp->set_align('vcenter');
	$formatp->set_color('black');
	$formatp->set_bold(0);
	$formatp->set_border(1);
	$formatp->set_text_wrap();

    $count_field = count($fields) + 2;

    $rayon = get_record('monit_rayon', 'id', $rid);

    $strtitle = get_string('titleforreportexcelrayon', 'block_monitoring', $razdel->name);
    $strtitle .= "\"$rayon->name\" за ";
	$strtitle .= get_string('nm_'.$nm, 'block_monitoring') . ' ';
    if ($year = get_record('monit_years', 'id', $yid))  {
		$strtitle .= get_string('uchyear', 'block_monitoring', $year->name);
  	}


	$strsql = "SELECT *  FROM {$CFG->prefix}monit_school
			   WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
			   ORDER BY number";
 	if ($schools = get_records_sql($strsql))	{

 		$sid = $schools->id;

        $numrows = count($schools)*2;
       	// $myxls->merge_cells(0, 0, 0, $numcolumn+1);

	    for ($i=1; $i<$numrows; $i++)	{
	       for ($j=0; $j<6; $j++)	{
				$myxls->write_blank($i, $j, $formatp);
	 		}
	    }

/*
		    for ($i=0; $i<$count_field; $i++)	{
		       for ($j=0; $j<5; $j++)	{
					$myxls->write_blank($i,$j,$formatp);
		 		}
		    }
*/

		// Print names of all the fields
		$myxls->set_column(0,0,14);
		$myxls->set_column(1,1,4);
		$myxls->set_column(2,2,28);
		// $myxls->set_column(2,2,20);
		$myxls->set_column(3,3,12);
		$myxls->set_column(4,4,27);
		$myxls->set_column(5,5,27);
		$myxls->set_row(0, 23);

		$strwin1251 =  $txtl->convert($strtitle, 'utf-8', 'windows-1251');

	    $myxls->write_string(0, 0, $strwin1251, $formath1);
		$myxls->merge_cells(0, 0, 0, 5);

		$strwin1251 =  $txtl->convert(get_string('school','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 0, $strwin1251, $formath2);

		$strwin1251 =  $txtl->convert(get_string('symbolnumber','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 1, $strwin1251, $formath2);

		$strwin1251 =  $txtl->convert(get_string('contolmeasure','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 2, $strwin1251, $formath2);

/*
		$strwin1251 =  $txtl->convert(get_string('contoldate','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);
*/
		$strwin1251 =  $txtl->convert(get_string('factdate','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 3, strip_tags($strwin1251), $formath2);

		$strwin1251 =  $txtl->convert(get_string('rekvizitsnormakt','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 4, strip_tags($strwin1251), $formath2);

		$strwin1251 =  $txtl->convert(get_string('hyperlinknormakt','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 5, strip_tags($strwin1251), $formath2);


	    $i = 2; $j = 2;
	    foreach ($schools as $school)  {

	    	$sid = $school->id;
			$strwin1251 = $txtl->convert($school->name, 'utf-8', 'windows-1251');
  	       	$myxls->write_string($i, 0, $strwin1251, $formatp);


		    $tablename = $CFG->prefix . 'monit_school_listforms';
			$strsql = "SELECT * FROM $tablename
			 		   WHERE (shortname='$rkp') and (datemodified=$datefrom) AND rayonid=$rid AND schoolid=$sid";
		    if ($rec = get_record_sql($strsql)) 	{
		    	$form = get_record('monit_form_'.$rkp, 'listformid', $rec->id);
		    	$arform = (array)$form;
		    } else {
		    	$form = NULL;
		    }

			if ($fields)	{
				foreach ($fields as $rfld) 	{
		            $strfld = translitfield($rfld->name_field);
					$strwin1251 = $txtl->convert($strfld, 'utf-8', 'windows-1251');
		  	       	$myxls->write_string($i, 1, $strwin1251, $formatp);
					$strwin1251 = $txtl->convert($rfld->name, 'utf-8', 'windows-1251');
		   	       	$myxls->write_string($i, 2, $strwin1251, $formatp);

					if (isset($arform[$rfld->name_field])) {
						switch($rfld->edizm)  {
							case 'bool': if ($arform[$rfld->name_field] == 1) $strvalue = get_string('yes');
					 					 else if ($arform[$rfld->name_field] == -1) $strvalue = get_string('no');
						 				      else $strvalue = '-';
						 	break;
						 	case 'data':
						 	           	$strvalue = get_rus_format_date($arform[$rfld->name_field]);
						 	break;
						 	case 'expl':
		          				if (!empty($arform[$rfld->name_field]))  {
		          					$temp = $arform[$rfld->name_field] . '||';
		          					list($fact,$rekv,$link) = explode("|", $temp);

					 	           	$strvalue = get_rus_format_date($fact);
									$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
						      	    $myxls->write($i, 3, $strwin1251, $formatp);

									$strwin1251 = $txtl->convert($rekv, 'utf-8', 'windows-1251');
						      	    $myxls->write($i, 4, $strwin1251, $formatp);

									$strwin1251 = $txtl->convert($link, 'utf-8', 'windows-1251');
						      	    $myxls->write_string($i, 5, $strwin1251, $formatp);

		      	           	    	$strvalue = '-';
		          				}
						 	break;
							case 'gets':
							    if ($rfld->name_field == 'f0_8u') {
									$strvalue = get_string('townvillage'.$arform[$rfld->name_field], 'block_monitoring');
								} else {
									$strvalue = get_string($arform[$rfld->name_field], 'block_monitoring');
								}
							break;


						 	default:  $strvalue = $arform[$rfld->name_field];
						}
		       	    } else {
		       	    	$strvalue = '';
		       	    }
					$i++;
				}
				$myxls->merge_cells($i-2, 0, $i-1, 0);
			}
		}
	}
}


?>
