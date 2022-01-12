<?php // $Id: rayons.php,v 1.15 2010/08/25 09:19:40 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../../course/lib.php');
    require_once('../lib.php');

  	$rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
	$action   = optional_param('action', '');


    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($action == 'excel') {
        print_excel_rayons();
        exit();
	}

    $strrayons = get_string('rayons', 'block_monitoring');
    $numberf = get_string('symbolnumber', 'block_monitoring');
    $strname = get_string('name');
    $strheadname = get_string('headrayonname', 'block_monitoring');
	$strphone = get_string('telnum','block_monitoring');
 	$straddress = get_string('address','block_monitoring');
	$straction = get_string('action','block_monitoring');

	// $creator_is = iscreator();

    // add_to_log(SITEID, 'dean', 'faculty view', 'faculty.php', $strfaculty);

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strrayons";
    print_header_mou("$site->shortname: $strrayons", $site->fullname, $breadcrumbs);

	if (!$admin_is && !$region_operator_is && $rayon_operator_is) {
		print_heading(get_string('rayon', 'block_monitoring'), "center");
	} else {
		print_heading($strrayons, "center");
	}

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }

    $table->head  = array ($numberf, $strname, $strheadname, $strphone, $straddress, 'WWW', 'Email', $straction);
    $table->align = array ('center', 'left', 'left', 'left', 'left', 'left', 'left', 'center');
    // $table->wrap = array ('', 'nowrap');
   	// $table->width = '95%';
    // $table->size = array ('5%', '10%', '10%', '10%', '10%', '10%', '10%', '5%');
    $table->class = 'moutable';


	$allrayons = get_records_sql("SELECT * FROM {$CFG->prefix}monit_rayon ORDER BY number");
	if ($allrayons)	 {

		foreach ($allrayons as $rayon) 	{

			if ($admin_is || $region_operator_is || ($rayon_operator_is == $rayon->id)) 	{
		       	$linkname = "<strong><a href=$CFG->wwwroot/blocks/monitoring/school/schools.php?rid={$rayon->id}&amp;yid=$yid>{$rayon->name}</a></strong>";

				$title = get_string('reports','block_monitoring');
				$strlinkupdate = "<a title=\"$title\" href=\"listrayonforms.php?rid={$rayon->id}&amp;yid=$yid\">";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/report.gif\" alt=\"$title\" /></a>&nbsp;";

				$title = get_string('editrayon','block_monitoring');
				$strlinkupdate .= "<a title=\"$title\" href=\"addrayon.php?mode=edit&amp;rid={$rayon->id}\">";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";

       		} else {
	       		$linkname = $rayon->name;
	       		$strlinkupdate = '-';
       		}

			if ($admin_is || $region_operator_is) 	{
        		if ($context = get_record('context', 'contextlevel', CONTEXT_RAYON, 'instanceid', $rayon->id)) {
					$title = get_string('assignroles','role');
				    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_school/roles/assign.php?contextid={$context->id}\">";
					$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/roles.gif\" alt=\"$title\" /></a>&nbsp;";
				}	

				$title = get_string('deleterayon','block_monitoring');
			    $strlinkupdate .= "<a title=\"$title\" href=\"delrayon.php?rid={$rayon->id}\">";
				$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";

			}

			if (!empty($rayon->headid) && $rayon->headid != 0)	{
				$head_user = get_record('user', 'id', $rayon->headid);
				$hname = '<strong><a href="'.$CFG->wwwroot.'/user/view.php?id='.$head_user->id.'&amp;course=1">'.fullname($head_user).'</a></strong>';
			} else {
				$hname = $rayon->fio;
			}

			$phone = $rayon->phones;

  			if ($admin_is || $region_operator_is || ($rayon_operator_is == $rayon->id)) 	{
		        $wwwwww = "<a target=\"_blank\" href=\"$rayon->www\">$rayon->www</a>";
		        $emaill = "<a href=\"mailto:$rayon->email\">$rayon->email</a>";
				$table->data[] = array ($rayon->number, $linkname, $hname, $phone, $rayon->address, $wwwwww, $emaill, $strlinkupdate);
			}
		}
    }
    print_color_table($table);

	if ($admin_is || $region_operator_is) 	{
		?><table align="center">
			<tr>
			<td>
		  <form name="addfac" method="post" action="addrayon.php?mode=new">
			    <div align="center">
				<input type="submit" name="addrayon" value="<?php print_string('addrayon','block_monitoring')?>">
				 </div>
		  </form>
		  	</td>
			<td>
			<form name="download" method="post" action="rayons.php?action=excel">
			    <div align="center">
				<input type="submit" name="downloadexcel" value="<?php print_string("downloadexcel")?>">
			    </div>
		  </form>
			</td>
			</tr>
		  </table>
		<?php
	}
    print_footer();


function print_excel_rayons()
{
    global $CFG;


    require_once("$CFG->libdir/excel/Worksheet.php");
    require_once("$CFG->libdir/excel/Workbook.php");

	// HTTP headers
    header("Content-type: application/vnd.ms-excel");
    $downloadfilename = 'rayons';
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

    $strrayons = get_string('rayons', 'block_monitoring');
    $numberf = get_string('symbolnumber', 'block_monitoring');
    $strname = get_string('name');
    $strheadname = get_string('headrayonname', 'block_monitoring');
	$strphone = get_string('telnum','block_monitoring');
 	$straddress = get_string('address','block_monitoring');

    $exceltable->head  = array ($numberf, $strname, strip_tags($strheadname), $strphone, $straddress);
    $exceltable->column = array (4, 30, 20, 15, 30);
 	$exceltable->fields = array('number', 'name', 'fio', 'phones', 'address');


    $i = $j = 0;
    foreach ($exceltable->column as $key => $width) {
		$myxls->set_column($i++, $j++, $width);
	}

    $txtl = new textlib();

	$myxls->set_row(0, 30);
	$strwin1251 =  $txtl->convert($strrayons, 'utf-8', 'windows-1251');
    $myxls->write_string(0, 0, $strwin1251, $formath1);

	$allrayons = get_records_sql("SELECT * FROM {$CFG->prefix}monit_rayon ORDER BY number");
	if ($allrayons)	 {

		$countcols = count($exceltable->head);
		$countrows = count($allrayons);
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
		foreach ($allrayons as $rayon) {
  	        $arrrayon = (array)$rayon;
 	        for ($j=0; $j<$countcols; $j++)	 {
        		$strwin1251 =  $txtl->convert($arrrayon[$exceltable->fields[$j]], 'utf-8', 'windows-1251');
    	       	$myxls->write($i, $j, $strwin1251, $formatp);
 		    }
	  	    $i++;
	  	    unset($arrrayon);
		}
   		$strwin1251 =  $txtl->convert(get_string('vsego','block_monitoring'), 'utf-8', 'windows-1251');
   	    $myxls->write_string($i, 2, $strwin1251, $formath1);
  		$myxls->write_formula($i, 3, "=COUNTA(A3:A$i)", $formath1);
    }

    $workbook->close();

	return true;
}

?>

