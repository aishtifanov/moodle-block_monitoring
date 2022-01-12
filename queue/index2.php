<?php // $Id: index2.php,v 1.10 2012/06/04 10:39:35 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    
    require_login();
    
    $rid = optional_param('rid', 0, PARAM_INT);          // Rayon id
    $tab = optional_param('tab', 'request');          // Rayon id
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $who = optional_param('who', 'user');
    $useridd = optional_param('useridd', 0, PARAM_INT);       // User id from operotor 
    $oid = optional_param('oid', 0, PARAM_INT);       // OU id

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

    if ($data1 = data_submitted())  {
        $outype = get_config_typeou($typeou);
        // echo '0 - index2.php - data1 <pre>';  print_r($data1); echo '</pre>';
        /*
        if (!isset($data1->who))    {
            $data1->who = $who;
        }
        if (!isset($data1->useridd))    {
            $data1->useridd = $useridd;
        }
        */
    }    

    
    // echo $action . '!!!!!!' . '<br>';
    if ($action == 'step1' && isset($data1->_qf__pupil_form)) {
        echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
    	listbox_rayons("index.php?useridd=$useridd&who=$who&yid=$yid&rid=", $rid, true);
        listbox_typeou("index.php?useridd=$useridd&who=$who&yid=$yid&rid=$rid&typeou=", $rid, $typeou);
        if ($typeou != '-')	{
           $outype = get_config_typeou($typeou); 
           switch ($typeou) {
                case '20': $oid =-1;
                break;
                
                default: listbox_ous("index.php?useridd=$useridd&who=$who&yid=$yid&rid=$rid&typeou=$typeou&oid=", $rid, $typeou, $oid, $yid);  
           } 
    	   
        }   
    	echo '</table>';
        
        if ($oid == 0)   {
             print_footer();
             exit();
        }
    }    


    if ($who == 'oper') {
        $user = get_record_select('user', "id = $useridd", 'id, lastname, firstname, email, phone1, phone2');
        print_heading('Регистрация заявления от имени заявителя: ' . fullname($user), 'center', 4);
    }
    
    $editform2 = new declarant_form();

    //if ($editform2->is_cancelled())	{
         // redirect("queue.php?rid=$rid&yid=$yid&uid=$uid", '', 30);
    if (isset($data1->backbutton))  {
        if(isset($data1->_qf__finish_form)) {
            $editform2->set_data($data1);
            $editform2->display();
        } else {       // $data1->_qf__declarant_form
            $editform1 = new pupil_form('index.php');
            $editform1->set_data($data1);
            $editform1->display();
        }    
    } else {
        if(isset($data1->_qf__finish_form)) {
            $editform3 = new finish_form();
            if ($data2 = $editform3->get_data()) 	{
                if ($requestid = save_request($data2))  {
                    /*
                    $outype = get_config_typeou($data2->typeou);
       				$role_declarant = get_record('role', 'shortname', 'declarant');
    			    $ctx = get_context_instance($outype->contextqueue, $requestid);
    	     		if (!role_assign($role_declarant->id, $USER->id, 0, $ctx->id))	{
    					notify("Роль заявителя для $USER->id не назначена.");
    			    }
                    */
                    notice (message_thanks($requestid), "my.php?rid=$rid&yid=$yid&uid=$uid");
                    // redirect("queue.php?rid=$rid&yid=$yid&uid=$uid", '', 30);
                }
            }
        } else  if (isset($data1->_qf__declarant_form)) {
            if ($data2 = $editform2->get_data()) 	{
                $editform3 = new finish_form();
                $editform3->display();
            } else {
                $editform2->display();
            }    
        } else {    
            $editform2->display();
        }
    }

        // echo '<pre>'; print_r($data2); echo '</pre>';
/*        if (check_data($data2)) {
        } else {
            notify (get_string('childexists', 'block_monitoring', $data2->lastname1 . ' ' . $data2->firstname1));
        }
*/        
    
    print_footer();


?>
