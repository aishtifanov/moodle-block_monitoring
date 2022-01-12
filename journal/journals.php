<?php // $Id: journals.php,v 1.2 2007/12/22 10:40:55 Shtifanov Exp $

	require_once("../../../config.php");
	require_once('../lib.php');
	require_once($CFG->libdir.'/tablelib.php');
	$perpage = optional_param('perpage', 20, PARAM_INT);        // how many per page
	$tsort = optional_param('tsort', 'status', PARAM_ALPHA);
	$nsort = optional_param('nsort', 'ASC', PARAM_ALPHA);
	$page = optional_param('page', 0, PARAM_INT);

	$idf = optional_param('idf', 0, PARAM_INT);
	$ids = optional_param('ids', 0, PARAM_INT);
	$idr = optional_param('idr', 0, PARAM_INT);

	$n = optional_param('n', 0, PARAM_INT);

	$frm = data_submitted(); /// load up any submitted data
    if($frm)  {
		$idf = $frm->idf;
		$ids = $frm->ids;
		$idr = $frm->idr;
	}

	$journalreportsdescription = get_string('journalreportsdescription', 'block_monitoring');

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/frontpage.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $journalreportsdescription";
    print_header("$site->shortname: $journalreportsdescription", $site->fullname, $breadcrumbs);

	print_simple_box_start("center", "%100");

	$tables = get_records_sql("select * from {$CFG->prefix}monit_form");
	$list_form[0] = get_string('all');

	foreach ($tables as $table){		$list_form[$table->id] = get_string($table->levelmonit,'block_monitoring').':  '.$table->name;
	}
    $tables = get_records_sql("select * from {$CFG->prefix}monit_status");
	$list_status[0] = get_string('all');

    foreach ($tables as $table){
		$list_status[$table->id] = $table->name;
	}
	$tables = get_records_sql("select * from {$CFG->prefix}monit_rayon");
	$list_region[0] = get_string('all');

	foreach ($tables as $table){
		$list_region[$table->id] = $table->name;
   }

	echo "<form name='form_report' method='post' action='reports.php'>";
	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
    echo '<tr><td align=right>';
	print_string('reports', 'block_monitoring');
	echo '</td><td>';
	choose_from_menu ($list_form, "idf", "$idf");
    echo '</td></tr>';
    echo '<tr><td align=right>';
	print_string('status', 'block_monitoring');
    echo '</td><td>';
	choose_from_menu ($list_status, "ids", "$ids");
    echo '</td></tr>';
    echo '<tr><td align=right>';
	print_string('rayon', 'block_monitoring');
    echo '</td><td>';
	choose_from_menu ($list_region, "idr", "$idr");
    echo '</td></tr>';
    echo '<tr><td align=right>';
    echo '</td><td>';
    $search = get_string('search');
    print "<input type=submit value=$search>";
    echo '</td></tr></table>';
	echo "</form>";

