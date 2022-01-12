<?php // $Id: lib_map.php,v 1.19 2013/02/25 06:17:19 shtifanov Exp $

require_once('lib_auth.php');

// GLOBAL DATA
$W = 800;
$H = 500;
$poly_data = '';

function analys_rayon_reports($rayonid, $analysetab, $nmonth, $nyear)
{
 global  $CFG;

 for ($i=1; $i<=7; $i++)	{
	$color[$i]	= '0x'.get_string('status'.$i.'color', 'block_monitoring') + 0;
 }


 $schools = get_records_sql("SELECT id FROM {$CFG->prefix}monit_school
 							 WHERE rayonid=$rayonid AND isclosing=0 AND yearid=$nyear");
 $count_sch = count ($schools);

 $currstatus = 7;

 $nm = $nmonth; // date('n');
 $year = date("Y");
 if ($eduyear = get_record('monit_years', 'id', $nyear))  {
	$ayears = explode("/", $eduyear->name);
	if ($nm >= 9 && $nm <= 12) $year = trim($ayears[0]);
	else if ($nm >= 1 && $nm <= 8)  $year = trim($ayears[1]);
 }
 $datefrom = make_timestamp($year, $nm, 1, 12);
 
 $rubikon =	1271760508;// Tue, 20 Apr 2010 10:48:28 GMT
 // $rubikon =	1274352531; // Thu, 20 May 2010 10:48:28 GMT
 
 if ($datefrom < $rubikon) $numofbkpform = 6;
 else   				   $numofbkpform = 6;// 7;
/*
 echo $year.'<br>';
 echo $nm.'<br>';
 echo $datefrom.'<br>';
*/

 switch ($analysetab)  {
 	case 1: /* -----
	 		$strsql = "SELECT id, rayonid, status FROM {$CFG->prefix}monit_rayon_listforms
	 	 		       WHERE (rayonid = $rayonid) and (datemodified=$datefrom) and (shortname like '_kp%')";

	 	    if ($listforms = get_records_sql($strsql)) 	{
	 	         // print_r($listforms);
	 	         // echo '<hr>';
	 	         $count_lf = count($listforms);
	 	         if ($count_lf != 2) return $color[1];
	 	         else {
                     foreach ($listforms as $lf)  {
                     	if ($lf->status < $currstatus) {
                      		$currstatus = $lf->status;
                       	}
                     }
	 	         }
	 	    } else {
	 	    	 return $color[1];
	 	    }
	 	    ----- */
            /*
            echo $currstatus;
            echo '<hr>';
            */
 			 if ($schools)	{

        	     $schoolsarray = array();
			     foreach ($schools as $sa)  {
			        // echo $sa->id.'<br>';
			        $schoolsarray[] = $sa->id;
	 			 }
		 	     $schoolslist = implode(',', $schoolsarray);

	 			 $strsql = "SELECT id, schoolid, status, shortname, datemodified FROM {$CFG->prefix}monit_school_listforms
		 			         WHERE (schoolid in ($schoolslist)) and (datemodified=$datefrom) and (shortname like '_kp%')";
  		 	     if ($listforms = get_records_sql($strsql)) 	{
		 	         // print_r($listforms);
  		 	         $count_lf = count($listforms);
		 	         // echo "!!!$count_lf === 7*{$count_sch}<hr>";
  		 	         // ---- if ($count_lf < 7*$count_sch) return $color[1];
  		 	         if ($count_lf < $numofbkpform*$count_sch) return $color[1];
  		 	         else {
	                     foreach ($listforms as $lf)		{
	                     	if ($lf->status < $currstatus) {
 	                     		$currstatus = $lf->status;
  	                     	}
	                     }
  		 	         }
	 	 	     } else {
	 	    	 	return $color[1];
		 	     }
             }
             if ($currstatus == 3 || $currstatus == 4)  $currstatus = 2;
 	break;
 	case 2:
 			$currstatus = 6;
 			if ($schools)	{
        	     $schoolsarray = array();
			     foreach ($schools as $sa)  {
			        $schoolsarray[] = $sa->id;
	 			 }
		 	     $schoolslist = implode(',', $schoolsarray);

	 			 $strsql = "SELECT id, schoolid, status, shortname, datemodified FROM {$CFG->prefix}monit_school_listforms
		 			        WHERE (schoolid in ($schoolslist)) and (shortname='rkp_u') and (datemodified=$datefrom)";

  		 	     if ($listforms = get_records_sql($strsql)) 	{
		 	         $count_lf = count($listforms);
		 	         if ($count_lf != $count_sch) return $color[1];

			   		 $formsarray = array();
				     foreach ($listforms as $lf)  {
				        $formsarray[] = $lf->id;
				     }
				     $formslist = implode(',', $formsarray);
				 	 $recs = get_records_sql("SELECT id, f1_5g1, f1_5g2, f1_5g3
				 	 						  FROM {$CFG->prefix}monit_form_rkp_u
								              WHERE listformid in ($formslist)");
					 foreach ($recs as $rec)	{
					 	 if (empty($rec->f1_5g1) || empty($rec->f1_5g2) || empty($rec->f1_5g3)) return $color[1];
					 }
	 	 	     } else {
	 	    	 	return $color[1];
		 	     }
/*
			   	 $fond_truda_teacher = $fond_truda = 0;
			   	 $count_right_school = 0;

				 // echo '<hr>';
			     foreach ($schools as $sa)  {


				    $strsql = "SELECT id, schoolid, status, shortname, datemodified FROM {$CFG->prefix}monit_school_listforms
					 		   WHERE (shortname='bkp_f') and (datemodified=$datefrom) and (schoolid={$sa->id})";
				    if ($listform = get_record_sql($strsql)) 	{
          		    	if ($form = get_record('monit_form_bkp_f', 'listformid', $listform->id))   {
					    	$sum = 0;
					    	if (isset($form->f2f)) $sum = $form->f2f*12;
                    	    $fond_truda = $sum;
					    	$sum = 0;
					    	if (isset($form->f2_6f)) $sum = $form->f2_6f*12;
                  		 	$fond_truda_teacher = $sum;

                            if ($fond_truda > 0) {
							    $proc = ($fond_truda_teacher/$fond_truda)*100;
							} else {
								$proc = 0;
							}
                           //  echo $proc.'<br>';
						    if ($proc > 50) $count_right_school++;

					    }
				    }
				 }
				 // echo "<b>$count_right_school</b>";
				 $proc = ($count_right_school/$count_sch)*100;
				 if ($proc < 70) $currstatus = 1;
				 else if ($proc > 70  && $proc < 90)  $currstatus = 2;
				      else $currstatus = 6;
*/
            }

 	break;
 	case 3:
			$currstatus = 6;
 			if ($schools)	{
        	     $schoolsarray = array();
			     foreach ($schools as $sa)  {
			        $schoolsarray[] = $sa->id;
	 			 }
		 	     $schoolslist = implode(',', $schoolsarray);

	 			 $strsql = "SELECT id, schoolid, status, shortname, datemodified FROM {$CFG->prefix}monit_school_listforms
		 			        WHERE (schoolid in ($schoolslist)) and (shortname='rkp_u') and (datemodified=$datefrom)";

  		 	     if ($listforms = get_records_sql($strsql)) 	{
		 	         $count_lf = count($listforms);
		 	         if ($count_lf != $count_sch) return $color[1];

			   		 $formsarray = array();
				     foreach ($listforms as $lf)  {
				        $formsarray[] = $lf->id;
				     }
				     $formslist = implode(',', $formsarray);
				 	 $recs = get_records_sql("SELECT id, f2_1u, f2_3u
				 	 						  FROM {$CFG->prefix}monit_form_rkp_u
								              WHERE listformid in ($formslist)");
					 foreach ($recs as $rec)	{
					 	 // if ($rec->f2_1u != 1 || $rec->f2_3u != 1) return $color[1];
                            if ($rec->f2_1u != 1) return $color[1];
					 }
	 	 	     } else {
	 	    	 	return $color[1];
		 	     }
		 	}
 	break;
 }

  return $color[$currstatus];
}

function table_monit_exists($table)
{
    global $CFG, $db;

    $exists = true;

    $tablename = $CFG->prefix.$table;

/// Search such tablename in DB
    $metatables = $db->MetaTables();
    $metatables = array_flip($metatables);
    $metatables = array_change_key_case($metatables, CASE_LOWER);
    if (!array_key_exists($tablename,  $metatables)) {
        $exists = false;
    }

    return $exists;
}


///
/// FUNCTIONS for map
///
function print_belgorod_region($newW=800, $newH=500, $analysetab=1, $nmonth = 1, $nyear = 1, $filename='map')
{
	global $CFG, $W,$H,$poly_data, $USER;

/*
    print_heading('<font color=red>ВНИМАНИЕ! Письма с темой "Ответы и отзывы:..." были разосланы системой сотрудникам образовательных учреждений, имеющим права учителя в системе. Полученные письма необходимо удалить.</font>');   
*/
    // print_heading('Доступ к сайту будет заблокирован с 19-00 до 22-00  30 ноября 2009 г.', 'center');   return false;
    
    // print_heading('ВНИМАНИЕ! 5 июля 2010 г. с 15-00 до 18-00 система электронного мониторинга образовательных учреждений будет отключена для проведения профилактических работ.'); 
    // print_heading('<font color=red>ВНИМАНИЕ! 6 августа 2013 г. с 10-00 до 14-00 система электронного мониторинга образовательных учреждений будет закрыта для проведения профилактических работ. Приносим свои извинения за доставленные неудобства.</font>');
    // print_heading('<font color=red>ВНИМАНИЕ! Система электронного мониторинга образовательных учреждений отключена для проведения профилактических работ.</font>');  
	// return false;
    
    if (record_exists_select('monit_att_staff', "userid = $USER->id AND schoolid in (3385, 2769, 2116)"))   {
        print_heading(get_string('staffdeleted', 'block_monitoring'));        
        exit();
    } 
    

	$localstring['msgmaptitle'] = 'Значения цветовых индикаторов электронного мониторинга образовательных учреждений: ';
	$localstring['msgstatus11'] = '- в районе есть хотя бы одна незаполненная форма с показателями мониторинга;';
	$localstring['msgstatus12'] = '- в районе все формы с показателями мониторинга находятся в процессе заполнения или согласования;';
	$localstring['msgstatus13'] = '- в районе все показатели проверены и приняты после согласования.';
	$localstring['msgstatus21'] = '- показатели, характеризующие введение НСОТ не имеют ссылок на действующие сайты; нет реквизитов принятых документов; в районе не выдерживается запланированная наполняемость классов и соотношение ФОТ учителей в общем объеме ФОТ;';
	$localstring['msgstatus22'] = '- показатели, характеризующим введение НСОТ имеют ссылки на действующие сайты; есть реквизиты принятых документов; в 70% школ района выдерживается запланированное соотношение ФОТ учителей в общем объеме ФОТ (50%);';
	$localstring['msgstatus23'] = '- показатели, характеризующим введение НСОТ имеют ссылки на действующие сайты; есть реквизиты принятых документов; в 90% школ района выдерживается запланированное соотношение ФОТ учителей в общем объеме ФОТ (50%).';
	$localstring['msgstatus31'] = '- показатели, характеризующие введение НПФ не имеют ссылок на действующие сайты, нет реквизитов принятых документов;';
	$localstring['msgstatus32'] = '- показатели, характеризующие введение НПФ не имеют ссылок на действующие сайты, есть реквизиты принятых документов;';
	$localstring['msgstatus33'] = '- показатели, характеризующие введение НПФ имеют ссылки на действующие сайты, есть реквизиты принятых документов.';
	$localstring['msgmapfooter'] = '<i>Для знакомства с различными показателями, собранными в результате мониторинга, выберите интересующий Вас район на интерактивной карте.</i>';

    if(!table_monit_exists('monit_region')) return false;
    $W = $newW;
    $H = $newH;
    $koefficient = 800/$newW;
    
	$filename .= '_'. $analysetab. '_' . $nyear . '_' . $nmonth . '.gif';
	$currtime = 0;
	
	$filearea = "/1/maps/";
	// if ($basedir = make_upload_directory($filearea))   {
	$ffurl = "$CFG->wwwroot/file.php/$filearea/$filename";	 
	$filename = $CFG->dataroot .  $filearea . $filename; 

    $flag_generate = false;
    $wheregenerate = array(6,7,9,10,12);
    if (in_array($nmonth, $wheregenerate)) {
               
    	if (file_exists($filename)) {
    		$ftime = filemtime($filename) + 2*HOURSECS;
    		$currtime = time();
    		if ($currtime > $ftime)	{
    			$flag_generate = true;
    		} 
    	}	else {
    		$flag_generate = true;
    	}
    	// echo $filename;
    }    
	// Создадим изображение и выделим цвета
	if ($flag_generate)	{
		$im = imagecreatetruecolor($W, $H);
		drawmap($im, $koefficient, $analysetab, $nmonth, $nyear);
	
		if (function_exists("imagegif")) {
	 	   imagegif($im, $filename);
	    }
	    imagedestroy($im);
   } else {
   		drawmap2();
   }	    

   $toprow = array();
   for ($i=1; $i<=3; $i++)  {
	   $toprow[] = new tabobject('tabmap'.$i, $CFG->wwwroot."/index.php?tab=$i",
 	               get_string('tabmap'.$i, 'block_monitoring'));
   }
   $tabs = array($toprow);
   print_heading(get_string('titlemap', 'block_monitoring'), 'center', 4);
   print_tabs($tabs, 'tabmap'.$analysetab, NULL, NULL);


	// Print tabs years
	$toprow1 = array();
    $link = $CFG->wwwroot."/index.php?tab=$analysetab&amp;year=";
    if ($years = get_records_select('monit_years', '', '', 'id, name'))  {
    	foreach ($years as $year)	{
	       $toprow1[] = new tabobject($year->id, $link.$year->id, get_string('uchyear', 'block_monitoring', $year->name));
	    }
  	}
    $tabs1 = array($toprow1);
	print_tabs($tabs1, $nyear, NULL, NULL);


   // Print tabs months
   /*$toprow4 = array(); 
    for ($i=9; $i<=12; $i++)   {
       $toprow4[] = new tabobject($i, $link.$i, get_string('nm_'.$i, 'block_monitoring'));
  	}
    for ($i=1; $i<=8; $i++)   {
       $toprow4[] = new tabobject($i, $link.$i, get_string('nm_'.$i, 'block_monitoring'));
  	}
    $tabs4 = array($toprow4);
	print_tabs($tabs4, $nmonth, NULL, NULL);
    */
    
   $link = $CFG->wwwroot."/index.php?tab=$analysetab&amp;year=$nyear&amp;nm=";
   print_tabs_all_months_maps($nmonth, $link);

   print_box_start();
   print "<map name=mymap>$poly_data</map><center><img border=0 src=$ffurl usemap=#mymap></center>";
   echo '<p></p>'.$localstring['msgmaptitle'];
   // print_string('msgmaptitle', 'block_monitoring');
   $sc[1] = get_string('status1color', 'block_monitoring');
   $sc[2] = get_string('status2color', 'block_monitoring');
   $sc[3] = get_string('status6color', 'block_monitoring');


   echo '<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2"><tbody>';
   for ($i=1; $i<=3; $i++)  {
   	   // $msg = get_string('msgstatus'.$i, 'block_monitoring');
   	   $msg = $localstring['msgstatus'.$analysetab.$i];
	   echo '<tr><td width="25">';
	   echo '<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">';
	   echo '<tbody><tr><td width="25" bgcolor="#'. $sc[$i] .'">&nbsp;</td></tr></tbody></table>';
       echo '</td><td>'. $msg . '</td></tr>';
   }
   echo '</tbody></table>';
   echo '<p></p>'.$localstring['msgmapfooter'];
   // print_string('msgmapfooter', 'block_monitoring');
   print_box_end();
}

function drawmap($im, $koefficient = 1, $analysetab = 1,  $nmonth = 1, $nyear = 1)
{
	global $CFG;

    $allrayons = get_records_sql("SELECT * FROM {$CFG->prefix}monit_rayon");

	$font_size = 9;
	$bg = 0xFFFFFF;
	imagefilledrectangle($im,0,0,imagesx($im),imagesy($im),$bg);

	$ink=imagecolorallocate($im,0,0,0);

    // Прорисовка областей
	$link = $CFG->wwwroot.'/blocks/monitoring/indices/indices.php?level=school&amp;rid=';
	drawillustratorfile($im, 'belgorod.crd', 0xFFFFFF);
	if ($allrayons) {
		foreach($allrayons as $rayon)	{
			if ($rayon->filemap != NULL)	{
	  		    $statuscolor = analys_rayon_reports($rayon->id, $analysetab,  $nmonth,  $nyear);
		    	drawillustratorfile($im, $rayon->filemap, $statuscolor, $link.$rayon->id);
				if ($rayon->filemap == 'belregion.crd')	{
					imageline($im, 214,253,239,261, $statuscolor);
					imageline($im, 213,253,238,261, $statuscolor);
				}
			}
		}
    }

    for ($i=1; $i<=22; $i++)	{
    	list($x,$y,$name) = explode(';', get_string('town_coords_'.$i, 'block_monitoring'));
    	$x = $x/$koefficient;
		$y = $y/$koefficient;
		imagettftext($im, $font_size, 0, $x, $y, 0x000000, 'arial.ttf', $name);
	}
}


function drawillustratorfile($im, $filename, $color, $link='')
{
	global $CFG, $W,$H, $maxx, $minx, $maxy, $miny, $mx, $my, $poly_data;

	// Чтения файла
	// $fn = $CFG->wwwroot.'/blocks/monitoring/map/'.$filename;
    $fn = $CFG->dirroot.'/blocks/monitoring/map/'.$filename;
	$d=file($fn);
	$points=Array();
	$num=-1;

	if ($filename == 'belgorod.crd')
	{
		$r=explode(";",$d[0]);
		$maxx=$minx=$r[1];
		$maxy=$miny=$r[0];
		for ($i=0;$i<count($d);$i++){
			$r=explode(";",$d[$i]);
			$x=$r[1];
			$y=$r[0];

			if ($x>$maxx) $maxx=$x;
			if ($x<$minx) $minx=$x;

			if ($y>$maxy) $maxy=$y;
			if ($y<$miny) $miny=$y;
			$num++;
			$points[$num][]=$x;
			$points[$num][]=$y;
		}

		$mx = $W / abs($maxx - $minx);
		$my = $H / abs($maxy - $miny);
	}
	else
	{
		for ($i=0;$i<count($d);$i++){
			$r=explode(";",$d[$i]);
			$x=$r[1];
			$y=$r[0];
			$num++;
			$points[$num][]=$x;
			$points[$num][]=$y;
		}
	}

	$poly=Array();
	$point='';
	for ($i=0;$i<count($d);$i++) {
//		Нормализуем и масштабируем координаты
		$x=round(($points[$i][0]-$minx)*$mx);
		$y=round(($points[$i][1]-$miny)*$my);

		$y = $H - $y;
//		Заносим координаты в массив, по которому будет построен полигон
		$poly[]=$x;
		$poly[]=$y;
		$point = $point.$x.','.$y.',';
//		print "$x	$y<br>";
	}

	// Вывод полигона
	imagefilledpolygon($im, $poly, count($poly)/2, $color);
	imagepolygon($im, $poly, count($poly)/2, 0x000000);

	if($filename!='belgorod.crd')
	{
		$poly_data.= '<AREA shape=poly coords="'.$point.'" href='.$link.'>';
//		print $poly_data."dfgdfg";
	}
}

function drawmap2()
{
	global $CFG;

	$link = $CFG->wwwroot.'/blocks/monitoring/indices/indices.php?level=school&amp;rid=';
	drawillustratorfile2('belgorod.crd');

    // Прорисовка областей
	if ($allrayons = get_records_sql("SELECT * FROM {$CFG->prefix}monit_rayon")) {
		foreach($allrayons as $rayon)	{
			if ($rayon->filemap != NULL)	{
		    	drawillustratorfile2($rayon->filemap, $link.$rayon->id);
			}
		}
    }
}

function drawillustratorfile2($filename, $link='')
{
	global $CFG, $W,$H, $maxx, $minx, $maxy, $miny, $mx, $my, $poly_data;

	// Чтения файла
	// $fn = $CFG->wwwroot.'/blocks/monitoring/map/'.$filename;
    $fn = $CFG->dirroot.'/blocks/monitoring/map/'.$filename;
	$d=file($fn);
	$points=Array();
	$num=-1;

	if ($filename == 'belgorod.crd')
	{
		$r=explode(";",$d[0]);
		$maxx=$minx=$r[1];
		$maxy=$miny=$r[0];
		for ($i=0;$i<count($d);$i++){
			$r=explode(";",$d[$i]);
			$x=$r[1];
			$y=$r[0];

			if ($x>$maxx) $maxx=$x;
			if ($x<$minx) $minx=$x;

			if ($y>$maxy) $maxy=$y;
			if ($y<$miny) $miny=$y;
			$num++;
			$points[$num][]=$x;
			$points[$num][]=$y;
		}

		$mx = $W / abs($maxx - $minx);
		$my = $H / abs($maxy - $miny);
	}
	else
	{
		for ($i=0;$i<count($d);$i++){
			$r=explode(";",$d[$i]);
			$x=$r[1];
			$y=$r[0];
			$num++;
			$points[$num][]=$x;
			$points[$num][]=$y;
		}
	}

	$poly=Array();
	$point='';
	for ($i=0;$i<count($d);$i++) {
//		Нормализуем и масштабируем координаты
		$x=round(($points[$i][0]-$minx)*$mx);
		$y=round(($points[$i][1]-$miny)*$my);

		$y = $H - $y;
//		Заносим координаты в массив, по которому будет построен полигон
		$poly[]=$x;
		$poly[]=$y;
		$point = $point.$x.','.$y.',';
//		print "$x	$y<br>";
	}

	if($filename!='belgorod.crd')
	{
		$poly_data.= '<AREA shape=poly coords="'.$point.'" href='.$link.'>';
	}
}

// Print tabs months
function print_tabs_all_months_maps(&$nmonth, $link = '', $isinactive=true)
{
    if ($isinactive)    {
        $INACTIVE_MONTH = '1,2,3,4,5,7,8,10,11';
        $inactive = explode(',', $INACTIVE_MONTH);
        if ($nmonth >= 1 && $nmonth<=5) {
            $nmonth = 12;
        } else if ($nmonth >= 7 && $nmonth <= 8) {
            $nmonth = 6;
        } else if ($nmonth >= 10 && $nmonth <= 11) {
            $nmonth = 9;
        }
       // print_object($inactive);
    } else {
        $inactive = NULL;
    }
    
	$toprow4 = array();

    for ($i=9; $i<=12; $i++)   {
       $stri = get_string('nm_'.$i, 'block_monitoring');
       if ($i == $nmonth) {
          $stri = "<b>$stri</b>";
       } 
       $toprow4[] = new tabobject($i, $link.$i, $stri);
  	}

    for ($i=1; $i<=8; $i++)   {
       $stri = get_string('nm_'.$i, 'block_monitoring');
       if ($i == $nmonth) {
          $stri = "<b>$stri</b>";
       } 
       $toprow4[] = new tabobject($i, $link.$i, $stri);
  	}
    $tabs4 = array($toprow4);

   //  print_heading(get_string('terms','block_monitoring'), 'center', 4);
	print_tabs($tabs4, $nmonth, $inactive, NULL);
}

?>