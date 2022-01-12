<?php // $Id: listcriteria.php,v 1.18 2012/12/06 12:30:25 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../../mou_ege/lib_ege.php');
    require_once('lib_rating.php');	

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);            // School id
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    $fid = optional_param('fid', 0, PARAM_INT);       // Form id
    $level = optional_param('level', 'school');       // Form id
    $shortname = optional_param('sn', '');

    // $nm  = optional_param('nm', 9, PARAM_INT);  // Month number
    $nm = 9;
	$itogmark = 0;
	
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
   	if (!$admin_is && !$region_operator_is) { 
	// if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
/*
    if (empty($shortname)) {  
        if ($yid < NEW_CRITERIA_YEARID)   {
        $shortname = 'rating_1';       
    } else {
        if ($level == 'school' || $level == 'region') {
            $shortname = 'rating_n';       // Shortname form: rating_n | rating_o .....
        } else if ($level == 'rayon')   {
            $shortname = 'rating_r';       
        }    
    } 
    }
*/

    $REGIONCRITERIA = new stdClass();
    
    init_rating_parameters($yid, $shortname, $select, $order);
		
	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
    	init_region_criteria($yid);
        if ($yid >= 6)  {
            $schoolids = print_tabs_years_rating("listcriteria.php?level=$level", $rid, $sid, $yid, false);
            $table = diff_tables($schoolids, $rid, $sid, $yid, $nm, $shortname, $action);
        } else {
  		    $table = table_listcriteria($rid, $sid, $yid, $nm, $shortname, $action);
        }    
  		print_table_to_excel($table);
        exit();
	}

	
	$TYPESETTLEMENT = 0;

    if ($sid != 0)	{
    	$school = get_record('monit_school', 'id', $sid);
   	    $strschool = $school->name;
   	    $TYPESETTLEMENT = $school->typesettlement;
    }	else  {
   	    $strschool = get_string('school', 'block_monitoring');
        $TYPESETTLEMENT = 1;
    }

	$strtitle = get_string('listcriteria', 'block_monitoring');
	
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid\">$strschools</a>";
	}
	$breadcrumbs .= " -> $strschool";
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strschool", $SITE->fullname, $breadcrumbs);

    $currenttab = 'listcriteria';
    include('tabs.php');

    $toprow2  = array();
    $toprow2[] = new tabobject('school', "listcriteria.php?level=school&rid=$rid&sid=$sid&nm=$nm&yid=$yid", 'По школе');
    $toprow2[] = new tabobject('rayon', "listcriteria.php?level=rayon&rid=$rid&nm=$nm&yid=$yid", 'По району');
    $tabs2 = array($toprow2);
    print_tabs($tabs2, $level, NULL, NULL);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	if ($admin_is  || $region_operator_is || $rayon_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("listcriteria.php?level=$level&yid=$yid&rid=", $rid);
        if ($level == 'school') {
		   // listbox_schools_lastyear("listcriteria.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
            listbox_schools("listcriteria.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
        }    
		echo '</table>';
	}

	if (($rid == 0 && $level == 'rayon') ||  ($sid == 0 && $level == 'school')) {
	    print_footer();
	 	exit();
	}

	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}

	if ($TYPESETTLEMENT == 0)	{
		error(get_string('errortypesettlement', 'block_monitoring'), "{$CFG->wwwroot}/blocks/monitoring/school/addschool.php?mode=edit&rid=$rid&sid=$sid&yid=$yid&tab=5");
	}
	init_region_criteria($yid);
	
	// print_tabs_years($yid, "listcriteria.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=", true);
    // print_tabs_years_link("listcriteria.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=", $rid, $sid, $yid, true);
    $schoolids = print_tabs_years_rating("listcriteria.php?level=$level", $rid, $sid, $yid);
    echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rating_level("listcriteria.php?level=$level&rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=", $shortname, $yid, false, $level);
    echo '</table>';
	echo '<p>';
    // вызываем повторно, так как shortname может быть пустым
	init_rating_parameters($yid, $shortname, $select, $order);
    // print "<br><br><br>!!! $yid, $shortname, $select, $order, $level !!!!<br>";
    
	$totalsum = $itogmark = 0;

	$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 
					 'fid' => $fid,  'nm' => $nm,  'sn' => $shortname,  'sesskey' => $USER->sesskey);

    if ($yid >= 6)  {
        
       	echo '<center>';
        print_single_button("listcriteria.php", $options, get_string('downloadexcel'));
        echo '<br /></center>';
        
        $table = diff_tables($schoolids, $rid, $sid, $yid, $nm, $shortname);
        print_color_table($table);
        // print_color_table($tableprev);
    } else {
        $table = table_listcriteria($rid, $sid, $yid, $nm, $shortname);    
      	$strtotlamark = get_string('total_mark', 'block_monitoring') . ': ' . $totalsum;
       	print_heading($strtotlamark, 'center', 4);
      	// print_table($table);
       	print_color_table($table);
	    print_heading($strtotlamark, 'center', 4);
    }
	
   	echo '<center>';
    print_single_button("listcriteria.php", $options, get_string('downloadexcel'));
    echo '</center>';

	// print_string('remarkyear', 'block_monitoring');
    print_footer();


