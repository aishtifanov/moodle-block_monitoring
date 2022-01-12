<?php // $Id: editgroup.php,v 1.1 2012/05/28 06:20:30 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    require_once($CFG->libdir.'/formslib.php');


class edit_queue_group_form extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $typeou, $oid, $age;
        
        $mform =& $this->_form;

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $mform->addElement('hidden', 'typeou', $typeou);  $mform->setType('typeou', PARAM_TEXT);
        $mform->addElement('hidden', 'oid', $oid);  $mform->setType('oid', PARAM_INT);
        $mform->addElement('hidden', 'age', $age);  $mform->setType('age', PARAM_INT);        
        
        $mform->addElement('header','', get_string('ou1', 'block_mou_att'));
      
        if($rayon = get_record_sql("SELECT id, shortname FROM {$CFG->prefix}monit_rayon WHERE id=$rid"))   {
             $mform->addElement('static', '', get_string('rayon', 'block_monitoring'), $rayon->shortname);
        }     
       
        $outype = get_config_typeou($typeou);
        $mform->addElement('static', '', get_string('typeou', 'block_mou_att'), $outype->name);
        
        $strsql = "SELECT id, name  FROM {$CFG->prefix}{$outype->tblname}
                    WHERE id=$oid";
        if ($ou =  get_record_sql($strsql))	{
            $mform->addElement('static', '', $outype->strtitle, $ou->name);
        }    
        
        $mform->addElement('header','', 'Комплектование группы');

     	$yearmenu = array();
    	if ($years = get_records_select ("monit_queue_birthyear", "rayonid=1", 'id'))  {
        	foreach ($years as $year)	{
        	   $numrequest = count_records_select('monit_queue_request', "edutypeid = {$outype->id} AND oid = $oid AND birthyear=$year->id");
               $mform->addElement('hidden', 'numrequest', $numrequest);  $mform->setType('numrequest', PARAM_INT);
    	       $yearmenu[$year->id] = $year->name. ' г.р. (' . $numrequest . ')' ;
    	    }
    	}
        $mform->addElement('select', 'birthyear', 'Год рождения детей (кол-во заявлений)', $yearmenu);
        $mform->addRule('birthyear', get_string('missingname'), 'required', null, 'client');
        	    
        
        /*
        $birthyear = '-';
        if ($year = get_record_select ("monit_queue_birthyear", "rayonid=1 AND id=$age", 'id, name'))  {
                $birthyear = $year->name. ' г.р.';
        }    
        $mform->addElement('static', '', 'Год рождения детей', $birthyear);
        */

       
        $mform->addElement('text', 'name', 'Наименование группы', 'maxlength="30" size="30"');
        $mform->addRule('name', get_string('missingname'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        
        /*
        $mform->addElement('text', 'numchild', 'Количество детей в группе', 'maxlength="4" size="4"');
        $mform->addRule('numchild', get_string('missingname'), 'required', null, 'client');
        $mform->setType('numchild', PARAM_INT);
        */

        $mform->addElement('text', 'numfromqueue', 'Количество зачисляемых детей из очереди', 'maxlength="4" size="4"');
        $mform->addRule('numfromqueue', get_string('missingname'), 'required', null, 'client');
        $mform->setType('numfromqueue', PARAM_INT);

        $mform->addElement('text', 'percent', 'Процент льготников', 'maxlength="4" size="4"');
        $mform->addRule('percent', get_string('missingname'), 'required', null, 'client');
        $mform->setType('percent', PARAM_INT);

        $this->add_action_buttons(true, 'Сформировать группу');
    }
    
    function validation($data) {
       
        $errors = array();
        
        /*
        $outype = get_config_typeou($data['typeou']);
        $cnt = count_records_select('monit_queue_request', "edutypeid = {$outype->id} AND oid = $oid AND birthyear=$year->id");
        echo '<pre>'; print_r($data); echo '</pre>';
        */
        // echo $data['isadress'];
        if ($data['numfromqueue'] == 0)  {
            $errors['numfromqueue'] = get_string('missingname'); 
        }
        
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }
    

}

    
    require_login();

    $rid = required_param('rid', PARAM_INT);    // Rayon id
    $oid = required_param('oid', PARAM_INT);    // OU id
    $yid = required_param('yid', PARAM_INT);    // Year id
    $age = required_param('age', PARAM_INT);    // Year id
    $typeou = required_param('typeou');       // Type OU
    $tab = optional_param('tab', 'queue');          // Rayon id
	$action   = optional_param('action', 'new');
    $level = optional_param('level', 'group');          // Rayon id
    $id = optional_param('id', 0, PARAM_INT);    // Request id
    $statusid = optional_param('status', 9, PARAM_INT);    // Request id
    $confirm = optional_param('confirm');

    ignore_user_abort(false); // see bug report 5352. This should kill this thread as soon as user aborts.
    @set_time_limit(0);
    @ob_implicit_flush(true);
    @ob_end_flush();
    
    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = 'Создание новой группы';
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strrequest, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    include('tabs.php');

    if (isoperatorinanyou($USER->id, true)) {

        if ($action == 'new')  {

                $redirlink = "queue.php?level=$level&typeou=$typeou&oid=$oid&yid=$yid&rid=$rid";
                $editform = new edit_queue_group_form();
                    
                if ($editform->is_cancelled()) {
                    redirect($redirlink, '', 0);
                } else if ($frm = $editform->get_data()) {
                    $outype = get_config_typeou($typeou);
                    $frm->rayonid = $rid;
                    $frm->edutypeid = $outype->id; 
                    $frm->oid = $oid;
                    $frm->numchild = $frm->numfromqueue;
                    if (!$qgroupid = insert_record('monit_queue_group', $frm))  {
                        echo '<pre>'; print_r($frm); echo '</pre>';  
                        error('Ошибка при сохранении данных группы.', $redirlink);
                    } else {
                        $frm->queuegroupid = $qgroupid; 
                        enroll_into_qgroup($frm);
                    }
    
                    redirect($redirlink, 'Группа сохранена.', 3);
                 }
                 $editform->display();       
        }
    }    
    
    print_footer();


