<?PHP // $Id: addrayon.php,v 1.10 2009/02/25 08:23:50 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../../lib/validateurlsyntax.php');
    require_once('../lib.php');

    $mode = required_param('mode', PARAM_ALPHA);    // new, add, edit, update
	$rid = optional_param('rid', 0, PARAM_INT);

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    if ($mode === "new" || $mode === "add" )	{
   	    $straddrayon = get_string('addrayon','block_monitoring');
    }
	else  {		$straddrayon = get_string('updaterayon','block_monitoring');
	}

    $strrayons = get_string('rayons', 'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/rayon/rayons.php\">$strrayons</a>";
	$breadcrumbs .= " -> $straddrayon";
    print_header_mou("$site->shortname: $straddrayon", $site->fullname, $breadcrumbs);


    switch ($mode)	{
    	case 'new':
			    	if ($allrayons = get_records('monit_rayon')) {
			    		$rec->number = count($allrayons) + 1;
			    	}
    	break;
	 	case 'add': if ($admin_is) {
						$rec->number = required_param('number');
						$rec->name = required_param('name');
						$rec->fio = required_param('fio');
						$rec->phones = required_param('phones');
						$rec->address = required_param('address');
						$rec->www = required_param('www');
						$rec->email = required_param('email');

						if (find_form_rayon_errors($rec, $err) == 0) {
							$rec->timemodified = time();
							if (insert_record('monit_rayon', $rec))	{
								 echo '<div align=center>';
								 notice(get_string('rayonadded','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/rayons.php");
								 echo '</div>';
							} else  {
								error(get_string('errorinaddingrayon','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/rayons.php");
							}
						}
						else $mode = "new";
					}
		break;
		case 'edit':
					if ($rid > 0) 	{
					    $rayon = get_record('monit_rayon', 'id', $rid);
						$rec->id = $rayon->id;
						$rec->number = $rayon->number;
						$rec->name = $rayon->name;
						$rec->fio = $rayon->fio;
						$rec->phones = $rayon->phones;
						$rec->address = $rayon->address;
						$rec->www = $rayon->www;
						$rec->email = $rayon->email;
					}
		break;
		case 'update':
					$rec->id = required_param('rayonid', PARAM_INT);
					$rec->number = required_param('number');
					$rec->name = required_param('name');
					$rec->fio = required_param('fio');
					$rec->phones = required_param('phones');
					$rec->address = required_param('address');
					$rec->www = required_param('www');
					$rec->email = required_param('email');

					if (find_form_rayon_errors($rec, $err, $mode) == 0) {
						$rec->timemodified = time();
						if (update_record('monit_rayon', $rec))	{
							 // add_to_log(1, 'dean', 'faculty update', 'blocks/dean/faculty/faculty.php', $USER->lastname.' '.$USER->firstname);
							 echo '<div align=center>';
							 notice(get_string('rayonupdate','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/rayons.php");
							 echo '</div>';
						} else {
							error(get_string('errorinupdatingrayon','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/rayon/rayons.php");
						}
					}
		break;
	}


	print_heading($straddrayon);

    print_simple_box_start("center");
/*
	if (isset($deanmenu)) unset($deanmenu);
	$dekani = get_records_sql ("SELECT id, userid FROM {$CFG->prefix}user_teachers");
	if ($dekani)	{
		foreach ($dekani as $dekan) {
				$duser = get_record("user", "id", $dekan->userid);
				$deanmenu[$duser->id] = fullname ($duser);
		}
	}
	natsort($deanmenu);
	$deanmenu[0] = get_string("selectadean","block_monitoring")." ...";
*/
?>

<form name="addform" method="post" action="<?php if ($mode === 'new') echo "addrayon.php?mode=add";
												 else echo "addrayon.php?mode=update&amp;rid=$rid"; ?>">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  print_string("numberrayon", "block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" id="number" name="number" size="5" value="<?php if (isset($rec->number)) p($rec->number) ?>" />
		<?php if (isset($err["number"])) formerr($err["number"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("name") ?>:</b></td>
    <td align="left">
		<input type="text" id="name" name="name" size="70" maxlength="255" value="<?php if (isset($rec->name))  p($rec->name) ?>" />
		<?php if (isset($err["name"])) formerr($err["name"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string('headrayonname', 'block_monitoring') ?>:</b></td>
    <td align="left">
		<input type="text" id="fio" name="fio" size="70" maxlength="100" value="<?php if (isset($rec->fio))  p($rec->fio) ?>" />
		<?php if (isset($err["fio"])) formerr($err["fio"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("telnum","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" id="phones" name="phones" size="50"  maxlength="100" value="<?php if(isset($rec->phones)) p($rec->phones) ?>" />
		<?php if (isset($err["phones"])) formerr($err["phones"]); ?>
    </td>
</tr>
<tr valign="top">
    <td align="right"><b><?php  print_string("address","block_monitoring") ?>:</b></td>
    <td align="left">
		<input type="text" id="address" name="address" size="70"  maxlength="500" value="<?php if (isset($rec->address)) p($rec->address) ?>" />
		<?php if (isset($err["address"])) formerr($err["address"]); ?>
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
		<input type="text" name="email" size="50"  maxlength="100" value="<?php if(isset($rec->email)) p($rec->email) ?>" />
		<?php if (isset($err["email"])) formerr($err["email"]); ?>
    </td>
</tr>
</table>

<?php
     if (!isregionviewoperator() && !israyonviewoperator())  {
?>
		   <div align="center">
		  <input type="hidden" name="rayonid" value="<?php p($rid)?>">
		  <input type="submit" name="addfaculty" value="<?php print_string('savechanges')?>">
		  </div>
<?php
	}
?>


 </center>
</form>


<?php
    print_simple_box_end();

	print_footer();


/// FUNCTIONS ////////////////////
function find_form_rayon_errors(&$rec, &$err, $mode='add')
{
		if ($mode == 'add')  {
	        if (empty($rec->number)) {
	            $err["number"] = get_string("missingname");
			}
			else if (record_exists('monit_rayon', 'number', $rec->number))  {
				$err["number"] = get_string("errornumberexist", "block_monitoring");
			}
		}
		else	{
			if (empty($rec->number)) {
	            $err["number"] = get_string("missingname");
			}
			else 	{
				$f = get_record('monit_rayon', 'id', $rec->id);
				if ($f->number != $rec->number)  {
					if (record_exists('monit_rayon', "number", $rec->number))  {
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
        if (empty($rec->phones))	{
		    $err["phones"] = get_string("missingname");
		}
        if (empty($rec->address))	{
		    $err["address"] = get_string("missingname");
		}

		if (!validate_email($rec->email)) {
 	       $err["email"] = get_string("invalidemail");
 	    }

 	    if (empty($rec->email))  {
   	         $err["email"] = get_string("missingemail");
   	    }

		if (!empty($rec->www) && !validateUrlSyntax($rec->www))  {
   	        $err["www"] = get_string("invalidurl", "block_monitoring");
		}


	   return count($err);
}

?>