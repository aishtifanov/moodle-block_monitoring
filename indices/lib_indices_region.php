<?php // $Id: lib_indices_region.php,v 1.9 2009/03/17 09:16:12 Shtifanov Exp $


    $nm = date('n');
	$yid = get_current_edu_year_id();

for ($x=0; $x<=1; $x++)	{
	$vsegoIX[$x] = $vsegoX[$x] = $vsegoXI[$x] = $PRR_RO[$x] = $PRR_EKS[$x] = 0;
	$datefrom[$x] = get_date_from_month_year($nm-$x, $yid);
	$strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_9u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)={$datefrom[$x]}))";
	if ($rec = get_record_sql($strsql))  {		if (!empty($rec->sum)) $vsegoIX[$x] = $rec->sum;	}

	$strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_10u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)={$datefrom[$x]}))";
	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $vsegoX[$x] = $rec->sum;
	}

	$strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_11u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)={$datefrom[$x]}))";
	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $vsegoXI[$x] = $rec->sum;
	}

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_ro.*
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_ro ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_ro.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified={$datefrom[$x]}";
	$PRR_RO[$x] = get_record_sql($strsql);

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_eks.*
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_eks ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_eks.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified={$datefrom[$x]}";
	$PRR_EKS[$x] = get_record_sql($strsql);

}


// print_r ($vsegoXI);

function func_0_1r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';
    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_1u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";

	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $sum = $rec->sum;
	}

    return $sum;
}


function func_0_3r($whati=0, $numi=1)
{
 	global  $CFG, $db, $yid, $nm;

/*
   	$sum = '-';
	$datefrom = get_date_from_month_year(date('n')-$whati);


    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_3u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";

	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $sum = $rec->sum;
	}

*/
/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = 0;

    if ($metacolumns = $db->MetaColumns($CFG->prefix . 'monit_form_bkp_dolj'))  {
   	   // print_r($metacolumns);
       foreach($metacolumns as $metacolumn) {
   		    $l = strlen ($metacolumn->name);
   		    $koncovka = substr($metacolumn->name, $l - 3, 3);
  		    if ($metacolumn->name != 'id' &&  $metacolumn->name != 'listformid' && $koncovka != '_st')  {
			    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_dolj.{$metacolumn->name}) AS sum
						   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_dolj ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_dolj.listformid
						   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";

				if ($rec = get_record_sql($strsql))  {
					$sum += $rec->sum;
				}

  		    }
       }
    }

    if ($sum == 0)   $sum = '-';

    return $sum;
}


function func_0_4_0r($whati=0, $numi=1)
{
	global $CFG, $db, $yid, $nm;

/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = 0;

    if ($metacolumns = $db->MetaColumns($CFG->prefix . 'monit_form_bkp_pred')) {
   	 	 // print_r($metacolumns);
       foreach($metacolumns as $metacolumn) {  		    if ($metacolumn->name != 'id' &&  $metacolumn->name != 'listformid')  {			    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_pred.{$metacolumn->name}) AS sum
						   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_pred ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_pred.listformid
						   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";

				if ($rec = get_record_sql($strsql))  {
					$sum += $rec->sum;
				}
  		    }
       }
    }

    if ($sum == 0)   $sum = '-';

    return $sum;
}


$NUM_SCHOOLS = 0;
function func_0_5r($whati=0, $numi=1)
{
    global $NUM_SCHOOLS, $yid, $nm;

	$NUM_SCHOOLS = count_records('monit_school', 'isclosing', 0, 'yearid', $yid);
    return $NUM_SCHOOLS;
}


function func_0_6r($whati=0, $numi=1)
{
    $ret = count_records('monit_rayon') - 2;
    return $ret;
}


function func_0_8_0r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_8u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=-1))";

	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $sum = abs($rec->sum);
	}

    return $sum;
}


function func_0_8_1r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_8u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=1))";

	if ($rec = get_record_sql($strsql))  {
		if (!empty($rec->sum)) $sum = $rec->sum;
	}

    return $sum;
}


