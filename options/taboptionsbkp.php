<?php  // $Id: taboptionsbkp.php,v 1.2 2008/09/12 11:02:31 Shtifanov Exp $

   if (empty($currenttab)) {
       error('You cannot call this script in that way');
   }
	$inactive = NULL;
	$activetwo = NULL;
	$toprow = array();

	$toprow[] = new tabobject(1, $CFG->wwwroot."/blocks/monitoring/options/options.php?ct=1&amp;idf=$idf&amp;id_ts=$id_ts", $optionsrzd);

//	$bkpforms = get_records_sql("select id, name from {$CFG->prefix}monit_form where period='year' and levelmonit='$levelmonit'");
//	if($bkpforms)  {
//		$i = 1;
//		foreach ($bkpforms as $bkpform)  {//			if($i == 1 && $fid==0)  {$fid=$bkpform->id;}
//			$toprow[] = new tabobject($i, $CFG->wwwroot."/blocks/monitoring/options/options.php?ct=$ct&amp;fid=$fid&amp;tsid=$tsid", );
//			$i++;
//		}
//	}
   $tabs = array($toprow);
   print_tabs($tabs, $currenttab, $inactive, $activetwo);
?>