<?php // $Id: queue.php,v 1.30 2012/10/12 09:04:33 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('../lib_flextable.php');
    require_once('lib_queue.php');
        
    require_login();

    $rid = optional_param('rid', 0, PARAM_INT);    // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);    // OU id
    $yid = optional_param('yid', 0, PARAM_INT);    // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $tab = optional_param('tab', 'queue');          // Rayon id
    $level = optional_param('level', 'ou');          // Rayon id
    $id = optional_param('id', 0, PARAM_INT);    // Request id
    $qgid = optional_param('qgid', 0, PARAM_INT);    // QGroupid id
    $statusid = optional_param('status', 9, PARAM_INT);    // Request id
    $confirm = optional_param('confirm');
    $columnsort = optional_param('tsort', 'number');
    $age = optional_param('age', 0, PARAM_INT);    // Request id

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strqueue, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    include('tabs.php');
    
    // $edutype = new stdClass; 

    if (isoperatorinanyou($USER->id, true)) {
  
	   $strlistrayons =  listbox_rayons_att("queue.php?level=$level&typeou=$typeou&oid=0&yid=$yid&rid=", $rid);
       if ($rid > 0 && $typeou == '-') $typeou = '20'; 
	   $strlisttypeou =  listbox_typeou_att("queue.php?level=$level&rid=$rid&yid=$yid&oid=$oid&typeou=", $rid, $typeou, true);

       $edutype = get_config_typeou($typeou);

       $params = "rid=$rid&yid=$yid&oid=$oid&typeou=$typeou";
       get_edit_capability_region_rayon_queue($rid, $edit_capability_region, $edit_capability_rayon);
       if ($edit_capability_region || $edit_capability_rayon)  {
            $toprow2 = array();
            $toprow2[] = new tabobject('rayon', "queue.php?level=rayon&$params", 'По району');
            $toprow2[] = new tabobject('ou', "queue.php?$params", 'В образовательное учреждение');
            $toprow2[] = new tabobject('group', "queue.php?level=group&$params", 'Комплектование групп');
            $tabs2 = array($toprow2);
            print_tabs($tabs2, $level, NULL, NULL);
            
        }
        
    	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
    	echo $strlistrayons;
    	echo $strlisttypeou;
    	// echo $typeou;
    	if ($typeou != '-')	{
    	   
           switch ($level)  {
            
               case 'rayon':   $table = table_queue_rayon ($rid, $typeou, $yid);
                                print_color_table($table);
    	       break; 
                           
    	       case 'ou':
                		if ($strlistou = listbox_ou_att("queue.php?rid=$rid&yid=$yid&typeou=$typeou&oid=", $rid, $typeou, $oid, $yid))	{ 
                			echo $strlistou;
                            
                            listbox_birth_years("queue.php?rid=$rid&yid=$yid&typeou=$typeou&oid=$oid&age=", $age);
                            
                            switch ($action)    {
                               case 'change':
                               if ($request = get_record_select('monit_queue_request', "id = $id", 'id, declarantid, code')) { 
                                   if (isset($confirm)) {
                                       $frm = data_submitted();
                                       $reason = '';
                                       if (isset($frm->_form_notice_yesno_with_reason))  {
                                            set_field('monit_queue_request', 'reason', $frm->reason, 'id', $id);
                                            $reason = '(по причине: '.$frm->reason.')';
                                            // print_r($frm);
                                       } 
                                       change_status ($statusid, $id);
                                       
                                       notify("<strong>Статус заявки с кодом $request->code изменен.</strong>", 'green');
                                       $declarant = get_record_select('monit_queue_declarant', "id = $request->declarantid", 'id, userid');
                                       $userto = get_record('user', 'id', $declarant->userid);
                                       $messagesubject = 'Статус Вашего заявления изменен';
                                       $status = get_record_select ('monit_status', "id=$statusid", 'id, name');
                                       // $a->fullname = fullname($userto); // $USER->firstname;// ;
                                       $a->fullname = $userto->firstname; // ;
                                       $a->code = $request->code;
                                       $a->statusname = $status->name . $reason;
                                       $a->admin = fullname($USER);
                                       $a->phone1 = $USER->phone1;
                                       $messagetext = get_string('emailstatusqueue', 'block_monitoring', $a);
                                       $messagehtml  = ''; // format_text($messagetext);     
                                       if (email_to_user($userto, $USER, $messagesubject, $messagetext, $messagehtml)) {
                                           notify("<strong>Пользователю отправлено сообщение по электронной почте.</strong>", 'green');
                                       }
               
                                       
                                   } else {
                                       echo '</table>';
                                       $status = get_record_select ('monit_status', "id=$statusid", 'id, name');
                                       $redirlink = "id=$id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age";
                                       $message = "<strong>Изменить статус заявки с кодом $request->code на '$status->name'?</strong>";
            
                                       if ($statusid == STATUS_DENIED)    {
                                            notice_yesno_with_reason ($message, "queue.php?confirm=1&action=$action&status=$statusid&$redirlink", "queue.php?$redirlink");
                                       }  else {
                    	                   notice_yesno($message, "queue.php?confirm=1&action=$action&status=$statusid&$redirlink", "queue.php?$redirlink");
                                       }    
                                       print_footer();
                                       exit(0);             
                                   }                 
                                } else {
                                    notify('Заявка не найдена.');
                                }  
                                  
                             break;
                             case 'break':
                                if ($request = get_record_select('monit_queue_request', "id = $id", 'id, declarantid, status, number, numberinyear, code')) {
                                   if (isset($confirm)) {
                                       notify("<strong>Заявка с кодом $request->code удалена.</strong>");
                                        if ($request->status == STATUS_PUTINTOQUEUE)    {
                                            renumber_queue_after_delete($id);
                                        }
                                        delete_records('monit_queue_request', 'id', $id);
                                   } else {
                                       echo '</table>';
                                       $redirlink = "id=$id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou";
                	                   notice_yesno("<strong>Вы уверены что хотите удалить заявку с кодом $request->code?</strong>" ,
                                                    "queue.php?confirm=1&action=$action&$redirlink", "queue.php?$redirlink");
                                       print_footer();
                                       exit(0);             
                                   }                 
                                } else {
                                    notify('Заявка не найдена.');
                                }    
                            break;    
                            case 'view':
                                echo '</table>';
                                $data1 = '';
                                view_request($id, $data1);
                                print_footer();
                                exit(0);             
                                // echo '<pre>'; print_r($data1); echo '</pre>';  
                            break;    
                            case 'sortyear':
                                // if ($age > 0)
                                sort_numberinyear ($rid, $oid);
                            break;
                            case 'sorttime':
                                // if ($age > 0)
                                resort_number_timecreated ($rid, $oid);
                            break;
                        }    
            
                
                            if ($oid > 0)   {
                   	            echo '</table>';
                              
                            	$context = get_context_instance($edutype->context, $oid);
                                $edit_capability_ou = has_capability('block/monitoring:editqueue', $context);
                                $view_capability_ou = has_capability('block/monitoring:viewqueue', $context);
                        
                                if ($edit_capability_ou)    {
                                    // $table = table_queue_ou ($rid, $typeou, $oid, $yid);
                                    // print_color_table($table);
                        	    	echo '<div align=center>';
                                    $table = flexible_table_queue_ou ($rid, $typeou, $oid, $yid, $age);
                        			$table->print_html();
                                	echo '</div>';
                                    if(!empty($table->data)) {
                                        echo '<div align=center><table align=center border=0><tr><td>';
                                    	$options = array('rid' => $rid, 'oid' => $oid, 'yid' => $yid, 'typeou' => $typeou, 
                                                         'action' => 'sorttime', 'age' => $age);
                                 	    print_single_button("queue.php", $options, 'Пронумеровать очередь по времени подачи заявки');
                                        echo '</td><td>';
                                    	$options = array('rid' => $rid, 'oid' => $oid, 'yid' => $yid, 'typeou' => $typeou, 
                                                         'action' => 'sortyear', 'age' => $age);
                                 	    print_single_button("queue.php", $options, 'Пронумеровать очередь по году рождения');
                                        echo '</td></tr></table></div>';
                                    }    
                                    
                                }    
                           }     
                		} else {
                			notice(get_string('ounotfound', 'block_mou_att'), "../index.php?rid=$rid&amp;yid=$yid");
                		}
               break;
               
               case 'group':
                		if ($strlistou = listbox_ou_att("queue.php?level=group&rid=$rid&yid=$yid&typeou=$typeou&oid=", $rid, $typeou, $oid, $yid))	{ 
                			echo $strlistou;
                            
                            // listbox_birth_years("queue.php?level=group&rid=$rid&yid=$yid&typeou=$typeou&oid=$oid&age=", $age);
                            
                            $table = table_queue_group ($rid, $typeou, $oid, $yid, $age);
                            print_color_table($table);
                            
                            echo '<div align=center>';
                        	$options = array('rid' => $rid, 'oid' => $oid, 'yid' => $yid, 'typeou' => $typeou, 'age' => $age);
                     	    print_single_button("editgroup.php", $options, 'Создать новую группу');
                            echo '</div><p></p>';
                        }    
               break;

               case 'grouplist':
                		if ($strlistou = listbox_ou_att("queue.php?level=group&rid=$rid&yid=$yid&typeou=$typeou&oid=", $rid, $typeou, $oid, $yid))	{ 
                			echo $strlistou;
                            echo '</table>';
                            // listbox_birth_years("queue.php?level=group&rid=$rid&yid=$yid&typeou=$typeou&oid=$oid&age=", $age);
                            
                            $table = table_queue_grouplist ($rid, $typeou, $oid, $yid, $qgid);

                            if ($qgroup = get_record_select ('monit_queue_group', "id = $qgid", 'id, name, birthyear')) {
                                print_heading(get_string('group') . " '$qgroup->name' ", 'center', 3);    
                            }

                            print_color_table($table);
                            
                        }    
               break;
               
               default:
               break;         
    	 } 
      }
      echo '</table>';
    }    
    
    print_footer();



