<?php // $Id: summaryregion.php,v 1.9 2013/02/25 06:17:19 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../../mou_ege/lib_ege.php');    
	require_once('../lib_excel.php');
    require_once('lib_rating.php');
    require_once('lib_report.php');

    $rid = required_param('rid', PARAM_INT);            // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);            // School id
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    // $nm  = optional_param('nm', 9, PARAM_INT);  // Month number
    $criteriaid = optional_param('cid', 0);       // Shortname form
    $level = optional_param('level', 'region');       // Form id
    $report = optional_param('r', 'r3');       //  Report
    $stype = optional_param('stype', 0, PARAM_INT);     // School type    
    $nm = 9;

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is) { //  && !$rayon_operator_is ) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

    $field_f = array();
    $field_f ['rating_n'] = 'fn_1';
    $field_f ['rating_o'] = 'fo_1';
    $field_f ['rating_s'] = 'fs_9';

    init_rating_parameters($yid, $shortname, $select, $order, $level);
    $select .=  " AND edizm <> 'null'";
    
   
	$action = optional_param('action', '');
    if ($action == 'excel') 	{
        switch($report) {
                case 'r1':
                case 'r1a':
                    if ($yid >= NEW_REPORT_YEARID)  {
                        $table = table_summary_rating_region($yid, $nm, $shortname, $select, $order, $report);
                    }  else {
                        $table = array();
                    }
                break;    
                case 'r2':
                case 'r3': // отчет по умолчанию
                    if ($stype > 0) {
                        if ($yid >= NEW_REPORT_YEARID)  {
                            $table = table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype);
                        }  else {
                            $table = array();
                        }
                    }
                break;                          
                case 'r3a':
                    if ($stype > 0) {
                        if ($yid >= NEW_REPORT_YEARID)  {
                            $table = table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype);
                        }  else {
                            $table = array();
                        }
                    }
                break;                          
        }
                
        if (!empty($table)) {
      		print_table_to_excel($table);
            exit();
        }    

	}

	$strtitle = get_string('summaryrating', 'block_monitoring');
	
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	}
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

    $currenttab = 'summaryrating';
    include('tabs.php');
    
    $toprow2  = array();
    $toprow2[] = new tabobject('school', "summaryrating.php?level=school&rid=$rid&sid=$sid&nm=$nm&yid=$yid&cid=$criteriaid", 'ОУ района');
    $toprow2[] = new tabobject('rayon',  "summaryrating.php?level=rayon&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Муниципалитетов');
    $toprow2[] = new tabobject('region', "summaryregion.php?level=region&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid&r=$report", 'Областной');    
    $tabs2 = array($toprow2);
    print_tabs($tabs2, $level, NULL, NULL);
    

	if ($admin_is  || $region_operator_is) {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
        if ($level == 'school') {
		      listbox_rayons("summaryrating.php?level=$level&sid=0&amp;yid=$yid&amp;rid=", $rid);
        }   else {
            $rid = 1; 
        }   
		echo '</table>';
	}

	if ($rid == 0) {
	    print_footer();
	 	exit();
	}

	if ($rayon_operator_is && $rayon_operator_is != $rid)  {
		notify(get_string('selectownrayon', 'block_monitoring'));
	    print_footer();
		exit();
	}
/*
    $toprow3  = array();
    $toprow3[] = new tabobject('r1', "summaryregion.php?r=r1&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 1');
    $toprow3[] = new tabobject('r1a',"summaryregion.php?r=r1a&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 1а');
    $toprow3[] = new tabobject('r2', "summaryregion.php?r=r2&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 2');    
    $toprow3[] = new tabobject('r3', "summaryregion.php?r=r3&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 3');    
    $toprow3[] = new tabobject('r3a', "summaryregion.php?r=r3a&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 3а');
    $toprow3[] = new tabobject('r4', "summaryregion.php?r=r4&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг 4');
    $tabs3 = array($toprow3);
    print_tabs($tabs3, $report, NULL, NULL);
*/

    $schoolids = print_tabs_years_rating("summaryregion.php?r=$report", $rid, $sid, $yid);

    switch($report) {
            case 'r1':
            case 'r1a':
            	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
            	listbox_rating_level("summaryregion.php?r=$report&level=$level&rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=", $shortname, $yid, true, $level);
            	echo '</table><p>';	
                if ($yid >= NEW_REPORT_YEARID)  {
                    $table = table_summary_rating_region($yid, $nm, $shortname, $select, $order, $report);
                }  else {
                    $table = array();
                }
            break;
                
            case 'r2':
            case 'r3': // отчет по умолчанию
            	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
            	listbox_type_school("summaryregion.php?r=$report&level=$level&rid=$rid&sid=$sid&nm=$nm&yid=$yid&sn=$shortname&stype=", $stype);
            	echo '</table><p>';
                //                print '!!!!!'. $shortname;	
                if ($stype > 0) {
                    if ($yid >= NEW_REPORT_YEARID)  {
                        $table = table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype);
                    }  else {
                        $table = array();
                    }
                }
            break;   
                                   
            case 'r3a':
            	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
            	listbox_type_school("summaryregion.php?r=$report&level=$level&rid=$rid&sid=$sid&nm=$nm&yid=$yid&sn=$shortname&stype=", $stype, $report);
            	echo '</table><p>';	
                if ($stype > 0) {
                    if ($yid >= NEW_REPORT_YEARID)  {
                        $table = table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype);
                    }  else {
                        $table = array();
                    }
                }
            break;                          

            case 'r4':
            	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
            	listbox_type_school("summaryregion.php?r=$report&level=$level&rid=$rid&sid=$sid&nm=$nm&yid=$yid&sn=$shortname&stype=", $stype);
            	echo '</table><p>';	
                if ($stype > 0) {
                    if ($yid >= NEW_REPORT_YEARID)  {
                        $table = table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype, 'monit_rating_total_ex');
                    }  else {
                        $table = array();
                    }
                }
            break;                          
    }
            
    if (!empty($table)) {
   	    print_color_table($table);

        if (!empty($table->schoolslist))   {
            $schoolslist = $table->schoolslist;
            if ($noschools = get_records_select('monit_school', "id in ($schoolslist)", 'name', 'id, name')) {
                $fff =  $field_f[$shortname];
                print_heading("Школы, не вошедшие в рейтинг ($fff = 0)", 'center', 4);
                // $strnoraitingschool = 'Замечание: школы не вошедшие в рейтинг:<br><table>';
                $strnoraitingschool = '<div align=center><table border=2>';
                foreach ($noschools as $sa) {
                    $strnoraitingschool .= '<tr align=left><td>' . $sa->name . '</td></tr>';
                }  
                $strnoraitingschool .= '</table></div>';
            }            
            notify($strnoraitingschool); 
        }        
   		$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid, 'stype' => $stype,
                         'r' => $report, 'level' => $level, 'sn' => $shortname, 'sesskey' => $USER->sesskey);
	   	echo '<center>';
	    print_single_button("summaryregion.php", $options, get_string('downloadexcel'));
	    echo '</center>';
    }    

	// print_string('remarkyear', 'block_monitoring');
    print_footer();



