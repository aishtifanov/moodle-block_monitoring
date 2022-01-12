<?php // $Id: my.php,v 1.6 2012/11/07 06:42:37 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
    
    require_login();

    $rid = optional_param('rid', 0, PARAM_INT);    // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);    // OU id
    $yid = optional_param('yid', 0, PARAM_INT);    // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    $id = optional_param('id', 0, PARAM_INT);    // Request id
    $tab = optional_param('tab', 'my');          // Rayon id
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

    if ($action == 'break') {
        if ($request = get_record_select('monit_queue_request', "id = $id", 'id, declarantid, code')) {
            $declarant = get_record_select('monit_queue_declarant', "id = $request->declarantid", 'id, userid');
            if ($USER->id == $declarant->userid)    { 
                   if (isset($confirm)) {
                       notify("<strong>Заявка с кодом $request->code удалена.</strong>");
                       delete_records('monit_queue_request', 'id', $id);
                   } else {
	                   notice_yesno("<strong>Вы уверены что хотите удалить заявку с кодом $request->code?</strong>" ,
                                    "my.php?confirm=1&action=$action&id=$id", "my.php");
                       print_footer();
                       exit(0);             
                   }                 
            }        
        } else {
            notify('Заявка не найдена.');
        }    
    }  else if ($action == 'view') {
            echo '</table>';
            $data1 = '';
            view_request($id, $data1);
            print_footer();
            exit(0);             
    }
                    
    $table = table_queue_user ();
    print_color_table($table);
    
    print_footer();




function table_queue_user ()
{
	global $CFG, $USER;

    $strstatus = get_string('status', 'block_monitoring');

    $table->head  = array ($strstatus,  get_string('numberqueue', 'block_monitoring'), get_string('numberinyear', 'block_monitoring'), 
                           get_string('coderequest','block_monitoring'), 
                           get_string('ou1', 'block_mou_att'), get_string('childata', 'block_monitoring'), 
    					   get_string('datetimerequest', 'block_monitoring'), get_string("action","block_monitoring"));

	$table->align = array ('center', 'center', 'center', 'left', 'center',  "left",  'center', 'center');
	$table->columnwidth = array (10, 7, 14, 7, 25, 14, 10);
    $table->class = 'moutable';
   	$table->width = '95%';
    $table->titles = array();
    $table->titles[] = get_string('queue', 'block_monitoring');
    $table->worksheetname = '';
    
    $strsql = "SELECT a.*, s.cod, s.tblname FROM mdl_monit_queue_request a
               INNER JOIN mdl_monit_queue_declarant b ON b.id = a.declarantid
               INNER JOIN mdl_monit_school_type s ON s.id=a.edutypeid
               WHERE b.userid=$USER->id AND a.deleted=0
               ORDER BY number";
    
    // echo $strsql;                 
  
    if($requests = get_records_sql($strsql))  {
        
        // print_r($requests);
        
        foreach($requests as $request){
            /*
            if($declarant = get_record_sql("SELECT d.id, u.lastname, u.firstname FROM {$CFG->prefix}monit_queue_declarant d
                                            inner join {$CFG->prefix}user u ON u.id=d.userid")){
               $dec_name   = fullname($declarant); 
            }else{
               $dec_name   = 'Имя заявителя не указано';
            }
            */
            
            if($ou = get_record_sql("SELECT id, name FROM {$CFG->prefix}{$request->tblname} WHERE id=$request->oid"))  {
                $ou_name = $ou->name; 
            } else {
                $ou_name = 'Не указано наименование образовательного учереждения.';
            }
            
            if($child = get_record('monit_queue_child', 'id', $request->childid)){
               $child_name = '<b>'.$child->lastname.' '.$child->firstname.' '.$child->secondname.'</b><br>('.date('d.m.Y', $child->birthday).')'; 
            } else{
               $child_name = 'Не указаны данные о ребенке'; 
            }                                   

            $status     = $request -> status;
            $code       = $request -> code;
            $date       = date('d.m.Y г. h:i', $request->timecreated);

   			$title = 'Просмотр заявления';
	  	 	$strlinkupdate  = "<a title=\"$title\" href=\"my.php?action=view&id=$request->id&rid=$request->rayonid\">";
			$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/monitoring/i/journal.gif\" alt=\"$title\" /></a>";

			$title = 'Удалить заявку';
	  	 	$strlinkupdate .= "<a title=\"$title\" href=\"my.php?action=break&id=$request->id&rid=$request->rayonid\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>";
            
            $strstatus = $strcolor = '';
            if ($status = get_record('monit_status', 'id', $status)) {     
                $strstatus = '<b>'.$status->name.'</b>';
                $strcolor =  $status->color;
            }
            
            if (isset($request->reason))    {
                $strstatus .= '<br>(причина: '.$request->reason.')';
            }
            
            $strnumber = $strnuminyear = '-';
            if ($request->number < NUMBER_DENIED)  {
                $strnumber = $request->number;
                $strnuminyear = $request->numberinyear . ' (' . $request->birthyear . ')';
            }
            
                    
            $table->data[] = array($strstatus, $strnumber, $strnuminyear, $code, $ou_name, $child_name, $date, $strlinkupdate);
            $table->bgcolor[] = array ($strcolor);            
        }
    }

    return $table;
}

?>