function table_queue_rayon ($rid, $typeou, $yid)
{
	global $CFG, $USER, $edutype, $edit_capability_rayon, $edit_capability_ou, $view_capability_ou, $columnsort;

    // $edutype = get_config_typeou($typeou);
    
    $strstatus = get_string('status', 'block_monitoring');

    $table->head  = array ($strstatus, get_string('numberqueue', 'block_monitoring'), get_string('coderequest','block_monitoring'), 
    					   get_string('childata', 'block_monitoring'), get_string('declarantdata', 'block_monitoring'),
    					   get_string('datetimerequest', 'block_monitoring'), get_string('benefit', 'block_monitoring'),  get_string('action', 'block_monitoring'));

	$table->align = array ('center', 'center', 'center', 'center',  "center",  'center', 'left', 'center');
	$table->columnwidth = array (10, 7, 10, 10, 10, 10, 14, 10);
    $table->class = 'moutable';
   	$table->width = '95%';
    $table->titles = array();
    $table->titles[] = get_string('queue', 'block_monitoring');
    $table->worksheetname = '';

    $statuses = get_records_select ('monit_status', 'isqueue = 1');
    
   
    $strsql = "SELECT * FROM {$CFG->prefix}monit_queue_request WHERE edutypeid = {$edutype->id} AND rayonid = $rid AND deleted=0 ORDER BY oid, number"; 
    if($requests = get_records_sql($strsql))  {
        $curoid = 0;
        foreach($requests as $request) {
            if ($curoid != $request->oid)   {
                $curoid = $request->oid;
                $ou = get_record ($edutype->tblname, 'id', $curoid); 
                // $table->data[] = array($ou->name, '', '', '', '', '', '', '');
                $table->data[] = 'hr';
                $table->bgcolor[] = array ('FFFFFF');                
                $table->data[] = 'dr|<b>'. $ou->name . '</b>';
                $table->bgcolor[] = array ('FFFFFF');            
            }
            $oid = $request->oid;
            
            $strsql = "SELECT d.id, u.lastname, u.firstname FROM {$CFG->prefix}monit_queue_declarant d
                       INNER JOIN {$CFG->prefix}user u ON u.id=d.userid
                       WHERE d.id = $request->declarantid"; 
            if($declarant = get_record_sql($strsql)){
               $dec_name = fullname($declarant); 
            }else{
               $dec_name = 'Имя заявителя не указано';
            }
            /*
            if($ou = get_record_sql("SELECT id, name FROM {$CFG->prefix}{$edutype->tblname} WHERE id=$oid"))  {
                $ou_name = $ou->name; 
            } else {
                $ou_name = 'Не указано наименование образовательного учереждения.';
            }*/

            
            if($child = get_record('monit_queue_child', 'id', $request->childid)){
               $child_name = $child->lastname.' '.$child->firstname.' '.$child->secondname.'<br>('.date('d.m.Y', $child->birthday).')'; 
            } else{
               $child_name = 'Не указаны данные о ребенке.'; 
            }                                   

            $status     = $request -> status;
            $code       = $request -> code;
            $date       = date('d.m.Y г. h:i', $request->timecreated);   

            $strstatus = $strcolor = '';
            if ($status = get_record('monit_status', 'id', $status)) {     
                $strstatus = '<b>'.$status->name.'</b>';
                $strcolor =  $status->color;
            }
            if (isset($request->reason))    {
                $strstatus .= '<br>(причина: '.$request->reason.')';
            }

   			$title = 'Просмотр заявления';
	  	 	$strlinkupdate  = "<a title=\"$title\" href=\"queue.php?action=view&id=$request->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
			$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/monitoring/i/journal.gif\" alt=\"$title\" /></a>";
            
            if ($request->status != STATUS_DENIED)  {
                foreach ($statuses as $status)  {
                    if ($status->pixpath != '-')    {
                		$title = $status->action;
                        if ($request->status < $status->id) {
                    		$strlinkupdate .= "<a title=\"$title\" href=\"queue.php?id=$request->id&status=$status->id&action=change&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
                    		$strlinkupdate .= "<img src=\"{$CFG->pixpath}{$status->pixpath}\" alt=\"$title\" /></a>&nbsp;";
                        }    
                    }
                } 
            }    
   			$title = 'Удалить заявку';
	  	 	$strlinkupdate .= "<a title=\"$title\" href=\"queue.php?action=break&id=$request->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>";
        
            $strnumber = '-';
            if ($request->number < NUMBER_DENIED)  {
                $strnumber = $request->number;
            }
            
            $strbenefit = '';
            if ($request->benefitsids != '0') {
                $benefits = get_records_select('monit_queue_benefit', "id in ($request->benefitsids)");
                foreach ($benefits as $benefit) {
                    $strbenefit .= '* '. $benefit->name . '<br>';
                }
            } else {
                $strbenefit = '-';
            }    
                
                 
            $table->data[] = array($strstatus, $strnumber, $code, $child_name, $dec_name, $date, $strbenefit, $strlinkupdate);
            $table->bgcolor[] = array ($strcolor);            
        }
    }

    return $table;
}


