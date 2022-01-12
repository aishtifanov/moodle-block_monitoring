<?php  // $Id: lib_queue.php,v 1.27 2012/10/25 09:50:35 shtifanov Exp $

/*
TRUNCATE TABLE mdl_monit_queue_declarant;
TRUNCATE TABLE mdl_monit_queue_child;
TRUNCATE TABLE mdl_monit_queue_request;
delete from mdl_role_assignments where userid=177183;
DELETE FROM mdl_context where contextlevel=1070;

update mdl_monit_queue_request set benefitsids=0, status=9, number=0, numberinyear=0
where rayonid=1 and edutypeid=1

ALTER TABLE `mou`.`mdl_monit_queue_request` ADD COLUMN `birthyear` SMALLINT UNSIGNED DEFAULT 2000 AFTER `reason`;
ALTER TABLE `mou`.`mdl_monit_queue_request` ADD COLUMN `numberinyear` SMALLINT UNSIGNED DEFAULT 0 AFTER `birthyear`;
*/

require_once($CFG->libdir.'/formslib.php');

define('STATUS_DENIED', 11);
define('STATUS_PUTINTOQUEUE', 14);
define('STATUS_SATISFIED', 15);
define('NUMBER_DENIED', 999999);
define('NUMBER_SATISFIED', 1000000);

