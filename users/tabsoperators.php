<?php  // $Id: tabsoperators.php,v 1.3 2008/07/11 10:35:00 Shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

   if (empty($currenttab)) {
       error('You cannot call this script in that way');
   }

   $inactive = NULL;
   $activetwo = NULL;
   $toprow = array();

   $toprow[] = new tabobject('region', $CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=region",
                get_string('regionopers', 'block_monitoring'));

   $toprow[] = new tabobject('rayon', $CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=rayon",
                get_string('rayonopers', 'block_monitoring'));

   $toprow[] = new tabobject('school', $CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rid&amp;sid=$sid&amp;level=school",
                get_string('schoolopers', 'block_monitoring'));

   if ($admin_is || $region_operator_is)	 {
   		$toprow[] = new tabobject('import', $CFG->wwwroot."/blocks/monitoring/users/importopers.php",
     	            get_string('importopers', 'block_monitoring'));

   }

   $tabs = array($toprow);

   print_tabs($tabs, $currenttab, $inactive, $activetwo);

?>
