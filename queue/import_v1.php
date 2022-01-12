<?php // $Id: importqueue.php,v 1.1 2012/03/26 12:29:26 shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    // require_once('../../mou_school/lib_school.php');    
    require_once('../../mou_att2/lib_att2.php');
    require_once('lib_queue.php');
	require_once($CFG->dirroot.'/lib/uploadlib.php');

	// define('ROLE_NON_EDITING_TEACHER', 4);

    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $oid = optional_param('oid', 0, PARAM_INT);       // OU id
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $typeou = optional_param('typeou', '-');       // Type OU
	$action   = optional_param('action', '');
    
	$currentyearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $currentyearid;
    }

	$strlistrayons =  listbox_rayons_att("importqueue.php?oid=0&amp;yid=$yid&amp;rid=", $rid);
	$strlisttypeou =  listbox_typeou_att("importqueue.php?rid=$rid&amp;yid=$yid&amp;oid=0&amp;typeou=", $rid, $typeou);

	if (!$strlistrayons && !$strlisttypeou)   { 
		error(get_string('permission', 'block_mou_school'), '../index.php');
	}	

	$struser = get_string('user');
	$strtitle = get_string('title', 'block_monitoring');
    $strinfo = get_string('infoaboutou', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
    $strimport = get_string('importqueue', 'block_monitoring');
  
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strimport, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strtitle, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    // $strnever = get_string('never');
    $tab = 'importqueue';
    include('tabs.php');

	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	echo $strlistrayons;
	echo $strlisttypeou;

	if ($typeou != '-')	{
		if ($strlistou = listbox_ou_att("importqueue.php?rid=$rid&amp;yid=$yid&amp;typeou=$typeou&amp;oid=", $rid, $typeou, $oid, $yid))	{ 
			echo $strlistou;
		} else {
			echo '</table>';
			notice(get_string('ounotfound', 'block_mou_att'), "../index.php?rid=$rid&amp;yid=$yid");
		}	
	} 
	echo '</table>';

//    print_heading('Страница в стадии доработки.', 'center', 3);
//    exit();

	if ($rid != 0 && $oid != 0 && $typeou != '-')   {

        $edutype = get_config_typeou($typeou);
        
    	$context = get_context_instance($edutype->context, $oid);
        $edit_capability = has_capability('block/monitoring:editqueue', $context);
        

    	if ($edit_capability )	{
    	
    		$um = new upload_manager('userfile',false,false,null,false,0);
    		$f = 0;
    		if ($um->preprocess_files()) {
    			$filename = $um->files['userfile']['tmp_name'];
                $rayon = get_record_select('monit_rayon', "id = $rid", 'id, name');
                importqueue($filename);
            }    
                
        	/// Print the form
            $struploadusers = get_string('importqueueou', 'block_monitoring');
            print_heading_with_help($struploadusers, 'importqueuou', 'mou');
            $struploadusers = get_string('importqueue', 'block_monitoring');
        
            $maxuploadsize = get_max_upload_file_size();
        	$strchoose = ''; // get_string("choose"). ':';
        
            echo '<center>';
            echo '<form method="post" enctype="multipart/form-data" action="importqueue.php">'.
                 $strchoose.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'">'.
                 '<input type="hidden" name="rid" value="'.$rid.'">'.
                 '<input type="hidden" name="oid" value="'.$oid.'">'.
                 '<input type="hidden" name="yid" value="'.$yid.'">'.
                 '<input type="hidden" name="typeou" value="'.$typeou.'">'.         
                 '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">';
       
    	    echo '<input type="file" name="userfile" size="100">'.
    	         '<br><input type="submit" value="'.$struploadusers.'">'.
    	         '</form>';
    	    echo '</center>';
            
            
            notify ('Страница в стадии тестирования');
            // print_help_import();
        }    
    }
    print_footer();



function importqueue($filename)
{
    global $CFG, $rid, $yid, $oid, $typeou, $rayon, $edutype, $context;

    @set_time_limit(0);
    @raise_memory_limit("192M");
    if (function_exists('apache_child_terminate')) {
        @apache_child_terminate();
    }

    $csv_delimiter = ';';
    $usersnew = 0;
    $userserrors  = 0;
    $linenum = 2; // since header is line 1
    $redirlink = "importqueue.php?rid=$rid&amp;yid=$yid&amp;typeou=$typeou&amp;oid=$oid";
    $ou = get_record_select($edutype->tblname, "id=$oid", 'id, name');
    
    // print_r($stafftype); echo '<br>';
    
    // $coursecontext = get_context_instance(CONTEXT_COURSE, 1);
    $currenttime = time();
    
    $teachersql = "SELECT u.id, u.username, u.firstname, u.lastname
                  FROM {$CFG->prefix}user u
	              LEFT JOIN {$CFG->prefix}monit_queue_declarant d ON d.userid = u.id
 	              WHERE d.rayonid=$rid AND u.deleted = 0 AND u.confirmed = 1";
	$tezki = array();

	if ($teachers = get_records_sql($teachersql))	{
        foreach ($teachers as $teacher)  {
        	$tezki[$teacher->id] = mb_strtolower ($teacher->lastname . ' '. $teacher->firstname, 'UTF-8');
        }
	}

    $text = file($filename);
	if($text == FALSE){
		error(get_string('errorfile', 'block_monitoring'), $redirlink);
	}
	$size = sizeof($text);

	$textlib = textlib_get_instance();
  	for($i=0; $i < $size; $i++)  {
		$text[$i] = $textlib->convert($text[$i], 'win1251');
    }   
    unset ($textlib);

    $required = array('datez' => 1, 'timez' => 1, "lastname" => 1, "firstname" => 1); // "email" => 1, 'phone1' => 1, 'city' => 1, 'description' => 1, 'idschool' => 1);
    
    $header = split($csv_delimiter, $text[0]); // --- get and check header (field names) ---
    // print_r($header); echo '<hr>';
    translate_header($header);
    
	echo 'login;password;lastname;firstname;email<br>';

	$userflds = array('lastname', 'firstname', 'secondname', 'email', 'username', 'password');
    $declarantflds = array('serial', 'number', 'when_hands', 'who_hands', 'addressfact', 'familystatus');
    $childflds = array('lastname1', 'firstname1', 'secondname1', 'pol', 'birthday', 'serial1', 'number1', 
                        'who_hands1', 'when_hands1', 'addressregistration', 'addresshome');
    $requestflds = array('datez', 'timez', 'isbenefits');
    $newrequestids = '';                        

  	for($i=1; $i < $size; $i++)  {
  	    // echo '!!!!!!!!!!!!!!!!! -------------- ' .$i;
        unset($record); 
        $line = split($csv_delimiter, $text[$i]);
        foreach ($line as $key => $value) {
            $record[$header[$key]] = trim($value);
        }

        // print_r($record);
        foreach ($record as $name => $value) {
            // check for required values
            if (isset($required[$name]) and !$value) {
                error(get_string('missingfield', 'error', $name). " ".
                      get_string('erroronline', 'error', $linenum) .". ".
                      get_string('processingstops', 'error'), $redirlink);
            } else {
                if (in_array($name, $userflds))    {
                	$user->{$name} = addslashes($value);
                } else if (in_array($name, $declarantflds))    {
                	$declarant->{$name} = addslashes($value);
                } else if (in_array($name, $childflds))    {
                    $child->{$name} = addslashes($value);
                } else if (in_array($name, $requestflds))    {
                    $request->{$name} = addslashes($value);
                }    
            }
        }

        // CREATE USER ====================
        $ln_fn = mb_strtolower ($user->lastname . ' '. $user->firstname . ' '. $user->secondname, 'UTF-8');
        $newid = 0;
        if ($existid = array_search($ln_fn, $tezki))	{
            notify(get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname . ' '. $user->secondname), 'green');
            if ($user1 = get_record_select('user', "id = $existid", 'id, lastname, firstname'))   {
                $newid = $user1->id;
            } else {    
                echo '<pre>';  print_r($user); echo '</pre>';
            }    
            $iscreatenewuser = false;            
        } else {
    		$translitlastname = translit_russian_utf8 ($user->lastname);
    		$user->username = $translitlastname;
            $j = 1;
            $iscreatenewuser = true;
    		while (record_exists_mou('user', 'username', $user->username))  {
    			$user->username = $translitlastname.$j;
    	 		if ($olduser = get_record_select('user', "username = '$user->username'", 'id, lastname, firstname'))		{
    			    if ($olduser->lastname == $user->lastname && $olduser->firstname == $user->firstname)	{
                       notify("$olduser->id ".get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname), 'green');
                       $iscreatenewuser = false;
                       $user = $olduser;
                       // echo '<pre>';  print_r($user); echo '</pre>';
                       $newid = $olduser->id; 
                       break;
                    }
                }
    			if ($j++ > 50) break;
    		}
        }

		if ($iscreatenewuser) {

    		if (empty ($user->email)) {
    			$user->email = $user->username . '@temp.ru';
    		}
    
            // $staff->pswtxt = gen_psw($user->username);
            $user->firstname .= ' ' . $user->secondname;
            $user->pswtxt =  $user->password;
            $user->password = hash_internal_user_password($user->password);
       	    $user->city = $rayon->name;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->confirmed = 1;
            $user->timemodified = $currenttime;
            $user->country = 'RU';
            $user->lang = 'ru_utf8';
            $user->description = 'Заявитель ('. $ou->name . ')';
            
            // echo '<pre>';  print_r($user); echo '</pre>';   
    
            if ($newid = insert_record("user", $user)) {
                $user->id = $newid; 
                notify ("Добавлен новый пользователь: $user->username; $user->pswtxt; $user->lastname; $user->firstname; $user->email", 'green');
                $usersnew++;
            } else {
                // Record not added -- possibly some other error
                notify(get_string('usernotaddederror', 'error', $user->username));
                $userserrors++;
                continue;
            }

            $tezki[] = mb_strtolower ($user->lastname . ' '. $user->firstname, 'UTF-8');   
        }   


        // CREATE DECLARANT ====================
        if (!$olddeclarant = get_record_select('monit_queue_declarant', "rayonid = $rid AND userid = $newid"))   {
            $declarant->userid = $newid;        
      		$declarant->rayonid = $rid;
            $when_hands = convert_date($declarant->when_hands);
            list($y, $m, $d) = explode ('-', $when_hands); 
            $declarant->when_hands = make_timestamp($y, $m, $d);
            
            $familystatus = mb_strtolower($declarant->familystatus, 'UTF-8'); 
            if ($familystatus == 'мать')    {
                $declarant->familystatus = 0;
            } else if ($familystatus == 'отец')    {
                $declarant->familystatus = 1;
            } else {
                $declarant->familystatus = 2;
            }    
    
            $declarant->isconfirmpersonaldata = 1;
            $declarant->isknowingou = 1;
            $declarant->issubscribe = 1;
            $declarant->documentzakon = '-';
            $declarant->documentplace = '-';
            $declarant->timemodified = $currenttime;
               
       		if ($newdeclid = insert_record('monit_queue_declarant', $declarant))	 {
                $declarant->id = $newdeclid;
                $newrequestids .=  $newdeclid . ', ';
                notify('Заявитель зарегистрирован.', 'green');  
    		} else  {
    		    print_r($declarant);  
    			error('Возникла ошибка при добавлении данных заявителя.', $redirlink);
    		}

            // echo '<pre>';  print_r($declarant); echo '</pre>';            
        }  else {
            $declarant = $olddeclarant;
        }  


        // CREATE CHILD ====================
        $child->lastname = $child->lastname1;
        $child->firstname = $child->firstname1;
        $child->secondname = $child->secondname1;
        
        $pol = mb_strtolower($child->pol, 'UTF-8'); 
        if ($pol == 'ж')    {
            $child->pol = 2;
        } else {
            $child->pol = 1;
        }    

        $birthday = convert_date($child->birthday);
        list($y, $m, $d) = explode ('-', $birthday); 
        $child->$birthday = make_timestamp($y, $m, $d);
        
        $child->serial = $child->serial1;
        $child->number = $child->number1;
        $child->who_hands = $child->who_hands1;
        $child->when_hands = convert_date($child->when_hands1);
        
       //  echo '<pre>';  print_r($child); echo '</pre>';
        
		if ($newchildid = insert_record('monit_queue_child', $child))	 {
            $child->id = $newchildid;
            notify('Данные ребенка сохранены.', 'green');   
		} else  {
		    print_r($child);  
			error('Возникла ошибка при добавлении данных ребенка.', $redirlink);
		}
        

        // CREATE REQUEST ====================
        $request->declarantid = $declarant->id;
        $request->rayonid = $rid;
        $request->number = 0;
        $request->edutypeid =$edutype->id;
        $request->oid = $oid;
        $request->childid = $child->id;
        
        $r = str_pad($rid, 2, '0', STR_PAD_LEFT);
        $ou    = $typeou . str_pad($oid, 4, '0', STR_PAD_LEFT);
        $struser  = str_pad($newid, 6, '0', STR_PAD_LEFT);
        $child = str_pad($child->id, 6, '0', STR_PAD_LEFT);    
        
        $request->code = $r . '-' . $ou . '-' . $struser . '-' . $child;
        
        $isbenefits = mb_strtolower($request->isbenefits, 'UTF-8');
        if ($isbenefits == 'да')   {
            $request->benefitsids = '0,1';    
        } 
        
        $request->status = STATUS_PUTINTOQUEUE;
        $request->dateenrollment = $currenttime;
        $request->timecreated = $currenttime;

        $datez = convert_date($request->datez);
        list($y, $m, $d) = explode ('-', $datez);
        list($h, $mm) = explode (':', $request->timez); 
        $request->timemodified = make_timestamp($y, $m, $d, $h, $mm);

		if ($requestid = insert_record('monit_queue_request', $request))	 {
            $request->id = $requestid;
            notify('Заявка зарегистрирована.', 'green');   
		} else  {
		    print_r($request);  
			error('Возникла ошибка при добавлении заявления.', $redirlink);
		}

        // echo '<pre>';  print_r($request); echo '</pre>';
                        
        $linenum++;
        unset($user);
        unset($declarant);
        unset($child);
        unset($request); 
    }
    
    $newrequestids .= '0';
    if ($newrequests = get_records_select('monit_queue_request', "id in ($newrequestids)", 'timemodified', 'id, timemodified'))    {
        foreach ($newrequests as $newrequest)   {
            change_status (STATUS_PUTINTOQUEUE, $newrequest->id);
            $dt = date("m.d.Y G:i", $newrequest->timemodified);
            notify("Заявка с датой $dt поставлена в очередь.", 'green');
        }    
    }
    
    $strusersnew = get_string("usersnew");
    notify("$strusersnew: $usersnew", 'green', 'center');
    notify(get_string('errors', 'admin') . ": $userserrors");
	echo '<hr />';
}


