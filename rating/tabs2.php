<?php  // $Id: tabs.php,v 1.12 2012/12/05 10:19:34 shtifanov Exp $

    $toprow2   = array();

	if ($admin_is || $region_operator_is)  {

        $link = "rid=$rid&sid=$sid&yid=$yid";
	    $toprow2[] = new tabobject('regionrating', "regionrating.php?{$link}", get_string('regionrating', 'block_monitoring'));
	    $toprow2[] = new tabobject('statsregion', "statsregion.php?{$link}", get_string('statsregion', 'block_monitoring'));
	    $toprow2[] = new tabobject('selectdata', "selectdata.php?{$link}",  get_string('selectdata', 'block_monitoring'));
	    $toprow2[] = new tabobject('recalcpage', "recalcpage.php?{$link}", 'Перерасчет показателей');
	    $toprow2[] = new tabobject('changestatusgroup', "changestatusgroup.php?{$link}", 'Изменение статуса таблиц');
	}
		
    $tabs2 = array($toprow2);

    print_tabs($tabs2, $currenttab2, NULL, NULL);

?>