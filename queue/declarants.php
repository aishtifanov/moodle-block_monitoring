<?php // $Id: declarants.php,v 1.9 2012/10/12 09:04:29 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    require_once('signup_form.php');
    require_once('edit_declarant_form.php');
    
    require_login();

    $rid = optional_param('rid', 0, PARAM_INT);    // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);    // OU id
    $yid = optional_param('yid', 0, PARAM_INT);    // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', 'view');
    $tab = optional_param('tab', 'declarants');          // Rayon id
    $level = optional_param('level', 'ou');          // Rayon id
    $id = optional_param('id', 0, PARAM_INT);    // declarants id
    $statusid = optional_param('status', 9, PARAM_INT);    // Request id
    $confirm = optional_param('confirm');
    $numsym = optional_param('numsym', 0, PARAM_INT);  	  //

    $arrRus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'К', 'Л', 'М',
                  'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Э', 'Ю', 'Я');

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
    $strdecl = get_string('declarants', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strdecl, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");

    $strlistrayons =  listbox_rayons_att("declarants.php?yid=$yid&rid=", $rid);
    include('tabs.php');

    get_edit_capability_region_rayon_queue($rid, $edit_capability_region, $edit_capability_rayon);
    $edit_capability_ou = false;
    if (!$edit_capability_region && !$edit_capability_rayon)  {
       	$strlisttypeou =  listbox_typeou_att("declarants.php?rid=$rid&amp;yid=$yid&amp;oid=0&amp;typeou=", $rid, $typeou, true);
        $strlistou = listbox_ou_att("declarants.php?rid=$rid&amp;yid=$yid&amp;typeou=$typeou&amp;oid=", $rid, $typeou, $oid, $yid);    
        $edutype = get_config_typeou($typeou);
    	$context = get_context_instance($edutype->context, $oid);
        $edit_capability_ou = has_capability('block/monitoring:editqueue', $context);
    }    

    if ($edit_capability_ou || $edit_capability_region || $edit_capability_rayon)  {
        
        switch ($action)    {
            case 'view':
                    	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
                    	echo $strlistrayons;
                        echo '</table>';        
           
                    	if ($rid != 0)	{
                            echo '<div align=center>';
                            $options = array('rid' => $rid, 'who' => 'oper', 'action' => 'new');
                     	    print_single_button("declarants.php", $options, 'Зарегистрировать нового заявителя');
                            echo '</div><p></p>';

                            $toprow = array();
                            foreach ($arrRus as $key => $aRus)	{
                               $toprow[] = new tabobject($key, "declarants.php?rid=$rid&numsym=".$key, $aRus);
                        	}	      
                            $toprow[] = new tabobject($key, "declarants.php?rid=$rid&numsym=99", 'Все');
                            $tabs = array($toprow);
                            print_tabs($tabs, $numsym, NULL, NULL);
                            
                            $table = table_declaran_rayon ($rid, $numsym, $oid);
                            print_color_table($table);
                            /*
                            echo '<div align=center>';
                            $options = array('rid' => $rid, 'who' => 'oper', 'action' => 'new');
                     	    print_single_button("declarants.php", $options, 'Зарегистрировать заявителя');
                            echo '</div>';
                            */

                        }
            break;
            case 'new':
                        $redirlink = "declarants.php?rid=$rid&action=view";
                        
                        $mform_signup = new login_signup_form();
                    
                        if ($mform_signup->is_cancelled()) {
                            redirect($redirlink, '', 0);
                        } else if ($user = $mform_signup->get_data()) {
                            $user->confirmed   = 1;
                            $user->lang        = 'ru_utf8';//current_language();
                            $user->country     = 'RU';
                            $user->firstaccess = time();
                            $user->mnethostid  = $CFG->mnet_localhost_id;
                            $user->secret      = random_string(15);
                            $user->auth        = $CFG->registerauth;
                            $user->firstname   .= ' ' . $user->secondname; 
                    
                            $user->password = hash_internal_user_password($user->password);
                    
                            if (!($user->id = insert_record('user', $user)) ) {
                                error('Ошибка! Новый пользователь не зарегистрирован.', $redirlink);
                            } else {
                                $rec->rayonid = $rid;
                                $rec->userid = $user->id;
                                $rec->timemodified = time();
                                $rec->modifierid = $USER->id;
                                if (!$declarantid = insert_record('monit_queue_declarant', $rec))  {
                                    error('Ошибка при сохранении данных заявителя.', $redirlink);
                                }
                                redirect($redirlink, 'Новый пользователь успешно зарегистрирован.', 30);
                            }
                        }
                    
                        $mform_signup->display();

            break;
            case 'edit':
                        if ($declarant = get_record_select('monit_queue_declarant', "id = $id"))  {
                            $redirlink = "declarants.php?rid=$rid&action=view";
                        
                            $editform = new edit_declarant_form();
                    
                            if ($editform->is_cancelled()) {
                                redirect($redirlink, '', 0);
                            } else if ($frm = $editform->get_data()) {
                                $rec->id = $declarant->userid;
                                $rec->lastname = trim($frm->lastname);
                                $rec->firstname = trim($frm->firstname);
                                $rec->firstname .= ' ' . trim ($frm->secondname);
                                $rec->email =  $frm->email;
                                $rec->phone1 =  $frm->phone1;
                                $rec->phone2 =  $frm->phone2;
                                
                                if (isset($frm->username)) {
                                    $rec->username = moodle_strtolower($frm->username);
                                }
                                
                                if (!empty($frm->newpassword))  {
                                    $rec->password = md5($frm->newpassword);
                                }
                                
                                if (!update_record('user', $rec))   {
                                    error('Ошибка при обновлении данных пользователя.', $redirlink);
                                }
                                
                                $frm->id = $id;
                                $frm->timemodified = time();
                                if (!update_record('monit_queue_declarant', $frm))  {
                                    error('Ошибка при сохранении данных заявителя.', $redirlink);
                                }
                                    redirect($redirlink, 'Данные заявителя успешно обновлены.', 3);
                             }
                             $editform->display();       
                        }
                        
            break;
            case 'del': 
            if ($edit_capability_region || $edit_capability_rayon)  {
                        if ($declarant = get_record_select('monit_queue_declarant', "id = $id", 'id, userid'))  {
                            $redirlink = "id=$id&rid=$rid&yid=$yid";                
                            if ($user = get_record_select('user', "id = $declarant->userid and deleted=0", 'id, username, lastname, firstname')) {
                                $fn = fullname ($user);
                                if (isset($confirm)) {
                                    $updateuser = new object();
                                    $updateuser->id           = $user->id;
                                    $updateuser->deleted      = 1;
                                    $updateuser->username     = $user->username . '_' . time();  // Remember it just in case
                                    $updateuser->email        = '';               // Clear this field to free it up
                                    $updateuser->idnumber     = '';               // Clear this field to free it up
                                    $updateuser->timemodified = time();
                                    if (update_record('user', $updateuser)) {
                                        role_unassign(0, $user->id);
                                  		delete_records('monit_queue_declarant', 'userid', $declarant->userid);
                                        delete_records('monit_queue_request', 'declarantid', $declarant->id);
                        		   		redirect("declarants.php?$redirlink", get_string('deletedactivity', '', $fn), 3);
                                    } else {
                                   		redirect("declarants.php?$redirlink", get_string('deletednot', '', $fn), 5);
                                    }
                                    
                               } else {  
                                   $message = "<strong>Удалить карточку заявителя '$fn' и все его заявления?</strong>";
              	                   notice_yesno($message, "declarants.php?confirm=1&action=$action&$redirlink", "declarants.php?$redirlink");
                                   print_footer();
                                   exit(0);
                               }
                            }
                        }  
            }                        
            break;
        }                        
                         
    }    
    
    print_footer();



function table_declaran_rayon ($rid, $numsym, $oid=0)
{
	global $CFG, $USER, $edutype, $edit_capability_rayon, $edit_capability_ou, $view_capability_ou, $edit_capability_region, $arrRus;

    $table->head  = array ('', get_string('fullname'), get_string('haverequest','block_monitoring'), get_string('username'), get_string('email', 'block_monitoring'), get_string('phone'),
                            get_string('lastaccess'), get_string('action', 'block_monitoring'));

	$table->align = array ('center', 'left', 'center', 'center',  "center",  'center', 'center', 'center');
	$table->columnwidth = array (10, 7, 10, 10, 10, 10, 14, 10);
    $table->class = 'moutable';
   	$table->width = '95%';
    $table->titles = array();
    $table->titles[] = get_string('declarants', 'block_monitoring');
    $table->worksheetname = 'declarants';

    $strnever = get_string('never');
    $stryes = get_string('yes');
    $strno = get_string('no');
    
    if ($edit_capability_ou && !$edit_capability_region && !$edit_capability_rayon)  {
        $strwhere = "WHERE edutypeid={$edutype->id}  AND oid=$oid";
        $strwhereplus = " || modifierid = {$USER->id}";
    } else {
        $strwhere = "WHERE rayonid = $rid";
        $strwhereplus = " || d.rayonid = $rid";
    }    

    $strsql = "SELECT DISTINCT declarantid FROM {$CFG->prefix}monit_queue_request $strwhere ";
    $declids = ''; 
    if($requests = get_records_sql($strsql))  {
        // print_r($requests);
        foreach ($requests as $request) {
            $declids .= $request->declarantid . ',';
        }     
    }    
    $declids .= '0';

    if ($numsym == 99 ) {
        $strwhere = " WHERE  d.id in ($declids) " . $strwhereplus;
    } else {
        $strwhere = " WHERE  (u.lastname like '$arrRus[$numsym]%') AND (d.id in ($declids) $strwhereplus)";
    }

    $strsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.email, u.phone1, u.phone2, u.lastaccess, d.id as declarantid 
               FROM {$CFG->prefix}user u
               RIGHT JOIN {$CFG->prefix}monit_queue_declarant d ON u.id=d.userid
               $strwhere 
               ORDER BY u.lastname";
    // echo $strsql;             
    if($declarants = get_records_sql($strsql))  {
        foreach($declarants as $declarant){
            
            if ($declarant->lastaccess) {
                $lastaccess = format_time(time() - $declarant->lastaccess);
            } else {
                $lastaccess = $strnever;
            }
            
            $foto = print_user_picture($declarant->id, 1, $declarant->picture, false, true);
            
            $strlinkupdate = '';

            if ($edit_capability_region || $edit_capability_rayon)  {
       			$title = 'Редактирование данных заявителя';
    	  	 	$strlinkupdate  = "<a title=\"$title\" href=\"declarants.php?action=edit&id=$declarant->declarantid&rid=$rid\">";
        		$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
    
        		$title = 'Удалить заявителя';
        	    $strlinkupdate .= "<a title=\"$title\" href=\"declarants.php?action=del&id=$declarant->declarantid&rid=$rid\">";
        		$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";
            }    

            $strsql = 'SELECT id FROM '. $CFG->prefix . 'monit_queue_request' . ' ' . "WHERE declarantid = $declarant->declarantid";
            if (record_exists_sql($strsql)) { 
                $strrequest = $stryes;     
            } else {
                $strrequest = $strno;
            }
            $options = array('rid' => $rid, 'who' => 'oper', 'useridd' => $declarant->id);
            // $strrequest .= '&nbsp;&nbsp;&nbsp;';
     	    $strrequest .= print_single_button("index.php", $options, 'Подать новую заявку', 'get', '_self', true);

                        
            $table->data[] = array($foto, fullname($declarant), $strrequest, "<strong>$declarant->username</strong>",  
                                    $declarant->email, $declarant->phone1 . '<br>' . $declarant->phone2,
                                    $lastaccess,  $strlinkupdate);
        }
    }

    return $table;
}


?>