class pupil_form extends moodleform 
{
    function definition() {

        global $CFG, $rid, $who, $useridd, $outype, $oid, $yid;

        $mform =& $this->_form;
       
        if ($rid != 0 && $oid < 0)  {

            $mform->addElement('header','', get_string('ou1', 'block_mou_att'));
          
            $schoolmenu = array();
            $schoolmenu[0] = $outype->strselect;
        

            $strsql = "SELECT id, rayonid, name  FROM {$CFG->prefix}{$outype->tblname}
            			WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid {$outype->where} 
            			ORDER BY number";                    
            if ($schools =  get_records_sql($strsql))	{
            	foreach ($schools as $school) {
            	    $schoolname = mb_ereg_replace('дошкольное образовательное учреждение', 'ДОУ', $school->name);
                    if ($schoolname)  $school->name = $schoolname;
            	   
            		$len = mb_strlen ($school->name);
            		if ($len > 100)  {
            			// $school->name = substr($school->name, 0, 200) . ' ...';
            			$school->name = mb_substr($school->name, 0,  100, 'UTF-8') . ' ...';
            		}
            		$schoolmenu[$school->id] =$school->name;
            	}
            }
            $mform->addElement('select', 'dou1', get_string('dou1', 'block_monitoring'), $schoolmenu);
            $mform->addRule('dou1', get_string('missingname'), 'required', null, 'client');
            $mform->setDefault('dou1', 0);
            // unset($data1->dou1);

            $mform->addElement('select', 'dou2', get_string('dou2', 'block_monitoring'), $schoolmenu);
            $mform->setDefault('dou2', 0);
            // unset($data1->dou2);

            $mform->addElement('select', 'dou3', get_string('dou3', 'block_monitoring'), $schoolmenu);
            $mform->setDefault('dou3', 0);
            // unset($data1->dou3);
        }

        $mform->addElement('header','', get_string('childata', 'block_monitoring'));
        
        $mform->addElement('text', 'lastname1', get_string('lastname'), 'maxlength="30" size="30"');
        $mform->addRule('lastname1', get_string('missingname'), 'required', null, 'client');
        $mform->setType('lastname1', PARAM_TEXT);

        $mform->addElement('text', 'firstname1', get_string('firstname'), 'maxlength="30" size="30"');
        $mform->addRule('firstname1', get_string('missingname'), 'required', null, 'client');
        $mform->setType('firstname1', PARAM_TEXT);

        $mform->addElement('text', 'secondname1', get_string('secondname', 'block_monitoring'), 'maxlength="30" size="30"');
        $mform->addRule('secondname1', get_string('missingname'), 'required', null, 'client');
        $mform->setType('secondname1', PARAM_TEXT);

        $choices = array();
        $choices['1'] = get_string('pol1', 'block_mou_school');
        $choices['2'] = get_string('pol2', 'block_mou_school');
        $mform->addElement('select', 'pol', get_string('pol', 'block_mou_ege'), $choices);
        $mform->addRule('pol', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('pol', 1);

		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 18;
		$mform->addElement('date_selector', 'birthday1', get_string('birthday', 'block_mou_school'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		$mform->addRule('birthday1', get_string('missingname'), 'required', null, 'client');
		// $mform->setType('birthday', PARAM_INT);
		$mform->setDefault('birthday1', time() - YEARSECS*9); 

        $mform->addElement('static', '', '');		
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('select', 'document1', get_string('typedocuments', 'block_mou_ege'), $choices);
        $mform->addRule('document1', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('document1', 2);

        $mform->addElement('text', 'serial1', get_string('serial', 'block_mou_ege'), 'maxlength="6" size="6"');
        $mform->addRule('serial1', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('serial1', PARAM_TEXT);

        $mform->addElement('text', 'number1', get_string('number', 'block_mou_ege'), 'maxlength="10" size="10"');
        $mform->addRule('number1', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('number1', PARAM_TEXT);
        
		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 18;
		$mform->addElement('date_selector', 'when_hands1', get_string('when_hands', 'block_mou_ege'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		$mform->addRule('when_hands1', get_string('missingname'), 'required', null, 'client');
		// $mform->setType('birthday', PARAM_INT);
		$mform->setDefault('when_hands1', time() - YEARSECS*9); 

        $mform->addElement('text', 'who_hands1', get_string('who_hands', 'block_mou_ege'), 'maxlength="100" size="70"');
        $mform->addRule('who_hands1', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('who_hands1', PARAM_TEXT);

        
        // $mform->addElement('header','', get_string('schooltab5', 'block_monitoring'));
        $mform->addElement('static', '', '');
        
 		$mform->addElement('textarea', 'addressregistration', get_string('regaddress', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        $mform->addRule('addressregistration', '', 'required', null, 'client');

        $mform->addElement('checkbox', 'isregequalhome', get_string('isregequalhome', 'block_monitoring'));
        
 		$mform->addElement('textarea', 'addresshome', get_string('addresshome', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        
        $mform->addElement('checkbox', 'isconfirmpersonaldata', get_string('isconfirmpersonaldata', 'block_monitoring'));
        
        /*
        $mform->addElement('header','', get_string('ou1', 'block_mou_att'));

        $rayonmenu = array();
        $rayonmenu[0] = get_string('selectarayon', 'block_monitoring').'...';
        
        if($allrayons = get_records_sql("SELECT id, shortname FROM {$CFG->prefix}monit_rayon ORDER BY number"))   {
            foreach ($allrayons as $rayon) 	{
                $rayonmenu[$rayon->id] = $rayon->shortname;
            }
        }
        $mform->addElement('select', 'rayonid', get_string('rayon', 'block_monitoring'), $rayonmenu);
        // $mform->setHelpButton('hiddensections', array('coursehiddensections', get_string('hiddensections')), true);
        $mform->addRule('rayonid', $rayonmenu[0], 'required', null, 'client');
        // $mform->setDefault('rayonid', $rid);
        $mform->setDefault('rayonid', 1);

        
        $listedutypeids = '18, 1, 17, 15, 16';
        $edutypes = get_records_select ('monit_school_type', "id in ($listedutypeids)", 'id, name'); 
        $choices = array();
        foreach ($edutypes as $edutype) {
            $choices[$edutype->id] = $edutype->name;
        }    
        $mform->addElement('select', 'edutypeid', get_string('typeou', 'block_mou_att'), $choices);
        // $mform->setHelpButton('hiddensections', array('coursehiddensections', get_string('hiddensections')), true);
        $mform->addRule('edutypeid', get_string('missingname'), 'required', null, 'client');
        // $mform->setDefault('edutypeid', 18);
        $mform->setDefault('edutypeid', 1);
        */
        $mform->addElement('hidden', 'rayonid', $rid);  $mform->setType('rid', PARAM_INT);
        $mform->addElement('hidden', 'rid', $rid);  $mform->setType('rayonid', PARAM_INT);
        $mform->addElement('hidden', 'edutypeid', $outype->id);  $mform->setType('edutypeid', PARAM_INT);
        $mform->addElement('hidden', 'typeou', $outype->cod);  $mform->setType('typeou', PARAM_TEXT);
        $mform->addElement('hidden', 'oid', $oid);  $mform->setType('oid', PARAM_INT);
		$mform->addElement('hidden', 'who', $who);          $mform->setType('who', PARAM_TEXT);
		$mform->addElement('hidden', 'useridd', $useridd);  $mform->setType('useridd', PARAM_INT);
        $mform->addElement('hidden', 'action', 'step2');    $mform->setType('action', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('continue'));
    }

    function validation($data) {
       
        $errors = array();

        // echo '<pre>'; print_r($data); echo '</pre>';
        // echo $data['isadress'];
        if ($data['rayonid'] == 0)  {
            $errors['rayonid'] = get_string('selectarayon', 'block_monitoring'); 
        }
        
       
        if (!isset($data['isregequalhome']) && empty($data['addresshome']))    {
            $errors['addresshome'] = get_string('noaddresshome', 'block_monitoring');
        }   

        $frm->lastname1 = trim($data['lastname1']);
        $frm->firstname1 = trim($data['firstname1']);
        if (!check_data($frm))  {
            $errors['firstname1'] = get_string('childexists', 'block_monitoring', $frm->lastname1 . ' ' . $frm->firstname1); 
        }
        //print_r($errors);
        
        if (!isset($data['isconfirmpersonaldata']))    {
            $errors['isconfirmpersonaldata'] = get_string('isnoconfirmpd', 'block_monitoring');
        }   

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }
}


class declarant_form extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $uid, $data1, $oid;
        
        $mform =& $this->_form;

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
		$mform->addElement('hidden', 'edutypeid', $data1->edutypeid);  $mform->setType('edutypeid', PARAM_INT);
        $mform->addElement('hidden', 'oid', $oid);  $mform->setType('oid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'step1');  $mform->setType('action', PARAM_TEXT);
                
        $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
        $mform->addElement('hidden', 'typeou', $edutype->cod);  $mform->setType('typeou', PARAM_TEXT);
		
        $mform->addElement('hidden', 'lastname1', $data1->lastname1);  $mform->setType('lastname1', PARAM_TEXT);
		$mform->addElement('hidden', 'firstname1', $data1->firstname1);  $mform->setType('firstname1', PARAM_TEXT);
		$mform->addElement('hidden', 'secondname1', $data1->secondname1);  $mform->setType('secondname1', PARAM_TEXT);
		$mform->addElement('hidden', 'pol', $data1->pol);  $mform->setType('pol', PARAM_INT);
		$mform->addElement('hidden', 'birthday1', $data1->birthday1);  $mform->setType('birthday1', PARAM_INT);        
		$mform->addElement('hidden', 'document1', $data1->document1);  $mform->setType('document1', PARAM_INT);
        $mform->addElement('hidden', 'serial1', $data1->serial1);  $mform->setType('serial1', PARAM_TEXT);
        $mform->addElement('hidden', 'number1', $data1->number1);  $mform->setType('number1', PARAM_TEXT);
        $mform->addElement('hidden', 'when_hands1', $data1->when_hands1); $mform->setType('when_hands1', PARAM_INT);
        $mform->addElement('hidden', 'who_hands1', $data1->when_hands1); $mform->setType('who_hands1', PARAM_TEXT);        
        $mform->addElement('hidden', 'addressregistration', $data1->addressregistration); $mform->setType('addressregistration', PARAM_TEXT);        
		$mform->addElement('hidden', 'isregequalhome', $data1->isregequalhome);  $mform->setType('isregequalhome', PARAM_INT);
        $mform->addElement('hidden', 'addresshome', $data1->addresshome); $mform->setType('addresshome', PARAM_TEXT);        
		$mform->addElement('hidden', 'who', $data1->who);  $mform->setType('who', PARAM_TEXT);
		$mform->addElement('hidden', 'useridd', $data1->useridd);  $mform->setType('useridd', PARAM_INT);
        $mform->addElement('hidden', 'isconfirmpersonaldata', $data1->useridd);  $mform->setType('isconfirmpersonaldata', PARAM_INT);
        
       
/*       
        $mform->addElement('header','', get_string('childata', 'block_monitoring'));
        
        $mform->addElement('static', '', get_string('fio', 'block_monitoring'), $data1->lastname1 . ' ' . $data1->firstname1 . ' ' . $data1->secondname1);

        $choices = array();
        $choices['1'] = get_string('pol1', 'block_mou_school');
        $choices['2'] = get_string('pol2', 'block_mou_school');
        $mform->addElement('static', '', get_string('pol', 'block_mou_ege'), $choices[$data1->pol]);        
        $mform->addElement('static', '', get_string('birthday', 'block_mou_school'), date('d.m.Y', $data1->birthday1) . ' г.');
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('static', '', get_string('typedocuments', 'block_mou_ege'), $choices[$data1->document1]);
        $mform->addElement('static', '', get_string('serial', 'block_mou_ege'), $data1->serial1);
        $mform->addElement('static', '', get_string('number', 'block_mou_ege'), $data1->number1);
		$mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', $data1->when_hands1) . ' г.');
        $mform->addElement('static', '', get_string('who_hands', 'block_mou_ege'), $data1->who_hands1);
        $mform->addElement('static', '', get_string('regaddress', 'block_monitoring'), $data1->addressregistration);
*/        

/*

        $mform->addElement('header','', get_string('ou1', 'block_mou_att'));
      
        if($rayon = get_record_sql("SELECT id, shortname FROM {$CFG->prefix}monit_rayon WHERE id=$rid"))   {
             $mform->addElement('static', '', get_string('rayon', 'block_monitoring'), $rayon->shortname);
        }     
       
        $outype = get_config_typeou($edutype->cod);
        $mform->addElement('static', '', get_string('typeou', 'block_mou_att'), $outype->name);

        if($ou = get_record_sql("SELECT id, name FROM {$CFG->prefix}{$outype->tblname} WHERE id=$oid"))   {
             $mform->addElement('static', '', $outype->strtitle, $ou->name);
        }     
*/
   
/*   
        $schoolmenu = array();
        $schoolmenu[0] = $outype->strselect;
        
        if ($rid != 0)  {
            $strsql = "SELECT id, rayonid, name  FROM {$CFG->prefix}{$outype->tblname}
            			WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid {$outype->where} 
            			ORDER BY number";                    
            if ($schools =  get_records_sql($strsql))	{
            	foreach ($schools as $school) {
            		$len = mb_strlen ($school->name);
            		if ($len > 100)  {
            			// $school->name = substr($school->name, 0, 200) . ' ...';
            			$school->name = mb_substr($school->name, 0,  100, 'UTF-8') . ' ...';
            		}
            		$schoolmenu[$school->id] =$school->name;
            	}
            }
            $mform->addElement('select', 'oid', $outype->strtitle, $schoolmenu);
            // $mform->setHelpButton('hiddensections', array('coursehiddensections', get_string('hiddensections')), true);
            $mform->addRule('oid', get_string('missingname'), 'required', null, 'client');
            $mform->setDefault('oid', 0);
        }
*/

        $mform->addElement('header','', get_string('declarantdata', 'block_monitoring'));
        
        /*
        $radio = array();
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('mother', 'block_mou_nsop'), 0);
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('father', 'block_mou_nsop'), 1);
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('predstavitel', 'block_mou_nsop'), 2);
        $mform->addGroup($radio, 'familystatus', get_string('familyplace', 'block_mou_nsop'), ' ', false);
        */
        
        if ($data1->who == 'oper')  {
            $user = get_record_select('user', "id = $data1->useridd", 'id, lastname, firstname, email, phone1, phone2');
            // print_heading('Регистрация заявления от имени заявителя: ' . fullname($user), 'center', 4);
        } else {
            // $user = $USER;
            $user->id = $USER->id;
            $user->lastname = $USER->lastname;
            $user->firstname = $USER->firstname;
            $user->email = $USER->email;
            $user->phone1 = $USER->phone1;
            $user->phone2 = $USER->phone2;
        }
            
   	    list($f,$s) = explode(' ', $user->firstname);
        $user->firstname = $f;
        $user->secondname = $s;

/*        
        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="30" size="30"');
        $mform->addRule('lastname', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('lastname', $user->lastname);
        $mform->setType('lastname', PARAM_TEXT);

        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="30" size="30"');
        $mform->addRule('firstname', get_string('missingname'), 'required', null, 'client');
        $mform->setType('firstname', PARAM_TEXT);
        $mform->setDefault('firstname', $user->firstname);

        $mform->addElement('text', 'secondname', get_string('secondname', 'block_monitoring'), 'maxlength="30" size="30"');
        $mform->addRule('secondname', get_string('missingname'), 'required', null, 'client');
        $mform->setType('secondname', PARAM_TEXT);
        $mform->setDefault('secondname', $user->secondname);
*/

        $mform->addElement('static', '', get_string('lastname'), $user->lastname);
        $mform->addElement('static', '', get_string('firstname'), $user->firstname);
        $mform->addElement('static', '', get_string('secondname', 'block_monitoring'), $user->secondname);                

        $choices = array();
        $choices['0'] = get_string('mother', 'block_mou_nsop');
        $choices['1'] = get_string('father', 'block_mou_nsop');
        $choices['2'] = get_string('predstavitel', 'block_mou_nsop');
        $mform->addElement('select', 'familystatus', get_string('familyplace', 'block_mou_nsop'), $choices);
        $mform->addRule('familystatus', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('familystatus', 0);
        
        // $mform->addElement('static', '', '');
        // $mform->addElement('static', '', get_string('typedocuments', 'block_mou_nsop'));
/*        		
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        // $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('select', 'document', get_string('typedocuments', 'block_mou_nsop'), $choices);
        // $mform->addRule('document', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('document', 1);

        $mform->addElement('text', 'serial', get_string('serial', 'block_mou_ege'), 'maxlength="6" size="6"');
        // $mform->addRule('serial', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('serial', PARAM_TEXT);

        $mform->addElement('text', 'number', get_string('number', 'block_mou_ege'), 'maxlength="10" size="10"');
        // $mform->addRule('number', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('number', PARAM_TEXT);
        
		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 20;
		$mform->addElement('date_selector', 'when_hands', get_string('when_hands', 'block_mou_ege'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		// $mform->addRule('when_hands', get_string('missingname'), 'required', null, 'client');
		$mform->setType('when_hands', PARAM_INT);
		$mform->setDefault('when_hands', time() - YEARSECS*9); 

        $mform->addElement('text', 'who_hands', get_string('who_hands', 'block_mou_ege'), 'maxlength="100" size="70"');
        // $mform->addRule('who_hands', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('who_hands', PARAM_TEXT);
*/
        // $mform->addElement('hidden', 'isconfirmpersonaldata', $data1->useridd);  $mform->setType('isconfirmpersonaldata', PARAM_INT);        
        // $mform->addElement('header','', get_string('schooltab5', 'block_monitoring'));
        $mform->addElement('static', '', '');
        $mform->addElement('text', 'documentzakon', get_string('documentzakon', 'block_monitoring'), 'maxlength="250" size="70"');
        $mform->addRule('documentzakon', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('documentzakon', PARAM_TEXT);

        $mform->addElement('text', 'documentplace', get_string('documentplace', 'block_monitoring'), 'maxlength="250" size="70"');
        $mform->addRule('documentplace', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('documentplace', PARAM_TEXT);

        
        $mform->addElement('static', '', '');
                
 		$mform->addElement('textarea', 'addressfact', get_string('regaddress', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        $mform->addRule('addressfact', '', 'required', null, 'client');

        $mform->addElement('text', 'workplace', get_string('workplace', 'block_monitoring'), 'maxlength="250" size="70"');
        $mform->addRule('workplace', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('workplace', PARAM_TEXT);

        $mform->addElement('static', '', get_string('email', 'block_monitoring'), $user->email);
        
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);
        $mform->setDefault('phone1', $user->phone1);

        $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="20"');
        $mform->setType('phone2', PARAM_TEXT);
        $mform->setDefault('phone2', $user->phone2);

        $mform->addElement('static', '', '');
        
        
        if($ou = get_record_sql("SELECT id, www FROM {$CFG->prefix}{$edutype->tblname} WHERE id=$oid"))   {
            $strwww = ' <br>на сайте ОУ';
            if (isset($ou->www) && !empty($ou->www)) {
                $strwww = "<a target=\"_blank\" href=\"$ou->www\"> $strwww </a>";
            }
            $strwww = get_string('isknowingou', 'block_monitoring') . $strwww;
        }     

        $mform->addElement('checkbox', 'isknowingou', $strwww);
        $mform->addElement('checkbox', 'issubscribe', get_string('issubscribe', 'block_monitoring'));
      
        if ($declarant = get_record_select('monit_queue_declarant', "userid = $user->id"))  {
            foreach ($declarant as $field => $value)    {
                $mform->setDefault($field, $value);
            } 
        }


        $buttonarray=array();
        // 'Подтверждаю правильность внесенных данных'
        $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('return','block_monitoring'));
        $buttonarray[] = &$mform->createElement('submit', 'nextbutton', get_string('continue'));
        // $buttonarray[] = &$mform->createElement('cancel', 'submitbutton', 'Отменить оформление заявки');
        // $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    function validation($data) {
        
        $errors = array();

        // echo '<pre>'; print_r($data); echo '</pre>';
        /*
        if ($data['oid'] == 0)  {
            $errors['oid'] = get_string ('selectou', 'block_mou_att'); 
        }
        */
        
        // print_r($errors);
        
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }
}



class finish_form extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $uid, $data1;
        
        $mform =& $this->_form;

        foreach ($data1 as $field => $value)    {
            $value = stripslashes($value);
            $mform->addElement('hidden', $field, $value);  // $mform->setType('yid', PARAM_INT);
            $data1->{$field} = $value; 
        }

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
        $mform->addElement('hidden', 'typeou', $edutype->cod);  $mform->setType('typeou', PARAM_TEXT);

        $mform->addElement('header','', get_string('ou1', 'block_mou_att'));
      
        if($rayon = get_record_sql("SELECT id, shortname FROM {$CFG->prefix}monit_rayon WHERE id=$rid"))   {
             $mform->addElement('static', '', get_string('rayon', 'block_monitoring'), $rayon->shortname);
        }     
       
        $outype = get_config_typeou($edutype->cod);
        $mform->addElement('static', '', get_string('typeou', 'block_mou_att'), $outype->name);
        
        $strsql = "SELECT id, name  FROM {$CFG->prefix}{$outype->tblname}
                    WHERE id=$data1->oid";
        if ($ou =  get_record_sql($strsql))	{
            $mform->addElement('static', '', $outype->strtitle, $ou->name);
        }    

       
        $mform->addElement('header','', get_string('childata', 'block_monitoring'));
        
        $mform->addElement('static', '', get_string('fio', 'block_monitoring'), $data1->lastname1 . ' ' . $data1->firstname1 . ' ' . $data1->secondname1);

        $choices = array();
        $choices['1'] = get_string('pol1', 'block_mou_school');
        $choices['2'] = get_string('pol2', 'block_mou_school');
        $mform->addElement('static', '', get_string('pol', 'block_mou_ege'), $choices[$data1->pol]);        
        $mform->addElement('static', '', get_string('birthday', 'block_mou_school'), date('d.m.Y', $data1->birthday1) . ' г.');
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('static', '', get_string('typedocuments', 'block_mou_ege'), $choices[$data1->document1]);
        $mform->addElement('static', '', get_string('serial', 'block_mou_ege'), $data1->serial1);
        $mform->addElement('static', '', get_string('number', 'block_mou_ege'), $data1->number1);
		$mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', $data1->when_hands1) . ' г.');
        $mform->addElement('static', '', get_string('who_hands', 'block_mou_ege'), $data1->who_hands1);
        $mform->addElement('static', '', get_string('regaddress', 'block_monitoring'), $data1->addressregistration);
        

        $mform->addElement('header','', get_string('declarantdata', 'block_monitoring'));
        
        if (isset($data1->isview)) {
            $user = get_record_select('user', "id = $data1->userid", 'id, lastname, firstname, email, phone1, phone2');            
        } else if (isset($data1->who) && $data1->who == 'oper') {
            $user = get_record_select('user', "id = $data1->useridd", 'id, lastname, firstname, email, phone1, phone2');
        } else {
            // $user = $USER;
            $user->id = $USER->id;
            $user->lastname = $USER->lastname;
            $user->firstname = $USER->firstname;
            $user->email = $USER->email;
            $user->phone1 = $USER->phone1;
            $user->phone2 = $USER->phone2;
        }  
        list($f,$s) = explode(' ', $user->firstname);
        $user->firstname = $f;
        $user->secondname = $s;

        $mform->addElement('static', '', get_string('lastname'), $user->lastname);
        $mform->addElement('static', '', get_string('firstname'), $user->firstname);
        $mform->addElement('static', '', get_string('secondname', 'block_monitoring'), $user->secondname);                
        
        $choices = array();
        $choices['0'] = get_string('mother', 'block_mou_nsop');
        $choices['1'] = get_string('father', 'block_mou_nsop');
        $choices['2'] = get_string('predstavitel', 'block_mou_nsop');
        $mform->addElement('static', '', get_string('familyplace', 'block_mou_nsop'), $choices[$data1->familystatus]);
        /*
        $mform->addElement('static', '', get_string('typedocuments', 'block_mou_nsop'), get_string('typedocuments1', 'block_mou_ege'));
        $mform->addElement('static', '', get_string('serial', 'block_mou_ege'), $data1->serial);
        $mform->addElement('static', '', get_string('number', 'block_mou_ege'), $data1->number);
        if (!is_array($data1->when_hands)) {
		      $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', $data1->when_hands));         
        } else {    
		      $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', mktime(0, 0, 0, $data1->when_hands['month'], $data1->when_hands['day'], $data1->when_hands['year'])) . ' г.');
        }      
        $mform->addElement('static', '', get_string('who_hands', 'block_mou_ege'), $data1->who_hands);
        */
        $mform->addElement('static', '', get_string('documentzakon', 'block_monitoring'), $data1->documentzakon);
        $mform->addElement('static', '', get_string('documentplace', 'block_monitoring'), $data1->documentplace);
        $mform->addElement('static', '', get_string('regaddress', 'block_monitoring'), $data1->addressfact);                
        $mform->addElement('static', '', get_string('workplace', 'block_monitoring'), $data1->workplace);
        $mform->addElement('static', '', get_string('email', 'block_monitoring'), $user->email);
        $mform->addElement('static', '', get_string('phone'), $user->phone1);
        $mform->addElement('static', '', get_string('phone2'), $user->phone2);

        $da = get_string('yes');
        $mform->addElement('static', '',  $da, get_string('isconfirmpersonaldata', 'block_monitoring'));
        $mform->addElement('static', '',  $da, get_string('isknowingou', 'block_monitoring'));
        $mform->addElement('static', '',  $da, get_string('issubscribe', 'block_monitoring'));
      


        $buttonarray=array();
        // 
        $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('return', 'block_monitoring'));
        if (!isset($data1->isview)) {
            $buttonarray[] = &$mform->createElement('submit', 'save', get_string('sendconfirm', 'block_monitoring'));
        }    
        // $buttonarray[] = &$mform->createElement('cancel', 'submitbutton', 'Отменить оформление заявки');
        // $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

}


function message_thanks($requestids)
{
    if (is_array($requestids))   {
       $str = 'Спасибо за регистрацию заявления!<br><br>
               Уважаемый пользователь! <br><br>
               Идентификаторы Ваших заявлений:<br>';

        foreach($requestids as $reqid)   {
            if ($request = get_record_select ('monit_queue_request', "id = $reqid", 'id, code')) {
                $str .= "<h3>$request->code</h3><br>";
            }
        }        
    } else {
        if ($request = get_record_select ('monit_queue_request', "id = $requestids", 'id, code')) {    
            $str = "Спасибо за регистрацию заявления!<br><br>
                    Уважаемый пользователь! <br><br>
                    Идентификатор Вашего заявления:<br> <h3>$request->code</h3>";
        } else {
            $str = "Идентификатор заявления не обнаружен.";
        }      
    }        
    
    return $str;      
    
}

function check_data($frm)   
{
    global $USER, $CFG;
    
    $ret = true;
    $strfirstname = mb_strtoupper($frm->firstname1, 'UTF-8');
    $strlastname  = mb_strtoupper($frm->lastname1, 'UTF-8');
    
    if ($frm->who == 'oper')    {
        $user = get_record_select('user', "id = $frm->useridd", 'id, lastname, firstname, email, phone1, phone2');            
    }  else {
        // $user = $USER;
        $user->id = $USER->id;
        $user->lastname = $USER->lastname;
        $user->firstname = $USER->firstname;
        $user->email = $USER->email;
        $user->phone1 = $USER->phone1;
        $user->phone2 = $USER->phone2;
    }
    
    if ($declarant = get_record_select('monit_queue_declarant', "userid = $user->id", 'id'))  {
        if ($requests = get_records_select('monit_queue_request', "declarantid = $declarant->id", '', 'id, childid'))  {
            foreach ($requests as $request) {
                $strsql = "select lastname, firstname from {$CFG->prefix}monit_queue_child
                           where id = $request->childid AND UCASE(lastname) = '$strlastname' 
                           AND UCASE(firstname) = '$strfirstname'";
                // echo $strsql . '<br>';                             
                if ($child = get_record_sql($strsql))    {
                    $ret = false;
                    break;                                  
                } 
                
            }
        }    
    }    
    
    return $ret;
}


class declarant_form_dou_step2 extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $uid, $action,  $data1;
        
        $mform =& $this->_form;

/*       
        foreach ($data1 as $field => $value)    {
            $value = stripslashes($value);
            $mform->addElement('hidden', $field, $value);  // $mform->setType('yid', PARAM_INT);
            $data1->{$field} = $value; 
        }
*/             
        $mform->addElement('hidden', 'action', 'step2');  $mform->setType('action', PARAM_TEXT);
        $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
        $mform->addElement('hidden', 'typeou', $edutype->cod);  $mform->setType('typeou', PARAM_TEXT);
    	$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $mform->addElement('hidden', 'rayonid', $rid);  $mform->setType('rid', PARAM_INT);
		$mform->addElement('hidden', 'edutypeid', $data1->edutypeid);  $mform->setType('edutypeid', PARAM_INT);
        $mform->addElement('hidden', 'lastname1', $data1->lastname1);  $mform->setType('lastname1', PARAM_TEXT);
		$mform->addElement('hidden', 'firstname1', $data1->firstname1);  $mform->setType('firstname1', PARAM_TEXT);
		$mform->addElement('hidden', 'secondname1', $data1->secondname1);  $mform->setType('secondname1', PARAM_TEXT);
		$mform->addElement('hidden', 'pol', $data1->pol);  $mform->setType('pol', PARAM_INT);
		$mform->addElement('hidden', 'birthday1', $data1->birthday1);  $mform->setType('birthday1', PARAM_INT);        
		$mform->addElement('hidden', 'document1', $data1->document1);  $mform->setType('document1', PARAM_INT);
        $mform->addElement('hidden', 'serial1', $data1->serial1);  $mform->setType('serial1', PARAM_TEXT);
        $mform->addElement('hidden', 'number1', $data1->number1);  $mform->setType('number1', PARAM_TEXT);
        $mform->addElement('hidden', 'when_hands1', $data1->when_hands1); $mform->setType('when_hands1', PARAM_INT);
        $mform->addElement('hidden', 'who_hands1', $data1->who_hands1); $mform->setType('who_hands1', PARAM_TEXT);        
        $mform->addElement('hidden', 'addressregistration', $data1->addressregistration); $mform->setType('addressregistration', PARAM_TEXT);        
		$mform->addElement('hidden', 'isregequalhome', $data1->isregequalhome);  $mform->setType('isregequalhome', PARAM_INT);
        $mform->addElement('hidden', 'addresshome', $data1->addresshome); $mform->setType('addresshome', PARAM_TEXT);
		$mform->addElement('hidden', 'who', $data1->who);  $mform->setType('who', PARAM_TEXT);
		$mform->addElement('hidden', 'useridd', $data1->useridd);  $mform->setType('useridd', PARAM_INT);
        $mform->addElement('hidden', 'dou1', $data1->dou1);  $mform->setType('dou1', PARAM_INT);
        $mform->addElement('hidden', 'dou2', $data1->dou2);  $mform->setType('dou2', PARAM_INT);
        $mform->addElement('hidden', 'dou3', $data1->dou3);  $mform->setType('dou3', PARAM_INT);
        $mform->addElement('hidden', 'isconfirmpersonaldata', $data1->useridd);  $mform->setType('isconfirmpersonaldata', PARAM_INT);
    

        $mform->addElement('header','', get_string('declarantdata', 'block_monitoring'));

/*        
        $radio = array();
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('mother', 'block_mou_nsop'), 0);
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('father', 'block_mou_nsop'), 1);
        $radio[] = &MoodleQuickForm::createElement('radio', 'familystatus', null, get_string('predstavitel', 'block_mou_nsop'), 2);
        $mform->addGroup($radio, 'familystatus', get_string('familyplace', 'block_mou_nsop'), ' ', false);
        $mform->addRule('familystatus', get_string('missingname'), 'required', null, 'client');
*/

        if ($data1->who == 'oper')  {
            $user = get_record_select('user', "id = $data1->useridd", 'id, lastname, firstname, email, phone1, phone2');
            // print_heading('Регистрация заявления от имени заявителя: ' . fullname($user), 'center', 4);
        } else {
            // $user = $USER;
            $user->id = $USER->id;
            $user->lastname = $USER->lastname;
            $user->firstname = $USER->firstname;
            $user->email = $USER->email;
            $user->phone1 = $USER->phone1;
            $user->phone2 = $USER->phone2;
        }

  	    list($f,$s) = explode(' ', $user->firstname);
        $user->firstname = $f;
        $user->secondname = $s;

        $mform->addElement('static', '', get_string('lastname'), $user->lastname);
        $mform->addElement('static', '', get_string('firstname'), $user->firstname);
        $mform->addElement('static', '', get_string('secondname', 'block_monitoring'), $user->secondname);              

        $choices = array();
        $choices['0'] = get_string('mother', 'block_mou_nsop');
        $choices['1'] = get_string('father', 'block_mou_nsop');
        $choices['2'] = get_string('predstavitel', 'block_mou_nsop');
        $mform->addElement('select', 'familystatus', get_string('familyplace', 'block_mou_nsop'), $choices);
        $mform->addRule('familystatus', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('familystatus', 0);
        
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        // $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('select', 'document', get_string('typedocuments', 'block_mou_nsop'), $choices);
        $mform->addRule('document', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('document', 1);

        $mform->addElement('text', 'serial', get_string('serial', 'block_mou_ege'), 'maxlength="6" size="6"');
        $mform->addRule('serial', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('serial', PARAM_TEXT);

        $mform->addElement('text', 'number', get_string('number', 'block_mou_ege'), 'maxlength="10" size="10"');
        $mform->addRule('number', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('number', PARAM_TEXT);
        
		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 20;
		$mform->addElement('date_selector', 'when_hands', get_string('when_hands', 'block_mou_ege'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		$mform->addRule('when_hands', get_string('missingname'), 'required', null, 'client');
		// $mform->setType('birthday', PARAM_INT);
		$mform->setDefault('when_hands', time() - YEARSECS*9); 

        $mform->addElement('text', 'who_hands', get_string('who_hands', 'block_mou_ege'), 'maxlength="100" size="70"');
        $mform->addRule('who_hands', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('who_hands', PARAM_TEXT);
        
        // $mform->addElement('header','', get_string('schooltab5', 'block_monitoring'));
        $mform->addElement('static', '', '');
        $mform->addElement('text', 'documentzakon', get_string('documentzakon', 'block_monitoring'), 'maxlength="200" size="70"');
        $mform->addRule('documentzakon', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('documentzakon', PARAM_TEXT);

        $mform->addElement('text', 'documentplace', get_string('documentplace', 'block_monitoring'), 'maxlength="200" size="70"');
        $mform->addRule('documentplace', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('documentplace', PARAM_TEXT);
        
 		$mform->addElement('textarea', 'addressfact', get_string('regaddress', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        $mform->addRule('addressfact', get_string('missingname'), 'required', null, 'client');

        $mform->addElement('text', 'workplace', get_string('workplace', 'block_monitoring'), 'maxlength="250" size="70"');
        $mform->addRule('workplace', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('workplace', PARAM_TEXT);

        $mform->addElement('static', '', '');
        
        $mform->addElement('static', '', get_string('email', 'block_monitoring'), $user->email);
        
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);
        $mform->setDefault('phone1', $user->phone1);

        $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="20"');
        $mform->setType('phone2', PARAM_TEXT);
        $mform->setDefault('phone2', $user->phone2);

        $mform->addElement('static', '', '');
         
        // $mform->addElement('checkbox', 'isconfirmpersonaldata', get_string('isconfirmpersonaldata', 'block_monitoring'));
        $mform->addElement('checkbox', 'isknowingou', get_string('isknowingou', 'block_monitoring'));
        $mform->addElement('checkbox', 'issubscribe', get_string('issubscribe', 'block_monitoring'));
      
        if ($declarant = get_record_select('monit_queue_declarant', "userid = $user->id"))  {
            foreach ($declarant as $field => $value)    {
                $mform->setDefault($field, $value);
            } 
        }
            
        $buttonarray=array();
        // 'Подтверждаю правильность внесенных данных'
        $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('return','block_monitoring'));
        $buttonarray[] = &$mform->createElement('submit', 'nextbutton', get_string('continue'));
        // $buttonarray[] = &$mform->createElement('cancel', 'submitbutton', 'Отменить оформление заявки');
        // $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    function validation($data) {
        
        global $strselect;
       
        $errors = array();

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }
}


class declarant_form_dou_step3 extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $uid, $data1;
        
        $mform =& $this->_form;
        
        $mform->setDefault('action', 'step3');
        /*
        $mform->addElement('hidden', 'action', $action);  $mform->setType('action', PARAM_TEXT);
        $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
        $mform->addElement('hidden', 'typeou', $edutype->cod);  $mform->setType('typeou', PARAM_TEXT);
        */

        $mform->addElement('header','', get_string('infolgoti', 'block_monitoring'));
        
        $mform->addElement('static', '', '', get_string('benefit', 'block_monitoring'));

        $rayon = get_record_select('monit_rayon', "id = $rid", 'id, benefitsids');
        $rayonbenefits = array();
        if ($rayon->benefitsids != '0') {
            $rayonbenefits = explode(',', $rayon->benefitsids); 
        }

        $benefits = get_records('monit_queue_benefit');
        foreach ($benefits as $benefit) {
            if (!empty($rayonbenefits))  {
                if (!in_array($benefit->id, $rayonbenefits)) {
                    continue;
                } 
            }
            
            $fname = 'b_'.$benefit->id;
            $mform->addElement('checkbox', $fname, $benefit->name);
            unset($data1->{$fname});
        }    

        $choices = array(get_string('no'));
        $healths = get_records('monit_queue_health');
        foreach ($healths as $health) {
            $choices[$health->id] = $health->name;
        }    
        $mform->addElement('select', 'healthid', get_string('health', 'block_monitoring'), $choices);
        $mform->setDefault('healthid', 0);
        unset($data1->healthid);

        $startyear = date('Y');
        $stopyear = $startyear + 5;
		$mform->addElement('date_selector', 'dateenrollment', get_string('dateenrollment', 'block_monitoring'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		// $mform->addRule('dateenrollment', get_string('missingname'), 'required', null, 'client');
		$mform->setDefault('dateenrollment', time());
        unset($data1->dateenrollment);
        unset($data1->_qf__declarant_form_dou_step2); 

        foreach ($data1 as $field => $value)    {
            // $value = stripslashes($value);
            $mform->addElement('hidden', $field, $value);  // $mform->setType('yid', PARAM_INT);
            // $data1->{$field} = $value; 
        }
           
        $buttonarray=array();
        
        $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('return','block_monitoring'));
        $buttonarray[] = &$mform->createElement('submit', 'nextbutton', get_string('continue'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    function validation($data) {
        
        $errors = array();

        // echo '<pre>'; print_r($data); echo '</pre>';
        if ($data['dou1'] == 0 && $data['dou2'] == 0 && $data['dou3'] == 0)  {
            $errors['dou1'] = get_string ('errordou', 'block_monitoring'); 
        }
        
        // print_r($errors);
        
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }
}


class finish_form_dou extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $uid, $data1;
        
        $mform =& $this->_form;

        foreach ($data1 as $field => $value)    {
            $value = stripslashes($value);
            $mform->addElement('hidden', $field, $value);  // $mform->setType('yid', PARAM_INT);
            $data1->{$field} = $value; 
        }
        
        // print_object($data1);

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
        $mform->addElement('hidden', 'typeou', $edutype->cod);  $mform->setType('typeou', PARAM_TEXT);


        $mform->addElement('header','', get_string('ou1', 'block_mou_att'));
      
        if($rayon = get_record_sql("SELECT id, shortname FROM {$CFG->prefix}monit_rayon WHERE id=$rid"))   {
             $mform->addElement('static', '', get_string('rayon', 'block_monitoring'), $rayon->shortname);
        }     
       
        $outype = get_config_typeou($edutype->cod);
        $mform->addElement('static', '', get_string('typeou', 'block_mou_att'), $outype->name);
        
        $dous = array('dou1', 'dou2', 'dou3');
        foreach ($dous as $dou) {
            if (isset($data1->{$dou}))  {
                $oid = $data1->{$dou};
                if ($oid > 0)  {
                    $strsql = "SELECT id, name  FROM {$CFG->prefix}{$outype->tblname}
                                WHERE id=$oid";
                    if ($ou =  get_record_sql($strsql))	{
                        $mform->addElement('static', '', get_string($dou, 'block_monitoring'), $ou->name);
                    }    
                }
            } else {
                $oid = 0;
            }    
        }
        


       
        $mform->addElement('header','', get_string('childata', 'block_monitoring'));
        
        $mform->addElement('static', '', get_string('fio', 'block_monitoring'), $data1->lastname1 . ' ' . $data1->firstname1 . ' ' . $data1->secondname1);

        $choices = array();
        $choices['1'] = get_string('pol1', 'block_mou_school');
        $choices['2'] = get_string('pol2', 'block_mou_school');
        $mform->addElement('static', '', get_string('pol', 'block_mou_ege'), $choices[$data1->pol]);        
        $mform->addElement('static', '', get_string('birthday', 'block_mou_school'), date('d.m.Y', $data1->birthday1) . ' г.');
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('static', '', get_string('typedocuments', 'block_mou_ege'), $choices[$data1->document1]);
        $mform->addElement('static', '', get_string('serial', 'block_mou_ege'), $data1->serial1);
        $mform->addElement('static', '', get_string('number', 'block_mou_ege'), $data1->number1);
        if ($data1->when_hands1 == '0000-00-00')    {
            $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), '-');
        } else {
            if (!isset($data1->isview)) {
        		$mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', $data1->when_hands1) . ' г.');
            } else {
                $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), convert_date($data1->when_hands1, 'en', 'ru') . ' г.');
            }    
        }
        $mform->addElement('static', '', get_string('who_hands', 'block_mou_ege'), $data1->who_hands1);
        $mform->addElement('static', '', get_string('regaddress', 'block_monitoring'), $data1->addressregistration);

        $benefits = get_records('monit_queue_benefit');
        $strbenefits = '';
        if (!isset($data1->isview)) {
            foreach ($benefits as $benefit) {
                $fname = 'b_'.$benefit->id;
                if (isset($data1->{$fname}))  {
                    $strbenefits .= $benefit->name . '<br>';
                }    
            }
        } else {
            $realbenefits = explode (',', $data1->benefitsids);
            foreach ($benefits as $benefit) { 
                if (in_array($benefit->id, $realbenefits))  {
                    $strbenefits .= $benefit->name . '<br>';
                }
            }    
        }
        $strhealth = get_string('no');
        if ($strbenefits == '') $strbenefits = $strhealth;                
        $mform->addElement('static', '', get_string('benefit', 'block_monitoring'), $strbenefits);
        

        if ($data1->healthid > 0)   {
            $health = get_record('monit_queue_health', 'id', $data1->healthid);
            $strhealth = $health->name; 
        }
        $mform->addElement('static', '', get_string('health', 'block_monitoring'), $strhealth);
        
        $mform->addElement('static', '', get_string('dateenrollment', 'block_monitoring'), date('d.m.Y', $data1->dateenrollment));
        

        $mform->addElement('header','', get_string('declarantdata', 'block_monitoring'));
        
        if (isset($data1->isview)) {
            $user = get_record_select('user', "id = $data1->userid", 'id, lastname, firstname, email, phone1, phone2');            
        } else if (isset($data1->who) && $data1->who == 'oper') {
            $user = get_record_select('user', "id = $data1->useridd", 'id, lastname, firstname, email, phone1, phone2');
        } else {
            // $user = $USER;
            $user->id = $USER->id;
            $user->lastname = $USER->lastname;
            $user->firstname = $USER->firstname;
            $user->email = $USER->email;
            $user->phone1 = $USER->phone1;
            $user->phone2 = $USER->phone2;
        }  
        list($f,$s) = explode(' ', $user->firstname);
        $user->firstname = $f;
        $user->secondname = $s;

        $mform->addElement('static', '', get_string('lastname'), $user->lastname);
        $mform->addElement('static', '', get_string('firstname'), $user->firstname);
        $mform->addElement('static', '', get_string('secondname', 'block_monitoring'), $user->secondname);                
        $choices = array();
        $choices['0'] = get_string('mother', 'block_mou_nsop');
        $choices['1'] = get_string('father', 'block_mou_nsop');
        $choices['2'] = get_string('predstavitel', 'block_mou_nsop');
        $mform->addElement('static', '', get_string('familyplace', 'block_mou_nsop'), $choices[$data1->familystatus]);
        $mform->addElement('static', '', get_string('typedocuments', 'block_mou_nsop'), get_string('typedocuments1', 'block_mou_ege'));
        $mform->addElement('static', '', get_string('serial', 'block_mou_ege'), $data1->serial);
        $mform->addElement('static', '', get_string('number', 'block_mou_ege'), $data1->number);
        if (!is_array($data1->when_hands)) {
		      $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', $data1->when_hands));         
        } else {    
		      $mform->addElement('static', '', get_string('when_hands', 'block_mou_ege'), date('d.m.Y', mktime(0, 0, 0, $data1->when_hands['month'], $data1->when_hands['day'], $data1->when_hands['year'])) . ' г.');
        }      
        $mform->addElement('static', '', get_string('who_hands', 'block_mou_ege'), $data1->who_hands);
        $mform->addElement('static', '', get_string('documentzakon', 'block_monitoring'), $data1->documentzakon);
        $mform->addElement('static', '', get_string('documentplace', 'block_monitoring'), $data1->documentplace);
        $mform->addElement('static', '', get_string('regaddress', 'block_monitoring'), $data1->addressfact);
        $mform->addElement('static', '', get_string('workplace', 'block_monitoring'), $data1->workplace);                
        $mform->addElement('static', '', get_string('email', 'block_monitoring'), $user->email);
        $mform->addElement('static', '', get_string('phone'), $user->phone1);
        $mform->addElement('static', '', get_string('phone2'), $user->phone2);


        $da = get_string('yes');
        $mform->addElement('static', '',  $da, get_string('isconfirmpersonaldata', 'block_monitoring'));
        $mform->addElement('static', '',  $da, get_string('isknowingou', 'block_monitoring'));
        $mform->addElement('static', '',  $da, get_string('issubscribe', 'block_monitoring'));
      


        $buttonarray=array();
        // Подтверждаю правильность внесенных данных 
        $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('return', 'block_monitoring'));
        if (!isset($data1->isview)) {
            $buttonarray[] = &$mform->createElement('submit', 'save', get_string('sendconfirm', 'block_monitoring'));
        }    
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}



function save_request($frm, $what='')   
{
    global $USER;

    if ($frm->who == 'oper') {
        $rec->id =  $frm->useridd;            
    }  else {
        $rec->id =  $USER->id;
    }    
    /*
    $rec->lastname = trim($frm->lastname);
    $rec->firstname = trim($frm->firstname);
    $rec->firstname .= ' ' . trim ($frm->secondname);
    $rec->email =  $frm->email;
    */
    $rec->phone1 =  $frm->phone1;
    $rec->phone2 =  $frm->phone2;
    if (!update_record('user', $rec))   {
        error('Ошибка при обновлении данных пользователя.', 'index.php');
    }
    
    if (!$declarant = get_record_select('monit_queue_declarant', "userid = $rec->id", 'id'))  {
        unset($rec);
        $rec = $frm;
        $rec->rayonid = $frm->rid;
        $rec->userid = $USER->id;
        $rec->timemodified = time();
        if (!$declarantid = insert_record('monit_queue_declarant', $rec))  {
            error('Ошибка при сохранении данных заявителя.', 'index.php');
        }
    } else {
        $declarantid = $declarant->id;
    }    
    
    $child->lastname = trim($frm->lastname1); 
    $child->firstname = trim($frm->firstname1);
    $child->secondname = trim($frm->secondname1);
    $child->birthday = $frm->birthday1;
    $child->pol = $frm->pol;    
    $child->typedocuments = $frm->document1;
    $child->serial = trim($frm->serial1);
    $child->number = trim($frm->number1);
    $child->who_hands = trim($frm->who_hands1);
    $child->when_hands = $frm->when_hands1;
    $child->addressregistration = $frm->addressregistration; 
    $child->addresshome = $frm->addresshome; 
    $child->isregequalhome = $frm->isregequalhome;
    $child->timemodified = time();
    if (!$frm->childid = insert_record('monit_queue_child', $child))  {
        error('Ошибка при сохранении данных ребенка.', 'index.php');
    }
    
    $request->declarantid = $declarantid; 
    $request->rayonid = $frm->rid;
    $request->edutypeid = $frm->edutypeid;  
    $request->childid = $frm->childid; 
    $request->status = 9;
    $request->timecreated = time();     
    $request->timemodified = $request->timecreated; 
    // $request->birthyear = date('Y', $child->birthday);
    $request->birthyear =  get_birthyear_child($child->birthday);
    
    if ($what == 'dou') {
        $request->healthid = $frm->healthid;
        $request->dateenrollment = $frm->dateenrollment; 
        
        $benefits = get_records('monit_queue_benefit');
        $abenefits = array(0);
        foreach ($benefits as $benefit) {
            $fname = 'b_'.$benefit->id;
            if (isset($frm->{$fname}))  {
                $abenefits[] = $benefit->id;
            }    
        }    
        $request->benefitsids = implode (',', $abenefits);
        
        $id = array();
        $dous = array('dou1', 'dou2', 'dou3');
        foreach ($dous as $dou) {
            $frm->oid = $frm->{$dou};
            if ($frm->oid > 0)  {
                $request->oid = $frm->oid;
                $request->number = 0; // get_number_queue ($frm);
                $request->code = generate_code_request($frm);        
                if (!$id[] = insert_record('monit_queue_request', $request))  {
                    error(get_string('errorsavedata', 'block_monitoring'), 'index.php');
                }
            }
        }
    }  else {
        $request->oid = $frm->oid;
        $request->code = generate_code_request($frm);      
        $request->number = 0; // get_number_queue ($frm);          
        if (!$id = insert_record('monit_queue_request', $request))  {
            error(get_string('errorsavedata', 'block_monitoring'), 'index.php');
        }
    }

    // print_r($id);
    
    return $id;
}


function get_number_queue ($frm)
{
    global $CFG;
    
    $maxnumber = 1;
    $strsql = "SELECT max(number) as mn FROM {$CFG->prefix}monit_queue_request WHERE rayonid=$frm->rid AND oid=$frm->oid";
    if ($number = get_record_sql($strsql))  {
        $maxnumber = $number->mn + 1;
    }
    
    return $maxnumber; 
}


function generate_code_request($frm)
{
    global $USER;
    
    if ($frm->who == 'oper')    {
        $user = get_record_select('user', "id = $frm->useridd", 'id, lastname, firstname, email, phone1, phone2');            
    }  else {
        // $user = $USER;
        $user->id = $USER->id;
        $user->lastname = $USER->lastname;
        $user->firstname = $USER->firstname;
        $user->email = $USER->email;
        $user->phone1 = $USER->phone1;
        $user->phone2 = $USER->phone2;
    }
    
        
    $rayon = str_pad($frm->rid, 2, '0', STR_PAD_LEFT);
    $ou    = $frm->typeou . str_pad($frm->oid, 4, '0', STR_PAD_LEFT);
    $struser  = str_pad($user->id, 6, '0', STR_PAD_LEFT);
    $child = str_pad($frm->childid, 6, '0', STR_PAD_LEFT);    
    
    // $code = '31-';
    
    $code = $rayon . '-' . $ou . '-' . $struser . '-' . $child;
    
    return $code;
}


class editou_form_view extends moodleform 
{
    
    function definition() {

        global $yid, $rid, $oid, $ou, $typeou, $tablename;

        $mform =& $this->_form;

        //--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('schooltab1', 'block_monitoring'));

        $mform->addElement('static', '', get_string('nameschool', 'block_monitoring'), $ou->name);
        $mform->addElement('static', '', get_string('directorschool', 'block_monitoring'), $ou->fio);
        $mform->addElement('static', '', get_string('appointmenthead', 'block_monitoring'), $ou->appointment);
        $mform->addElement('static', '', get_string('numsession', 'block_monitoring'), $ou->numsession);

        //--------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('schooltab5', 'block_monitoring'));        

        $strwww = '-';
        if (isset($ou->www) && !empty($ou->www)) {
            $strwww = "<a href=\"$ou->www\"> $ou->www </a>";
        }
        $mform->addElement('static', '',  get_string('www', 'block_monitoring'), $strwww);
        $stremail = '-';
        if (isset($ou->www) && !empty($ou->www)) {
            $stremail = "<a href=\"mailto:$ou->email\"> $ou->email </a>";
        }
        $mform->addElement('static', '',  get_string('email', 'block_monitoring'), $stremail);
        $mform->addElement('static', '',  get_string('telnum', 'block_monitoring'), $ou->phones);
        
        if (isset($ou->fax) && !empty($ou->fax)) {
            $mform->addElement('static', '',  get_string('fax', 'block_monitoring'), $ou->fax);
        }    
        $mform->addElement('static', '', get_string('realaddress', 'block_monitoring'), $ou->realaddress);

        /*
        $mform->addElement('static', '', get_string('juridicaladdress', 'block_monitoring'), $ou->juridicaladdress);
        unset($choices);
	    $choices[0] = '-';
        $choices[1] = get_string('yes');
        $choices[-1] = get_string('no');;
        $mform->addElement('static', '',  get_string('isjurequalreal', 'block_monitoring'), $choices[$ou->isjurequalreal]);
        */

        //--------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('licensedu', 'block_monitoring'));
        $mform->addElement('static', '', get_string('numlicense', 'block_monitoring'), $ou->numlicense);
        $mform->addElement('static', '', get_string('regnumlicense', 'block_monitoring'), $ou->regnumlicense);
        
        if ($ou->startdatelicense == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->startdatelicense);
        }
        $mform->addElement('static', '', get_string('startdatelicense', 'block_monitoring'), $date1);
                
        if ($ou->enddatelicense == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->enddatelicense); 
        }
        $mform->addElement('static', '', get_string('enddatelicense', 'block_monitoring'), $date1);

        //--------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('licensextra', 'block_monitoring'));
        $mform->addElement('static', '', get_string('numlicensextra', 'block_monitoring'), $ou->numlicensextra);
        $mform->addElement('static', '', get_string('regnumlicensextra', 'block_monitoring'), $ou->regnumlicensextra);
        
        if ($ou->startdatelicensextra == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->startdatelicensextra); 
        }
        $mform->addElement('static', '', get_string('startdatelicensextra', 'block_monitoring'), $date1);

        if ($ou->enddatelicenseextra == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->enddatelicenseextra); 
        }
        $mform->addElement('static', '', get_string('enddatelicenseextra', 'block_monitoring'), $date1);

        //--------------------------------------------------------------------------------
        $mform->addElement('header', '', get_string('accreditcertificate', 'block_monitoring'));
        $mform->addElement('static', '', get_string('numcertificate', 'block_monitoring'), $ou->numcertificate);
        $mform->addElement('static', '', get_string('regnumcertificate', 'block_monitoring'), $ou->regnumcertificate);

        if ($ou->startdatecertificate == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->startdatecertificate); 
        }
        $mform->addElement('static', '', get_string('startdatecertificate', 'block_monitoring'), $date1);

        if ($ou->enddatecertificate == 0) {
            $date1 = '-';
        } else {
            $date1 =  date('d.m.Y', $ou->enddatecertificate); 
        }
        $mform->addElement('static', '', get_string('enddatecertificate', 'block_monitoring'), $date1);
       
    }
}


/**
 * Determines if a user is a operator in any OU, or an admin
 *
 * @uses $USER
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $includeadmin Include anyone wo is an admin as well
 * @return bool
 */
function isoperatorinanyou($userid=0, $includeadmin=true) {
    global $USER, $CFG;

    if (empty($CFG->rolesactive)) {     // Teachers are locked out during an upgrade to 1.7
        return false;
    }

    if (!$userid) {
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (!record_exists('role_assignments', 'userid', $userid)) {    // Has no roles anywhere
        return false;
    }

    $rolesids = array(8, 11, 12, 18); 
/// If this user is assigned as an editing teacher anywhere then return true
    foreach ($rolesids as $roleid) {
        if (record_exists('role_assignments', 'roleid', $roleid, 'userid', $userid)) {
            // print_r($role); echo '1<hr>';
            return true;
        }
    }

/// Include admins if required
    if ($includeadmin) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/legacy:admin', $context, $userid, false)) {
            return true;
        }
    }

    return false;
}


function view_request($id, &$data1)
{        
   
    $strselect = "lastname as lastname1, firstname as firstname1, secondname as secondname1, pol, 
    birthday as birthday1, typedocuments as document1, serial as serial1, number as number1, 
    who_hands as who_hands1, when_hands as when_hands1, addressregistration";
    $request = get_record_select('monit_queue_request', "id = $id");
    $declarant = get_record_select('monit_queue_declarant', "id = $request->declarantid");
    $child = get_record_select('monit_queue_child', "id = $request->childid", $strselect);
    //print_object($child);
    $data = (array)$request + (array)$declarant + (array)$child;
    $data1 = (object)$data;
    $data1->isview = true; 
    // $edutype = get_record_select ('monit_school_type', "id=$data1->edutypeid", 'id, cod, name, tblname');
    // $data1->typeou = $edutype->cod;  
    if ($data1->edutypeid == 18)    {
        
        $data1->dou1 = $data1->oid;
        // print_object($data1); 
        $editform4 = new finish_form_dou();
    } else {
        $editform4 = new finish_form();
    }
    $editform4->display();                    
    // echo '<pre>'; print_r($data1); echo '</pre>';  
}                



function listbox_typeou($scriptname, $rid, $typeou)
{
    global $CFG;

    $typeoumenu = array();
	$typeoumenu['-'] = get_string('selecttypeou', 'block_mou_att').'...';

    $listedutypeids = '18, 1'; // , 17, 15, 16

 	$strsql = "SELECT id, name, cod, tblname FROM {$CFG->prefix}monit_school_type 
               WHERE id in ($listedutypeids)";
 	if($alltypeou = get_records_sql($strsql))   {
        foreach ($alltypeou as $typeou1) 	{
            $typeoumenu[$typeou1->cod] = $typeou1->name;
        }
    }
    // print_r($typeoumenu);    
    echo '<tr> <td>'.get_string('typeou', 'block_mou_att').': </td><td>';
    echo popup_form($scriptname, $typeoumenu, 'switchtypeou', $typeou, '', '', '', true);
  	echo '</td></tr>';
    
    return 1;
}


function listbox_ous($scriptname, $rid, $typeou, $oid, $yid)
{
    global $CFG;

    $outype = get_config_typeou($typeou);

 	$strsql = "SELECT id, rayonid, name  FROM {$CFG->prefix}{$outype->tblname}
				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid AND typeinstitution=$outype->id {$outype->where} 
				ORDER BY number";

	$schoolmenu = array();
	// echo $strsql . '<hr>';
    if ($arr_schools =  get_records_sql($strsql))	{
    	$schoolmenu[0] = $outype->strselect;
    	foreach ($arr_schools as $school) {
    	    $schoolname = mb_ereg_replace('дошкольное образовательное учреждение', 'ДОУ', $school->name);
            if ($schoolname)  $school->name = $schoolname;
    	    $schoolname = mb_ereg_replace('средняя общеобразовательная школа', 'СОШ', $school->name);
            if ($schoolname)  $school->name = $schoolname;
    	    $schoolname = mb_ereg_replace('основная общеобразовательная школа', 'ООШ', $school->name);
            if ($schoolname)  $school->name = $schoolname;
            
    		$len = strlen ($school->name);
    		if ($len > 200)  {
    			// $school->name = substr($school->name, 0, 200) . ' ...';
                
    			$school->name = substr($school->name,0,strrpos(substr($school->name,0, 210),' ')) . ' ...';
    		}
    		$schoolmenu[$school->id] =$school->name;
    	}
     	echo '<tr><td>'.$outype->strtitle.':</td><td>';
    	echo popup_form($scriptname, $schoolmenu, 'switchou', $oid, '', '', '', true);
    	echo '</td></tr>';
    }    
}

function get_max_number_inqueue($field, $strselect)
{
    global $CFG;
    $maxnumber = 1;
    $strsql = "SELECT max($field) as mn FROM {$CFG->prefix}monit_queue_request WHERE ";
    $strsql .= $strselect;
               
    // echo $strsql;
    if ($number = get_record_sql($strsql))  {
        $maxnumber = $number->mn + 1;
    }
    return $maxnumber;
}


function renumber_queue($maxnumber, $strselect, $field)
{
    if ($requests = get_records_select('monit_queue_request', $strselect, $field, 'id, number, numberinyear')) {
        $i = $maxnumber + 1;
        foreach ($requests as $req1)    {
            set_field('monit_queue_request', $field, $i, 'id', $req1->id);
            $i++; 
        }
    }    
}


function change_status ($statusid, $id)
{
    global $CFG;
     
    if ($request = get_record_select('monit_queue_request', "id = $id", 'id, number, edutypeid, oid, benefitsids, status, deleted, birthyear')) {
        
        switch ($statusid) {
            case STATUS_DENIED:
                    set_field('monit_queue_request', 'number', NUMBER_DENIED, 'id', $id);
            break;
            case STATUS_PUTINTOQUEUE:
                if ($request->benefitsids == '0') {
                    // если нет льгот, то присваиваем очередной номер, следующий за максимальным
                    $maxnumber = get_max_number_inqueue('number', "edutypeid=$request->edutypeid AND oid=$request->oid  AND number < " . NUMBER_DENIED);
                    set_field('monit_queue_request', 'number', $maxnumber, 'id', $id);
                    
                    // находим номер в очереди по году рождения
                    
                    $maxnumber = get_max_number_inqueue('numberinyear', "edutypeid=$request->edutypeid AND oid=$request->oid  AND birthyear = $request->birthyear AND numberinyear < " . NUMBER_DENIED);
                    set_field('monit_queue_request', 'numberinyear', $maxnumber, 'id', $id);
                    
                } else {
                    // если льготы есть, то присваиваем номер в льготной части очереди
                    $maxnumber = get_max_number_inqueue('number', "edutypeid=$request->edutypeid AND oid=$request->oid AND benefitsids <> '0' AND number < " . NUMBER_DENIED);
                    set_field('monit_queue_request', 'number', $maxnumber, 'id', $id);
                    // перенумеровываем нельготников
                    $strselect = "edutypeid=$request->edutypeid AND oid=$request->oid AND benefitsids = '0' AND status >= 14 AND number < " . NUMBER_DENIED;
                    renumber_queue($maxnumber, $strselect, 'number');

                    // находим номер в очереди по году рождения
                    
                    $maxnumber = get_max_number_inqueue('numberinyear', "edutypeid=$request->edutypeid AND oid=$request->oid AND benefitsids <> '0' AND birthyear = $request->birthyear AND numberinyear < " . NUMBER_DENIED);
                    set_field('monit_queue_request', 'numberinyear', $maxnumber, 'id', $id);
                    $strselect = "edutypeid=$request->edutypeid AND oid=$request->oid AND benefitsids = '0' AND status >= 14 AND birthyear = $request->birthyear AND numberinyear < " . NUMBER_DENIED;
                    renumber_queue($maxnumber, $strselect, 'numberinyear');
                }   
            break;
            case STATUS_SATISFIED:
                    renumber_queue_after_delete($id);
                    set_field('monit_queue_request', 'number', NUMBER_SATISFIED, 'id', $id);
                    set_field('monit_queue_request', 'numberinyear', NUMBER_SATISFIED, 'id', $id);
                    /*
                    if ($requests = get_records_select('monit_queue_request', "edutypeid=$request->edutypeid AND oid = $request->oid and number > 0 and number < " . NUMBER_DENIED, 'number', 'id, number')) {
                        $i = 1;
                        foreach ($requests as $req1)    {
                            if ($i != $req1->number) {
                                set_field('monit_queue_request', 'number', $i, 'id', $req1->id);
                            }    
                            $i++; 
                        }
                    }
                    */


                    /*
                    if ($requests = get_records_select('monit_queue_request', "edutypeid=$request->edutypeid AND oid = $request->oid AND birthyear = $request->birthyear and numberinyear > 0 and numberinyear < " . NUMBER_DENIED, 'numberinyear', 'id, numberinyear')) {
                        $i = 1;
                        foreach ($requests as $req1)    {
                            if ($i != $req1->numberinyear) {
                                set_field('monit_queue_request', 'numberinyear', $i, 'id', $req1->id);
                            }    
                            $i++; 
                        }
                    }
                    */
            break;
        }
           
        set_field('monit_queue_request', 'status', $statusid, 'id', $id);
    }     
}    


function get_birthyear_child($birthday)
{
   // $ret = date('Y'); 
   $birthyear = date('Y-m-d', $birthday);
   if ($by = get_record_select('monit_queue_birthyear', "datestart < '$birthyear' AND '$birthyear' < dateend"))  {
       $ret = $by->id; 
   } else {
       $ret = date('Y', $birthday);   
   }
   
   return $ret; 
}    


function listbox_birth_years($scriptname, $nyear)
{	
	global $CFG;
	
 	$yearmenu = array();
 	$yearmenu[0] = 'Все возрасты';
	if ($years = get_records_select ("monit_queue_birthyear", "rayonid=1", 'id'))  {
    	foreach ($years as $year)	{
	        $yearmenu[$year->id] = $year->name. ' г.р.';
	    }
	}	    
    
	echo '<tr><td>'.get_string('age', 'block_monitoring').':</td><td>';
	popup_form($scriptname, $yearmenu, "switchyear", $nyear, "", "", "", false);
	echo '</td></tr>';	
	return 1;
}


function renumber_queue_after_delete($id)
{
    if ($request = get_record_select('monit_queue_request', "id = $id", 'id, number, numberinyear, edutypeid, oid, benefitsids, status, deleted, birthyear')) {
        $strselect = "edutypeid=$request->edutypeid AND oid=$request->oid AND status >= 14 AND number > $request->number AND number < " . NUMBER_DENIED;
        if ($requests = get_records_select('monit_queue_request', $strselect, 'number', 'id, number')) {
            $i = $request->number;
            foreach ($requests as $req1)    {
                set_field('monit_queue_request', 'number', $i, 'id', $req1->id);
                $i++; 
            }
        }
        
        $strselect = "edutypeid=$request->edutypeid AND oid=$request->oid AND status >= 14 AND birthyear = $request->birthyear AND numberinyear > $request->numberinyear AND numberinyear < " . NUMBER_DENIED;
        if ($requests = get_records_select('monit_queue_request', $strselect, 'numberinyear', 'id, numberinyear')) {
            $i = $request->numberinyear;
            foreach ($requests as $req1)    {
                set_field('monit_queue_request', 'numberinyear', $i, 'id', $req1->id);
                $i++; 
            }
        }    
    }            
}


function dativecase($lastname, $firstname, $secondname, $sex=-1) 
{
    global $user;
    
	$lastname = trim($lastname);
	$firstname = trim($firstname);
	$secondname = trim($secondname);

	if (!empty($lastname) && !empty($firstname) && !empty($secondname)) {
		if($sex == -1) {
			$user->sex = 0;
			if (mb_substr($secondname, -1, 1, 'UTF-8') == 'ч')	{
				$user->sex = 1;
			}
		}
		if ($user->sex == 1)	{
# Склонение фамилии мужчины:
			switch (mb_substr($lastname, -2, 2, 'UTF-8'))	{
				case 'ха':
					$lastname = mb_substr($lastname, 0, -2, 'UTF-8').'хи';
				break;
				default:
					switch (mb_substr($lastname, -1, 1, 'UTF-8')) {
						case 'е': case 'о': case 'и': case 'я': case 'а':
						break;
						case 'й':
							$lastname = mb_substr($lastname, 0, -1, 'UTF-8').'ому';
						break;
						case 'ь':
							$lastname = mb_substr($lastname, 0, -1, 'UTF-8').'ю';
						break;
						default:
							$lastname = $lastname.'у';
						break;
					}
				break;
			}

# Склонение мужского имени:
			switch (mb_substr($firstname, -1, 1, 'UTF-8')) {
				case 'л':
					$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'лу';
				break;
				case 'а': case 'я':
					if (mb_substr($firstname, -2, 1, 'UTF-8') == 'и') {
						$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'и';
					} else {
						$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'е';
					}
				break;
				case 'й': case 'ь':
					$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'ю';
				break;
				default:
					$firstname = $firstname.'у';
				break;
			}
# Склонение отчества
			$secondname = $secondname.'у';
		} else {
# Склоенение женской фамилии
			switch (mb_substr($lastname, -1, 1, 'UTF-8'))	{
				case 'о': case 'и': case 'б': case 'в': case 'г':
				case 'д': case 'ж': case 'з': case 'к': case 'л':
				case 'м': case 'н': case 'п': case 'р': case 'с':
				case 'т': case 'ф': case 'х': case 'ц': case 'ч':
				case 'ш': case 'щ': case 'ь':
				break;
				case 'я':
					$lastname = mb_substr($lastname, 0, -1, 'UTF-8').'ой';
				default:
					$lastname = mb_substr($lastname, 0, -1, 'UTF-8').'ой';
				break;
			}
# Склонение женского имени:
			switch (mb_substr($firstname, -1, 1, 'UTF-8')) {
				case 'а': case 'я':
					if (mb_substr($firstname, -2, 1, 'UTF-8') == 'и') {
						$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'и';
					} else {
						$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'е';
					}
				break;
				case 'ь':
					$firstname = mb_substr($firstname, 0, -1, 'UTF-8').'и';
				break;
			}
# Склонение женского отчества
			$secondname = mb_substr($secondname, 0, -1, 'UTF-8').'е';
		}
		return "$lastname $firstname $secondname";
	}
}


function roditelcase($lastname, $firstname, $secondname, &$sex) 
{
    global $user;
    
	$lastname = trim($lastname);
	$firstname = trim($firstname);
	$secondname = trim($secondname);
    
    $roditelname = "$lastname $firstname $secondname";

	if (!empty($lastname) && !empty($firstname) && !empty($secondname)) {
     
        $textlib = textlib_get_instance();
        $ln = $textlib->convert($lastname, 'utf-8', 'win1251');
        $fn = $textlib->convert($firstname, 'utf-8', 'win1251');
        $sn = $textlib->convert($secondname, 'utf-8', 'win1251');      
    
        $a = new RussianNameProcessor($ln, $fn, $sn);      // годится обычная форма
        // echo $a->lastName($a->gcaseRod);
        // echo '<pre>'; print_r($a); echo '</pre>'; 
        $roditelname = $a->fullName($a->gcaseRod);
        $sex = $a->getSex();
    
        $roditelname = $textlib->convert($roditelname, 'win1251');
   
        unset ($textlib);
   }
   
   return $roditelname;     
}     


function get_edit_capability_region_rayon_queue($rid, &$edit_capability_region, &$edit_capability_rayon)    
{
    $context_region = get_context_instance(CONTEXT_REGION_ATT, 1);
    if (!$edit_capability_region = has_capability('block/monitoring:editqueue', $context_region))    {
       if ($rid == 0)   {
            $edit_capability_rayon = false;
            return false;
       } 
       $context_rayon = get_context_instance(CONTEXT_RAYON, $rid);
	   if (!$edit_capability_rayon = has_capability('block/monitoring:editqueue', $context_rayon))  {
            $context_rayon = get_context_instance(CONTEXT_RAYON_COLLEGE, $rid);
	        if (!$edit_capability_rayon = has_capability('block/monitoring:editqueue', $context_rayon))  {
                 $context_rayon = get_context_instance(CONTEXT_RAYON_UDOD, $rid);
          	     if (!$edit_capability_rayon = has_capability('block/monitoring:editqueue', $context_rayon))  {
       	               $context_rayon = get_context_instance(CONTEXT_RAYON_DOU, $rid);
	                   if (!$edit_capability_rayon = has_capability('block/monitoring:editqueue', $context_rayon))  {
	                       return false;
                       }
                 }
            }                
	   }
    }
}
     

?>
