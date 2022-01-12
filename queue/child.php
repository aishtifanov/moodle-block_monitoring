<?php // $Id: child.php,v 1.3 2012/06/21 09:05:18 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    require_once('edit_child_form.php');
    
    require_login();

    $rid = optional_param('rid', 0, PARAM_INT);    // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);    // OU id
    $yid = optional_param('yid', 0, PARAM_INT);    // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $tab = optional_param('tab', 'queue');          // Rayon id
    $level = optional_param('level', 'ou');          // Rayon id
    $id = optional_param('id', 0, PARAM_INT);    // Request id
    $statusid = optional_param('status', 9, PARAM_INT);    // Request id
    $confirm = optional_param('confirm');

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strrequest, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    include('tabs.php');

    if (isoperatorinanyou($USER->id, true)) {

        if ($action == 'edit')  {
            if ($child = get_record_select('monit_queue_child', "id = $id"))  {
                
                $edutype = get_record_select('monit_school_type', "cod = '$typeou'", 'id');
                $strsql = "SELECT * FROM {$CFG->prefix}monit_queue_request 
                           WHERE childid = $id AND edutypeid = {$edutype->id} AND oid = $oid AND deleted=0"; 
                $request = get_record_sql($strsql);

                $redirlink = "queue.php?level=$level&typeou=$typeou&oid=$oid&yid=$yid&rid=$rid";
                        
                $editform = new edit_child_form();
                    
                if ($editform->is_cancelled()) {
                    redirect($redirlink, '', 0);
                } else if ($frm = $editform->get_data()) {
                    
                    $frm->id = $id;
                    $frm->when_hands = date('Y-m-d', $frm->when_hands); 
                    $frm->timemodified = time();
                    if (!update_record('monit_queue_child', $frm))  {
                        error('Ошибка при сохранении данных ребенка.', $redirlink);
                    }
                    
                    if ($request)   {
                        $request1->id = $request->id;
                        $request1->healthid = $frm->healthid;
                        $request1->dateenrollment = $frm->dateenrollment;
                        
                        $benefits = get_records('monit_queue_benefit');
                        $abenefits = array(0);
                        foreach ($benefits as $benefit) {
                            $fname = 'b_'.$benefit->id;
                            if (isset($frm->{$fname}))  {
                                $abenefits[] = $benefit->id;
                            }    
                        }    
                        $request1->benefitsids = implode (',', $abenefits);
                        
                        if ($request->benefitsids == '0' && $request1->benefitsids != '0' && $request->status == STATUS_PUTINTOQUEUE)  {
                            $request1->number = 0;
                            $request1->status = 9; 
                        }
                        
                        // $request1->birthyear = date('Y', $frm->birthday);
                        $request1->birthyear =  get_birthyear_child($frm->birthday);
                        if (!update_record('monit_queue_request', $request1))  {
                            error('Ошибка при сохранении дополнительной информации.', $redirlink);
                        }
                        
                        if ($request->benefitsids == '0' && $request1->benefitsids != '0' && $request->status == STATUS_PUTINTOQUEUE)  {
                            change_status (STATUS_PUTINTOQUEUE, $request->id);
                        }    
                    }
                    // echo '<pre>'; print_r($frm); echo '</pre>'; 
                    redirect($redirlink, 'Данные ребенка успешно обновлены.', 1);
                 }
                 $editform->display();       
            }
        }          
    }    
    
    print_footer();

?>
