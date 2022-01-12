<?PHP // $Id: addremark.php,v 1.2 2009/04/13 05:55:48 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');

    $mode = required_param('mode', PARAM_ALPHA);    // new, add, edit, update
    $rid = required_param('rid', PARAM_INT);   // Rayon id
    $sid = required_param('sid', PARAM_INT);	// School id
	$yid = required_param('yid', PARAM_INT);			// Year id
	$mid = optional_param('mid', 0, PARAM_INT);			// Remark id

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('staff');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('staffaccess', 'block_mou_att'));
	}

    $straccreditation = get_string('accreditation', 'block_monitoring');
    $strremarks = get_string('remarks', 'block_monitoring');
    if ($mode === "new" || $mode === "add" ) {
    	$straddremark = get_string('addremark','block_monitoring');
    } else {
    	$straddremark = get_string('updateremark','block_monitoring');
    }


    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= "-> <a href=\"accreditation.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">$straccreditation</a>";
	$breadcrumbs .= "-> $strremarks";
	$breadcrumbs .= "-> $straddremark";
    print_header("$site->shortname: $straddremark", $site->fullname, $breadcrumbs);

	$rec->schoolid = $sid;
	$rec->name = '';

	if ($mode === 'add')  {
		$rec->name = required_param('name');

		if (find_form_disc_errors($rec, $err) == 0) {
			// $rec->timemodified = time();
			if ($mid = insert_record('monit_accr_remark', $rec))		{				 // add_to_log(1, 'school', 'one discipline added', "blocks/school/curriculum/addiscipline.php?mode=2&amp;fid=$fid&amp;sid=$sid&amp;cid=$cid", $USER->lastname.' '.$USER->firstname);
				 notice(get_string('remarkadded','block_monitoring'), "accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark");
			} else
				error(get_string('errorinaddingremark','block_monitoring'), "accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark");
		}
		else $mode = "new";
	}
	else if ($mode === 'edit')	{
		if ($mid > 0) 	{
			$remark = get_record('monit_accr_remark', 'id', $mid);
			$rec->id = $remark->id;
			$rec->name = $remark->name;
		}
	}
	else if ($mode === 'update')	{
		$rec->id = required_param('mid', PARAM_INT);
		$rec->name = required_param('name');

		if (find_form_disc_errors($rec, $err) == 0) {
			if (update_record('monit_accr_remark', $rec))	{				 // add_to_log(1, 'school', 'discipline update', "blocks/school/curriculum/addiscipline.php?mode=2&amp;fid=$fid&amp;sid=$sid&amp;cid=$cid", $USER->lastname.' '.$USER->firstname);
				 notice(get_string('remarkupdate','block_monitoring'), "accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark");
			} else  {
				error(get_string('errorinupdatingremark','block_monitoring'), "accreditation.php?tab=schoolreport&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;ataba=remark");
			}
		}
	}


	print_heading($straddremark, "center", 3);

    print_simple_box_start("center");

	if ($mode === 'new') $newmode='add';
	else 				 $newmode='update';

?>

<form name="addform" method="post" action="addremark.php">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string('remark', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text"  name="name" size="120" value="<?php p($rec->name) ?>" />
		<?php if (isset($err["name"])) formerr($err["name"]); ?>
    </td>
</tr>
</table>
<?php  if (!isregionviewoperator() && !israyonviewoperator())  {  ?>
   <div align="center">
     <input type="hidden" name="mode" value="<?php echo $newmode ?>">
     <input type="hidden" name="rid" value="<?php echo $rid ?>">
     <input type="hidden" name="sid" value="<?php echo $sid ?>">
     <input type="hidden" name="yid" value="<?php echo $yid ?>">
     <input type="hidden" name="mid" value="<?php echo $mid ?>">
 	 <input type="submit" name="adddisc" value="<?php print_string('savechanges')?>">
  </div>
<?php  }  ?>
 </center>
</form>


<?php
    print_simple_box_end();

	print_footer();


/// FUNCTIONS ////////////////////
function find_form_disc_errors(&$rec, &$err, $mode='add') {

    if (empty($rec->name)) {
            $err["name"] = get_string("missingname");
	}

    return count($err);
}

?>