function table_summary_rating_region($yid, $nm, $shortname, $select, $order, $report)
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $field_f;

    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');
    $valueofpokazatel_k = 'Кадровые условия';

    $table = new stdClass();
    if ($report == 'r1')   {
        $strselect = '';
        $table->head  = array ($strstatus, $numberf, $strname, 'Динамический показатель ступени');
        $table->align = array ("center", "center", "left", "center");
    	$table->width = '90%';
        $table->size = array ('5%', '5%', '90%', '5%');
        $table->columnwidth = array (10, 7, 100, 15);
    	$table->class = 'moutable';
    } else {
        $strselect = ', dynamic_rating_k';
        $table->head  = array ($strstatus, $numberf, $strname, 'Динамический показатель ступени', 'Динамический показатель по кадрам', 'Итого');
        $table->align = array ("center", "center", "left", "center", "center", "center");
    	$table->width = '90%';
        $table->size = array ('5%', '5%', '90%', '5%', '5%', '5%');
        $table->columnwidth = array (10, 7, 100, 15, 15, 15);
    	$table->class = 'moutable';
    }
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "regionreport_{$shortname}_{$report}";
    $table->worksheetname = 'totalreport';

  // print_object($table);
  
/*
    if ($report == 'r')    {    
        $datefrom = get_date_from_month_year($nm, $yid);
    	$strsql =  "SELECT s.id as sid, s.name, t.dynamic_{$shortname} as dynamic FROM mdl_monit_rating_total t
                    INNER JOIN mdl_monit_school s ON s.id=t.schoolid
                    WHERE s.isclosing=0 AND t.yearid=$yid
                    ORDER BY dynamic DESC";
    } else {
*/        
        $datefroms = array();
    	$datefroms[] = get_date_from_month_year($nm, $yid-1);
        $datefroms[] = get_date_from_month_year($nm, $yid);
      
    	$noschoolsids =  array();
        $fff =  $field_f[$shortname];
        foreach ($datefroms as $datefrom)   { 
            $strsql = "SELECT l.id, l.schoolid, f.{$fff} FROM mdl_monit_rating_listforms l
                       inner join mdl_monit_form_{$shortname} f ON l.id=f.listformid
                       WHERE (shortname='$shortname') and (datemodified=$datefrom) and ($fff=0)";
            // echo $strsql;            
        	if ($recs = get_records_sql($strsql))	{
        	    foreach ($recs as $rec)    {
        	       $noschoolsids[] = $rec->schoolid;
        	    }
        	}
        }
               //print_object($noschoolsids);
        translate_schoolids_fromotheryearid($noschoolsids, $yid);
        $noschoolsids = array_unique($noschoolsids);
        $schoolslist = implode(',', $noschoolsids);
        $table->schoolslist = $schoolslist; 
        $strsql =  "SELECT s.id as sid, s.name, t.dynamic_{$shortname} as dynamic $strselect 
                   FROM mdl_monit_rating_total t
                   INNER JOIN mdl_monit_school s ON s.id=t.schoolid
                   WHERE s.isclosing=0 AND t.yearid=$yid AND s.id not in ($schoolslist)
                   ORDER BY dynamic DESC";
        // echo $strsql;                   
