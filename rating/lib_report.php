<?php // $Id: lib_report.php,v 1.6 2012/12/07 06:05:36 shtifanov Exp $


function listbox_type_school($scriptname, $stype, $report='')
{
  global $CFG;
  
  $type_schools = array(); // get_records_sql("select id, name from {$CFG->prefix}monit_school_type");
  $type_schools[5] = 'Начальная общеобразовательная школа';
  $type_schools[6] = 'Основная общеобразовательная школа';
  if ($report == 'r3a') {
        $type_schools[98] = 'Средняя школа, включая школы повышенного уровня (средняя школа с УИОП, гимназия, лицей)';
  } else {
        $type_schools[1] = 'Средняя общеобразовательная школа';
        $type_schools[99] = 'Школа «повышенного» статуса (гимназия, лицей, школа с УИОП и т.п.)';
  }        
 
  $list_type = array();
  $list_type[0] = get_string('selecttypeschool', 'block_monitoring'). ' ...';

  foreach ($type_schools as $id => $type_school) {
		$list_type[$id] = $type_school;
  }
  
  echo '<tr> <td>'.get_string('typeoufull', 'block_monitoring').': </td><td>';
  popup_form($scriptname, $list_type, 'switchstype', $stype, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


function listbox_seat($scriptname, $seat)
{
  // global $CFG;

  $seatmenu = array();
  $seatmenu[0] = 'Выберите тип населенного пункта ...';
  $seatmenu[1] = get_string('typesettlement1', 'block_monitoring');
  $seatmenu[2] = get_string('typesettlement2', 'block_monitoring');

  echo '<tr> <td>'.get_string('typesettlement', 'block_monitoring').': </td><td>';
  popup_form($scriptname, $seatmenu, 'switchmesto', $seat, '', '', '', false);
  echo '</td></tr>';
  return 1;
}


function table_summaryrating($rid, $sid, $yid, $nm, $shortname, $select, $order)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;

    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');

    $table = new stdClass();
    $table->head  = array ($strstatus, $numberf, $strname, $valueofpokazatel);
    $table->align = array ("center", "center", "left", "center");
	$table->width = '90%';
    $table->size = array ('5%', '5%', '90%', '5%');
    $table->columnwidth = array (10, 7, 100, 15);
	$table->class = 'moutable';
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "report_{$rid}_{$shortname}";
    $table->worksheetname = $table->downloadfilename;
	
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $curryid = $yid;

	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_school
				WHERE rayonid = $rid AND isclosing=0 AND yearid=$curryid
				ORDER BY number";	

	$color = 'red';
	if ($schools = get_records_sql($strsql))	{
		
        $schoolsarray = array();
        $schoolsname = array();
        $schoolsmark = array();
        foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	        $schoolsname[$sa->id] = $sa->name;
	        $schoolsmark[$sa->id] = 0;
	    }
	    // $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT id, number, name FROM {$CFG->prefix}monit_rating_criteria
	   			   WHERE $select 
	 		   	   ORDER BY $order";
		if ($criterias = get_records_sql($strsql)) 	{
  			$criteriaids = array();
	   		foreach($criterias as $criteria)	{
	   			$criteriaids[] = $criteria->id;
		  	}
	    	// $criterialist = implode(',', $criteriaids);
		}  	


//		$strsql = "SELECT id, schoolid, mark FROM {$CFG->prefix}monit_rating_school
//		 		   WHERE (schoolid in ($schoolslist))  AND criteriaid=$criteriaid";
		
		$strsql = "SELECT id, schoolid, criteriaid, mark FROM {$CFG->prefix}monit_rating_school
		 		   WHERE rayonid = $rid AND yearid=$yid ";		

	    if ($ratschools = get_records_sql($strsql)) 	{
		    foreach ($ratschools as $rs)  {
		    	if (in_array($rs->criteriaid, $criteriaids))	{ 
		            $schoolsmark[$rs->schoolid] += $rs->mark;
		        }    
		    }
		}
        
		arsort($schoolsmark);
		reset($schoolsmark);
		$maxmark = current($schoolsmark);
		// echo $maxsm; 
		$placerating = array();
		$mesto = 1;
		foreach ($schoolsmark as $schoolid => $schoolmark) {
			// if ($schoolmark > 0) {
				if ($schoolmark == $maxmark)	{
					$placerating[$schoolid] = $mesto;
				} else {
					$placerating[$schoolid] = ++$mesto;
					$maxmark = $schoolmark; 
				}	 
			/* } else {
				$placerating[$schoolid] = '-';
			}*/
		}	
			
 	
		foreach ($schoolsmark as $schoolid => $schoolmark) {
			$schoolname = $schoolsname[$schoolid];
			$schoolname = "<strong>$schoolname</strong></a>";
			$mesto = '<b><i>'.$placerating[$schoolid] . '</i></b>';
			// $mesto = $placerating[$schoolid];
			// if ($schoolmark >= 0)	{
			   $strmark = "<b><font color=green>$schoolmark</font></b>";	
			/*} else {
			   $strmark = "<b><font color=red>-</font></b>";	
			}*/
			
	    	$strformrkpu_status = get_string("status1","block_monitoring");			
			$strcolor = get_string("status1color","block_monitoring");
		
			if ($rec = get_record_select('monit_rating_listforms', " (schoolid=$schoolid) and (shortname='$shortname') and (datemodified=$datefrom) ", 'id, status'))	{
				$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
				$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");	
			}
	 	
	 		if ($shortname == 'rating_9')	{
		    	$strformrkpu_status = '-';			
				$strcolor = 'FFFFFF';
			}	
	 			
		    $table->data[] = array ($strformrkpu_status, $mesto, $schoolname , $strmark);
		    $table->bgcolor[] = array ($strcolor);
		}    
	}
	
	return $table;
}