function print_help_import()
{
        	echo '<hr>';
            print_simple_box_start_old('center', '100%', '#ffffff', 0);
            ?>


            <h2>Как загрузить список учителей школы в систему</h2>
            <p> Данное средство позволяет загрузить список педагогических и руководящих работников выбранной школы. </p>
            
            <ul>
            <li>Каждая строка файла содержит одну запись.</li>
            <li>Каждая запись - ряд данных, отделенных точками с запятой (;).</li>
            <li>Первая запись файла является особенной и содержит список имен полей. Они определяют формат остальной части файла.
            <blockquote>
            <p><strong>Требования к именам полей:</strong> эти поля должен быть включены в первую запись; они определяют для каждого сотрудника: </p>
            <p></p>
            <font color="#990000" face="Courier New, Courier, mono">
            фамилия;имя отчество;электронная почта;телефон;дата рождения;образование;какое учреждение закончил;год окончания;должность (преподаваемый предмет);руководящая должность;педагогическая нагрузка;стаж педагогической работы;стаж руководящей работы;стаж в занимаемой должности ;стаж в занимаемой руководящей должности ;наличие квалификационной категории учителя;дата присвоения для учителя;наличие квалификационной категории руководителя;дата присвоения для руководителя;государственные награды (год, название);отраслевые награды (год, название);дата окончания последнего курсового обучения;место обучения
            </font></p>
            </p>
            <p> <strong>Обязательными полями являются 'фамилия' и 'имя отчество'.</strong>
            </ul>
            <p><strong>Методика создания файла импорта:</strong>
            <ul>
            <li>Создать новую книгу в Microsof Excel</li>
            <li>В первой строке ввести названия полей. Одно название в одну ячейку, т.е. в ячейку A1 - фамилия, B1 - имя отчество, C1 - электронная почта и т.д. (названия полей вводить без точек с запятой и других знаков препинания)</li>
            <li>Заполнить вторую строку данными сотрудника, например, в ячейку A2 - Иванов, B2 - Иван Иванович, C2 - ivanov@mail.ru и т.д.
            <li>Аналогично заполнить данные по другим сотрудникам (одна строка - один сотрудник).
            <li>После ввода данных сохранить книгу сначала в формате XLS, а затем в формате CSV. Сохранение в CSV формате выполняется командой "Сохранить как" и в списке "Тип файла" выбирается "CSV".
            <li>Полученный CSV файл можно использовать для импорта кадрового состава школы в базу данных системы.
            </ul>
            
            <p><strong>Пример файла импорта в формате CSV (кодировка Windows-1251):</strong> </p>
            <p><font size="-1" face="Courier New, Courier, mono"></font>
            фамилия;имя отчество;электронная почта;телефон;дата рождения;образование;какое учреждение закончил;год окончания;должность (преподаваемый предмет);руководящая должность;педагогическая нагрузка;стаж педагогической работы;стаж руководящей работы;стаж в занимаемой должности ;стаж в занимаемой руководящей должности ;наличие квалификационной категории учителя;дата присвоения для учителя;наличие квалификационной категории руководителя;дата присвоения для руководителя;государственные награды (год, название);отраслевые награды (год, название);дата окончания последнего курсового обучения;место обучения<br />
            Алинова;Алина Ивановна;alinova@mail.ru;(4722)55-22-33;21.11.1949;высшее;Белгородский ГПИ;1976;;директор;;39;2;;1;высшая;03.03.2005;первая;04.04.2006;"2005, почетное звание ""Заслуженный учитель РФ""";"2001 значок ""Отличник  народного просвещения""";02.02.2003;БРИПКППС<br />
            Натальева;Наталья Ивановна;nataly@rambler.ru;(4722)11-22-33;01.01.1965;высшее;Белгородский ГПИ;1988;учитель;зам по воспитательной работе;18;19;3;18;3;Высшая;09.10.2003;первая;03.11.2005;;"2007  нагрудный знак ""Почетный работник общего образованияРФ""";04.11.2004;БРИПКППС<br />
            Нинова;Нина Ивановна;nina@temp.ru;(4722)11-11-11;01.01.1957;высшее;Белгородский ГПИ;1982;учитель;;18;26;;15;;Вторая;19.03.2002;;;;;03.05.2006;БРИПКППС<br />
            Иванов;Иван Федорович;ivanov@yandex.ru;(4722)44-11-22;01.01.1963;высшее;Белгородский ГПИ;1988;преподователь-организатор ОБЖ;;18;19;;12;;Первая;11.12.2001;;;;;02.02.2003;БРИПКППС<br />
            </font></p>
            
            <p><strong>Пример файла импорта в формате XLS: <a href="<?php echo $CFG->wwwroot.'/file.php/1/shablon_t_h.xls' ?>"> shablon_t_h.xls</a></strong></p>

            <?php
            print_simple_box_end_old();
}   

