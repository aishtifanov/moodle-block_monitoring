<?php // $Id: htmlregionforms.php,v 1.7 2009/02/25 08:23:51 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $fid = required_param('fid', PARAM_INT);     // Form id
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $nm = required_param('nm', PARAM_INT);       // Month number
    $shortname = required_param('sn');       // Shortname form

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

    $strreports = get_string('reportregion', 'block_monitoring');
    $strrep = get_string('reports', 'block_monitoring');
	$strformname = get_string('name_'.$shortname,'block_monitoring');

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title','block_monitoring').'</a>';
	$breadcrumbs .= " -> <a href=\"{$CFG->wwwroot}/blocks/monitoring/region/region.php\">$strrep</a>";
	$breadcrumbs .= " -> $strformname";
    print_header_mou("$site->shortname: $strformname", $site->fullname, $breadcrumbs);

	if ($rec = data_submitted())  {

	    if ($shortname == 'rkp_d')	 {
		    $frm = $rec;
	    	$errcount = find_errors_in_links($frm, $err);
	    } else {
		    $errcount = find_form_errors($rec, $err, 'monit_form_'.$shortname);
	    }

		if ($errcount == 0)  {

		    if ($fid == 0)  { // insert new records
		       $rkp->regionid = 1;
		       $rkp->status = 2;
		       $rkp->shortname = $shortname;
		       $rkp->shortrusname =  $strformname;
		       // $rkp->fullname = ??????????
		       $rkp->datemodified = get_date_from_month_year($nm, $yid);


		       if (!$idnew = insert_record('monit_region_listforms', $rkp))	{
					error(get_string('errorincreatinglist','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/region/region.php?nm=$nm&amp;yid=$yid");
			   }

		       $rec->listformid = $idnew;

		        switch($shortname)	{
		        	case 'rkp_d':				   	    fill_frm($frm);
			 			fill_record($frm, $rec);
			 		break;
			 		case 'rkp_prr_ro':
	 			        if ($rec->f2_5_1rday!=0 &&  $rec->f2_5_1rmonth !=0 && $rec->f2_5_1ryear !=0) {
					  	   $rec->f2_5_1r = get_timestamp_from_date($rec->f2_5_1rday, $rec->f2_5_1rmonth, $rec->f2_5_1ryear);
	 					}
	 				break;
			    }


		       if (!insert_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorincreatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/region/region.php?nm=$nm&amp;yid=$yid");
			   }
		       notice(get_string('succesavedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/region/region.php?nm=$nm&amp;yid=$yid");

		    } else {  // update records

			   $rec->listformid = $fid;
		       $df = get_record_sql("SELECT id, listformid FROM {$CFG->prefix}monit_form_$shortname WHERE listformid=$fid");
		       $rec->id = $df->id;

		        switch($shortname)	{
		        	case 'rkp_d':
				   	    fill_frm($frm);
			 			fill_record($frm, $rec);
			 		break;
			 		case 'rkp_prr_ro':
	 			        if ($rec->f2_5_1rday!=0 &&  $rec->f2_5_1rmonth !=0 && $rec->f2_5_1ryear !=0) {
					  	   $rec->f2_5_1r = get_timestamp_from_date($rec->f2_5_1rday, $rec->f2_5_1rmonth, $rec->f2_5_1ryear);
	 					}
	 				break;
			    }

		       if (!update_monit_record('monit_form_'.$shortname, $rec))	{
					error(get_string('errorinupdatingform','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/region/region.php?nm=$nm&amp;yid=$yid");
			   }
		       notice(get_string('succesupdatedata','block_monitoring'), "$CFG->wwwroot/blocks/monitoring/region/region.php?nm=$nm&amp;yid=$yid");
		    }

		}
	}

    if ($fid != 0)  {
	   	$rec = get_record('monit_form_'.$shortname, 'listformid', $fid);
        if ($shortname == 'rkp_d')	 {
	       fill_frm_list($frm, $rec);
	    }
    }

	print_heading($strreports, "center", 3);
	print_heading($strformname, "center", 4);

    print_simple_box_start("center");

    include("$shortname.html");
    include("end_of_forms.html");

  	print_simple_box_end();

    print_footer();


//
// FUNCTION
//
function fill_frm(&$frm)
{
 if ($frm->fd1_factday!=0 &&  $frm->fd1_factmonth !=0 && $frm->fd1_factyear !=0)
    $frm->fd1_fact=get_timestamp_from_date($frm->fd1_factday,$frm->fd1_factmonth,$frm->fd1_factyear);

 if ($frm->fd2_factday!=0 &&  $frm->fd2_factmonth !=0 && $frm->fd2_factyear !=0)
	$frm->fd2_fact=get_timestamp_from_date($frm->fd2_factday,$frm->fd2_factmonth,$frm->fd2_factyear);

 if ($frm->fd3_factday!=0 &&  $frm->fd3_factmonth !=0 && $frm->fd3_factyear !=0)
	$frm->fd3_fact=get_timestamp_from_date($frm->fd3_factday,$frm->fd3_factmonth,$frm->fd3_factyear);

 if ($frm->fd4_factday!=0 &&  $frm->fd4_factmonth !=0 && $frm->fd4_factyear !=0)
	$frm->fd4_fact=get_timestamp_from_date($frm->fd4_factday,$frm->fd4_factmonth,$frm->fd4_factyear);

 if ($frm->fd5_factday!=0 &&  $frm->fd5_factmonth !=0 && $frm->fd5_factyear !=0)
	$frm->fd5_fact=get_timestamp_from_date($frm->fd5_factday,$frm->fd5_factmonth,$frm->fd5_factyear);

 if ($frm->fd6_factday!=0 &&  $frm->fd6_factmonth !=0 && $frm->fd6_factyear !=0)
	$frm->fd6_fact=get_timestamp_from_date($frm->fd6_factday,$frm->fd6_factmonth,$frm->fd6_factyear);

 if ($frm->fd7_factday!=0 &&  $frm->fd7_factmonth !=0 && $frm->fd7_factyear !=0)
	$frm->fd7_fact=get_timestamp_from_date($frm->fd7_factday,$frm->fd7_factmonth,$frm->fd7_factyear);

 if ($frm->fd8_factday!=0 &&  $frm->fd8_factmonth !=0 && $frm->fd8_factyear !=0)
	$frm->fd8_fact=get_timestamp_from_date($frm->fd8_factday,$frm->fd8_factmonth,$frm->fd8_factyear);

 if ($frm->fd8_0factday!=0 &&  $frm->fd8_0factmonth !=0 && $frm->fd8_0factyear !=0)
	$frm->fd8_0fact=get_timestamp_from_date($frm->fd8_0factday,$frm->fd8_0factmonth,$frm->fd8_0factyear);

 if ($frm->fd8_1factday!=0 &&  $frm->fd8_1factmonth !=0 && $frm->fd8_1factyear !=0)
	$frm->fd8_1fact=get_timestamp_from_date($frm->fd8_1factday,$frm->fd8_1factmonth,$frm->fd8_1factyear);

 if ($frm->fd8_2factday!=0 &&  $frm->fd8_2factmonth !=0 && $frm->fd8_2factyear !=0)
	$frm->fd8_2fact=get_timestamp_from_date($frm->fd8_2factday,$frm->fd8_2factmonth,$frm->fd8_2factyear);

 if ($frm->fd9_factday!=0 &&  $frm->fd9_factmonth !=0 && $frm->fd9_factyear !=0)
	$frm->fd9_fact=get_timestamp_from_date($frm->fd9_factday,$frm->fd9_factmonth,$frm->fd9_factyear);

 if ($frm->fd9_0factday!=0 &&  $frm->fd9_0factmonth !=0 && $frm->fd9_0factyear !=0)
	$frm->fd9_0fact=get_timestamp_from_date($frm->fd9_0factday,$frm->fd9_0factmonth,$frm->fd9_0factyear);

 if ($frm->fd9_1factday!=0 &&  $frm->fd9_1factmonth !=0 && $frm->fd9_1factyear !=0)
	$frm->fd9_1fact=get_timestamp_from_date($frm->fd9_1factday,$frm->fd9_1factmonth,$frm->fd9_1factyear);

 if ($frm->fd9_2factday!=0 &&  $frm->fd9_2factmonth !=0 && $frm->fd9_2factyear !=0)
	$frm->fd9_2fact=get_timestamp_from_date($frm->fd9_2factday,$frm->fd9_2factmonth,$frm->fd9_2factyear);

 if ($frm->fd10_factday!=0 &&  $frm->fd10_factmonth !=0 && $frm->fd10_factyear !=0)
	$frm->fd10_fact=get_timestamp_from_date($frm->fd10_factday,$frm->fd10_factmonth,$frm->fd10_factyear);

 if ($frm->fd11_factday!=0 &&  $frm->fd11_factmonth !=0 && $frm->fd11_factyear !=0)
	$frm->fd11_fact=get_timestamp_from_date($frm->fd11_factday,$frm->fd11_factmonth,$frm->fd11_factyear);

 if ($frm->fd12_factday!=0 &&  $frm->fd12_factmonth !=0 && $frm->fd12_factyear !=0)
	$frm->fd12_fact=get_timestamp_from_date($frm->fd12_factday,$frm->fd12_factmonth,$frm->fd12_factyear);

 if ($frm->fd13_factday!=0 &&  $frm->fd13_factmonth !=0 && $frm->fd13_factyear !=0)
	$frm->fd13_fact=get_timestamp_from_date($frm->fd13_factday,$frm->fd13_factmonth,$frm->fd13_factyear);

 if ($frm->fd14_factday!=0 &&  $frm->fd14_factmonth !=0 && $frm->fd14_factyear !=0)
	$frm->fd14_fact=get_timestamp_from_date($frm->fd14_factday,$frm->fd14_factmonth,$frm->fd14_factyear);

 if ($frm->fd15_factday!=0 &&  $frm->fd15_factmonth !=0 && $frm->fd15_factyear !=0)
	$frm->fd15_fact=get_timestamp_from_date($frm->fd15_factday,$frm->fd15_factmonth,$frm->fd15_factyear);

 if ($frm->fd16_factday!=0 &&  $frm->fd16_factmonth !=0 && $frm->fd16_factyear !=0)
	$frm->fd16_fact=get_timestamp_from_date($frm->fd16_factday,$frm->fd16_factmonth,$frm->fd16_factyear);

 if ($frm->fd17_factday!=0 &&  $frm->fd17_factmonth !=0 && $frm->fd17_factyear !=0)
	$frm->fd17_fact=get_timestamp_from_date($frm->fd17_factday,$frm->fd17_factmonth,$frm->fd17_factyear);

}



function fill_record($frm, &$rec)
{
if (isset($frm->fd1_fact))$rec->fd1_= $frm->fd1_fact. '|' . $frm->fd1_rekv. '|' . $frm->fd1_link;
if (isset($frm->fd2_fact))$rec->fd2_= $frm->fd2_fact. '|' . $frm->fd2_rekv. '|' . $frm->fd2_link;
if (isset($frm->fd3_fact))$rec->fd3_= $frm->fd3_fact. '|' . $frm->fd3_rekv. '|' . $frm->fd3_link;
if (isset($frm->fd4_fact))$rec->fd4_= $frm->fd4_fact. '|' . $frm->fd4_rekv. '|' . $frm->fd4_link;
if (isset($frm->fd5_fact))$rec->fd5_= $frm->fd5_fact. '|' . $frm->fd5_rekv. '|' . $frm->fd5_link;
if (isset($frm->fd6_fact))$rec->fd6_= $frm->fd6_fact. '|' . $frm->fd6_rekv. '|' . $frm->fd6_link;
if (isset($frm->fd7_fact))$rec->fd7_= $frm->fd7_fact. '|' . $frm->fd7_rekv. '|' . $frm->fd7_link;
if (isset($frm->fd8_fact))$rec->fd8_= $frm->fd8_fact. '|' . $frm->fd8_rekv. '|' . $frm->fd8_link;
if (isset($frm->fd8_0fact))$rec->fd8_0= $frm->fd8_0fact. '|' . $frm->fd8_0rekv. '|' . $frm->fd8_0link;
if (isset($frm->fd8_1fact))$rec->fd8_1= $frm->fd8_1fact. '|' . $frm->fd8_1rekv. '|' . $frm->fd8_1link;
if (isset($frm->fd8_2fact))$rec->fd8_2= $frm->fd8_2fact. '|' . $frm->fd8_2rekv. '|' . $frm->fd8_2link;
if (isset($frm->fd9_fact))$rec->fd9_= $frm->fd9_fact. '|' . $frm->fd9_rekv. '|' . $frm->fd9_link;
if (isset($frm->fd9_0fact))$rec->fd9_0= $frm->fd9_0fact. '|' . $frm->fd9_0rekv. '|' . $frm->fd9_0link;
if (isset($frm->fd9_1fact))$rec->fd9_1= $frm->fd9_1fact. '|' . $frm->fd9_1rekv. '|' . $frm->fd9_1link;
if (isset($frm->fd9_2fact))$rec->fd9_2= $frm->fd9_2fact. '|' . $frm->fd9_2rekv. '|' . $frm->fd9_2link;
if (isset($frm->fd10_fact))$rec->fd10_= $frm->fd10_fact. '|' . $frm->fd10_rekv. '|' . $frm->fd10_link;
if (isset($frm->fd11_fact))$rec->fd11_= $frm->fd11_fact. '|' . $frm->fd11_rekv. '|' . $frm->fd11_link;
if (isset($frm->fd12_fact))$rec->fd12_= $frm->fd12_fact. '|' . $frm->fd12_rekv. '|' . $frm->fd12_link;
if (isset($frm->fd13_fact))$rec->fd13_= $frm->fd13_fact. '|' . $frm->fd13_rekv. '|' . $frm->fd13_link;
if (isset($frm->fd14_fact))$rec->fd14_= $frm->fd14_fact. '|' . $frm->fd14_rekv. '|' . $frm->fd14_link;
if (isset($frm->fd15_fact))$rec->fd15_= $frm->fd15_fact. '|' . $frm->fd15_rekv. '|' . $frm->fd15_link;
if (isset($frm->fd16_fact))$rec->fd16_= $frm->fd16_fact. '|' . $frm->fd16_rekv. '|' . $frm->fd16_link;
if (isset($frm->fd17_fact))$rec->fd17_= $frm->fd17_fact. '|' . $frm->fd17_rekv. '|' . $frm->fd17_link;
}

function fill_frm_list(&$frm, $rec)
{
    if (isset($rec->fd1_) && !empty($rec->fd1_)) list($frm->fd1_fact,$frm->fd1_rekv,$frm->fd1_link) = explode("|", $rec->fd1_);
	if (isset($rec->fd2_) && !empty($rec->fd2_)) list($frm->fd2_fact,$frm->fd2_rekv,$frm->fd2_link) = explode("|", $rec->fd2_);
	if (isset($rec->fd3_) && !empty($rec->fd3_)) list($frm->fd3_fact,$frm->fd3_rekv,$frm->fd3_link) = explode("|", $rec->fd3_);
	if (isset($rec->fd4_) && !empty($rec->fd4_)) list($frm->fd4_fact,$frm->fd4_rekv,$frm->fd4_link) = explode("|", $rec->fd4_);
	if (isset($rec->fd5_) && !empty($rec->fd5_)) list($frm->fd5_fact,$frm->fd5_rekv,$frm->fd5_link) = explode("|", $rec->fd5_);
	if (isset($rec->fd6_) && !empty($rec->fd6_)) list($frm->fd6_fact,$frm->fd6_rekv,$frm->fd6_link) = explode("|", $rec->fd6_);
	if (isset($rec->fd7_) && !empty($rec->fd7_)) list($frm->fd7_fact,$frm->fd7_rekv,$frm->fd7_link) = explode("|", $rec->fd7_);
	if (isset($rec->fd8_) && !empty($rec->fd8_)) list($frm->fd8_fact,$frm->fd8_rekv,$frm->fd8_link) = explode("|", $rec->fd8_);
	if (isset($rec->fd8_0) && !empty($rec->fd8_0)) list($frm->fd8_0fact,$frm->fd8_0rekv,$frm->fd8_0link) = explode("|", $rec->fd8_0);
	if (isset($rec->fd8_1) && !empty($rec->fd8_1)) list($frm->fd8_1fact,$frm->fd8_1rekv,$frm->fd8_1link) = explode("|", $rec->fd8_1);
	if (isset($rec->fd8_2) && !empty($rec->fd8_2)) list($frm->fd8_2fact,$frm->fd8_2rekv,$frm->fd8_2link) = explode("|", $rec->fd8_2);
	if (isset($rec->fd9_) && !empty($rec->fd9_)) list($frm->fd9_fact,$frm->fd9_rekv,$frm->fd9_link) = explode("|", $rec->fd9_);
	if (isset($rec->fd9_0) && !empty($rec->fd9_0)) list($frm->fd9_0fact,$frm->fd9_0rekv,$frm->fd9_0link) = explode("|", $rec->fd9_0);
	if (isset($rec->fd9_1) && !empty($rec->fd9_1)) list($frm->fd9_1fact,$frm->fd9_1rekv,$frm->fd9_1link) = explode("|", $rec->fd9_1);
	if (isset($rec->fd9_2) && !empty($rec->fd9_2)) list($frm->fd9_2fact,$frm->fd9_2rekv,$frm->fd9_2link) = explode("|", $rec->fd9_2);
	if (isset($rec->fd10_) && !empty($rec->fd10_)) list($frm->fd10_fact,$frm->fd10_rekv,$frm->fd10_link) = explode("|", $rec->fd10_);
	if (isset($rec->fd11_) && !empty($rec->fd11_)) list($frm->fd11_fact,$frm->fd11_rekv,$frm->fd11_link) = explode("|", $rec->fd11_);
	if (isset($rec->fd12_) && !empty($rec->fd12_)) list($frm->fd12_fact,$frm->fd12_rekv,$frm->fd12_link) = explode("|", $rec->fd12_);
	if (isset($rec->fd13_) && !empty($rec->fd13_)) list($frm->fd13_fact,$frm->fd13_rekv,$frm->fd13_link) = explode("|", $rec->fd13_);
	if (isset($rec->fd14_) && !empty($rec->fd14_)) list($frm->fd14_fact,$frm->fd14_rekv,$frm->fd14_link) = explode("|", $rec->fd14_);
	if (isset($rec->fd15_) && !empty($rec->fd15_)) list($frm->fd15_fact,$frm->fd15_rekv,$frm->fd15_link) = explode("|", $rec->fd15_);
	if (isset($rec->fd16_) && !empty($rec->fd16_)) list($frm->fd16_fact,$frm->fd16_rekv,$frm->fd16_link) = explode("|", $rec->fd16_);
	if (isset($rec->fd17_) && !empty($rec->fd17_)) list($frm->fd17_fact,$frm->fd17_rekv,$frm->fd17_link) = explode("|", $rec->fd17_);
}
// Find input error in forms
function find_errors_in_links(&$rec, &$err)
{
	global $CFG, $db;

    if (isset($rec->fd_u_1_link))	{
	  // $a = get_headers($rec->fd_u_1_link);
       // $a=check_url($rec->fd_u_1_link);
      // $a=check_url_file($rec->fd_u_1_link);
      //print $a;
      // if (!$a)      $err['fd_u_1_link']=0;
      // else print_r($a);
    }

    if (isset($rec->fd_u_2_link))	{

    }
    // return count($err);
    return 0;
}


?>