function table_summaryratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $order)
{
    global $CFG, $itogmark; 
    
    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('rayon', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');

    $table = new stdClass();
    $table->head  = array ($strstatus, $numberf, $strname, $valueofpokazatel);
    $table->align = array ("center", "center", "left", "center");
	$table->width = '90%';
    $table->size = array ('5%', '5%', '90%', '5%');
    $table->columnwidth = array (10, 7, 100, 15);
	$table->class = 'moutable';
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "rayonreport_{$rid}_{$shortname}";
    $table->worksheetname = $table->downloadfilename;
  
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $curryid = $yid;

    $strsql = "SELECT id, number, name, formula, edizm, indicator, ordering 
			   FROM {$CFG->prefix}monit_rating_criteria
    		   WHERE  yearid = 6 AND gradelevel = 5
			   ORDER BY id";

	if ($criterias = get_records_sql($strsql)) 	{
        // print_object($criterias);
    	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_rayon
    				WHERE number < 100
    				ORDER BY number";	
    
    	if ($rayons = get_records_sql($strsql))	{
    	   
           $rayonsmarks = array();
           $rayonnames = array();
           foreach ($rayons as $rayon)  {
                $rayonsmarks[$rayon->id] = 0;
       	        $rayonnames[$rayon->id] = $rayon->name;
           } 
  
	   
           foreach ($rayons as $rayon)  {
            
                $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
                   		   WHERE (rayonid=$rayon->id) and (shortname='rating_r') and (datemodified=$datefrom)";
             	
            	$arr_df = array();
            	if ($rec = get_record_sql($strsql))	{
             		$fid = $rec->id;
               		if ($df = get_record_sql("SELECT * FROM {$CFG->prefix}monit_form_rating_r WHERE listformid=$fid"))	{
               			$arr_df = (array)$df;
            			// print_object($arr_df);   	
               		}
               	}
                   
                $totalsum = $itogmark = 0;   
           		foreach($criterias as $criteria)	{
        			$color = 'red';// get_string('status1color', 'block_monitoring');
        			$strmark = "<b><font color=\"$color\">0</font></b>";
        
           			if ($criteria->formula == 'null')	{
        				$criterianumber = '<b>'. $criteria->number . '</b>';
        				eval("\$criterianame = \"$criteria->name\";");
           				$criterianame = '<b>'.$criterianame.'</b>';   			
           				$criteriaformula = '';
        				$strmark = ''; 
           			} else {
           				// $criterianame = $criteria->name;
           				eval("\$criterianame = \"$criteria->name\";");
           				$criterianumber = $criteria->number;
        				$operands = explode('#', $criteria->formula);
        				// echo $criteria->formula . '<br>';
        				// print_r($operands); echo '<br>';
        				$o1 = trim($operands[0]);
        				$o2 = trim($operands[1]);
        				$criteriaformula = '<i>'.translitfield('f'.$operands[1]) . '</i>';
        
        				if (!empty($arr_df))	{
        				    if ($o1 == 'func_p140___') {
        				        $strmark = func_p140__($o2, $criteria->indicator, $arr_df, $criteria->ordering);
        				    } else  if ($o1 == 'func_p136') {
        				        $fo_1 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_o', 'fo_1');
                                $fs_9 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_s', 'fs_9');
        	               		$strmark = func_p136($fo_1+$fs_9, $arr_df);
        	               		// echo "$totalsum += $itogmark;<br>" . $strmark . '<br>';
        	               		$totalsum += $itogmark;
        				        
        				    } else if ($o1 == 'func_p137') {
                                $fs_9 = get_summa_field_for_all_rayon($rid, $datefrom, 'rating_s', 'fs_9');
        	               		$strmark = func_p137($fs_9, $arr_df);
        	               		// echo "$totalsum += $itogmark;<br>" . $strmark . '<br>';
        	               		$totalsum += $itogmark;
                            
                            } else if (function_exists($o1)) {
        	               		$namefunc = $o1;
        	               		$strmark = $namefunc($o2, $criteria->indicator, $arr_df, $criteria->ordering);
        	               		// echo "$totalsum += $itogmark;<br>" . $strmark . '<br>';
        	               		$totalsum += $itogmark;
        	               		// echo "$totalsum<hr>";
        					}
        				} else {
        					$strmark = '-';
        				}	
        			}	   
        		} // foreach criteria
                
		        $rayonsmarks[$rayon->id] = $totalsum;
                if ($rec)   {
      	             set_field('monit_rayon_listforms', 'rating_r', $totalsum, 'id', $rec->id);
                }                    
                
           } // foreach rayon
           //print_object($rayonsmarks);

    		arsort($rayonsmarks);
    		reset($rayonsmarks);
    		$maxmark = current($rayonsmarks);
    		// echo $maxsm; 
    		$placerating = array();
    		$mesto = 1;
    		foreach ($rayonsmarks as $rayonid => $rayonmark) {
    			if ($rayonmark == $maxmark)	{
    				$placerating[$rayonid] = $mesto;
    			} else {
    				$placerating[$rayonid] = ++$mesto;
    				$maxmark = $rayonmark; 
    			}	 
    		}	
            
    		foreach ($rayonsmarks as $rayonid => $rayonmark) {
    			$rayonname = $rayonnames[$rayonid];
    			$rayonname = "<strong>$rayonname</strong></a>";
    			$mesto = '<b><i>'.$placerating[$rayonid] . '</i></b>';
    			// $mesto = $placerating[$schoolid];
    			// if ($schoolmark >= 0)	{
    			   $strmark = "<b><font color=green>$rayonmark</font></b>";	
    			/*} else {
    			   $strmark = "<b><font color=red>-</font></b>";	
    			}*/
    			
    	    	$strformrkpu_status = get_string("status1","block_monitoring");			
    			$strcolor = get_string("status1color","block_monitoring");

    			if ($rec = get_record_select('monit_rayon_listforms', " (rayonid=$rayonid) and (shortname='rating_r') and (datemodified=$datefrom) ", 'id, status'))	{
    				$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
    				$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");	
    			}
    	 	/*
    	 		if ($shortname == 'rating_9')	{
    		    	$strformrkpu_status = '-';			
    				$strcolor = 'FFFFFF';
    			}	
    	 	*/		
    		    $table->data[] = array ($strformrkpu_status, $mesto, $rayonname , $strmark);
    		    $table->bgcolor[] = array ($strcolor);
    		}    
            
            
    	   
    	}   
    }
    
    return $table;
}


function table_summaryrating_with_k($rid, $sid, $yid, $nm, $shortname, $select, $order)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;

    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');
    $valueofpokazatel_k = 'Кадровые условия';

    $table = new stdClass();
    $table->head  = array ($strstatus, $numberf, $strname, $valueofpokazatel, $valueofpokazatel_k, 'Всего');
    $table->align = array ("center", "center", "left", "center", 'center', 'center');
	$table->width = '90%';
    $table->size = array ('5%', '5%', '90%', '5%', '5%', '5%');
    $table->columnwidth = array (10, 7, 100, 15, 15, 15);
	$table->class = 'moutable';
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "reportkadri_{$rid}_{$shortname}";
    $table->worksheetname = $table->downloadfilename;
	
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $curryid = $yid;

	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_school
				WHERE rayonid = $rid AND isclosing=0 AND yearid=$curryid
				ORDER BY number";	

	$color = 'red';
	if ($schools = get_records_sql($strsql))	{
		
        $schoolsarray = array();
        $schoolsname = array();
        $schoolsmark = array();
        $schoolsmark_k = array();
        $schoolsmark_i = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	        $schoolsname[$sa->id] = $sa->name;
	        $schoolsmark[$sa->id] = 0;
	        $schoolsmark_k[$sa->id] = 0;            
            $schoolsmark_i[$sa->id] = 0;
	    }
	    // $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT id, number, name FROM {$CFG->prefix}monit_rating_criteria
	   			   WHERE $select 
	 		   	   ORDER BY $order";
		if ($criterias = get_records_sql($strsql)) 	{
  			$criteriaids = array();
	   		foreach($criterias as $criteria)	{
	   			$criteriaids[] = $criteria->id;
		  	}
	    	// $criterialist = implode(',', $criteriaids);
		}  	

		$strsql = "SELECT id, number, name FROM {$CFG->prefix}monit_rating_criteria
	   			   WHERE yearid = 6 AND gradelevel = 4 
	 		   	   ORDER BY id";
		if ($criterias_k = get_records_sql($strsql)) 	{
  			$criteriaids_k = array();
	   		foreach($criterias_k as $criteria_k)	{
	   			$criteriaids_k[] = $criteria_k->id;
		  	}
	    	// $criterialist = implode(',', $criteriaids);
		}  	

//		$strsql = "SELECT id, schoolid, mark FROM {$CFG->prefix}monit_rating_school
//		 		   WHERE (schoolid in ($schoolslist))  AND criteriaid=$criteriaid";
		
		$strsql = "SELECT id, schoolid, criteriaid, mark FROM {$CFG->prefix}monit_rating_school
		 		   WHERE rayonid = $rid AND yearid=$yid ";		

	    if ($ratschools = get_records_sql($strsql)) 	{
		    foreach ($ratschools as $rs)  {
		    	if (in_array($rs->criteriaid, $criteriaids))	{ 
		            $schoolsmark[$rs->schoolid] += $rs->mark;
		        }    
		    }
           
		    foreach ($ratschools as $rs)  {
		    	if (in_array($rs->criteriaid, $criteriaids_k))	{ 
		            $schoolsmark_k[$rs->schoolid] += $rs->mark;
		        }    
		    }
		}
		
        foreach ($schools as $sa)  { 
            $schoolsmark_i[$sa->id] = $schoolsmark[$sa->id] + $schoolsmark_k[$sa->id]; 
        }    
                            
		arsort($schoolsmark_i);        
		reset($schoolsmark_i);
		$maxmark = current($schoolsmark_i);
		// echo $maxsm; 
		$placerating = array();
		$mesto = 1;
		foreach ($schoolsmark_i as $schoolid => $schoolmark) {
			// if ($schoolmark > 0) {
				if ($schoolmark == $maxmark)	{
					$placerating[$schoolid] = $mesto;
				} else {
					$placerating[$schoolid] = ++$mesto;
					$maxmark = $schoolmark; 
				}	 
			/* } else {
				$placerating[$schoolid] = '-';
			}*/
		}	
			
 	
		foreach ($schoolsmark_i as $schoolid => $schoolmark) {
			$schoolname = $schoolsname[$schoolid];
			$schoolname = "<strong>$schoolname</strong></a>";
			$mesto = '<b><i>'.$placerating[$schoolid] . '</i></b>';
			// $mesto = $placerating[$schoolid];
			// if ($schoolmark >= 0)	{
			$strmark = 	 "<b><font color=green>{$schoolsmark[$schoolid]}</font></b>";
            $strmark_k = "<b><font color=green>{$schoolsmark_k[$schoolid]}</font></b>";
            $strmark_i = "<b><font color=green>$schoolmark</font></b>"; 
			/*} else {
			   $strmark = "<b><font color=red>-</font></b>";	
			}*/
			
	    	$strformrkpu_status = get_string("status1","block_monitoring");			
			$strcolor = get_string("status1color","block_monitoring");
		
			if ($rec = get_record_select('monit_rating_listforms', " (schoolid=$schoolid) and (shortname='$shortname') and (datemodified=$datefrom) ", 'id, status'))	{
				$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
				$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");	
			}
	 	
	 		if ($shortname == 'rating_9')	{
		    	$strformrkpu_status = '-';			
				$strcolor = 'FFFFFF';
			}	

            if ($shortname == 'rating_k')   {	 			
		      $table->data[] = array ($strformrkpu_status, $mesto, $schoolname , '', $strmark_k, '');
            } else {
              $table->data[] = array ($strformrkpu_status, $mesto, $schoolname , $strmark, $strmark_k, $strmark_i);  
            }  
		    $table->bgcolor[] = array ($strcolor);
		}    
	}
	
	return $table;
}