function func_1_1_1($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); $yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = -1;

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_d.fd11_
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_d ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_d.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{		if (isset($rec->fd11_) && !empty($rec->fd11_)) 	{
			list($fd11_fact,$fd11_rekv,$fd11_link) = explode("|", $rec->fd11_);
			$ret = 1;
    	}
    }

    return $ret;
}

function func_1_2($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

/*
    $nm = date('n');
	$yid = get_current_edu_year_id();
*/
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_ro.f1_2_0
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_ro ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_ro.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f1_2_0) && !empty($rec->f1_2_0)) 	{
			$ret = $rec->f1_2_0;
    	}
    }

    return $ret;
}


function func_1_3($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sumFOT = $sumFOT_u = 0;

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_f.f2f) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_f ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_f.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";
	if ($rec = get_record_sql($strsql))  {
		$sumFOT += $rec->sum*12;
	}

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_f.f2_6f) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_f ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_f.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";
	if ($rec = get_record_sql($strsql))  {
		$sumFOT_u += $rec->sum*12;
	}

	if ($sumFOT != 0) {
		$proc = number_format($sumFOT_u/$sumFOT*100, 1, ',', '');
		$ret = $proc.'%';
	} else {
		$ret = '-';
	}


/*
    for ($i=4; $i<=8; $i++)  {
       	$nf = 'f'.$i.'f';
	    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_f.$nf) AS sum
				   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_f ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_f.listformid
				   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";
		if ($rec = get_record_sql($strsql))  {			$sumFOT += $rec->sum;
		}

	}

    for ($i=9; $i<=11; $i++)  {
       	$nf = 'f'.$i.'f';
	    $strsql = "SELECT Sum({$CFG->prefix}monit_form_bkp_f.{$nf}) AS sum
				   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_bkp_f ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_bkp_f.listformid
				   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom))";

		if ($rec = get_record_sql($strsql))  {
			$sumFOT_u += $rec->sum;
		}
	}
	echo $sumFOT.'!<hr>!'.$sumFOT_u.'!<hr>!';
	$sumFOT += $sumFOT_u;
	if ($sumFOT != 0) {
		$proc = number_format($sumFOT_u/$sumFOT*100, 1, ',', '');
		$ret = $proc.'%';
	} else {
		$ret = '-';
	}
*/

    return $ret;
}

function func_1_4($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();
	$datefrom = get_date_from_month_year($nm-$whati, $yid);

	$ret = '-';

	$strsql = "SELECT {$CFG->prefix}monit_rayon.id, {$CFG->prefix}monit_school_listforms.shortname, {$CFG->prefix}monit_school_listforms.datemodified, {$CFG->prefix}monit_form_rkp_u.f1_5u
			   FROM (({$CFG->prefix}monit_rayon INNER JOIN {$CFG->prefix}monit_school ON {$CFG->prefix}monit_rayon.id = {$CFG->prefix}monit_school.rayonid) INNER JOIN {$CFG->prefix}monit_school_listforms ON {$CFG->prefix}monit_school.id = {$CFG->prefix}monit_school_listforms.schoolid) INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.shortname)='rkp_u') AND (({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) AND (({$CFG->prefix}monit_form_rkp_u.f1_5u)=1) AND ({$CFG->prefix}monit_rayon.regionid=1))";

    // echo $strsql;
	if ($rayons = get_records_sql($strsql))  {		foreach ($rayons as $rayon)	{			// echo $rayon->id . '<br>';
			$ret = count($rayons);
		}
	}

    return $ret;
}


function func_1_5($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); $yid = get_current_edu_year_id();

    $allcount = count_records('monit_school', 'isclosing', 0, 'yearid', $yid);

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f1_5u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) and ({$CFG->prefix}monit_form_rkp_u.f1_5u=1))";

	if ($rec = get_record_sql($strsql))  {		$proc = number_format($rec->sum/$allcount*100, 2, ',', '');
		$sum = $proc.'%';
	}

    return $sum;
}



function func_2_1r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); 	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f2_1u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) and ({$CFG->prefix}monit_form_rkp_u.f2_1u=1))";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum)) {
			$sum = $rec->sum;
		}
	}

    return $sum;
}


