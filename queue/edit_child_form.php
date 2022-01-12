<?php  // $Id: edit_child_form.php,v 1.3 2012/06/21 09:05:18 shtifanov Exp $

require_once($CFG->libdir.'/formslib.php');

class edit_child_form extends moodleform 
{
    
    function definition() {

        global $CFG, $USER, $rid, $yid, $typeou, $oid, $child, $request;
        
        $mform =& $this->_form;

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
        $mform->addElement('hidden', 'id', $child->id);  $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'typeou', $typeou);  $mform->setType('typeou', PARAM_TEXT);
        $mform->addElement('hidden', 'oid', $oid);  $mform->setType('oid', PARAM_INT);
        
        $mform->addElement('hidden', 'action', 'edit');  $mform->setType('action', PARAM_TEXT);
                
        $mform->addElement('header','', get_string('childata', 'block_monitoring'));
        
        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="30" size="30"');
        $mform->addRule('lastname', get_string('missingname'), 'required', null, 'client');
        $mform->setType('lastname', PARAM_TEXT);

        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="30" size="30"');
        $mform->addRule('firstname', get_string('missingname'), 'required', null, 'client');
        $mform->setType('firstname', PARAM_TEXT);

        $mform->addElement('text', 'secondname', get_string('secondname', 'block_monitoring'), 'maxlength="30" size="30"');
        $mform->addRule('secondname', get_string('missingname'), 'required', null, 'client');
        $mform->setType('secondname', PARAM_TEXT);

        $choices = array();
        $choices['1'] = get_string('pol1', 'block_mou_school');
        $choices['2'] = get_string('pol2', 'block_mou_school');
        $mform->addElement('select', 'pol', get_string('pol', 'block_mou_ege'), $choices);
        $mform->addRule('pol', get_string('missingname'), 'required', null, 'client');
        $mform->setDefault('pol', 1);

		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 18;
		$mform->addElement('date_selector', 'birthday', get_string('birthday', 'block_mou_school'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		$mform->addRule('birthday', get_string('missingname'), 'required', null, 'client');
		// $mform->setType('birthday', PARAM_INT);
		$mform->setDefault('birthday', time() - YEARSECS*9); 

        $mform->addElement('static', '', '');		
        $choices = array();
        $choices['1'] = get_string('typedocuments1', 'block_mou_ege');
        $choices['2'] = get_string('typedocuments2', 'block_mou_ege');
        $mform->addElement('select', 'typedocuments', get_string('typedocuments', 'block_mou_ege'), $choices);
        $mform->addRule('typedocuments', get_string('missingname'), 'required', null, 'client');

        $mform->addElement('text', 'serial', get_string('serial', 'block_mou_ege'), 'maxlength="6" size="6"');
        $mform->addRule('serial', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('serial', PARAM_TEXT);

        $mform->addElement('text', 'number', get_string('number', 'block_mou_ege'), 'maxlength="10" size="10"');
        $mform->addRule('number', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('number', PARAM_TEXT);
        
		$stopyear = date('Y'); // - 18;
        $startyear = $stopyear - 18;
		$mform->addElement('date_selector', 'when_hands', get_string('when_hands', 'block_mou_ege'),
							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
		$mform->addRule('when_hands', get_string('missingname'), 'required', null, 'client');
		// $mform->setType('birthday', PARAM_INT);
		//$mform->setDefault('when_hands', time() - YEARSECS*9); 

        $mform->addElement('text', 'who_hands', get_string('who_hands', 'block_mou_ege'), 'maxlength="100" size="70"');
        $mform->addRule('who_hands', get_string('missingname'), 'required', null, 'client');        
        $mform->setType('who_hands', PARAM_TEXT);

        
        // $mform->addElement('header','', get_string('schooltab5', 'block_monitoring'));
        $mform->addElement('static', '', '');
        
 		$mform->addElement('textarea', 'addressregistration', get_string('regaddress', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        $mform->addRule('addressregistration', '', 'required', null, 'client');

        $mform->addElement('checkbox', 'isregequalhome', get_string('isregequalhome', 'block_monitoring'));
        
 		$mform->addElement('textarea', 'addresshome', get_string('addresshome', 'block_monitoring'), array('rows'=>5, 'cols'=>50));
        
        
        if ($request)   {
            $mform->addElement('header','', get_string('infolgoti', 'block_monitoring'));
            
            $mform->addElement('static', '', '', get_string('benefit', 'block_monitoring'));
            $rayon = get_record_select('monit_rayon', "id = $rid", 'id, benefitsids');
            $rayonbenefits = array();
            if ($rayon->benefitsids != '0') {
                $rayonbenefits = explode(',', $rayon->benefitsids); 
            }
            
            $childbenefits = explode(',', $request->benefitsids);
            $benefits = get_records('monit_queue_benefit');
            foreach ($benefits as $benefit) {
                if (!empty($rayonbenefits))  {
                    if (!in_array($benefit->id, $rayonbenefits)) {
                        continue;
                    } 
                }
                $fname = 'b_'.$benefit->id;
                $mform->addElement('checkbox', $fname, $benefit->name);
                if (in_array($benefit->id, $childbenefits)) {
                    $mform->setDefault($fname, true);
                }
            }    
    
            $choices = array(get_string('no'));
            $healths = get_records('monit_queue_health');
            foreach ($healths as $health) {
                $choices[$health->id] = $health->name;
            }    
            $mform->addElement('select', 'healthid', get_string('health', 'block_monitoring'), $choices);
            $mform->setDefault('healthid', $request->healthid);
    
    
            $startyear = date('Y');
            $stopyear = $startyear + 5;
    		$mform->addElement('date_selector', 'dateenrollment', get_string('dateenrollment', 'block_monitoring'),
    							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
    		// $mform->addRule('dateenrollment', get_string('missingname'), 'required', null, 'client');
    		$mform->setDefault('dateenrollment', $request->dateenrollment);
        }    
        
        if ($child)  {
            foreach ($child as $field => $value)    {
                if ($field == 'when_hands') {
                    if ($value == '0000-00-00') {
                        $mform->setDefault($field, time());    
                    } else {
                        list ($y, $m, $d) = explode ('-', $value);
                        $mform->setDefault($field, get_timestamp_from_date($d, $m, $y)); 
                    }    
                } else {
                    $mform->setDefault($field, $value);
                }    
            } 
        }


        $this->add_action_buttons(true, get_string('savechanges'));
    }

}
?>
