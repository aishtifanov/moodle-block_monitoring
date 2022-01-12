<?php  // $Id: graph_teachers.php,v 1.2 2009/03/17 08:48:06 Shtifanov Exp $

    require_once("../../../config.php");
	require_once($CFG->libdir."/graphlib.php");
    require_once('../lib.php');

    $nm = date('n') - 1;
	$yid = get_current_edu_year_id();

	$datefromcurr = get_date_from_month_year($nm, $yid);
	$datefromprev = get_date_from_month_year($nm-1, $yid);

	$formslist[0] = '';
	$formslist[1] = '';

	$schools = array();

    $rayons = get_records('monit_rayon');

    $stat_graph = array();
	$zps = array();
	$a=0;
    foreach ($rayons as $rayon)	{    	$rid = $rayon->id;
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

		$stat_graph[$rid-1] = func_0_1m_();
		$zps[$a] = $rayon->name;
		$a++;
	}


    // print_r($stat_graph);

			$graphwidth = 800;
			$graphheight = 1200;

			$graph = new graph($graphwidth, $graphheight);
			$graph->parameter['title'] 			= get_string('f0_1u', 'block_monitoring');;
			$graph->x_data           			= $zps;
			$graph->y_data['stat_graph']   		= $stat_graph;
			$graph->y_order 					= array('stat_graph');
			$graph->y_format['stat_graph'] 		= array('colour' => 'blue','bar' => 'fill','bar_size' => 0.5);
			$graph->parameter['bar_spacing'] 	= 0;
			$graph->parameter['y_label_left']   = 'kol';
			$graph->parameter['label_size']		= '1';
			$graph->parameter['x_axis_angle']	= 90;
			$graph->parameter['x_label_angle']  = 0;
			$graph->parameter['tick_length'] 	= 0;
			$graph->parameter['shadow']         = 'none';
			error_reporting(5); // ignore most warnings such as font problems etc
			$graph->draw_stack();


    exit();
    // print $fid;



function func_0_1m_($whati=0, $numi=1)
{
 	global  $CFG, $formslist;

	if (!empty($formslist[$whati])) {
		$rec = get_record_sql("SELECT Sum(f0_1u) AS sum FROM {$CFG->prefix}monit_form_rkp_u
								where listformid in ({$formslist[$whati]})");
		$sum = $rec->sum;
	} else {
	   	$sum = '-';
	}

    return $sum;
}

?>