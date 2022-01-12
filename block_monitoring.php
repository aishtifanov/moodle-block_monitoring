<?php // $Id: block_monitoring.php,v 1.32 2012/08/22 08:52:29 shtifanov Exp $

class block_monitoring	extends block_list
{
    function init() {
        $this->title = get_string('title', 'block_monitoring');
        $this->version = 2007102000;
    }


    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        if (isloggedin() and !isguest() and isset($CFG->frontpageloggedin)) {
            $this->content->footer = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/index.php">'.get_string('title', 'block_monitoring').'</a>'.' ...';
        }    

        if (empty($this->instance)) {
            $this->content = '';
        } else {
            $this->load_content();
        }

        return $this->content;
        }

    function load_content() {
        global $CFG;

		$admin_is = isadmin();
		$staff_operator_is = ismonitoperator('staff');
		$region_operator_is = ismonitoperator('region');
        $region_operator_zp = ismonitoperator('regionzp');
		$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
        $rayon_operator_zp  = ismonitoperator('rayonzp', 0, 0, 0, true);
		if  (!$admin_is && !$region_operator_is && $rayon_operator_is) 	{
			$rid = $rayon_operator_is;
		}	else {
			$rid = 0;
		}
		$sid = ismonitoperator('school', 0, 0, 0, true);
		$college_operator_is = ismonitoperator('college', 0, 0, 0, true);

		if ($admin_is || $staff_operator_is || $rayon_operator_is)	 {
			/*
 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/school/accreditation.php?rid=$rid\">".get_string('accreditation', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/tick_amber_big.gif" height="16" width="16" alt="" />';
  	       */
	   }

		if ($admin_is || $region_operator_is)	 {
 	       $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/region/region.php">'.get_string('region', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/db.gif" height="16" width="16" alt="" />';
        }

		if ($admin_is || $region_operator_zp || $rayon_operator_zp)	 {
	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/zp/listforms.php?rid=$rid\">".get_string('zp', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/monitoring/i/percent.png" height="16" width="16" alt="" />';		   
        }

	   if ($admin_is || $region_operator_is || $rayon_operator_is)  {

	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/course/view.php?id=100\">Вопросы рейтингования</a>";
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/course.gif" height="16" width="16" alt="" />';		   

	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/rating/listforms.php?rid=$rid\">".get_string('rating', 'block_monitoring').'</a>';
		   // $this->content->icons[]='<img src="'.$CFG->pixpath.'/c/stats.gif" height="16" width="16" alt="" />';
  	       $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/monitoring/i/percent.png" height="16" width="16" alt="" />';		   


 	       $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/rayon/rayons.php">'.get_string('rayons', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/mod/wiki/icon.gif" height="16" width="16" alt="" />';

 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/school/schools.php?rid=$rid\">".get_string('schools', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';

 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/college/college.php?rid=$rid\">".get_string('colleges', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';

	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/school/journals.php\">".get_string('journalreports', 'block_monitoring').'</a>';
		   $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/monitoring/i/journal.gif" height="16" width="16" alt="" />';

	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/rayon/sumreports.php?rid=$rid\">".get_string('summaryreports', 'block_monitoring').'</a>';
		   $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/report.gif" height="16" width="16" alt="" />';

	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/region/stats.php?rid=$rid\">".get_string('stats', 'block_monitoring').'</a>';
		   $this->content->icons[]='<img src="'.$CFG->pixpath.'/c/stats.gif" height="16" width="16" alt="" />';

 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/users/operators.php?rid=$rid\">".get_string('operators', 'block_monitoring').'</a>';
  	       $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/monitoring/i/curators.gif" height="16" width="16" alt="" />';

	    }

		if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $sid) {
	       if ($school = get_record('monit_school', 'id', $sid)) {
	       	
	       	   $eduyear = get_records('monit_years', '', '', 'id', 'id');
	       	   $lastyear = end ($eduyear);
	       	   $yid = $lastyear->id;
	       	   $schoollastyear =  get_record('monit_school', 'uniqueconstcode', $sid, 'yearid', $yid);
		       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/rating/listforms.php?rid={$school->rayonid}&amp;sid={$schoollastyear->id}\">".get_string('rating', 'block_monitoring').'</a>';
			   $this->content->icons[]='<img src="'.$CFG->pixpath.'/c/stats.gif" height="16" width="16" alt="" />';

	       	/*
	 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/school/accreditation.php?rid={$school->rayonid}&amp;sid=$sid\">".get_string('accreditation', 'block_monitoring').'</a>';
	  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/tick_amber_big.gif" height="16" width="16" alt="" />';
*/
	 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/school/schools.php?rid={$school->rayonid}&amp;sid=$sid\">".get_string('school', 'block_monitoring').'</a>';
 	 	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';
 	 	       
	         $this->content->items[] = '<a href="'.$CFG->wwwroot."/course/view.php?id=100\">Вопросы рейтингования</a>";
  	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/course.gif" height="16" width="16" alt="" />';		   

 	 	   }
	    }

		if (!$admin_is && !$staff_operator_is && !$rayon_operator_is && $college_operator_is)  {
	       if ($college = get_record('monit_college', 'id', $college_operator_is))	 {
	 	       $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/monitoring/college/college.php?rid={$college->rayonid}&amp;sid={$college->id}\">".get_string('colleges', 'block_monitoring').'</a>';
 	 	       $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';
           }
        }

		if ($admin_is || $region_operator_is)	 {
	       $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/form/forms.php">'.get_string('forms', 'block_monitoring').'</a>';
	 	   $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/settings.gif" height="16" width="16" alt="" />';
        }

        if (isloggedin() and !isguest() and isset($CFG->frontpageloggedin)) {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/indices/indices.php">'.get_string('indices', 'block_monitoring').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/news.gif" height="16" width="16" alt="" />';
        }    
        
        if ($admin_is || $region_operator_is || $rayon_operator_is || $sid)  {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/importqueue.php">'.get_string('importqueue', 'block_monitoring').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/restore.gif" height="16" width="16" alt="" />';
            
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/queue.php">'.get_string('queue', 'block_monitoring').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/report.gif" height="16" width="16" alt="" />';

            // $this->content->items[] = '';
      	    // $this->content->icons[] = '';

        }    

        
/*
	    $this->content->items[] = '<a href="'.$CFG->wwwroot.'/file.php/1/instruction_queue.pdf">'.get_string('instrqueue', 'block_monitoring').'</a>';
	    $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/info.gif" height="16" width="16" alt="" />';
        

        $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/monitoring/queue/index.php">'.get_string('requestshort', 'block_monitoring').'</a>';
  	    $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/monitoring/i/child.gif" height="16" width="16" alt="" />';
*/                

    }


    function instance_allow_config() {
        return false;
    }


    function specialization() {
        $this->title =  get_string('title', 'block_monitoring');
    }
}


?>
