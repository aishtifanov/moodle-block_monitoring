<?php // $Id: index.php,v 1.13 2012/08/22 08:52:29 shtifanov Exp $

    require_once('../../config.php');
    require_once('lib.php');

   // require_login();

     
    if (!$site = get_site()) {
        redirect('index.php');
    }

    $strmonit = get_string('frontpagetitle', 'block_monitoring');

    print_header_mou("$site->shortname: $strmonit", $site->fullname, $strmonit);

    print_heading($strmonit);

    $table->align = array ('right', 'left');

	$admin_is = isadmin();
	$staff_operator_is = ismonitoperator('staff');
	$region_operator_is = ismonitoperator('region');
    $region_operator_zp = ismonitoperator('regionzp');    
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
    $rayon_operator_zp  = ismonitoperator('rayonzp', 0, 0, 0, true);
	$sid = ismonitoperator('school', 0, 0, 0, true);
	$college_operator_is = ismonitoperator('college', 0, 0, 0, true);

	if ($admin_is || $staff_operator_is || $rayon_operator_is)	 {
		/*
        $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/school/accreditation.php?\">".get_string('accreditation', 'block_monitoring').'</a></strong>',
	                           get_string('description_accreditation','block_monitoring'));
*/	                           
    }

	if ($admin_is || $region_operator_is)	 {
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/region/region.php">'.get_string('region', 'block_monitoring').'</a></strong>',
 	                          get_string('description_region','block_monitoring'));
    }

	if ($admin_is || $region_operator_zp || $rayon_operator_zp)	 {
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/zp/listforms.php?rid=$rayon_operator_is\">".get_string('zp', 'block_monitoring').'</a></strong>',
	                           get_string('description_zp','block_monitoring'));
    }

    if ($admin_is || $region_operator_is || $rayon_operator_is)  {
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/rating/listforms.php?rid=$rayon_operator_is\">".get_string('rating', 'block_monitoring').'</a></strong>',
	                           get_string('description_rating','block_monitoring'));


	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/rayon/rayons.php?rid=$rayon_operator_is\">".get_string('rayons', 'block_monitoring').'</a></strong>',
	                           get_string('description_rayon','block_monitoring'));
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/school/schools.php?rid=$rayon_operator_is\">".get_string('schools', 'block_monitoring').'</a></strong>',
	                           get_string('description_school','block_monitoring'));
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/college/college.php?rid=$rayon_operator_is\">".get_string('colleges', 'block_monitoring').'</a></strong>',
	                           get_string('description_college','block_monitoring'));
	    $table->data[] = array('<strong><a href='.$CFG->wwwroot.'/blocks/monitoring/school/journals.php>'.get_string('journalreports', 'block_monitoring').'</a></strong>',
 	                          get_string('journalreportsdescription','block_monitoring'));
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/rayon/sumreports.php?rid=$rayon_operator_is\">".get_string('summaryreports', 'block_monitoring').'</a></strong>',
 	                          get_string('sumreportsrayon','block_monitoring'));
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/region/stats.php?rid=$rayon_operator_is\">".get_string('statsreport', 'block_monitoring').'</a></strong>',
 	                          get_string('description_stats','block_monitoring'));
	    $table->data[] = array('<strong><a href='.$CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rayon_operator_is>".get_string('operators', 'block_monitoring').'</a></strong>',
 	                          get_string('description_operators','block_monitoring'));

	}

	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $sid) {
       if ($school = get_record('monit_school', 'id', $sid)) {
       	   $eduyear = get_records('monit_years', '', '', 'id', 'id');
       	   $lastyear = end ($eduyear);
       	   $yid = $lastyear->id;
       	   $schoollastyear =  get_record('monit_school', 'uniqueconstcode', $sid, 'yearid', $yid);
       	
       	/*
	        $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/school/accreditation.php?rid={$school->rayonid}&amp;sid=$sid\">".get_string('accreditation', 'block_monitoring').'</a></strong>',
		                           get_string('description_accreditation','block_monitoring'));
*/
//	       $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/rating/begindata.php?rid={$school->rayonid}&amp;sid=$sid\">".get_string('rating', 'block_monitoring').'</a></strong>',
//	                           get_string('description_rating','block_monitoring'));

		    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/rating/listforms.php?rid={$school->rayonid}&amp;sid={$schoollastyear->id}\">".get_string('rating', 'block_monitoring').'</a></strong>',
		                           get_string('description_rating','block_monitoring'));

	 	   $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/school/schools.php?rid={$school->rayonid}&amp;sid=$sid\">".get_string('school', 'block_monitoring').'</a></strong>',
	  	                         get_string('description_school_one','block_monitoring'));
 	   }
   }


	if (!$admin_is && !$staff_operator_is && !$rayon_operator_is && $college_operator_is)  {
	   if ($college = get_record('monit_college', 'id', $college_operator_is)) {
	    $table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/monitoring/college/college.php?rid={$college->rayonid}&amp;sid=$college_operator_is\">".get_string('colleges', 'block_monitoring').'</a></strong>',
	                           get_string('description_college','block_monitoring'));
 	   }
   }


	if ($admin_is || $region_operator_is)	 {
	    $table->data[] = array('<strong><a href='.$CFG->wwwroot.'/blocks/monitoring/form/forms.php>'.get_string('forms', 'block_monitoring').'</a></strong>',
 	                          get_string('workforms','block_monitoring'));
	    $table->data[] = array('<strong><a href='.$CFG->wwwroot.'/blocks/monitoring/options/options.php>'.get_string('options', 'block_monitoring').'</a></strong>',
 	                          get_string('optionsdesc','block_monitoring'));
   }

	if ($admin_is || $staff_operator_is || $rayon_operator_is || $college_operator_is || $sid)	 {

	   	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/file.php/1/instruction_mo.doc\">".get_string('instruction', 'block_monitoring').'</a></strong>',
	 	                          get_string('description_instruction', 'block_monitoring'));
	   
	}

   $table->data[] = array('<strong><a href='.$CFG->wwwroot.'/blocks/monitoring/indices/indices.php>'.get_string('indices', 'block_monitoring').'</a></strong>',
 	                          get_string('description_indices','block_monitoring'));