function func_2_3r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); $yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$sum = '-';

    $strsql = "SELECT Sum({$CFG->prefix}monit_form_rkp_u.f2_3u) AS sum
			   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
			   WHERE ((({$CFG->prefix}monit_school_listforms.datemodified)=$datefrom) and ({$CFG->prefix}monit_form_rkp_u.f2_3u=1))";

	if ($rec = get_record_sql($strsql))  {		if (isset($rec->sum)) {
			$sum = $rec->sum;
		}
	}

    return $sum;
}

function func_2_4_1($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); $yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_ro.f2_4_1r
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_ro ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_ro.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f2_4_1r) && !empty($rec->f2_4_1r)) 	{
			$ret = $rec->f2_4_1r;
    	}
    }

    return $ret;
}


function func_2_4_2($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); 	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_ro.f2_4_2r
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_ro ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_ro.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f2_4_2r) && !empty($rec->f2_4_2r)) 	{
			$ret = $rec->f2_4_2r;
    	}
    }

    return $ret;
}


function func_2_5r($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); 	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_prr_ro.f2_5_1r, {$CFG->prefix}monit_form_rkp_prr_ro.f2_5_1g
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_prr_ro ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_prr_ro.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f2_5_1r) && !empty($rec->f2_5_1r) && isset($rec->f2_5_1g) && !empty($rec->f2_5_1g) ) 	{
			$ret = 1;
    	}
    }

    return $ret;
}


function func_3_2($whati=0, $numi=1)
{
 	global  $CFG, $yid, $nm;

    // $nm = date('n'); 	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_2
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f3_2) && !empty($rec->f3_2)) 	{
			$ret = $rec->f3_2;
    	}
    }

    return $ret;
}


$f3_3_1r[0] = $f3_3_2r[0] = 0;
$f3_3_1r[1] = $f3_3_2r[1] = 0;

function func_3_3_1($whati=0, $numi=1)
{
 	global  $CFG, $f3_3_1r, $f3_3_2r, $vsegoXI, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_3_1r as sum
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum) && !empty($rec->sum)) 	{			$f3_3_1r[$whati] = $rec->sum;
		    if ($vsegoXI[$whati] != 0) {				$proc = number_format($rec->sum/$vsegoXI[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->sum.' шт.)';
			}
    	}
    }


    return $ret;
}


function func_3_3_2($whati=0, $numi=1)
{
 	global  $CFG, $f3_3_1r, $f3_3_2r, $vsegoXI, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_3_2r as sum
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum) && !empty($rec->sum)) 	{			$f3_3_2r[$whati] = $rec->sum;
		    if ($vsegoXI[$whati] != 0) {
				$proc = number_format($rec->sum/$vsegoXI[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->sum.' шт.)';
			}
    	}
    }

    return $ret;
}


function func_3_3($whati=0, $numi=1)
{
 	global  $CFG, $f3_3_1r, $f3_3_2r, $vsegoXI, $yid, $nm;

   	$ret = '-';

	func_3_3_1($whati);
	func_3_3_2($whati);

	$sum = $f3_3_1r[$whati] + $f3_3_2r[$whati];
    if ($vsegoXI[$whati] != 0) {
		$proc = number_format($sum/$vsegoXI[$whati]*100, 1, ',', '');
		$ret = $proc.'%('.$sum.' шт.)';
	}

    return $ret;
}


function func_3_4($whati=0, $numi=1)
{
 	global  $CFG, $vsegoXI, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';
    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_4r as sum
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum) && !empty($rec->sum)) 	{
		    if ($vsegoXI[$whati] != 0) {
				$proc = number_format($rec->sum/$vsegoXI[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->sum.' шт.)';
			}
    	}
    }

    return $ret;
}


$f3_5_1r[0] = $f3_5_2r[0] = 0;
$f3_5_1r[1] = $f3_5_2r[1] = 0;

