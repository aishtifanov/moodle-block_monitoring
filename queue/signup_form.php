<?php  // $Id: signup_form.php,v 1.1 2012/03/13 13:58:59 shtifanov Exp $

require_once($CFG->libdir.'/formslib.php');

class login_signup_form extends moodleform {
    function definition() {
        global $USER, $CFG, $rid, $yid;

        $mform =& $this->_form;

        $login = 'z'.time();
        $email = $login . '@temp.ru';  
        
        $mform->addElement('header', '', get_string('createuserandpass'), '');

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
		$mform->addElement('hidden', 'action', 'new');  $mform->setType('action', PARAM_TEXT);

        $mform->addElement('text', 'username', get_string('username'), 'size="30"');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');
        $mform->setDefault('username', $login);

        $mform->addElement('text', 'password', get_string('password'), 'size="30"');
        $mform->setType('password', PARAM_NOTAGS);
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        $mform->setDefault('password', substr($login, 4, 6));

        $mform->addElement('header', '', get_string('declarantdata', 'block_monitoring'),'');

        $mform->addElement('text', 'email', get_string('email'), 'size="25"');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setDefault('email', $email);

        $mform->addElement('text', 'email2', get_string('emailagain'), 'size="25"');
        $mform->setType('email2', PARAM_NOTAGS);
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setDefault('email2', $email);

        $mform->addElement('text', 'lastname', get_string('lastname'), 'size="25"');
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('missinglastname'), 'required', null, 'client');
        
        $mform->addElement('text', 'firstname', get_string('firstname'), 'size="25"');
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('missingfirstname'), 'required', null, 'client');

        $mform->addElement('text', 'secondname', get_string('secondname', 'block_monitoring'), 'size="25"');
        $mform->setType('secondname', PARAM_TEXT);
        $mform->addRule('secondname', get_string('missingsecondname'), 'required', null, 'client');

        $mform->addElement('text', 'city', get_string('settlement'), 'size="50"');
        $mform->setType('city', PARAM_TEXT);
        $mform->addRule('city', get_string('missingcity'), 'required', null, 'client');
        if ($rid > 0)   {
            $rayon = get_record('monit_rayon', 'id', $rid);
            $mform->setDefault('city', $rayon->shortname);            
        }
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);
        // $mform->addRule('phone1', get_string('missingname'), 'required', null, 'client');

        $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="20"');
        $mform->setType('phone2', PARAM_TEXT);
        // $mform->addRule('phone1', get_string('missingname'), 'required', null, 'client');

        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        global $CFG;
        $mform =& $this->_form;

        $mform->applyFilter('username', 'moodle_strtolower');
        $mform->applyFilter('username', 'trim');
    }

    function validation($data) {
        global $CFG;
        $errors = array();

        if (record_exists('user', 'username', $data['username'], 'mnethostid', $CFG->mnet_localhost_id)) {
            $errors['username'] = get_string('usernameexists');
        } 

        if (! validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else if (record_exists('user', 'email', $data['email'])) {
            $errors['email'] = get_string('emailexists').' <a href="forgot_password.php">'.get_string('newpassword').'?</a>';
        }
        if (empty($data['email2'])) {
            $errors['email2'] = get_string('missingemail');

        } else if ($data['email2'] != $data['email']) {
            $errors['email2'] = get_string('invalidemail');
        }
        if (!isset($errors['email'])) {
            if ($err = email_is_not_allowed($data['email'])) {
                $errors['email'] = $err;
            }

        }


        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }


    }
}

?>
