<?php // $Id: summaryrating.php,v 1.26 2013/02/25 06:17:19 shtifanov Exp $

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
    $level = optional_param('level', 'school');       // Form id
    $report = optional_param('r', 'rA');       //  Report
    $shortname = optional_param('sn', '');
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

    init_rating_parameters($yid, $shortname, $select, $order, $level);
    $select .=  " AND edizm <> 'null'";
    
	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
		// $table = table_summaryrating($rid, $sid, $yid, $nm, $shortname, $select, $order);
    	if ($shortname <> 'rating_0')	{	
    	    switch($level) {
    	       // ВКЛАДКА "ОУ района"
                case 'school':
                    // $table = table_summaryrating($rid, $sid, $yid, $nm, $shortname, $select, $order);
                    // рейтинг для учебных годов с $yid = 6
                    if ($yid >= NEW_REPORT_YEARID) {
                        if ($shortname == 'rating_9')   { 
                            // рейтинг по всем группам показателей
                            $table = table_summaryrating_dynamic($rid, $sid, $yid, $nm, $shortname, $select, $order);    
                        } else {
                            // рейтинг по одной группе показателей
                            $table = table_summaryrating_delta($rid, $sid, $yid, $nm, $shortname, $select, $order);
                        }                        
                    } else { 
                        // таблица первых версий рейтинга
                        $table = table_summaryrating_with_k($rid, $sid, $yid, $nm, $shortname, $select, $order); 
                    }
                break;   
                
                // ВКЛАДКА "Муниципалитетов"        
                case 'rayon':
                /*
                    $toprow3  = array();
                    $toprow3[] = new tabobject('rA', "summaryregion.php?r=rA&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг А');
                    $toprow3[] = new tabobject('rB', "summaryregion.php?r=rB&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Рейтинг Б');    
                    $tabs3 = array($toprow3);
                    print_tabs($tabs3, $report, NULL, NULL);
                */    
                    if ($report == 'rA')    {
                        $tablename = 'monit_rating_total';
                    } else {
                        $tablename = 'monit_rating_total_ex';
                    }
                
                    if ($yid >= NEW_REPORT_YEARID)  { 
                        table_summaryratingrayon($rid, $sid, $yid-1, $nm, $shortname, $select, $order);  
                        table_summaryratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $order);
                        calc_dynamic_value_rayons($yid, $nm, $tablename);
                        $table = table_summary_rating_rayon($yid, $nm);
                    } else {
                        $table = table_summaryratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $order);
                    }      
                break;    
                case 'region':
                    $table = array();
                    /*
                    if ($yid == 6)  {
                        $table = table_summary_rating_region($yid, $nm, $shortname, $select, $order);
                    }  else {
                        $table = array();
                    }
                    */
                break;                  
            }
    	}
        
  		print_table_to_excel($table);
        exit();
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
    $toprow2[] = new tabobject('region', "summaryregion.php?level=region&rid=$rid&nm=$nm&yid=$yid&cid=$criteriaid", 'Областной');    
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

    $schoolids = print_tabs_years_rating("summaryrating.php?level=$level", $rid, $sid, $yid);
	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rating_level("summaryrating.php?level=$level&rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=", $shortname, $yid, true, $level);
	echo '</table><p>';	

    init_rating_parameters($yid, $shortname, $select, $order, $level);
    $select .=  " AND edizm <> 'null'";

	if ($shortname <> 'rating_0')	{	
	    switch($level) {
            case 'school':
                // $table = table_summaryrating($rid, $sid, $yid, $nm, $shortname, $select, $order);
                if ($yid < 6) {
                    $table = table_summaryrating_with_k($rid, $sid, $yid, $nm, $shortname, $select, $order);
                } else {   
                    if ($shortname == 'rating_9')   {
                        $table = table_summaryrating_dynamic($rid, $sid, $yid, $nm, $shortname, $select, $order);    
                    } else {
                        $table = table_summaryrating_delta($rid, $sid, $yid, $nm, $shortname, $select, $order);
                    }
                    
                }
            break;           
            case 'rayon':
            /*
                $toprow3  = array();
                $toprow3[] = new tabobject('rA', "summaryrating.php?r=rA&level=rayon&rid=$rid&sid=$sid&nm=$nm&yid=$yid&sn=$shortname", 'Рейтинг А');
                $toprow3[] = new tabobject('rB', "summaryrating.php?r=rB&level=rayon&rid=$rid&sid=$sid&nm=$nm&yid=$yid&sn=$shortname", 'Рейтинг Б');    
                $tabs3 = array($toprow3);
                print_tabs($tabs3, $report, NULL, NULL);
            */    
                if ($report == 'rA')    {
                    $tablename = 'monit_rating_total';
                } else {
                    $tablename = 'monit_rating_total_ex';
                }
            
                if ($yid >= NEW_REPORT_YEARID)  { 
                    $table = table_summaryratingrayon($rid, $sid, $yid-1, $nm, $shortname, $select, $order);
                    // print_color_table($table);  
                    $table =  table_summaryratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $order);
                    // print_color_table($table);
                    calc_dynamic_value_rayons($yid, $nm, $tablename);
                    $table = table_summary_rating_rayon($yid, $nm);
                } else {
                    $table = table_summaryratingrayon($rid, $sid, $yid, $nm, $shortname, $select, $order);
                }      
            break;    
            case 'region':
                $table = array();
            /*
                if ($yid >= NEW_REPORT_YEARID)  {
                    $table = table_summary_rating_region($yid, $nm, $shortname, $select, $order);
                }  else {
                    $table = array();
                }
                */
            break;                  
        }
            
        if (!empty($table)) {
   	   	    print_color_table($table);
    		$options = array('action'=> 'excel', 'rid' => $rid, 'sid' => $sid, 'yid' => $yid,
                              'level' => $level, 'sn' => $shortname, 'sesskey' => $USER->sesskey);
    	   	echo '<center>';
    	    print_single_button("summaryrating.php", $options, get_string('downloadexcel'));
    	    echo '</center>';
            
            if ($shortname == 'rating_k')   {
                notify('<b>ВНИМАНИЕ!!! Значение динамического показателя скорректировано с учетом обратной формулы расчета показателя П-115.</b>');
            }
        }    
	}

	// print_string('remarkyear', 'block_monitoring');
    print_footer();
    