function func_3_5_1($whati=0, $numi=1)
{
 	global  $CFG, $f3_5_1r, $f3_5_2r, $vsegoXI, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_5_1r as sum
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum) && !empty($rec->sum)) 	{
			$f3_5_1r[$whati] = $rec->sum;
		    if ($vsegoXI[$whati] != 0) {
				$proc = number_format($rec->sum/$vsegoXI[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->sum.' шт.)';
			}
    	}
    }


    return $ret;
}


function func_3_5_2($whati=0, $numi=1)
{
 	global  $CFG, $f3_5_1r, $f3_5_2r, $vsegoXI, $yid, $nm;

    // $nm = date('n');	$yid = get_current_edu_year_id();

	$datefrom = get_date_from_month_year($nm-$whati, $yid);

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f3_5_2r as sum
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified=$datefrom";

	if ($rec = get_record_sql($strsql))  {
		if (isset($rec->sum) && !empty($rec->sum)) 	{
			$f3_5_2r[$whati] = $rec->sum;
		    if ($vsegoXI[$whati] != 0) {
				$proc = number_format($rec->sum/$vsegoXI[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->sum.' шт.)';
			}
    	}
    }

    return $ret;
}


function func_3_5($whati=0, $numi=1)
{
 	global  $CFG, $f3_5_1r, $f3_5_2r, $vsegoXI, $yid, $nm;

   	$ret = '-';

	func_3_5_1($whati);
	func_3_5_2($whati);

	$sum = $f3_5_1r[$whati] + $f3_5_2r[$whati];
    if ($vsegoXI[$whati] != 0) {
		$proc = number_format($sum/$vsegoXI[$whati]*100, 1, ',', '');
		$ret = $proc.'%('.$sum.' шт.)';
	}

    return $ret;
}



function func_3_6_1($whati=0, $numi=1)
{
 	global  $PRR_RO;

   	$ret = '-';

    if (isset($PRR_RO[$whati]->f3_6_1g)) {
		$ret = $PRR_RO[$whati]->f3_6_1g;
	}

    return $ret;
}


function func_3_6_2($whati=0, $numi=1)
{
 	global  $PRR_RO;

   	$ret = '-';

    if (isset($PRR_RO[$whati]->f3_6_2g)) {
		$ret = $PRR_RO[$whati]->f3_6_2g;
	}

    return $ret;
}


function func_3_7_1($whati=0, $numi=1)
{
 	global  $PRR_RO;

   	$ret = '-';

    if (isset($PRR_RO[$whati]->f3_7_1g)) {
		$ret = $PRR_RO[$whati]->f3_7_1g;
	}

    return $ret;
}


function func_3_7_2($whati=0, $numi=1)
{
 	global  $PRR_RO;

   	$ret = '-';

    if (isset($PRR_RO[$whati]->f3_7_2g)) {
		$ret = $PRR_RO[$whati]->f3_7_2g;
	}

    return $ret;
}


function func_3_8($whati=0, $numi=1)
{
 	global  $PRR_RO;

   	$ret = '-';

    if (isset($PRR_RO[$whati]->f3_8g)) {
		$ret = $PRR_RO[$whati]->f3_8;
	}

    return $ret;
}



function func_4_2($whati=0, $numi=1)
{
 	global  $CFG, $datefrom;

    $sum = '-';

	$rec01u = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_1u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=1))");

	$rec02u = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_2u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=1))");

	if ($rec01u && $rec02u) {
		if ($rec02u->sum != 0)	{
			$sum = number_format($rec01u->sum/$rec02u->sum, 2, ',', '');
		}
	}
    return $sum;
}

function func_4_3($whati=0, $numi=1)
{
 	global  $CFG, $datefrom;

    $sum = '-';

	$rec01u = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_1u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=-1))");

	$rec02u = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f0_2u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f0_8u=-1))");

	if ($rec01u && $rec02u) {
		if ($rec02u->sum != 0)	{
			$sum = number_format($rec01u->sum/$rec02u->sum, 2, ',', '');
		}
	}
    return $sum;
}