//    }                	

	$color = 'red';
	if ($schools = get_records_sql($strsql))	{
	    $schoolsdynamic = array();
        $schoolsdynamic_k = array();
		$schoolsname = array();
        $schoolsmark = array();
	    foreach ($schools as $sa)  {
	        $schoolsname[$sa->sid] = $sa->name;
            if ($report == 'r1a')    {
                $schoolsdynamic[$sa->sid]   = $sa->dynamic;
                $schoolsdynamic_k[$sa->sid] = $sa->dynamic_rating_k;
                $schoolsmark[$sa->sid] = $sa->dynamic + $sa->dynamic_rating_k;    
            } else {
                $schoolsmark[$sa->sid] = $sa->dynamic;
            }
	        
	    }
    }    
	// $schoolslist = implode(',', $schoolsarray);
    
 	arsort($schoolsmark);
	reset($schoolsmark);
	$maxmark = current($schoolsmark);
	$placerating = array();
	$mesto = 1;
	foreach ($schoolsmark as $schoolid => $schoolmark) {
		if ($schoolmark == $maxmark)	{
			$placerating[$schoolid] = $mesto;
		} else {
			$placerating[$schoolid] = ++$mesto;
			$maxmark = $schoolmark; 
		}	 
	}	

	foreach ($schoolsmark as $schoolid => $schoolmark) {
		$schoolname = $schoolsname[$schoolid];
		$schoolname = "<strong>$schoolname</strong></a>";
		$mesto = '<b><i>'.$placerating[$schoolid] . '</i></b>';
        $roundschoolmark = round($schoolsmark[$schoolid], 4); 
		$strmark = 	 "<b><font color=green>{$roundschoolmark}</font></b>";
		/*
        $strsql = " (schoolid=$schoolid) and (shortname='$shortname') and (datemodified=$datefrom) ";
        // echo $strsql;
        
		if ($rec = get_record_select('monit_rating_listforms', $strsql, 'id, status'))	{
			$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
			$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");	
       } else {
	      	$strformrkpu_status = get_string("status1","block_monitoring");			
	        $strcolor = get_string("status1color","block_monitoring");
		}
 	     */
      	$strformrkpu_status = '';			
	    $strcolor = get_string("status5color","block_monitoring");
           
        if ($report == 'r1a')    {
            $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $schoolsdynamic[$schoolid], 
                                    $schoolsdynamic_k[$schoolid], $strmark);
        } else {
            $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $strmark);            
        }
  
	    $table->bgcolor[] = array ($strcolor);
	}    
    
    return $table;
    
}


