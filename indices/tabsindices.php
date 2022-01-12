<?php  // $Id: tabsindices.php,v 1.1.1.1 2007/12/12 07:26:01 Shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set

   if (empty($currenttab)) {
       error('You cannot call this script in that way');
   }

   $inactive = NULL;
   $activetwo = NULL;
   $toprow = array();

   $toprow[] = new tabobject('region', $CFG->wwwroot."/blocks/monitoring/indices/indices.php?rid=$rid&amp;sid=$sid&amp;level=region",
                get_string('indicesregion', 'block_monitoring'));

   $toprow[] = new tabobject('rayon', $CFG->wwwroot."/blocks/monitoring/indices/indices.php?rid=$rid&amp;sid=$sid&amp;level=rayon",
                get_string('indicesrayon', 'block_monitoring'));

   $toprow[] = new tabobject('school', $CFG->wwwroot."/blocks/monitoring/indices/indices.php?rid=$rid&amp;sid=$sid&amp;level=school",
                get_string('indicesschool', 'block_monitoring'));

   $tabs = array($toprow);

   print_tabs($tabs, $currenttab, $inactive, $activetwo);

?>