function func_4_4($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $vsegoX, $vsegoXI;

    $sum = '-';

	$rec4_0_3 = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f4_0_3) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f4_0_3=1))");
	if ($rec4_0_3) {
		if ($rec4_0_3->sum != 0)	{
		    $sumX_XI = $vsegoX[$whati] + $vsegoXI[$whati];
			$sum = number_format($sumX_XI/$rec4_0_3->sum, 2, ',', '');
		}
	}

    return $sum;
}


function func_5_1($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $NUM_SCHOOLS;

   	$sum = '-';
	$rec = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f5_1u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f5_1u=1))");

    if ($rec)	{
		if ($NUM_SCHOOLS != 0) {
			$proc = number_format($rec->sum/$NUM_SCHOOLS*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	}

    return $sum;
}


function func_5_3($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $NUM_SCHOOLS;

   	$sum = '-';
	$rec = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f5_3_0u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f5_3_0u=1))");

    if ($rec)	{
		if ($NUM_SCHOOLS != 0) {
			$proc = number_format($rec->sum/$NUM_SCHOOLS*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	}

    return $sum;
}


function func_5_4($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $NUM_SCHOOLS;

   	$sum = '-';
	$rec = get_record_sql("SELECT Sum({$CFG->prefix}monit_form_rkp_u.f5_4_1u) AS sum
		   FROM {$CFG->prefix}monit_school_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_u ON {$CFG->prefix}monit_school_listforms.id = {$CFG->prefix}monit_form_rkp_u.listformid
		   WHERE (({$CFG->prefix}monit_school_listforms.datemodified={$datefrom[$whati]}) and ({$CFG->prefix}monit_form_rkp_u.f5_4_1u=1))");

    if ($rec)	{
		if ($NUM_SCHOOLS != 0) {
			$proc = number_format($rec->sum/$NUM_SCHOOLS*100, 1, ',', '');
		} else {
			$proc = '0.0';
		}
		$sum = $proc.'%('.$rec->sum.' шт.)';
	}

    return $sum;
}



$f5_5_1[0]= $f5_5_1[1] = 0;

function func_5_5_1($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $f5_5_1;

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f5_5_1
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified={$datefrom[$whati]}";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f5_5_1) && !empty($rec->f5_5_1)) 	{
			$ret = $rec->f5_5_1;
			$f5_5_1 = $rec->f5_5_1;
    	}
    }

    return $ret;
}


function func_5_5_2($whati=0, $numi=1)
{
 	global  $CFG, $datefrom, $f5_5_1;

   	$ret = '-';

    $strsql = "SELECT {$CFG->prefix}monit_form_rkp_ege.f5_5_2
			   FROM {$CFG->prefix}monit_region_listforms INNER JOIN {$CFG->prefix}monit_form_rkp_ege ON {$CFG->prefix}monit_region_listforms.id = {$CFG->prefix}monit_form_rkp_ege.listformid
			   WHERE {$CFG->prefix}monit_region_listforms.datemodified={$datefrom[$whati]}";

	if ($rec = get_record_sql($strsql))  {
//    if ($rec1 = get_record('monit_region_listforms', 'shortname', 'rkp_d', 'datemodified', $datefrom))	{
		if (isset($rec->f5_5_2) && !empty($rec->f5_5_2)) 	{
		    if ($f5_5_1[$whati] != 0) {
				$proc = number_format($rec->f5_5_2/$f5_5_1[$whati]*100, 1, ',', '');
				$ret = $proc.'%('.$rec->f5_5_2.' шт.)';
			}
    	}
    }

    return $ret;
}


function func_6_2_1($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_2_1)) {
		$ret = $PRR_EKS[$whati]->f6_2_1;
	}

    return $ret;
}

function func_6_2_2($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_2_2)) {
		$ret = $PRR_EKS[$whati]->f6_2_2;
	}

    return $ret;
}

function func_6_2_3($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_2_3)) {
		$ret = $PRR_EKS[$whati]->f6_2_3;
	}

    return $ret;
}


function func_6_2_4($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_2_4)) {
		$ret = $PRR_EKS[$whati]->f6_2_4;
	}

    return $ret;
}


function func_6_2_5($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_2_5)) {
		$ret = $PRR_EKS[$whati]->f6_2_5;
	}

    return $ret;
}


