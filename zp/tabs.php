<?php  // $Id: tabs.php,v 1.3 2012/09/24 11:40:51 shtifanov Exp $

    if (empty($currenttab)) {
        error('You cannot call this script in that way');
    }

    $toprow   = array();
    $toprow[] = new tabobject('listforms', "listforms.php?rid=$rid&amp;nm=$nm&amp;yid=$yid",
                get_string('primarydata', 'block_monitoring'));

    $toprow[] = new tabobject('rayon', "sumreports.php?rid=$rid&nm=$nm&yid=$yid&level=rayon",
                get_string('summaryreportsrayon', 'block_monitoring'));

    if ($admin_is || $region_operator_is)   {                
        $toprow[] = new tabobject('region', "sumreports.php?rid=-1&nm=$nm&yid=$yid&level=region",
                    get_string('summaryreportsregion', 'block_monitoring'));
    }            
                
		
    $tabs = array($toprow);

    print_tabs($tabs, $currenttab, NULL, NULL);

?>