function get_list_old_new_id ($table, $yid)
{
	$lastyid = $yid-1;
	
	$edusids = array();
	$edus = get_records($table, 'yearid', $lastyid);
	foreach($edus as $edu)	{
		if ($edu->isclosing == false)	{
			$edusids[$edu->uniqueconstcode]->oldid = $edu->id;
		}	
	}
	$edus = get_records($table, 'yearid', $yid);
	foreach($edus as $edu)	{
		if ($edu->isclosing == false)	{
			$edusids[$edu->uniqueconstcode]->newid = $edu->id;
		}	
	}
	
	$newedusids = array();
	foreach ($edusids as $eid)	{
	    if (isset($eid->oldid) && isset($eid->newid))  {
		  $newedusids[$eid->oldid] = $eid->newid;
        }  
	}
	
	return $newedusids;
}


function translate_schoolids_fromotheryearid(&$noschoolsids, $yid)
{
    $newedusids = get_list_old_new_id ('monit_school', $yid);
    
    foreach ($noschoolsids as $i => $id)    {
        if (isset($newedusids[$id])) {
            $noschoolsids[$i] = $newedusids[$id];  
        }
    }
    return; 
}


// Рейтинг образовательных учреждений области, выстраиваемый по типу ОУ
function table_summary_rating_region_type($yid, $nm, $shortname, $select, $order, $report, $stype, $tablename='monit_rating_total')
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $field_f;

    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');
    $valueofpokazatel_k = 'Кадровые условия';

    $datefrom = get_date_from_month_year($nm, $yid);
    
    $table = new stdClass();
    if ($report == 'r2')   {
        $table->head  = array ($strstatus, $numberf, $strname);
        $table->align = array ("center", "center", "left");
    	$table->width = '90%';
        $table->size = array ('5%', '5%', '90%', '5%');
        $table->columnwidth = array (10, 7, 100, 15);
    	$table->class = 'moutable';
    } else {
        $table->head  = array ($strstatus, $numberf, $strname, 'Динамический показатель по кадрам');
        $table->align = array ("center", "center", "left", "center");
    	$table->width = '90%';
        $table->size = array ('5%', '5%', '90%', '5%', '5%', '5%');
        $table->columnwidth = array (10, 7, 100, 15, 15, 15);
    	$table->class = 'moutable';
    }
    
    switch($stype)  {
        // Начальная общеобразовательная школа
        case 5:
                $table->head[] = 'Динамический показатель НОШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                if ($report == 'r3' || $report == 'r3a' || $report == 'r4')    {
                    $table->head[] = 'Итого';
                    $table->align[] = "center";
                    $table->size[] = '5%';
                    $table->columnwidth[] = 15;
                }    
        break;
        // Основная общеобразовательная школа
        case 6:
                $table->head[] = 'Динамический показатель НОШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                $table->head[] = 'Динамический показатель ООШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                $table->head[] = 'Итого';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
        break;
        // Средняя общеобразовательная школа
        // Школа повышенного статуса
        case 1: case 99: case 98:
                $table->head[] = 'Динамический показатель НОШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                $table->head[] = 'Динамический показатель ООШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                $table->head[] = 'Динамический показатель CОШ';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
                $table->head[] = 'Итого';
                $table->align[] = "center";
                $table->size[] = '5%';
                $table->columnwidth[] = 15;
        break;
    }
	
   	$table->titlesrows = array(30, 30);
    $table->titles = array();
