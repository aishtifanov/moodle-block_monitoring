<?php // $Id: college.php,v 1.4 2010/06/25 07:58:16 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = optional_param('sid', '0', PARAM_INT);       // School id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
	$action   = optional_param('action', '');


	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('college', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}
	
    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }
	

//    $sid = optional_param('sid', '');
    if ($action == 'excel') {
        print_excel_schools($rid);
        exit();
	}

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
 	   $strcolleges = get_string('colleges', 'block_monitoring');
 	} else {
 	   $strcolleges = get_string('college', 'block_monitoring');
 	}
    $numberf = get_string('symbolnumber', 'block_monitoring');;
    $strname = get_string("name");
    $strheadname = get_string('directorschool', 'block_monitoring');
	$strphone = get_string('telnum','block_monitoring');
 	$straddress = get_string('realaddress','block_monitoring');
	$straction = get_string("action","block_monitoring");

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	}
	$breadcrumbs .= " -> $strcolleges";
    print_header_mou("$SITE->shortname: $strcolleges", $SITE->fullname, $breadcrumbs);

	if ($rid == 0)  {
	   $rayon = get_record('monit_rayon', 'id', 1);
	}
	elseif (!$rayon = get_record('monit_rayon', 'id', $rid)) {
        error(get_string('errorrayon', 'block_monitoring'), '..\rayon\rayons.php');
    }

    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);
    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("college.php?yid=$yid&amp;rid=", $rid);
		echo '</table>';

	    if ($rid == 0) {
		    print_footer();
		    exit();
	    }

		if ($rayon_operator_is && $rayon_operator_is != $rid)  {
			notify(get_string('selectownrayon', 'block_monitoring'));
		    print_footer();
			exit();
		}
    }

	print_heading($strcolleges, "center");

	print_tabs_years($yid, "college.php?rid=$rid&amp;sid=$sid&amp;yid=");

    $table->head  = array ($numberf, $strname, $strheadname, $strphone, $straddress, 'WWW', 'Email', $straction);
    $table->align = array ('center', 'left', 'left', 'left', 'left', 'left', 'left', 'center');
    $table->class = 'moutable';

    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		// $arr_schools =  get_records('monit_school', 'rayonid', $rayon->id, 'isclosing', false);
		$arr_schools =  get_records_sql("SELECT *  FROM {$CFG->prefix}monit_college
					     				WHERE rayonid = {$rayon->id} AND isclosing=0 AND yearid=$yid
					     				ORDER BY number");

		if ($arr_schools) foreach ($arr_schools as $school) {

			if ($admin_is || $region_operator_is || ($rayon_operator_is == $rayon->id)) 	{
				$title = get_string('reports','block_monitoring');
				$strlinkupdate = "<a title=\"$title\" href=\"listforms.php?rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
				$schoolname = $strlinkupdate . "<strong>$school->name</strong></a>&nbsp;";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/report.gif\" alt=\"$title\" /></a>&nbsp;";

                if ($curryearid == $yid || $admin_is)	{
					$title = get_string('editcollege','block_monitoring');
					$strlinkupdate .= "<a title=\"$title\" href=\"addcollege.php?mode=edit&amp;rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				}
			} else {
	       		$linkname = $rayon->name;
	      		$strlinkupdate = '-';
			}

			if ($admin_is || $region_operator_is) 	{

                 if ($curryearid == $yid || $admin_is)	{
					$title = get_string('deletecollege','block_monitoring');
				    $strlinkupdate .= "<a title=\"$title\" href=\"delschool.php?rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";
				 }

			}

			if (!empty($school->headid) && $school->headid != 0)	{
				$head_user = get_record('user', 'id', $school->headid);
				$hname = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$head_user->id.'&amp;course=1">'.fullname($head_user).'</a></strong>';
			} else {
				$hname = $school->fio;
			}

			$phone = $school->phones;
		/*
		    	if ($school->phone2 == 0){
	       			$phone = $school->phone1;
	        	}
	        	else {
	   	    		$phone = $school->phone1 . ",  " . $school->phone2;
	        	}
	   */
	        $wwwwww = "<a target=\"_blank\" href=\"$school->www\">$school->www</a>";
	        $emaill = "<a href=\"mailto:$school->email\">$school->email</a>";
			$table->data[] = array ($school->number, $schoolname, $hname, $phone, $school->realaddress, $wwwwww, $emaill, $strlinkupdate);
		}

	}  else {
		$school =  get_record('monit_college', 'uniqueconstcode', $sid, 'rayonid', $rayon->id, 'yearid', $yid);

		$title = get_string('reports','block_monitoring');
		$strlinkupdate = "<a title=\"$title\" href=\"listforms.php?rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
		$schoolname = $strlinkupdate . "<strong>$school->name</strong></a>&nbsp;";
		$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/report.gif\" alt=\"$title\" /></a>&nbsp;";
        if ($curryearid == $yid || $admin_is)	{
			$title = get_string('editschool','block_monitoring');
			$strlinkupdate .= "<a title=\"$title\" href=\"addcollege.php?mode=edit&amp;rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
		}
		if (!empty($school->headid) && $school->headid != 0)	{
			$head_user = get_record('user', 'id', $school->headid);
			$hname = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$head_user->id.'&amp;course=1">'.fullname($head_user).'</a></strong>';
		} else {
			$hname = $school->fio;
		}
		$phone = $school->phones;
        $wwwwww = "<a target=\"_blank\" href=\"http://$school->www\">$school->www</a>";
        $emaill = "<a href=\"mailto:$school->email\">$school->email</a>";
		$table->data[] = array ($school->number, $schoolname, $hname, $phone, $school->realaddress, $wwwwww, $emaill, $strlinkupdate);
	}
   	print_color_table($table);

	if ($admin_is || $region_operator_is) 	{
		?>	<table align="center">
			<tr>
			<td>
		  <form name="addspec" method="post" action="<?php echo "addcollege.php?mode=new&amp;rid={$rayon->id}&amp;yid=$yid" ?>">
			    <div align="center">
				<input type="submit" name="addcollege" value="<?php print_string('addcollege','block_monitoring')?>">
			    </div>
		  </form>
		  </td>
			<td>
			<form name="download" method="post" action="<?php echo "college.php?action=excel&amp;rid={$rayon->id}&amp;yid=$yid" ?>">
			    <div align="center">
				<input type="submit" name="downloadexcel" value="<?php print_string("downloadexcel")?>">
			    </div>
		  </form>
			</td>
			</tr>
		  </table>
		<?php
    }


    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		if ($arr_schools = get_records_sql("SELECT *  FROM {$CFG->prefix}monit_college
					     				WHERE rayonid = {$rayon->id} AND isclosing=1 AND yearid=$yid"))		{

			echo '<p><hr>';
			print_heading(get_string('closedschools','block_monitoring'), 'center', 3);

            // print_r($arr_schools);
            unset($table);
            $table->head  = array ($numberf, $strname, $strheadname, $strphone, $straddress, 'WWW', 'Email', $straction);
		    $table->align = array ('center', 'left', 'left', 'left', 'left', 'left', 'left', 'center');
		    $table->class = 'moutable';


			foreach ($arr_schools as $school) {

				if ($admin_is || $region_operator_is || ($rayon_operator_is == $rayon->id)) 	{
					$title = get_string('reports','block_monitoring');
					$strlinkupdate = "<a title=\"$title\" href=\"listforms.php?rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
					$schoolname = $strlinkupdate . "<strong>$school->name</strong></a>&nbsp;";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/report.gif\" alt=\"$title\" /></a>&nbsp;";
	/*
					$title = get_string('editschool','block_monitoring');
					$strlinkupdate .= "<a title=\"$title\" href=\"addschool.php?mode=edit&amp;rid={$rayon->id}&amp;sid={$school->id}\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
	*/
				} else {
		       		$linkname = $rayon->name;
		      		$strlinkupdate = '-';
				}

				if ($admin_is || $region_operator_is) 	{
	                if ($curryearid == $yid || $admin_is)	{
						$title = get_string('deleteschool','block_monitoring');
					    $strlinkupdate .= "<a title=\"$title\" href=\"delschool.php?rid={$rayon->id}&amp;sid={$school->id}&amp;yid=$yid\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";
					}
				}

				if (!empty($school->headid) && $school->headid != 0)	{
					$head_user = get_record('user', 'id', $school->headid);
					$hname = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$head_user->id.'&amp;course=1">'.fullname($head_user).'</a></strong>';
				} else {
					$hname = $school->fio;
				}

				$phone = $school->phones;
			/*
			    	if ($school->phone2 == 0){
		       			$phone = $school->phone1;
		        	}
		        	else {
		   	    		$phone = $school->phone1 . ",  " . $school->phone2;
		        	}
		   */
		        $wwwwww = "<a target=\"_blank\" href=\"$school->www\">$school->www</a>";
		        $emaill = "<a href=\"mailto:$school->email\">$school->email</a>";
				$table->data[] = array ($school->number, $schoolname, $hname, $phone, $school->realaddress, $wwwwww, $emaill, $strlinkupdate);
			}
		   	print_color_table($table);
		}

	}
    print_footer();


function print_excel_schools($rid)
{
    global $CFG;

    if ($rid == 0) return false;

    require_once("$CFG->libdir/excel/Worksheet.php");
    require_once("$CFG->libdir/excel/Workbook.php");

	// HTTP headers
    header("Content-type: application/vnd.ms-excel");
    $downloadfilename = 'schools_'.$rid;
    header("Content-Disposition: attachment; filename=\"$downloadfilename.xls\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    // Creating a workbook
    $workbook = new Workbook("-");
    $myxls =& $workbook->add_worksheet($downloadfilename);

	// Print names of all the fields
	$formath1 =& $workbook->add_format();
	$formath2 =& $workbook->add_format();
	$formatp =& $workbook->add_format();

	$formath1->set_size(12);
	$formath1->set_align('center');
	$formath1->set_align('vcenter');
	$formath1->set_color('black');
	$formath1->set_bold(1);
	$formath1->set_italic();
	// $formath1->set_border(2);

	$formath2->set_size(11);
    $formath2->set_align('center');
    $formath2->set_align('vcenter');
	$formath2->set_color('black');
	$formath2->set_bold(1);
	//$formath2->set_italic();
	$formath2->set_border(2);
	$formath2->set_text_wrap();

	$formatp->set_size(11);
    $formatp->set_align('left');
    $formatp->set_align('vcenter');
	$formatp->set_color('black');
	$formatp->set_bold(0);
	$formatp->set_border(1);
	$formatp->set_text_wrap();

    $numberf = get_string('symbolnumber', 'block_monitoring');;
    $strname = get_string("name");
    $strheadname = get_string('directorschool', 'block_monitoring');
	$strphone = get_string('telnum','block_monitoring');
 	$straddress = get_string('address','block_monitoring');

    $exceltable->head  = array ($numberf, $strname, $strheadname, $strphone, $straddress, 'WWW', 'Email');
    $exceltable->column = array (4, 30, 20, 15, 30, 30, 20);
 	$exceltable->fields = array('number', 'name', 'fio', 'phones', 'address', 'www', 'email');

	// $countcols = count($table->head);
    $i = $j = 0;
    // for ($cnt=0; $cnt<$countcols; $cnt++)    {
    foreach ($exceltable->column as $key => $width) {
		$myxls->set_column($i++, $j++, $width);
	}

	$rayon = get_record('monit_rayon', 'id', $rid);

    $txtl = new textlib();

	$myxls->set_row(0, 30);
	$strwin1251 =  $txtl->convert($rayon->name, 'utf-8', 'windows-1251');
    $myxls->write_string(0, 0, $strwin1251, $formath1);

	if ($schools =  get_records('monit_college', 'rayonid', $rayon->id, 'number'))	{

		$countcols = count($exceltable->head);
		$countrows = count($schools);
	    for ($i=1; $i<$countrows; $i++)	{
	       for ($j=0; $j<$countcols; $j++)	{
				$myxls->write_blank($i,$j,$formatp);
	 		}
	    }
		$myxls->merge_cells(0, 0, 0, $countcols-1);

		$i = 1;	$j = 0;
	    foreach ($exceltable->head as $key => $heading) {
			$strwin1251 =  $txtl->convert($heading, 'utf-8', 'windows-1251');
	        $myxls->write_string($i, $j++, $strwin1251, $formath2);
	    }

		$i = 2;
		foreach ($schools as $school) {
  	        $arrschool = (array)$school;
 	        for ($j=0; $j<$countcols; $j++)	 {
        		$strwin1251 =  $txtl->convert($arrschool[$exceltable->fields[$j]], 'utf-8', 'windows-1251');
    	       	$myxls->write($i, $j, $strwin1251, $formatp);
 		    }
	  	    $i++;
	  	    unset($arrschool);
		}
   		$strwin1251 =  $txtl->convert(get_string('vsego','block_monitoring'), 'utf-8', 'windows-1251');
   	    $myxls->write_string($i, 2, $strwin1251, $formath1);
  		$myxls->write_formula($i, 3, "=COUNTA(A3:A$i)", $formath1);
    }

    $workbook->close();

	return true;
}

?>