function notice_yesno_with_reason ($message, $linkyes, $linkno, $options=NULL, $method='post', $return=false) 
{

    global $CFG;

    print_box_start('generalbox', 'notice');
    echo '<p>'. $message .'</p>';
    
    $output = '';
    $linkyes = str_replace('"', '&quot;', $linkyes); //basic XSS protection
    $output .= '<div class="buttons">';
    $output .= '<div class="singlebutton">';
    $output .= '<form action="'. $linkyes .'" method="'. $method .'">';
    // echo '<div align=center>';
    if ($options) {
        foreach ($options as $name => $value) {
            $output .= '<input type="hidden" name="'. $name .'" value="'. s($value) .'" />';
        }
    }
    $output .= '<input type="hidden" name="_form_notice_yesno_with_reason" value="1" />';
    $output .= 'Причина отклонения: <input type="text" name="reason" size="100" maxlength="255" value=""><br>';
    $output .= '<input type="submit" value="'. get_string('yes') ."\"/></form></div>";
    $output .= '<div class="singlebutton">';
    $output .= '<form action="'. $linkno .'" method="'. $method .'">';
    $output .= '<input type="submit" value="'. get_string('no') ."\" /></form></div></div>";
        

    if ($return) {
        return $output;
    } else {
        echo $output;
    }

    print_box_end();
}




