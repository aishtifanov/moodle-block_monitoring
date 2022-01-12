<?php  // $Id: tabsreportbkp.php,v 1.9 2008/09/12 08:44:27 Shtifanov Exp $

   if (empty($currenttab)) {
       error('You cannot call this script in that way');
   }

	$schooltype = get_record_sql("select stateinstitution from {$CFG->prefix}monit_school where id=$sid");

	$inactive = NULL;
	$activetwo = NULL;
	$toprow = array();
	$bkpforms = get_records_sql("select id, name from {$CFG->prefix}monit_form where period='year' and levelmonit='$levelmonit' order by reported");
	
	if($bkpforms)  {
		$i = 1;
		foreach ($bkpforms as $bkpform)  {
			switch ($levelmonit)	{
				case 'region':
						if($i == 1 && $fid==0)  {
							$fid = $bkpform->id;
						}
		
						$toprow[] = new tabobject($i, $CFG->wwwroot."/blocks/monitoring/bkp/bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$bkpform->id&amp;id=$i", $bkpform->name);
						$i++;
				break;
				case 'rayon':
						if($i == 1 && $fid==0)  {
							$fid = $bkpform->id;
						}
		
						$toprow[] = new tabobject($i, $CFG->wwwroot."/blocks/monitoring/bkp/bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$bkpform->id&amp;id=$i", $bkpform->name);
						$i++;
		  		break;
				case 'school':
					$formvisible = verify_visible_form($schooltype->stateinstitution, $bkpform->id, $sid);
					if($formvisible) {
						if($i == 1 && $fid==0)  {
							$fid = $bkpform->id;
						}
						$toprow[] = new tabobject($i, $CFG->wwwroot."/blocks/monitoring/bkp/bkpmain.php?level=$levelmonit&amp;rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid&amp;fid=$bkpform->id&amp;id=$i", $bkpform->name);
						$i++;
					}
				break;
			}

		}
	}
	print_heading(get_string('nameform', 'block_monitoring'), "center");
	$tabs = array($toprow);

	print_tabs($tabs, $currenttab, $inactive, $activetwo);
?>