/*
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "regionreport_{$shortname}";
    $table->worksheetname = 'totalreport';
*/

    $type_schools = array(); // get_records_sql("select id, name from {$CFG->prefix}monit_school_type");
 	$type_schools[5] = 'Начальная общеобразовательная школа';
	$type_schools[6] = 'Основная общеобразовательная школа';
	$type_schools[1] = 'Средняя общеобразовательная школа';
    $type_schools[98] = 'Средняя школа, включая школы повышенного уровня (средняя школа с УИОП, гимназия, лицей)';
	$type_schools[99] = 'Школа «повышенного» статуса (гимназия, лицей, школа с УИОП и т.п.)';  
	$table->titles[] = $type_schools[$stype];
    $table->downloadfilename = "ratingregion_{$shortname}_{$stype}_{$report}";
    $table->worksheetname = 'ratingregion';
	
	if ($stype == 99)	{
		$strwhere1 = 'stateinstitution in (2,3,4)';
	} else if  ($stype == 98)	{
	    $strwhere1 = 'stateinstitution in (1,2,3,4)';
	} else {   
		$strwhere1 = 'stateinstitution = ' . $stype;
	}

	$strsql =  "SELECT id, name  FROM {$CFG->prefix}monit_school
				WHERE $strwhere1 AND yearid=$yid";	

	if ($schools = get_records_sql($strsql))	{
		
        $schoolsarray = array();
        $schoolsname = array();
        $schoolsmark = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	        $schoolsname[$sa->id] = $sa->name;
	        $schoolsmark[$sa->id] = 0;
	    }
	    $schoolslist = implode(',', $schoolsarray);

        $strsql =  "SELECT s.id as sid, s.name, dynamic_rating_n, dynamic_rating_o, dynamic_rating_s, dynamic_rating_k  
                   FROM mdl_{$tablename} t
                   INNER JOIN mdl_monit_school s ON s.id=t.schoolid
                   WHERE s.isclosing=0 AND t.yearid=$yid AND s.id in ($schoolslist)";
                   // ORDER BY dynamic DESC";
        // print $strsql;                   
    }                	

	$color = 'red';
    $schoolsdynamic = array();
    $schoolsdynamic_k = array();
	$schoolsname = array();
    $schoolsmark = array();
    $dynamic_n = array();
    $dynamic_o = array();
    $dynamic_s = array();
    $dynamic_k = array();        
	if ($schools = get_records_sql($strsql))	{
	    foreach ($schools as $sa)  {
	        $schoolsname[$sa->sid] = $sa->name;
            $dynamic_n[$sa->sid] = $sa->dynamic_rating_n;
            $dynamic_o[$sa->sid] = $sa->dynamic_rating_o;
            $dynamic_s[$sa->sid] = $sa->dynamic_rating_s;
            $dynamic_k[$sa->sid] = $sa->dynamic_rating_k;
            if ($report == 'r2')    {
                // $schoolsmark[$sa->sid] = $sa->dynamic;
                switch($stype)  {
                    case 5: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n;
                    break;
                    case 6: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n + $sa->dynamic_rating_o;
                    break;
                    case 1:
                    case 98: 
                    case 99: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n + $sa->dynamic_rating_o + $sa->dynamic_rating_s;
                    break;
                }
            } else if ($report == 'r3' || $report == 'r3a' || $report == 'r4')    {
                switch($stype)  {
                    case 5: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n + $sa->dynamic_rating_k;
                    break;
                    case 6: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n + $sa->dynamic_rating_o + $sa->dynamic_rating_k;
                    break;
                    case 1:
                    case 98:
                    case 99: $schoolsmark[$sa->sid] = $sa->dynamic_rating_n + $sa->dynamic_rating_o + $sa->dynamic_rating_s + $sa->dynamic_rating_k;
                    break;
                }
                /*
                $schoolsdynamic[$sa->sid]   = $sa->dynamic;
                $schoolsdynamic_k[$sa->sid] = $sa->dynamic_rating_k;
                $schoolsmark[$sa->sid] = $sa->dynamic + $sa->dynamic_rating_k;
                */    
            } 
	    }
    }  else {
        /*
       $schools = get_records_select('monit_school', "id in ($schoolslist)", 'id', 'id as sid, name'); 
       foreach ($schools as $sa)  {
            $schoolsname[$sa->sid] = $sa->name;
            $dynamic_n[$sa->sid] = 0;
            $dynamic_o[$sa->sid] = 0;
            $dynamic_s[$sa->sid] = 0;
            $dynamic_k[$sa->sid] = 0;
            $schoolsmark[$sa->sid] = 0;
        } 
        */   
    }  
	// $schoolslist = implode(',', $schoolsarray);
    
 	arsort($schoolsmark);
	reset($schoolsmark);
	$maxmark = current($schoolsmark);
	$placerating = array();
	$mesto = 1;
	foreach ($schoolsmark as $schoolid => $schoolmark) {
		if ($schoolmark == $maxmark)	{
			$placerating[$schoolid] = $mesto;
		} else {
			$placerating[$schoolid] = ++$mesto;
			$maxmark = $schoolmark; 
		}	 
	}	

	foreach ($schoolsmark as $schoolid => $schoolmark) {
		$schoolname = $schoolsname[$schoolid];
		$schoolname = "<strong>$schoolname</strong></a>";
		$mesto = '<b><i>'.$placerating[$schoolid] . '</i></b>';
        $roundschoolmark = round($schoolsmark[$schoolid], 4); 
		$strmark = 	 "<b><font color=green>{$roundschoolmark}</font></b>";
		/*
        $strsql = " (schoolid=$schoolid) and (shortname='$shortname') and (datemodified=$datefrom) ";
        // echo $strsql;
		if ($rec = get_record_select('monit_rating_listforms', $strsql, 'id, status'))	{
			$strformrkpu_status = get_string('status'.$rec->status, "block_monitoring");
			$strcolor = get_string('status'.$rec->status.'color',"block_monitoring");	
       } else {
	      	$strformrkpu_status = get_string("status1","block_monitoring");			
	        $strcolor = get_string("status1color","block_monitoring");
		}
 	    */
         
      	$strformrkpu_status = '';			
	    $strcolor = get_string("status6color","block_monitoring");
           
        if ($report == 'r2')    {
            switch($stype)  {
                case 5: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $strmark);
                break;
                case 6: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $dynamic_n[$schoolid], $dynamic_o[$schoolid], $strmark);
                break;
                case 1:
                case 98: 
                case 99: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $dynamic_n[$schoolid], $dynamic_o[$schoolid], $dynamic_s[$schoolid], $strmark);
                break;
            }
            // $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $strmark);  
        } else if ($report == 'r3' || $report == 'r3a' || $report == 'r4')    {
            // $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $schoolsdynamic[$schoolid], $schoolsdynamic_k[$schoolid], $strmark);
            switch($stype)  {
                case 5: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $dynamic_k[$schoolid], $dynamic_n[$schoolid], $strmark);
                break;
                case 6: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $dynamic_k[$schoolid], $dynamic_n[$schoolid], $dynamic_o[$schoolid], $strmark);
                break;
                case 1:
                case 98: 
                case 99: $table->data[] = array ($strformrkpu_status, $mesto, $schoolname, $dynamic_k[$schoolid], $dynamic_n[$schoolid], $dynamic_o[$schoolid], $dynamic_s[$schoolid], $strmark);
                break;
            }

        }
  
	    $table->bgcolor[] = array ($strcolor);
	}    
    
    return $table;
    
}

?>