function translate_header(&$header)
{

    $string_rus[]='Дата подачи заявления'; // 0
    $string_rus[]='Время подачи';
    $string_rus[]='Фамилия заявителя';
    $string_rus[]='Имя заявителя';
    $string_rus[]='Отчество заявителя';
    $string_rus[]='Email';
    $string_rus[]='Предполагаемый логин';
    $string_rus[]='Пароль';
    $string_rus[]='Серия паспорта';
    $string_rus[]='Номер паспорта';
    $string_rus[]='Дата выдачи паспорта';
    $string_rus[]='Кем выдан паспорт';
    $string_rus[]='Адрес регистрации';
    $string_rus[]='Семейное положение';
    $string_rus[]='Фамилия ребенка';
    $string_rus[]='Имя ребенка';
    $string_rus[]='Отчество ребенка';
    $string_rus[]='Пол';
    $string_rus[]='Дата рождения';
    $string_rus[]='Серия свидетельства';
    $string_rus[]='Номер свидетельства';
    $string_rus[]='Дата выдачи свидетельства';
    $string_rus[]='Кем выдано свидетельство';
    $string_rus[]='Адрес регистрации ';
    $string_rus[]='Фактический адрес';
    $string_rus[]='Наличие льгот';

    $string_lat[]='datez';
    $string_lat[]='timez';
    $string_lat[]='lastname';
    $string_lat[]='firstname';
    $string_lat[]='secondname';
    $string_lat[]='email';
    $string_lat[]='username';
    $string_lat[]='password';
    $string_lat[]='serial';
    $string_lat[]='number';
    $string_lat[]='when_hands';
    $string_lat[]='who_hands';
    $string_lat[]='addressfact';
    $string_lat[]='familystatus';
    $string_lat[]='lastname1';
    $string_lat[]='firstname1';
    $string_lat[]='secondname1';
    $string_lat[]='pol';
    $string_lat[]='birthday';
    $string_lat[]='serial1';
    $string_lat[]='number1';
    $string_lat[]='when_hands1';
    $string_lat[]='who_hands1';
    $string_lat[]='addressregistration';
    $string_lat[]='addresshome';
    $string_lat[]='isbenefits';

    foreach ($header as $i => $h) {
		$h = trim($h);
		$flag = true;
		foreach ($string_rus as $j => $strrus) {
       		if ($strrus == $h)  {
       			$header[$i] = $string_lat[$j];
				$flag = false;
       			break;
       		}
       	}
       	if ($flag)  {
       	     echo '<pre>'; print_r($header); echo '</pre>';
             echo $i;
			 error(get_string('errorinnamefield', 'block_mou_att', $header[$i]), "importqueue.php");
       	}
    }
    // echo '<pre>'; print_r($header); echo '</pre>';
}


function translit_russian_utf8($input)
{
  $arrRus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
                  'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь',
                  'ы', 'ъ', 'э', 'ю', 'я',
                  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
                  'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь',
                  'Ы', 'Ъ', 'Э', 'Ю', 'Я');
  $arrEng = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',
                  'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '',
                  'y', '', 'e', 'ju', 'ja',
                  'A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M',
                  'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'C', 'CH', 'SH', 'SCH', '',
                  'Y', '', 'E', 'JU', 'JA');
  return str_replace($arrRus, $arrEng, $input);
}


function record_exists_mou($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return record_exists_sql('SELECT id FROM '. $CFG->prefix . $table .' '. $select);
}
?>
