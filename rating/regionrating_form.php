<?php  // $Id: regionrating_form.php,v 1.4 2012/10/18 10:40:41 shtifanov Exp $

require_once($CFG->libdir.'/formslib.php');

class regionrating_form extends moodleform {
	
    function definition() {

        global $yid;

        $mform =& $this->_form;
        
        // $mform->addElement('header','general', get_string('regionrating', 'block_monitoring'));

		$year = get_record('monit_years', 'id', $yid);
		$ayears = explode("/", $year->name);
		$plugin = 'rating'.$ayears[0];
		
        
        
		if ($indicators = get_records('config_plugins', 'plugin', $plugin, 'id'))	{
		  
            // echo '<pre>'; print_r($indicators); echo '</pre>';
            
			$table_html = '<table cellspacing="0" border=2 cellpadding="10" align="center" class="moutable">';
	    	$table_html .= '<tr><th class=header>'.get_string('symbolnumber', 'block_monitoring').'</th>';
			$table_html .= '<th class=header>' . get_string('nameofpokazatel', 'block_monitoring'). '</th>';
			$table_html .= '<th class=header>' . get_string('valueofpokazatel', 'block_monitoring') . '</th></tr>';
	 
			$mform->addElement('html', $table_html);
			$num = 1;

			foreach ($indicators as $indicator)	{
				$name = trim($indicator->name);
				$parts = explode('#', $indicator->value);
				$part1  = trim($parts[1]);
			
				$mform->addElement('html', "<tr><td>$num.</td><td>$part1</td><td>");
                if ($name == 'timeaccessdenied' || $name == 'doutimeaccessdenied')    {
            		$stopyear = date('Y');
                    $startyear = $stopyear - $yid;
            		$mform->addElement('date_time_selector', $name, '',
            							array('startyear'=>$startyear, 'stopyear'=>$stopyear, 'timezone'=>99, 'applydst'=>true, 'optional'=>false));
            		// $mform->setDefault($name, time()); 
                    
                } else {    
    		        $mform->addElement('text', $name,  '', 'maxlength="10" size="7"');
    		        // $mform->addRule($name, get_string('missingname'), 'numeric', null, 'client');
    		        // $mform->addRule($name, get_string('missingname'), 'nonzero', null, 'client');
    		        $mform->setType($name, PARAM_NUMBER);
                }    
		        $mform->addElement('html', "</td></tr>");
		        // $mform->addElement('static', '', "<hr>");
		        $num++;
		    }
			    
	  		$mform->addElement('html', "</table><div align=center>");
	  		
			$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
	
	        $this->add_action_buttons();
	        
	        $mform->addElement('html', "</div>");

		}				
    }

    function validation($data) {
        $errors = array();

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }

}
?>
