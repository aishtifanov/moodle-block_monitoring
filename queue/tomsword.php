<?php // $Id: tomsword.php,v 1.5 2012/07/05 07:12:28 shtifanov Exp $

    require_once("../../../config.php");
    // require_once($CFG->libdir.'/tablelib.php');
    // require_once($CFG->libdir.'/filelib.php');    
    require_once('../lib.php');
    require_once('../../mou_att2/lib_att2.php');
    // require_once('../lib_flextable.php');
    require_once('lib_queue.php');
    require_once('names.php');

    require_login();

    $rid = required_param('rid', PARAM_INT);    // Rayon id
    $oid = required_param('oid', PARAM_INT);    // OU id
    $yid = required_param('yid', PARAM_INT);    // Year id
    $typeou = required_param('typeou');       // Type OU
    $age = required_param('age', PARAM_INT);    // Request id
    $id = required_param('id', PARAM_INT);    // Request id    
    $childid = required_param('cid', PARAM_INT);    // Rayon id
    $declarantid = required_param('did', PARAM_INT);    // OU id
    $userid = required_param('userid', PARAM_INT);    // Year id

    $action   = optional_param('action', '');

	$strtitle = get_string('title', 'block_monitoring');
	$strrequest  = get_string('requestdeclare', 'block_monitoring');
    $strqueue = get_string('queue', 'block_monitoring');
    $strword = get_string('tomsword', 'block_monitoring');

    $params = "rid=$rid&yid=$yid&oid=$oid&typeou=$typeou&id=$id&age=$age";
    
    if ($action == 'toword')    {
        print_zajavlenie_to_word($id, $userid, $declarantid, $childid);
        exit();
    }
      
    $navlinks = array();
    $navlinks[] = array('name' => $strtitle, 'link' => "$CFG->wwwroot/blocks/monitoring/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strqueue, 'link' => "queue.php?$params", 'type' => 'misc');
    $navlinks[] = array('name' => $strword,  'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($SITE->shortname . ': '. $strword, $SITE->fullname, $navigation, "", "", true, "&nbsp;");
    
    if (isoperatorinanyou($USER->id, true)) {

        notify ('<i>Замечание: в заявлении необходимо обязательно проверить правильность записи Ф.И.О. заявителя в родительном падеже. <br>Сложные фамилии, имена и отчества система может сформировать в родительном падеже неправильно.</i>');
        ?>         
        <form name="addform" method="post" action="tomsword.php">
        	    <div align="center">
        				<input type="hidden" name="rid" value="<?php echo $rid ?>" />
        				<input type="hidden" name="oid" value="<?php echo $oid ?>" />
        			    <input type="hidden" name="yid" value="<?php echo $yid ?>" />
        				<input type="hidden" name="typeou" value="<?php echo $typeou ?>" />
        				<input type="hidden" name="age" value="<?php echo $age?>" />
        				<input type="hidden" name="id" value="<?php echo $id ?>" />                            
        				<input type="hidden" name="cid" value="<?php echo $childid ?>" />
        				<input type="hidden" name="did" value="<?php echo $declarantid ?>" />
        			    <input type="hidden" name="userid" value="<?php echo $userid ?>" />
        		        <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
        		<input type="hidden" name="action" value="toword" />
        		<input type="submit" name="downloadword" value="<?php print_string('downloadword', 'block_mou_att')?>">
        	    </div>
          </form>
          <p></p>
        <?php         

        print_simple_box_start_old('center', '600', '#ffffff', 0);
    
        $strout = print_zajavlenie($id, $userid, $declarantid, $childid);
        echo $strout;

        print_simple_box_end_old();

        
        ?>         
        <form name="addform" method="post" action="tomsword.php">
        	    <div align="center">
        				<input type="hidden" name="rid" value="<?php echo $rid ?>" />
        				<input type="hidden" name="oid" value="<?php echo $oid ?>" />
        			    <input type="hidden" name="yid" value="<?php echo $yid ?>" />
        				<input type="hidden" name="typeou" value="<?php echo $typeou ?>" />
        				<input type="hidden" name="age" value="<?php echo $age?>" />
        				<input type="hidden" name="id" value="<?php echo $id ?>" />                            
        		        <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
        		<input type="hidden" name="action" value="toword" />
        		<input type="submit" name="downloadword" value="<?php print_string('downloadword', 'block_mou_att')?>">
        	    </div>
          </form>
        <?php         

        notify ('<i>Замечание: в заявлении необходимо обязательно проверить правильность записи Ф.И.О. заявителя в родительном падеже. <br>Сложные фамилии, имена и отчества система может сформировать в родительном падеже неправильно.</i>');
	}

    print_footer();




function  print_zajavlenie($id, $userid, $declarantid, $childid)
{   
    global $CFG, $typeou;
    
    $request = get_record_select ('monit_queue_request', "id = $id", 'id, code');
    $user = get_record_select ('user', "id = $userid", 'id, lastname, firstname, phone1, phone2');
    $declarant =  get_record_select ('monit_queue_declarant', "id = $declarantid", 'id, rayonid, addressfact, workplace, familystatus');
    $child = get_record_select ('monit_queue_child', "id = $childid", 'id, lastname, firstname, secondname, birthday, addressregistration');
    
    
    $outype = get_config_typeou($typeou);
    $strsql = "SELECT o.id, o.name FROM mdl_monit_queue_request r
               INNER JOIN {$CFG->prefix}{$outype->tblname} o ON o.id=r.oid
               where r.childid=$childid";
    $ous = get_records_sql($strsql);
    
    $user->fio = $user->lastname . ' ' . $user->firstname;
    if (isset($user->firstname) && !empty($user->firstname)) {
     	   list($f,$s) = explode(' ', $user->firstname);
           $user->firstname = $f;
           $user->secondname = trim($s);
    } else {
           $user->secondname = '';
    }

    $choices = array();
    $choices['0'] = get_string('mother', 'block_mou_nsop');
    $choices['1'] = get_string('father', 'block_mou_nsop');
    $choices['2'] = get_string('predstavitel', 'block_mou_nsop');
    $familystatus = $choices[$declarant->familystatus];     

    // $user->dativecase  =  dativecase($user->lastname, $user->firstname, $user->secondname);
    $pol = 'm';
    $user->roditelcase =  roditelcase($user->lastname, $user->firstname, $user->secondname, $pol);
    if ($pol == 'm') {
        $pr = 'проживающего';
        // $familystatus = $choices['1'];
    }     
    else    {
        $pr = 'проживающей';
        // $familystatus = $choices['0'];
    }    
    // get_dative_case_qualify($staff->qualifynow);
    $phones = '';
    if (!empty($user->phone1))  {
        $phones .= $user->phone1;
    } 
    if (!empty($user->phone2))  {
        if (!empty($phones))  {
            $phones .= ', ';
        }
        $phones .= $user->phone2;    
    }    

    $child->roditelcase =  roditelcase($child->lastname, $child->firstname, $child->secondname, $pol);
    if ($pol == 'm') $polchild = 'моем сыне';
    else             $polchild = 'моей дочери';
        
    $childfio = $child->lastname . ' ' . $child->firstname . ' ' . $child->secondname;
    $birthday = date('d.m.Y', $child->birthday);
    $currdate = date('d.m.Y');
    
    $dous = ''; $i=0;
    if ($ous)   {
        $countdous = count ($ous);
        foreach ($ous as $ou)   {
           $dous .=  $ou->name;
        }
        if (++$i < $countdous)  $dous .=  ', ';
    }
    

    // $declarant->workpalce ='-';
    
    $nachalniku = get_string('nachalkniku'.$declarant->rayonid, 'block_monitoring');
         
    $stroutput = "<p class=MsoNormal style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:207.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal'><span
style='font-size:12.0pt;font-family:Arial'>$nachalniku <br>$user->roditelcase, $pr по адресу: $declarant->addressfact</span></p>
<p class=MsoNormal style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:207.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal'><span
style='font-size:12.0pt;font-family:Arial'>Тел. $phones</span></p>


<p></p>

<p class=MsoNormal align=center style='margin-bottom:0cm;margin-bottom:.0001pt;
text-align:center;line-height:normal'><b><span style='font-size:13.0pt;
font-family:Arial'>заявление.</span></b></p>

<p></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Прошу внести в базу данных по предоставлению мест в дошкольных
образовательных учреждениях информацию о $polchild.</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>&nbsp;</span></p>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none'>
 <tr>
  <td width=175 valign=top style='width:131.4pt;border:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>Ф.И.О.
  ребенка</span></p>
  </td>
  <td width=444 valign=top style='width:332.9pt;border:solid windowtext 1.0pt;
  border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span lang=EN-US style='font-size:12.0pt;
  font-family:Arial'>$childfio</span></p>
  </td>
 </tr>
 <tr>
  <td width=175 valign=top style='width:131.4pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>Дата
  рождения</span></p>
  </td>
  <td width=444 valign=top style='width:332.9pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span lang=EN-US style='font-size:12.0pt;
  font-family:Arial'>$birthday</span></p>
  </td>
 </tr>
 <tr>
  <td width=175 valign=top style='width:131.4pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>Адрес
  проживания</span></p>
  </td>
  <td width=444 valign=top style='width:332.9pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span lang=EN-US style='font-size:12.0pt;
  font-family:Arial'>$child->addressregistration</span></p>
  </td>
 </tr>
 <tr>
  <td width=175 valign=top style='width:131.4pt;border:solid windowtext 1.0pt;
  border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>Предпочтительные
  детские сады</span></p>
  </td>
  <td width=444 valign=top style='width:332.9pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
  justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>$dous</span></p>
  </td>
 </tr>
</table>

<p></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Сведения о родителях:</span></p>

<p class=MsoNormal style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:1.0cm;margin-bottom:.0001pt;line-height:normal'><span lang=EN-US
style='font-size:12.0pt;font-family:Arial'>$familystatus: $user->fio<br>
Место работы: $declarant->workplace</span></p>


<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Обязуюсь сообщать об обстоятельствах, связанных с
изменениями места жительства и сведений о ребенке, в десятидневный срок после
наступления данных обстоятельств.</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Согласен на сбор, систематизацию, хранение и передачу
следующих персональных данных: фамилия, имя, отчество, регистрация по месту
проживания, серия, номер, дата и место выдачи паспорта. Также даю согласие на
сбор, систематизацию, хранение и передачу персональных данных о своем
несовершеннолетнем (их) ребенке (детях) с момента внесения в базу данных и до
выпуска из ДОУ: фамилия, имя, отчество, регистрация по месту проживания, серия,
номер, дата и место выдачи свидетельства о рождении.</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Не возражаю против проверки предоставленных мною данных.</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>&nbsp;</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>Талон очереди выдан. Персональный код №$request->code.</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>&nbsp;</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>&nbsp;</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;text-indent:1.0cm;line-height:normal'><span style='font-size:12.0pt;
font-family:Arial'>&nbsp;</span></p>

<p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;text-align:
justify;line-height:normal'><span style='font-size:12.0pt;font-family:Arial'>Дата
</span><span lang=EN-US style='font-size:12.0pt;font-family:Arial'>$currdate </span><span
style='font-size:12.0pt;font-family:Arial'>г.     </span><span lang=EN-US
style='font-size:12.0pt;font-family:Arial'>                        </span><span
style='font-size:12.0pt;font-family:Arial'>Подпись ________________</span></p>

<p ></p>";

    return $stroutput;
}



function print_zajavlenie_to_word($id, $userid, $declarantid, $childid)
{
    global $CFG, $user, $stft;
    	
   	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment; filename=\"z_{$id}.doc\"");	
	header("Expires: 0");
	header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
	header("Pragma: public");
    
   //$numcolumn = count ($table->columnwidth) - $lastcols;
    
    $buffer = '<html xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:w="urn:schemas-microsoft-com:office:word"
	xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
	<meta name=ProgId content=Word.Document>
	<meta name=Generator content="Microsoft Word 11">
	<meta name=Originator content="Microsoft Word 11">
	<title>Аттестация кадров</title>
    <!--[if gte mso 9]><xml>
     <w:WordDocument>
      <w:View>Print</w:View>
      <w:GrammarState>Clean</w:GrammarState>
      <w:ValidateAgainstSchemas/>
      <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
      <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
      <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
      <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
     </w:WordDocument>
    </xml><![endif]--><!--[if gte mso 9]><xml>
     <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
     </w:LatentStyles>
    </xml><![endif]-->
    <style>
    <!--
     /* Style Definitions */
     p.MsoNormal, li.MsoNormal, div.MsoNormal
    	{mso-style-parent:"";
    	margin-top:0cm;
    	margin-right:0cm;
    	margin-bottom:10.0pt;
    	margin-left:0cm;
    	line-height:115%;
    	mso-pagination:widow-orphan;
    	font-size:11.0pt;
    	font-family:Calibri;
    	mso-fareast-font-family:"Times New Roman";
    	mso-bidi-font-family:"Times New Roman";}
    p
    	{mso-margin-top-alt:auto;
    	margin-right:0cm;
    	mso-margin-bottom-alt:auto;
    	margin-left:0cm;
    	mso-pagination:widow-orphan;
    	font-size:12.0pt;
    	font-family:"Times New Roman";
    	mso-fareast-font-family:"Times New Roman";}
    p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
    	{mso-style-noshow:yes;
    	mso-style-link:"Balloon Text Char";
    	margin:0cm;
    	margin-bottom:.0001pt;
    	mso-pagination:widow-orphan;
    	font-size:8.0pt;
    	font-family:Tahoma;
    	mso-fareast-font-family:"Times New Roman";}
    span.BalloonTextChar
    	{mso-style-name:"Balloon Text Char";
    	mso-style-noshow:yes;
    	mso-style-locked:yes;
    	mso-style-link:"Текст выноски";
    	mso-ansi-font-size:8.0pt;
    	mso-bidi-font-size:8.0pt;
    	font-family:Tahoma;
    	mso-ascii-font-family:Tahoma;
    	mso-hansi-font-family:Tahoma;
    	mso-bidi-font-family:Tahoma;}
    span.SpellE
    	{mso-style-name:"";
    	mso-spl-e:yes;}
    span.GramE
    	{mso-style-name:"";
    	mso-gram-e:yes;}
    @page Section1
    	{size:595.3pt 841.9pt;
    	margin:49.65pt 42.55pt 49.65pt 70.9pt;
    	mso-header-margin:35.4pt;
    	mso-footer-margin:35.4pt;
    	mso-paper-source:0;}
    div.Section1
    	{page:Section1;}
    -->
    </style>
    <!--[if gte mso 10]>
    <style>
     /* Style Definitions */
     table.MsoNormalTable
    	{mso-style-name:"Обычная таблица";
    	mso-tstyle-rowband-size:0;
    	mso-tstyle-colband-size:0;
    	mso-style-noshow:yes;
    	mso-style-parent:"";
    	mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
    	mso-para-margin:0cm;
    	mso-para-margin-bottom:.0001pt;
    	mso-pagination:widow-orphan;
    	font-size:10.0pt;
    	font-family:"Times New Roman";
    	mso-ansi-language:#0400;
    	mso-fareast-language:#0400;
    	mso-bidi-language:#0400;}
    </style>
    <![endif]-->
    </head>    
    <body lang=RU>
    <div class=Section1>';
    
    
    $buffer = print_zajavlenie($id, $userid, $declarantid, $childid);
    // $buffer .= '<br><br>';
    // echo '<pre>'; print_r($frm); echo '</pre>'; 
    $buffer .= '</div></body></html>';

	print $buffer;    
}    




?>


