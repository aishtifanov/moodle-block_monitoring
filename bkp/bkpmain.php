<?php // $Id: bkpmain.php,v 1.16 2008/09/12 11:22:55 Shtifanov Exp $
    require_once("../../../config.php");
    require_once('../lib.php');
 	require_once($CFG->libdir.'/uploadlib.php'); 
	require_once($CFG->libdir.'/filelib.php');
    
    require_once('bkplib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);          // School id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $currenttab = optional_param('id', '1', PARAM_INT);       // Tab id;
    $fid = optional_param('fid', '0', PARAM_INT);       // Form id
  	$levelmonit = optional_param('level', 'region');       // Level
  	$action = optional_param('action', '');       // action
	$nm = optional_param('nm', 1);         // Month

	$frm = data_submitted(); /// load up any submitted data
	if($frm) {
		$sql = get_record_sql("select id, filename from {$CFG->prefix}monit_form_file where (rayonid=$rid)and(schoolid=$sid)and(formid=$fid)and(yearid=$yid)");
		if($sql) {
			$data->id = $sql->id;
		}

		$data->rayonid = $rid;
		$data->schoolid = $sid;
		$data->formid = $fid;
		$data->yearid = $yid;		
		
		$um = new upload_manager('filename',false,false,null,false,0);
		if (!empty($_FILES['filename']['name']))	{		
			if ($um->preprocess_files()) {
				$tmp_filename = $um->files['filename']['tmp_name'];
				$filename = $um->files['filename']['name'];
				$dir = "0/bkp/$yid/$fid/$sid/";
				$um->process_file_uploads($dir);
				$data->filename = $filename;
			}
		}

		if(($sql->id != 0)&&(trim($data->filename) != '')) {
			$dir = $CFG->dataroot.'/'.$dir.$sql->filename;
			unlink($dir);
			update_record('monit_form_file', $data);
		} else {
			insert_record('monit_form_file', $data);
		}
		redirect("bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$fid&amp;id=$currenttab", get_string('financeformupdateded','block_monitoring'));				
	}
	
	$datemodified = get_date_from_month_year($nm, $yid);

    if ($action == 'word') {
		form_download($fid, $sid, $rid, $levelmonit);
        exit();
	}

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}
    $school = get_record('monit_school', 'id', $sid);
    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');
    $strschools = get_string('schools', 'block_monitoring');
    $strreports = get_string('reportschool', 'block_monitoring');
    $yeareport = get_string('yeareport', 'block_monitoring');

    $strstatus = get_string('status', 'block_monitoring');
	$strform = get_string('form','block_monitoring');
	$strrazdel = get_string('razdel','block_monitoring');
 	$strperiod = get_string('period','block_monitoring');
	$straction = get_string('action','block_monitoring');
    $strlevel = get_string($levelmonit, 'block_monitoring');
	$downloadword = get_string('downloadword', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";

//	add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

	switch ($levelmonit)	{
		case 'region':
			if ($admin_is || $region_operator_is) 	{
			    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
				$breadcrumbs .= " -> $strlevel";
			    print_header_mou("$site->shortname: $strlevel", $site->fullname, $breadcrumbs);
			}
		break;
		case 'rayon':
			if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
				$rayon = get_record('monit_rayon', 'id', $rid);
				$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/bkp/bkpmain.php?level=rayon&rid=$rid&sid=0&nm=$nm&yid=$yid&id=$currenttab\">$rayon->name</a>";
				$breadcrumbs .= " -> $yeareport";
				print_header_mou("$site->shortname: $strschool", $site->fullname, $breadcrumbs);
				if ($rid == 0) {
				    print_footer();
				 	exit();
				}
				echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox"><tr><td>';
				listbox_rayons("bkpmain.php?level=$levelmonit&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;rid=", $rid);
				echo '</td></tr></table>';

				if ($rid == 0) {
				    print_footer();
				 	exit();
				}
				if ($rayon_operator_is && $rayon_operator_is != $rid)  {
					notify(get_string('selectownrayon', 'block_monitoring'));
				    print_footer();
					exit();
				}
				print_tabs_years($yid, "bkpmain.php?level=$levelmonit&amp;sid=$sid&amp;nm=$nm&amp;rid=$rid&amp;yid=");
			}
  		break;

		case 'school':
			$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/school/schools.php?rid=$rid&amp;yid=$yid\">$strschool</a>";
			$breadcrumbs .= " -> $school->name";
			print_header_mou("$site->shortname: $strschool", $site->fullname, $breadcrumbs);

			$school = get_record('monit_school', 'id', $sid);

			if ($admin_is || $region_operator_is || $rayon_operator_is) 	{
				echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox"><tr><td>';
				listbox_rayons("bkpmain.php?level=$levelmonit&amp;sid=0&amp;nm=$nm&amp;yid=$yid&amp;id=$currenttab&amp;rid=", $rid);
				listbox_schools("bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;nm=$nm&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
				echo '</td></tr></table>';
			}
			if ($rid == 0 ||  $sid == 0) {
				print_footer();
				exit();
			}

//	    	$eduyear = get_record('monit_years', 'id', $yid);
//		    $str1 = $strreports.': '.$school->name . get_string('zauchyear', 'block_monitoring', $eduyear->name);
			print_heading($str1, "center", 3);

		break;
	}

    print_tabs_typeforms($levelmonit, 'yeareport', $nm, $yid, $rid, $sid);

    include('tabsreportbkp.php');

	echo "<form name='form_bkp' enctype='multipart/form-data' method='post' action='bkpmain.php'><center>";
	$bkpstatus = get_record_sql("select * from {$CFG->prefix}monit_form_file where (rayonid=$rid)and(schoolid=$sid)and(formid=$fid)and(yearid=$yid)");
	if($bkpstatus) {
		echo '<br>'.get_string('loadscan', 'block_mou_nsop').' ';
		$file = $bkpstatus->filename;
		$filearea = "0/bkp/$yid/$fid/$sid";
		$icon = mimeinfo('icon', $file);
		if ($CFG->slasharguments) {
			$ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
		} else {
			$ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
		}
		$output = '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" class="icon" alt="'.$icon.'" />'.
				'<a href="'.$ffurl.'" >'.$file.'</a><br />';
		echo $output;
	}

	$file = 0;


	if ($school_operator_is || $rayon_operator_is) {
			if(($sendsoglas == 1)) {
				if(($bkpstatus->status != 5) && ($bkpstatus->status != 6)) {		
					echo '<center><input type="file" name="filename" size="50"></center>'.
						"<center><input type='submit' name='savebkp' value='".get_string('savechanges')."'></center>";
					$file = 1;
				}
				echo '<form name="statuses" method="post" action="changestatus.php">
					<table border="0" cellspacing="2" cellpadding="5" align="center">
						<tr>';
				$status = get_record('monit_status', 'id', 2);
				echo '		<td align="center" bgcolor="'.$status->color.'" >
								<input type="submit" name=status4 value="'.get_string('sendtocoordination', 'block_monitoring').'" />
							</td></tr></table>';
			}
	}

    if ($region_operator_is)  {
		echo '<center><input type="file" name="filename" size="50"></center>'.
			"<center><input type='submit' name='savebkp' value='".get_string('savechanges')."'></center>";
			$file = 1;			
    	
    	if($bkpstatus->status == 4) {
			echo '<form name="statuses" method="post" action="changestatus.php">
				<table border="0" cellspacing="2" cellpadding="5" align="center">
					<tr>';

			$status = get_record('monit_status', 'id', 6);
			echo '<td align="center" bgcolor="'.$status->color.'" >
					<input type="submit" name=status6 value="'.$status->name.'" />
				</td>';
			$status = get_record('monit_status', 'id', 3);
			echo '<td align="center" bgcolor="'.$status->color.'" >
					<input type="submit" name=status3 value="'.$status->name.'" />
				</td>';
			$status = get_record('monit_status', 'id', 5);
			echo '<td align="center" bgcolor="'.$status->color.'" >
					<input type="submit" name=status5 value="'.$status->name.'" />
				</td></tr></table>';
		} else {

		}
	}

    if ($admin_is)  {
		if($file == 0) echo '<center><input type="file" name="filename" size="50"></center>'.
			"<center><input type='submit' name='savebkp' value='".get_string('savechanges')."'></center>";
    	
		echo '<form name="statuses" method="post" action="changestatus.php">
			<table border="0" cellspacing="2" cellpadding="5" align="center">
			<tr>';

		$status = get_record('monit_status', 'id', 6);
		echo '<td align="center" bgcolor="'.$status->color.'" >
				<input type="submit" name=status6 value="'.$status->name.'" />
			</td>';
		$status = get_record('monit_status', 'id', 3);
		echo '<td align="center" bgcolor="'.$status->color.'" >
				<input type="submit" name=status3 value="'.$status->name.'" />
			</td>';
		$status = get_record('monit_status', 'id', 5);
		echo '<td align="center" bgcolor="'.$status->color.'" >
				<input type="submit" name=status5 value="'.$status->name.'" />
			</td></tr></table>';
	}
	echo '<input type="hidden" name="level" value="'.$levelmonit.'" />
		<input type="hidden" name="fid" value="'.$fid.'" />
		<input type="hidden" name="rid" value="'.$rid.'" />
		<input type="hidden" name="sid" value="'.$sid.'" />
		<input type="hidden" name="yid" value="'.$yid.'" />
		<input type="hidden" name="id" value="'.$currenttab.'" />
		<input type="hidden" name="nm" value="'.$nm.'" />
		</form>';
	
    print_simple_box_end("center");
    print_footer();

?>