function table_summaryrating_delta($rid, $sid, $yid, $nm, $shortname, $select, $order)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $schoolids;
    
    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring') . '(текущий год)';
    $valueofpokazatel_k = 'Кадровые условия (текущий год)';
    $valueofpokazatel0 = get_string('valueofpokazatel', 'block_monitoring') . '(предыдущий год)';
    $valueofpokazatel_k0 = 'Кадровые условия (предыдущий год)';
/*
    $table->head  = array ($strstatus, $numberf, $strname, 
                            $valueofpokazatel, $valueofpokazatel_k, 'Всего (текущий год)', 
                            $valueofpokazatel0, $valueofpokazatel_k0, 'Всего (предыдущий год)',
                            get_string ('dynamicvalue', 'block_monitoring'));
    $table->align = array ("center", "center", "left", 
                            "center", 'center', 'center',
                            "center", 'center', 'center', 'center');
	$table->width = '90%';
    // $table->size = array ('5%', '5%', '90%', '5%', '5%', '5%');
    $table->columnwidth = array (10, 7, 100, 15, 15, 15, 15, 15, 15, 15);
	$table->class = 'moutable';
*/	

    $table = new stdClass();
    $table->head  = array ($strstatus, $numberf, $strname, 
                            $valueofpokazatel, $valueofpokazatel0, get_string ('dynamicvalue', 'block_monitoring'));
    $table->align = array ("center", "center", "left", 
                            "center", "center", 'center');
	$table->width = '90%';
    $table->columnwidth = array (10, 7, 100, 15, 15, 15);
	$table->class = 'moutable';

   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "deltareport_{$rid}_{$shortname}";
    $table->worksheetname = $table->downloadfilename;
	
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $prevyid = $yid - 1;
    $curryid = $yid;
    $schoolsids = array();
    $schoolsname = array();
    $schoolsmark = array();
    
    // берем имена форм текущего учебного года
    $shortnames = get_listnameforms($curryid, 'school');
    $fieldsn = implode (',', $shortnames);
    
    if ($curryid == 9)  {
        $name_rating_k = 'rating_9_k';
    } else {
        $name_rating_k = 'rating_k';
    }     
    for ($y = $prevyid; $y <= $curryid; $y++)   {
        
        $strsql =  "SELECT r.id as sid, r.rayonid, r.schoolid, s.uniqueconstcode, s.name, $fieldsn
                    FROM mdl_monit_rating_total r INNER JOIN mdl_monit_school s on s.id=r.schoolid
                    where r.rayonid=$rid AND r.yearid=$y and s.isclosing=0
                    order by s.uniqueconstcode, r.schoolid";
        // echo '<br>' . $shortname . '<br>' . $strsql;             	
    	if ($schools = get_records_sql($strsql))	{
            $schoolsname[$y] = array();
            $schoolsmark[$y] = array();
    	    foreach ($schools as $sa)  {
    	        $schoolsids[$y][$sa->uniqueconstcode] = new stdClass();
    	        $schoolsids[$y][$sa->uniqueconstcode] = $sa->sid;
                
                $schoolsname[$y][$sa->uniqueconstcode] = new stdClass();
    	        $schoolsname[$y][$sa->uniqueconstcode] = $sa->name;
                
                $schoolsmark[$y][$sa->uniqueconstcode] = new stdClass();
                foreach ($shortnames as $sn) {
                   // $oldsn = str_ireplace('_9', '', $sn); 
    	           $schoolsmark[$y][$sa->uniqueconstcode]->{$sn} = $sa->{$sn};
                } 
    	    }
        }
    }

    /*
    print_object($schoolsids);
    print_object($schoolsname);
    print_object($schoolsmark);
    */            
    
    $mesto = '-';
    $strformrkpu_status = '-';			
	$strcolor = 'FFFFFF';
    $rayonsmark = array();
    // $shortname = str_ireplace('_9', '', $shortname);
    foreach ($schoolsname[$curryid] as $uniqueconstcode => $schoolname) {
            // $sumprev = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$curryid][$uniqueconstcode]->rating_k;
            // $sumcurr = $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$prevyid][$uniqueconstcode]->rating_k;
            // $dynvalues[$uniqueconstcode] = $sumprev - $sumcurr;
            $dynvalues[$uniqueconstcode] = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} - $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname};
            
      	     if ($shortname == 'rating_k')  {
       	        $dynvalues[$uniqueconstcode] = correct_dynamic_value_in_criteriaP115($yid, $uniqueconstcode, $dynvalues[$uniqueconstcode], 517);
       	     }   
 
    }        

 	arsort($dynvalues);
	reset($dynvalues);
	$maxmark = current($dynvalues);
	$placerating = array();
	$mesto = 1;
	foreach ($dynvalues as $uniqueconstcode => $dynvalue) {
		if ($dynvalue == $maxmark)	{
			$placerating[$uniqueconstcode] = $mesto;
		} else {
			$placerating[$uniqueconstcode] = ++$mesto;
			$maxmark = $dynvalue; 
		}	 
	}	

    foreach ($dynvalues as $uniqueconstcode => $dynvalue) {
        $schoolname = $schoolsname[$curryid][$uniqueconstcode];
		$schoolname = "<strong>$schoolname</strong></a>";
		$mesto = '<b><i>'.$placerating[$uniqueconstcode] . '</i></b>';
        $strmark = "<b><font color=green>$dynvalue</font></b>";
            
    	// $strformrkpu_status = get_string("status4","block_monitoring");
        $strformrkpu_status = '';			
		$strcolor = get_string("status5color","block_monitoring");
/*
        $strformrkpu_status = get_string('status'.$lfcurrs[$rayonid]->status, "block_monitoring");
        $strcolor = get_string('status'.$lfcurrs[$rayonid]->status.'color',"block_monitoring");
*/
        $sumprev = $schoolsmark[$curryid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$curryid][$uniqueconstcode]->{$name_rating_k};
        $sumcurr = $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname} +  $schoolsmark[$prevyid][$uniqueconstcode]->{$name_rating_k};
