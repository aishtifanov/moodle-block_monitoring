<?php // $Id: lib_excel.php,v 1.18 2012/10/29 13:49:35 shtifanov Exp $

	require_once("$CFG->libdir/excel/Worksheet.php");
	require_once("$CFG->libdir/excel/Workbook.php");
	require_once("$CFG->libdir/textlib.class.php");

	require_once('indices/lib_indices_school.php');

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

function print_excel_form($rkp, $datefrom, $levelmonit, $rid=0, $sid=0, $yid=9, $level2 = '', $isdou = false, $isspo=false)
{
    global $CFG, $WORKBOOK, $nm, $yid, $typeou;

	  $yearedus = get_records('monit_years');
	  $arryearedus[-1] = '2005/2006';
	  $arryearedus[0]  = '2006/2007';
	  foreach ($yearedus as $yearedu)	{
	  	$arryearedus[$yearedu->id] = $yearedu->name;
	  }
	  $a = $arryearedus[$yid-1];
	  $b = $arryearedus[$yid-2];

    $txtl = new textlib();
    
    if  ($levelmonit == 'zp')   {
        $tablename = $CFG->prefix . 'monit_rayon_listforms';        
    } else {
        $tablename = $CFG->prefix . 'monit_'.$levelmonit.'_listforms';
    }


	$strsql = "SELECT * FROM $tablename
	 		   WHERE (shortname='$rkp') and (datemodified=$datefrom) ";
   
	if ($rid!=0 && $sid==0)  {
	    $rayon = get_record('monit_rayon', 'id', $rid);
		$strsql .= " AND rayonid=$rid ";
	}

	if ($sid != 0)  {
	    $rayon = get_record('monit_rayon', 'id', $rid);
	    if ($isdou)    {
	        if ($typeou == '03')   {
    	      $school = get_record('monit_school', 'id', $sid);
    		  $strsql .= " AND schoolid=$sid ";
	        } else {
    	      $school = get_record('monit_education', 'id', $sid);
    		  $strsql .= " AND douid=$sid ";
            }  
	       
	    } else if ($isspo) {
    	    $school = get_record('monit_college', 'id', $sid);
    		$strsql .= " AND collegeid=$sid ";
	       
	    } else  {
    	    $school = get_record('monit_school', 'id', $sid);
    		$strsql .= " AND schoolid=$sid ";
	    }
	}
    
    // echo $strsql; 
    if ($rec = get_record_sql($strsql)) 	{
    	$form = get_record('monit_form_'.$rkp, 'listformid', $rec->id);
    	$arform = (array)$form;
    } else {
    	$form = NULL;
    }

    $razdel = get_record('monit_razdel', 'shortname', $rkp, 'reported', 0);
//    print_r($razdel);
//    print '<hr>';
    if ($isdou || $isspo)    {
        $sql = "SELECT rf.* FROM mdl_monit_razdel_field rf
               inner join mdl_monit_razdel_field_year rfy on rf.id=rfy.razdelfieldid
               where rfy.yearid=$yid and rf.razdelid=$razdel->id";
    	$fields = get_records_sql($sql);
    } else {
       $fields = get_records('monit_razdel_field', 'razdelid', $razdel->id); 
    }    
    
//    print_r($fields);

	// Creating a worksheet
	if (mb_strlen($razdel->name, 'UTF-8') > 15)	{
		$razdelname = mb_substr($razdel->name, 0,  15, 'UTF-8') . ' ...'; 
	}  else {
		$razdelname = $razdel->name;	
	}
	
	if ($levelmonit == 'rating' || $levelmonit == 'zp')	{
		$razdelname = $rkp;
	}	
	
	$strwin1251 =  $txtl->convert($razdelname, 'utf-8', 'windows-1251');
    $myxls =& $WORKBOOK->add_worksheet($strwin1251);
    $myxls->fit_to_pages(1, 5);

	$formath1 =& $WORKBOOK->add_format();
	$formath2 =& $WORKBOOK->add_format();
	$formatp =& $WORKBOOK->add_format();

	$formath1->set_size(12);
    $formath1->set_align('center');
    $formath1->set_align('vcenter');
	$formath1->set_color('black');
	$formath1->set_bold(1);
	$formath1->set_italic();
	$formath1->set_text_wrap();
	// $formath1->set_border(2);

	$formath2->set_size(11);
    $formath2->set_align('center');
    $formath2->set_align('vcenter');
	$formath2->set_color('black');
	$formath2->set_bold(1);
	//$formath2->set_italic();
	$formath2->set_border(2);
	$formath2->set_text_wrap();

	$formatp->set_size(11);
    $formatp->set_align('left');
    $formatp->set_align('vcenter');
	$formatp->set_color('black');
	$formatp->set_bold(0);
	$formatp->set_border(1);
	$formatp->set_text_wrap();

    $count_field = count($fields) + 2;

    $strtitle = get_string('titleforexcel'.$levelmonit, 'block_monitoring', $razdel->name);

    switch ($levelmonit) {
        case 'zp': $strtitle = $razdel->name . '. ('; 
        // break;
    	case 'rayon':  $strtitle .= $rayon->name;
    	break;
    	case 'school':  $strtitle .= $school->name . ', '. $rayon->name;
    	break;
        case 'rating': $strtitle .= $school->name;
    	break;
    }

	// $strtitle .= ' по состоянию на ' . get_rus_format_date(time());

	if ($levelmonit <> 'rating' && $level2 == '')	{
		$strtitle .= ' по состоянию на ' . get_string('nm_'.$nm, 'block_monitoring') . ' ';;
	    if ($year = get_record('monit_years', 'id', $yid))  {
			$strtitle .= get_string('uchyear', 'block_monitoring', $year->name);
	  	}
	} else if ($levelmonit == 'rayon' && $level2 == 'ratingrayon')	{
		$strtitle .= ' по состоянию на ';
		$strtitle .= get_string('uchyear', 'block_monitoring', $arryearedus[$yid-1]);
    }	   
    
    if ($levelmonit == 'zp') {
        $strtitle .= ')';
    }    

    if ($rkp == 'rkp_d' || $rkp == 'rkp_du')  {

	    for ($i=0; $i<$count_field; $i++)	{
	       for ($j=0; $j<5; $j++)	{
				$myxls->write_blank($i,$j,$formatp);
	 		}
	    }

		// Print names of all the fields
		$myxls->set_column(0,0,9);
		$myxls->set_column(1,1,30);
		// $myxls->set_column(2,2,20);
		$myxls->set_column(2,2,20);
		$myxls->set_column(3,3,30);
		$myxls->set_column(4,4,30);
 	    $myxls->set_row(0, 70);            
            
		$strwin1251 =  $txtl->convert($strtitle, 'utf-8', 'windows-1251');

	    $myxls->write_string(0, 0, $strwin1251, $formath1);
		$myxls->merge_cells(0, 0, 0, 4);

		$strwin1251 =  $txtl->convert(get_string('symbolnumber','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 0, $strwin1251, $formath2);

		$strwin1251 =  $txtl->convert(get_string('contolmeasure','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 1, $strwin1251, $formath2);

/*
		$strwin1251 =  $txtl->convert(get_string('contoldate','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);
*/
		$strwin1251 =  $txtl->convert(get_string('factdate','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);

		$strwin1251 =  $txtl->convert(get_string('rekvizitsnormakt','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 3, strip_tags($strwin1251), $formath2);

		$strwin1251 =  $txtl->convert(get_string('hyperlinknormakt','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 4, strip_tags($strwin1251), $formath2);

    } else {
        $numcolumn = 3;
        if ($rkp == 'bkp_dolj') {
        	$numcolumn = 4;
        } else if ($rkp == 'bkp_pred')  {
        	$numcolumn = 5;
        }

	    for ($i=0; $i<$count_field; $i++)	{
	       for ($j=0; $j<$numcolumn; $j++)	{
				$myxls->write_blank($i, $j, $formatp);
	 		}
	    }

		// Print names of all the fields
		$myxls->set_column(0,0,9);
		$myxls->set_column(2,2,20);
		$myxls->set_row(0, 70);

        if ($levelmonit == 'zp') {
           $myxls->set_column(1,1,67);
           $myxls->set_row(0, 70);
        } else {
		   $myxls->set_column(1,1,100);
		   $myxls->set_row(0, 70);            
        }

		$strwin1251 =  $txtl->convert($strtitle, 'utf-8', 'windows-1251');

	    $myxls->write_string(0, 0, $strwin1251, $formath1);
		$myxls->merge_cells(0, 0, 0, 2);

		$strwin1251 =  $txtl->convert(get_string('symbolnumber','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 0, $strwin1251, $formath2);


		$strwin1251 =  $txtl->convert(get_string('nameofpokazatel','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 1, $strwin1251, $formath2);

		$strwin1251 =  $txtl->convert(get_string('valueofpokazatel','block_monitoring'), 'utf-8', 'windows-1251');
	    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);

		if ($rkp == 'bkp_dolj') {
			$strwin1251 =  $txtl->convert(get_string('kolphisperson','block_monitoring'), 'utf-8', 'windows-1251');
		    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);
			$strwin1251 =  $txtl->convert(get_string('kolstavok','block_monitoring'), 'utf-8', 'windows-1251');
		    $myxls->write_string(1, 3, strip_tags($strwin1251), $formath2);
    		$myxls->merge_cells(0, 0, 0, 3);
			$myxls->set_column(3,3,12);
		}

		if ($rkp == 'bkp_pred') {
			$strwin1251 =  $txtl->convert(get_string('kolphislicosndolj','block_monitoring'), 'utf-8', 'windows-1251');
		    $myxls->write_string(1, 2, strip_tags($strwin1251), $formath2);
			$strwin1251 =  $txtl->convert(get_string('kolphislicsovdolj','block_monitoring'), 'utf-8', 'windows-1251');
		    $myxls->write_string(1, 3, strip_tags($strwin1251), $formath2);
			$strwin1251 =  $txtl->convert(get_string('kolphisliccondolj','block_monitoring'), 'utf-8', 'windows-1251');
		    $myxls->write_string(1, 4, strip_tags($strwin1251), $formath2);
    		$myxls->merge_cells(0, 0, 0, 4);
			$myxls->set_column(3,3,12);
			$myxls->set_column(4,4,12);
		}
    }

	if ($fields)	{
        $i = 1;
		foreach ($fields as $rfld) 	{
			$i++;
            $strfld = translitfield($rfld->name_field);
            if ($levelmonit == 'rating')	$strfld = 'ф'.$strfld;
            if ($strfld == 'фн.')  $strfld = '';
			$strwin1251 = $txtl->convert($strfld, 'utf-8', 'windows-1251');
  	       	$myxls->write_string($i, 0, $strwin1251, $formatp);
  	       	
  	       	if ($levelmonit == 'rating')	{
  	       		eval("\$fieldname = \"$rfld->name\";");
				$strwin1251 = $txtl->convert($fieldname, 'utf-8', 'windows-1251');  	       		
  	       	} else {
				$strwin1251 = $txtl->convert($rfld->name, 'utf-8', 'windows-1251');
  	       	}
  	       	
  	       	$myxls->write_string($i, 1, $strwin1251, $formatp);
  	       	
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
				      	    $myxls->write($i, 2, $strwin1251, $formatp);

							$strwin1251 = $txtl->convert($rekv, 'utf-8', 'windows-1251');
				      	    $myxls->write($i, 3, $strwin1251, $formatp);

							$strwin1251 = $txtl->convert($link, 'utf-8', 'windows-1251');
				      	    $myxls->write_string($i, 4, $strwin1251, $formatp);

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


				 	default:  
                     $strizm = get_string($rfld->edizm, 'block_monitoring');
                     $strvalue = $arform[$rfld->name_field] . ' ' . $strizm;
				}
       	    } else {
       	    	$strvalue = '';
       	    }

			if ($rkp != 'rkp_d' && $rkp != 'rkp_du')  {
	           	if (!empty($rfld->calcfunc) && function_exists($rfld->calcfunc)) {
			          $namefunc = $rfld->calcfunc;
			          $strvalue = $namefunc(date('n') - $nm);
	 			}

				$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
				if (is_numeric($strwin1251)) {
	           	    $myxls->write($i, 2, $strwin1251, $formatp);
	           	} else {
	           	    $myxls->write_string($i, 2, $strwin1251, $formatp);
	           	}
            }
           	// печать ставок в bkp_dolj
           	if ($rkp == 'bkp_dolj') {
				$fieldname_st = $rfld->name_field . '_st';
				if (isset($arform[$fieldname_st]))  {
 				    $strvalue = $arform[$fieldname_st];
					$myxls->write($i, 3, $strvalue, $formatp);
				}

           	}

           	// печать  bkp_pred
           	if ($rkp == 'bkp_pred') {
				$fieldname_sov = $rfld->name_field . '_sov';
				if (isset($arform[$fieldname_sov]))  {
 				    $strvalue = $arform[$fieldname_sov];
					$myxls->write($i, 3, $strvalue, $formatp);
				}
				$fieldname_con = $rfld->name_field . '_con';
				if (isset($arform[$fieldname_con]))  {
 				    $strvalue = $arform[$fieldname_con];
					$myxls->write($i, 4, $strvalue, $formatp);
				}
           	}

		}

		$formats =& $WORKBOOK->add_format();


		$formats->set_size(10);
	    $formatp->set_align('left');
	    $formatp->set_align('vcenter');
		$formatp->set_color('black');
		$formatp->set_bold(0);
//		$formatp->set_border(0);

		if ($levelmonit <> 'rating')	{
			$i+=3;
			$strvalue = 'Руководитель';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i++;
	        $strvalue = 'организации    _____________________________       _____________________';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i++;
			$strvalue = '                                        (Ф.И.О.)                                   (подпись) ';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i+=3;
			$strvalue = 'Должностное лицо,';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i++;
			$strvalue = 'ответственное за';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i++;
			$strvalue = 'составление формы __________________   _______________________       _____________________';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
	
			$i++;
			$strvalue = '                                      (должность)                        (Ф.И.О.)                              (подпись)';
			$strwin1251 = $txtl->convert($strvalue, 'utf-8', 'windows-1251');
		    $myxls->write($i, 1, $strwin1251, $formats);
		}    

	}
    
}


?>