function flexible_table_queue_ou ($rid, $typeou, $oid, $yid, $age=0)
{
	global $CFG, $USER, $edutype, $edit_capability_rayon, $edit_capability_ou, $view_capability_ou;

    // $edutype = get_record_select('monit_school_type', "cod = '$typeou'", 'id');
    
    $strstatus = get_string('status', 'block_monitoring');

    $tablecolumns = array('status', 'number', 'birthyear, numberinyear', 'num', 'code', 'lastname',  'declarantid', 'timecreated', 'benefitsids', 'action');
    $tableheaders = array ($strstatus, get_string('numberqueue', 'block_monitoring'), get_string('numberinyear', 'block_monitoring'),
                           get_string('numberinotherou', 'block_monitoring'), get_string('coderequest','block_monitoring'), 
   					       get_string('childata', 'block_monitoring'), get_string('declarantdata', 'block_monitoring'),
    					   get_string('datetimerequest', 'block_monitoring'), get_string('benefit', 'block_monitoring'),  get_string('action', 'block_monitoring'));
    $table = new flexible_table("user-index-$oid");

    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
	// $table->column_style_all('align', 'left');

    $table->define_baseurl("queue.php?rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age");
    $table->sortable(true, 'number', SORT_ASC);
    $table->set_attribute('cellspacing', '0');
	// $table->set_attribute('align', 'left');
    $table->set_attribute('border', '1');
    $table->set_attribute('class', 'moutable');
    // $table->set_attribute('id', 'queue');
    // $table->set_attribute('bordercolor', 'black');
    $table->no_sorting('action');
    $table->no_sorting('declarantid');
    $table->no_sorting('num');    
    
    $table->setup();

	 // print_r($studentsql);

    $statuses = get_records_select ('monit_status', 'isqueue = 1');
   
    $strsql = "SELECT r.*, c.lastname, c.firstname, c.secondname, c.birthday FROM {$CFG->prefix}monit_queue_request r
               INNER JOIN   {$CFG->prefix}monit_queue_child c ON c.id=r.childid
               WHERE edutypeid = {$edutype->id} AND oid = $oid AND r.deleted=0 ";
    
    if ($age > 0)   {
        $strsql .= " AND r.birthyear=$age "; 
    }               
    if($sortclause = $table->get_sql_sort()) {
        $strsql .= ' ORDER BY '.$sortclause;
    }
    // echo $strsql;
    if($requests = get_records_sql($strsql))  {
        
        foreach($requests as $request){
            
            $strsql = "SELECT d.id, d.userid, u.lastname, u.firstname FROM {$CFG->prefix}monit_queue_declarant d
                       INNER JOIN {$CFG->prefix}user u ON u.id=d.userid
                       WHERE d.id = $request->declarantid"; 
            if($declarant = get_record_sql($strsql))  {
   			   $title = 'Редактирование данных заявителя';
	  	 	   $alink  = "<a title=\"$title\" href=\"declarants.php?action=edit&id=$request->declarantid&rid=$rid\">";
               $dec_name = $alink . fullname($declarant) . '</a>'; 
            } else {
               $dec_name = 'Имя заявителя не указано';
            }
            
            $strnuminou = '';
            
            if($otherrequests = get_records_select('monit_queue_request', "childid=$request->childid AND oid<>$oid", '', 'id, number, edutypeid, oid')) {
                foreach ($otherrequests as $otherrequest)   {
                    $edutype = get_record_select('monit_school_type', "id = {$otherrequest->edutypeid}", 'id, name, cod, tblname, category');
                    if ($ou = get_record_select($edutype->tblname, "id = $otherrequest->oid", 'id, name'))    {
                        if ($otherrequest->number == NUMBER_SATISFIED)  {
                            // print_object($ou);
                            $strnuminou .= "<img src=\"{$CFG->pixpath}/i/tick_green_big.gif\" alt=\"$ou->name\" />";
                        } else if ($otherrequest->number == NUMBER_DENIED) {
                            $strnuminou .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$ou->name\" />";
                         } else {   
                            $strnuminou .= "<a title=\"$ou->name\" href = \"queue.php?rid=$rid&yid=$yid&typeou=$edutype->cod&oid=$otherrequest->oid\">$otherrequest->number</a>";
                        }      
                    } else {
                        $strnuminou .= $otherrequest->number;
                    }    
                    $strnuminou .= '<br>';
                }
            }    
                
            /*
            if($ou = get_record_sql("SELECT id, name FROM {$CFG->prefix}{$edutype->tblname} WHERE id=$oid"))  {
                $ou_name = $ou->name; 
            } else {
                $ou_name = 'Не указано наименование образовательного учереждения.';
            }*/


            /*            
            if($child = get_record('monit_queue_child', 'id', $request->childid)){
               $child_name = '<b>'.$child->lastname.' '.$child->firstname.' '.$child->secondname.'</b><br>('.date('d.m.Y', $child->birthday).')'; 
   			   $title = 'Редактирование данных ребенка';
	  	 	   $alink  = "<a title=\"$title\" href=\"child.php?action=edit&id=$request->childid&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
               $child_name = $alink . $child_name . '</a>'; 
            } else{
               $child_name = 'Не указаны данные о ребенке.'; 
            } 
            */                                  

           $child_name = '<b>'.$request->lastname.' '.$request->firstname.' '.$request->secondname.'</b><br>('.date('d.m.Y', $request->birthday).')'; 
		   $title = 'Редактирование данных ребенка';
  	 	   $alink  = "<a title=\"$title\" href=\"child.php?action=edit&id=$request->childid&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
           $child_name = $alink . $child_name . '</a>'; 


            $status = $request->status;
            $code   = $request->code;
            $date   = date('d.m.Y г. h:i', $request->timecreated);   

            $strstatus = $strcolor = '';
            if ($status = get_record('monit_status', 'id', $status)) {     
                $strstatus = '<b>'.$status->name.'</b>';
                $strcolor =  $status->color;
            }
            if (isset($request->reason))    {
                $strstatus .= '<br>(причина: '.$request->reason.')';
            }

            if($declarant)  {
                $paramsdop = "userid=$declarant->userid";
            } 
            $strlinkupdate  = '';
            
            if ($typeou == '20') {
                $title = 'Заявление в формате MS Word';
                $strlinkupdate .= "<a title=\"$title\" href=\"tomsword.php?did=$request->declarantid&cid=$request->childid&id=$request->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age&$paramsdop\">";
                $strlinkupdate .= "<img src=\"{$CFG->pixpath}/f/word.gif\" alt=\"$title\" /></a>";
            }    
             
   			$title = 'Просмотр заявления';
	  	 	$strlinkupdate .= "<a title=\"$title\" href=\"queue.php?action=view&id=$request->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age\">";
			$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/monitoring/i/journal.gif\" alt=\"$title\" /></a>";
            
            if ($request->status != STATUS_DENIED)  {
                foreach ($statuses as $status)  {
                    if ($status->pixpath != '-')    {
                		$title = $status->action;
                        if ($request->status < $status->id) {
                    		$strlinkupdate .= "<a title=\"$title\" href=\"queue.php?id=$request->id&status=$status->id&action=change&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age\">";
                    		$strlinkupdate .= "<img src=\"{$CFG->pixpath}{$status->pixpath}\" alt=\"$title\" /></a>&nbsp;";
                        }    
                    }
                } 
            }    
   			$title = 'Удалить заявку';
	  	 	$strlinkupdate .= "<a title=\"$title\" href=\"queue.php?action=break&id=$request->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&age=$age\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>";
        
            $strnumber = $strnuminyear = '-';
            if ($request->number < NUMBER_DENIED)  {
                $strnumber = $request->number;
                $strnuminyear = $request->numberinyear . ' (' . $request->birthyear . ')';
            }
            
            $strbenefit = '';
            if ($request->benefitsids != '0') {
                $benefits = get_records_select('monit_queue_benefit', "id in ($request->benefitsids)");
                foreach ($benefits as $benefit) {
                    $strbenefit .= '* '. $benefit->name . '<br>';
                }
            } else {
                $strbenefit = '-';
            }    
                
                 
            $table->add_data(array($strstatus, $strnumber, $strnuminyear, $strnuminou, $code, $child_name, $dec_name, $date, $strbenefit, $strlinkupdate));
            $table->bgcolor[] = array ($strcolor);
        }
    }

    return $table;
}