/*
        $table->data[] = array ($strformrkpu_status, $mesto, $schoolname , 
                                $schoolsmark[$curryid][$uniqueconstcode]->{$shortname}, 
                                $schoolsmark[$curryid][$uniqueconstcode]->rating_k, 
                                '<b>'.$sumprev.'</b>',
                                $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname}, 
                                $schoolsmark[$prevyid][$uniqueconstcode]->rating_k, 
                                '<b>'.$sumcurr.'</b>',
                                '<b>'.$dynvalue.'</b>');  
*/
        $table->data[] = array ($strformrkpu_status, $mesto, $schoolname , 
                                $schoolsmark[$curryid][$uniqueconstcode]->{$shortname}, 
                                $schoolsmark[$prevyid][$uniqueconstcode]->{$shortname}, 
                                '<b>'.$dynvalue.'</b>');  

        $table->bgcolor[] = array ($strcolor);
                    
	}    

       
	return $table;
}


function calc_dynamic_value_rayons($yid, $nm, $tablename)
{
    global $CFG, $DB;

	$datefromprev = get_date_from_month_year($nm, $yid-1);
	$datefromcurr = get_date_from_month_year($nm, $yid);
    
	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_rayon
				WHERE number < 100
				ORDER BY number";	

	if ($rayons = get_records_sql($strsql))	{
	   
       foreach ($rayons as $rayon)  {
        
            // echo $rayon->name . '<br>';
            $strsql = "SELECT id, rating_r FROM {$CFG->prefix}monit_rayon_listforms
               		   WHERE (rayonid=$rayon->id) and (shortname='rating_r') and (datemodified=$datefromprev)";
        	$lfprev = get_record_sql($strsql);
            
            $strsql = "SELECT id, rating_r FROM {$CFG->prefix}monit_rayon_listforms
               		   WHERE (rayonid=$rayon->id) and (shortname='rating_r') and (datemodified=$datefromcurr)";
        	$lfcurr = get_record_sql($strsql);
            
            if ($lfprev && $lfcurr) {
                $dynamic = $lfcurr->rating_r - $lfprev->rating_r;
                set_field('monit_rayon_listforms', 'dynamic_rating_r', $dynamic, 'id', $lfcurr->id);
                // echo $dynamic . '<hr>';
            }             
                   
            
            if ($lfcurr) {
                $allsum = calc_sum_dynamic_school($rayon->id, $yid, $tablename);
                $sums = $allsum;// implode(';', $allsum);
                set_field('monit_rayon_listforms', 'sum_dynamic_school', $sums, 'id', $lfcurr->id);
                
                /*
                $strsql = "SELECT count(schoolid) as cntschool FROM mdl_monit_rating_total
                           where rayonid=$rayon->id and yearid=$yid";
                $cnt = get_record_sql($strsql);
                set_field('monit_rayon_listforms', 'numratingschool', $cnt->cntschool, 'id', $lfcurr->id);
                */
           }     
       }
  }          
}



