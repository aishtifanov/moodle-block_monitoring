<?php // $Id: lib_indices_rayon.php,v 1.8 2008/10/31 07:31:36 Shtifanov Exp $


require_once('lib_indices_school.php');


    $nm = date('n');
	$yid = get_current_edu_year_id();

	$datefromcurr = get_date_from_month_year($nm, $yid);
	$datefromprev = get_date_from_month_year($nm-1, $yid);

	$formslist[0] = '';
	$formslist[1] = '';

	$schools = array();

	$strsql =  "SELECT *  FROM {$CFG->prefix}monit_school
   				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid";

 	if ($schools = get_records_sql($strsql))	{
        $schoolsarray = array();
	    foreach ($schools as $sa)  {
	        $schoolsarray[] = $sa->id;
	    }
	    $schoolslist = implode(',', $schoolsarray);

		$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
		 		   WHERE (schoolid in ($schoolslist)) and (shortname='rkp_u') and (datemodified=$datefromcurr)";
	    if ($listforms = get_records_sql($strsql)) 	{
	        $formsarray = array();
		    foreach ($listforms as $lf)  {
		        $formsarray[] = $lf->id;
		    }
		    $formslist[0] = implode(',', $formsarray);
		}

		$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
		 		   WHERE (schoolid in ($schoolslist)) and (shortname='rkp_u') and (datemodified=$datefromprev)";
	    if ($listforms = get_records_sql($strsql)) 	{
	        $formsarray = array();
		    foreach ($listforms as $lf)  {
		        $formsarray[] = $lf->id;
		    }
		    $formslist[1] = implode(',', $formsarray);
		}

	}


function func_0_1m($whati=0, $numi=1)
{ 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ({$formslist[$whati]})");
		$sum = $rec->sum;
	} else {	   	$sum = '-';
	}

    return $sum;
}


function func_0_3m($whati=0, $numi=1)
{
 	global $CFG, $sid, $schools;

   	$sum = 0;

 	if ($schools)	{
	    foreach ($schools as $sa)  {
	        $sid = $sa->id;
	        $k = func_f0_3u($whati);
	        $sum += $k;
	        // print $sa->name . ' ' . $k.'<hr>';
	    }
	}

    if ($sum == 0)   $sum = '-';

    return $sum;

}


function func_0_4m($whati=0, $numi=1)
{
 	global $CFG, $sid, $schools;

   	$sum = 0;

 	if ($schools)	{	    foreach ($schools as $sa)  {
	        $sid = $sa->id;
	        $k = func_f0_4u($whati);
	        $sum += $k;
	        // print $sa->name . ' ' . $k.'<hr>';
	    }
	}

    if ($sum == 0)   $sum = '-';

    return $sum;
}


function func_0_5m($whati=0, $numi=1)
{
/* 	global $CFG, $schools;

    if ($whati == 1) return '-';
   	$sum = 0;
 	if ($schools)	{
        $sum = count($schools);
	}
    if ($sum == 0)   $sum = '-';
*/

 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_8u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=-1)");
		$sum = abs($rec->sum);
		$rec = get_record_sql("SELECT Sum(f0_8u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=1)");
		$sum += abs($rec->sum);
	} else {
	   	$sum = '-';
	}

    return $sum;
}



function func_0_8_0m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_8u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=-1)");
		$sum = abs($rec->sum);
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_0_8_1m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_8u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=1)");
		$sum = abs($rec->sum);
	} else {
	   	$sum = '-';
	}

    return $sum;
}

function func_0_9m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_9u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ({$formslist[$whati]})");
		$sum = $rec->sum;
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_0_10m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_10u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ({$formslist[$whati]})");
		$sum = $rec->sum;
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_0_11m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_11u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ({$formslist[$whati]})");
		$sum = $rec->sum;
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_1_5m($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f1_5u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f1_5u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {			$proc = '0.0';		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_2_1m($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f2_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f2_1u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {
		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}

function func_2_3m($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f2_3u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f2_3u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {
		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}

function func_4_2m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

    $sum = '-';
	if (!empty($formslist[$whati])) {
		$rec01u = get_record_sql("SELECT Sum(f0_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=1)");
		$rec02u = get_record_sql("SELECT Sum(f0_2u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=1)");
		if ($rec01u && $rec02u) {			if ($rec02u->sum != 0)	{				$sum = number_format($rec01u->sum/$rec02u->sum, 2, ',', '');			}		}
	}

    return $sum;
}

function func_4_3m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

    $sum = '-';
	if (!empty($formslist[$whati])) {
		$rec01u = get_record_sql("SELECT Sum(f0_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=-1)");
		$rec02u = get_record_sql("SELECT Sum(f0_2u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f0_8u=-1)");
		if ($rec01u && $rec02u) {
			if ($rec02u->sum != 0)	{
				$sum = number_format($rec01u->sum/$rec02u->sum, 2, ',', '');
			}
		}
	}

    return $sum;
}


function func_4_4m($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

    $sum = '-';
	if (!empty($formslist[$whati])) {
		$rec4_0_3 = get_record_sql("SELECT Sum(f4_0_3) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f4_0_3=1)");
		if ($rec4_0_3) {
			if ($rec4_0_3->sum != 0)	{			    $sum = 0;
				$rec10u = get_record_sql("SELECT Sum(f0_10u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
										where listformid in ({$formslist[$whati]})");
				if ($rec10u) {
					$sum = $rec10u->sum;
				}
				$rec11u = get_record_sql("SELECT Sum(f0_11u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
										where listformid in ({$formslist[$whati]})");
				if ($rec11u) {
					$sum += $rec11u->sum;
				}
				$sum = number_format($sum/$rec4_0_3->sum, 2, ',', '');
			}
		}

	}

    return $sum;
}



function func_5_2m($whati=0, $numi=1)
{
 	global $CFG, $rid;

    $nm = date('n');
	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
	 		   WHERE (rayonid = $rid) and (shortname='rkp_prm_eks') and (datemodified=$datefrom)";
    if ($listform = get_record_sql($strsql)) 	{    	$form = get_record('monit_form_rkp_prm_eks', 'listformid' , $listform->id);
    	$ret = $form->f5_2m;
    } else {
	    $ret = '-';    }

    return $ret;
}


function func_5_2dm($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f5_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f5_1u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {
		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_5_3dm($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f5_3_0u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f5_3_0u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {
		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}


function func_5_4dm($whati=0, $numi=1)
{
 	global  $CFG, $formslist, $schools;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f5_4_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where (listformid in ({$formslist[$whati]})) and (f5_4_1u=1)");
	 	if ($schools)	{
 	       $count = count($schools);
		} else {
		   $count = 0;
		}
		if ($count != 0) {
			$proc = number_format($rec->sum/$count*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	} else {
	   	$sum = '-';
	}

    return $sum;
}

?>