/*
   $table->data[] = array('<hr>', '<hr>');


   $table->data[] = array('<strong><a href="http://mou.bsu.edu.ru/file.php/1/instruction_queue.pdf">'.get_string('instrqueue', 'block_monitoring').'</a>',
  	                     get_string('description_instrqueue','block_monitoring'));        

   $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/info.php">'.get_string('infoaboutou', 'block_monitoring').'</a></strong>',
 	                          get_string('description_infoaboutou','block_monitoring'));


   $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/index.php">'.get_string('requestdeclare', 'block_monitoring').'</a></strong>',
 	                          get_string('description_requestdeclare','block_monitoring'));

   $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/my.php">'.get_string('myrequest', 'block_monitoring').'</a></strong>',
 	                          get_string('description_myrequest','block_monitoring'));

*/
	if ($admin_is || $staff_operator_is || $rayon_operator_is || $college_operator_is || $sid)	 {

	   /*
        $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/queue.php">'.get_string('queue', 'block_monitoring').'</a></strong>',
 	                          get_string('description_queue','block_monitoring'));

        $table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/declarants.php">'.get_string('declarants', 'block_monitoring').'</a></strong>',
 	                          get_string('description_declarants','block_monitoring'));
                              
         $table->data[] = array('<strong><a href="http://mou.bsu.edu.ru/file.php/1/instruction_queue_operator.pdf">'.get_string('instroper', 'block_monitoring').'</a>',
  	                     get_string('instroper','block_monitoring'));
        */                                   
                              
	}


    print_table($table);
    // print_color_table($table);

    print_footer($site);

?>


