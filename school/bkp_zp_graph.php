<?php  // $Id: bkp_zp_graph.php,v 1.1 2008/03/31 11:03:18 Shtifanov Exp $

    require_once("../../../config.php");
	require_once($CFG->libdir."/graphlib.php");
    require_once('../lib.php');

    $fid = required_param('fid', PARAM_INT);       // Form id

    // print $fid;

    if ($fid != 0)  {
    	$rec = get_record('monit_form_bkp_zp', 'listformid', $fid);
    } else {    	exit(1);    }

	if (isset($rec)) {
	  $arrec = (array)$rec;
	}
    // print_r($arrec);
    // echo '<hr>';


    $razdel = get_record ('monit_razdel', 'shortname', 'bkp_zp');

    // print_r($razdel);

    if ($razdel)	{
  		$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id);

 	    // print_r($fields);
 	    // echo '<hr>';

  		if ($fields) {

			$nbfields = 19;
			$graphwidth = 640;
			$graphheight = 480;

			$zps = array();
			$percentss = array();

			$a=0;
			 //For ($i=0; $i<$nbfields; $i++) {
		    foreach ($fields as $field)  {

                if ($field->name_field == 'f1') continue;

		    	// $zps[$a] = translitfield($field->name_field);
		    	$zps[$a] = $field->name;
				if (isset($arrec[$field->name_field]) &&  $arrec[$field->name_field]> 0) {
					$percentss[$a] = $arrec[$field->name_field];
				}
				else  {
					$percentss[$a] = 0;
				}
				$a = $a+1;
			}

			/*
			print_r($zps);
			echo '<hr>';
			print_r($percentss);
			echo '<hr>';
			*/

			$graph = new graph($graphwidth, $graphheight);
			$graph->parameter['title'] 			= '';
			$graph->x_data           			= $zps;
			$graph->y_data['percentss']   		= $percentss;
			$graph->y_order 					= array('percentss');
			$graph->y_format['percentss'] 		= array('colour' => 'blue','bar' => 'fill','bar_size' => 0.6);
			$graph->parameter['bar_spacing'] 	= 0;
			$graph->parameter['y_label_left']   = '%';
			$graph->parameter['label_size']		= '1';
			$graph->parameter['x_axis_angle']	= 90;
			$graph->parameter['x_label_angle']  = 0;
			$graph->parameter['tick_length'] 	= 0;
			$graph->parameter['shadow']         = 'none';
			error_reporting(5); // ignore most warnings such as font problems etc
			$graph->draw_stack();
		}
	}


?>