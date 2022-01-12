<?php  // $Id: tabs.php,v 1.12 2012/12/05 10:19:34 shtifanov Exp $

    if (empty($currenttab)) {
        error('You cannot call this script in that way');
    }

    $toprow   = array();
    $toprow[] = new tabobject('listforms', "listforms.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid",
                get_string('begindataou', 'block_monitoring'));
            
    /*            
    if ($admin_is || $region_operator_is || $rayon_operator_is)  {
        $toprow[] = new tabobject('listformsrayon', "listformsrayon.php?rid=$rid&nm=$nm&yid=$yid",
                    get_string('begindatamo', 'block_monitoring'));
    }
    */

	if ($admin_is || $region_operator_is) { // } || $rayon_operator_is)  {
        $toprow[] = new tabobject('listcriteria', "listcriteria.php?rid=$rid&amp;sid=$sid&amp;yid=$yid",
                    get_string('listcriteria', 'block_monitoring'));
	    $toprow[] = new tabobject('ratingrayon', "ratingrayon.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid",
    	            get_string('ratingrayon', 'block_monitoring'));
	    $toprow[] = new tabobject('summaryrating', "summaryrating.php?rid=$rid&amp;sid=$sid&amp;nm=$nm&amp;yid=$yid",
    	            get_string('summaryrating2', 'block_monitoring'));
	}

	if ($admin_is || $region_operator_is)  {

	    $toprow[] = new tabobject('reports', "regionrating.php?rid=$rid&amp;sid=$sid&amp;yid=$yid", 'Настройки и отчеты');

	   /*

	    $toprow[] = new tabobject('regionrating', "regionrating.php?rid=$rid&amp;sid=$sid&amp;yid=$yid",
    	            get_string('regionrating', 'block_monitoring'));
	    $toprow[] = new tabobject('statsregion', "statsregion.php?rid=$rid&amp;sid=$sid&amp;yid=$yid",
    	            get_string('statsregion', 'block_monitoring'));
	    $toprow[] = new tabobject('selectdata', "selectdata.php?rid=$rid&amp;sid=$sid&amp;yid=$yid",
    	            get_string('selectdata', 'block_monitoring'));
       */             

/*    	            
	    $toprow[] = new tabobject('ratingregion', "ratingregion.php?rid=$rid&amp;sid=$sid&amp;yid=$yid",
    	            get_string('ratingregion', 'block_monitoring'));
*/    	            
	}
		
    $tabs = array($toprow);

    print_tabs($tabs, $currenttab, NULL, NULL);

?>