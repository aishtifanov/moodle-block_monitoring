<?php // $Id: monthreports.php,v 1.9 2012/10/01 11:22:13 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('../../mou_ege/lib_ege.php');    

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $sn  = optional_param('sn', ''); // Shortname       				
    $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
	$action   = optional_param('action', '');
    $levelmonit  = optional_param('level', 'rayon');

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('regionzp');
	$rayon_operator_is  = ismonitoperator('rayonzp', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $rayon = get_record('monit_rayon', 'id', $rid);
    
    if ($action == 'excel') {
        $table = table_svodka($sn, $rid, $yid, $nm);
        // print_object($table);
        print_table_to_excel($table);        
        exit();
	}

    $strtitle = get_string('summaryreport', 'block_monitoring');
    if ($rid > 0)   {
        
    } else {

    }    
    
    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
    if ($rid > 0)   {
        $strrayon = get_string('rayon', 'block_monitoring');
        $strreports = get_string('reportrayon', 'block_monitoring');
        $breadcrumbs .= " -> <a href=\"sumreports.php?rid=$rid&yid=$yid&nm=$nm\">$strreports</a>";
    } else {
        $strrayon = get_string('region', 'block_monitoring');
        $strreports = get_string('summaryreportsregion', 'block_monitoring');
        $breadcrumbs .= " -> <a href=\"sumreports.php?level=region&rid=$rid&yid=$yid&nm=$nm\">$strreports</a>";
    }        
   	
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strrayon", $SITE->fullname, $breadcrumbs);

    $mm = get_string('mm_'.$nm,  'block_monitoring');
    
    $year = date("Y");
    if ($eduyear = get_record('monit_years', 'id', $yid))  {
        $ayears = explode("/", $eduyear->name);
        if ($nm >= 9 && $nm <= 12) $year = trim($ayears[0]);
        else if ($nm >= 1 && $nm <= 8)  $year = trim($ayears[1]);
    }

    print_heading(get_string($sn, 'block_monitoring', $mm . ' ' . $year . ' года '), 'center', 2);

    if ($rid > 0)   {
        print_heading('Район: ' . $rayon->name, 'center', 3);
    }    

    $table = table_svodka($sn, $rid, $yid, $nm);
    
    print_color_table($table);
    
	$options = array('action'=> 'excel', 'rid' => $rid, 'yid' => $yid,  
                     'sn' => $sn, 'nm' => $nm,  'sesskey' => $USER->sesskey);
   	echo '<center>';
    print_single_button("monthreports.php", $options, get_string('downloadexcel'));
    echo '</center>';
    

    print_footer();    



function table_svodka($sn, $rid, $yid, $nm)
{
    global $CFG, $rayon;   

    $table->dblhead->head1  = array (get_string('nameotrasli', 'block_monitoring'));
    $table->dblhead->span1  = array ("rowspan=2");
    $table->dblhead->head2  = array ();    
	$table->align = array ('left');
    $table->size = array ('25%');
	$table->columnwidth = array (25);
    $table->class = 'moutable';
    $table->headerstyle = 'moutable';
   	$table->width = '100%';
	$table->titlesrows = array(50, 30);
    if ($rid > 0)   {
        $table->titles = array(get_string($sn, 'block_monitoring'), $rayon->name);
    } else {
        $table->titles = array(get_string($sn, 'block_monitoring'));
    }    
    $table->downloadfilename = 'table_'.$sn.'_'.$rid;
    $table->worksheetname = $table->downloadfilename;
    
    switch ($sn)    {
        /*
        case 'svodka1': $table->fields = array (
                        'r1'  => array ('r1_0',  'r2_0', 'r3_0', 'r4_0', 'r5_0'), 'r1_0' => '', 
                        'f1'  => array ('f1_1',  'f2_1', 'f3_1', 'f4_1', 'f5_1'), 
                        'f1_1'=> array ('f1_1_1','f2_1_1', 'f3_1_1', 'f4_1_1', 'f5_1_1'),
                        'f2'  => array ('f1_2',  'f2_2', 'f3_2', 'f4_2', 'f5_2'), 
                        'f2_1'=> array ('f1_2_1','f2_2_1', 'f3_2_1', 'f4_2_1', 'f5_2_1'),
                        'r3'  => array ('r1_3',  'r2_3', 'f3_3', 'f4_3', 'f5_3'),
                        'f3'  => array ('f1_3',  'f2_3'),
                        'f3_1'=> array ('f1_3_1','f2_3_1'),
                        'f4'  => array ('f1_4',  'f2_4', 'f3_3', 'f4_3', 'f5_3'),
                        'r4'  => array ('r1_4',  'r2_4', 'r3_4', 'r4_4', 'r5_4'),
                        'f5'  => array ('f1_5',  'f2_5', 'f3_4', 'f4_4', 'f5_4'),
                        'f6'  => array ('f1_6',  'f2_6', 'f3_5', 'f4_5', 'f5_5')
                        ); 
        
        break;
        */
        case 'svodka1': $table->fields = array (
                        'r1'  => array ('r1_0',  'r3_0', 'r4_0', 'r5_0'), 'r1_0' => '', 
                        'f1'  => array ('f1_1',  'f3_1', 'f4_1', 'f5_1'), 
                        'f1_1'=> array ('f1_1_1','f3_1_1', 'f4_1_1', 'f5_1_1'),
                        'f2'  => array ('f1_2',  'f3_2', 'f4_2', 'f5_2'), 
                        'f2_1'=> array ('f1_2_1','f3_2_1', 'f4_2_1', 'f5_2_1'),
                        'r3'  => array ('r1_3',  'f3_3', 'f4_3', 'f5_3'),
                        'f3'  => array ('f1_3'),
                        'f3_1'=> array ('f1_3_1'),
                        'f4'  => array ('f1_4',  'f3_3', 'f4_3', 'f5_3'),
                        'r4'  => array ('r1_4',  'r3_4', 'r4_4', 'r5_4'),
                        'f5'  => array ('f1_5',  'f3_4', 'f4_4', 'f5_4'),
                        'f6'  => array ('f1_6',  'f3_5', 'f4_5', 'f5_5')
                        ); 
        
        break;
        case 'svodka2': $table->fields = array ('r1' => 'r1_0', 'r1_0' => '', 'f1' => 'f1_1', 'f1_1'=> 'f1_1_1',
                        'f2'=> 'f1_2', 'f2_1'=> 'f1_2_1', 'r3'=> 'r1_3', 'f3'=> 'f1_3',
                        'f3_1'=> 'f1_3_1', 'f4'=> 'f1_4', 'r4'=> 'r1_4', 'f5'=> 'f1_5', 'f6'=> 'f1_6');
        break;
        
        case 'svodka3': $table->fields = array ('r1' => 'r2_0', 'r1_0' => '', 'f1' => 'f2_1', 'f1_1'=> 'f2_1_1',
                        'f2'=> 'f2_2', 'f2_1'=> 'f2_2_1', 'r3'=> 'r2_3', 'f3'=> 'f2_3',
                        'f3_1'=> 'f2_3_1', 'f4'=> 'f2_4', 'r4'=> 'r2_4', 'f5'=> 'f2_5', 'f6'=> 'f2_6');
        break;

        case 'svodka4': $table->fields = array ('r1' => 'r3_0', 'r1_0' => '', 'f1' => 'f3_1', 'f1_1'=> 'f3_1_1',
                        'f2'=> 'f3_2', 'f2_1'=> 'f3_2_1', 'r3_0'=> 'f3_3', 
                        'r4'=> 'r3_4', 'f5'=> 'f3_4', 'f6'=> 'f3_5');
        break;

        case 'svodka5': $table->fields = array ('r1' => 'r4_0', 'r1_0' => '', 'f1' => 'f4_1', 'f1_1'=> 'f4_1_1',
                        'f2'=> 'f4_2', 'f2_1'=> 'f4_2_1', 'r3_0'=> 'f4_3',
                        'r4'=> 'r4_4', 'f5'=> 'f4_4', 'f6'=> 'f4_5');
        break;

        case 'svodka6': $table->fields = array ('r1' => 'r5_0', 'r1_0' => '', 'f1' => 'f5_1', 'f1_1'=> 'f5_1_1',
                        'f2'=> 'f5_2', 'f2_1'=> 'f5_2_1', 'r3_0'=> 'f5_3',
                        'r4'=> 'r5_4', 'f5'=> 'f5_4', 'f6'=> 'f5_5');
        break;

        case 'svodka7': $table->fields = array (
                        'r1'  => array ('r1_0',  'r3_0', 'r4_0', 'r5_0'), 'r1_0' => '', 
                        'f1'  => array ('f1_1',  'f3_1', 'f4_1', 'f5_1'), 
                        'f2'  => array ('f1_2',  'f3_2', 'f4_2', 'f5_2'), 
                        'r3'  => array ('r1_3',  'f3_3', 'f4_3', 'f5_3'),
                        'f3'  => array ('f1_3'),
                        'f3_1'=> array ('f1_4'),
                        'r4'  => array ('r1_4',  'r3_4', 'r4_4', 'r5_4'),
                        'f5'  => array ('f1_5',  'f3_4', 'f4_4', 'f5_4'),
                        'f6'  => array ('f1_6',  'f3_5', 'f4_5', 'f5_5')
                        ); 
        break;
        case 'svodka8': $table->fields = array ('r1' => 'r1_0', 'r1_0' => '', 'f1' => 'f1_1', 
                        'f2'=> 'f1_2', 'r3_0'=> 'r1_3', 'f3'=> 'f1_3',
                        'f4'=> 'f1_4', 'r4'=> 'r1_4', 'f5'=> 'f1_5', 'f6'=> 'f1_6');
        break;
        case 'svodka9': $table->fields = array ('r1' => 'r3_0', 'r1_0' => '', 'f1' => 'f3_1', 
                        'f2'=> 'f3_2', 'r3_0'=> 'f3_3', 'r4'=> 'r3_4', 'f5'=> 'f3_4', 'f6'=> 'f3_5');
        break;
        case 'svodka10': $table->fields = array ('r1' => 'r4_0', 'r1_0' => '', 'f1' => 'f4_1', 
                        'f2'=> 'f4_2', 'r3_0'=> 'f4_3', 'r4'=> 'r4_4', 'f5'=> 'f4_4', 'f6'=> 'f4_5');
        break;
        case 'svodka11': $table->fields = array ('r1' => 'r5_0', 'r1_0' => '', 'f1' => 'f5_1', 
                        'f2'=> 'f5_2', 'r3_0'=> 'f5_3', 'r4'=> 'r5_4', 'f5'=> 'f5_4', 'f6'=> 'f5_5');
        break;

    }    
    
    $i = -1;
    foreach ($table->fields as $idx => $fld)   {    
        $table->data[++$i][0] = get_string($idx, 'block_monitoring');
    }

    if ($sn != 'svodka1' && $sn != 'svodka7')   {
        $arrflds = array();
        foreach ($table->fields as $idx => $fld)   {
            if ($fld != '') {
                $arrflds[] = $fld;
            }
        }
        $table->sqlflds = implode (',', $arrflds);
    }  else {
        $table->sqlflds = '*';
    }  

    $strpred = get_string('nm_'.($nm-1), 'block_monitoring');
    $strcurr = get_string('nm_'.$nm, 'block_monitoring');                   

	if ($nm == 9 ) $predyid = $yid-1;
	else $predyid = $yid;


    $num_sn = substr($sn, 6);
    if ($num_sn < 7)   {
        $rkps = array('zp_d_num_worker', 'zp_e_fot_worker', 'zp_f_oklad_worker');
        calc_month_data($table, $rkps, $sn, $rid, 4, 5, "май 2011 года");        
        calc_month_data($table, $rkps, $sn, $rid, $predyid, $nm-1, "Предыдущий месяц ($strpred)");               
        calc_month_data($table, $rkps, $sn, $rid, $yid, $nm, "Текущий месяц ($strcurr)");
        calc_month_data_percent($table, $sn, $rid, $yid, $nm-1, $nm, "Темп роста текущего месяца ($strcurr) к предыдущему месяцу ($strpred)");    
        calc_month_data_percent($table, $sn, $rid, $yid, 5, $nm, "Темп роста текущего месяца ($strcurr) к маю 2011 года", 4);
        calc_month_data_sovmestiteli($table, $sn, $rid, $yid, $nm, "Справочно: Совместители ($strcurr)");
    }   else {
        $rkps = array('zp_m_num_8046', 'zp_n_fot_8046', 'zp_o_oklad_8046');
        // calc_month_data($table, $rkps, $sn, $rid, $yid, $nm-1, "Предыдущий месяц ($strpred)");
        $september = get_string('nm_9', 'block_monitoring') . ' ' . date('Y') . ' года'; 
        // calc_month_data($table, $rkps, $sn, $rid, $yid, 9,    $september);              
        calc_month_data($table, $rkps, $sn, $rid, $predyid, $nm-1, "Предыдущий месяц ($strpred)");
        calc_month_data($table, $rkps, $sn, $rid, $yid, $nm, "Текущий месяц ($strcurr)");
        calc_month_data_sub($table, $rkps, $sn, $rid, $yid, $nm-1, $nm, "Отклонение (+/-)");
    }    
    
    return $table;
}    




function get_data_from_monit_form($rid, $rkp, $sqlflds, $datefrom)
{
    global $CFG;

    $zp_form = false;
        
    if ($rid > 0)   {
        $zp_form = get_data_from_monit_form2($rid, $rkp, $sqlflds, $datefrom);
    } else {
        for ($rid=1; $rid<=22; $rid++)  {
            $zp_form1 = get_data_from_monit_form2($rid, $rkp, $sqlflds, $datefrom);
            if ($zp_form1)  {
                foreach ($zp_form1 as $fld => $value)   {
                    $zp_form->{$fld} += $value;    
                }
            } 
        }
    }    
    return $zp_form;
}


function get_data_from_monit_form2($rid, $rkp, $sqlflds, $datefrom)
{
    global $CFG; 
    
    $strsql = "SELECT id FROM {$CFG->prefix}monit_rayon_listforms
	   		   WHERE (rayonid=$rid) and (shortname='$rkp') and (datemodified=$datefrom)";
    // echo $strsql . '<br>';            
    if ($lf = get_record_sql($strsql)) 	{
        // print_object($lf);
        $strsql = "SELECT $sqlflds FROM {$CFG->prefix}monit_form_$rkp WHERE listformid= $lf->id";
        // echo $strsql; 
        $zp_form = get_record_sql($strsql);
    } else {
        $zp_form = false;
    }
    
    return $zp_form;
}


function calc_summ_total($adata, $listfields)
{
    $sum = 0;
    foreach ($listfields as $fld)   {
        $sum += $adata->{$fld};
    }
    
    return $sum;    
}



function calc_month_data(&$table, $rkps, $sn, $rid, $yid, $nm, $strhead='')
{
    global $CFG, $action;
    
    $datefrom = get_date_from_month_year($nm, $yid);

    $table->dblhead->head1[] = $strhead;
    $table->dblhead->span1[] = 'colspan=5';   
    for($i=1; $i<=5; $i++)  {
        $strzpi = get_string('zp_'.$i, 'block_monitoring');
        if ($action == 'excel') {
            $strzpi = str_replace('&nbsp;', ' ', $strzpi);
        }
        $table->dblhead->head2[] = $strzpi; 
        $table->align[] = 'center';
        // $table->size[] = '5%';
        $table->columnwidth[] = '15'; 
    }


    foreach($rkps as $rkp)	{
        $zp_forms[$rkp] = get_data_from_monit_form($rid, $rkp, $table->sqlflds, $datefrom);
    }
    
    // $zp_forms = get_zp_forms($rid, $rkps, $table->sqlflds, $datefrom);    
    // print_object($zp_forms);
            
    $i = 0;
    foreach ($table->fields as $idx => $fld)   {
        
        if ($fld == '') {
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            
        } else { 
            $num = $fot = $avg1 = $oklad =  $avg2 = 0;
            if ($sn == 'svodka1' || $sn == 'svodka7')   {
                // print_object($zp_forms[$rkps[0]]);
                if ($zp_forms[$rkps[0]])   {  // 'zp_d_num_worker'
                    $num = calc_summ_total($zp_forms[$rkps[0]], $fld);
                }    
                if ($zp_forms[$rkps[1]])   {
                    $fot = calc_summ_total($zp_forms[$rkps[1]], $fld);
                }
                if ($zp_forms[$rkps[2]]) {    
                    $oklad = calc_summ_total($zp_forms[$rkps[2]], $fld);
                }     
            } else {    
                if (isset($zp_forms[$rkps[0]]->{$fld})) {
                    $num = $zp_forms[$rkps[0]]->{$fld};
                } 
                if (isset($zp_forms[$rkps[1]]->{$fld})) {
                    $fot = $zp_forms[$rkps[1]]->{$fld};
                }
                if (isset($zp_forms[$rkps[2]]->{$fld})) {
                    $oklad = $zp_forms[$rkps[2]]->{$fld};
                }
            }

            if ($num > 0) {
                $avg1 = $fot / $num;
                $avg2 = $oklad / $num;                
            }

            $table->data[$i][] = $num;
            $table->data[$i][] = number_format($fot, 2, ',', ' ');
            $table->data[$i][] = number_format($avg1, 2, ',', ' ');
            $table->data[$i][] = number_format($oklad, 2, ',', ' ');
            $table->data[$i][] = number_format($avg2, 2, ',', ' ');                
        }    
        
        $i++;      
    }           
    
    return;
}



function calc_month_data_percent(&$table, $sn, $rid, $yid, $prednm, $currnm, $strhead='', $predyid=0)
{
    global $CFG;
    
    if ($predyid > 0)   {
        $datefrom[1] = get_date_from_month_year($prednm, $predyid);
    } else {
       	if ($currnm == 9 ) {
      	    $datefrom[1] = get_date_from_month_year($prednm, $yid-1);
        } else {
            $datefrom[1] = get_date_from_month_year($prednm, $yid);
        }
    }        
    
    $datefrom[2] = get_date_from_month_year($currnm, $yid);

    $table->dblhead->head1[] = $strhead;
    $table->dblhead->span1[] = 'colspan=5';   
    for($i=1; $i<=5; $i++)  {
        $table->dblhead->head2[] = get_string('zpp_'.$i, 'block_monitoring');
        $table->align[] = 'center';
        $table->size[] = '10%';
        $table->columnwidth[] = '10'; 
    }
    $rkps = array('zp_d_num_worker', 'zp_e_fot_worker', 'zp_f_oklad_worker');
    $zp_forms = array();

    for ($i=1; $i<=2; $i++) {
        $zp_forms[$i] = array();
        foreach($rkps as $rkp)	{
            $zp_forms[$i][$rkp] = get_data_from_monit_form($rid, $rkp, $table->sqlflds, $datefrom[$i]);
        }    
    }    
        
    // print_object($zp_forms);
            
    $i = 0;
    foreach ($table->fields as $idx => $fld)   {
        
        if ($fld == '') {
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            
        } else { 

            $num = $num1 = $num2 = 0;        
            $fot = $fot1 = $fot2 = 0;
            $oklad = $oklad1 = $oklad2 = 0;
            if ($sn == 'svodka1')   {
                if ($zp_forms[1]['zp_d_num_worker'])    {
                    $num1 = calc_summ_total($zp_forms[1]['zp_d_num_worker'], $fld);
                }    
                if ($zp_forms[2]['zp_d_num_worker'])    {
                    $num2 = calc_summ_total($zp_forms[2]['zp_d_num_worker'], $fld);
                }
                if ($zp_forms[1]['zp_e_fot_worker'])    {    
                    $fot1 = calc_summ_total($zp_forms[1]['zp_e_fot_worker'], $fld);
                }    
                if ($zp_forms[2]['zp_e_fot_worker'])    {
                    $fot2 = calc_summ_total($zp_forms[2]['zp_e_fot_worker'], $fld);
                }
                if ($zp_forms[1]['zp_f_oklad_worker'])  {    
                    $oklad1 = calc_summ_total($zp_forms[1]['zp_f_oklad_worker'], $fld);
                }
                if ($zp_forms[2]['zp_f_oklad_worker'])  {     
                    $oklad2 = calc_summ_total($zp_forms[2]['zp_f_oklad_worker'], $fld);
                }    
                
            } else {    
                if (isset($zp_forms[1]['zp_d_num_worker']->{$fld})) {
                    $num1 = $zp_forms[1]['zp_d_num_worker']->{$fld};
                } 
                if (isset($zp_forms[2]['zp_d_num_worker']->{$fld})) {
                    $num2 = $zp_forms[2]['zp_d_num_worker']->{$fld};
                } 
                if (isset($zp_forms[1]['zp_e_fot_worker']->{$fld})) {
                    $fot1 = $zp_forms[1]['zp_e_fot_worker']->{$fld};
                }
                if (isset($zp_forms[2]['zp_e_fot_worker']->{$fld})) {
                    $fot2 = $zp_forms[2]['zp_e_fot_worker']->{$fld};
                }
                if (isset($zp_forms[1]['zp_f_oklad_worker']->{$fld})) {
                    $oklad1 = $zp_forms[1]['zp_f_oklad_worker']->{$fld};
                }
                if (isset($zp_forms[2]['zp_f_oklad_worker']->{$fld})) {
                    $oklad2 = $zp_forms[2]['zp_f_oklad_worker']->{$fld};
                }
            }

            if ($num1 > 0)  {
                $num = ($num2 / $num1) * 100.0 - 100;
            } 
            if ($fot1 > 0)  {
                $fot = ($fot2 / $fot1) * 100.0 - 100;
            }
            if ($oklad1 > 0)  {
                $oklad = ($oklad2 / $oklad1) * 100.0 - 100;
            }

            $avg = $avg1 = $avg2 = 0;
            $avgokl = $avgokl1 = $avgokl2 =0;            
            if ($num1 > 0) {
                $avg1 = $fot1 / $num1;
                $avgokl1 = $oklad1 / $num1;
            }
            if ($num2 > 0) {
                $avg2 = $fot2 / $num2;
                $avgokl2 = $oklad2 / $num2;
            }
            if ($avg1 > 0)  {
                $avg = ($avg2 / $avg1) * 100.0 - 100;
                $avgokl = ($avgokl2 / $avgokl1) * 100.0 - 100;
            }

            $table->data[$i][] = number_format($num, 2, ',', ' ') . '%';
            $table->data[$i][] = number_format($fot, 2, ',', ' ') . '%';
            $table->data[$i][] = number_format($avg, 2, ',', ' ') . '%';
            $table->data[$i][] = number_format($oklad, 2, ',', ' ') . '%';
            $table->data[$i][] = number_format($avgokl, 2, ',', ' ') . '%';
        }    
        
        $i++;      
    }           
    
    return;
}


function calc_month_data_sovmestiteli(&$table, $sn, $rid, $yid, $nm, $strhead='')
{
    global $CFG, $action;
    
    $datefrom = get_date_from_month_year($nm, $yid);

    $table->dblhead->head1[] = $strhead;
    $table->dblhead->span1[] = 'colspan=5';   
    for($i=1; $i<=5; $i++)  {
        $strzpi = get_string('zp_'.$i, 'block_monitoring');
        if ($action == 'excel') {
            $strzpi = str_replace('&nbsp;', ' ', $strzpi);
        }
        $table->dblhead->head2[] = $strzpi; 
        $table->align[] = 'center';
        // $table->size[] = '10%';
        $table->columnwidth[] = '15'; 
    }
    $rkps[1] = array('zp_g_num_outsovm', 'zp_h_fot_outsovm', 'zp_i_oklad_outsovm');
    $rkps[2] = array('zp_j_num_insovm', 'zp_k_fot_insovm', 'zp_l_oklad_insovm');
    $zp_forms = array();

    for ($i=1; $i<=2; $i++) {
        $zp_forms[$i] = array();
        foreach($rkps[$i] as $rkp)	{
            $zp_forms[$i][$rkp] = get_data_from_monit_form($rid, $rkp, $table->sqlflds, $datefrom);            
        }    
    }    
        
    // print_object($zp_forms);
                
    $i = 0;
    foreach ($table->fields as $idx => $fld)   {
        
        if ($fld == '') {
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            
        } else { 

            $num = $num1 = $num2 = 0;
            $fot = $fot1 = $fot2 = 0;
            $oklad = $oklad1 = $oklad2 = 0;
            $avg = $avgokl = 0;            
            if ($sn == 'svodka1')   {
                if ($zp_forms[1]['zp_g_num_outsovm'])   { 
                    $num1 = calc_summ_total($zp_forms[1]['zp_g_num_outsovm'], $fld);
                }
                if ($zp_forms[2]['zp_j_num_insovm'])    {    
                    $num2 = calc_summ_total($zp_forms[2]['zp_j_num_insovm'], $fld);
                }    
                if ($zp_forms[1]['zp_h_fot_outsovm'])   {
                    $fot1 = calc_summ_total($zp_forms[1]['zp_h_fot_outsovm'], $fld);
                }    
                if ($zp_forms[2]['zp_k_fot_insovm'])    {
                    $fot2 = calc_summ_total($zp_forms[2]['zp_k_fot_insovm'], $fld);
                }    
                if ($zp_forms[1]['zp_i_oklad_outsovm']) {
                    $oklad1 = calc_summ_total($zp_forms[1]['zp_i_oklad_outsovm'], $fld);
                }    
                if ($zp_forms[2]['zp_l_oklad_insovm'])  {
                    $oklad2 = calc_summ_total($zp_forms[2]['zp_l_oklad_insovm'], $fld);
                }    
            } else {    
                if (isset($zp_forms[1]['zp_g_num_outsovm']->{$fld})) {
                    $num1 = $zp_forms[1]['zp_g_num_outsovm']->{$fld};
                } 
                if (isset($zp_forms[2]['zp_j_num_insovm']->{$fld})) {
                    $num2 = $zp_forms[2]['zp_j_num_insovm']->{$fld};
                } 
                if (isset($zp_forms[1]['zp_h_fot_outsovm']->{$fld})) {
                    $fot1 = $zp_forms[1]['zp_h_fot_outsovm']->{$fld};
                }
                if (isset($zp_forms[2]['zp_k_fot_insovm']->{$fld})) {
                    $fot2 = $zp_forms[2]['zp_k_fot_insovm']->{$fld};
                }
                if (isset($zp_forms[1]['zp_i_oklad_outsovm']->{$fld})) {
                    $oklad1 = $zp_forms[1]['zp_i_oklad_outsovm']->{$fld};
                }
                if (isset($zp_forms[2]['zp_l_oklad_insovm']->{$fld})) {
                    $oklad2 = $zp_forms[2]['zp_l_oklad_insovm']->{$fld};
                }
             }


            if ($num1 > 0)  {
                $num = $num2 + $num1;
            } 
            if ($fot1 > 0)  {
                $fot = $fot2 + $fot1;
            }
            if ($oklad1 > 0)  {
                $oklad = $oklad2 + $oklad1;
            }
            if ($num > 0) {
                $avg = $fot / $num;
                $avgokl = $oklad / $num;
            }

            $table->data[$i][] = $num;
            $table->data[$i][] = number_format($fot, 2, ',', ' ');
            $table->data[$i][] = number_format($avg, 2, ',', ' ');
            $table->data[$i][] = number_format($oklad, 2, ',', ' ');
            $table->data[$i][] = number_format($avgokl, 2, ',', ' ');
                 
        }    
        
        $i++;      
    }           
    
    return;
}




function calc_month_data_sub(&$table, $rkps, $sn, $rid, $yid, $prednm, $currnm, $strhead='')
{
    global $CFG;
    
   	if ($currnm == 9 ) {
   	    $datefrom[1] = get_date_from_month_year($prednm, $yid-1);
    } else {
        $datefrom[1] = get_date_from_month_year($prednm, $yid);
    }    
    $datefrom[2] = get_date_from_month_year($currnm, $yid);

    $table->dblhead->head1[] = $strhead;
    $table->dblhead->span1[] = 'colspan=5';   
    for($i=1; $i<=5; $i++)  {
        $table->dblhead->head2[] = get_string('zp_'.$i, 'block_monitoring');
        $table->align[] = 'center';
        $table->size[] = '10%';
        $table->columnwidth[] = '10'; 
    }
    $zp_forms = array();

    for ($i=1; $i<=2; $i++) {
        $zp_forms[$i] = array();
        foreach($rkps as $rkp)	{
            $zp_forms[$i][$rkp] = get_data_from_monit_form($rid, $rkp, $table->sqlflds, $datefrom[$i]);
        }    
    }    
        
    // print_object($zp_forms);
            
    $i = 0;
    foreach ($table->fields as $idx => $fld)   {
        
        if ($fld == '') {
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            $table->data[$i][] = '';
            
        } else { 

            $num = $num1 = $num2 = 0;        
            $fot = $fot1 = $fot2 = 0;
            $oklad = $oklad1 = $oklad2 = 0;
            $avg = $avgokl = 0;            

            if ($sn == 'svodka7')   {
                if ($zp_forms[1][$rkps[0]])    {
                    $num1 = calc_summ_total($zp_forms[1][$rkps[0]], $fld);
                }    
                if ($zp_forms[2][$rkps[0]])    {
                    $num2 = calc_summ_total($zp_forms[2][$rkps[0]], $fld);
                }
                if ($zp_forms[1][$rkps[1]])    {    
                    $fot1 = calc_summ_total($zp_forms[1][$rkps[1]], $fld);
                }    
                if ($zp_forms[2][$rkps[1]])    {
                    $fot2 = calc_summ_total($zp_forms[2][$rkps[1]], $fld);
                }
                if ($zp_forms[1][$rkps[2]])  {    
                    $oklad1 = calc_summ_total($zp_forms[1][$rkps[2]], $fld);
                }
                if ($zp_forms[2][$rkps[2]])  {     
                    $oklad2 = calc_summ_total($zp_forms[2][$rkps[2]], $fld);
                }    
                
            } else {    

                if (isset($zp_forms[1][$rkps[0]]->{$fld})) {
                    $num1 = $zp_forms[1][$rkps[0]]->{$fld};
                } 
                if (isset($zp_forms[2][$rkps[0]]->{$fld})) {
                    $num2 = $zp_forms[2][$rkps[0]]->{$fld};
                } 
                if (isset($zp_forms[1][$rkps[1]]->{$fld})) {
                    $fot1 = $zp_forms[1][$rkps[1]]->{$fld};
                }
                if (isset($zp_forms[2][$rkps[1]]->{$fld})) {
                    $fot2 = $zp_forms[2][$rkps[1]]->{$fld};
                }
                if (isset($zp_forms[1][$rkps[2]]->{$fld})) {
                    $oklad1 = $zp_forms[1][$rkps[2]]->{$fld};
                }
                if (isset($zp_forms[2][$rkps[2]]->{$fld})) {
                    $oklad2 = $zp_forms[2][$rkps[2]]->{$fld};
                }
            }    

            $num = $num2 - $num1;
            $fot = $fot2 - $fot1;
            $oklad = $oklad2 - $oklad1;
            if ($num > 0) {
                $avg = $fot / $num;
                $avgokl = $oklad / $num;
            }

            $table->data[$i][] = $num;
            $table->data[$i][] = number_format($fot, 2, ',', ' ');
            $table->data[$i][] = number_format($avg, 2, ',', ' ');
            $table->data[$i][] = number_format($oklad, 2, ',', ' ');
            $table->data[$i][] = number_format($avgokl, 2, ',', ' ');
        }    
        
        $i++;      
    }           
    
    return;
}

?>
