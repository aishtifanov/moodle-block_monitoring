<?php  // $Id: statsregion.php,v 1.8 2012/12/06 12:30:26 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('regionrating_form.php');
    require_once('lib_rating.php');
    
	define("NUMBER_OF_STATUS", 6);
	
    $rid = optional_param('rid', 0, PARAM_INT);
    $sid = optional_param('sid', 0, PARAM_INT);            // School id

    require_login();

	// $nm  = optional_param('nm', date('n'), PARAM_INT);       // Month number
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $nm = 9;    

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
    $rayon_operator_is = false;
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}
/*
	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}
*/
    $strrayons = get_string('rayons', 'block_monitoring');
	$strreportregion = 'Перерасчет показателей';

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> $strreportregion";
    print_header_mou("$SITE->shortname: $strreportregion", $SITE->fullname, $breadcrumbs);

    $currenttab = 'reports';
    include('tabs.php');

    $currenttab2 = 'recalcpage';
    include('tabs2.php');    

    print_tabs_years_rating("recalcpage.php?a=0", $rid, $sid, $yid);

    
?>    
	<form name="recalcform" method="post" action="recalcrating.php">
	<input type="hidden" name="rid" value="1" />
    <input type="hidden" name="yid" value=" <?php echo $yid ?>" />
	<table align="center"><tr><td align="center">
	<input type="submit" name="recalc" value="<?php print_string('recalcrating', 'block_monitoring') ?>" />
	 </td></tr></table>
	</form>
    <hr />
	<form name="recalcform" method="post" action="recalcdynamic.php">
	<input type="hidden" name="rid" value="1" />
    <input type="hidden" name="yid" value=" <?php echo $yid ?>" />
	<table align="center"><tr><td align="center">
	<input type="submit" name="recalc" value="Расчет динамических показателей школ" />
	 </td></tr></table>
	</form>
<?php    

    print_footer();

?>