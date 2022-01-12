<?php // $Id: selectdata.php,v 1.4 2012/12/06 12:30:26 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
	require_once('../lib_excel.php');
    require_once('../../mou_ege/lib_ege.php');    
    require_once('lib_rating.php');

    $rid=1;
    $sid=0;
    $yid = optional_param('yid', 0, PARAM_INT);       		// Year id
    // $nm  = optional_param('nm', 9, PARAM_INT);  // Month number
    $fldid = optional_param('fldid', '');       // Shortname form
    $fldid2 = optional_param('fldid2', '');       // Shortname form    
    $nm = 9;

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) { //  && !$rayon_operator_is ) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    
   	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
        $shortname  = optional_param('shortname');  // Month number
      	$table = table_selectdata($yid, $shortname, $fldid, $fldid2);
        print_table_to_excel($table);
        exit();
	}

    init_rating_parameters($yid, $shortname, $select, $order);    
    $select .=  " AND edizm <> 'null'";

	$strtitle = get_string('ratingrayon', 'block_monitoring');
	
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	if ($admin_is  || $region_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	}
	$breadcrumbs .= " -> $strtitle";	
    print_header_mou("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

    $currenttab = 'reports';
    include('tabs.php');

    $currenttab2 = 'selectdata';
    include('tabs2.php');    
   
    print_tabs_years_rating("selectdata.php?a=0", $rid, $sid, $yid);
    echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_rating_level("selectdata.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=", $shortname, $yid);
    echo '<br>';
    listbox_rating_sourcedata("selectdata.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&fldid=", $shortname, $fldid);
    echo '<br>';
    listbox_rating_sourcedata("selectdata.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;sn=$shortname&fldid=$fldid&fldid2=", $shortname, $fldid2, 2);
    echo '</table>';
	echo '<p>';
	
    if ($fldid != '' && $fldid2 != '') {    
    	$options = array('action'=> 'excel', 'shortname' => $shortname, 'fldid' => $fldid, 'fldid2' => $fldid2, 
                         'yid' => $yid, 'nm' => $nm,  'sesskey' => $USER->sesskey);

       	echo '<center>';
        print_single_button("selectdata.php", $options, get_string('downloadexcel'));
        echo '</center><p></p>';

    	$table = table_selectdata($yid, $shortname, $fldid, $fldid2);
      	print_color_table($table);
    
       	echo '<center>';
        print_single_button("selectdata.php", $options, get_string('downloadexcel'));
        echo '</center>';
    }
	// print_string('remarkyear', 'block_monitoring');
    print_footer();



// Display list rating level as popup_form
function listbox_rating_sourcedata($scriptname, $shortname, $fldid, $numform=1)
{
	global $CFG, $yid;

	get_name_otchet_year ($yid, $a, $b);
	
	$criteriamenu = array();
 	$criteriamenu[0] = 'Выберите исходное данное  ...';

	$strsql = "SELECT f.id, f.name, f.name_field FROM mdl_monit_razdel r
                inner join mdl_monit_razdel_field f on r.id=f.razdelid
                where shortname = '$shortname'";
	if ($criterias = get_records_sql($strsql)) 	{			   
   		foreach($criterias as $criteria)	{
   			$criteriamenu[$criteria->name_field] = $criteria->name_field . '. ' . $criteria->name;
	  	}
	}  	
  	
  	echo '<div align=center>';
    popup_form($scriptname, $criteriamenu, 'switchsrc'.$numform, $fldid, '', '', '', false);
  	echo '</div>';

  return 1;
}


function  table_selectdata($yid, $shortname, $fldid, $fldid2)	
{
	global $CFG, $admin_is, $region_operator_is, $rayon_operator_is;

    $numberf = get_string('ratingnum', 'block_monitoring');
    $strname = get_string('school', 'block_monitoring');
    $valueofpokazatel = get_string('valueofpokazatel', 'block_monitoring');

    $table = new stdClass();
    $table->head  = array ('№', 'id района', $strname, $fldid, $fldid2);
    $table->align = array ("center", "center", "left", "center", "center");
	$table->width = '90%';
    $table->size = array ('5%', '5%', '90%', '5%', '5%');
    $table->columnwidth = array (7, 7, 100, 15, 15);
	$table->class = 'moutable';

   	$table->titlesrows = array(30);
    $table->titles = array();
	$table->titles[] = 'Исходные данные ' . $fldid . ', ' . $fldid2;
    $table->downloadfilename = "selectdata_{$shortname}_{$fldid}_{$fldid2}";
    $table->worksheetname = $fldid . '_' . $fldid2;


	$strsql =  "SELECT l.id as lid, l.rayonid, schoolid, s.name, r.{$fldid}, r.{$fldid2} 
                FROM mdl_monit_rating_listforms l
                INNER JOIN mdl_monit_school s ON s.id=l.schoolid
                INNER JOIN mdl_monit_form_{$shortname} r ON l.id=r.listformid
                where shortname='$shortname' and s.yearid=$yid and s.isclosing=0
                order by l.rayonid, l.schoolid";	

	$color = 'red';
    $i = 1;
	if ($schools = get_records_sql($strsql))	{
		foreach ($schools as $school) {
		    $table->data[] = array ($i++ . '.', $school->rayonid, $school->name , $school->{$fldid}, $school->{$fldid2});
		}    
	}
	
	return $table;
}

?>