function diff_tables($schoolids, $rid, $sid, $yid, $nm, $shortname, $action='')
{
    global $totalsum;
    
    if ($yid == 9)  {
        $oldshortname = str_ireplace('_9', '', $shortname);
    } else {
        $oldshortname = $shortname;
    }
    
    $totalsum = (double)0.0;
    $tablecurr = table_listcriteria($rid, $schoolids[$yid], $yid, $nm, $shortname);
    $totalsum1 = $totalsum;
    $totalsum = (double)0.0; 
    $tableprev = table_listcriteria($rid, $schoolids[$yid-1], $yid-1, $nm, $oldshortname);
    $totalsum2 = $totalsum;
    
    $tablecurr->head[] = get_string('mark', 'block_monitoring') . '<br> (прошлый год)';
    $tablecurr->align[] = "center";
    $tablecurr->size[] = '15%';
    $tablecurr->columnwidth[] = 18;
    
    $tablecurr->head[] = get_string ('dynamicvalue', 'block_monitoring'); // 'Разность';
    $tablecurr->align[] = "center";
    $tablecurr->size[] = '15%';
    $tablecurr->columnwidth[] = 17;
    
    // print_object($tablecurr); 
    // print_object($tableprev);
    
    $prevyearvalue = array();
    foreach ($tableprev->data as $i => $tablerow)   {
        $prevyearvalue[$tablerow[0]] = $tablerow[3];    
    }    
    
    // print_object($prevyearvalue);
    
    $totalsum2_new = (float)0.0; 
    $totalsum3 = (float)0.0; 
    $subP115 = 0;
    foreach ($tablecurr->data as $i => $tablerow)   {
        $strballcurr = $strballprev = $dopstr = '';
        if (isset($prevyearvalue[$tablerow[0]]) && !empty($prevyearvalue[$tablerow[0]]))    {
            $o2 = (double)strip_tags($prevyearvalue[$tablerow[0]]);// , '<small>');
            $strballprev  = $prevyearvalue[$tablerow[0]];
        } else {
            $o2 = 0.0;
            $strballprev  = '-';
        }     
        $tablecurr->data[$i][] = $strballprev;             
             
        if (empty($tablecurr->data[$i][3]) && empty($tablerow[3])) {
            $o1 = 0;
        } else {         
            // print $tablecurr->data[$i][3] . ' ~ ' . $tablerow[3] . '<br>'; 
            $o1 = (double)strip_tags($tablecurr->data[$i][3]); // , '<small>');
        }     
                
        $sub = (double)$o1 - (float)$o2;
        
        if ( (isset($prevyearvalue[$tablerow[0]]) && !empty($prevyearvalue[$tablerow[0]])) ||  !empty($tablecurr->data[$i][3]))  {
            $totalsum3 += $sub; 
            $tablecurr->data[$i][] = '<b>'.round($sub, 4).'<b>' . $dopstr;
            $totalsum2_new += $o2;
        } else {
            $tablecurr->data[$i][] = '';
        }    
        
                // echo $o1 . ' ~ ' . $o2 . '=' . $sub . '<hr>';
                /*
                if ($tablecurr->data[$i][0] == 'П-115') {
                    $sub *= -1;
                    $subP115 = $sub;                
                    $dopstr = "<br /><small>$o2-$o1<small>";
                } 
                */    
                /*
                if ($tablecurr->data[$i][0] == 'П-140') {
                    $o1 = strip_tags($tablecurr->data[$i][3]); // , '<small>'); 
                    $o2 = strip_tags($tablerow[3]);// , '<small>');
                    if ($o1 == 'есть' && $o2 == 'есть') {
                        $sub = 1.2;    
                    }  else if ($o1 == 'есть' && $o2 == 'нет') {
                        $sub = 1.1;
                    }  else if ($o1 == 'нет' && $o2 == 'нет') {
                        $sub = -0.8;
                    }  else if ($o1 == 'нет' && $o2 == 'есть') {
                        $sub = -0.9;
                    }    
                } 
                */
    
    }
    
    // print_object($tablecurr);
    
    if ($shortname == 'rating_k')   {
        $totalsum3_1 = $totalsum1 - $totalsum2;
        $totalsum1_1 = $totalsum1 + $subP115;
        $tablecurr->data[] = array ('', '<b>Итого</b>', '', '<b>' . $totalsum1 . '</b>', // '<b>' . $totalsum1_1 . '<br>(' . $totalsum1 . ')</b>', 
                                    '<b>' . $totalsum2_new . "<br>($totalsum2)"  . '</b>', '<b>' . $totalsum3 . '<br>(' . $totalsum3_1 . ')</b>');
        if ( $action == '') {                                    
            notify('<b>ВНИМАНИЕ!!! Значение итогового значения динамического показателя скорректировано с учетом обратной формулы расчета показателя П-115.</b>');
        }    
                                            
    } else {
        $tablecurr->data[] = array ('', '<b>Итого</b>', '', '<b>' . $totalsum1 . '</b>', 
                                    '<b>' . $totalsum2_new . "<br>($totalsum2)" . '</b>', '<b>' . $totalsum3 . '</b>');
        
    }                                      
    // print_object($tablecurr);
    // print_object($tableprev);
    return $tablecurr;
}