function table_summaryrating_dynamic($rid, $sid, $yid, $nm, $shortname, $select, $order)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $schoolids;
    
    // $shortnames = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
    // $shortnames = get_listnameforms($yid, 'school');
    
    $strstatus = get_string('status', 'block_monitoring');
    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring') . '(текущий год)';
    $valueofpokazatel_k = 'Кадровые условия (текущий год)';
    $valueofpokazatel0 = get_string('valueofpokazatel', 'block_monitoring') . '(предыдущий год)';
    $valueofpokazatel_k0 = 'Кадровые условия (предыдущий год)';
    
    $table = new stdClass();
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

    $strdynamic = get_string ('dynamicvalue', 'block_monitoring');
    $table->head  = array ($strstatus, $numberf, $strname, 
                            $strdynamic . '(НОШ)', $strdynamic . '(ООШ)', $strdynamic . '(СОШ)', $strdynamic . '(кадры)', 
                            'Формула', 'Среднее значение');
    $table->align = array ("center", "center", "left", 
                            "center", "center", 'center', 'center',
                            "center", 'center', 'center');
	$table->width = '90%';
    $table->columnwidth = array (10, 7, 100, 15, 15, 15, 15, 15, 15, 15);
	$table->class = 'moutable';

   	$table->titlesrows = array(30, 30);
    $table->titles = array();
    $table->titles[] = get_string('summaryrating', 'block_monitoring'); 
	$table->titles[] = get_string('name_'.$shortname, 'block_monitoring');
    $table->downloadfilename = "totalreport_{$rid}_{$shortname}";
    $table->worksheetname = $table->downloadfilename;
	
	$datefrom = get_date_from_month_year($nm, $yid);
	// $curryid = get_current_edu_year_id();
    $prevyid = $yid - 1;
    $curryid = $yid;
    $schoolsids = array();
    $schoolsname = array();
    $schoolsmark = array();

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
       
    $strsql =  "SELECT r.id as sid, r.rayonid, schoolid, 
                       dynamic_rating_n, dynamic_rating_o, dynamic_rating_s, dynamic_rating_k,        
                       s.uniqueconstcode, s.name, s.stateinstitution
                FROM mdl_monit_rating_total r INNER JOIN mdl_monit_school s on s.id=r.schoolid
                where r.rayonid=$rid AND r.yearid=$yid and s.isclosing=0
                order by s.name";
    // echo $strsql;             	
    if ($schools = get_records_sql($strsql))	{
        $schoolsname = array();
        $schoolsmark = array();
        foreach ($schools as $sa)  {
            $schoolsids[] = $sa->sid;
            $schoolsname[$sa->sid] = $sa->name;
            
            foreach ($params as $i => $param) {
                if (in_array($sa->stateinstitution, $param->strwhere))   {
                    $schoolsmark[$sa->sid] = new stdClass();
                    $schoolsmark[$sa->sid]->totalsum = 0;
                    $schoolsmark[$sa->sid]->cntdyn = count($param->dynamic);        
                    foreach ($param->dynamic as $dynam) {
                        $schoolsmark[$sa->sid]->totalsum += $sa->{$dynam};
                    }
                    $schoolsmark[$sa->sid]->avg = round($schoolsmark[$sa->sid]->totalsum/$schoolsmark[$sa->sid]->cntdyn, 4);
                    break;
                }
            } 
       }
    }            
    
    $strformrkpu_status = '';			
	$strcolor = get_string("status5color","block_monitoring");
    $mesto = '-';
   	$itogo = 0;         
    $i=1;      
    foreach ($schools as $sa)  {
            
        $table->data[] = array ($i++, $mesto, $sa->name , 
                                $sa->dynamic_rating_n,  $sa->dynamic_rating_o, $sa->dynamic_rating_s, $sa->dynamic_rating_k,
                                $schoolsmark[$sa->sid]->totalsum . ' / ' . $schoolsmark[$sa->sid]->cntdyn, 
                                '<b>'.$schoolsmark[$sa->sid]->avg.'</b>');  
        $table->bgcolor[] = array ($strcolor);
        $itogo += $schoolsmark[$sa->sid]->avg;
	}    

    $table->data[] = array ($strformrkpu_status, $mesto, 'Сумма динамических показателей школ муниципалитета', 
                            '',  '', '', '', '', '<b>'.$itogo.'</b>');  
       
	return $table;
}
    

?>