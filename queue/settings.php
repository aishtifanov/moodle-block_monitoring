<?PHP // $Id: settings.php,v 1.1 2012/05/11 13:05:55 shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');
    require_once('lib_queue.php');
    
    $rid = optional_param('rid', 0, PARAM_INT);    // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);    // OU id
    $yid = optional_param('yid', 0, PARAM_INT);    // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $tab = optional_param('tab', 'settings');          // Rayon id


	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strrequest, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    include('tabs.php');

    if (isoperatorinanyou($USER->id, true)) {
	   	$table = table_birthyearperiod ($rid);
        print_heading('Диапазоны дат рождения детей дошкольного возраста', 'center');
        print_color_table($table);        

/*	
	   	if (isset($table->data))	{
	 		print_color_table($table);
		} else {
			notice(get_string('notfoundtypestudyperiod', 'block_mou_school'), "typestudyperiod.php?rid=$rid&amp;sid=$sid&amp;yid=$yid");
		}
*/
        
	}	else {
		error(get_string('permission', 'block_mou_school'), '../index.php');
	}

    print_footer();

	// print_heading(get_string('studyperiod', 'block_mou_school'), 'center');
	

function table_birthyearperiod ($rid)
{
	global $CFG, $context;

	$edit_capability = false; // ('block/mou_school:edittypestudyperiod', $context);
	
	$table->head  = array (	get_string('name', 'block_mou_school'), get_string('timestart', 'block_mou_school'),
							get_string('timeend', 'block_mou_school'), get_string('action', 'block_mou_school'));
    $table->align = array ("center", "center", "center", "center");
    $table->class = 'moutable';
    $table->size = array('10%', '10%', '10%', '5%');
   	$table->width = '50%';

	$terms = get_records_sql ("SELECT * FROM {$CFG->prefix}monit_queue_birthyear
							   WHERE rayonid=1
                               ORDER BY name");

	if ($terms)	{
		foreach ($terms as $term) {

			if ($edit_capability)	{
				$title = get_string('editperiod','block_mou_school');
				$strlinkupdate = "<a title=\"$title\" href=\"addperiod.php?mode=edit&amp;yid=$yid&amp;sid=$sid&amp;rid=$rid&amp;tid={$term->id}\">";
				$strlinkupdate = $strlinkupdate . "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				/*
				$title = get_string('deleteperiod','block_mou_school');
			    $strlinkupdate = $strlinkupdate . "<a title=\"$title\" href=\"delcurriculum.php?sid=$sid&amp;cid={$curr->id}\">";
				$strlinkupdate = $strlinkupdate . "<img src=\"{$CFG->pixpath}/t/delete.gif\" alt=\"$title\" /></a>&nbsp;";
				*/
				$title = get_string('disciplines','block_mou_school');
				$strdiscipline = $term->name;
			}
			else	{
				$strlinkupdate = '-';
				$strdiscipline = $term->name;
			}

			$table->data[] = array ($strdiscipline, convert_date($term->datestart, 'en', 'ru'),
									convert_date($term->dateend, 'en', 'ru'), $strlinkupdate);
		}
	}
    return $table;
}


?>