function func_6_3_1($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_3_1)) {
		$ret = $PRR_EKS[$whati]->f6_3_1;
	}

    return $ret;
}

function func_6_3_2($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_3_2)) {
		$ret = $PRR_EKS[$whati]->f6_3_2;
	}

    return $ret;
}

function func_6_3_3($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_3_3)) {
		$ret = $PRR_EKS[$whati]->f6_3_3;
	}

    return $ret;
}


function func_6_3_4($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_3_4)) {
		$ret = $PRR_EKS[$whati]->f6_3_4;
	}

    return $ret;
}


function func_6_3_5($whati=0, $numi=1)
{
 	global  $PRR_EKS;

   	$ret = '-';

    if (isset($PRR_EKS[$whati]->f6_3_5)) {
		$ret = $PRR_EKS[$whati]->f6_3_5;
	}

    return $ret;
}









//  ==========
//  ==========//  ==========
//  ==========//  ==========//  ==========
//  ==========//  ==========//  ==========//  ==========
//  ==========//  ==========//  ==========//  ==========//  ==========
//  ==========//  ==========//  ==========//  ==========//  ==========//  ==========
/*
function func_12_4($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '65%';
  } else {
    $proc = 0;
    $datefrom = get_date_from_month_year(date('n'));
	$strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";
    if ($listforms = get_records_sql($strsql)) 	{        $formsarray = array();
	    foreach ($listforms as $lf)  {
	        $formsarray[] = $lf->id;
	    }
	    $formslist = implode(',', $formsarray);
		$sum1 = get_record_sql("SELECT Sum(f0_6u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ($formslist)");
		$sum2 = get_record_sql("SELECT Sum(f0_7u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ($formslist)");
		if ($sum1->sum == 0)  $proc = '0%';
		else  $proc = number_format($sum2->sum/$sum1->sum*100, 2, ',', '');
	}
	return $proc.'%';
  }
}
*/

/* f0_0_0m
function func_12_5($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '100%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_rayon');
     $datefrom = get_date_from_month_year(date('n'));
 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_rayon_listforms
	 		   WHERE (shortname='rkp_prm_mo') and (datemodified=$datefrom)";
     if ($listforms = get_records_sql($strsql)) 	{
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
		 $count = count_records_sql("SELECT * FROM {$CFG->prefix}monit_form_rkp_prm_mo
									where listformid in ($formslist) AND f0_0_0m = 1");
	 	 if ($count == 0) return '0%';
	 	 else {			$proc = number_format($count/$allcount*100, 2, ',', '');
	 	 }
	 }
 	 return $proc.'%';
  }
}
*/