//print_r($frm);

    if($frm || ($n == 1))  {		if($n==0)  {
      $temptable = $CFG->prefix . 'monit';
      $droptablesql[] = 'DROP TEMPORARY TABLE ' . $temptable; // sql command to drop the table (because session scope could be a problem)
      execute_sql_arr($droptablesql, true, false); /// Drop temp table to avoid persistence problems later
      echo "Creating temp table $temptable\n";
      execute_sql('CREATE TEMPORARY TABLE ' . $temptable . ' (username VARCHAR(64), PRIMARY KEY (username)) TYPE=MyISAM', true);
/*
			$db->Prepare("CREATE TEMPORARY TABLE `{$CFG->prefix}monit_reports` (
							`id` int(10) NOT NULL auto_increment,
							`status` VARCHAR(20),
							`name` TEXT,
							`period` VARCHAR(10),
							`action` TEXT,
							PRIMARY KEY(`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$r = $db->Execute();
*/
//    print "r=$r";
//execute_sql("insert into {$CFG->prefix}monit_reports(status) select (2)", true);
//execute_sql("insert into {$CFG->prefix}monit_reports(status) select (3)", true);
//			execute_sql("TRUNCATE TABLE {$CFG->prefix}monit_reports", false);
			if($frm->idf!=0)  {				$where = " where formid=$frm->idf";			}	        $tables=get_records_sql("select * from {$CFG->prefix}monit_razdel$where");
	        $where = '';
			if($tables)  {				foreach($tables as $tbl)  {					$formname = get_record_sql("select name from {$CFG->prefix}monit_form where id=$tbl->formid");
					$formname = $formname->name;
					$fid = $tbl->formid;
					if($frm->ids!=0 || $frm->idr!=0)  {						$where=' where';
					}
					if($frm->ids!=0)  {						$where.=" status=$frm->ids";					}
					if($frm->idr!=0)  {						if($where ==' where')  {							$where.=" regid=$frm->idr";						}  else  {							$where.=" and regid=$frm->idr";
						}
					}
					$bkpstatus = get_records_sql("select id, schoolid, status, datemodified from {$CFG->prefix}monit_bkp_table_$tbl->id$where");
					if($bkpstatus)  {
						foreach($bkpstatus as $bkpstat)  {							$sid = $bkpstat->schoolid;							$status = get_record('monit_status', 'id', $bkpstat->status);
							$color = $status->color;
	   						$status = $status->name;
							$rid = get_record_sql("select id, rayonid from {$CFG->prefix}monit_school where id=$sid");
							$rid = $rid->rayonid;
							$strlinkupdate = "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/monitoring/school/bkpforms.php?sid=$sid&amp;rzd=$tbl->id&amp;fid=$fid&amp;nm=1&amp;rid=$rid\">";
							$strlinkupdate .= "<img src=\"{$CFG->pixpath}/t/edit.gif\" alt=\"$title\" /></a>&nbsp;";
							$log->status = $status;
							$log->name = $formname.' '.$tbl->shortname;
							$log->action = $strlinkupdate;
							$log->period = date("Y", $bkpstat->datemodified).' '.get_string('year', 'block_monitoring');
							insert_record("monit_reports", $log);
						}
					}  else  {						$status = get_record('monit_status', 'id', 1);
						$color = $status->color;
   						$status = $status->name;


							$rid = get_record_sql("select id, rayonid from {$CFG->prefix}monit_school where id=$sid");
							$rid = $rid->rayonid;
							$sid = $bkpstat->schoolid;

							$strlinkupdate = "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/monitoring/school/bkpforms.php?sid=$sid&amp;rzd=$tbl->id&amp;fid=$fid&amp;nm=1&amp;rid=$rid\">";
							$strlinkupdate .= "<img src=\"{$CFG->pixpath}/t/edit.gif\" alt=\"$title\" /></a>&nbsp;";
						$log->status = $status;
						$log->name = $formname.' '.$tbl->shortname;
						$log->action = $strlinkupdate;
						$log->period = date("Y", $date).' '.get_string('year', 'block_monitoring');
						insert_record("monit_reports", $log);
					}
				}
			}
		}

		$sort = $nsort;
		if($nsort == 'DESC'){
			$nsort = 'ASC';
		}  else  {
			$nsort = 'DESC';
		}

		$tablecolumns = array('status', 'name', 'period', 'action');
		$tableheaders = array(get_string('status', 'block_monitoring'), get_string('nameofpokazatel', 'block_monitoring'), get_string('period', 'block_monitoring'), get_string('action', 'block_monitoring'));
		$table = new flexible_table('reports');
		$table->column_style_all('align', 'center');
		$table->define_columns($tablecolumns);
		$table->define_headers($tableheaders);
		$table->define_baseurl("{$CFG->wwwroot}/blocks/monitoring/report/reports.php?idf=$idf&amp;ids=$ids&amp;idr=$idr&amp;nsort=$nsort&amp;n=1");
		$table->sortable(true, 'status');
		$table->set_attribute('align', 'center');
		$table->column_style('status', 'text-align', 'center');
		$table->column_style('name', 'text-align', 'left');
		$table->column_style('period', 'text-align', 'center');
		$table->column_style('action', 'text-align', 'center');
		$table->set_attribute('cellspacing', '2');
		$table->set_attribute('class', 'generaltable generalbox');
		$table->setup();
		$table_rep = get_records_select('monit_reports', '', $tsort. ' '. $sort, 'id, status');
		$table->pagesize($perpage, count($table_rep));
		$table_rep = get_records_select('monit_reports', '', $tsort. ' '. $sort, 'id, status, name, action, period', $page * $perpage, $perpage);
		foreach ($table_rep as $tbl) {			$table->add_data(array ($tbl->status, $tbl->name, $tbl->period, $tbl->action));
		}
		$table->print_html();
  	}

    print_footer();
?>