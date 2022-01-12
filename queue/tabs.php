<?php  // $Id: tabs.php,v 1.11 2012/06/13 12:47:58 shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


/*

<p align="center"><a href="http://mou.bsu.edu.ru/blocks/monitoring/queue/index.php"><img title="Регистрация электронных заявлений о приеме детей в общеобразовательные учреждения, в дошкольные образовательные учреждения и учреждения дополнительного образования." border="0" hspace="0" alt="Регистрация электронных заявлений о приеме детей в общеобразовательные учреждения,<br> в дошкольные образовательные учреждения и учреждения дополнительного образования." align="middle" src="http://mou.bsu.edu.ru/file.php/1/queue.jpg" width="176" height="156" /> </a></p>

*/
    if ($rid <> 0)  {
        $rayon2 = get_record_select('monit_rayon', "id = $rid", 'id, isqueue');
        if (!$rayon2->isqueue && $tab == 'request')  {
            $rid=0;
            redirect('index.php','',0);
        }
    }
    
    $toprow = array();
    $toprow[] = new tabobject('info', "info.php", $strinfo);
    $toprow[] = new tabobject('request', "index.php", $strrequest);
    $toprow[] = new tabobject('my', "my.php", get_string('myrequest', 'block_monitoring'));
    if (isoperatorinanyou($USER->id, true)) {
        $toprow[] = new tabobject('queue', "queue.php?rid=$rid", $strqueue);
        $toprow[] = new tabobject('declarants', "declarants.php?rid=$rid", get_string('declarants', 'block_monitoring'));
        $toprow[] = new tabobject('importqueue', "importqueue.php?rid=$rid", get_string('importqueue', 'block_monitoring'));    
        $toprow[] = new tabobject('manual', "http://mou.bsu.edu.ru/file.php/1/instruction_queue_operator.pdf", get_string('instroper', 'block_monitoring'));
        $toprow[] = new tabobject('settings', "settings.php?rid=$rid", get_string('configuration'));        
        $toprow[] = new tabobject('reports', "reports.php?rid=$rid", 'Отчеты');
    }    
    $tabs = array($toprow);
/*    
    print_heading('<font color=red>ВНИМАНИЕ! Подсистема "Очередь в ОУ" работает в тестовом режиме. Регистрация пользователей и прием заявок будут открыты после окончания тестирования. Просьба к муниципальным и школьным операторам зарегистрировать пробные заявления (чем больше - тем лучше) и высказать в форуме свои замечания и предложения. После окончания тестирования все пробные заявки будут удалены. </font>', '', 4);
*/    
    // print_heading('<font color=red>ВНИМАНИЕ! Подсистема "Очередь в ОУ" доступна только для операторов ЭМОУ.<br>Регистрация пользователей и прием заявок будут открыты для конкретного района только после того, как операторы района введут уже существующую очередь в систему. <br>Рекомендуется использовать "Импорт заявлений" для пакетного ввода заявлений в систему. </font>', '', 4);
    print_tabs($tabs, $tab, NULL, NULL);

?>