function calc_sum_dynamic_school($rid, $yid, $tablename)
{
    global $CFG;
/*    
    $strsql = "SELECT sum(dynamic_rating_n) as sum1 FROM mdl_monit_rating_total
               where rayonid=$rid and yearid=$yid";
    $sumn = get_record_sql($strsql);
    $strsql = "SELECT sum(dynamic_rating_o) as sum1 FROM mdl_monit_rating_total
               where rayonid=$rid and yearid=$yid";
    $sumo = get_record_sql($strsql);
    $strsql = "SELECT sum(dynamic_rating_s) as sum1 FROM mdl_monit_rating_total
               where rayonid=$rid and yearid=$yid";
    $sums = get_record_sql($strsql);
    
    $allsum = $sumn->sum1 + $sumo->sum1 + $sums->sum1;
*/

    // $type_schools[5] = 'Начальная общеобразовательная школа';
    // $type_schools[6] = 'Основная общеобразовательная школа';
	// $type_schools[1] = 'Средняя общеобразовательная школа';
    // $type_schools[99] = 'Школа «повышенного» статуса (гимназия, лицей, школа с УИОП и т.п.)'; 'stateinstitution in (2,3,4)';  


/*
    $params = array();
    $params[0]->dynamic = array('dynamic_rating_n', 'dynamic_rating_o', 'dynamic_rating_s', 'dynamic_rating_k');
    $params[0]->strwhere = 'stateinstitution in (1,2,3,4)';
    $params[1]->dynamic = array('dynamic_rating_n', 'dynamic_rating_o', 'dynamic_rating_k');
    $params[1]->strwhere = 'stateinstitution = 6';
    $params[2]->dynamic = array('dynamic_rating_n', 'dynamic_rating_k');
    $params[2]->strwhere = 'stateinstitution = 5';
    
    
    $allsum = array();
    foreach ($params as $i => $param) {
        $strsql =  "SELECT id FROM {$CFG->prefix}monit_school
	   			   WHERE rayonid=$rid AND yearid=$yid AND {$param->strwhere}";	

    	if ($schools = get_records_sql($strsql))	{
            $schoolsarray = array();
    	    foreach ($schools as $sa)  {
    	        $schoolsarray[] = $sa->id;
    	    }
    	    $schoolslist = implode(',', $schoolsarray);
        }

        $totalsum = 0;
        $cntdyn = count($param->dynamic);        
        foreach ($param->dynamic as $dynam) {
            $strsql = "SELECT sum($dynam) as summa FROM mdl_monit_rating_total
                       where schoolid in ($schoolslist)";
            $s = get_record_sql($strsql);
            $totalsum += $s->summa;
        }
        $allsum[$i] = round($totalsum/$cntdyn, 4); 
   }      
*/


    $params = array();
    $params[0] = new stdClass();
    $params[0]->dynamic = array('dynamic_rating_n', 'dynamic_rating_o', 'dynamic_rating_s', 'dynamic_rating_k');
    $params[0]->strwhere = array (1,2,3,4);
    $params[1] = new stdClass();
    $params[1]->dynamic = array('dynamic_rating_n', 'dynamic_rating_o', 'dynamic_rating_k');
    $params[1]->strwhere = array (6);
    $params[2] = new stdClass();
    $params[2]->dynamic = array('dynamic_rating_n', 'dynamic_rating_k');
    $params[2]->strwhere = array (5);
       
    $strsql =  "SELECT r.id as sid, r.rayonid, schoolid, rating_n, rating_o, rating_s, rating_k, 
                       dynamic_rating_n, dynamic_rating_o, dynamic_rating_s, dynamic_rating_k,        
                       s.uniqueconstcode, s.name, s.stateinstitution
                FROM mdl_{$tablename} r INNER JOIN mdl_monit_school s on s.id=r.schoolid
                where r.rayonid=$rid AND r.yearid=$yid and s.isclosing=0
                order by s.name";
    // echo $strsql;
    $allsum = 0;             	
    if ($schools = get_records_sql($strsql))	{
        foreach ($schools as $sa)  {
            foreach ($params as $i => $param) {
                if (in_array($sa->stateinstitution, $param->strwhere))   {
                    $totalsum = 0;
                    $cntdyn = count($param->dynamic);        
                    foreach ($param->dynamic as $dynam) {
                        $totalsum += $sa->{$dynam};
                    }
                    $avg = round($totalsum/$cntdyn, 4);
                    $allsum += $avg;
                    break;
                }
            } 
       }
    }            
    
    return $allsum; 
}                


