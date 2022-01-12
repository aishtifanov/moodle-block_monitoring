<?PHP // $Id: addcollege.php,v 1.4 2009/02/25 08:23:49 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../../lib/validateurlsyntax.php');
    require_once('../lib.php');

    $mode = required_param('mode', PARAM_ALPHA);    // new, add, edit, update
    $rid = required_param('rid', PARAM_INT);          // rayon id
    $yid 	 = required_param('yid', PARAM_INT);       		// Year id
	$sid = optional_param('sid', 0, PARAM_INT);			// school id
	$currtab = optional_param('tab', '1', PARAM_INT);	// # tab's


	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('college', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    // $school = get_record('monit_college', 'id', $sid);

    if ($mode === "new" || $mode === "add" )	{
         $straddschool = get_string('addcollege','block_monitoring');
    }
	else 	{
	     $straddschool = get_string('updatecollege','block_monitoring');
	 }

    $strrayon = get_string('rayon', 'block_monitoring');
    $strrayons = get_string('rayons', 'block_monitoring');
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
 	   $strschools = get_string('colleges', 'block_monitoring');
 	} else {
 	   $strschools = get_string('college', 'block_monitoring');
 	}

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
    if ($admin_is  || $region_operator_is || $rayon_operator_is)  {
		$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	}
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/college/college.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strschools</a>";
	$breadcrumbs .= " -> $straddschool";
    print_header_mou("$site->shortname: $straddschool", $site->fullname, $breadcrumbs);


    if (!$rayon = get_record('monit_rayon', 'id', $rid)) {
	    error(get_string('errorrayon', 'block_monitoring'), '..\rayon\rayon.php');
	}

	$rec->rayonid = $rid;

   if ($mode == 'new') {	   	$inactive = array('schooltab2', 'schooltab3', 'schooltab4', 'schooltab5');   } else {   		$inactive = NULL;
   }
   $activetwo = NULL;

   $toprow = array();
   for ($i=1; $i<=5; $i++)	{
	   $toprow[] = new tabobject('schooltab'.$i, $CFG->wwwroot."/blocks/monitoring/college/addcollege.php?mode=$mode&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;tab=$i",
 	               get_string('schooltab'.$i, 'block_monitoring'));
   }
   $tabs = array($toprow);
   print_tabs($tabs,'schooltab'.$currtab, $inactive, $activetwo);

    switch ($mode)	{    	case 'new':			    	if ($allschools = get_records('monit_college', 'rayonid', $rid)) {			    		$rec->number = count($allschools)+1;			    	}
			    	$mode = 'add';    	break;
	 	case 'add': if ($admin_is || $region_operator_is) {
						$rec->number = required_param('number');
						$rec->name = required_param('name');
						$rec->fio = required_param('fio');
						$rec->appointment = required_param('appointment');
						$rec->typeinstitution = required_param('typeinstitution');
						$rec->stateinstitution = required_param('stateinstitution');
						$rec->numsession = required_param('numsession');

						if (find_form_school_errors($rec, $err, $currtab) == 0) {
							$rec->timemodified = time();
                            $rec->yearid = $yid;
							if ($idnew = insert_record('monit_college', $rec))	{								 $rec->id = $idnew;
								 $rec->uniqueconstcode = $idnew;
								 if (update_record('monit_college', $rec))	{
									 echo '<div align=center>';
									 notice(get_string('collegeadded','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/college/addcollege.php?mode=edit&amp;rid=$rid&amp;sid=$idnew&amp;yid=$yid&amp;tab=2");
								   	 echo '</div>';
	 							 } else  {
							  		error(get_string('errorinaddingcollege','block_monitoring') . '(insert)', "$CFG->wwwroot/blocks/monitoring/college/addcollege.php?rid=$rid&amp;yid=$yid");
							  	 }
							} else  {
						  		error(get_string('errorinaddingcollege','block_monitoring') . '(update)', "$CFG->wwwroot/blocks/monitoring/college/addcollege.php?rid=$rid&amp;yid=$yid");
						  	}

						}
						else $mode = "new";
					}
		break;
		case 'edit':
					if ($sid > 0) 	{
					    $rec = get_record('monit_college', 'id', $sid);
					}
			    	$mode = 'update';
		break;
		case 'update':
					if ($sid > 0) 	{
					    $rec = get_record('monit_college', 'id', $sid);
					    // print_r($rec);  echo '<hr>';
						if ($frm = data_submitted())  {						    // print_r($frm); echo '<hr>';
						    $arrec = (array)$rec;
						    foreach ($frm as $key => $value)	{						    	// if (isset($arrec[$key])) {						    	if (!empty($value)) {						    		$arrec[$key] = $value;
						    	}						    }
						    $rec = (object)$arrec;
						    // print_r($rec); echo '<hr>';
						    if ($currtab == 2)	{							      if (isset($frm->s_l_day) && $frm->s_l_day!=0 &&  $frm->s_l_month !=0 && $frm->s_l_year !=0)  {
									 $rec->startdatelicense = get_timestamp_from_date($frm->s_l_day, $frm->s_l_month, $frm->s_l_year);
								  }
							      if (isset($frm->e_l_day) && $frm->e_l_day!=0 &&  $frm->e_l_month !=0 && $frm->e_l_year !=0)  {
									 $rec->enddatelicense = get_timestamp_from_date($frm->e_l_day, $frm->e_l_month, $frm->e_l_year);
								  }
							      if (isset($frm->s_e_day) && $frm->s_e_day!=0 &&  $frm->s_e_month !=0 && $frm->s_e_year !=0)  {
									 $rec->startdatelicensextra = get_timestamp_from_date($frm->s_e_day, $frm->s_e_month, $frm->s_e_year);
								  }
							      if (isset($frm->e_e_day) && $frm->e_e_day!=0 &&  $frm->e_e_month !=0 && $frm->e_e_year !=0)  {
									 $rec->enddatelicenseextra = get_timestamp_from_date($frm->e_e_day, $frm->e_e_month, $frm->e_e_year);
								  }
							      if (isset($frm->s_s_day) && $frm->s_s_day!=0 &&  $frm->s_s_month !=0 && $frm->s_s_year !=0)  {
									 $rec->startdatecertificate = get_timestamp_from_date($frm->s_s_day, $frm->s_s_month, $frm->s_s_year);
								  }
							      if (isset($frm->e_s_day) && $frm->e_s_day!=0 &&  $frm->e_s_month !=0 && $frm->e_s_year !=0)  {
									 $rec->enddatecertificate = get_timestamp_from_date($frm->e_s_day, $frm->e_s_month, $frm->e_s_year);
								  }
						    }

						    // print_r($rec);  echo '<hr>';
							if (find_form_school_errors($rec, $err, $currtab, $mode) == 0) {
								$rec->timemodified = time();
								if (update_record('monit_college', $rec))	{
									 // add_to_log(1, 'dean', 'speciality update', "blocks/dean/speciality/speciality.php?id=$fid", $USER->lastname.' '.$USER->firstname);
									 echo '<div align=center>';
									 notice(get_string('collegeupdate','block_monitoring') . '</center>', "$CFG->wwwroot/blocks/monitoring/college/addcollege.php?mode=edit&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;tab=$currtab");
									 echo '</div>';
								} else
									error(get_string('errorinupdatingcollege','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/college/college.php?rid=$rid&amp;yid=$yid");
							}

						}
					}
		break;
	}

	$strtitle = '';
	if (isset($rec->name))  {
		$strtitle .= $rec->name . ', ';
	}	$strtitle .= $rayon->name;

	print_heading($strtitle, "center", 4);

    switch ($currtab)	{
    	case 1:   // print_simple_box_start("center", 70);
		 	     print_simple_box_start('center', '', 'white');
			     echo '<div align=right><font color="red"><small><b>'.get_string('attentionschoolform', 'block_monitoring').'</b></small></font></div>';
?>

<form name="addform" method="post" action="addcollege.php">
<input type="hidden" name="mode" value="<?php p($mode)?>">
<input type="hidden" name="rid" value="<?php p($rid)?>">
<input type="hidden" name="sid" value="<?php p($sid)?>">
<input type="hidden" name="yid" value="<?php p($yid)?>">
<input type="hidden" name="tab" value="<?php p($currtab)?>">
<input type="hidden" name="typeinstitution" value="0">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string('numbercollege', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" id="number" name="number" size="3" maxlength="3" value="<?php if (isset($rec->number)) p($rec->number) ?>" />
		<?php if (isset($err["number"])) formerr($err["number"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('nameschool', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" id="name" name="name" size="70" maxlength="255" value="<?php if (isset($rec->name))  p($rec->name) ?>" />
		<?php if (isset($err["name"])) formerr($err["name"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('directorschool', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" id="fio" name="fio" size="70" maxlength="100" value="<?php if (isset($rec->fio))  p($rec->fio) ?>" />
		<?php if (isset($err["fio"])) formerr($err["fio"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('appointmenthead', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="appointment" size="70" maxlength="100" value="<?php if (isset($rec->appointment))  p($rec->appointment) ?>" />
		<?php if (isset($err["appointment"])) formerr($err["appointment"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('isdirectormanager', 'block_monitoring') ?>:</b></td>
    <td align="left">
	    <select size="1" name="isdirectormanager">
	 	   <option <?php if (isset($rec->isdirectormanager) && $rec->isdirectormanager == 0) echo 'selected' ?> value="0">-</option>
		   <option <?php if (isset($rec->isdirectormanager) && $rec->isdirectormanager == 1) echo 'selected' ?> value="1"><?php print_string('yes'); ?></option>
		   <option <?php if (isset($rec->isdirectormanager) && $rec->isdirectormanager == -1) echo 'selected' ?> value="-1"><?php print_string('no'); ?></option>
		</select>
    </td>
</tr>

<tr valign="top">
    <td align="right"><b><?php  print_string('stateinstitution', 'block_monitoring') ?>:</b></td>
    <td align="left"> <?php    unset($choices);
				    $choices[0] = '-';
				    $records = get_records('monit_school_category');
				    if ($records)	{				    	foreach  ($records as $type)	{				    		$choices[$type->id] = $type->name;
				    	}
	                    unset($records);
				    }

				    /*
				    for ($i=1; $i<=14; $i++)	{
					    $choices[$i] = get_string('stateinstitution'.$i, 'block_monitoring');
					}
					*/

				    if (isset($rec->stateinstitution))  $selected = $rec->stateinstitution;
				    else $selected = 0;
				    choose_from_menu ($choices, 'stateinstitution', $selected, '');
				    if (isset($err['stateinstitution'])) formerr($err['stateinstitution']); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('numsession', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="numsession" size="1" maxlength="1" value="<?php if (isset($rec->numsession))  p($rec->numsession) ?>" />
		<?php if (isset($err["numsession"])) formerr($err["numsession"]); ?>
    </td>
</tr>
</table>
<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
 	 <input type="submit" name="addschool" value="<?php print_string('savechanges')?>">
  </div>
<?php  } ?>
</center>
</form>


<?php
	    print_simple_box_end();
        // print_box_end();
		print_footer();
    break;
    case 2: // print_box_start();
	 	     print_simple_box_start('center', '80%', 'white');
?>
<form name="addform" method="post" action="addcollege.php">
<input type="hidden" name="mode" value="<?php p($mode)?>">
<input type="hidden" name="rid" value="<?php p($rid)?>">
<input type="hidden" name="sid" value="<?php p($sid)?>">
<input type="hidden" name="yid" value="<?php p($yid)?>">
<input type="hidden" name="tab" value="<?php p($currtab)?>">
<center>
<table cellpadding="5">
<tr valign="top">
    <td COLSPAN=2 align="center"><b><i><?php  print_string('licensedu', 'block_monitoring') ?><i></b></td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('numlicense', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="numlicense" size="20" maxlength="20" value="<?php if (isset($rec->numlicense)) p($rec->numlicense) ?>" />
		<?php if (isset($err["numlicense"])) formerr($err["numlicense"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('regnumlicense', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="regnumlicense" size="20" maxlength="20" value="<?php if (isset($rec->regnumlicense))  p($rec->regnumlicense) ?>" />
		<?php if (isset($err["regnumlicense"])) formerr($err["regnumlicense"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('startdatelicense', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
//      if (isset($rec->f1_5_day) && $rec->f1_5_day!=0 &&  $rec->f1_5_month !=0 && $rec->f1_5_year !=0)  {
//		 $rec->f1_5g1 = get_timestamp_from_date($rec->f1_5_day, $rec->f1_5_month, $rec->f1_5_year);
//      }
      if (isset($rec->startdatelicense))  {
          $startdatelicense = $rec->startdatelicense;
	  }
	  else {
		  $startdatelicense = 0;
	  }
      print_date_monitoring('s_l_day', 's_l_month', 's_l_year', $startdatelicense);
    ?>

    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('enddatelicense', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
      if (isset($rec->enddatelicense))  {
          $enddatelicense = $rec->enddatelicense;
	  }
	  else {
		  $enddatelicense = 0;
	  }
      print_date_monitoring('e_l_day', 'e_l_month', 'e_l_year', $enddatelicense);
    ?>
    </td>
</tr>


<tr valign="top">
    <td COLSPAN=2 align="center"><hr></td>
</tr>
<tr valign="top">
    <td COLSPAN=2 align="center"><b><i><?php  print_string('licensextra', 'block_monitoring') ?><i></b></td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('numlicensextra', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="numlicensextra" size="20" maxlength="20" value="<?php if (isset($rec->numlicensextra)) p($rec->numlicensextra) ?>" />
		<?php if (isset($err["numlicensextra"])) formerr($err["numlicensextra"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('regnumlicensextra', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="regnumlicensextra" size="20" maxlength="20" value="<?php if (isset($rec->regnumlicensextra))  p($rec->regnumlicensextra) ?>" />
		<?php if (isset($err["regnumlicensextra"])) formerr($err["regnumlicensextra"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('startdatelicensextra', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
//      if (isset($rec->f1_5_day) && $rec->f1_5_day!=0 &&  $rec->f1_5_month !=0 && $rec->f1_5_year !=0)  {
//		 $rec->f1_5g1 = get_timestamp_from_date($rec->f1_5_day, $rec->f1_5_month, $rec->f1_5_year);
//      }
      if (isset($rec->startdatelicensextra))  {
          $startdatelicensextra = $rec->startdatelicensextra;
	  }
	  else {
		  $startdatelicensextra = 0;
	  }
      print_date_monitoring('s_e_day', 's_e_month', 's_e_year', $startdatelicensextra);
    ?>

    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('enddatelicenseextra', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
      if (isset($rec->enddatelicenseextra))  {
          $enddatelicenseextra = $rec->enddatelicenseextra;
	  }
	  else {
		  $enddatelicenseextra = 0;
	  }
      print_date_monitoring('e_e_day', 'e_e_month', 'e_e_year', $enddatelicenseextra);
    ?>
    </td>
</tr>

<tr valign="top">
    <td COLSPAN=2 align="center"><hr></td>
</tr>
<tr valign="top">
    <td COLSPAN=2 align="center"><b><i><?php  print_string('accreditcertificate', 'block_monitoring') ?><i></b></td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('numcertificate', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="numcertificate" size="20" maxlength="20" value="<?php if (isset($rec->numcertificate)) p($rec->numcertificate) ?>" />
		<?php if (isset($err["numcertificate"])) formerr($err["numcertificate"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('regnumcertificate', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="regnumcertificate" size="20" maxlength="20" value="<?php if (isset($rec->regnumcertificate))  p($rec->regnumcertificate) ?>" />
		<?php if (isset($err["regnumcertificate"])) formerr($err["regnumcertificate"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('startdatecertificate', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
//      if (isset($rec->f1_5_day) && $rec->f1_5_day!=0 &&  $rec->f1_5_month !=0 && $rec->f1_5_year !=0)  {
//		 $rec->f1_5g1 = get_timestamp_from_date($rec->f1_5_day, $rec->f1_5_month, $rec->f1_5_year);
//      }
      if (isset($rec->startdatecertificate))  {
          $startdatecertificate = $rec->startdatecertificate;
	  }
	  else {
		  $startdatecertificate = 0;
	  }
      print_date_monitoring('s_s_day', 's_s_month', 's_s_year', $startdatecertificate);
    ?>

    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('enddatecertificate', 'block_monitoring') ?>:</b></td>
    <td align="left">
    <?php
      if (isset($rec->enddatecertificate))  {
          $enddatecertificate = $rec->enddatecertificate;
	  }
	  else {
		  $enddatecertificate = 0;
	  }
      print_date_monitoring('e_s_day', 'e_s_month', 'e_s_year', $enddatecertificate);
    ?>
    </td>
</tr>


</table>
<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
     <input type="hidden" name="schoolid" value="<?php p($sid)?>">
 	 <input type="submit" name="addspec" value="<?php print_string('savechanges')?>">
  </div>
<?php  } ?>
</center>
</form>
<?php
        // print_box_end();
   	    print_simple_box_end();
		print_footer();
    break;



    case 3: // print_box_start();
	 	     print_simple_box_start('center', '70%', 'white');
?>
<form name="addform" method="post" action="addcollege.php">
<input type="hidden" name="mode" value="<?php p($mode)?>">
<input type="hidden" name="rid" value="<?php p($rid)?>">
<input type="hidden" name="sid" value="<?php p($sid)?>">
<input type="hidden" name="yid" value="<?php p($yid)?>">
<input type="hidden" name="tab" value="<?php p($currtab)?>">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string('inn', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" name="inn" size="10" maxlength="15" value="<?php if (isset($rec->inn)) p($rec->inn) ?>" />
		<?php if (isset($err["inn"])) formerr($err["inn"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('kpp', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="kpp" size="10" maxlength="10" value="<?php if (isset($rec->kpp))  p($rec->kpp) ?>" />
		<?php if (isset($err["kpp"])) formerr($err["kpp"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('okpo', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="okpo" size="10" maxlength="10" value="<?php if (isset($rec->okpo))  p($rec->okpo) ?>" />
		<?php if (isset($err["okpo"])) formerr($err["okpo"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('okato', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="okato" size="11" maxlength="11" value="<?php if (isset($rec->okato))  p($rec->okato) ?>" />
		<?php if (isset($err["okato"])) formerr($err["okato"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('okogu', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="okogu" size="10" maxlength="10" value="<?php if (isset($rec->okogu))  p($rec->okogu) ?>" />
		<?php if (isset($err["okogu"])) formerr($err["okogu"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('okfs', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="okfs" size="10" maxlength="10" value="<?php if (isset($rec->okfs))  p($rec->okfs) ?>" />
		<?php if (isset($err["okfs"])) formerr($err["okfs"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('okved', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="okved" size="10" maxlength="20" value="<?php if (isset($rec->okved))  p($rec->okved) ?>" />
		<?php if (isset($err["okved"])) formerr($err["okved"]); ?>
    </td>
</tr>
</table>

<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
     <input type="hidden" name="schoolid" value="<?php p($sid)?>">
 	 <input type="submit" name="addspec" value="<?php print_string('savechanges')?>">
  </div>
<?php  } ?>

</center>
</form>



<?php
        // print_box_end();
	    print_simple_box_end();
		print_footer();
    break;
    case 4: // print_box_start();
	 	     print_simple_box_start('center', '70%', 'white');

?>
<form name="addform" method="post" action="addcollege.php">
<input type="hidden" name="mode" value="<?php p($mode)?>">
<input type="hidden" name="rid" value="<?php p($rid)?>">
<input type="hidden" name="sid" value="<?php p($sid)?>">
<input type="hidden" name="yid" value="<?php p($yid)?>">
<input type="hidden" name="tab" value="<?php p($currtab)?>">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string('type_ege', 'block_monitoring') ?>:</b></td>
    <td align="left"> <?php    unset($choices);
				    $choices[0] = '-';
				    for ($i=1; $i<=5; $i++)	{
					    $choices[$i] = get_string('type_ege'.$i, 'block_monitoring');
					}
				    if (isset($rec->type_ege))  $selected = $rec->type_ege;
				    else $selected = 0;
				    choose_from_menu ($choices, 'type_ege', $selected, '');
				    if (isset($err['type_ege'])) formerr($err['type_ege']); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('state_ege', 'block_monitoring') ?>:</b></td>
    <td align="left"> <?php    unset($choices);
				    $choices[0] = '-';
				    for ($i=1; $i<=6; $i++)	{
					    $choices[$i] = get_string('state_ege'.$i, 'block_monitoring');
					}
				    if (isset($rec->state_ege))  $selected = $rec->state_ege;
				    else $selected = 0;
				    choose_from_menu ($choices, 'state_ege', $selected, '');
				    if (isset($err['state_ege'])) formerr($err['state_ege']); ?>
    </td>
</tr>
</table>
<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
     <input type="hidden" name="schoolid" value="<?php p($sid)?>">
 	 <input type="submit" name="addspec" value="<?php print_string('savechanges')?>">
  </div>
<?php  } ?>
</center>
</form>


<?php

        // print_box_end();
	    print_simple_box_end();
		print_footer();
    break;
    case 5: // print_box_start();
  	 	     print_simple_box_start('center', '70%', 'white');
?>
<form name="addform" method="post" action="addcollege.php">
<input type="hidden" name="mode" value="<?php p($mode)?>">
<input type="hidden" name="rid" value="<?php p($rid)?>">
<input type="hidden" name="sid" value="<?php p($sid)?>">
<input type="hidden" name="yid" value="<?php p($yid)?>">
<input type="hidden" name="tab" value="<?php p($currtab)?>">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string('typesettlement', 'block_monitoring') ?>:</b></td>
    <td align="left"> <?php    unset($choices);
				    $choices[0] = '-';
				    for ($i=1; $i<=7; $i++)	{
					    $choices[$i] = get_string('typesettlement'.$i, 'block_monitoring');
					}
				    if (isset($rec->typesettlement))  $selected = $rec->typesettlement;
				    else $selected = 0;
				    choose_from_menu ($choices, 'typesettlement', $selected, '');
				    if (isset($err['typesettlement'])) formerr($err['typesettlement']); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("iscountryside","block_monitoring") ?>:</b></td>
    <td align="left">
	    <select size="1" name="iscountryside">
	 	   <option <?php if (isset($rec->iscountryside) && $rec->iscountryside == 0) echo 'selected' ?> value="0">-</option>
		   <option <?php if (isset($rec->iscountryside) && $rec->iscountryside == 1) echo 'selected' ?> value="1"><?php print_string('yes'); ?></option>
		   <option <?php if (isset($rec->iscountryside) && $rec->iscountryside == -1) echo 'selected' ?> value="-1"><?php print_string('no'); ?></option>
		</select>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("telnum","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="phones" size="40"  maxlength="99" value="<?php if(isset($rec->phones)) p($rec->phones) ?>" />
		<?php if (isset($err["phones"])) formerr($err["phones"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("fax","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="fax" size="15"  maxlength="15" value="<?php if(isset($rec->fax)) p($rec->fax) ?>" />
		<?php if (isset($err["fax"])) formerr($err["fax"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("realaddress","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="realaddress" size="85"  maxlength="254" value="<?php if (isset($rec->realaddress)) p($rec->realaddress) ?>" />
		<?php if (isset($err["realaddress"])) formerr($err["realaddress"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("isjurequalreal","block_monitoring") ?>:</b></td>
    <td align="left">
	    <select size="1" name="isjurequalreal">
	 	   <option <?php if (isset($rec->isjurequalreal) && $rec->isjurequalreal == 0) echo 'selected' ?> value="0">-</option>
		   <option <?php if (isset($rec->isjurequalreal) && $rec->isjurequalreal == 1) echo 'selected' ?> value="1"><?php print_string('yes'); ?></option>
		   <option <?php if (isset($rec->isjurequalreal) && $rec->isjurequalreal == -1) echo 'selected' ?> value="-1"><?php print_string('no'); ?></option>
		</select>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("juridicaladdress","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="juridicaladdress" size="85"  maxlength="254" value="<?php if (isset($rec->juridicaladdress)) p($rec->juridicaladdress) ?>" />
		<?php if (isset($err["juridicaladdress"])) formerr($err["juridicaladdress"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("www", "block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="www" size="70"  maxlength="255" value="<?php if(isset($rec->www)) p($rec->www) ?>" />
		<?php if (isset($err["www"])) formerr($err["www"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("email","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" name="email" size="40"  maxlength="100" value="<?php if(isset($rec->email)) p($rec->email) ?>" />
		<?php if (isset($err["email"])) formerr($err["email"]); ?>
    </td>
</tr>
</table>
<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
 	 <input type="submit" name="addspec" value="<?php print_string('savechanges')?>">
  </div>
<?php  } ?>
</center>
</form>

<?php
        // print_box_end();
	    print_simple_box_end();
		print_footer();
    break;
}




/// FUNCTIONS ////////////////////
function find_form_school_errors(&$rec, &$err, $currtab, $mode='add')
{

    switch ($currtab)	{    	case 1:
				if ($mode == 'add')  {
			        if (empty($rec->number)) {
			            $err["number"] = get_string("missingname");
					}
					else if (record_exists('monit_college', 'rayonid', $rec->rayonid, 'number', $rec->number))  {
						$err["number"] = get_string("errornumberexist", "block_monitoring");
					}
				}
				else	{
					if (empty($rec->number)) {
			            $err["number"] = get_string("missingname");
					}
					else 	{
						$f = get_record('monit_college', 'id', $rec->id);
						if ($f->number != $rec->number)  {
							if (record_exists('monit_college', 'rayonid', $rec->rayonid, 'number', $rec->number))  {
								$err["number"] = get_string("errornumberexist", "block_monitoring");
							}
						}
					}
				}


		        if (empty($rec->name))	{
				    $err["name"] = get_string("missingname");
				}
		        if (empty($rec->fio))	{
				    $err["fio"] = get_string("missingname");
				}
		break;
		case 5:
		        if (empty($rec->phones))	{
				    $err["phones"] = get_string("missingname");
				}
		        if (empty($rec->realaddress))	{
				    $err["realaddress"] = get_string("missingname");
				}

		   	   if ((!empty($rec->email)) && (!validate_email($rec->email))) {
			 	    $err["email"] = get_string("invalidemail");
		 	   }

/*
		 	   if (empty($rec->email))  {
		   	        $err["email"] = get_string("missingemail");
		   	   }
*/
				if (!empty($rec->www) && !validateUrlSyntax($rec->www, 's+u-I-p-q-r-'))  {		   	        $err["www"] = get_string("invalidurl", "block_monitoring");				}


		break;
	}
    // print_r($err);

    return count($err);

// return 0;
}

?>