function table_queue_group ($rid, $typeou, $oid, $yid, $age)
{
	global $CFG, $USER, $edutype, $edit_capability_rayon, $edit_capability_ou, $view_capability_ou;

    // $edutype = get_record_select('monit_school_type', "cod = '$typeou'", 'id');
    
    $strstatus = get_string('status', 'block_monitoring');

    $table->head  = array ('Наименование группы', get_string('age', 'block_monitoring'), 'Количество детей', 'Процент льготников', get_string('action', 'block_monitoring'));

	$table->align = array ('center', 'center', 'center', 'center', 'center');
	$table->columnwidth = array (10, 10, 10, 10, 10);
    $table->class = 'moutable';
   	$table->width = '60%';
    $table->titles = array();
    $table->titles[] = get_string('queue', 'block_monitoring');
    $table->worksheetname = '';

    $strselect = '';
    if ($age > 0)   {
        $strselect = " AND birthyear=$age ";
    }
   
    $strsql = "SELECT * FROM {$CFG->prefix}monit_queue_group 
               WHERE edutypeid = {$edutype->id} AND oid = $oid $strselect 
               ORDER BY birthyear"; 
    if($qgroups = get_records_sql($strsql))  {
        
        foreach($qgroups as $qgroup){
            
            if ($year = get_record_select ("monit_queue_birthyear", "rayonid=1 AND id = $qgroup->birthyear", 'id, name'))  {
                $birthyear = $year->name. ' г.р.';
            }    

            $strlinkupdate  = '';
   			
            $title = 'Просмотр состава группы';
	  	 	$strlinkupdate  = "<a title=\"$title\" href=\"queue.php?level=grouplist&qgid=$qgroup->id&rid=$rid&yid=$yid&oid=$oid&typeou=$typeou\">";
			$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/monitoring/i/journal.gif\" alt=\"$title\" /></a>";
                 
            $table->data[] = array($qgroup->name, $birthyear, $qgroup->numchild, $qgroup->percent . '%',$strlinkupdate);
        }
    }

    return $table;
}


