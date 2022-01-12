<?php // $Id: lib_indices_school.php,v 1.16 2011/01/26 09:01:13 shtifanov Exp $


// Количество работников в ОУ (текущее)
function func_f0_3u($whati=0, $numi = 1)
{
	global $CFG, $sid;

   	$sum = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $sum;
   	}

    // echo $nm . ' - '. $whati . '<br>';
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='bkp_dolj') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{

    	if ($form = get_record('monit_form_bkp_dolj', 'listformid', $listform->id))   {
	    	$sum = 0;
      		foreach ($form as $key => $value) 	{
      		    $l = strlen ($key);
      		    $koncovka = substr($key, $l - 3, 3);
      		    // echo $koncovka . '<br>';
      		    if ($key != 'id' &&  $key != 'listformid' && $koncovka != '_st' && isset($value)) {
      		    	 $sum += $value;
      		    }
		    }
	    	// $sum .= ' ' . get_string('man', 'block_monitoring');
	    }
    }
    return $sum;

}

// Количество учителей (текущее)
function func_f0_4u($whati=0, $numi=1)
{
	global $CFG, $sid;

   	$sum = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		$datefrom = get_date_from_month_year(1, $yid);
		$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
  	 		       WHERE (shortname='79') and (datemodified=$datefrom) and (schoolid=$sid)";
        if ($rec = get_record_sql($strsql))		{
			$strsql = "select listformid, `f-r6-01` as rez from {$CFG->prefix}monit_bkp_table_79 where listformid={$rec->id}";
            if ($field = get_record_sql($strsql))	{
               	 $sum = $field->rez;
            }
	   	}
        return $sum;
	} 
    
    
	$datefrom = get_date_from_month_year($nm-$whati, $yid);
	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='bkp_pred') and (datemodified=$datefrom) and (schoolid=$sid)";
    if ($listform = get_record_sql($strsql)) 	{

    	if ($form = get_record('monit_form_bkp_pred', 'listformid', $listform->id))   {
	    	$sum = 0;
      		foreach ($form as $key => $value) 	{
      		    $l = strlen ($key);
	      		$koncovka = substr($key, $l - 3, 3);
      		    if ($key != 'id' &&  $key != 'listformid' && $koncovka != 'sov'  && isset($value))  {  // && $koncovka != 'con'
      		    	 $sum += $value;
      		    }
		    }
	    	// $sum .= ' ' . get_string('man', 'block_monitoring');
	    }
    }

    return $sum;

}

// Годовой бюджет учреждения
function func_f0_5u($whati=0, $numi=1)
{
	global $CFG, $sid, $FOND_OOU;

   	$sum = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $sum;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='bkp_f') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{

    	if ($form = get_record('monit_form_bkp_f', 'listformid', $listform->id))   {
	    	$sum = 0;
	    	if (isset($form->f1f)) $sum = $form->f1f*12;
            /*
	    	if (isset($form->f1f)) $sum += $form->f1f;
	    	if (isset($form->f2f)) $sum += $form->f2f;
	    	if (isset($form->f3f)) $sum += $form->f3f;
	    	*/
	    	// $sum .= ' ' . get_string('trub', 'block_monitoring');
	    	$FOND_OOU[$whati] = $sum;
	    }
    }
    return $sum;
}

// ФОТ учреждения
function func_f0_6u($whati=0, $numi=1)
{
	global $CFG, $sid, $FOND_TRUDA;

   	$sum = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{

   		return $sum;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='bkp_f') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{

    	if ($form = get_record('monit_form_bkp_f', 'listformid', $listform->id))   {
    		/*
    		$arrform = (array)$form;
	    	$sum = 0;
            for ($i=4; $i<=11; $i++) {
            	$nf = 'f'.$i.'f';
		    	if (isset($arrform[$nf])) $sum += $arrform[$nf];
		    }
	    	// $sum .= ' ' . get_string('trub', 'block_monitoring');
	    	*/
	    	$sum = 0;
	    	if (isset($form->f2f)) $sum = $form->f2f*12;

	    	$FOND_TRUDA[$whati] = $sum;
	    }
    }
    return $sum;
}

// ФОТ учителей учреждения
function func_f0_7u($whati=0, $numi=1)
{
	global $CFG, $sid, $FOND_TRUDA_TEACHER;

   	$sum = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $sum;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='bkp_f') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{

    	if ($form = get_record('monit_form_bkp_f', 'listformid', $listform->id))   {
	    	$sum = 0;
	    	if (isset($form->f2_6f)) $sum = $form->f2_6f*12;

    		/*
    		$arrform = (array)$form;
	    	$sum = 0;
            for ($i=9; $i<=11; $i++) {
            	$nf = 'f'.$i.'f';
		    	if (isset($arrform[$nf])) $sum += $arrform[$nf];
		    }
	    	// $sum .= ' ' . get_string('trub', 'block_monitoring');
	    	*/
	    	$FOND_TRUDA_TEACHER[$whati] = $sum;
	    }
    }
    return $sum;
}


function portion_f1_3u($whati=0, $numi=1)
{
	global $FOND_TRUDA, $FOND_TRUDA_TEACHER;

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return '-';
   	}

    if (!empty($FOND_TRUDA[$whati]) && !empty($FOND_TRUDA_TEACHER[$whati]))		{
		$proc = number_format($FOND_TRUDA_TEACHER[$whati]/$FOND_TRUDA[$whati]*100, 2, ',', '');
	    $proc .= '%';
    } else {
	    $proc = '-';
    }

	return $proc;
}