function enroll_into_qgroup($qgroup)
{
    // Количество льготников
    $numbenefit = round(($qgroup->numfromqueue * $qgroup->percent)/100, 0);
    $numover = $qgroup->numfromqueue - $numbenefit;
    // echo $numbenefit . '<hr>';
    // echo $numover . '<hr>';
    
    $rec->queuegroupid = $qgroup->queuegroupid;
    
    $reqbenefits = get_records_select('monit_queue_request', "edutypeid=$qgroup->edutypeid AND oid=$qgroup->oid AND benefitsids <> '0'  AND birthyear = $qgroup->birthyear AND number < " . NUMBER_DENIED);
    $i=1;
    foreach ($reqbenefits as $reqbenefit)   {
        if ($i <= $numbenefit)  {
            // print_r($reqbenefit); echo '<hr>';
            change_status (STATUS_SATISFIED, $reqbenefit->id);
            $rec->childid = $reqbenefit->childid;
            if (!insert_record('monit_queue_groupchild', $rec))  {
                echo '<pre>'; print_r($rec); echo '</pre>';  
                error('Ошибка при сохранении данных в groupchild.', $redirlink);
            } 
            $i++;
        } else {
            break;
        }
    }

    $requests = get_records_select('monit_queue_request', "edutypeid=$qgroup->edutypeid AND oid=$qgroup->oid AND benefitsids = '0'  AND birthyear = $qgroup->birthyear AND number < " . NUMBER_DENIED);
    $i=1;
    foreach ($requests as $request)   {
        if ($i <= $numover)  {
            // print_r($request); echo '<hr>';
            change_status (STATUS_SATISFIED, $request->id);
            $rec->childid = $request->childid;
            if (!insert_record('monit_queue_groupchild', $rec))  {
                echo '<pre>'; print_r($rec); echo '</pre>';  
                error('Ошибка при сохранении данных в groupchild.', $redirlink);
            } 
            $i++;
        } else {
            break;
        }
    }
       
    return true;
}

?>