function table_queue_grouplist ($rid, $typeou, $oid, $yid, $qgid)
{
	global $CFG, $USER, $edutype, $edit_capability_rayon, $edit_capability_ou, $view_capability_ou;

    $table->head  = array ('№', 'Ф.И.О ребенка', 'Дата рождения');

	$table->align = array ('left', 'left', 'center', );
	$table->columnwidth = array (5, 20, 10);
    $table->class = 'moutable';
   	$table->width = '50%';
    $table->titles = array();
    $table->titles[] = get_string('queue', 'block_monitoring');
    $table->worksheetname = '';

  
    $strsql = "SELECT gc.childid, c.lastname, c.firstname, c.secondname, c.birthday
                FROM mdl_monit_queue_groupchild gc
                INNER JOIN mdl_monit_queue_group g ON g.id=gc.queuegroupid
                INNER JOIN mdl_monit_queue_child c ON c.id=gc.childid
                WHERE gc.queuegroupid=$qgid
                ORDER BY lastname;"; 
    if($childs = get_records_sql($strsql))  {
        $i = 1;
        foreach($childs as $child){
            
            $fio = $child->lastname . ' '. $child->firstname . ' ' . $child->secondname;
            $birthday = date('d.m.Y', $child->birthday); 

            $table->data[] = array($i++ . '.', $fio, $birthday);
        }
    }

    return $table;
}