function table_summary_rating_rayon($yid, $nm)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $schoolids;

    // $shortnames = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
    $shortnames = get_listnameforms($yid, 'school');
    
    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('rayon', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring') . '(текущий год)';
    $valueofpokazatel0 = get_string('valueofpokazatel', 'block_monitoring') . '(предыдущий год)';

    $table = new stdClass();
    $table->head  = array ($strstatus, $numberf, $strname, 
                           $valueofpokazatel,  $valueofpokazatel0, get_string ('dynamicvalue', 'block_monitoring'),
                           'Сумма динамических показателей школ муниципалитета',
                           'Кол-во школ в муниципалитете, участвующих в рейтинге',
                           'Средний динамический показатель школ муниципалитета',
                           'Интегрированный динамический показатель муниципалитета');
    $table->align = array ("center", "center", "left", 
                            "center", 'center', 'center',
                            "center", 'center', 'center', 'center');
	$table->width = '90%';
    // $table->size = array ('5%', '5%', '90%', '5%', '5%', '5%');
    $table->columnwidth = array (10, 7, 100, 15, 15, 15, 15, 15, 15, 15);
	$table->class = 'moutable';
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_rating_r', 'block_monitoring');
    $table->downloadfilename = "summaryreport_rayons_$yid";
    $table->worksheetname = $table->downloadfilename;
	
	$datefromprev = get_date_from_month_year($nm, $yid-1);
	$datefromcurr = get_date_from_month_year($nm, $yid);
    
	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_rayon
				WHERE number < 100
				ORDER BY number";	
	if ($rayons = get_records_sql($strsql))	{

       $rayonsname = array();
       $rayonsmark = array();
       $lfprevs = array();
       $lfcurrs = array(); 
       $avg = array();
       $sumdynamic = array();
       foreach ($rayons as $rayon)  {
          $rayonsname[$rayon->id] = $rayon->name;
          $rayonsmark[$rayon->id] = 0;
       }
       
	   
       foreach ($rayons as $rayon)  {
            $strsql = "SELECT id, rating_r FROM {$CFG->prefix}monit_rayon_listforms
               		   WHERE (rayonid=$rayon->id) and (shortname='rating_r') and (datemodified=$datefromprev)";
        	$lfprevs[$rayon->id] = get_record_sql($strsql);

            $strsql = "SELECT id, status, rating_r, dynamic_rating_r, sum_dynamic_school, numratingschool 
                       FROM {$CFG->prefix}monit_rayon_listforms
               		   WHERE (rayonid=$rayon->id) and (shortname='rating_r') and (datemodified=$datefromcurr)";
        	$lfcurrs[$rayon->id]  = get_record_sql($strsql);
            
            if ($lfcurrs[$rayon->id] ) {
                // $avg[$rayon->id] = round($lfcurrs[$rayon->id]->sum_dynamic_school / $lfcurrs[$rayon->id]->numratingschool, 4);
                $adynamic = explode (';', $lfcurrs[$rayon->id]->sum_dynamic_school);
                $sumdynamic[$rayon->id] = array_sum($adynamic);
                if ($lfcurrs[$rayon->id]->numratingschool > 0)  {
                    $avg[$rayon->id] = round($sumdynamic[$rayon->id]/$lfcurrs[$rayon->id]->numratingschool, 4);
                } else {
                    $avg[$rayon->id] = '-';
                }                    
                $rayonsmark[$rayon->id] = round ($avg[$rayon->id] + $lfcurrs[$rayon->id]->dynamic_rating_r, 4);
           }  else {
                $avg[$rayon->id] = 0;
                $rayonsmark[$rayon->id] = 0;
            
           }   
       }
       
       
     	arsort($rayonsmark);
		reset($rayonsmark);
		$maxmark = current($rayonsmark);
		$placerating = array();
		$mesto = 1;
		foreach ($rayonsmark as $rayonid => $rayonmark) {
			if ($rayonmark == $maxmark)	{
				$placerating[$rayonid] = $mesto;
			} else {
				$placerating[$rayonid] = ++$mesto;
				$maxmark = $rayonmark; 
			}	 
		}	

		foreach ($rayonsmark as $rayonid => $rayonmark) {
			$rayonname = $rayonsname[$rayonid];
			$rayonname = "<strong>$rayonname</strong></a>";
			$mesto = '<b><i>'.$placerating[$rayonid] . '</i></b>';
		    $strmark = "<b><font color=green>$rayonmark</font></b>";
            
   	    	$strformrkpu_status = get_string("status1","block_monitoring");			
			$strcolor = get_string("status1color","block_monitoring");
            
            if ($lfprevs[$rayonid]) {
                $lfprevsrating_r = $lfprevs[$rayonid]->rating_r;    
            } else {
                $lfprevsrating_r = 0;
            }
	
            if ($lfcurrs[$rayonid]) {
                $strformrkpu_status = get_string('status'.$lfcurrs[$rayonid]->status, "block_monitoring");
                $strcolor = get_string('status'.$lfcurrs[$rayonid]->status.'color',"block_monitoring");
                $table->data[] = array ($strformrkpu_status, $mesto, $rayonname, 
                                     $lfcurrs[$rayonid]->rating_r, $lfprevsrating_r, 
                                     '<b>' . $lfcurrs[$rayonid]->dynamic_rating_r . '</b>' , 
                                     $sumdynamic[$rayonid], //  . '<br><small>'.$lfcurrs[$rayonid]->sum_dynamic_school.'</small>', 
                                     $lfcurrs[$rayonid]->numratingschool, 
                                     '<b>' . $avg[$rayonid] . '<b>', 
                                     '<b>' . $rayonmark . '<b>');      
            } else {
                $table->data[] = array ($strformrkpu_status, $mesto, $rayonname, 
                                     '-', $lfprevsrating_r, '-', '-', '-', '-', $rayonmark);      
                
            }
		    $table->bgcolor[] = array ($strcolor);
		}    
  }          
  return $table;
}


?>