function table_listcriteria($rid, $sid, $yid, $nm, $shortname, $action='')	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $totalsum, $itogmark, $level;

    $symbolnumber = get_string('symbolnumber', 'block_monitoring'); 
    $nameofpokazatel = get_string('nameofpokazatel', 'block_monitoring');
    $valueofpokazatel = get_string('mark', 'block_monitoring');
	$formula = get_string('formula','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $table = new stdClass();
    if ($admin_is || $region_operator_is)   {
        $table->head  = array ($symbolnumber, $nameofpokazatel, $formula, $valueofpokazatel);
        $table->align = array ("left", "left", "center", "center");
    	$table->width = '90%';
        $table->size = array ('5%', '65%', '15%', '15%');
        $table->columnwidth = array (7, 100, 18, 18);
    	$table->class = 'moutable';
    } else {
        $table->head  = array ($symbolnumber, $nameofpokazatel, $valueofpokazatel);
        $table->align = array ("left", "left", "center");
    	$table->width = '90%';
        $table->size = array ('5%', '65%', '15%');
        $table->columnwidth = array (7, 100, 18);
    	$table->class = 'moutable';
    }

    $schoolname = get_field_select('monit_school', 'name', "id =$sid");
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('listcriteria', 'block_monitoring') . ' ' . $schoolname; 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "criteria_{$rid}_{$sid}_{$shortname}";
    $table->worksheetname = 'criteria';
	
	get_name_otchet_year ($yid, $a, $b);
	// echo $a . $b;	

    init_rating_parameters($yid, $shortname, $select, $order, $level);	
 
    $datefrom = get_date_from_month_year($nm, $yid);
    
    if ($shortname == 'rating_r')   {
        $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
    	   		   WHERE (rayonid=$rid) and (shortname='$shortname') and (datemodified=$datefrom)";
    } else {    
        $strsql = "SELECT * FROM {$CFG->prefix}monit_rating_listforms
    	   		   WHERE (schoolid=$sid) and (shortname='$shortname') and (datemodified=$datefrom)";
    }                   
  	
	$arr_df = array();
	if ($rec = get_record_sql($strsql))	{
 		$fid = $rec->id;
   		if ($df = get_record_sql("SELECT * FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid"))	{
   			$arr_df = (array)$df;
			// print_object($arr_df);   	
   		}
   	}	
 
    $strsql = "SELECT id, number, name, formula, edizm, indicator, ordering 
			   FROM {$CFG->prefix}monit_rating_criteria
    		   WHERE  $select 
			   ORDER BY $order";
           
	if ($criterias = get_records_sql($strsql)) 	{
	
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
				    if ($o1 == 'func_p140__') {
				        $strmark = func_p140__($o2, $criteria->indicator, $arr_df, $criteria->ordering);
				    } else if ($o1 == 'func_p136') {
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
    		if ($action == 'excel') 	{
    			$criterianumber = " " . $criterianumber; 
    		}	
            
            if ($admin_is || $region_operator_is)   {
                $table->data[] = array ($criterianumber, $criterianame, $criteriaformula, $strmark); //
            } else {
                $table->data[] = array ($criterianumber, $criterianame, $strmark); //                
            }  
		}    
	}
	
	return $table;
}


?>