function sort_numberinyear ($rid, $oid)
{
    global $CFG, $edutype;

    $NUMBER_DENIED = NUMBER_DENIED; 
    for ($yy=2006; $yy<=2012; $yy++)    {
        $strsql = "SELECT id, childid FROM {$CFG->prefix}monit_queue_request 
                   WHERE rayonid = $rid AND edutypeid=$edutype->id AND oid = $oid AND birthyear=$yy AND numberinyear < $NUMBER_DENIED AND number > 0
                   ORDER BY number";
         
        if($requests = get_records_sql($strsql))  {
             $i=1;   
             foreach($requests as $request) {
                set_field('monit_queue_request', 'numberinyear', $i, 'id', $request->id);
                $i++;  
             }
        }     
    }
}


function resort_number_timecreated ($rid, $oid)
{
    global $db, $CFG, $edutype;

    $NUMBER_STATUS_PUTINTOQUEUE = STATUS_PUTINTOQUEUE;
    $strsql = "update mdl_monit_queue_request set status=8, number=0, numberinyear=0
               where rayonid = $rid AND edutypeid=$edutype->id AND oid = $oid AND status=$NUMBER_STATUS_PUTINTOQUEUE";
    execute_sql($strsql);           

    $strsql = "SELECT id, timecreated FROM {$CFG->prefix}monit_queue_request 
               WHERE rayonid = $rid AND edutypeid=$edutype->id AND oid=$oid AND status=8
               ORDER BY timecreated";
    
    if($requests = get_records_sql($strsql))  {
         // print_object($requests);
         $i=1;   
         foreach($requests as $request) {
            // set_field('monit_queue_request', 'number', $i, 'id', $request->id);
            change_status (STATUS_PUTINTOQUEUE, $request->id);
            $i++;  
         }
    }     
    
    // exit();
}

?>