function func_4_4_1($whati=0, $numi=1)
{
	global $CFG, $sid;

	$ret = 0;

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$strsql = "SELECT id, numlicense, regnumlicense FROM {$CFG->prefix}monit_school
	 		   WHERE id=$sid";

    if ($rec = get_record_sql($strsql)) 	{
		if (!empty($rec->numlicense) && !empty($rec->regnumlicense))	{
			$ret = 1;
		}
	}

   return $ret;
}


function func_4_4_2($whati=0, $numi=1)
{
	global $CFG, $sid;

	$ret = 0;

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$strsql = "SELECT id, numcertificate, regnumcertificate FROM {$CFG->prefix}monit_school
	 		   WHERE id=$sid";

    if ($rec = get_record_sql($strsql)) 	{
		if (!empty($rec->numcertificate) && !empty($rec->regnumcertificate))	{
			$ret = 1;
		}
    }

   return $ret;
}


function func_4_1_11($whati=0, $numi=1)
{
	global $CFG, $sid;

	$ret = 0;

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$strsql = "SELECT id, isdirectormanager FROM {$CFG->prefix}monit_school
	 		   WHERE id=$sid";

    if ($rec = get_record_sql($strsql)) 	{
		if (!empty($rec->isdirectormanager))	{
			$ret = $rec->isdirectormanager;
		}
	}

   return $ret;
}


function func_d_u_1($whati=0, $numi=1)
{
	global $sid;

	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

    if ($listform = get_record('monit_school_listforms', 'schoolid', $sid, 'shortname', 'rkp_du', 'datemodified', $datefrom)) 	{
    	if ($form = get_record('monit_form_rkp_du', 'listformid', $listform->id))   {
	    	if (isset($form->fd_u_1)) {
		    	$form->fd_u_1 .= '||';
	    	  	list($fd_fact,$fd_rekv,$fd_link) = explode("|", $form->fd_u_1);
                $ret = get_string('yes');
            }
	    }
    }
    return $ret;
}


function func_d_u_1_doc($whati=0, $numi=1)
{
	global $sid;

	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

    if ($listform = get_record('monit_school_listforms', 'schoolid', $sid, 'shortname', 'rkp_du', 'datemodified', $datefrom)) 	{
    	if ($form = get_record('monit_form_rkp_du', 'listformid', $listform->id))   {
	    	if (isset($form->fd_u_1)) {
		    	$form->fd_u_1 .= '||';
	    	  	list($fd_fact,$fd_rekv,$fd_link) = explode("|", $form->fd_u_1);
                $ret = $fd_rekv;
            }
	    }
    }
    return $ret;
}


function func_d_u_1_link($whati=0, $numi=1)
{
	global $sid;

	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

    if ($listform = get_record('monit_school_listforms', 'schoolid', $sid, 'shortname', 'rkp_du', 'datemodified', $datefrom)) 	{
    	if ($form = get_record('monit_form_rkp_du', 'listformid', $listform->id))   {
	    	if (isset($form->fd_u_1)) {
		    	$form->fd_u_1 .= '||';
	    	  	list($fd_fact,$fd_rekv,$fd_link) = explode("|", $form->fd_u_1);
                $ret = $fd_link;
            }
	    }
    }
    return $ret;
}


function func_6_0($whati=0, $numi=1)
{
	global $sid;

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return '-';
   	}

    if (record_exists('monit_operator_school', 'schoolid', $sid)) {
    	$ret = get_string('yes');
    } else {
    	$ret = get_string('no');
    }

    return $ret;
}


function func_5_6_1($whati=0, $numi=1)
{
	global $CFG,  $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f5_6_1 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f5_6_1;
	    }
    }
    return $ret;
}


function func_5_6_2($whati=0, $numi=1)
{
	global $CFG, $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f5_6_2 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f5_6_2;
	    }
    }
    return $ret;
}


function func_6_3_6_1($whati=0, $numi=1)
{
	global $CFG,  $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f6_3_6_1 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f6_3_6_1;
	    }
    }
    return $ret;
}

function func_6_3_6_2($whati=0, $numi=1)
{
	global $CFG, $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f6_3_6_2 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f6_3_6_2;
	    }
    }
    return $ret;
}

function func_6_3_6_3($whati=0, $numi=1)
{
	global $CFG,  $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
   	else if ($whati < 0)	{
   		return $ret;
   	}

	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f6_3_6_3 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f6_3_6_3;
	    }
    }
    return $ret;
}


function func_6_3_6_4($whati=0, $numi=1)
{
	global $CFG, $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
    
	$datefrom = get_date_from_month_year($nm-$whati, $yid);


	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f6_3_6_4 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f6_3_6_4;
	    }
    }
    return $ret;
}


function func_6_3_6_5($whati=0, $numi=1)
{
	global $CFG, $sid;

   	$ret = '-';

    $nm = date('n');
	$yid = get_current_edu_year_id();

    if ($numi == 'xls') {
        if ($whati < 0)	{
            $nm += abs($whati);
            $whati = 0; 
        }
    }    
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT id FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_prm_u') and (datemodified=$datefrom) and (schoolid=$sid)";

    if ($listform = get_record_sql($strsql)) 	{
    	if ($form = get_record_sql("SELECT id, f6_3_6_5 FROM {$CFG->prefix}monit_form_rkp_prm_u WHERE listformid = {$listform->id}"))   {
  		   	$ret = $form->f6_3_6_5;
	    }
    }
    return $ret;
}

?>