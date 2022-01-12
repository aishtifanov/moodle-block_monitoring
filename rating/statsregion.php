<?php  // $Id: statsregion.php,v 1.8 2012/12/06 12:30:26 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('regionrating_form.php');
	require_once($CFG->libdir.'/formslib.php');    
    require_once('lib_rating.php');
    
	define("NUMBER_OF_STATUS", 6);
	
    $rid = optional_param('rid', 0, PARAM_INT);
    $sid = optional_param('sid', 0, PARAM_INT);            // School id

    require_login();

	// $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $nm = 9;    

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
    $rayon_operator_is = false;
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}
/*
	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}
*/
    $strrayons = get_string('rayons', 'block_monitoring');
	$strreportregion = get_string('statsregion', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strreportregion";
    print_header_mou("$SITE->shortname: $strreportregion", $SITE->fullname, $breadcrumbs);

    $currenttab = 'reports';
    include('tabs.php');

    $currenttab2 = 'statsregion';
    include('tabs2.php');    

    print_tabs_years_rating("statsregion.php?a=0", $rid, $sid, $yid);

    print_heading(get_string('statsregion', 'block_monitoring'), 'center', 3);
    $table = table_rating_stat($yid, $nm);
    print_color_table($table);
    
    echo '<hr>';
	$options = array('rid' => 1, 'yid' => $yid, 'trunc' => 'no', 'del' => 'yes');
	echo '<table align="center" border=0><tr><td>';
	// print_single_button("recalcrating.php", $options, get_string('recalcrating', 'block_monitoring'));
	echo '</td></tr></table>';


    print_footer();

	
function table_rating_stat($yid, $nm)
{
	global $CFG;
	/*
    if ($yid < NEW_CRITERIA_YEARID)   {
	   $rkps = array('rating_1' , 'rating_2');
       $sqlexclude = '';
    } else {   
       $rkps = array('rating_n', 'rating_o', 'rating_s', 'rating_k');
       $sqlexclude = "AND shortname not in ('rating_1', 'rating_2')";
    } 
    */
       
    $rkps = get_listnameforms($yid, 'school'); // array('zp_d');
    $strlist = implode ('\',\'', $rkps);
    $sqlexclude = "AND shortname in ('$strlist')";
    // print $sqlexclude;
    
    $countrkps = count($rkps);
    
	$datefrom = get_date_from_month_year($nm, $yid);
		
    $table = new stdClass();
	$table->head[0] = get_string('rayon', 'block_monitoring');
	$table->align[0] = 'left';
	for ($i=1; $i<=NUMBER_OF_STATUS; $i++)  {
		$table->head[$i] = get_string('status'.$i, 'block_monitoring');
		$table->align[$i] = 'center';
    }
	$table->class = 'moutable';

	$totalcount = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	if($allrayons = get_records_sql("SELECT id, name FROM {$CFG->prefix}monit_rayon ORDER BY number"))   {
		$i = -1;
 		foreach ($allrayons as $rayon) 	{
			
			$allcount = count_records('monit_school', 'rayonid', $rayon->id, 'yearid', $yid, 'isclosing', 0);
			//$allcount = count_records('monit_school', 'rayonid', $rayon->id, 'yearid', $yid);
			 		
			$allcount *= $countrkps;		 	

			$table->data[++$i][0] = $rayon->name;

			$count_except_new = 0;
			for ($status=1; $status<=NUMBER_OF_STATUS; $status++)  {
					$strsql = "SELECT id, rayonid, status
							   FROM {$CFG->prefix}monit_rating_listforms
					  		   WHERE (rayonid = $rayon->id) and (status = $status) and (datemodified=$datefrom) $sqlexclude";
					// echo $strsql . '<hr>'; 
				    $countforms = 0;
				    if ($stat = get_records_sql($strsql)) 	{
				    	$countforms = count ($stat);
                    }

	    			$proc = number_format($countforms/$allcount*100, 2, ',', '');
					$table->data[$i][$status] = "$countforms<br>($proc%)";
     				$count_except_new += $countforms;
     				$totalcount[$status] += $countforms; 
         	}

            $countforms = $allcount - $count_except_new;
			$proc = number_format($countforms/$allcount*100, 2, ',', '');
			$table->data[$i][1] = "$countforms<br>($proc%)";
			$totalcount[1] += $countforms; 
		}
		$table->data[++$i][0] = '<b>' . get_string('vsego', 'block_monitoring') . '</b>';
		for ($status=1; $status<=NUMBER_OF_STATUS; $status++)  {
			$table->data[$i][$status] = '<b>' . $totalcount[$status] . '</b>';
		}	
	}

     return $table;
}    

?>