/*
function func_12_5($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '100%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_rayon');
     $datefrom = get_date_from_month_year(date('n'));

 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";

     if ($listforms = get_records_sql($strsql)) 	{//     	 print_r($listforms);
//     	 echo '<hr>';
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
	     $strsql = "SELECT * FROM {$CFG->prefix}monit_form_rkp_u
									where listformid in ($formslist) AND f1_5u = 1";
		 if ($forms = get_records_sql($strsql))   {//     	 print_r($forms);
//     	 echo '<hr>';

	  		 unset($formsarray);
	  		 unset($formslist);
		     $formsarray = array();
			 foreach ($forms as $frm)  {
			     $formsarray[] = $frm->listformid;
			 }
		     $formslist = implode(',', $formsarray);

		     $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
									where id in ($formslist)";
		 	 if ($listforms = get_records_sql($strsql))  {//     	 print_r($listforms);
//     	 echo '<hr>';

		  		 unset($formsarray);
		  		 unset($formslist);
			     $formsarray = array();
				 foreach ($listforms as $lf)  {
				     $formsarray[] = $lf->schoolid;
				 }
			     $formslist = implode(',', $formsarray);

			     $strsql = "SELECT DISTINCT rayonid FROM {$CFG->prefix}monit_school
									where id in ($formslist)";
			     if ($forms = get_records_sql($strsql))  {
//     	 print_r($forms);
//     	 echo '<hr>';
				     $count = count($forms); // count_records_sql($strsql);
//     	 echo $count.'<hr>';
//     	 echo $allcount.'<hr>';
				 	 if ($count == 0) return '0%';
				 	 else {
						$proc = number_format($count/$allcount*100, 2, ',', '');
			 		 }
			 	 }
			 }
		}
	 }
 	 return $proc.'%';
  }
}


function func_12_6($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '56%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_school');
     $datefrom = get_date_from_month_year(date('n'));
 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";
     if ($listforms = get_records_sql($strsql)) 	{
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
		 if ($forms = get_records_sql("SELECT * FROM {$CFG->prefix}monit_form_rkp_u
									where listformid in ($formslist) AND f1_5u = 1"))  {			 $count = count($forms);
		 	 if ($count == 0) return '0%';
		 	 else {
				$proc = number_format($count/$allcount*100, 2, ',', '');
		 	 }
		 }
	 }
 	 return $proc.'%';
  }
}


function func_13_1($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '100%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_rayon');
     $datefrom = get_date_from_month_year(date('n'));

 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";

     if ($listforms = get_records_sql($strsql)) 	{
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
	     $strsql = "SELECT * FROM {$CFG->prefix}monit_form_rkp_u
									where listformid in ($formslist) AND f2_1u = 1";
		 if ($forms = get_records_sql($strsql))   {
	  		 unset($formsarray);
	  		 unset($formslist);
		     $formsarray = array();
			 foreach ($forms as $frm)  {
			     $formsarray[] = $frm->listformid;
			 }
		     $formslist = implode(',', $formsarray);

		     $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
									where id in ($formslist)";
		 	 if ($listforms = get_records_sql($strsql))  {		  		 unset($formsarray);
		  		 unset($formslist);
			     $formsarray = array();
				 foreach ($listforms as $lf)  {
				     $formsarray[] = $lf->schoolid;
				 }
			     $formslist = implode(',', $formsarray);

			     $strsql = "SELECT DISTINCT rayonid FROM {$CFG->prefix}monit_school
									where id in ($formslist)";
			     if ($forms = get_records_sql($strsql))  {
                  	 $count = count($forms);
				 	 if ($count == 0) return '0%';
				 	 else {
						$proc = number_format($count/$allcount*100, 2, ',', '');
				 	 }
				 }
			 }
		}
	 }
 	 return $proc.'%';
  }
}


function func_13_2($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '65%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_school');
     $datefrom = get_date_from_month_year(date('n'));
 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";
     if ($listforms = get_records_sql($strsql)) 	{
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
		 if ($forms = get_records_sql("SELECT * FROM {$CFG->prefix}monit_form_rkp_u
									where listformid in ($formslist) AND f2_1u = 1"))  {			 $count = count($forms);
		 	 if ($count == 0) return '0%';
		 	 else {
				$proc = number_format($count/$allcount*100, 2, ',', '');
		 	 }
		 }
	 }
 	 return $proc.'%';
  }
}


function func_13_3($whati=0, $numi=1)
{
  global $CFG;

  if ($whati == 0)  {
  	return '8%';
  } else {
     $proc = 0;
     $allcount = count_records('monit_school');
     $datefrom = get_date_from_month_year(date('n'));
 	 $strsql = "SELECT * FROM {$CFG->prefix}monit_school_listforms
	 		   WHERE (shortname='rkp_u') and (datemodified=$datefrom)";
     if ($listforms = get_records_sql($strsql)) 	{
	     $formsarray = array();
		 foreach ($listforms as $lf)  {
		     $formsarray[] = $lf->id;
		 }
	     $formslist = implode(',', $formsarray);
		 if ($forms = get_records_sql("SELECT * FROM {$CFG->prefix}monit_form_rkp_u
									where listformid in ($formslist) AND f2_3u = 1"))  {
			 $count = count($forms);
		 	 if ($count == 0) return '0%';
		 	 else {
				$proc = number_format($count/$allcount*100, 2, ',', '');
		 	 }
		 }
	 }
 	 return $proc.'%';
  }
}
*/

?>
