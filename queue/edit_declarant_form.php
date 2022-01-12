<?php  // $Id: edit_declarant_form.php,v 1.4 2012/06/28 10:35:03 shtifanov Exp $

require_once($CFG->libdir.'/formslib.php');

class edit_declarant_form extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $declarant;
        
        $mform =& $this->_form;

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $mform->addElement('hidden', 'id', $declarant->id);  $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'edit');  $mform->setType('action', PARAM_TEXT);
                
        $mform->addElement('header','', get_string('declarantdata', 'block_monitoring'));
        
        $user = get_record_select('user', "id = $declarant->userid", 'id, username, password, lastname, firstname, email, phone1, phone2');
   	    list($f,$s) = explode(' ', $user->firstname);
        $user->firstname = $f;
        $user->secondname = $s;

        
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

        $mform->addElement('static', '', '');
                
 		$mform->addElement('textarea', 'addressfact', get_string('regaddress', 'block_monitoring'), array('rows'=>5, 'cols'=>55));
        $mform->addRule('addressfact', '', 'required', null, 'client');

        $mform->addElement('text', 'workplace', get_string('workplace', 'block_monitoring'), 'maxlength="250" size="70"');
        $mform->addRule('workplace', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('workplace', PARAM_TEXT);

        $mform->addElement('text', 'email', get_string('email'), 'size="25"');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setDefault('email', $user->email);
        
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);
        $mform->setDefault('phone1', $user->phone1);

        $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="20"');
        $mform->setType('phone2', PARAM_TEXT);
        $mform->setDefault('phone2', $user->phone2);

        $mform->addElement('static', '', '');

        $mform->addElement('text', 'username', get_string('username'), 'maxlength="20" size="20"');
        $mform->setType('username', PARAM_TEXT);
        $mform->setDefault('username', $user->username);

        $strpsw = get_string('password') . "<small> (".get_string("leavetokeep").")</small>";
        
        $mform->addElement('text', 'newpassword', $strpsw, 'maxlength="20" size="20"');
        $mform->setType('newpassword', PARAM_TEXT);
        // $mform->setDefault('password', $user->password);
        
        if ($declarant)  {
            foreach ($declarant as $field => $value)    {
                $mform->setDefault($field, $value);
            } 
        }


        $this->add_action_buttons(true, get_string('savechanges'));
    }

}


?>
