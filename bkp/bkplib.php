<?php // $Id: bkplib.php,v 1.15 2008/02/28 13:05:00 Zagorodnyuk Exp $

    if (!empty($SESSION->has_timed_out)) {
        $session_has_timed_out = true;
        $SESSION->has_timed_out = false;
    } else {
        $session_has_timed_out = false;
    }

    if ($session_has_timed_out) {
        $errormsg = get_string('sessionerroruser', 'error');
        echo '<div class="loginerrors">';
        formerr($errormsg);
        echo '</div>';
    }


function verify_visible_form($schooltype, $formid, $sid)
{	global $yid, $CFG, $datemodified, $nm;
	$sql_razdelids = get_records_sql("SELECT id FROM {$CFG->prefix}monit_razdel WHERE formid=$formid");
    $result = false;

	foreach ($sql_razdelids as $sql_razdelid) {		$razdelvisible = verify_visible_razdel($schooltype, $sql_razdelid->id, $formid, $sid);

//print "$razdelvisible=$razdelvisible<br>";
		if( $razdelvisible == true) {
			$result = true;
		}
	}

    $sql = get_record_sql("SELECT fullname FROM {$CFG->prefix}monit_form WHERE id=$formid");

//print "fullname = $sql->fullname'  'schooltype=$schooltype<br>";

//print "resultdo=$result";
//    if(($sql->fullname == 'osh-5')&&($schooltype == 1)) {//		$result = true;//    }
//print "resultposle=$result";

	return $result;
}

function verify_visible_razdel($schooltype, $razdelid, $formid, $sid)
{	global $yid, $CFG, $datemodified, $nm;

	$result = true;

	$sqls_on = get_records_sql("SELECT id FROM {$CFG->prefix}monit_razdel_field WHERE razdelid=$razdelid");
	$sqls_off = get_records_sql("SELECT idfield FROM {$CFG->prefix}monit_options WHERE idtypeschool=$schooltype");
	if(!$sqls_off) {
	    $seconds[0] ='0';
	}

	foreach ($sqls_on as $sql) {
		$firsts[$sql->id] = $sql->id;
	}
	foreach ($sqls_off as $sql) {
		$seconds[$sql->idfield] = $firsts[$sql->idfield];
	}

	$datas = array_diff($firsts, $seconds);

	if(count($datas) == 0) {		$result = false;
	}

    $sql = get_record_sql("SELECT fullname FROM {$CFG->prefix}monit_form WHERE id=$formid");
    $fullname = $sql->fullname;
    $sql = get_record_sql("SELECT isukg FROM {$CFG->prefix}monit_school WHERE id=$sid");

//print "resultdo=$result<br>fullname=$sql->fullname        schooltype=$schooltype<br>";
//print "(($fullname == 'osh-5')&&($schooltype == 1)&&($sql->isukg == 1))<br>";

//    if(($fullname == 'osh-5')&&($schooltype in ('1,2,3,4,5,6,7,8,9'))&&($sql->isukg == 1)) {    $pos = strpos('0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,', "$schooltype,");
//print "sssss=$sssss<br>";
//print_r($sssss);
//    if(($fullname == 'osh-5')&&(strpos("$schooltype,", '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89'))&&($sql->isukg == 1)) {

    if(($fullname == 'osh-5')&&($pos != 0)&&($sql->isukg == 1)) {
		$result = true;
    }
//print "resultposle=$result<br><br>";
	return $result;
}

function d12($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	foreach ($table_rzds as $table_rzd)  {

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

		list($formname, $rzdid) = explode(",", $table_rzd->shortname);
		$fid_main = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='$formname'");
		$rzdid = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$fid_main->id and shortname=$rzdid");
		$listforms = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where rayonid=$rid and shortname=$rzdid->id and datemodified=$datemodified");
//		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where (schoolid in ($listform))");
		$listformsarray = array();
	    foreach ($listforms as $listform)  {
	        $listformsarray[] = $listform->id;
	    }

		$fields = get_records_sql("select name_field, calcfunc from {$CFG->prefix}monit_razdel_field where razdelid=$rzdid->id");
		$str = '';

		foreach($fields as $field)  {
			$str.='sum(`'.$field->name_field.'`),';
		}
		$str = substr($str, 0, -1);

	    $listform = implode(',', $listformsarray);
	    $sqls = get_records_sql("select $str from {$CFG->prefix}monit_bkp_table_$rzdid->id where (listformid in ($listform))");

        foreach($sqls as $key=>$val)  {
			$sql = '';
        	$datas = (array)$val;
        	foreach($datas as $key=>$value)  {
        		$field = substr($key, 0, -2);
                $field = substr($field, 5);
				$sql.="`$field`=$value,";
        	}

        	$sql = substr($sql, 0, -1);
		    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
		    if(!$table)  {
		    	$lf->listformid=$listformid;
		    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
		    }
			$sql = "update {$CFG->prefix}monit_bkp_table_$table_rzd->id set ".$sql." where listformid=$listformid";

			$fields = get_records_sql("select name_field, calcfunc from {$CFG->prefix}monit_razdel_field where razdelid=$table_rzd->id");
			foreach($fields as $field)  {
				$sql = str_replace($field->calcfunc, $field->name_field, $sql);
			}
			execute_sql($sql, false);
        }
	}
}

function calcdata($fid, $rid)
{
	global $yid,$CFG;
	$table = get_record('monit_form', 'id', $fid);
	$table_rzds = get_records_sql("select id, shortname from {$CFG->prefix}monit_razdel where formid=$fid");
	switch ($table->fullname)	{
		case '76-rik':
			rik76($table_rzds, $rid, 0);
		break;
		case '76-rik-i':
			rik76($table_rzds, $rid, 1);
		break;
		case '76-rik-fil':
			rik76($table_rzds, $rid, 2);
		break;
		case 'd-12':
			d12($table_rzds, $rid);
		break;
		case 'd-4':
			d4($table_rzds, $rid);
		break;
		case 'd-7':
			d7($table_rzds, $rid);
		break;
		case 'd-8':
			d8($table_rzds, $rid);
		break;
		case 'd-13':
			d13($table_rzds, $rid);
		break;
		case 'd-6':
			d6($table_rzds, $rid);
		break;
		case 'd-9':
			d9($table_rzds, $rid);
		break;
		case 'sv-1':
			sv1($table_rzds, $rid);
		break;
	}
}

function updaterzd($id, $data, $listformid)
{
global $yid,$CFG;

	$table_fields = get_records_sql("select name_field from {$CFG->prefix}monit_razdel_field where razdelid=$id");
	$sql = '';

	foreach ($table_fields as $table_field) {
		if(isset($data[$table_field->name_field]))  {
			if($data[$table_field->name_field] == '')  {
				$data[$table_field->name_field] = 0;
			}
			if(empty($data[$table_field->name_field]))  {				$data[$table_field->name_field] = 0;
			}
			if(is_numeric($data[$table_field->name_field]))  {
				$value=$data[$table_field->name_field].',';
			} else {
				$value="'".$data[$table_field->name_field]."',";
			}
			if(($table_field->name_field != '') && ($value != '')) {
				$sql.="`$table_field->name_field`=".$value;
			}
		}
	}

	$sql = substr($sql, 0, strlen($sql) - 1);
	$sql = "update {$CFG->prefix}monit_bkp_table_$id set ".$sql." where listformid=$listformid";

	execute_sql($sql, false);
}

function rik76rzd1_02_16($rid, $stateinstitution, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$sqls = get_record_sql("select count(id) as cccc from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1 and stateinstitution=$stateinstitution");

	$result[$field0] = $sqls->cccc;

	$sqls = get_record_sql("select count(id) as cccc from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1 and stateinstitution=$stateinstitution");
	$result[$field1] = $sqls->cccc;
	$result[$field2] = $result[$field0] + $result[$field1];
	return $result;
}

function rik76rzd1_18_29($rzd, $rid, $stateinstitution, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1 and stateinstitution=$stateinstitution");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);
//		print $listform.'<br>';

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r4-18-4`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1 and stateinstitution=$stateinstitution");

    if($sqls) {
		unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $listss[] = $sql->id;
		    }

		    $listschool = implode(',', $listss);
		    $sqls = get_record_sql("select sum(`f-r4-18-4`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_28($rzd, $rid, $stateinstitution, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1 and stateinstitution=$stateinstitution");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r4-18-9`) as cccc0, sum(`f-r4-18-11`) as cccc1, sum(`f-r4-18-13`) as cccc2 from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc0 + $sqls->cccc1 + $sqls->cccc2;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1 and stateinstitution=$stateinstitution");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
	    	unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r4-18-9`) as cccc0, sum(`f-r4-18-11`) as cccc1, sum(`f-r4-18-13`) as cccc2 from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc0 + $sqls->cccc1 + $sqls->cccc2;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_30($rzd, $rid, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r6-01`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r6-01`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_31($rzd, $rid, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(`id`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `f-r8-03` <> 'null')");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(`id`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `f-r8-03` <> 'null')");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_33($rzd, $rid, $field0, $field1, $field2, $field3)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `f-r8-03` <> 'null')");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `f-r8-03` <> 'null')");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_34($rzd, $rid, $field0, $field1, $field2)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r4-19`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`f-r4-19`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_35($rzd, $rid, $field0, $field1, $field2, $field3)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(`id`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `$field3` <> 'null')");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(`id`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `$field3` <> 'null')");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_37($rzd, $rid, $field0, $field1, $field2, $field3)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field1] = $sqls->cccc;
		}
	}
	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd1_39($rzd, $rid, $field0, $field1, $t=0)
{
global $yid, $CFG, $datemodified;

    $result[$field0] = 0;
	switch ($t) {
		case 0:
			$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");
		break;
		case 1:
			$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and stateinstitution<>16");
		break;
		case 2:
			$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and stateinstitution = 16");
		break;
	}

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field1`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}
	return $result;
}

function rik76rzd1_43($rzd, $rid, $field0, $field1)
{
global $yid, $CFG, $datemodified;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field1`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field0] = $sqls->cccc;
		}
	}
	return $result;
}

function rik76rzd2($rzd, $rid, $field0, $field1, $field2, $t, $category)
{
global $yid, $CFG, $datemodified;
    $result[$field0] = $result[$field1] = $result[$field2] = 0;
	$dats[0]=$datss[0]=0;
	$dats[1]=$datss[1]=0;


	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {

		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_records_sql("select * from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

	    	$dats[0] = 0;
	    	$dats[1] = 0;
		    foreach ($sqls as $sql)  {		    	$sql = (array)$sql;
		    	$str = $sql['f-r3-01'];
		    	$k = 0;  $g = 0;
		    	$dat[0] = '';
		    	$dat[1] = '';
				for ($i = 0; $i <= strlen($str) - 1; $i++) {
                    if($str[$i] >= '0' && $str[$i] <='9') {	                    $dat[$k].= $str[$i];
                    } else {                    	$k = 1;                    }
				}
		    	$dats[0] = $dats[0]+$dat[0];
		    	$dats[1] = $dats[1]+$dat[1];
		    }
		    $result[$field0] = $dats[0].'/'.$dats[1];
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_records_sql("select * from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

	    	$datss[0] = 0;
	    	$datss[1] = 0;
		    foreach ($sqls as $sql)  {
		    	$sql = (array)$sql;
		    	$str = $sql['f-r3-01'];
		    	$k = 0;  $g = 0;
		    	$dat[0] = '';
		    	$dat[1] = '';
				for ($i = 0; $i <= strlen($str) - 1; $i++) {
                    if($str[$i] >= '0' && $str[$i] <='9') {
	                    $dat[$k].= $str[$i];
                    } else {
                    	$k = 1;
                    }
				}
		    	$datss[0] = $datss[0] + $dat[0];
		    	$datss[1] = $datss[1] + $dat[1];
		    }
		    $result[$field1] = $datss[0].'/'.$datss[1];
		}
	}

	$a = $dats[0]+$datss[0];
	$b = $dats[1]+$datss[1];
	$c = $a.'/'.$b;

	$result[$field2] =  $c;

	return $result;
}

function rik76rzd3_02($rzd, $rid, $field0, $field1, $field2, $field3, $t, $category)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `$field3` <> 'null')");
		    $result[$field0] = $sqls->cccc;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select sum(`$field3`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool) and `$field3` <> 'null')");
		    $result[$field1] = $sqls->cccc;
		}
	}

	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd3($rzd, $rid, $field0, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $t, $category)
{
global $yid, $CFG, $datemodified;

	$result[$field0] = $result[$field1] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $str = '';

		    $sqls = get_record_sql("select sum(`$field3`) as c, sum(`$field4`) as cc, sum(`$field5`) as ccc $str from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

		    if($field6 != '') {		    	$str.= ", sum(`$field6`) as cccc";
		    } else
		    if($field7 != '') {
		    	$str.= ", sum(`$field7`) as ccccc";
		    } else
		    if($field8 != '') {		    	$str.= ", sum(`$field8`) as cccccc";		    }

		    $sqls = get_record_sql("select sum(`$field3`) as c, sum(`$field4`) as cc, sum(`$field5`) as ccc $str from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

		    $result[$field0] = $sqls->c + $sqls->cc + $sqls->ccc + $sqls->cccc + $sqls->ccccc + $sqls->cccccc;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $str = '';
		    if($field6 != '') {
		    	$str.= ", sum(`$field6`) as cccc";
		    }

		    if($field7 != '') {
		    	$str.= ", sum(`$field7`) as ccccc";
		    }

		    if($field8 != '') {
		    	$str.= ", sum(`$field8`) as cccccc";
		    }

		    $sqls = get_record_sql("select sum(`$field3`) as c, sum(`$field4`) as cc, sum(`$field5`) as ccc $str from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field1] = $sqls->c + $sqls->cc + $sqls->ccc + $sqls->cccc + $sqls->ccccc + $sqls->cccccc;
		}
	}

	$result[$field2] = $result[$field0] + $result[$field1];

	return $result;
}

function rik76rzd4($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;

    $result[$field[0]] = $result[$field[2]] = $result[$field[3]] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

		    $result[$field[0]] = $sqls->c;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");

		    $result[$field[2]] = $sqls->c;
		}
	}

    $result[$field[3]] = $result[$field[0]] + $result[$field[2]];

	return $result;
}


function rik76rzd5($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;

    $result[$field[0]] = $result[$field[2]] = $result[$field[4]] = $result[$field[10]] = $result[$field[11]] = $result[$field[12]] = $result[$field[13]] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[5]`) as ccc, sum(`$field[9]`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field[0]] = $sqls->c;
		    $result[$field[2]] = $sqls->cc;
		    $result[$field[4]] = $sqls->ccc;
		    $result[$field[10]] = $sqls->cccc;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[5]`) as ccc, sum(`$field[9]`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field[11]] = $sqls->c;
		    $result[$field[12]] = $sqls->cc;
		    $result[$field[13]] = $sqls->ccc;
		    $result[$field[10]] = $result[$field[10]] + $sqls->cccc;
		}
	}

    $result[$field[6]] = $result[$field[0]] + $result[$field[11]];
    $result[$field[7]] = $result[$field[2]] + $result[$field[12]];
    $result[$field[8]] = $result[$field[4]] + $result[$field[13]];

	return $result;
}

function rik76rzd6($rzd, $rid, $field, $i, $t, $category)
{
global $yid, $CFG, $datemodified;

	$result[$field[11]] =  $result[$field[0]] = $result[$field[2]] = $result[$field[4]] = $result[$field[6]] = $result[$field[10]] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

			switch ($i)	{
				case 0:
				    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
				break;
				case 1:
				    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[11]`) as ccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
				    $result[$field[10]] = $sqls->ccc;
				break;
			}


		    $result[$field[0]] = $sqls->c;
		    $result[$field[2]] = $sqls->cc;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

			switch ($i)	{
				case 0:
				    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
				break;
				case 1:
				    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[11]`) as ccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
			    $result[$field[10]] = $result[$field[10]] + $sqls->ccc;
				break;
			}

		    $result[$field[4]] = $sqls->c;
		    $result[$field[6]] = $sqls->cc;
		}
	}

	$result[$field[8]] = $result[$field[0]] + $result[$field[2]] + $result[$field[4]] + $result[$field[6]];
	$result[$field[9]] = $result[$field[2]] + $result[$field[6]];

	return $result;
}

function rik76rzd8($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;
	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (`$field[1]`='Да')and(listformid in ($listschool))");
		    if($sqls) {
			    $result[$field[0]] = $sqls->c;
			} else {
			    $result[$field[0]] = 0;
			}

		    $sqls = get_record_sql("select sum(`$field[3]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where listformid in ($listschool)");
		    $result[$field[2]] = $sqls->c;

		    $sqls = get_record_sql("select sum(`$field[5]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where listformid in ($listschool)");
		    $result[$field[4]] = $sqls->c;


		    $sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (`$field[7]`='Да')and(listformid in ($listschool))");
		    if($sqls) {
			    $result[$field[6]] = $sqls->c;
			} else {			    $result[$field[6]] = 0;			}

		    $sqls = get_record_sql("select sum(`$field[9]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where listformid in ($listschool)");
		    $result[$field[8]] = $sqls->c;

		    $sqls = get_record_sql("select sum(`$field[11]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where listformid in ($listschool)");
		    $result[$field[10]] = $sqls->c;
		}
	}
	return $result;
}

function rik76rzd9($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (`$field[1]`='Да')and(listformid in ($listschool))");
		    $result[$field[0]] = $sqls->c;

		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (`$field[3]`='Да')and(listformid in ($listschool))");
		    $result[$field[2]] = $sqls->c;
		}
	}
	return $result;
}

function rik76rzd10($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where (`$field[1]`='Да')and(listformid in ($listschool))");
		    $result[$field[0]] = $sqls->c;

		    $sqls = get_record_sql("select sum(`$field[3]`) as c from {$CFG->prefix}monit_bkp_table_$rzd->id where listformid in ($listschool)");
		    $result[$field[2]] = $sqls->c;
		}
	}

	return $result;
}

function rik76rzd11($rzd, $rid, $field, $t, $category)
{
global $yid, $CFG, $datemodified;

    $result[$field[0]] = $result[$field[2]] = $result[$field[8]] = $result[$field[10]] = $result[$field[4]] = $result[$field[5]] = $result[$field[12]] = $result[$field[13]] = 0;

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=-1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[9]`) as ccc, sum(`$field[11]`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field[0]] = $sqls->c;
		    $result[$field[2]] = $sqls->cc;
		    $result[$field[8]] = $sqls->ccc;
		    $result[$field[10]] = $sqls->cccc;
		}
	}

	if ($t==0) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=1");
	} else {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($category))");
	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`$field[1]`) as c, sum(`$field[3]`) as cc, sum(`$field[9]`) as ccc, sum(`$field[11]`) as cccc from {$CFG->prefix}monit_bkp_table_$rzd->id where (listformid in ($listschool))");
		    $result[$field[4]] = $sqls->c;
		    $result[$field[5]] = $sqls->cc;
		    $result[$field[12]] = $sqls->ccc;
		    $result[$field[13]] = $sqls->cccc;
		}
	}

	$result[$field[6]] = $result[$field[0]] + $result[$field[4]];
	$result[$field[7]] = $result[$field[2]] + $result[$field[5]];

	$result[$field[14]] = $result[$field[8]] + $result[$field[12]];
	$result[$field[15]] = $result[$field[10]] + $result[$field[13]];

	return $result;
}

function rik76($table_rzds, $rid, $i)
{
	global $yid, $CFG, $datemodified, $nm;

	foreach ($table_rzds as $table_rzd)  {

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");

		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
		if(!$table)  {
			$table_bkp->listformid = $listformid;
			insert_record("monit_bkp_table_$table_rzd->id", $table_bkp);
		}
		switch ($table_rzd->shortname)	{
			case 1:
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
				$rzd_id6 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=6");
				$rzd_id7 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=7");
				$rzd_id8 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
                unset($data);
				$data = rik76rzd1_02_16($rid, 1, 'f-r1-02-3', 'f-r1-02-4', 'f-r1-02-5', $i);
				$data = array_merge($data, rik76rzd1_02_16($rid, 2, 'f-r1-03-3', 'f-r1-03-4', 'f-r1-03-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 3, 'f-r1-04-3', 'f-r1-04-4', 'f-r1-04-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 4, 'f-r1-05-3', 'f-r1-05-4', 'f-r1-05-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 5, 'f-r1-06-3', 'f-r1-06-4', 'f-r1-06-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 6, 'f-r1-07-3', 'f-r1-07-4', 'f-r1-07-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 7, 'f-r1-08-3', 'f-r1-08-4', 'f-r1-08-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 8, 'f-r1-09-3', 'f-r1-09-4', 'f-r1-09-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 9, 'f-r1-10-3', 'f-r1-10-4', 'f-r1-10-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 10, 'f-r1-11-3', 'f-r1-11-4', 'f-r1-11-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 11, 'f-r1-12-3', 'f-r1-12-4', 'f-r1-12-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 12, 'f-r1-13-3', 'f-r1-13-4', 'f-r1-13-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 13, 'f-r1-14-3', 'f-r1-14-4', 'f-r1-14-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 14, 'f-r1-15-3', 'f-r1-15-4', 'f-r1-15-5', $i));
				$data = array_merge($data, rik76rzd1_02_16($rid, 15, 'f-r1-16-3', 'f-r1-16-4', 'f-r1-16-5', $i));

				$data['f-r1-01-3'] = $data['f-r1-02-3'] + $data['f-r1-04-3'] + $data['f-r1-05-3'] + $data['f-r1-11-3'] + $data['f-r1-12-3'] + $data['f-r1-13-3'];
				$data['f-r1-01-4'] = $data['f-r1-02-4'] + $data['f-r1-04-4'] + $data['f-r1-05-4'] + $data['f-r1-11-4'] + $data['f-r1-12-4'] + $data['f-r1-13-4'];
				$data['f-r1-01-5'] = $data['f-r1-01-3'] + $data['f-r1-01-4'];

				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 1, 'f-r1-18-3', 'f-r1-18-4', 'f-r1-18-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 2, 'f-r1-19-3', 'f-r1-19-4', 'f-r1-19-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 3, 'f-r1-20-3', 'f-r1-20-4', 'f-r1-20-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 4, 'f-r1-21-3', 'f-r1-21-4', 'f-r1-21-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 5, 'f-r1-22-3', 'f-r1-22-4', 'f-r1-22-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 6, 'f-r1-23-3', 'f-r1-23-4', 'f-r1-23-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 7, 'f-r1-24-3', 'f-r1-24-4', 'f-r1-24-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 8, 'f-r1-25-3', 'f-r1-25-4', 'f-r1-25-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 9, 'f-r1-26-3', 'f-r1-26-4', 'f-r1-26-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 10, 'f-r1-27-3', 'f-r1-27-4', 'f-r1-27-5', $i));
				$data = array_merge($data, rik76rzd1_28($rzd_id4, $rid, 10, 'f-r1-28-3', 'f-r1-28-4', 'f-r1-28-5', $i));
				$data = array_merge($data, rik76rzd1_18_29($rzd_id4, $rid, 11, 'f-r1-29-3', 'f-r1-29-4', 'f-r1-29-5', $i));
				$data = array_merge($data, rik76rzd1_28($rzd_id4, $rid, 10, 'f-r1-28-3', 'f-r1-28-4', 'f-r1-28-5', $i));
				$data = array_merge($data, rik76rzd1_30($rzd_id6, $rid, 'f-r1-30-3', 'f-r1-30-4', 'f-r1-30-5', $i));
				$data = array_merge($data, rik76rzd1_31($rzd_id8, $rid, 'f-r1-31-3', 'f-r1-31-4', 'f-r1-31-5', $i));
				$data = array_merge($data, rik76rzd1_33($rzd_id8, $rid, 'f-r1-32-3', 'f-r1-32-4', 'f-r1-32-5', 'f-r8-05', $i));
				$data = array_merge($data, rik76rzd1_33($rzd_id8, $rid, 'f-r1-33-3', 'f-r1-33-4', 'f-r1-33-5', 'f-r8-06', $i));
				$data = array_merge($data, rik76rzd1_34($rzd_id4, $rid, 'f-r1-34-3', 'f-r1-34-4', 'f-r1-34-5', $i));
				$data = array_merge($data, rik76rzd1_35($rzd_id7, $rid, 'f-r1-35-3', 'f-r1-35-4', 'f-r1-35-5', 'f-r7-01-3', $i));
				$data = array_merge($data, rik76rzd1_35($rzd_id7, $rid, 'f-r1-36-3', 'f-r1-36-4', 'f-r1-36-5', 'f-r7-01-5', $i));
				$data = array_merge($data, rik76rzd1_37($rzd_id7, $rid, 'f-r1-37-3', 'f-r1-37-4', 'f-r1-37-5', 'f-r7-01-4', $i));
				$data = array_merge($data, rik76rzd1_37($rzd_id7, $rid, 'f-r1-38-3', 'f-r1-38-4', 'f-r1-38-5', 'f-r7-01-6', $i));
				$data = array_merge($data, rik76rzd1_39($rzd_id4, $rid, 'f-r1-39', 'f-r4-21', 1, $i));
				$data = array_merge($data, rik76rzd1_39($rzd_id4, $rid, 'f-r1-40', 'f-r4-22', $i));
				$data = array_merge($data, rik76rzd1_39($rzd_id4, $rid, 'f-r1-41', 'f-r4-21', 2, $i));
				$data = array_merge($data, rik76rzd1_39($rzd_id4, $rid, 'f-r1-42', 'f-r4-25', $i));
				$data = array_merge($data, rik76rzd1_43($rzd_id6, $rid, 'f-r1-43', 'f-r6-05', $i));
				$data = array_merge($data, rik76rzd1_43($rzd_id6, $rid, 'f-r1-44', 'f-r6-06', $i));

				$data['f-r1-17-3'] = $data['f-r1-18-3'] + $data['f-r1-20-3'] + $data['f-r1-21-3'] + $data['f-r1-27-3'] + $data['f-r1-29-3'];
				$data['f-r1-17-4'] = $data['f-r1-18-4'] + $data['f-r1-20-4'] + $data['f-r1-21-4'] + $data['f-r1-27-4'] + $data['f-r1-29-4'];
				$data['f-r1-17-5'] = $data['f-r1-17-3'] + $data['f-r1-17-4'];
			break;
			case 2:
				if($i<2) {
					$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
					$rzd_id3 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=3");
					unset($data);
					$data = rik76rzd2($rzd_id3, $rid, 'f-r2-01-3', 'f-r2-01-4', 'f-r2-01-5', $i, '11,12,13,14,15,17,18,27,28,32');
				}
			break;
			case 3:
				if($i<2) {
					$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
					$rzd_id2 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=2");
					unset($data);
					$data = rik76rzd3_02($rzd_id2, $rid, 'f-r3-02-3', 'f-r3-02-4', 'f-r3-02-5', 'f-r2-04', $i, '11,12,13,14,15,17,18,27,28,32');
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-03-3', 'f-r3-03-4', 'f-r3-03-5', 'f-r2-05', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-04-3', 'f-r3-04-4', 'f-r3-04-5', 'f-r2-06', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-06-3', 'f-r3-06-4', 'f-r3-06-5', 'f-r2-11', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-08a-3', 'f-r3-08a-4', 'f-r3-08a-5', 'f-r2-13a', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-08b-3', 'f-r3-08b-4', 'f-r3-08b-5', 'f-r2-13b', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-09-3', 'f-r3-09-4', 'f-r3-09-5', 'f-r2-14', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-10-3', 'f-r3-10-4', 'f-r3-10-5', 'f-r2-15', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-11-3', 'f-r3-11-4', 'f-r3-11-5', 'f-r2-16', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-12-3', 'f-r3-12-4', 'f-r3-12-5', 'f-r2-18', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-13-3', 'f-r3-13-4', 'f-r3-13-5', 'f-r2-19', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-14-3', 'f-r3-14-4', 'f-r3-14-5', 'f-r2-20', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3_02($rzd_id2, $rid, 'f-r3-15-3', 'f-r3-15-4', 'f-r3-15-5', 'f-r2-21', $i, '11,12,13,14,15,17,18,27,28,32'));

					$data = array_merge($data, rik76rzd3($rzd_id2, $rid, 'f-r3-01-3', 'f-r3-01-4', 'f-r3-01-5', 'f-r2-01', 'f-r2-02', 'f-r2-03', 'f-r2-04', 'f-r2-05', 'f-r2-06', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3($rzd_id2, $rid, 'f-r3-05-3', 'f-r3-05-4', 'f-r3-05-5', 'f-r2-07', 'f-r2-08', 'f-r2-09', 'f-r2-10', 'f-r2-11', '', $i, '11,12,13,14,15,17,18,27,28,32'));
					$data = array_merge($data, rik76rzd3($rzd_id2, $rid, 'f-r3-07-3', 'f-r3-07-4', 'f-r3-07-5', 'f-r2-12', 'f-r2-13a', 'f-r2-13b', '', '', '', $i, '11,12,13,14,15,17,18,27,28,32'));
				}
			break;
			case 4:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-9'");
				$rzd_id1 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=1");
                unset($data);
                unset($list_field);
                $list_field = explode(',', 'f-r4-01-3,f-r1-01-5,f-r4-01-4,f-r4-01-5');
				$data = rik76rzd4($rzd_id1, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');

                unset($list_field);
                $list_field = explode(',', 'f-r4-02-3,f-r1-01-6,f-r4-02-4,f-r4-02-5');
				$data = array_merge($data, rik76rzd4($rzd_id1, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r4-03-3,f-r1-01-7,f-r4-03-4,f-r4-03-5');
				$data = array_merge($data, rik76rzd4($rzd_id1, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));
				}
			break;
			case 5:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
                unset($data);
                unset($list_field);
                $list_field = explode(',', 'f-r5-01-3,f-r4-01-3,f-r5-01-4,f-r4-01-4,f-r5-01-5,f-r4-01-6,f-r5-01-9,f-r5-01-10,f-r5-01-11,f-r4-01-7,f-r5-01-12,f-r5-01-6,f-r5-01-7,f-r5-01-8');
				$data = rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');

                unset($list_field);
                $list_field = explode(',', 'f-r5-02-3,f-r4-02-3,f-r5-02-4,f-r4-02-4,f-r5-02-5,f-r4-02-6,f-r5-02-9,f-r5-02-10,f-r5-02-11,f-r4-02-7,f-r5-02-12,f-r5-02-6,f-r5-02-7,f-r5-02-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-03-3,f-r4-03-3,f-r5-03-4,f-r4-03-4,f-r5-03-5,f-r4-03-6,f-r5-03-9,f-r5-03-10,f-r5-03-11,f-r4-03-7,f-r5-03-12,f-r5-03-6,f-r5-03-7,f-r5-03-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-04-3,f-r4-04-3,f-r5-04-4,f-r4-04-4,f-r5-04-5,f-r4-04-6,f-r5-04-9,f-r5-04-10,f-r5-04-11,f-r4-04-7,f-r5-04-12,f-r5-04-6,f-r5-04-7,f-r5-04-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-05-3,f-r4-05-3,f-r5-05-4,f-r4-05-4,f-r5-05-5,f-r4-05-6,f-r5-05-9,f-r5-05-10,f-r5-05-11,f-r4-05-7,f-r5-05-12,f-r5-05-6,f-r5-05-7,f-r5-05-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-06-3,f-r4-06-3,f-r5-06-4,f-r4-06-4,f-r5-06-5,f-r4-06-6,f-r5-06-9,f-r5-06-10,f-r5-06-11,f-r4-06-7,f-r5-06-12,f-r5-06-6,f-r5-06-7,f-r5-06-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-07-3,f-r4-07-3,f-r5-07-4,f-r4-07-4,f-r5-07-5,f-r4-07-6,f-r5-07-9,f-r5-07-10,f-r5-07-11,f-r4-07-7,f-r5-07-12,f-r5-07-6,f-r5-07-7,f-r5-07-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-08-3,f-r4-08-3,f-r5-08-4,f-r4-08-4,f-r5-08-5,f-r4-08-6,f-r5-08-9,f-r5-08-10,f-r5-08-11,f-r4-08-7,f-r5-08-12,f-r5-08-6,f-r5-08-7,f-r5-08-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-09-3,f-r4-09-3,f-r5-09-4,f-r4-09-4,f-r5-09-5,f-r4-09-6,f-r5-09-9,f-r5-09-10,f-r5-09-11,f-r4-09-7,f-r5-09-12,f-r5-09-6,f-r5-09-7,f-r5-09-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

				$data['f-r5-10-3'] = $data['f-r5-01-3'] + $data['f-r5-02-3'] + $data['f-r5-03-3'] + $data['f-r5-04-3'] + $data['f-r5-05-3'] + $data['f-r5-06-3'] + $data['f-r5-07-3'] + $data['f-r5-08-3'] + $data['f-r5-09-3'];
				$data['f-r5-10-4'] = $data['f-r5-01-4'] + $data['f-r5-02-4'] + $data['f-r5-03-4'] + $data['f-r5-04-4'] + $data['f-r5-05-4'] + $data['f-r5-06-4'] + $data['f-r5-07-4'] + $data['f-r5-08-4'] + $data['f-r5-09-4'];
				$data['f-r5-10-5'] = $data['f-r5-01-5'] + $data['f-r5-02-5'] + $data['f-r5-03-5'] + $data['f-r5-04-5'] + $data['f-r5-05-5'] + $data['f-r5-06-5'] + $data['f-r5-07-5'] + $data['f-r5-08-5'] + $data['f-r5-09-5'];
				$data['f-r5-10-6'] = $data['f-r5-01-6'] + $data['f-r5-02-6'] + $data['f-r5-03-6'] + $data['f-r5-04-6'] + $data['f-r5-05-6'] + $data['f-r5-06-6'] + $data['f-r5-07-6'] + $data['f-r5-08-6'] + $data['f-r5-09-6'];
				$data['f-r5-10-7'] = $data['f-r5-01-7'] + $data['f-r5-02-7'] + $data['f-r5-03-7'] + $data['f-r5-04-7'] + $data['f-r5-05-7'] + $data['f-r5-06-7'] + $data['f-r5-07-7'] + $data['f-r5-08-7'] + $data['f-r5-09-7'];
				$data['f-r5-10-8'] = $data['f-r5-01-8'] + $data['f-r5-02-8'] + $data['f-r5-03-8'] + $data['f-r5-04-8'] + $data['f-r5-05-8'] + $data['f-r5-06-8'] + $data['f-r5-07-8'] + $data['f-r5-08-8'] + $data['f-r5-09-8'];
				$data['f-r5-10-9'] = $data['f-r5-01-9'] + $data['f-r5-02-9'] + $data['f-r5-03-9'] + $data['f-r5-04-9'] + $data['f-r5-05-9'] + $data['f-r5-06-9'] + $data['f-r5-07-9'] + $data['f-r5-08-9'] + $data['f-r5-09-9'];
				$data['f-r5-10-10'] = $data['f-r5-01-10'] + $data['f-r5-02-10'] + $data['f-r5-03-10'] + $data['f-r5-04-10'] + $data['f-r5-05-10'] + $data['f-r5-06-10'] + $data['f-r5-07-10'] + $data['f-r5-08-10'] + $data['f-r5-09-10'];
				$data['f-r5-10-11'] = $data['f-r5-01-11'] + $data['f-r5-02-11'] + $data['f-r5-03-11'] + $data['f-r5-04-11'] + $data['f-r5-05-11'] + $data['f-r5-06-11'] + $data['f-r5-07-11'] + $data['f-r5-08-11'] + $data['f-r5-09-11'];
				$data['f-r5-10-12'] = $data['f-r5-01-12'] + $data['f-r5-02-12'] + $data['f-r5-03-12'] + $data['f-r5-04-12'] + $data['f-r5-05-12'] + $data['f-r5-06-12'] + $data['f-r5-07-12'] + $data['f-r5-08-12'] + $data['f-r5-09-12'];

                unset($list_field);
                $list_field = explode(',', 'f-r5-11-3,f-r4-10-3,f-r5-11-4,f-r4-10-4,f-r5-11-5,f-r4-10-6,f-r5-11-9,f-r5-11-10,f-r5-11-11,f-r4-10-7,f-r5-11-12,f-r5-11-6,f-r5-11-7,f-r5-11-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-12-3,f-r4-11-3,f-r5-12-4,f-r4-11-4,f-r5-12-5,f-r4-11-6,f-r5-12-9,f-r5-12-10,f-r5-12-11,f-r4-11-7,f-r5-12-12,f-r5-12-6,f-r5-12-7,f-r5-12-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-13-3,f-r4-12-3,f-r5-13-4,f-r4-12-4,f-r5-13-5,f-r4-12-6,f-r5-13-9,f-r5-13-10,f-r5-13-11,f-r4-12-7,f-r5-13-12,f-r5-13-6,f-r5-13-7,f-r5-13-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-14-3,f-r4-13-3,f-r5-14-4,f-r4-13-4,f-r5-14-5,f-r4-13-6,f-r5-14-9,f-r5-14-10,f-r5-14-11,f-r4-13-7,f-r5-14-12,f-r5-14-6,f-r5-14-7,f-r5-14-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-15-3,f-r4-14-3,f-r5-15-4,f-r4-14-4,f-r5-15-5,f-r4-14-6,f-r5-15-9,f-r5-15-10,f-r5-15-11,f-r4-14-7,f-r5-15-12,f-r5-15-6,f-r5-15-7,f-r5-15-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

				$data['f-r5-16-3'] = $data['f-r5-11-3'] + $data['f-r5-12-3'] + $data['f-r5-13-3'] + $data['f-r5-14-3'] + $data['f-r5-15-3'];
				$data['f-r5-16-4'] = $data['f-r5-11-4'] + $data['f-r5-12-4'] + $data['f-r5-13-4'] + $data['f-r5-14-4'] + $data['f-r5-15-4'];
				$data['f-r5-16-5'] = $data['f-r5-11-5'] + $data['f-r5-12-5'] + $data['f-r5-13-5'] + $data['f-r5-14-5'] + $data['f-r5-15-5'];
				$data['f-r5-16-6'] = $data['f-r5-11-6'] + $data['f-r5-12-6'] + $data['f-r5-13-6'] + $data['f-r5-14-6'] + $data['f-r5-15-6'];
				$data['f-r5-16-7'] = $data['f-r5-11-7'] + $data['f-r5-12-7'] + $data['f-r5-13-7'] + $data['f-r5-14-7'] + $data['f-r5-15-7'];
				$data['f-r5-16-8'] = $data['f-r5-11-8'] + $data['f-r5-12-8'] + $data['f-r5-13-8'] + $data['f-r5-14-8'] + $data['f-r5-15-8'];
				$data['f-r5-16-9'] = $data['f-r5-11-9'] + $data['f-r5-12-9'] + $data['f-r5-13-9'] + $data['f-r5-14-9'] + $data['f-r5-15-9'];
				$data['f-r5-16-10'] = $data['f-r5-11-10'] + $data['f-r5-12-10'] + $data['f-r5-13-10'] + $data['f-r5-14-10'] + $data['f-r5-15-10'];
				$data['f-r5-16-11'] = $data['f-r5-11-11'] + $data['f-r5-12-11'] + $data['f-r5-13-11'] + $data['f-r5-14-11'] + $data['f-r5-15-11'];
				$data['f-r5-16-12'] = $data['f-r5-11-12'] + $data['f-r5-12-12'] + $data['f-r5-13-12'] + $data['f-r5-14-12'] + $data['f-r5-15-12'];

                unset($list_field);
                $list_field = explode(',', 'f-r5-17-3,f-r4-15-3,f-r5-17-4,f-r4-15-4,f-r5-17-5,f-r4-15-6,f-r5-17-9,f-r5-17-10,f-r5-17-11,f-r4-15-7,f-r5-17-12,f-r5-17-6,f-r5-17-7,f-r5-17-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-18-3,f-r4-16-3,f-r5-18-4,f-r4-16-4,f-r5-18-5,f-r4-16-6,f-r5-18-9,f-r5-18-10,f-r5-18-11,f-r4-16-7,f-r5-18-12,f-r5-18-6,f-r5-18-7,f-r5-18-8');
				$data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
                $list_field = explode(',', 'f-r5-19-3,f-r4-17-3,f-r5-19-4,f-r4-17-4,f-r5-19-5,f-r4-17-6,f-r5-19-9,f-r5-19-10,f-r5-19-11,f-r4-17-7,f-r5-19-12,f-r5-19-6,f-r5-19-7,f-r5-19-8');
                $data = array_merge($data, rik76rzd5($rzd_id4, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

				$data['f-r5-20-3'] = $data['f-r5-17-3'] + $data['f-r5-18-3'] + $data['f-r5-19-3'];
				$data['f-r5-20-4'] = $data['f-r5-17-4'] + $data['f-r5-18-4'] + $data['f-r5-19-4'];
				$data['f-r5-20-5'] = $data['f-r5-17-5'] + $data['f-r5-18-5'] + $data['f-r5-19-5'];
				$data['f-r5-20-6'] = $data['f-r5-17-6'] + $data['f-r5-18-6'] + $data['f-r5-19-6'];
				$data['f-r5-20-7'] = $data['f-r5-17-7'] + $data['f-r5-18-7'] + $data['f-r5-19-7'];
				$data['f-r5-20-8'] = $data['f-r5-17-8'] + $data['f-r5-18-8'] + $data['f-r5-19-8'];
				$data['f-r5-20-9'] = $data['f-r5-17-9'] + $data['f-r5-18-9'] + $data['f-r5-19-9'];
				$data['f-r5-20-10'] = $data['f-r5-17-10'] + $data['f-r5-18-10'] + $data['f-r5-19-10'];
				$data['f-r5-20-11'] = $data['f-r5-17-11'] + $data['f-r5-18-11'] + $data['f-r5-19-11'];
				$data['f-r5-20-12'] = $data['f-r5-17-12'] + $data['f-r5-18-12'] + $data['f-r5-19-12'];

				$data['f-r5-21-3'] = $data['f-r5-10-3'] + $data['f-r5-16-3'] + $data['f-r5-20-3'];
				$data['f-r5-21-4'] = $data['f-r5-10-4'] + $data['f-r5-16-4'] + $data['f-r5-20-4'];
				$data['f-r5-21-5'] = $data['f-r5-10-5'] + $data['f-r5-16-5'] + $data['f-r5-20-5'];
				$data['f-r5-21-6'] = $data['f-r5-10-6'] + $data['f-r5-16-6'] + $data['f-r5-20-6'];
				$data['f-r5-21-7'] = $data['f-r5-10-7'] + $data['f-r5-16-7'] + $data['f-r5-20-7'];
				$data['f-r5-21-8'] = $data['f-r5-10-8'] + $data['f-r5-16-8'] + $data['f-r5-20-8'];
				$data['f-r5-21-9'] = $data['f-r5-10-9'] + $data['f-r5-16-9'] + $data['f-r5-20-9'];
				$data['f-r5-21-10'] = $data['f-r5-10-10'] + $data['f-r5-16-10'] + $data['f-r5-20-10'];
				$data['f-r5-21-11'] = $data['f-r5-10-11'] + $data['f-r5-16-11'] + $data['f-r5-20-11'];
				$data['f-r5-21-12'] = $data['f-r5-10-12'] + $data['f-r5-16-12'] + $data['f-r5-20-12'];

				$data['f-r5-23-3'] = $data['f-r5-10-3'];
				$data['f-r5-23-4'] = $data['f-r5-10-4'];
				$data['f-r5-23-5'] = $data['f-r5-10-5'];
				$data['f-r5-23-6'] = $data['f-r5-10-6'];
				$data['f-r5-23-7'] = $data['f-r5-10-7'];
				$data['f-r5-23-8'] = $data['f-r5-10-8'];
				$data['f-r5-23-9'] = $data['f-r5-10-9'];
				$data['f-r5-23-10'] = $data['f-r5-10-10'];
				$data['f-r5-23-11'] = $data['f-r5-10-11'];
				$data['f-r5-23-12'] = $data['f-r5-10-12'];
				}
			break;

			case 6:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id5 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=5");
                unset($data);
				$category='11,12,13,14,15,17,18,27,28,32';
				$ii=0;

				if ($i==0) {
					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid");
				} else {
					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($category))");
				}


			    if($sqls) {
			    	unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

					if($sqls) {
						unset($lists);
					    foreach ($sqls as $sql)  {
				 	       $lists[] = $sql->id;
					    }

					    $listschool = implode(',', $lists);

					    $sqls = get_record_sql("select sum(`f-r5-16`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id5->id where (listformid in ($listschool))");
					    $ii = $sqls->c;
					}
				}

                $data['f-r6-16'] = $ii;

                unset($list_field);
				$list_field = explode(',', 'f-r6-01-3,f-r5-01-3,f-r6-01-4,f-r5-01-4,f-r6-01-5,f-r5-01-3,f-r6-01-6,f-r5-01-4,f-r6-01-7,f-r6-01-8,f-r6-01-9,f-r5-01-5');
				$data = rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32');

                unset($list_field);
				$list_field = explode(',', 'f-r6-01-3,f-r5-01-3,f-r6-01-4,f-r5-01-4,f-r6-01-5,f-r5-01-3,f-r6-01-6,f-r5-01-4,f-r6-01-7,f-r6-01-8,f-r6-01-9,f-r5-01-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-02-3,f-r5-02-3,f-r6-02-4,f-r5-02-4,f-r6-02-5,f-r5-02-3,f-r6-02-6,f-r5-02-4,f-r6-02-7,f-r6-02-8,f-r6-02-9,f-r5-02-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-03-3,f-r5-03-3,f-r6-03-4,f-r5-03-4,f-r6-03-5,f-r5-03-3,f-r6-03-6,f-r5-03-4,f-r6-03-7,f-r6-03-8,f-r6-03-9,f-r5-03-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-04-3,f-r5-04-3,f-r6-04-4,f-r5-04-4,f-r6-04-5,f-r5-04-3,f-r6-04-6,f-r5-04-4,f-r6-04-7,f-r6-04-8,f-r6-04-9,f-r5-04-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-05-3,f-r5-05-3,f-r6-05-4,f-r5-05-4,f-r6-05-5,f-r5-05-3,f-r6-05-6,f-r5-05-4,f-r6-05-7,f-r6-05-8,f-r6-05-9,f-r5-05-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-06-3,f-r5-06-3,f-r6-06-4,f-r5-06-4,f-r6-06-5,f-r5-06-3,f-r6-06-6,f-r5-06-4,f-r6-06-7,f-r6-06-8,f-r6-06-9,f-r5-06-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-07-3,f-r5-07-3,f-r6-07-4,f-r5-07-4,f-r6-07-5,f-r5-07-3,f-r6-07-6,f-r5-07-4,f-r6-07-7,f-r6-07-8,f-r6-07-9,f-r5-07-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-08-3,f-r5-08-3,f-r6-08-4,f-r5-08-4,f-r6-08-5,f-r5-08-3,f-r6-08-6,f-r5-08-4,f-r6-08-7,f-r6-08-8,f-r6-08-9,f-r5-08-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 0, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-09-3,f-r5-09-3,f-r6-09-4,f-r5-09-4,f-r6-09-5,f-r5-09-3,f-r6-09-6,f-r5-09-4,f-r6-09-7,f-r6-09-8,f-r6-09-9,f-r5-09-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-10-3,f-r5-10-3,f-r6-10-4,f-r5-10-4,f-r6-10-5,f-r5-10-3,f-r6-10-6,f-r5-10-4,f-r6-10-7,f-r6-10-8,f-r6-10-9,f-r5-10-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-11-3,f-r5-11-3,f-r6-11-4,f-r5-11-4,f-r6-11-5,f-r5-11-3,f-r6-11-6,f-r5-11-4,f-r6-11-7,f-r6-11-8,f-r6-11-9,f-r5-11-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-12-3,f-r5-12-3,f-r6-12-4,f-r5-12-4,f-r6-12-5,f-r5-12-3,f-r6-12-6,f-r5-12-4,f-r6-12-7,f-r6-12-8,f-r6-12-9,f-r5-12-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-13-3,f-r5-13-3,f-r6-13-4,f-r5-13-4,f-r6-13-5,f-r5-13-3,f-r6-13-6,f-r5-13-4,f-r6-13-7,f-r6-13-8,f-r6-13-9,f-r5-13-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r6-14-3,f-r5-14-3,f-r6-14-4,f-r5-14-4,f-r6-14-5,f-r5-14-3,f-r6-14-6,f-r5-14-4,f-r6-14-7,f-r6-14-8,f-r6-14-9,f-r5-14-5');
				$data = array_merge($data, rik76rzd6($rzd_id5, $rid, $list_field, 1, $i, '11,12,13,14,15,17,18,27,28,32'));
				}
			break;

			case 7:
				if($i<2) {
                unset($data);
                $data['f-r7-01'] = 0;
				}
			break;

			case 8:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id10 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=10");
                unset($data);
				$list_field = explode(',', 'f-r8-01-3,f-r10-01,f-r8-02-3,f-r10-03,f-r8-03-3,f-r10-05,f-r8-04-3,f-r10-02,f-r8-05-3,f-r10-04,f-r8-06-3,f-r10-06');
				$data = rik76rzd8($rzd_id10, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');
				}
			break;

			case 9:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id11 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=11");
                unset($data);
				$list_field = explode(',', 'f-r9-01,f-r11-01,f-r9-02,f-r11-02');
				$data = rik76rzd9($rzd_id11, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');
				}
			break;

			case 10:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id12 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=12");
                unset($data);
				$list_field = explode(',', 'f-r10-01-3,f-r12-01,f-r10-02-3,f-r12-02');
				$data = rik76rzd10($rzd_id12, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');
				}
			break;

			case 11:
				if($i<2) {
				$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
				$rzd_id14 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=14");
                unset($data);
                unset($list_field);
				$list_field = explode(',', 'f-r11-01-3,f-r14-01-3,f-r11-01-4,f-r14-01-4,f-r11-01-5,f-r11-01-6,f-r11-01-7,f-r11-01-8,f-r11-02-3,f-r14-01-5,f-r11-02-4,f-r14-01-6,f-r11-02-5,f-r11-02-6,f-r11-02-7,f-r11-02-8');
				$data = rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32');

                unset($list_field);
				$list_field = explode(',', 'f-r11-03-3,f-r14-02-3,f-r11-03-4,f-r14-02-4,f-r11-03-5,f-r11-03-6,f-r11-03-7,f-r11-03-8,f-r11-04-3,f-r14-02-5,f-r11-04-4,f-r14-02-6,f-r11-04-5,f-r11-04-6,f-r11-04-7,f-r11-04-8');
				$data = array_merge($data, rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r11-05-3,f-r14-03-3,f-r11-05-4,f-r14-03-4,f-r11-05-5,f-r11-05-6,f-r11-05-7,f-r11-05-8,f-r11-06-3,f-r14-03-5,f-r11-06-4,f-r14-03-6,f-r11-06-5,f-r11-06-6,f-r11-06-7,f-r11-06-8');
				$data = array_merge($data, rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r11-07-3,f-r14-04-3,f-r11-07-4,f-r14-04-4,f-r11-07-5,f-r11-07-6,f-r11-07-7,f-r11-07-8,f-r11-08-3,f-r14-04-5,f-r11-08-4,f-r14-04-6,f-r11-08-5,f-r11-08-6,f-r11-08-7,f-r11-08-8');
				$data = array_merge($data, rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

				unset($list_field);
				$list_field = explode(',', 'f-r11-09-3,f-r14-05-3,f-r11-09-4,f-r14-05-4,f-r11-09-5,f-r11-09-6,f-r11-09-7,f-r11-09-8,f-r11-10-3,f-r14-05-5,f-r11-10-4,f-r14-05-6,f-r11-10-5,f-r11-10-6,f-r11-10-7,f-r11-10-8');
				$data = array_merge($data, rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));

                unset($list_field);
				$list_field = explode(',', 'f-r11-11-3,f-r14-06-3,f-r11-11-4,f-r14-06-4,f-r11-11-5,f-r11-11-6,f-r11-11-7,f-r11-11-8,f-r11-12-3,f-r14-06-5,f-r11-12-4,f-r14-06-6,f-r11-12-5,f-r11-12-6,f-r11-12-7,f-r11-12-8');
				$data = array_merge($data, rik76rzd11($rzd_id14, $rid, $list_field, $i, '11,12,13,14,15,17,18,27,28,32'));
				}
			break;

		}
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d4_02_53($sqls, $field)
{	global $yid, $CFG, $datemodified, $nm;

	$result[0] = 0;
	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id8 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
	$rzd_id13 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=13");

	unset($listarray);
	foreach ($sqls as $sql)  {
		$listarray[] = $sql->id;
	}
	$listform = implode(',', $listarray);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

	if($sqls) {
		unset($lists);
	    foreach ($sqls as $sql)  {
	        $lists[] = $sql->id;
	    }
	    $listschool = implode(',', $lists);

		$sqls = get_record_sql("select sum(`f-r13-01`) as fr1301, sum(`f-r13-02`) as fr1302, sum(`f-r13-03`) as fr1303, sum(`f-r13-06`) as fr1306, sum(`f-r13-07`) as fr1307, sum(`f-r13-11`) as fr1311, sum(`f-r13-13`) as fr1313, sum(`f-r13-14`) as fr1314, sum(`f-r13-15`) as fr1315, sum(`f-r13-16`) as fr1316, sum(`f-r13-17`) as fr1317, sum(`f-r13-24`) as fr1324, sum(`f-r13-25`) as fr1325, sum(`f-r13-26`) as fr1326, sum(`f-r13-27`) as fr1327, sum(`f-r13-28`) as fr1328, sum(`f-r13-29`) as fr1329, sum(`f-r13-30`) as fr1330, sum(`f-r13-31`) as fr1331, sum(`f-r13-37`) as fr1337, sum(`f-r13-38`) as fr1338 from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool))");

		$result[$field[0]] = $sqls->fr1301;
		$result[$field[1]] = $sqls->fr1302;
		$result[$field[2]] = $sqls->fr1303;
		$result[$field[3]] = $sqls->fr1306;
		$result[$field[4]] = $sqls->fr1307;
		$result[$field[5]] = $sqls->fr1311;
		$result[$field[6]] = $sqls->fr1317;
		$result[$field[7]] = $sqls->fr1315;
		$result[$field[8]] = $sqls->fr1316;
		$result[$field[9]] = $sqls->fr1313;
		$result[$field[10]] = $sqls->fr1314;
		$result[$field[11]] = $sqls->fr1324;
		$result[$field[12]] = $sqls->fr1325;
		$result[$field[13]] = $sqls->fr1326;
		$result[$field[14]] = $sqls->fr1327;
		$result[$field[15]] = $sqls->fr1328;
		$result[$field[16]] = $sqls->fr1329;
		$result[$field[17]] = $sqls->fr1330;
		$result[$field[18]] = $sqls->fr1331;
		$result[$field[19]] = $sqls->fr1337;
		$result[$field[20]] = $sqls->fr1338;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-04`=0");
		$result[$field[21]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-06`<>0");
		$result[$field[22]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-07`<>0");
		$result[$field[23]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-08`<>0");
		$result[$field[24]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-09`='Да'");
		$result[$field[25]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-10`='Да'");
		$result[$field[26]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-11`<>0");
		$result[$field[27]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-17`<>0");
		$result[$field[28]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-12`='Да'");
		$result[$field[29]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-16`<>0");
		$result[$field[30]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id8->id where (listformid in ($listschool)) and `f-r8-01`<>0");
		$result[$field[31]] = $sqls->c;

		$sqls = get_record_sql("select sum(`f-r8-01`) as fr801, sum(`f-r8-02`) as fr802 from {$CFG->prefix}monit_bkp_table_$rzd_id8->id where (listformid in ($listschool))");
		$result[$field[32]] = $sqls->fr801;
		$result[$field[33]] = $sqls->fr802;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-18`='Да'");
		$result[$field[34]] = $sqls->c;

		$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-18`='Да'");
		$result[$field[35]] = 0;
		if($sqls) {
			unset($listarray);
			foreach ($sqls as $sql)  {
				$listarray[] = $sql->listformid;
			}
			$listform = implode(',', $listarray);
			$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->schoolid;
				}
				$listschool1 = implode(',', $listarray);

				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzd_id4->id");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);
					$sqls = get_record_sql("select sum(`f-r4-18-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id4->id where (listformid in ($listform))");
					$result[$field[35]] = $sqls->c;
				}
			}
		}

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-19`='Да'");
		$result[$field[36]] = $sqls->c;

		$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-19`='Да'");
		$result[$field[37]] = 0;

		if($sqls) {
			unset($listarray);
			foreach ($sqls as $sql)  {
				$listarray[] = $sql->listformid;
			}
			$listform = implode(',', $listarray);
			$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->schoolid;
				}
				$listschool1 = implode(',', $listarray);

				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzd_id4->id");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);
					$sqls = get_record_sql("select sum(`f-r4-18-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id4->id where (listformid in ($listform))");
					$result[$field[37]] = $sqls->c;
				}
			}
		}
		$result[$field[37]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-20`='Да'");
		$result[$field[38]] = $sqls->c;

		$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-20`='Да'");
		$result[$field[39]] = 0;
		if($sqls) {
			unset($listarray);
			foreach ($sqls as $sql)  {
				$listarray[] = $sql->listformid;
			}
			$listform = implode(',', $listarray);
			$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->schoolid;
				}
				$listschool1 = implode(',', $listarray);

				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzd_id4->id");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);
					$sqls = get_record_sql("select sum(`f-r4-18-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id4->id where (listformid in ($listform))");
					$result[$field[39]] = $sqls->c;
				}
			}
		}

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-21`='Да'");
		$result[$field[40]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-22`='Да'");
		$result[$field[41]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-23`='Да'");
		$result[$field[42]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-24`<>0");
		$result[$field[43]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-26`<>0");
		$result[$field[44]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-32`='Да'");
		$result[$field[45]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-33`='Да'");
		$result[$field[46]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-34`='Да'");
		$result[$field[47]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-35`='Да'");
		$result[$field[48]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-36`='Да'");
		$result[$field[49]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-36`='Нет'");
		$result[$field[50]] = $sqls->c;

		$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id13->id where (listformid in ($listschool)) and `f-r13-39`='Да'");
		$result[$field[51]] = $sqls->c;
	}
	return $result;
}

function d4sum($field, $data)
{
	if(!isset($data[$field[1]])) {		$data[$field[1]] = 0;	}
	if(!isset($data[$field[2]])) {
		$data[$field[2]] = 0;
	}
	if(!isset($data[$field[3]])) {
		$data[$field[3]] = 0;
	}
	if(!isset($data[$field[5]])) {
		$data[$field[5]] = 0;
	}
	if(!isset($data[$field[6]])) {
		$data[$field[6]] = 0;
	}
	if(!isset($data[$field[7]])) {
		$data[$field[7]] = 0;
	}
	$result[$field[0]] = $data[$field[1]] + $data[$field[2]] + $data[$field[3]];
	$result[$field[4]] = $data[$field[5]] + $data[$field[6]] + $data[$field[7]];
	$result[$field[8]] = $result[$field[0]] + $result[$field[4]];
	$result[$field[9]] = $data[$field[1]] + $data[$field[5]];
	$result[$field[10]] = $data[$field[2]] + $data[$field[6]];
	$result[$field[11]] = $data[$field[3]] + $data[$field[7]];

	return $result;
}

function d4($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$category = '11,12,13,14,15,17,18,27,28,32';
	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id8 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
	$rzd_id13 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=13");

	$data['f-r1-01-3'] = 0;
	$data['f-r1-01-4'] = 0;
	$data['f-r1-01-5'] = 0;
	$data['f-r1-01-6'] = 0;
	$data['f-r1-01-7'] = 0;
	$data['f-r1-01-8'] = 0;
	$data['f-r1-01-9'] = 0;
	$data['f-r1-01-10'] = 0;


	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution not in ($category))and(stateinstitution in (5))");
	if($sqls) {
		$data['f-r1-01-4'] = count($sqls);
		$field = explode(',', 'f-r1-02-4,f-r1-03-4,f-r1-04-4,f-r1-07-4,f-r1-09-4,f-r1-14-4,f-r1-16-4,f-r1-19-4,f-r1-20-4,f-r1-21-4,f-r1-22-4,f-r1-36-4,f-r1-37-4,f-r1-39-4,f-r1-40-4,f-r1-41-4,f-r1-42-4,f-r1-43-4,f-r1-44-4,f-r1-51-4,f-r1-52-4,f-r1-05-4,f-r1-06-4,f-r1-08-4,f-r1-10-4,f-r1-11-4,f-r1-12-4,f-r1-13-4,f-r1-15-4,f-r1-17-4,f-r1-18-4,f-r1-23-4,f-r1-24-4,f-r1-25-4,f-r1-26-4,f-r1-27-4,f-r1-28-4,f-r1-29-4,f-r1-30-4,f-r1-31-4,f-r1-32-4,f-r1-33-4,f-r1-34-4,f-r1-35-4,f-r1-38-4,f-r1-45-4,f-r1-46-4,f-r1-47-4,f-r1-48-4,f-r1-49-4,f-r1-50-4,f-r1-53-4');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution not in ($category))and(stateinstitution in (6))");
	if($sqls) {
		$data['f-r1-01-5'] = count($sqls);
		$field = explode(',', 'f-r1-02-5,f-r1-03-5,f-r1-04-5,f-r1-07-5,f-r1-09-5,f-r1-14-5,f-r1-16-5,f-r1-19-5,f-r1-20-5,f-r1-21-5,f-r1-22-5,f-r1-36-5,f-r1-37-5,f-r1-39-5,f-r1-40-5,f-r1-41-5,f-r1-42-5,f-r1-43-5,f-r1-44-5,f-r1-51-5,f-r1-52-5,f-r1-05-5,f-r1-06-5,f-r1-08-5,f-r1-10-5,f-r1-11-5,f-r1-12-5,f-r1-13-5,f-r1-15-5,f-r1-17-5,f-r1-18-5,f-r1-23-5,f-r1-24-5,f-r1-25-5,f-r1-26-5,f-r1-27-5,f-r1-28-5,f-r1-29-5,f-r1-30-5,f-r1-31-5,f-r1-32-5,f-r1-33-5,f-r1-34-5,f-r1-35-5,f-r1-38-5,f-r1-45-5,f-r1-46-5,f-r1-47-5,f-r1-48-5,f-r1-49-5,f-r1-50-5,f-r1-53-5');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution not in ($category))and(stateinstitution in (1,2))");
	if($sqls) {
		$data['f-r1-01-6'] = count($sqls);
		$field = explode(',', 'f-r1-02-6,f-r1-03-6,f-r1-04-6,f-r1-07-6,f-r1-09-6,f-r1-14-6,f-r1-16-6,f-r1-19-6,f-r1-20-6,f-r1-21-6,f-r1-22-6,f-r1-36-6,f-r1-37-6,f-r1-39-6,f-r1-40-6,f-r1-41-6,f-r1-42-6,f-r1-43-6,f-r1-44-6,f-r1-51-6,f-r1-52-6,f-r1-05-6,f-r1-06-6,f-r1-08-6,f-r1-10-6,f-r1-11-6,f-r1-12-6,f-r1-13-6,f-r1-15-6,f-r1-17-6,f-r1-18-6,f-r1-23-6,f-r1-24-6,f-r1-25-6,f-r1-26-6,f-r1-27-6,f-r1-28-6,f-r1-29-6,f-r1-30-6,f-r1-31-6,f-r1-32-6,f-r1-33-6,f-r1-34-6,f-r1-35-6,f-r1-38-6,f-r1-45-6,f-r1-46-6,f-r1-47-6,f-r1-48-6,f-r1-49-6,f-r1-50-6,f-r1-53-6');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution not in ($category))and(stateinstitution in (5))");
	if($sqls) {
		$data['f-r1-01-8'] = count($sqls);
		$field = explode(',', 'f-r1-02-8,f-r1-03-8,f-r1-04-8,f-r1-07-8,f-r1-09-8,f-r1-14-8,f-r1-16-8,f-r1-19-8,f-r1-20-8,f-r1-21-8,f-r1-22-8,f-r1-36-8,f-r1-37-8,f-r1-39-8,f-r1-40-8,f-r1-41-8,f-r1-42-8,f-r1-43-8,f-r1-44-8,f-r1-51-8,f-r1-52-8,f-r1-05-8,f-r1-06-8,f-r1-08-8,f-r1-10-8,f-r1-11-8,f-r1-12-8,f-r1-13-8,f-r1-15-8,f-r1-17-8,f-r1-18-8,f-r1-23-8,f-r1-24-8,f-r1-25-8,f-r1-26-8,f-r1-27-8,f-r1-28-8,f-r1-29-8,f-r1-30-8,f-r1-31-8,f-r1-32-8,f-r1-33-8,f-r1-34-8,f-r1-35-8,f-r1-38-8,f-r1-45-8,f-r1-46-8,f-r1-47-8,f-r1-48-8,f-r1-49-8,f-r1-50-8,f-r1-53-8');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution not in ($category))and(stateinstitution in (6))");
	if($sqls) {
		$data['f-r1-01-9'] = count($sqls);
		$field = explode(',', 'f-r1-02-9,f-r1-03-9,f-r1-04-9,f-r1-07-9,f-r1-09-9,f-r1-14-9,f-r1-16-9,f-r1-19-9,f-r1-20-9,f-r1-21-9,f-r1-22-9,f-r1-36-9,f-r1-37-9,f-r1-39-9,f-r1-40-9,f-r1-41-9,f-r1-42-9,f-r1-43-9,f-r1-44-9,f-r1-51-9,f-r1-52-9,f-r1-05-9,f-r1-06-9,f-r1-08-9,f-r1-10-9,f-r1-11-9,f-r1-12-9,f-r1-13-9,f-r1-15-9,f-r1-17-9,f-r1-18-9,f-r1-23-9,f-r1-24-9,f-r1-25-9,f-r1-26-9,f-r1-27-9,f-r1-28-9,f-r1-29-9,f-r1-30-9,f-r1-31-9,f-r1-32-9,f-r1-33-9,f-r1-34-9,f-r1-35-9,f-r1-38-9,f-r1-45-9,f-r1-46-9,f-r1-47-9,f-r1-48-9,f-r1-49-9,f-r1-50-9,f-r1-53-9');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	$sqls = get_records_sql("select id, name stateinstitution from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution not in ($category))and(stateinstitution in (1,2))");
	if($sqls) {
		$data['f-r1-01-10'] = count($sqls);
		$field = explode(',', 'f-r1-02-10,f-r1-03-10,f-r1-04-10,f-r1-07-10,f-r1-09-10,f-r1-14-10,f-r1-16-10,f-r1-19-10,f-r1-20-10,f-r1-21-10,f-r1-22-10,f-r1-36-10,f-r1-37-10,f-r1-39-10,f-r1-40-10,f-r1-41-10,f-r1-42-10,f-r1-43-10,f-r1-44-10,f-r1-51-10,f-r1-52-10,f-r1-05-10,f-r1-06-10,f-r1-08-10,f-r1-10-10,f-r1-11-10,f-r1-12-10,f-r1-13-10,f-r1-15-10,f-r1-17-10,f-r1-18-10,f-r1-23-10,f-r1-24-10,f-r1-25-10,f-r1-26-10,f-r1-27-10,f-r1-28-10,f-r1-29-10,f-r1-30-10,f-r1-31-10,f-r1-32-10,f-r1-33-10,f-r1-34-10,f-r1-35-10,f-r1-38-10,f-r1-45-10,f-r1-46-10,f-r1-47-10,f-r1-48-10,f-r1-49-10,f-r1-50-10,f-r1-53-10');
		$data = array_merge($data, d4_02_53($sqls, $field));
	}

	for ($i=1;$i<54;$i++) {		if($i<10) { $s='0'.$i; } else { $s=$i; }
		$field = explode(',', "f-r1-$s-3,f-r1-$s-4,f-r1-$s-5,f-r1-$s-6,f-r1-$s-7,f-r1-$s-8,f-r1-$s-9,f-r1-$s-10,f-r1-$s-11,f-r1-$s-12,f-r1-$s-13,f-r1-$s-14");
		$data = array_merge($data, d4sum($field, $data));
	}

	foreach ($table_rzds as $table_rzd)  {		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d7($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id1 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=1");

	foreach ($table_rzds as $table_rzd)  {		if($table_rzd->id == 240) {   			$type = -1;		}  else  {   			$type = 1;
		}	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where isclosing=0 AND yearid=$yid AND rayonid=$rid and iscountryside=$type");
	$c = count($sqls);

    if($sqls) {    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}
		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);

		    $sqls = get_record_sql("select sum(`f-r1-01-4c`)as fr1014c, sum(`f-r1-01-4z`)as fr1014z, sum(`f-r1-01-5c`)as fr1015c, sum(`f-r1-01-5z`)as fr1015z, sum(`f-r1-01-6c`)as fr1016c, sum(`f-r1-01-6z`)as fr1016z, sum(`f-r1-02-4c`)as fr1024c, sum(`f-r1-02-4z`)as fr1024z, sum(`f-r1-02-5c`)as fr1025c, sum(`f-r1-02-5z`)as fr1025z, sum(`f-r1-02-6c`)as fr1026c, sum(`f-r1-02-6z`)as fr1026z, sum(`f-r1-03-4`)as fr1034, sum(`f-r1-03-5`)as fr1035, sum(`f-r1-03-6`)as fr1036, sum(`f-r1-03-7`)as fr1037, sum(`f-r1-03-8`)as fr1038, sum(`f-r1-03-9`)as fr1039, sum(`f-r1-01-7`)as fr1017, sum(`f-r1-01-8`)as fr1018, sum(`f-r1-01-9`)as fr1019, sum(`f-r1-02-7`)as fr1027, sum(`f-r1-02-8`)as fr1028, sum(`f-r1-02-9`)as fr1029, sum(`f-r1-03-7`)as fr1037, sum(`f-r1-03-8`)as fr1038, sum(`f-r1-03-9`)as fr1039 from {$CFG->prefix}monit_bkp_table_$rzd_id1->id where (listformid in ($listschool))");

			$data['f-r1-52-3'] = $c;
			$data['f-r1-52-4c'] = $sqls->fr1014c + $sqls->fr1015c + $sqls->fr1016c + $sqls->fr1017;
			$data['f-r1-52-4z'] = $sqls->fr1014z + $sqls->fr1015z + $sqls->fr1016z;

			$data['f-r1-52-5'] = $sqls->fr1018;
			$data['f-r1-52-6'] = $sqls->fr1019;
			$data['f-r1-52-7'] = $data['f-r1-52-4c'] + $data['f-r1-52-4z'] + $data['f-r1-52-5'] + $data['f-r1-52-6'];

			$data['f-r1-01-4c'] = $data['f-r1-52-4c'];
			$data['f-r1-01-4z'] = $data['f-r1-52-4z'];
			$data['f-r1-01-5'] = $data['f-r1-52-5'];
			$data['f-r1-01-6'] = $data['f-r1-52-6'];
			$data['f-r1-01-7'] = $data['f-r1-52-7'];
			$data['f-r1-92-3'] = $data['f-r1-52-3'];
		    $sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id1->id where (listformid in ($listschool)) and `f-r1-05-10`<>0");
			$data['f-r1-95-3'] = $sqls->c;
		}
	}

	foreach ($table_rzds as $table_rzd)  {
		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d8_calc($field, $listschool, $rzd_id4, $rzd_id16)
{	global $CFG;
//print_r($field);

	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-04-4`<>0)");
	$result[$field[0]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-04-6`<>0)");
	$result[$field[1]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-04-8`<>0)");
	$result[$field[2]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-04-10`<>0)");
	$result[$field[3]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r16-04-3`) as fr16043, sum(`f-r16-04-5`) as fr16045, sum(`f-r16-04-7`) as fr16047, sum(`f-r16-04-9`) as fr16049, sum(`f-r16-01-3`) as fr16013, sum(`f-r16-01-5`) as fr16015, sum(`f-r16-01-7`) as fr16017, sum(`f-r16-01-9`) as fr16019, sum(`f-r16-01-4`) as fr16014, sum(`f-r16-01-6`) as fr16016, sum(`f-r16-01-8`) as fr16018, sum(`f-r16-01-10`) as fr160110, sum(`f-r16-02-3`) as fr16023, sum(`f-r16-02-5`) as fr16025, sum(`f-r16-02-7`) as fr16027, sum(`f-r16-02-9`) as fr16029, sum(`f-r16-02-4`) as fr16024, sum(`f-r16-02-6`) as fr16026, sum(`f-r16-02-8`) as fr16028, sum(`f-r16-02-10`) as fr160210, sum(`f-r16-03-3`) as fr16033, sum(`f-r16-03-5`) as fr16035, sum(`f-r16-03-7`) as fr16037, sum(`f-r16-03-9`) as fr16039, sum(`f-r16-03-4`) as fr16034, sum(`f-r16-03-6`) as fr16036, sum(`f-r16-03-8`) as fr16038, sum(`f-r16-03-10`) as fr160310, sum(`f-r16-05-3`) as fr16053, sum(`f-r16-05-4`) as fr16054, sum(`f-r16-05-5`) as fr16055, sum(`f-r16-05-6`) as fr16056, sum(`f-r16-05-7`) as fr16057, sum(`f-r16-05-8`) as fr16058, sum(`f-r16-05-9`) as fr16059, sum(`f-r16-05-10`) as fr160510 from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))");

	$result[$field[4]] = $sqls->fr16043;
	$result[$field[5]] = $sqls->fr16045;
	$result[$field[6]] = $sqls->fr16047;
	$result[$field[7]] = $sqls->fr16049;
	$result[$field[8]] = $sqls->fr16013;
	$result[$field[9]] = $sqls->fr16015;
	$result[$field[10]] = $sqls->fr16017;
	$result[$field[11]] = $sqls->fr16019;
	$result[$field[12]] = $sqls->fr16014;
	$result[$field[13]] = $sqls->fr16016;
	$result[$field[14]] = $sqls->fr16018;
	$result[$field[15]] = $sqls->fr160110;
	$result[$field[16]] = $sqls->fr16023;
	$result[$field[17]] = $sqls->fr16025;
	$result[$field[18]] = $sqls->fr16027;
	$result[$field[19]] = $sqls->fr16029;
	$result[$field[20]] = $sqls->fr16024;
	$result[$field[21]] = $sqls->fr16026;
	$result[$field[22]] = $sqls->fr16028;
	$result[$field[23]] = $sqls->fr160210;
	$result[$field[24]] = $sqls->fr16033;
	$result[$field[25]] = $sqls->fr16035;
	$result[$field[26]] = $sqls->fr16037;
	$result[$field[27]] = $sqls->fr16039;
	$result[$field[28]] = $sqls->fr16034;
	$result[$field[29]] = $sqls->fr16036;
	$result[$field[30]] = $sqls->fr16038;
	$result[$field[31]] = $sqls->fr160310;
	$result[$field[32]] = $sqls->fr16053;
	$result[$field[33]] = $sqls->fr16054;
	$result[$field[34]] = $sqls->fr16055;
	$result[$field[35]] = $sqls->fr16056;
	$result[$field[36]] = $sqls->fr16057;
	$result[$field[37]] = $sqls->fr16058;
	$result[$field[38]] = $sqls->fr16059;
	$result[$field[39]] = $sqls->fr160510;

	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-04-4`=0)and(`f-r16-04-6`=0)and(`f-r16-04-8`=0)and(`f-r16-04-10`=0)");
	$result[$field[40]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r4-10-4`) as fr4104, sum(`f-r4-11-4`) as fr4114, sum(`f-r4-12-4`) as fr4124, sum(`f-r4-13-4`) as fr4134, sum(`f-r4-14-4`) as fr4144, sum(`f-r4-15-4`) as fr4154, sum(`f-r4-16-4`) as fr4164, sum(`f-r4-17-4`) as fr4174 from {$CFG->prefix}monit_bkp_table_$rzd_id4->id where (listformid in ($listschool))");
	$result[$field[41]] = $sqls->fr4104 + $sqls->fr4114 + $sqls->fr4124 + $sqls->fr4134 + $sqls->fr4144 + $sqls->fr4154 + $sqls->fr4164 + $sqls->fr4174;

	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-05-4`<>0)");
	$result[$field[42]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-05-6`<>0)");
	$result[$field[43]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-05-8`<>0)");
	$result[$field[44]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id16->id where (listformid in ($listschool))and(`f-r16-05-10`<>0)");
	$result[$field[45]] = $sqls->c;

	$result[$field[58]] = $result[$field[12]] + $result[$field[20]] + $result[$field[28]];
	$result[$field[59]] = $result[$field[13]] + $result[$field[21]] + $result[$field[29]];
	$result[$field[60]] = $result[$field[14]] + $result[$field[22]] + $result[$field[30]];
	$result[$field[61]] = $result[$field[15]] + $result[$field[23]] + $result[$field[31]];

	$result[$field[46]] = $result[$field[0]] + $result[$field[1]] + $result[$field[2]] + $result[$field[3]];
	$result[$field[47]] = $result[$field[4]] + $result[$field[5]] + $result[$field[6]] + $result[$field[7]];
	$result[$field[48]] = $result[$field[58]] + $result[$field[59]] + $result[$field[60]] + $result[$field[61]];
	$result[$field[49]] = $result[$field[8]] + 	$result[$field[9]] + $result[$field[10]] + $result[$field[11]];
	$result[$field[50]] = $result[$field[12]] + $result[$field[13]] + $result[$field[14]] + $result[$field[15]];
	$result[$field[51]] = $result[$field[16]] + $result[$field[17]] + $result[$field[18]] + $result[$field[19]];
	$result[$field[52]] = $result[$field[20]] + $result[$field[21]] + $result[$field[22]] + $result[$field[23]];
	$result[$field[53]] = $result[$field[24]] + $result[$field[25]] + $result[$field[26]] + $result[$field[27]];
	$result[$field[54]] = $result[$field[28]] + $result[$field[29]] + $result[$field[30]] + $result[$field[31]];
	$result[$field[55]] = $result[$field[42]] + $result[$field[43]] + $result[$field[44]] + $result[$field[45]];
	$result[$field[56]] = $result[$field[32]] + $result[$field[34]] + $result[$field[36]] + $result[$field[38]];
	$result[$field[57]] = $result[$field[33]] + $result[$field[35]] + $result[$field[37]] + $result[$field[39]];

	return $result;
}

function d8_calc1($field, $listschool, $rzd_id17, $id)
{	global $CFG;

	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-02-7`<>0)or(`f-r17-02-8`<>0)or(`f-r17-02-9`<>0))");
	$result[$field[2]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-03-7`<>0)or(`f-r17-03-8`<>0)or(`f-r17-03-9`<>0))");
	$result[$field[3]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-04-7`<>0)or(`f-r17-04-8`<>0)or(`f-r17-04-9`<>0))");
	$result[$field[4]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-05-7`<>0)or(`f-r17-05-8`<>0)or(`f-r17-05-9`<>0))");
	$result[$field[5]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-06-7`<>0)or(`f-r17-06-8`<>0)or(`f-r17-06-9`<>0))");
	$result[$field[6]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-07-7`<>0)or(`f-r17-07-8`<>0)or(`f-r17-07-9`<>0))");
	$result[$field[7]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-08-7`<>0)or(`f-r17-08-8`<>0)or(`f-r17-08-9`<>0))");
	$result[$field[8]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-09-7`<>0)or(`f-r17-09-8`<>0)or(`f-r17-09-9`<>0))");
	$result[$field[9]] = $sqls->c;
	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and((`f-r17-10-7`<>0)or(`f-r17-10-8`<>0)or(`f-r17-10-9`<>0))");
	$result[$field[10]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[13]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[14]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[15]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[16]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[17]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[18]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[19]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[20]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-3`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[21]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[24]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[25]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[26]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[27]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[28]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[29]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[30]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[31]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-4`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[32]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[35]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[36]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[37]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[38]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[39]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[40]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[41]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[42]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-5`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[43]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[57]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[58]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[59]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[60]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[61]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[62]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[63]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[64]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-7`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[65]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[68]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[69]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[70]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[71]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[72]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[73]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[74]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[75]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-8`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[76]] = $sqls->c;

	$sqls = get_record_sql("select sum(`f-r17-02-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[79]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-03-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[80]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-04-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[81]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-05-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[82]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-06-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[83]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-07-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[84]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-08-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[85]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-09-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[86]] = $sqls->c;
	$sqls = get_record_sql("select sum(`f-r17-10-9`) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))");
	$result[$field[87]] = $sqls->c;

//	$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzd_id17->id where (listformid in ($listschool))and(`f-r17-05-10`<>0)");
//	$result[$field[13]] = ;

	$result[$field[1]] = $result[$field[2]] + $result[$field[3]] + $result[$field[4]] + $result[$field[5]] + $result[$field[6]];
	$result[$field[12]] = $result[$field[13]] + $result[$field[14]] + $result[$field[15]] + $result[$field[16]] + $result[$field[17]];
	$result[$field[23]] = $result[$field[24]] + $result[$field[25]] + $result[$field[26]] + $result[$field[27]] + $result[$field[28]];
	$result[$field[34]] = $result[$field[35]] + $result[$field[36]] + $result[$field[37]] + $result[$field[38]] + $result[$field[39]];
//	$result[$field[45]] = $result[$field[46]] + $result[$field[47]] + $result[$field[48]] + $result[$field[49]] + $result[$field[50]];
	$result[$field[56]] = $result[$field[57]] + $result[$field[58]] + $result[$field[59]] + $result[$field[60]] + $result[$field[61]];
	$result[$field[67]] = $result[$field[68]] + $result[$field[69]] + $result[$field[70]] + $result[$field[71]] + $result[$field[72]];
	$result[$field[78]] = $result[$field[79]] + $result[$field[80]] + $result[$field[81]] + $result[$field[82]] + $result[$field[83]];
//	$result[$field[89]] = $result[$field[90]] + $result[$field[91]] + $result[$field[92]] + $result[$field[93]] + $result[$field[94]];

	$result[$field[0]] = $result[$field[1]] + $result[$field[7]] + $result[$field[8]] + $result[$field[9]] + $result[$field[10]];
	$result[$field[11]] = $result[$field[12]] + $result[$field[18]] + $result[$field[19]] + $result[$field[20]] + $result[$field[21]];
	$result[$field[22]] = $result[$field[23]] + $result[$field[29]] + $result[$field[30]] + $result[$field[31]] + $result[$field[32]];
	$result[$field[33]] = $result[$field[34]] + $result[$field[40]] + $result[$field[41]] + $result[$field[42]] + $result[$field[43]];
//	$result[$field[44]] = $result[$field[45]] + $result[$field[51]] + $result[$field[52]] + $result[$field[53]] + $result[$field[54]];
	$result[$field[55]] = $result[$field[56]] + $result[$field[62]] + $result[$field[63]] + $result[$field[64]] + $result[$field[65]];
	$result[$field[66]] = $result[$field[67]] + $result[$field[73]] + $result[$field[74]] + $result[$field[75]] + $result[$field[76]];
	$result[$field[77]] = $result[$field[78]] + $result[$field[84]] + $result[$field[85]] + $result[$field[86]] + $result[$field[87]];
//	$result[$field[88]] = $result[$field[89]] + $result[$field[95]] + $result[$field[96]] + $result[$field[97]] + $result[$field[98]];

	$result[$field[44]] = $result[$field[11]] + $result[$field[22]] + $result[$field[33]];
	$result[$field[45]] = $result[$field[12]] + $result[$field[23]] + $result[$field[34]];
	$result[$field[46]] = $result[$field[13]] + $result[$field[24]] + $result[$field[35]];
	$result[$field[47]] = $result[$field[14]] + $result[$field[25]] + $result[$field[36]];
	$result[$field[48]] = $result[$field[15]] + $result[$field[26]] + $result[$field[37]];
	$result[$field[49]] = $result[$field[16]] + $result[$field[27]] + $result[$field[38]];
	$result[$field[50]] = $result[$field[17]] + $result[$field[28]] + $result[$field[39]];
	$result[$field[51]] = $result[$field[18]] + $result[$field[29]] + $result[$field[40]];
	$result[$field[52]] = $result[$field[19]] + $result[$field[30]] + $result[$field[41]];
	$result[$field[53]] = $result[$field[20]] + $result[$field[31]] + $result[$field[42]];
	$result[$field[54]] = $result[$field[21]] + $result[$field[32]] + $result[$field[43]];

	$result[$field[88]] = $result[$field[55]] + $result[$field[66]] + $result[$field[77]];
	$result[$field[89]] = $result[$field[56]] + $result[$field[67]] + $result[$field[78]];
	$result[$field[90]] = $result[$field[57]] + $result[$field[68]] + $result[$field[79]];
	$result[$field[91]] = $result[$field[58]] + $result[$field[69]] + $result[$field[80]];
	$result[$field[92]] = $result[$field[59]] + $result[$field[70]] + $result[$field[81]];
	$result[$field[93]] = $result[$field[60]] + $result[$field[71]] + $result[$field[82]];
	$result[$field[94]] = $result[$field[61]] + $result[$field[72]] + $result[$field[83]];
	$result[$field[95]] = $result[$field[62]] + $result[$field[73]] + $result[$field[84]];
	$result[$field[96]] = $result[$field[63]] + $result[$field[74]] + $result[$field[85]];
	$result[$field[97]] = $result[$field[64]] + $result[$field[75]] + $result[$field[86]];
	$result[$field[98]] = $result[$field[65]] + $result[$field[76]] + $result[$field[87]];

	return $result;
}

function d8($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$category = '11,12,13,14,15,17,18,27,28,32';
	$data[]=0;
	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id16 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=16");
	$rzd_id17 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=17");

	foreach ($table_rzds as $table_rzd)  {
		switch ($table_rzd->shortname)	{
			case 1:
				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution not in ($category))and(stateinstitution not in (28,29,30,31,32))");
			    if($sqls) {
			    	unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

					if($sqls) {						unset($lists);
					    foreach ($sqls as $sql)  {
					        $lists[] = $sql->id;
					    }

					    $listschool = implode(',', $lists);
						$field =explode(',', 'f-r1-01-4,f-r1-01-5,f-r1-01-6,f-r1-01-7,f-r1-02-4,f-r1-02-5,f-r1-02-6,f-r1-02-7,f-r1-04-4,f-r1-04-5,f-r1-04-6,f-r1-04-7,f-r1-05-4,f-r1-05-5,f-r1-05-6,f-r1-05-7,f-r1-06-4,f-r1-06-5,f-r1-06-6,f-r1-06-7,f-r1-07-4,f-r1-07-5,f-r1-07-6,f-r1-07-7,f-r1-08-4,f-r1-08-5,f-r1-08-6,f-r1-08-7,f-r1-09-4,f-r1-09-5,f-r1-09-6,f-r1-09-7,f-r1-13-4,f-r1-14-4,f-r1-13-5,f-r1-14-5,f-r1-13-6,f-r1-14-6,f-r1-13-7,f-r1-14-7,f-r1-10-3,f-r1-11-3,f-r1-12-4,f-r1-12-5,f-r1-12-6,f-r1-12-7,f-r1-01-3,f-r1-02-3,f-r1-03-3,f-r1-04-3,f-r1-05-3,f-r1-06-3,f-r1-07-3,f-r1-08-3,f-r1-09-3,f-r1-12-3,f-r1-13-3,f-r1-14-3,f-r1-03-4,f-r1-03-5,f-r1-03-6,f-r1-03-7');
						$data = array_merge($data, d8_calc($field, $listschool, $rzd_id4, $rzd_id16));
					}
				}

				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution not in ($category))and(stateinstitution not in (28,29,30,31,32))");
			    if($sqls) {
			    	unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

					if($sqls) {
						unset($lists);
					    foreach ($sqls as $sql)  {
					        $lists[] = $sql->id;
					    }

					    $listschool = implode(',', $lists);
						$field =explode(',', 'f-r1-01-9,f-r1-01-10,f-r1-01-11,f-r1-01-12,f-r1-02-9,f-r1-02-10,f-r1-02-11,f-r1-02-12,f-r1-04-9,f-r1-04-10,f-r1-04-11,f-r1-04-12,f-r1-05-9,f-r1-05-10,f-r1-05-11,f-r1-05-12,f-r1-06-9,f-r1-06-10,f-r1-06-11,f-r1-06-12,f-r1-07-9,f-r1-07-10,f-r1-07-11,f-r1-07-12,f-r1-08-9,f-r1-08-10,f-r1-08-11,f-r1-08-12,f-r1-09-9,f-r1-09-10,f-r1-09-11,f-r1-09-12,f-r1-13-9,f-r1-14-9,f-r1-13-10,f-r1-14-10,f-r1-13-11,f-r1-14-11,f-r1-13-12,f-r1-14-12,f-r1-10-8,f-r1-11-8,f-r1-12-9,f-r1-12-10,f-r1-12-11,f-r1-12-12,f-r1-01-8,f-r1-02-8,f-r1-03-8,f-r1-04-8,f-r1-05-8,f-r1-06-8,f-r1-07-8,f-r1-08-8,f-r1-09-8,f-r1-12-8,f-r1-13-8,f-r1-14-8,f-r1-03-9,f-r1-03-10,f-r1-03-11,f-r1-03-12');
						$data = array_merge($data, d8_calc($field, $listschool, $rzd_id4, $rzd_id16));
					}
				}
			break;
			case 2:
				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution not in ($category))and(stateinstitution not in (28,29,30,31,32))");
			    if($sqls) {
			    	unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}
					$listform = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

					if($sqls) {
						unset($lists);
					    foreach ($sqls as $sql)  {
					        $lists[] = $sql->id;
					    }

					    $listschool = implode(',', $lists);
						$field =explode(',', 'f-r2-01-3,f-r2-02-3,f-r2-03-3,f-r2-04-3,f-r2-05-3,f-r2-06-3,f-r2-07-3,f-r2-08-3,f-r2-09-3,f-r2-10-3,f-r2-11-3,f-r2-01-4,f-r2-02-4,f-r2-03-4,f-r2-04-4,f-r2-05-4,f-r2-06-4,f-r2-07-4,f-r2-08-4,f-r2-09-4,f-r2-10-4,f-r2-11-4,f-r2-01-5,f-r2-02-5,f-r2-03-5,f-r2-04-5,f-r2-05-5,f-r2-06-5,f-r2-07-5,f-r2-08-5,f-r2-09-5,f-r2-10-5,f-r2-11-5,f-r2-01-6,f-r2-02-6,f-r2-03-6,f-r2-04-6,f-r2-05-6,f-r2-06-6,f-r2-07-6,f-r2-08-6,f-r2-09-6,f-r2-10-6,f-r2-11-6,f-r2-01-7,f-r2-02-7,f-r2-03-7,f-r2-04-7,f-r2-05-7,f-r2-06-7,f-r2-07-7,f-r2-08-7,f-r2-09-7,f-r2-10-7,f-r2-11-7,f-r2-01-8,f-r2-02-8,f-r2-03-8,f-r2-04-8,f-r2-05-8,f-r2-06-8,f-r2-07-8,f-r2-08-8,f-r2-09-8,f-r2-10-8,f-r2-11-8,f-r2-01-9,f-r2-02-9,f-r2-03-9,f-r2-04-9,f-r2-05-9,f-r2-06-9,f-r2-07-9,f-r2-08-9,f-r2-09-9,f-r2-10-9,f-r2-11-9,f-r2-01-10,f-r2-02-10,f-r2-03-10,f-r2-04-10,f-r2-05-10,f-r2-06-10,f-r2-07-10,f-r2-08-10,f-r2-09-10,f-r2-10-10,f-r2-11-10,f-r2-01-11,f-r2-02-11,f-r2-03-11,f-r2-04-11,f-r2-05-11,f-r2-06-11,f-r2-07-11,f-r2-08-11,f-r2-09-11,f-r2-10-11,f-r2-11-11');
						$data = array_merge(d8_calc1($field, $listschool, $rzd_id17, 2));
					}
				}
			break;
		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d13_1_1($rid, $id, $rzdid1, $field, $rzdid5) {	global $yid, $CFG, $datemodified;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");
    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);


		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r1-14`) as c, sum(`f-r1-15`) as cc from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))");
			$result[$field[0]] = $sqls->cc;
			$result[$field[1]] = $sqls->c;

			$sqls = get_record_sql("select sum(`f-r5-01-3`) as c, sum(`f-r5-02-3`) as cc, sum(`f-r5-03-3`) as ccc, sum(`f-r5-04-3`) as cccc, sum(`f-r5-05-3`) as ccccc, sum(`f-r5-01`) as cccccc from {$CFG->prefix}monit_bkp_table_$rzdid5 where (listformid in ($listschool))");
			$result[$field[2]] = $sqls->c + $sqls->cc + $sqls->ccc + $sqls->cccc + $sqls->ccccc + $sqls->cccccc;
		}
	}
	if(empty($result[$field[0]])) { $result[$field[0]] = '0'; }
	if(empty($result[$field[1]])) { $result[$field[1]] = '0'; }
	if(empty($result[$field[2]])) { $result[$field[2]] = '0'; }

	return $result;
}

function d13_1_2($rid, $id, $rzdid1, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");

    if($sqls) {		$result[$field[0]] = count($sqls);

    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r1-06`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->c;
		}
	}
	if(empty($result[$field[0]])) { $result[$field[0]] = '0'; }
	if(empty($result[$field[1]])) { $result[$field[1]] = '0'; }

	return $result;
}

function d13_1_3($rid, $id, $rzdid1, $field) {
	global $yid, $CFG, $datemodified;

	$result[$field[0]] = $result[$field[1]] = $result[$field[2]] = $result[$field[3]] = $result[$field[4]] = $result[$field[5]] = 0;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 0)and(`f-r1-14` < 3)");
			if($sqls) {
				$result[$field[0]] = count($sqls);
			}
			$sqls = get_record_sql("select sum(`f-r1-15`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 0)and(`f-r1-14` < 3)");
			$result[$field[5]] = $sqls->c;



			$sqls = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 2)and(`f-r1-14` < 5)");
			if($sqls) {
				$result[$field[1]] = count($sqls);
			}
			$sqls = get_record_sql("select sum(`f-r1-15`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 2)and(`f-r1-14` < 5)");
			$result[$field[5]] = $result[$field[5]] + $sqls->c;



			$sqls = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 4)and(`f-r1-14` < 7)");
			if($sqls) {
				$result[$field[2]] = count($sqls);
			}
			$sqls = get_record_sql("select sum(`f-r1-15`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 4)and(`f-r1-14` < 7)");
			$result[$field[5]] = $result[$field[5]] + $sqls->c;



			$sqls = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 6)and(`f-r1-14` < 9)");
			if($sqls) {
				$result[$field[3]] = count($sqls);
			}
			$sqls = get_record_sql("select sum(`f-r1-15`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 6)and(`f-r1-14` < 9)");
			$result[$field[5]] = $result[$field[5]] + $sqls->c;



			$sqls = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 9)");
			if($sqls) {
				$result[$field[4]] = count($sqls);
			}
			$sqls = get_record_sql("select sum(`f-r1-15`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))and(`f-r1-14` > 9)");
			$result[$field[5]] = $result[$field[5]] + $sqls->c;


		}
	}
	return $result;
}

function d13_2($rid, $id, $rzdid, $field) {
	global $yid, $CFG, $datemodified;

	$result[$field[0]] = $result[$field[1]] = $result[$field[2]] = $result[$field[3]] = $result[$field[4]] = $result[$field[5]] = $result[$field[6]] = $result[$field[7]] = $result[$field[8]] = $result[$field[9]] = $result[$field[10]] = $result[$field[11]] = $result[$field[12]] = $result[$field[13]] = 0;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where (datemodified=$datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r2-01`) as f01,sum(`f-r2-02`) as f02,sum(`f-r2-04`) as f04,sum(`f-r2-05`) as f05,sum(`f-r2-06`) as f06,sum(`f-r2-07`) as f07,sum(`f-r2-08`) as f08,sum(`f-r2-09`) as f09,sum(`f-r2-10`) as f10,sum(`f-r2-11`) as f11,sum(`f-r2-12`) as f12,sum(`f-r2-13`) as f13,sum(`f-r2-14`) as f14 from {$CFG->prefix}monit_bkp_table_$rzdid where (listformid in ($listschool))");

			$result[$field[0]] = $sqls->f01;
			$result[$field[1]] = $sqls->f02;
			$result[$field[3]] = $sqls->f04;
			$result[$field[4]] = $sqls->f05;
			$result[$field[5]] = $sqls->f06;
			$result[$field[6]] = $sqls->f07;
			$result[$field[7]] = $sqls->f08;
			$result[$field[8]] = $sqls->f09;
			$result[$field[9]] = $sqls->f10;
			$result[$field[10]] = $sqls->f11;
			$result[$field[11]] = $sqls->f12;
			$result[$field[12]] = $sqls->f13;
			$result[$field[13]] = $sqls->f14;
		}
	}

	$result[$field[2]] = $result[$field[3]] + $result[$field[4]] + $result[$field[5]] + $result[$field[6]] + $result[$field[7]] + $result[$field[8]] + $result[$field[9]] + $result[$field[10]] + $result[$field[11]] + $result[$field[12]] + $result[$field[13]];

	return $result;
}

function d13_3($rid, $id, $rzdid1, $field, $rzdid2) {
	global $yid, $CFG, $datemodified;

	$result[$field[0]] = $result[$field[1]] = $result[$field[2]] = $result[$field[3]] = $result[$field[4]] = $result[$field[5]] = $result[$field[6]] = $result[$field[7]] = $result[$field[8]] = $result[$field[9]] = $result[$field[10]] = $result[$field[11]] = $result[$field[12]] = $result[$field[13]] = 0;
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where (datemodified=$datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r1-07`) as f07,sum(`f-r1-01`) as f01,sum(`f-r1-02`) as f02,sum(`f-r1-03`) as f03,sum(`f-r1-04`) as f04,sum(`f-r1-05`) as f05,sum(`f-r1-08`) as f08,sum(`f-r1-09`) as f09,sum(`f-r1-10`) as f10,sum(`f-r1-11`) as f11,sum(`f-r1-12`) as f12,sum(`f-r1-13`) as f13 from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))");

			$result[$field[0]] = $sqls->f07;
			$result[$field[1]] = $sqls->f01;
			$result[$field[2]] = $sqls->f02;
			$result[$field[3]] = $sqls->f03;
			$result[$field[4]] = $sqls->f04;
			$result[$field[5]] = $sqls->f05;
			$result[$field[6]] = $sqls->f08;
			$result[$field[7]] = $sqls->f09;
			$result[$field[8]] = $sqls->f10;
			$result[$field[9]] = $sqls->f11;
			$result[$field[10]] = $sqls->f12;
			$result[$field[11]] = $sqls->f13;

			$sqls = get_record_sql("select sum(`f-r2-15`) as f15,sum(`f-r2-16`) as f16 from {$CFG->prefix}monit_bkp_table_$rzdid2 where (listformid in ($listschool))");
			$result[$field[12]] = $sqls->f15;
			$result[$field[13]] = $sqls->f16;
		}
	}

	$result[$field[2]] = $result[$field[3]] + $result[$field[4]] + $result[$field[5]] + $result[$field[6]] + $result[$field[7]] + $result[$field[8]] + $result[$field[9]] + $result[$field[10]] + $result[$field[11]] + $result[$field[12]] + $result[$field[13]];

	return $result;
}

function d13_4($rid, $id, $rzdid4, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	$result[$field[0]] = $result[$field[1]] = 0;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r4-01`) as f01, sum(`f-r4-02`) as f02 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");
			$result[$field[0]] = $sqls->f01;
			$result[$field[1]] = $sqls->f02;
		}
	}
	return $result;
}

function d13_5($rid, $id, $rzdid3, $field) {
	global $yid, $CFG, $datemodified;

	$result[$field[0]] = $result[$field[1]] = 0;

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }

		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))and(`f-r3-01` > 0)");
			$result[$field[0]] = $sqls->c;
			$sqls = get_record_sql("select sum(`f-r3-01`) as f01 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f01;
		}
	}
	return $result;
}

function d13_6($rid, $id, $rzdid5, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<35;$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r5-01-4`) as f6,sum(`f-r5-01-5`) as f7,sum(`f-r5-01-6`) as f8,sum(`f-r5-01-7`) as f9,sum(`f-r5-02-4`) as f11,sum(`f-r5-02-5`) as f12,sum(`f-r5-02-6`) as f13,sum(`f-r5-02-7`) as f14,sum(`f-r5-02`) as f16,sum(`f-r5-03`) as f17,sum(`f-r5-04`) as f18,sum(`f-r5-05`) as f19,sum(`f-r5-03-4`) as f21,sum(`f-r5-03-5`) as f22,sum(`f-r5-03-6`) as f23,sum(`f-r5-03-7`) as f24,sum(`f-r5-04-4`) as f26,sum(`f-r5-04-5`) as f27,sum(`f-r5-04-6`) as f28,sum(`f-r5-04-7`) as f29,sum(`f-r5-05-4`) as f31,sum(`f-r5-05-5`) as f32,sum(`f-r5-05-6`) as f33,sum(`f-r5-05-7`) as f34 from {$CFG->prefix}monit_bkp_table_$rzdid5 where (listformid in ($listschool))");

			$result[$field[6]] = $sqls->f6;
			$result[$field[7]] = $sqls->f7;
			$result[$field[8]] = $sqls->f8;
			$result[$field[9]] = $sqls->f9;
			$result[$field[11]] = $sqls->f11;
			$result[$field[12]] = $sqls->f12;
			$result[$field[13]] = $sqls->f13;
			$result[$field[14]] = $sqls->f14;
			$result[$field[16]] = $sqls->f16;
			$result[$field[17]] = $sqls->f17;
			$result[$field[18]] = $sqls->f18;
			$result[$field[19]] = $sqls->f19;
			$result[$field[21]] = $sqls->f21;
			$result[$field[22]] = $sqls->f22;
			$result[$field[23]] = $sqls->f23;
			$result[$field[24]] = $sqls->f24;
			$result[$field[26]] = $sqls->f26;
			$result[$field[27]] = $sqls->f27;
			$result[$field[28]] = $sqls->f28;
			$result[$field[29]] = $sqls->f29;
			$result[$field[31]] = $sqls->f31;
			$result[$field[32]] = $sqls->f32;
			$result[$field[33]] = $sqls->f33;
			$result[$field[34]] = $sqls->f34;
		}
	}

	$result[$field[1]] = $result[$field[6]] + $result[$field[11]] + $result[$field[16]] + $result[$field[21]] + $result[$field[26]] + $result[$field[31]];
	$result[$field[2]] = $result[$field[7]] + $result[$field[12]] + $result[$field[17]] + $result[$field[22]] + $result[$field[27]] + $result[$field[32]];
	$result[$field[3]] = $result[$field[8]] + $result[$field[13]] + $result[$field[18]] + $result[$field[23]] + $result[$field[28]] + $result[$field[33]];
	$result[$field[4]] = $result[$field[9]] + $result[$field[14]] + $result[$field[19]] + $result[$field[24]] + $result[$field[29]] + $result[$field[34]];
	$result[$field[0]] = $result[$field[1]] + $result[$field[2]] + $result[$field[3]] + $result[$field[4]];

	$result[$field[5]] = $result[$field[6]] + $result[$field[7]] + $result[$field[8]] + $result[$field[9]];
	$result[$field[10]] = $result[$field[11]] + $result[$field[12]] + $result[$field[13]] + $result[$field[14]];
	$result[$field[15]] = $result[$field[16]] + $result[$field[17]] + $result[$field[18]] + $result[$field[19]];
	$result[$field[20]] = $result[$field[21]] + $result[$field[22]] + $result[$field[23]] + $result[$field[24]];
	$result[$field[25]] = $result[$field[26]] + $result[$field[27]] + $result[$field[28]] + $result[$field[29]];
	$result[$field[30]] = $result[$field[31]] + $result[$field[32]] + $result[$field[33]] + $result[$field[34]];

	return $result;
}

function d13_7($rid, $id, $rzdid7, $field, $rzdid1) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<14;$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where (datemodified=$datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-01`='Да')and(listformid in ($listschool))");
		    $result[$field[0]] = $sqls->c;
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-02`='Да')and(listformid in ($listschool))");
		    $result[$field[1]] = $sqls->c;
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-03`>0)and(listformid in ($listschool))");
		    $result[$field[2]] = $sqls->c;
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-04`>0)and(listformid in ($listschool))");
		    $result[$field[3]] = $sqls->c;
		    $sqls = get_record_sql("select sum(`f-r7-06`) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool))");
		    $result[$field[4]] = $sqls->c;

		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-09`='Да')and(listformid in ($listschool))");
			$result[$field[5]] = $sqls->c;

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool)) and `f-r7-09`='Да'");
		    $result[$field[6]] = 0;
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid1");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r1-06`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listform))");
						$result[$field[6]] = $sqls->c;
					}
				}
			}

		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-07`='Да')and(listformid in ($listschool))");
			$result[$field[7]] = $sqls->c;

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool)) and `f-r7-07`='Да'");
			$result[$field[8]] = 0;
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid1");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r1-06`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listform))");
						$result[$field[8]] = $sqls->c;
					}
				}
			}

		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-08`='Да')and(listformid in ($listschool))");
		    $result[$field[9]] = $sqls->c;
			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool)) and `f-r7-08`='Да'");
		    $result[$field[10]] = 0;
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid1");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r1-06`) as c from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listform))");
						$result[$field[10]] = $sqls->c;
					}
				}
			}

		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-10`='Да')and(listformid in ($listschool))");
		    $result[$field[11]] = $sqls->c;
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-11`='Да')and(listformid in ($listschool))");
		    $result[$field[12]] = $sqls->c;
		    $sqls = get_record_sql("select count(*) as c from {$CFG->prefix}monit_bkp_table_$rzdid7 where (`f-r7-12`='Да')and(listformid in ($listschool))");
		    $result[$field[13]] = $sqls->c;
		}
	}
	return $result;
}

function d13_8($rid, $id, $rzdid8, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<2;$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			$sqls = get_record_sql("select sum(`f-r8-01`) as f1,sum(`f-r8-02`) as f2 from {$CFG->prefix}monit_bkp_table_$rzdid8 where (listformid in ($listschool))");
			$result[$field[0]] = $sqls->f1;
			$result[$field[1]] = $sqls->f2;
		}
	}
	return $result;
}

function d13_9($rid, $id, $rzdid9, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<2;$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool)) and (`f-r9-01`>0)");
			$result[$field[0]] = $sqls->f1;

			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
		}
	}
	return $result;
}

function d13($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='1-od'");
	$rzd_id1 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=1");
	$rzd_id2 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=2");
	$rzd_id3 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=3");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id5 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=5");
	$rzd_id6 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=6");
	$rzd_id7 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=7");
	$rzd_id8 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
	$rzd_id9 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=9");

	foreach ($table_rzds as $table_rzd)  {
		switch ($table_rzd->shortname)	{
			case 1:
				unset($data);
				$field[0] = 'f-r1-02-4';
				$field[1] = 'f-r1-02-5';
				$field[2] = 'f-r1-02-7';
				$data = d13_1_1($rid, 41, $rzd_id1->id, $field, $rzd_id5->id);
				$field[0] = 'f-r1-03-4';
				$field[1] = 'f-r1-03-5';
				$field[2] = 'f-r1-03-7';
				$data = array_merge($data, d13_1_1($rid, 42, $rzd_id1->id, $field, $rzd_id5->id));
				$field[0] = 'f-r1-04-4';
				$field[1] = 'f-r1-04-5';
				$field[2] = 'f-r1-04-7';
				$data = array_merge($data, d13_1_1($rid, 57, $rzd_id1->id, $field, $rzd_id5->id));
				$field[0] = 'f-r1-05-4';
				$field[1] = 'f-r1-05-5';
				$field[2] = 'f-r1-05-7';
				$data = array_merge($data, d13_1_1($rid, 43, $rzd_id1->id, $field, $rzd_id5->id));
				$field[0] = 'f-r1-06-4';
				$field[1] = 'f-r1-06-5';
				$field[2] = 'f-r1-06-7';
				$data = array_merge($data, d13_1_1($rid, 40, $rzd_id1->id, $field, $rzd_id5->id));
				$field[0] = 'f-r1-07-4';
				$field[1] = 'f-r1-07-5';
				$field[2] = 'f-r1-07-7';
				$data = array_merge($data, d13_1_1($rid, 37, $rzd_id1->id, $field, $rzd_id5->id));
				$field[0] = 'f-r1-08-4';
				$field[1] = 'f-r1-08-5';
				$field[2] = 'f-r1-08-7';
				$data = array_merge($data, d13_1_1($rid, 56, $rzd_id1->id, $field, $rzd_id5->id));

				$field[0] = 'f-r1-02-8';
				$field[1] = 'f-r1-02-9';
				$data = array_merge($data, d13_1_2($rid, 41, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-03-8';
				$field[1] = 'f-r1-03-9';
				$data = array_merge($data, d13_1_2($rid, 42, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-04-8';
				$field[1] = 'f-r1-04-9';
				$data = array_merge($data, d13_1_2($rid, 57, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-05-8';
				$field[1] = 'f-r1-05-9';
				$data = array_merge($data, d13_1_2($rid, 43, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-06-8';
				$field[1] = 'f-r1-06-9';
				$data = array_merge($data, d13_1_2($rid, 40, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-07-8';
				$field[1] = 'f-r1-07-9';
				$data = array_merge($data, d13_1_2($rid, 37, $rzd_id1->id, $field, -1));
				$field[0] = 'f-r1-08-8';
				$field[1] = 'f-r1-08-9';
				$data = array_merge($data, d13_1_2($rid, 56, $rzd_id1->id, $field, -1));

				$field[0] = 'f-r1-02-10';
				$field[1] = 'f-r1-02-11';
				$data = array_merge($data, d13_1_2($rid, 41, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-03-10';
				$field[1] = 'f-r1-03-11';
				$data = array_merge($data, d13_1_2($rid, 42, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-04-10';
				$field[1] = 'f-r1-04-11';
				$data = array_merge($data, d13_1_2($rid, 57, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-05-10';
				$field[1] = 'f-r1-05-11';
				$data = array_merge($data, d13_1_2($rid, 43, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-06-10';
				$field[1] = 'f-r1-06-11';
				$data = array_merge($data, d13_1_2($rid, 40, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-07-10';
				$field[1] = 'f-r1-07-11';
				$data = array_merge($data, d13_1_2($rid, 37, $rzd_id1->id, $field, 1));
				$field[0] = 'f-r1-08-10';
				$field[1] = 'f-r1-08-11';
				$data = array_merge($data, d13_1_2($rid, 56, $rzd_id1->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-10-12,f-r1-10-13,f-r1-10-14,f-r1-10-15,f-r1-10-16,f-r1-10-18');
				$data = array_merge($data, d13_1_3($rid, 44, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-11-12,f-r1-11-13,f-r1-11-14,f-r1-11-15,f-r1-11-16,f-r1-11-18');
				$data = array_merge($data, d13_1_3($rid, 45, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-12-12,f-r1-12-13,f-r1-12-14,f-r1-12-15,f-r1-12-16,f-r1-12-18');
				$data = array_merge($data, d13_1_3($rid, 46, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-13-12,f-r1-13-13,f-r1-13-14,f-r1-13-15,f-r1-13-16,f-r1-13-18');
				$data = array_merge($data, d13_1_3($rid, 47, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-15-12,f-r1-15-13,f-r1-15-14,f-r1-15-15,f-r1-15-16,f-r1-15-18');
				$data = array_merge($data, d13_1_3($rid, 48, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-16-12,f-r1-16-13,f-r1-16-14,f-r1-16-15,f-r1-16-16,f-r1-16-18');
				$data = array_merge($data, d13_1_3($rid, 49, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-17-12,f-r1-17-13,f-r1-17-14,f-r1-17-15,f-r1-17-16,f-r1-17-18');
				$data = array_merge($data, d13_1_3($rid, 50, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-18-12,f-r1-18-13,f-r1-18-14,f-r1-18-15,f-r1-18-16,f-r1-18-18');
				$data = array_merge($data, d13_1_3($rid, 51, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-20-12,f-r1-20-13,f-r1-20-14,f-r1-20-15,f-r1-20-16,f-r1-20-18');
				$data = array_merge($data, d13_1_3($rid, 52, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-21-12,f-r1-21-13,f-r1-21-14,f-r1-21-15,f-r1-21-16,f-r1-21-18');
				$data = array_merge($data, d13_1_3($rid, 53, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-22-12,f-r1-22-13,f-r1-22-14,f-r1-22-15,f-r1-22-16,f-r1-22-18');
				$data = array_merge($data, d13_1_3($rid, 54, $rzd_id1->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-23-12,f-r1-23-13,f-r1-23-14,f-r1-23-15,f-r1-23-16,f-r1-23-18');
				$data = array_merge($data, d13_1_3($rid, 55, $rzd_id1->id, $field));

				$data['f-r1-09-12'] = $data['f-r1-10-12'] + $data['f-r1-11-12'] + $data['f-r1-12-12'] + $data['f-r1-13-12'];
				$data['f-r1-09-13'] = $data['f-r1-10-13'] + $data['f-r1-11-13'] + $data['f-r1-12-13'] + $data['f-r1-13-13'];
				$data['f-r1-09-14'] = $data['f-r1-10-14'] + $data['f-r1-11-14'] + $data['f-r1-12-14'] + $data['f-r1-13-14'];
				$data['f-r1-09-15'] = $data['f-r1-10-15'] + $data['f-r1-11-15'] + $data['f-r1-12-15'] + $data['f-r1-13-15'];
				$data['f-r1-09-16'] = $data['f-r1-10-16'] + $data['f-r1-11-16'] + $data['f-r1-12-16'] + $data['f-r1-13-16'];
				$data['f-r1-09-17'] = $data['f-r1-09-12'] + $data['f-r1-09-13'] + $data['f-r1-09-14'] + $data['f-r1-09-15'] + $data['f-r1-09-16'];

				$data['f-r1-14-12'] = $data['f-r1-15-12'] + $data['f-r1-16-12'] + $data['f-r1-17-12'] + $data['f-r1-18-12'];
				$data['f-r1-14-13'] = $data['f-r1-15-13'] + $data['f-r1-16-13'] + $data['f-r1-17-13'] + $data['f-r1-18-13'];
				$data['f-r1-14-14'] = $data['f-r1-15-14'] + $data['f-r1-16-14'] + $data['f-r1-17-14'] + $data['f-r1-18-14'];
				$data['f-r1-14-15'] = $data['f-r1-15-15'] + $data['f-r1-16-15'] + $data['f-r1-17-15'] + $data['f-r1-18-15'];
				$data['f-r1-14-16'] = $data['f-r1-15-16'] + $data['f-r1-16-16'] + $data['f-r1-17-16'] + $data['f-r1-18-16'];
				$data['f-r1-14-17'] = $data['f-r1-14-12'] + $data['f-r1-14-13'] + $data['f-r1-14-14'] + $data['f-r1-14-15'] + $data['f-r1-14-16'];

				$data['f-r1-19-12'] = $data['f-r1-20-12'] + $data['f-r1-21-12'] + $data['f-r1-22-12'] + $data['f-r1-23-12'];
				$data['f-r1-19-13'] = $data['f-r1-20-13'] + $data['f-r1-21-13'] + $data['f-r1-22-13'] + $data['f-r1-23-13'];
				$data['f-r1-19-14'] = $data['f-r1-20-14'] + $data['f-r1-21-14'] + $data['f-r1-22-14'] + $data['f-r1-23-14'];
				$data['f-r1-19-15'] = $data['f-r1-20-15'] + $data['f-r1-21-15'] + $data['f-r1-22-15'] + $data['f-r1-23-15'];
				$data['f-r1-19-16'] = $data['f-r1-20-16'] + $data['f-r1-21-16'] + $data['f-r1-22-16'] + $data['f-r1-23-16'];
				$data['f-r1-19-17'] = $data['f-r1-19-12'] + $data['f-r1-19-13'] + $data['f-r1-19-14'] + $data['f-r1-19-15'] + $data['f-r1-19-16'];

				$data['f-r1-01-4'] = $data['f-r1-02-4'] + $data['f-r1-03-4'] + $data['f-r1-04-4'] + $data['f-r1-05-4'];
				$data['f-r1-01-5'] = $data['f-r1-02-5'] + $data['f-r1-03-5'] + $data['f-r1-04-5'] + $data['f-r1-05-5'];
				$data['f-r1-01-7'] = $data['f-r1-02-7'] + $data['f-r1-03-7'] + $data['f-r1-04-7'] + $data['f-r1-05-7'];
				$data['f-r1-01-8'] = $data['f-r1-02-8'] + $data['f-r1-03-8'] + $data['f-r1-04-8'] + $data['f-r1-05-8'];
				$data['f-r1-01-9'] = $data['f-r1-02-9'] + $data['f-r1-03-9'] + $data['f-r1-04-9'] + $data['f-r1-05-9'];
				$data['f-r1-01-10'] = $data['f-r1-02-10'] + $data['f-r1-03-10'] + $data['f-r1-04-10'] + $data['f-r1-05-10'];
				$data['f-r1-01-11'] = $data['f-r1-02-11'] + $data['f-r1-03-11'] + $data['f-r1-04-11'] + $data['f-r1-05-11'];

				$data['f-r1-01-3'] = $data['f-r1-01-8'] + $data['f-r1-01-10'];
				$data['f-r1-02-3'] = $data['f-r1-02-8'] + $data['f-r1-02-10'];
				$data['f-r1-03-3'] = $data['f-r1-03-8'] + $data['f-r1-03-10'];
				$data['f-r1-04-3'] = $data['f-r1-04-8'] + $data['f-r1-04-10'];
				$data['f-r1-05-3'] = $data['f-r1-05-8'] + $data['f-r1-05-10'];
				$data['f-r1-06-3'] = $data['f-r1-06-8'] + $data['f-r1-06-10'];
				$data['f-r1-07-3'] = $data['f-r1-07-8'] + $data['f-r1-07-10'];
				$data['f-r1-08-3'] = $data['f-r1-08-8'] + $data['f-r1-08-10'];

				$data['f-r1-01-6'] = $data['f-r1-01-9'] + $data['f-r1-01-11'];
				$data['f-r1-02-6'] = $data['f-r1-02-9'] + $data['f-r1-02-11'];
				$data['f-r1-03-6'] = $data['f-r1-03-9'] + $data['f-r1-03-11'];
				$data['f-r1-04-6'] = $data['f-r1-04-9'] + $data['f-r1-04-11'];
				$data['f-r1-05-6'] = $data['f-r1-05-9'] + $data['f-r1-05-11'];
				$data['f-r1-06-6'] = $data['f-r1-06-9'] + $data['f-r1-06-11'];
				$data['f-r1-07-6'] = $data['f-r1-07-9'] + $data['f-r1-07-11'];
				$data['f-r1-08-6'] = $data['f-r1-08-9'] + $data['f-r1-08-11'];

				$data['f-r1-01-6'] = $data['f-r1-02-6'] + $data['f-r1-03-6'] + $data['f-r1-04-6'] + $data['f-r1-05-6'];
			break;

			case 2:
				unset($field);
				$field = explode(',', 'f-r2-01-3,f-r2-01-4,f-r2-01-5,f-r2-01-6,f-r2-01-7,f-r2-01-8,f-r2-01-9,f-r2-01-10,f-r2-01-11,f-r2-01-12,f-r2-01-13,f-r2-01-14,f-r2-01-15,f-r2-01-16');
				$data = d13_2($rid, '41,42,43,57', $rzd_id2->id, $field);
				$field = explode(',', 'f-r2-02-3,f-r2-02-4,f-r2-02-5,f-r2-02-6,f-r2-02-7,f-r2-02-8,f-r2-02-9,f-r2-02-10,f-r2-02-11,f-r2-02-12,f-r2-02-13,f-r2-02-14,f-r2-02-15,f-r2-02-16');
				$data = array_merge($data, d13_2($rid, '40', $rzd_id2->id, $field));
				$field = explode(',', 'f-r2-03-3,f-r2-03-4,f-r2-03-5,f-r2-03-6,f-r2-03-7,f-r2-03-8,f-r2-03-9,f-r2-03-10,f-r2-03-11,f-r2-03-12,f-r2-03-13,f-r2-03-14,f-r2-03-15,f-r2-03-16');
				$data = array_merge($data, d13_2($rid, '37', $rzd_id2->id, $field));
				$field = explode(',', 'f-r2-04-3,f-r2-04-4,f-r2-04-5,f-r2-04-6,f-r2-04-7,f-r2-04-8,f-r2-04-9,f-r2-04-10,f-r2-04-11,f-r2-04-12,f-r2-04-13,f-r2-04-14,f-r2-04-15,f-r2-04-16');
				$data = array_merge($data, d13_2($rid, '56', $rzd_id2->id, $field));
			break;
			case 3:
				unset($field);
				$field = explode(',', 'f-r3-01-3,f-r3-01-4,f-r3-01-5,f-r3-01-6,f-r3-01-7,f-r3-01-8,f-r3-01-9,f-r3-01-10,f-r3-01-11,f-r3-01-12,f-r3-01-13,f-r3-01-14,f-r3-01-15,f-r3-01-16');
				$data = d13_3($rid, '41,42,43,57', $rzd_id1->id, $field, $rzd_id2->id);
				$field = explode(',', 'f-r3-02-3,f-r3-02-4,f-r3-02-5,f-r3-02-6,f-r3-02-7,f-r3-02-8,f-r3-02-9,f-r3-02-10,f-r3-02-11,f-r3-02-12,f-r3-02-13,f-r3-02-14,f-r3-02-15,f-r3-02-16');
				$data = array_merge($data, d13_3($rid, '40', $rzd_id1->id, $field, $rzd_id2->id));
				$field = explode(',', 'f-r3-03-3,f-r3-03-4,f-r3-03-5,f-r3-03-6,f-r3-03-7,f-r3-03-8,f-r3-03-9,f-r3-03-10,f-r3-03-11,f-r3-03-12,f-r3-03-13,f-r3-03-14,f-r3-03-15,f-r3-03-16');
				$data = array_merge($data, d13_3($rid, '37', $rzd_id1->id, $field, $rzd_id2->id));
				$field = explode(',', 'f-r3-04-3,f-r3-04-4,f-r3-04-5,f-r3-04-6,f-r3-04-7,f-r3-04-8,f-r3-04-9,f-r3-04-10,f-r3-04-11,f-r3-04-12,f-r3-04-13,f-r3-04-14,f-r3-04-15,f-r3-04-16');
				$data = array_merge($data, d13_3($rid, '56', $rzd_id1->id, $field, $rzd_id2->id));
			break;
			case 4:
				unset($field);
				$field = explode(',', 'f-r4-02-3,f-r4-02-4');
				$data = d13_4($rid, '41', $rzd_id4->id, $field, -1);

				unset($field);
				$field = explode(',', 'f-r4-03-3,f-r4-03-4');
				$data = array_merge($data, d13_4($rid, '42', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r4-04-3,f-r4-04-4');
				$data = array_merge($data, d13_4($rid, '57', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r4-05-3,f-r4-05-4');
				$data = array_merge($data, d13_4($rid, '43', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r4-06-3,f-r4-06-4');
				$data = array_merge($data, d13_4($rid, '40', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r4-07-3,f-r4-07-4');
				$data = array_merge($data, d13_4($rid, '37', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r4-02-5,f-r4-02-6');
				$data = array_merge($data, d13_4($rid, '41', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r4-03-5,f-r4-03-6');
				$data = array_merge($data, d13_4($rid, '42', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r4-04-5,f-r4-04-6');
				$data = array_merge($data, d13_4($rid, '57', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r4-05-5,f-r4-05-6');
				$data = array_merge($data, d13_4($rid, '43', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r4-06-5,f-r4-06-6');
				$data = array_merge($data, d13_4($rid, '40', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r4-07-5,f-r4-07-6');
				$data = array_merge($data, d13_4($rid, '37', $rzd_id4->id, $field, 1));

				$data['f-r4-01-3'] = $data['f-r4-02-3'] + $data['f-r4-03-3'] + $data['f-r4-04-3'] + $data['f-r4-05-3'];
				$data['f-r4-01-4'] = $data['f-r4-02-4'] + $data['f-r4-03-4'] + $data['f-r4-04-4'] + $data['f-r4-05-4'];
				$data['f-r4-01-5'] = $data['f-r4-02-5'] + $data['f-r4-03-5'] + $data['f-r4-04-5'] + $data['f-r4-05-5'];
				$data['f-r4-01-6'] = $data['f-r4-02-6'] + $data['f-r4-03-6'] + $data['f-r4-04-6'] + $data['f-r4-05-6'];

				$data['f-r4-01-7'] = $data['f-r4-01-3'] + $data['f-r4-01-4'] + $data['f-r4-01-5'] + $data['f-r4-01-6'];
				$data['f-r4-02-7'] = $data['f-r4-02-3'] + $data['f-r4-02-4'] + $data['f-r4-02-5'] + $data['f-r4-02-6'];
				$data['f-r4-03-7'] = $data['f-r4-03-3'] + $data['f-r4-03-4'] + $data['f-r4-03-5'] + $data['f-r4-03-6'];
				$data['f-r4-04-7'] = $data['f-r4-04-3'] + $data['f-r4-04-4'] + $data['f-r4-04-5'] + $data['f-r4-04-6'];
				$data['f-r4-05-7'] = $data['f-r4-05-3'] + $data['f-r4-05-4'] + $data['f-r4-05-5'] + $data['f-r4-05-6'];
				$data['f-r4-06-7'] = $data['f-r4-06-3'] + $data['f-r4-06-4'] + $data['f-r4-06-5'] + $data['f-r4-06-6'];
				$data['f-r4-07-7'] = $data['f-r4-07-3'] + $data['f-r4-07-4'] + $data['f-r4-07-5'] + $data['f-r4-07-6'];

			break;
			case 5:
				unset($field);
				$field = explode(',', 'f-r5-01-3,f-r5-02-3');
				$data = d13_5($rid, '41,42,43,57', $rzd_id3->id, $field);
				unset($field);
				$field = explode(',', 'f-r5-01-4,f-r5-02-4');
				$data = array_merge($data, d13_5($rid, '40', $rzd_id3->id, $field));
				unset($field);
				$field = explode(',', 'f-r5-01-5,f-r5-02-5');
				$data = array_merge($data, d13_5($rid, '37', $rzd_id3->id, $field));
			break;
			case 6:
				unset($field);
				$field = explode(',', 'f-r6-01-3,f-r6-01-4,f-r6-01-5,f-r6-01-6,f-r6-01-7,f-r6-02-3,f-r6-02-4,f-r6-02-5,f-r6-02-6,f-r6-02-7,f-r6-03-3,f-r6-03-4,f-r6-03-5,f-r6-03-6,f-r6-03-7,f-r6-04-3,f-r6-04-4,f-r6-04-5,f-r6-04-6,f-r6-04-7,f-r6-05-3,f-r6-05-4,f-r6-05-5,f-r6-05-6,f-r6-05-7,f-r6-06-3,f-r6-06-4,f-r6-06-5,f-r6-06-6,f-r6-06-7,f-r6-07-3,f-r6-07-4,f-r6-07-5,f-r6-07-6,f-r6-07-7');
				$data = d13_6($rid, '41,42,43,57', $rzd_id5->id, $field);
				unset($field);
				$field = explode(',', 'f-r6-08-3,f-r6-08-4,f-r6-08-5,f-r6-08-6,f-r6-08-7,f-r6-09-3,f-r6-09-4,f-r6-09-5,f-r6-09-6,f-r6-09-7,f-r6-10-3,f-r6-10-4,f-r6-10-5,f-r6-10-6,f-r6-10-7,f-r6-11-3,f-r6-11-4,f-r6-11-5,f-r6-11-6,f-r6-11-7,f-r6-12-3,f-r6-12-4,f-r6-12-5,f-r6-12-6,f-r6-12-7,f-r6-13-3,f-r6-13-4,f-r6-13-5,f-r6-13-6,f-r6-13-7,f-r6-14-3,f-r6-14-4,f-r6-14-5,f-r6-14-6,f-r6-14-7');
				$data = array_merge($data, d13_6($rid, '40', $rzd_id5->id, $field));
				unset($field);
				$field = explode(',', 'f-r6-15-3,f-r6-15-4,f-r6-15-5,f-r6-15-6,f-r6-15-7,f-r6-16-3,f-r6-16-4,f-r6-16-5,f-r6-16-6,f-r6-16-7,f-r6-17-3,f-r6-17-4,f-r6-17-5,f-r6-17-6,f-r6-17-7,f-r6-18-3,f-r6-18-4,f-r6-18-5,f-r6-18-6,f-r6-18-7,f-r6-19-3,f-r6-19-4,f-r6-19-5,f-r6-19-6,f-r6-19-7,f-r6-20-3,f-r6-20-4,f-r6-20-5,f-r6-20-6,f-r6-20-7,f-r6-21-3,f-r6-21-4,f-r6-21-5,f-r6-21-6,f-r6-21-7');
				$data = array_merge($data, d13_6($rid, '37', $rzd_id5->id, $field));
				unset($field);
				$field = explode(',', 'f-r6-22-3,f-r6-22-4,f-r6-22-5,f-r6-22-6,f-r6-22-7,f-r6-23-3,f-r6-23-4,f-r6-23-5,f-r6-23-6,f-r6-23-7,f-r6-24-3,f-r6-24-4,f-r6-24-5,f-r6-24-6,f-r6-24-7,f-r6-25-3,f-r6-25-4,f-r6-25-5,f-r6-25-6,f-r6-25-7,f-r6-26-3,f-r6-26-4,f-r6-26-5,f-r6-26-6,f-r6-26-7,f-r6-27-3,f-r6-27-4,f-r6-27-5,f-r6-27-6,f-r6-27-7,f-r6-28-3,f-r6-28-4,f-r6-28-5,f-r6-28-6,f-r6-28-7');
				$data = array_merge($data, d13_6($rid, '56', $rzd_id5->id, $field));
			break;
			case 7:
				unset($field);
				$field = explode(',', 'f-r7-01-3,f-r7-02-3,f-r7-03-3,f-r7-04-3,f-r7-05-3,f-r7-06-3,f-r7-07-3,f-r7-08-3,f-r7-09-3,f-r7-10-3,f-r7-11-3,f-r7-12-3,f-r7-13-3,f-r7-14-3');
				$data = d13_7($rid, '41,42,43,57', $rzd_id7->id, $field, $rzd_id1->id);

				unset($field);
				$field = explode(',', 'f-r7-01-4,f-r7-02-4,f-r7-03-4,f-r7-04-4,f-r7-05-4,f-r7-06-4,f-r7-07-4,f-r7-08-4,f-r7-09-4,f-r7-10-4,f-r7-11-4,f-r7-12-4,f-r7-13-4,f-r7-14-4');
				$data = array_merge($data, d13_7($rid, '40', $rzd_id7->id, $field, $rzd_id1->id));

				unset($field);
				$field = explode(',', 'f-r7-01-5,f-r7-02-5,f-r7-03-5,f-r7-04-5,f-r7-05-5,f-r7-06-5,f-r7-07-5,f-r7-08-5,f-r7-09-5,f-r7-10-5,f-r7-11-5,f-r7-12-5,f-r7-13-5,f-r7-14-5');
				$data = array_merge($data, d13_7($rid, '37', $rzd_id7->id, $field, $rzd_id1->id));
			break;
			case 8:
				unset($field);
				$field = explode(',', 'f-r8-01-3,f-r8-02-3');
				$data = d13_8($rid, '41,42,43,57', $rzd_id8->id, $field);

				unset($field);
				$field = explode(',', 'f-r8-01-4,f-r8-02-4');
				$data = array_merge($data, d13_8($rid, '40', $rzd_id8->id, $field));

				unset($field);
				$field = explode(',', 'f-r8-01-5,f-r8-02-5');
				$data = array_merge($data, d13_8($rid, '37', $rzd_id8->id, $field));
			break;
			case 9:
				unset($field);
				$field = explode(',', 'f-r9-01-3,f-r9-02-3');
				$data = d13_9($rid, '41,42,43,57', $rzd_id9->id, $field);

				unset($field);
				$field = explode(',', 'f-r9-01-4,f-r9-02-4');
				$data = array_merge($data, d13_9($rid, '40', $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r9-01-5,f-r9-02-5');
				$data = array_merge($data, d13_9($rid, '37', $rzd_id9->id, $field));
			break;

		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d6_1($rid, $id, $iscountryside, $rzdid4, $rzdid6, $field, $countclass) {	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid=$sql->id)");
			if($sqls) {				unset($lists);
			    foreach ($sqls as $sql)  {
			        $lists[] = $sql->id;
			    }
			    $listschool = implode(',', $lists);

				$sql1 = get_record_sql("select `f-r6-01` as f01 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
				$sql2 = get_record_sql("select `f-r4-18-3` as f01 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");

				if($sql2->f01 == $countclass) {					switch($sql1->f01) {						case 0:
						break;
						case 1:
							$result[$field[0]] = $result[$field[0]] + $sql2->f01;
						break;						case 2:
							$result[$field[1]] = $result[$field[1]] + $sql2->f01;
						break;
						default:;
							$result[$field[2]] = $result[$field[2]] + $sql2->f01;
					}				} else {					if(($countclass == 5)&&($sql2->f01 > 4)) {						switch($sql1->f01) {
							case 0:
							break;
							case 1:
								$result[$field[0]] = $result[$field[0]] + $sql2->f01;
							break;
							case 2:
								$result[$field[1]] = $result[$field[1]] + $sql2->f01;
							break;
							default:;
								$result[$field[2]] = $result[$field[2]] + $sql2->f01;
						}
					}				}
			}
		}
	}

	$result[$field[3]] = $result[$field[0]] + $result[$field[1]] + $result[$field[2]];
	return $result;
}

function d6_12($rid, $id, $rzdid6, $field) {	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool)) and (`f-r6-01`>4) and (`f-r6-01`< 8)");
			$result[$field[0]] = $sqls->f1;
			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool)) and (`f-r6-01`>7) and (`f-r6-01` < 10)");
			$result[$field[1]] = $sqls->f1;
			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool)) and (`f-r6-01`>9)");
			$result[$field[2]] = $sqls->f1;

		}
	}
	return $result;
}

function d6_2($rid, $id, $rzdid4, $field, $where) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=-1)and(stateinstitution in ($id))");
    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and $where");
			$result[$field[0]] = $sqls->f1;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($id))");
    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and $where");
			$result[$field[1]] = $sqls->f1;
		}
	}

	$result[$field[2]] = $result[$field[0]] + $result[$field[1]];
	return $result;
}

function d6_3($rid, $id, $iscountryside, $rzdid4, $field, $first, $second) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			for($i=1;$i<count($field);$i++) {
				$where = " (`f-r4-18-4` > $first[$i]) and (`f-r4-18-4` < $second[$i])";
				$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and $where");
				$result[$field[$i]] = $sqls->f1;
			}
		}
	}

	for($i=1;$i<count($field);$i++) {
		$result[$field[0]] = $result[$field[0]] + $result[$field[$i]];
	}

	return $result;
}

function d6_4($rid, $id, $iscountryside, $rzdid4, $field, $first, $second) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);
			for($i=1;$i<count($field);$i++) {
				$where = " (`f-r4-18-4` > $first[$i]) and (`f-r4-18-4` < $second[$i])";
				$sqls = get_record_sql("select sum(`f-r4-18-4`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and $where");

				if($sqls->f1) {
					$result[$field[$i]] = $sqls->f1;
				} else {					$result[$field[$i]] = 0;
				}
			}
		}
	}

	for($i=1;$i<count($field);$i++) {
		$result[$field[0]] = $result[$field[0]] + $result[$field[$i]];
	}

	return $result;
}

function d6($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id3 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=3");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id6 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=6");

	foreach ($table_rzds as $table_rzd)  {
		switch ($table_rzd->shortname)	{
			case 1:
				unset($field);
				$field = explode(',', 'f-r1-01-3,f-r1-01-4,f-r1-01-5,f-r1-01-6');
				$data = d6_1($rid, '5', -1, $rzd_id4->id, $rzd_id6->id, $field, 1);
				unset($field);
				$field = explode(',', 'f-r1-02-3,f-r1-02-4,f-r1-02-5,f-r1-02-6');
				$data = array_merge($data, d6_1($rid, '5', -1, $rzd_id4->id, $rzd_id6->id, $field, 2));
				unset($field);
				$field = explode(',', 'f-r1-03-3,f-r1-03-4,f-r1-03-5,f-r1-03-6');
				$data = array_merge($data, d6_1($rid, '5', -1, $rzd_id4->id, $rzd_id6->id, $field, 3));
				unset($field);
				$field = explode(',', 'f-r1-04-3,f-r1-04-4,f-r1-04-5,f-r1-04-6');
				$data = array_merge($data, d6_1($rid, '5', -1, $rzd_id4->id, $rzd_id6->id, $field, 4));
				unset($field);
				$field = explode(',', 'f-r1-05-3,f-r1-05-4,f-r1-05-5,f-r1-05-6');
				$data = array_merge($data, d6_1($rid, '5', -1, $rzd_id4->id, $rzd_id6->id, $field, 5));

				unset($field);
				$field = explode(',', 'f-r1-01-7,f-r1-01-8,f-r1-01-9,f-r1-01-10');
				$data = array_merge($data, d6_1($rid, '5', 1, $rzd_id4->id, $rzd_id6->id, $field, 1));
				unset($field);
				$field = explode(',', 'f-r1-02-7,f-r1-02-8,f-r1-02-9,f-r1-02-10');
				$data = array_merge($data, d6_1($rid, '5', 1, $rzd_id4->id, $rzd_id6->id, $field, 2));
				unset($field);
				$field = explode(',', 'f-r1-03-7,f-r1-03-8,f-r1-03-9,f-r1-03-10');
				$data = array_merge($data, d6_1($rid, '5', 1, $rzd_id4->id, $rzd_id6->id, $field, 3));
				unset($field);
				$field = explode(',', 'f-r1-04-7,f-r1-04-8,f-r1-04-9,f-r1-04-10');
				$data = array_merge($data, d6_1($rid, '5', 1, $rzd_id4->id, $rzd_id6->id, $field, 4));
				unset($field);
				$field = explode(',', 'f-r1-05-7,f-r1-05-8,f-r1-05-9,f-r1-05-10');
				$data = array_merge($data, d6_1($rid, '5', 1, $rzd_id4->id, $rzd_id6->id, $field, 5));


				$data['f-r1-06-3'] = $data['f-r1-01-3'] + $data['f-r1-02-3'] + $data['f-r1-03-3'] + $data['f-r1-04-3'] + $data['f-r1-05-3'];
				$data['f-r1-06-4'] = $data['f-r1-01-4'] + $data['f-r1-02-4'] + $data['f-r1-03-4'] + $data['f-r1-04-4'] + $data['f-r1-05-4'];
				$data['f-r1-06-5'] = $data['f-r1-01-5'] + $data['f-r1-02-5'] + $data['f-r1-03-5'] + $data['f-r1-04-5'] + $data['f-r1-05-5'];
				$data['f-r1-06-6'] = $data['f-r1-01-6'] + $data['f-r1-02-6'] + $data['f-r1-03-6'] + $data['f-r1-04-6'] + $data['f-r1-05-6'];
				$data['f-r1-06-7'] = $data['f-r1-01-7'] + $data['f-r1-02-7'] + $data['f-r1-03-7'] + $data['f-r1-04-7'] + $data['f-r1-05-7'];
				$data['f-r1-06-8'] = $data['f-r1-01-8'] + $data['f-r1-02-8'] + $data['f-r1-03-8'] + $data['f-r1-04-8'] + $data['f-r1-05-8'];
				$data['f-r1-06-9'] = $data['f-r1-01-9'] + $data['f-r1-02-9'] + $data['f-r1-03-9'] + $data['f-r1-04-9'] + $data['f-r1-05-9'];
				$data['f-r1-06-10'] = $data['f-r1-01-10'] + $data['f-r1-02-10'] + $data['f-r1-03-10'] + $data['f-r1-04-10'] + $data['f-r1-05-10'];

				$data['f-r1-01-11'] = $data['f-r1-01-3'] + $data['f-r1-01-7'];
				$data['f-r1-02-11'] = $data['f-r1-02-3'] + $data['f-r1-02-7'];
				$data['f-r1-03-11'] = $data['f-r1-03-3'] + $data['f-r1-03-7'];
				$data['f-r1-04-11'] = $data['f-r1-04-3'] + $data['f-r1-04-7'];
				$data['f-r1-05-11'] = $data['f-r1-05-3'] + $data['f-r1-05-7'];
				$data['f-r1-06-11'] = $data['f-r1-06-3'] + $data['f-r1-06-7'];

				$data['f-r1-01-12'] = $data['f-r1-01-4'] + $data['f-r1-01-8'];
				$data['f-r1-02-12'] = $data['f-r1-02-4'] + $data['f-r1-02-8'];
				$data['f-r1-03-12'] = $data['f-r1-03-4'] + $data['f-r1-03-8'];
				$data['f-r1-04-12'] = $data['f-r1-04-4'] + $data['f-r1-04-8'];
				$data['f-r1-05-12'] = $data['f-r1-05-4'] + $data['f-r1-05-8'];
				$data['f-r1-06-12'] = $data['f-r1-06-4'] + $data['f-r1-06-8'];

				$data['f-r1-01-13'] = $data['f-r1-01-5'] + $data['f-r1-01-9'];
				$data['f-r1-02-13'] = $data['f-r1-02-5'] + $data['f-r1-02-9'];
				$data['f-r1-03-13'] = $data['f-r1-03-5'] + $data['f-r1-03-9'];
				$data['f-r1-04-13'] = $data['f-r1-04-5'] + $data['f-r1-04-9'];
				$data['f-r1-05-13'] = $data['f-r1-05-5'] + $data['f-r1-05-9'];
				$data['f-r1-06-13'] = $data['f-r1-06-5'] + $data['f-r1-06-9'];

				$data['f-r1-01-14'] = $data['f-r1-01-6'] + $data['f-r1-01-10'];
				$data['f-r1-02-14'] = $data['f-r1-02-6'] + $data['f-r1-02-10'];
				$data['f-r1-03-14'] = $data['f-r1-03-6'] + $data['f-r1-03-10'];
				$data['f-r1-04-14'] = $data['f-r1-04-6'] + $data['f-r1-04-10'];
				$data['f-r1-05-14'] = $data['f-r1-05-6'] + $data['f-r1-05-10'];
				$data['f-r1-06-14'] = $data['f-r1-06-6'] + $data['f-r1-06-10'];

				unset($field);
				$field = explode(',', 'f-r1-07,f-r1-08,f-r1-09');
				$data = array_merge($data, d6_12($rid, '5', $rzd_id6->id, $field));
			break;
			case 2:
				unset($field);
				$field = explode(',', 'f-r2-01-3,f-r2-01-4,f-r2-01-5');
				$data = d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 0) and (`f-r4-18-3` < 5) ');

				unset($field);
				$field = explode(',', 'f-r2-02-3,f-r2-02-4,f-r2-02-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 4) and (`f-r4-18-3` < 7) '));

				unset($field);
				$field = explode(',', 'f-r2-03-3,f-r2-03-4,f-r2-03-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 6) and (`f-r4-18-3` < 11) '));

				unset($field);
				$field = explode(',', 'f-r2-04-3,f-r2-04-4,f-r2-04-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 10) and (`f-r4-18-3` < 14) '));

				unset($field);
				$field = explode(',', 'f-r2-05-3,f-r2-05-4,f-r2-05-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 13) and (`f-r4-18-3` < 17) '));

				unset($field);
				$field = explode(',', 'f-r2-06-3,f-r2-06-4,f-r2-06-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 16) and (`f-r4-18-3` < 20) '));

				unset($field);
				$field = explode(',', 'f-r2-07-3,f-r2-07-4,f-r2-07-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 19) and (`f-r4-18-3` < 23) '));

				unset($field);
				$field = explode(',', 'f-r2-08-3,f-r2-08-4,f-r2-08-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 22) and (`f-r4-18-3` < 26) '));

				unset($field);
				$field = explode(',', 'f-r2-09-3,f-r2-09-4,f-r2-09-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 25) and (`f-r4-18-3` < 30) '));

				unset($field);
				$field = explode(',', 'f-r2-10-3,f-r2-10-4,f-r2-10-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 29) and (`f-r4-18-3` < 34) '));

				unset($field);
				$field = explode(',', 'f-r2-11-3,f-r2-11-4,f-r2-11-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 33) and (`f-r4-18-3` < 38) '));

				unset($field);
				$field = explode(',', 'f-r2-12-3,f-r2-12-4,f-r2-12-5');
				$data = array_merge($data, d6_2($rid, 6, $rzd_id4->id, $field, ' (`f-r4-18-3` > 37) '));

				unset($field);
				$field = explode(',', 'f-r2-01-6,f-r2-01-7,f-r2-01-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 0) and (`f-r4-18-3` < 5) '));

				unset($field);
				$field = explode(',', 'f-r2-02-6,f-r2-02-7,f-r2-02-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 4) and (`f-r4-18-3` < 7) '));

				unset($field);
				$field = explode(',', 'f-r2-03-6,f-r2-03-7,f-r2-03-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 6) and (`f-r4-18-3` < 11) '));

				unset($field);
				$field = explode(',', 'f-r2-04-6,f-r2-04-7,f-r2-04-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 10) and (`f-r4-18-3` < 14) '));

				unset($field);
				$field = explode(',', 'f-r2-05-6,f-r2-05-7,f-r2-05-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 13) and (`f-r4-18-3` < 17) '));

				unset($field);
				$field = explode(',', 'f-r2-06-6,f-r2-06-7,f-r2-06-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 16) and (`f-r4-18-3` < 20) '));

				unset($field);
				$field = explode(',', 'f-r2-07-6,f-r2-07-7,f-r2-07-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 19) and (`f-r4-18-3` < 23) '));

				unset($field);
				$field = explode(',', 'f-r2-08-6,f-r2-08-7,f-r2-08-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 22) and (`f-r4-18-3` < 26) '));

				unset($field);
				$field = explode(',', 'f-r2-09-6,f-r2-09-7,f-r2-09-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 25) and (`f-r4-18-3` < 30) '));

				unset($field);
				$field = explode(',', 'f-r2-10-6,f-r2-10-7,f-r2-10-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 29) and (`f-r4-18-3` < 34) '));

				unset($field);
				$field = explode(',', 'f-r2-11-6,f-r2-11-7,f-r2-11-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 33) and (`f-r4-18-3` < 38) '));

				unset($field);
				$field = explode(',', 'f-r2-12-6,f-r2-12-7,f-r2-12-8');
				$data = array_merge($data, d6_2($rid, '1,2', $rzd_id4->id, $field, ' (`f-r4-18-3` > 37) '));

				$data['f-r2-13-3'] = $data['f-r2-12-3'] + $data['f-r2-11-3'] + $data['f-r2-10-3'] + $data['f-r2-09-3'] + $data['f-r2-08-3'] + $data['f-r2-07-3'] + $data['f-r2-06-3'] + $data['f-r2-05-3'] + $data['f-r2-04-3'] + $data['f-r2-03-3'] + $data['f-r2-02-3'] + $data['f-r2-01-3'];
				$data['f-r2-13-4'] = $data['f-r2-12-4'] + $data['f-r2-11-4'] + $data['f-r2-10-4'] + $data['f-r2-09-4'] + $data['f-r2-08-4'] + $data['f-r2-07-4'] + $data['f-r2-06-4'] + $data['f-r2-05-4'] + $data['f-r2-04-4'] + $data['f-r2-03-4'] + $data['f-r2-02-4'] + $data['f-r2-01-4'];
				$data['f-r2-13-5'] = $data['f-r2-12-5'] + $data['f-r2-11-5'] + $data['f-r2-10-5'] + $data['f-r2-09-5'] + $data['f-r2-08-5'] + $data['f-r2-07-5'] + $data['f-r2-06-5'] + $data['f-r2-05-5'] + $data['f-r2-04-5'] + $data['f-r2-03-5'] + $data['f-r2-02-5'] + $data['f-r2-01-5'];
				$data['f-r2-13-6'] = $data['f-r2-12-6'] + $data['f-r2-11-6'] + $data['f-r2-10-6'] + $data['f-r2-09-6'] + $data['f-r2-08-6'] + $data['f-r2-07-6'] + $data['f-r2-06-6'] + $data['f-r2-05-6'] + $data['f-r2-04-6'] + $data['f-r2-03-6'] + $data['f-r2-02-6'] + $data['f-r2-01-6'];
				$data['f-r2-13-7'] = $data['f-r2-12-7'] + $data['f-r2-11-7'] + $data['f-r2-10-7'] + $data['f-r2-09-7'] + $data['f-r2-08-7'] + $data['f-r2-07-7'] + $data['f-r2-06-7'] + $data['f-r2-05-7'] + $data['f-r2-04-7'] + $data['f-r2-03-7'] + $data['f-r2-02-7'] + $data['f-r2-01-7'];
				$data['f-r2-13-8'] = $data['f-r2-12-8'] + $data['f-r2-11-8'] + $data['f-r2-10-8'] + $data['f-r2-09-8'] + $data['f-r2-08-8'] + $data['f-r2-07-8'] + $data['f-r2-06-8'] + $data['f-r2-05-8'] + $data['f-r2-04-8'] + $data['f-r2-03-8'] + $data['f-r2-02-8'] + $data['f-r2-01-8'];


				$id = '6,1,2';
				$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

			    if($sqls) {
			    	unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->id;
					}

					$listform = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

					if($sqls) {
						unset($lists);
					    foreach ($sqls as $sql)  {
					        $lists[] = $sql->id;
					    }
					    $listschool = implode(',', $lists);
						$sqls = get_records_sql("select `f-r3-02` as f1 from {$CFG->prefix}monit_bkp_table_$rzd_id3->id where (listformid in ($listschool))");
						$class = 0;
						$komplekt = 0;
						foreach ($sqls as $sql)  {
							$clakom = explode('/', $sql->f1);
							$class = $class + $clakom[0];
							$komplekt = $komplekt + $clakom[1];
						}

						$data['f-r2-14'] = "$class/$komplekt";
					}
				}
			break;
			case 3:
				$first = explode(',', '0,0,9,14,20,29,40,60,100,120,180,280');
				$second = explode(',', '0,10,15,21,30,41,61,101,121,181,281,1000000');

				unset($field);
				$field = explode(',', 'f-r3-01-3,f-r3-01-4,f-r3-01-5,f-r3-01-6,f-r3-01-7,f-r3-01-8,f-r3-01-9,f-r3-01-10,f-r3-01-11,f-r3-01-12,f-r3-01-13,f-r3-01-14');
				$data = d6_3($rid, 5, -1, $rzd_id4->id, $field, $first, $second);

				unset($field);
				$field = explode(',', 'f-r3-02-3,f-r3-02-4,f-r3-02-5,f-r3-02-6,f-r3-02-7,f-r3-02-8,f-r3-02-9,f-r3-02-10,f-r3-02-11,f-r3-02-12,f-r3-02-13,f-r3-02-14');
				$data = array_merge($data, d6_3($rid, 5, 1, $rzd_id4->id, $field, $first, $second));

				$data['f-r3-03-3'] = $data['f-r3-01-3'] + $data['f-r3-02-3'];
				$data['f-r3-03-4'] = $data['f-r3-01-4'] + $data['f-r3-02-4'];
				$data['f-r3-03-5'] = $data['f-r3-01-5'] + $data['f-r3-02-5'];
				$data['f-r3-03-6'] = $data['f-r3-01-6'] + $data['f-r3-02-6'];
				$data['f-r3-03-7'] = $data['f-r3-01-7'] + $data['f-r3-02-7'];
				$data['f-r3-03-8'] = $data['f-r3-01-8'] + $data['f-r3-02-8'];
				$data['f-r3-03-9'] = $data['f-r3-01-9'] + $data['f-r3-02-9'];
				$data['f-r3-03-10'] = $data['f-r3-01-10'] + $data['f-r3-02-10'];
				$data['f-r3-03-11'] = $data['f-r3-01-11'] + $data['f-r3-02-11'];
				$data['f-r3-03-12'] = $data['f-r3-01-12'] + $data['f-r3-02-12'];
				$data['f-r3-03-13'] = $data['f-r3-01-13'] + $data['f-r3-02-13'];
				$data['f-r3-03-14'] = $data['f-r3-01-14'] + $data['f-r3-02-14'];

				unset($first);
				unset($second);
				$first = explode(',', '0,0,40,100,200,280,400,640');
				$second = explode(',', '0,41,101,201,281,401,641,1000000');

				unset($field);
				$field = explode(',', 'f-r3-01-15,f-r3-01-16,f-r3-01-17,f-r3-01-18,f-r3-01-19,f-r3-01-20,f-r3-01-21,f-r3-01-22');
				$data = array_merge($data, d6_3($rid, 6, -1, $rzd_id4->id, $field, $first, $second));

				unset($field);
				$field = explode(',', 'f-r3-02-15,f-r3-02-16,f-r3-02-17,f-r3-02-18,f-r3-02-19,f-r3-02-20,f-r3-02-21,f-r3-02-22');
				$data = array_merge($data, d6_3($rid, 6, 1, $rzd_id4->id, $field, $first, $second));


				$data['f-r3-03-15'] = $data['f-r3-01-15'] + $data['f-r3-02-15'];
				$data['f-r3-03-16'] = $data['f-r3-01-16'] + $data['f-r3-02-16'];
				$data['f-r3-03-17'] = $data['f-r3-01-17'] + $data['f-r3-02-17'];
				$data['f-r3-03-18'] = $data['f-r3-01-18'] + $data['f-r3-02-18'];
				$data['f-r3-03-19'] = $data['f-r3-01-19'] + $data['f-r3-02-19'];
				$data['f-r3-03-20'] = $data['f-r3-01-20'] + $data['f-r3-02-20'];
				$data['f-r3-03-21'] = $data['f-r3-01-21'] + $data['f-r3-02-21'];
				$data['f-r3-03-22'] = $data['f-r3-01-22'] + $data['f-r3-02-22'];


				unset($first);
				unset($second);
				$first = explode(',', '0,0,40,100,200,280,400,640,880,1120,1360,1600');
				$second = explode(',', '0,101,201,281,401,641,881,1121,1361,1601,1000000');

				unset($field);
				$field = explode(',', 'f-r3-01-23,f-r3-01-24,f-r3-01-25,f-r3-01-26,f-r3-01-27,f-r3-01-28,f-r3-01-29,f-r3-01-30,f-r3-01-31,f-r3-01-32,f-r3-01-33');
				$data = array_merge($data, d6_3($rid, '1,2', -1, $rzd_id4->id, $field, $first, $second));

				unset($field);
				$field = explode(',', 'f-r3-02-23,f-r3-02-24,f-r3-02-25,f-r3-02-26,f-r3-02-27,f-r3-02-28,f-r3-02-29,f-r3-02-30,f-r3-02-31,f-r3-02-32,f-r3-02-33');
				$data = array_merge($data, d6_3($rid, '1,2', 1, $rzd_id4->id, $field, $first, $second));

				$data['f-r3-03-23'] = $data['f-r3-01-23'] + $data['f-r3-02-23'];
				$data['f-r3-03-24'] = $data['f-r3-01-24'] + $data['f-r3-02-24'];
				$data['f-r3-03-25'] = $data['f-r3-01-25'] + $data['f-r3-02-25'];
				$data['f-r3-03-26'] = $data['f-r3-01-26'] + $data['f-r3-02-26'];
				$data['f-r3-03-27'] = $data['f-r3-01-27'] + $data['f-r3-02-27'];
				$data['f-r3-03-28'] = $data['f-r3-01-28'] + $data['f-r3-02-28'];
				$data['f-r3-03-29'] = $data['f-r3-01-29'] + $data['f-r3-02-29'];
				$data['f-r3-03-30'] = $data['f-r3-01-30'] + $data['f-r3-02-30'];
				$data['f-r3-03-31'] = $data['f-r3-01-31'] + $data['f-r3-02-31'];
				$data['f-r3-03-32'] = $data['f-r3-01-32'] + $data['f-r3-02-32'];
				$data['f-r3-03-33'] = $data['f-r3-01-33'] + $data['f-r3-02-33'];
			break;
			case 4:
				$first = explode(',', '0,0,9,14,20,29,40,60,100,120,180,280');
				$second = explode(',', '0,10,15,21,30,41,61,101,121,181,281,1000000');

				unset($field);
				$field = explode(',', 'f-r4-01-3,f-r4-01-4,f-r4-01-5,f-r4-01-6,f-r4-01-7,f-r4-01-8,f-r4-01-9,f-r4-01-10,f-r4-01-11,f-r4-01-12,f-r4-01-13,f-r4-01-14');
				$data = d6_4($rid, 5, -1, $rzd_id4->id, $field, $first, $second);

				unset($field);
				$field = explode(',', 'f-r4-02-3,f-r4-02-4,f-r4-02-5,f-r4-02-6,f-r4-02-7,f-r4-02-8,f-r4-02-9,f-r4-02-10,f-r4-02-11,f-r4-02-12,f-r4-02-13,f-r4-02-14');
				$data = array_merge($data, d6_4($rid, 5, 1, $rzd_id4->id, $field, $first, $second));

				$data['f-r4-03-3'] = $data['f-r4-01-3'] + $data['f-r4-02-3'];
				$data['f-r4-03-4'] = $data['f-r4-01-4'] + $data['f-r4-02-4'];
				$data['f-r4-03-5'] = $data['f-r4-01-5'] + $data['f-r4-02-5'];
				$data['f-r4-03-6'] = $data['f-r4-01-6'] + $data['f-r4-02-6'];
				$data['f-r4-03-7'] = $data['f-r4-01-7'] + $data['f-r4-02-7'];
				$data['f-r4-03-8'] = $data['f-r4-01-8'] + $data['f-r4-02-8'];
				$data['f-r4-03-9'] = $data['f-r4-01-9'] + $data['f-r4-02-9'];
				$data['f-r4-03-10'] = $data['f-r4-01-10'] + $data['f-r4-02-10'];
				$data['f-r4-03-11'] = $data['f-r4-01-11'] + $data['f-r4-02-11'];
				$data['f-r4-03-12'] = $data['f-r4-01-12'] + $data['f-r4-02-12'];
				$data['f-r4-03-13'] = $data['f-r4-01-13'] + $data['f-r4-02-13'];
				$data['f-r4-03-14'] = $data['f-r4-01-14'] + $data['f-r4-02-14'];

				unset($first);
				unset($second);
				$first = explode(',', '0,0,40,100,200,280,400,640');
				$second = explode(',', '0,41,101,201,281,401,641,1000000');

				unset($field);
				$field = explode(',', 'f-r4-01-15,f-r4-01-16,f-r4-01-17,f-r4-01-18,f-r4-01-19,f-r4-01-20,f-r4-01-21,f-r4-01-22');
				$data = array_merge($data, d6_4($rid, 6, -1, $rzd_id4->id, $field, $first, $second));

				unset($field);
				$field = explode(',', 'f-r4-02-15,f-r4-02-16,f-r4-02-17,f-r4-02-18,f-r4-02-19,f-r4-02-20,f-r4-02-21,f-r4-02-22');
				$data = array_merge($data, d6_4($rid, 6, 1, $rzd_id4->id, $field, $first, $second));


				$data['f-r4-03-15'] = $data['f-r4-01-15'] + $data['f-r4-02-15'];
				$data['f-r4-03-16'] = $data['f-r4-01-16'] + $data['f-r4-02-16'];
				$data['f-r4-03-17'] = $data['f-r4-01-17'] + $data['f-r4-02-17'];
				$data['f-r4-03-18'] = $data['f-r4-01-18'] + $data['f-r4-02-18'];
				$data['f-r4-03-19'] = $data['f-r4-01-19'] + $data['f-r4-02-19'];
				$data['f-r4-03-20'] = $data['f-r4-01-20'] + $data['f-r4-02-20'];
				$data['f-r4-03-21'] = $data['f-r4-01-21'] + $data['f-r4-02-21'];
				$data['f-r4-03-22'] = $data['f-r4-01-22'] + $data['f-r4-02-22'];


				unset($first);
				unset($second);
				$first = explode(',', '0,0,40,100,200,280,400,640,880,1120,1360,1600');
				$second = explode(',', '0,101,201,281,401,641,881,1121,1361,1601,1000000');

				unset($field);
				$field = explode(',', 'f-r4-01-23,f-r4-01-24,f-r4-01-25,f-r4-01-26,f-r4-01-27,f-r4-01-28,f-r4-01-29,f-r4-01-30,f-r4-01-31,f-r4-01-32,f-r4-01-33');
				$data = array_merge($data, d6_4($rid, '1,2', -1, $rzd_id4->id, $field, $first, $second));

				unset($field);
				$field = explode(',', 'f-r4-02-23,f-r4-02-24,f-r4-02-25,f-r4-02-26,f-r4-02-27,f-r4-02-28,f-r4-02-29,f-r4-02-30,f-r4-02-31,f-r4-02-32,f-r4-02-33');
				$data = array_merge($data, d6_4($rid, '1,2', 1, $rzd_id4->id, $field, $first, $second));

				$data['f-r4-03-23'] = $data['f-r4-01-23'] + $data['f-r4-02-23'];
				$data['f-r4-03-24'] = $data['f-r4-01-24'] + $data['f-r4-02-24'];
				$data['f-r4-03-25'] = $data['f-r4-01-25'] + $data['f-r4-02-25'];
				$data['f-r4-03-26'] = $data['f-r4-01-26'] + $data['f-r4-02-26'];
				$data['f-r4-03-27'] = $data['f-r4-01-27'] + $data['f-r4-02-27'];
				$data['f-r4-03-28'] = $data['f-r4-01-28'] + $data['f-r4-02-28'];
				$data['f-r4-03-29'] = $data['f-r4-01-29'] + $data['f-r4-02-29'];
				$data['f-r4-03-30'] = $data['f-r4-01-30'] + $data['f-r4-02-30'];
				$data['f-r4-03-31'] = $data['f-r4-01-31'] + $data['f-r4-02-31'];
				$data['f-r4-03-32'] = $data['f-r4-01-32'] + $data['f-r4-02-32'];
				$data['f-r4-03-33'] = $data['f-r4-01-33'] + $data['f-r4-02-33'];
//				print_r($data);
			break;
		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function d9_1($rid, $id, $rzdid3, $rzdid6, $rzdid9, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");
    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

		if($sqls) {	$result[$field[0]] = $sqls->f1; }

		$id = explode(',', $id);
		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id[1]))");
		if($sqls) {	$result[$field[7]] = $sqls->f1; }

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			if($sqls) {	$result[$field[8]] = $sqls->f1; }

			$sqls = get_record_sql("select sum(`f-r6-01`) as f1, sum(`f-r6-05`) as f2, sum(`f-r6-06`) as f3 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
			$result[$field[2]] = $sqls->f2;
			$result[$field[4]] = $sqls->f3;

			$sqls = get_records_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			if($sqls) {
				$class = 0;
				$komplekt = 0;
				foreach ($sqls as $sql)  {
					$clakom = explode('/', $sql->f1);
					if(count($clakom)==2) {
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
						$clakom = explode('/', $sql->f2);
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
					}
				}
				$result[$field[6]] = "$class/$komplekt";
			}
		}
	}
	return $result;
}

function d9_12($rid, $id0, $id1, $rzdid3, $rzdid6, $rzdid9, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($id0,$id1))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(iscountryside=1)and(rayonid=$rid)and(stateinstitution in ($id0,$id1))");
		if($sqls) {	$result[$field[0]] = $sqls->f1; }

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(iscountryside=1)and(rayonid=$rid)and(stateinstitution in ($id1))");
		if($sqls) {	$result[$field[7]] = $sqls->f1; }

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			if($sqls) {	$result[$field[8]] = $sqls->f1; }

			$sqls = get_record_sql("select sum(`f-r6-01`) as f1, sum(`f-r6-05`) as f2, sum(`f-r6-06`) as f3 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
			$result[$field[2]] = $sqls->f2;
			$result[$field[4]] = $sqls->f3;

			$sqls = get_records_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			if($sqls) {
				$class = 0;
				$komplekt = 0;
				foreach ($sqls as $sql)  {
					$clakom = explode('/', $sql->f1);
					if(count($clakom)==2) {
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
						$clakom = explode('/', $sql->f2);
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
					}
				}
				$result[$field[6]] = "$class/$komplekt";
			}
		}
	}
	return $result;
}

function d9_13($rid, $id, $rzdid3, $rzdid6, $rzdid9, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[0]] = $sqls->f1; }

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[7]] = $sqls->f1; }

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

//			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
//			if($sqls) {	$result[$field[8]] = $sqls->f1; }

			$sqls = get_record_sql("select sum(`f-r6-01`) as f1, sum(`f-r6-05`) as f2, sum(`f-r6-06`) as f3 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
			$result[$field[2]] = $sqls->f2;
			$result[$field[4]] = $sqls->f3;

			$sqls = get_records_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			if($sqls) {
				$class = 0;
				$komplekt = 0;
				foreach ($sqls as $sql)  {
					$clakom = explode('/', $sql->f1);
					if(count($clakom)==2) {
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
						$clakom = explode('/', $sql->f2);
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
					}
				}
				$result[$field[6]] = "$class/$komplekt";
			}
		}
	}
	return $result;
}

function d9_14($rid, $id, $rzdid3, $rzdid6, $rzdid9, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}
	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=1)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(iscountryside=1)and(rayonid=$rid)and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[0]] = $sqls->f1; }

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(iscountryside=1)and(rayonid=$rid)and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[7]] = $sqls->f1; }

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

//			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
//			if($sqls) {	$result[$field[8]] = $sqls->f1; }

			$sqls = get_record_sql("select sum(`f-r6-01`) as f1, sum(`f-r6-05`) as f2, sum(`f-r6-06`) as f3 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
			$result[$field[2]] = $sqls->f2;
			$result[$field[4]] = $sqls->f3;

			$sqls = get_records_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			if($sqls) {
				$class = 0;
				$komplekt = 0;
				foreach ($sqls as $sql)  {
					$clakom = explode('/', $sql->f1);
					if(count($clakom)==2) {
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
						$clakom = explode('/', $sql->f2);
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
					}
				}
				$result[$field[6]] = "$class/$komplekt";
			}
		}
	}
	return $result;
}

function d9_15($rid, $id, $rzdid3, $rzdid4, $rzdid6, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}
	if($iscountryside != 1) {
		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");
	} else {		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(iscountryside=$iscountryside)and(stateinstitution in ($id))");	}

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and(`f-r4-18-8`>0  or `f-r4-18-10`>0 or `f-r4-18-12`>0)");
			if($sqls) {

				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");


				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid6");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r6-01`) as c from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listform))");
						$result[$field[0]] = $sqls->c;
					}

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid3");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listform))");

						if($sqls) {
							$class = 0;
							$komplekt = 0;
							foreach ($sqls as $sql)  {
								$clakom = explode('/', $sql->f1);
								if(count($clakom)==2) {
									$class = $class + $clakom[0];
									$komplekt = $komplekt + $clakom[1];
									$clakom = explode('/', $sql->f2);
									$class = $class + $clakom[0];
									$komplekt = $komplekt + $clakom[1];
								}
							}
							$result[$field[1]] = "$class/$komplekt";
						}
					}
				}
			}
		}
	}
	return $result;
}

function d9_17($rid, $id, $rzdid3, $rzdid4, $rzdid6, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool)) and `$field[2]`>0");
			if($sqls) {

				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");

				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid6");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r6-01`) as c from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listform))");
						$result[$field[0]] = $sqls->c;
					}

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid3");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listform))");

						if($sqls) {
							$class = 0;
							$komplekt = 0;
							foreach ($sqls as $sql)  {
								$clakom = explode('/', $sql->f1);
								if(count($clakom)==2) {
									$class = $class + $clakom[0];
									$komplekt = $komplekt + $clakom[1];
									$clakom = explode('/', $sql->f2);
									$class = $class + $clakom[0];
									$komplekt = $komplekt + $clakom[1];
								}
							}
							$result[$field[1]] = "$class/$komplekt";
						}
					}
				}
			}
		}
	}
	return $result;
}

function d9_20($rid, $id, $rzdid3, $rzdid6, $rzdid9, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	if($iscountryside == 1) {		$str = 'and(iscountryside=1)';
	} else {		$str = '';	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[0]] = $sqls->f1; }

		$sqls = get_record_sql("select count(id) as f1 from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id))");
		if($sqls) {	$result[$field[7]] = $sqls->f1; }

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`f-r9-01`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			if($sqls) {	$result[$field[8]] = $sqls->f1; }

			$sqls = get_record_sql("select sum(`f-r6-01`) as f1, sum(`f-r6-05`) as f2, sum(`f-r6-06`) as f3 from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listschool))");
			$result[$field[1]] = $sqls->f1;
			$result[$field[2]] = $sqls->f2;
			$result[$field[4]] = $sqls->f3;

			$sqls = get_records_sql("select `f-r3-01` as f1, `f-r3-02` as f2 from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			if($sqls) {
				$class = 0;
				$komplekt = 0;
				foreach ($sqls as $sql)  {
					$clakom = explode('/', $sql->f1);
					if(count($clakom)==2) {
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
						$clakom = explode('/', $sql->f2);
						$class = $class + $clakom[0];
						$komplekt = $komplekt + $clakom[1];
					}
				}
				$result[$field[6]] = "$class/$komplekt";
			}
		}
	}
	return $result;
}

function d9_27($rid, $id0, $id1, $rzdid4, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	if($iscountryside == 1) {
		$str = 'and(iscountryside=1)';
	} else {
		$str = '';
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id0,$id1))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`$field[20]`) as f20,sum(`$field[21]`) as f21,sum(`$field[22]`) as f22,sum(`$field[23]`) as f23,sum(`$field[24]`) as f24,sum(`$field[25]`) as f25,sum(`$field[26]`) as f26,sum(`$field[27]`) as f27,sum(`$field[28]`) as f28,sum(`$field[29]`) as f29,sum(`$field[30]`) as f30,sum(`$field[31]`) as f31,sum(`$field[32]`) as f32,sum(`$field[33]`) as f33,sum(`$field[34]`) as f34,sum(`$field[35]`) as f35,sum(`$field[36]`) as f36,sum(`$field[37]`) as f37,sum(`$field[38]`) as f38,sum(`$field[39]`) as f39,sum(`$field[40]`) as f40,sum(`$field[41]`) as f41,sum(`$field[42]`) as f42,sum(`$field[43]`) as f43,sum(`$field[44]`) as f44,sum(`$field[45]`) as f45,sum(`$field[46]`) as f46,sum(`$field[47]`) as f47,sum(`$field[48]`) as f48,sum(`$field[49]`) as f49,sum(`$field[50]`) as f50,sum(`$field[51]`) as f51,sum(`$field[52]`) as f52,sum(`$field[53]`) as f53,sum(`$field[54]`) as f54,sum(`$field[55]`) as f55,sum(`$field[56]`) as f56,sum(`$field[57]`) as f57,sum(`$field[58]`) as f58,sum(`$field[59]`) as f59,sum(`$field[60]`) as f60,sum(`$field[61]`) as f61,sum(`$field[62]`) as f62,sum(`$field[63]`) as f63,sum(`$field[64]`) as f64,sum(`$field[65]`) as f65,sum(`$field[66]`) as f66,sum(`$field[67]`) as f67,sum(`$field[68]`) as f68,sum(`$field[69]`) as f69,sum(`$field[70]`) as f70,sum(`$field[71]`) as f71,sum(`$field[72]`) as f72,sum(`$field[73]`) as f73,sum(`$field[74]`) as f74,sum(`$field[75]`) as f75,sum(`$field[76]`) as f76,sum(`$field[77]`) as f77,sum(`$field[78]`) as f78,sum(`$field[79]`) as f79,sum(`$field[80]`) as f80,sum(`$field[81]`) as f81,sum(`$field[82]`) as f82,sum(`$field[83]`) as f83,sum(`$field[84]`) as f84,sum(`$field[85]`) as f85,sum(`$field[86]`) as f86,sum(`$field[87]`) as f87,sum(`f-r4-22`) as fr422  from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");

			$result[$field[2]] = $sqls->f20 + $sqls->f37 + $sqls->f54 + $sqls->f71;
			$result[$field[3]] = $sqls->f21 + $sqls->f22 + $sqls->f23 + $sqls->f38 + $sqls->f39 + $sqls->f40 + $sqls->f55 + $sqls->f56 + $sqls->f57 + $sqls->f72 + $sqls->f73 + $sqls->f74;
			$result[$field[4]] = $sqls->f24 + $sqls->f25 + $sqls->f41 + $sqls->f42 + $sqls->f58 + $sqls->f59 + $sqls->f75 + $sqls->f76;
			$result[$field[5]] = $sqls->f26 + $sqls->f27 + $sqls->f43 + $sqls->f44 + $sqls->f60 + $sqls->f61 + $sqls->f77 + $sqls->f78;
			$result[$field[6]] = $sqls->f28 + $sqls->f45 + $sqls->f62 + $sqls->f79;
			$result[$field[7]] = $sqls->f29 + $sqls->f46 + $sqls->f63 + $sqls->f80;
			$result[$field[8]] = $sqls->f30 + $sqls->f47 + $sqls->f64 + $sqls->f81;
			$result[$field[9]] = $sqls->f31 + $sqls->f48 + $sqls->f65 + $sqls->f82;
			$result[$field[10]] = $sqls->f32 + $sqls->f49 + $sqls->f66 + $sqls->f83;
			$result[$field[11]] = $sqls->f33 + $sqls->f50 + $sqls->f67 + $sqls->f84;
			$result[$field[12]] = $sqls->f34 + $sqls->f51 + $sqls->f68 + $sqls->f85;
			$result[$field[13]] = $sqls->f35 + $sqls->f52 + $sqls->f69 + $sqls->f86;
			$result[$field[14]] = $sqls->f36 + $sqls->f53 + $sqls->f70 + $sqls->f87;
			$result[$field[16]] = $sqls->fr422;
		}
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id1))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`f-r4-18-4`) as f18, sum(`f-r4-19`) as f19, sum(`f-r4-18-5`) as f185 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");

			$result[$field[17]] = $sqls->f18;
			$result[$field[18]] = $sqls->f19;
			$result[$field[19]] = $sqls->f185;
		}
	}


	for($i=2;$i<15;$i++) {
		$result[$field[0]] = $result[$field[0]] + $result[$field[$i]];
	}
	return $result;
}

function d9_40($rid, $id, $id1, $rzdid4, $field, $iscountryside) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sum = '';
	switch ($id1) {		case 0:
			$field1 = explode(',', 'f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
		break;		case 1:
			$field1 = explode(',', 'f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9');
		break;
		case 2:
			$field1 = explode(',', 'f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11');
		break;
		case 3:
			$field1 = explode(',', 'f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
		break;
	}

	for($i=0;$i<count($field1);$i++) {
		$sum.= "sum(`$field1[$i]`) as f$i,";
	}

	$sum = substr($sum,0,-1);

	if($iscountryside == 1) {
		$str = 'and(iscountryside=1)';
	} else {
		$str = '';
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)$str and(stateinstitution in ($id))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum, sum(`f-r4-22`) as f22 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");

			$result[$field[15]] = $sqls->f22;

			switch ($id1) {
				case 0:
					$result[$field[1]] = $sqls->f0 + $sqls->f17 + $sqls->f34;
					$result[$field[2]] = $sqls->f1 + $sqls->f2 + $sqls->f3 + $sqls->f18 + $sqls->f19 + $sqls->f20 + $sqls->f35 + $sqls->f36 + $sqls->f37;
					$result[$field[3]] = $sqls->f4 + $sqls->f5 + $sqls->f21 + $sqls->f22 + $sqls->f38 + $sqls->f39;
					$result[$field[4]] = $sqls->f6 + $sqls->f7 + $sqls->f23 + $sqls->f24 + $sqls->f40 + $sqls->f41;
					$result[$field[5]] = $sqls->f8 + $sqls->f25 + $sqls->f42;
					$result[$field[6]] = $sqls->f9 + $sqls->f26 + $sqls->f43;
					$result[$field[7]] = $sqls->f10 + $sqls->f27 + $sqls->f44;
					$result[$field[8]] = $sqls->f11 + $sqls->f28 + $sqls->f45;
					$result[$field[9]] = $sqls->f12 + $sqls->f29 + $sqls->f46;
					$result[$field[10]] = $sqls->f13 + $sqls->f30 + $sqls->f47;
					$result[$field[11]] = $sqls->f14 + $sqls->f31 + $sqls->f48;
					$result[$field[12]] = $sqls->f15 + $sqls->f32 + $sqls->f49;
					$result[$field[13]] = $sqls->f16 + $sqls->f33 + $sqls->f50;
				break;
				case 1:
					$result[$field[1]] = $sqls->f0;
					$result[$field[2]] = $sqls->f1 + $sqls->f2 + $sqls->f3;
					$result[$field[3]] = $sqls->f4 + $sqls->f5;
					$result[$field[4]] = $sqls->f6 + $sqls->f7;
					$result[$field[5]] = $sqls->f8;
					$result[$field[6]] = $sqls->f9;
					$result[$field[7]] = $sqls->f10;
					$result[$field[8]] = $sqls->f11;
					$result[$field[9]] = $sqls->f12;
					$result[$field[10]] = $sqls->f13;
					$result[$field[11]] = $sqls->f14;
					$result[$field[12]] = $sqls->f15;
					$result[$field[13]] = $sqls->f16;
				break;
				case 2:
					$result[$field[1]] = $sqls->f0;
					$result[$field[2]] = $sqls->f1 + $sqls->f2 + $sqls->f3;
					$result[$field[3]] = $sqls->f4 + $sqls->f5;
					$result[$field[4]] = $sqls->f6 + $sqls->f7;
					$result[$field[5]] = $sqls->f8;
					$result[$field[6]] = $sqls->f9;
					$result[$field[7]] = $sqls->f10;
					$result[$field[8]] = $sqls->f11;
					$result[$field[9]] = $sqls->f12;
					$result[$field[10]] = $sqls->f13;
					$result[$field[11]] = $sqls->f14;
					$result[$field[12]] = $sqls->f15;
					$result[$field[13]] = $sqls->f16;
				break;
				case 3:
					$result[$field[1]] = $sqls->f0;
					$result[$field[2]] = $sqls->f1 + $sqls->f2 + $sqls->f3;
					$result[$field[3]] = $sqls->f4 + $sqls->f5;
					$result[$field[4]] = $sqls->f6 + $sqls->f7;
					$result[$field[5]] = $sqls->f8;
					$result[$field[6]] = $sqls->f9;
					$result[$field[7]] = $sqls->f10;
					$result[$field[8]] = $sqls->f11;
					$result[$field[9]] = $sqls->f12;
					$result[$field[10]] = $sqls->f13;
					$result[$field[11]] = $sqls->f14;
					$result[$field[12]] = $sqls->f15;
					$result[$field[13]] = $sqls->f16;
				break;
			}
		}
    }
	for($i=1;$i<14;$i++) {
		$result[$field[0]] = $result[$field[0]] + $result[$field[$i]];
	}
	return $result;
}

function d9_2($rid, $id0, $id1, $rzdid4, $rzdid8, $rzdid13, $field) {	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($id0$id1))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select sum(`f-r13-13`) as f13,sum(`f-r13-14`) as f14,sum(`f-r13-24`) as f24,sum(`f-r13-25`) as f25,sum(`f-r13-26`) as f26,sum(`f-r13-27`) as f27,sum(`f-r13-28`) as f28,sum(`f-r13-29`) as f29,sum(`f-r13-30`) as f30,sum(`f-r13-31`) as f31,sum(`f-r13-37`) as f37,sum(`f-r13-38`) as f38 from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool))");
			$result[$field[14]] = $sqls->f13;
			$result[$field[15]] = $sqls->f14;
			$result[$field[17]] = $sqls->f24;
			$result[$field[18]] = $sqls->f25;
			$result[$field[20]] = $sqls->f26;
			$result[$field[21]] = $sqls->f27;
			$result[$field[22]] = $sqls->f28;
			$result[$field[23]] = $sqls->f29;
			$result[$field[24]] = $sqls->f30;
			$result[$field[25]] = $sqls->f31;
			$result[$field[32]] = $sqls->f37;
			$result[$field[33]] = $sqls->f38;

			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-08`>0");
			$result[$field[0]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-15`>0");
			$result[$field[1]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-17`>0");
			$result[$field[2]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-24`>0");
			$result[$field[17]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-26`>0");
			$result[$field[19]] = $sqls->f;

			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid8 where (listformid in ($listschool)) and `f-r8-01`>0");
			$result[$field[3]] = $sqls->f;
			$sqls = get_record_sql("select sum(`f-r8-01`) as f from {$CFG->prefix}monit_bkp_table_$rzdid8 where (listformid in ($listschool))");
			$result[$field[4]] = $sqls->f;

			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-21`='Да'");
			$result[$field[11]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-22`='Да'");
			$result[$field[12]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-23`='Да'");
			$result[$field[13]] = $sqls->f;

			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-33`='Да'");
			$result[$field[27]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-34`='Да'");
			$result[$field[28]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-35`='Да'");
			$result[$field[29]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-36`='Да'");
			$result[$field[30]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-36`='Нет'");
			$result[$field[31]] = $sqls->f;
			$sqls = get_record_sql("select count(id) as f from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-39`='Да'");
			$result[$field[34]] = $sqls->f;

			$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-18`='Да'");
			$result[$field[5]] = $sqls->c;
			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-18`='Да'");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid4");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r4-18-4`) as c1, sum(`f-r4-18-4`) as c2, sum(`f-r4-18-4`) as c3, sum(`f-r4-18-4`) as c4 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listform))");
						$result[$field[6]] = $sqls->c1 + $sqls->c2 + $sqls->c3 + $sqls->c4;
					}
				}
			}

			$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-19`='Да'");
			$result[$field[7]] = $sqls->c;

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-19`='Да'");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid4");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r4-18-4`) as c1, sum(`f-r4-18-4`) as c2, sum(`f-r4-18-4`) as c3, sum(`f-r4-18-4`) as c4 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listform))");
						$result[$field[8]] = $sqls->c1 + $sqls->c2 + $sqls->c3 + $sqls->c4;
					}
				}
			}

			$sqls = get_record_sql("select count(id) as c from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-20`='Да'");
			$result[$field[9]] = $sqls->c;

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid13 where (listformid in ($listschool)) and `f-r13-20`='Да'");
			if($sqls) {
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listform = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listform))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listschool1 = implode(',', $listarray);

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listschool1)) and shortname=$rzdid4");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r4-18-4`) as c1, sum(`f-r4-18-4`) as c2, sum(`f-r4-18-4`) as c3, sum(`f-r4-18-4`) as c4 from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listform))");
						$result[$field[10]] = $sqls->c1 + $sqls->c2 + $sqls->c3 + $sqls->c4;
					}
				}
			}
		}
	}

	$result[$field[26]] = $result[$field[27]] + $result[$field[28]] + $result[$field[29]] + $result[$field[30]] + $result[$field[31]];
	return $result;
}

function d9($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$form_id  = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-1'");
	$rzd_id3  = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=3");
	$rzd_id4  = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id6  = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=6");
	$rzd_id8  = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
	$rzd_id9  = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=9");
	$rzd_id13 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=13");

	foreach ($table_rzds as $table_rzd)  {
		switch ($table_rzd->shortname)	{
			case 1:
				unset($field);
				$field = explode(',', 'f-r1-01-3,f-r1-01-4,f-r1-01-5a,f-r1-01-5b,f-r1-01-6a,f-r1-01-6b,f-r1-01-7,f-r1-01-8,f-r1-01-9');
				$data = d9_1($rid, '58,59', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field);

				unset($field);
				$field = explode(',', 'f-r1-02-3,f-r1-02-4,f-r1-02-5a,f-r1-02-5b,f-r1-02-6a,f-r1-02-6b,f-r1-02-7,f-r1-02-8,f-r1-02-9');
				$data = array_merge($data, d9_1($rid, '0,59', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-03-3,f-r1-03-4,f-r1-03-5a,f-r1-03-5b,f-r1-03-6a,f-r1-03-6b,f-r1-03-7,f-r1-03-8,f-r1-03-9');
				$data = array_merge($data, d9_1($rid, '60,70', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-04-3,f-r1-04-4,f-r1-04-5a,f-r1-04-5b,f-r1-04-6a,f-r1-04-6b,f-r1-04-7,f-r1-04-8,f-r1-04-9');
				$data = array_merge($data, d9_1($rid, '61,71', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-05-3,f-r1-05-4,f-r1-05-5a,f-r1-05-5b,f-r1-05-6a,f-r1-05-6b,f-r1-05-7,f-r1-05-8,f-r1-05-9');
				$data = array_merge($data, d9_1($rid, '62,72', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-06-3,f-r1-06-4,f-r1-06-5a,f-r1-06-5b,f-r1-06-6a,f-r1-06-6b,f-r1-06-7,f-r1-06-8,f-r1-06-9');
				$data = array_merge($data, d9_1($rid, '63,73', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-07-3,f-r1-07-4,f-r1-07-5a,f-r1-07-5b,f-r1-07-6a,f-r1-07-6b,f-r1-07-7,f-r1-07-8,f-r1-07-9');
				$data = array_merge($data, d9_1($rid, '64,74', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-08-3,f-r1-08-4,f-r1-08-5a,f-r1-08-5b,f-r1-08-6a,f-r1-08-6b,f-r1-08-7,f-r1-08-8,f-r1-08-9');
				$data = array_merge($data, d9_1($rid, '65,75', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-09-3,f-r1-09-4,f-r1-09-5a,f-r1-09-5b,f-r1-09-6a,f-r1-09-6b,f-r1-09-7,f-r1-09-8,f-r1-09-9');
				$data = array_merge($data, d9_1($rid, '66,76', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-10-3,f-r1-10-4,f-r1-10-5a,f-r1-10-5b,f-r1-10-6a,f-r1-10-6b,f-r1-10-7,f-r1-10-8,f-r1-10-9');
				$data = array_merge($data, d9_1($rid, '67,77', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));


				$data['f-r1-11-3'] = $data['f-r1-01-3'] + $data['f-r1-03-3'] + $data['f-r1-04-3'] + $data['f-r1-05-3'] + $data['f-r1-06-3'] + $data['f-r1-07-3'] + $data['f-r1-08-3'] + $data['f-r1-09-3'] + $data['f-r1-10-3'];
				$data['f-r1-11-4'] = $data['f-r1-01-4'] + $data['f-r1-03-4'] + $data['f-r1-04-4'] + $data['f-r1-05-4'] + $data['f-r1-06-4'] + $data['f-r1-07-4'] + $data['f-r1-08-4'] + $data['f-r1-09-4'] + $data['f-r1-10-4'];
				$data['f-r1-11-5a'] = $data['f-r1-01-5a'] + $data['f-r1-03-5a'] + $data['f-r1-04-5a'] + $data['f-r1-05-5a'] + $data['f-r1-06-5a'] + $data['f-r1-07-5a'] + $data['f-r1-08-5a'] + $data['f-r1-09-5a'] + $data['f-r1-10-5a'];
				$data['f-r1-11-5b'] = $data['f-r1-01-5b'] + $data['f-r1-03-5b'] + $data['f-r1-04-5b'] + $data['f-r1-05-5b'] + $data['f-r1-06-5b'] + $data['f-r1-07-5b'] + $data['f-r1-08-5b'] + $data['f-r1-09-5b'] + $data['f-r1-10-5b'];
				$data['f-r1-11-6a'] = $data['f-r1-01-6a'] + $data['f-r1-03-6a'] + $data['f-r1-04-6a'] + $data['f-r1-05-6a'] + $data['f-r1-06-6a'] + $data['f-r1-07-6a'] + $data['f-r1-08-6a'] + $data['f-r1-09-6a'] + $data['f-r1-10-6a'];
				$data['f-r1-11-6b'] = $data['f-r1-01-6b'] + $data['f-r1-03-6b'] + $data['f-r1-04-6b'] + $data['f-r1-05-6b'] + $data['f-r1-06-6b'] + $data['f-r1-07-6b'] + $data['f-r1-08-6b'] + $data['f-r1-09-6b'] + $data['f-r1-10-6b'];
				$data['f-r1-11-7'] = $data['f-r1-01-7'] + $data['f-r1-03-7'] + $data['f-r1-04-7'] + $data['f-r1-05-7'] + $data['f-r1-06-7'] + $data['f-r1-07-7'] + $data['f-r1-08-7'] + $data['f-r1-09-7'] + $data['f-r1-10-7'];
				$data['f-r1-11-8'] = $data['f-r1-01-8'] + $data['f-r1-03-8'] + $data['f-r1-04-8'] + $data['f-r1-05-8'] + $data['f-r1-06-8'] + $data['f-r1-07-8'] + $data['f-r1-08-8'] + $data['f-r1-09-8'] + $data['f-r1-10-8'];
				$data['f-r1-11-9'] = $data['f-r1-01-9'] + $data['f-r1-03-9'] + $data['f-r1-04-9'] + $data['f-r1-05-9'] + $data['f-r1-06-9'] + $data['f-r1-07-9'] + $data['f-r1-08-9'] + $data['f-r1-09-9'] + $data['f-r1-10-9'];


				unset($field);
				$field = explode(',', 'f-r1-12-3,f-r1-12-4,f-r1-12-5a,f-r1-12-5b,f-r1-12-6a,f-r1-12-6b,f-r1-12-7,f-r1-12-8,f-r1-12-9');
				$data = array_merge($data, d9_12($rid, '58,60,61,62,63,64,65,66,67','59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-13-3,f-r1-13-4,f-r1-13-5a,f-r1-13-5b,f-r1-13-6a,f-r1-13-6b,f-r1-13-7,f-r1-13-8,f-r1-13-9');
				$data = array_merge($data, d9_13($rid, '59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-14-3,f-r1-14-4,f-r1-14-5a,f-r1-14-5b,f-r1-14-6a,f-r1-14-6b,f-r1-14-7,f-r1-14-8,f-r1-14-9');
				$data = array_merge($data, d9_14($rid, '59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-15-4,f-r1-15-7');
				$data = array_merge($data, d9_15($rid, '58,60,61,62,63,64,65,66,67,59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id4->id, $rzd_id6->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-16-4,f-r1-16-7');
				$data = array_merge($data, d9_15($rid, '58,60,61,62,63,64,65,66,67,59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id4->id, $rzd_id6->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-17-4,f-r1-17-7,f-r4-18-8');
				$data = array_merge($data, d9_17($rid, '58,60,61,62,63,64,65,66,67,59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id4->id, $rzd_id6->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-18-4,f-r1-18-7,f-r4-18-10');
				$data = array_merge($data, d9_17($rid, '58,60,61,62,63,64,65,66,67,59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id4->id, $rzd_id6->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-19-4,f-r1-19-7,f-r4-18-12');
				$data = array_merge($data, d9_17($rid, '58,60,61,62,63,64,65,66,67,59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id3->id, $rzd_id4->id, $rzd_id6->id, $field));

				unset($field);
				$field = explode(',', 'f-r1-20-3,f-r1-20-4,f-r1-20-5a,f-r1-20-5b,f-r1-20-6a,f-r1-20-6b,f-r1-20-7,f-r1-20-8,f-r1-20-9');
				$data = array_merge($data, d9_20($rid, '31,78', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-21-3,f-r1-21-4,f-r1-21-5a,f-r1-21-5b,f-r1-21-6a,f-r1-21-6b,f-r1-21-7,f-r1-21-8,f-r1-21-9');
				$data = array_merge($data, d9_20($rid, '78', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-22-3,f-r1-22-4,f-r1-22-5a,f-r1-22-5b,f-r1-22-6a,f-r1-22-6b,f-r1-22-7,f-r1-22-8,f-r1-22-9');
				$data = array_merge($data, d9_20($rid, '68,79,80', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-23-3,f-r1-23-4,f-r1-23-5a,f-r1-23-5b,f-r1-23-6a,f-r1-23-6b,f-r1-23-7,f-r1-23-8,f-r1-23-9');
				$data = array_merge($data, d9_20($rid, '79', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-24-3,f-r1-24-4,f-r1-24-5a,f-r1-24-5b,f-r1-24-6a,f-r1-24-6b,f-r1-24-7,f-r1-24-8,f-r1-24-9');
				$data = array_merge($data, d9_20($rid, '80', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-25-3,f-r1-25-4,f-r1-25-5a,f-r1-25-5b,f-r1-25-6a,f-r1-25-6b,f-r1-25-7,f-r1-25-8,f-r1-25-9');
				$data = array_merge($data, d9_20($rid, '69', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-26-3,f-r1-26-4,f-r1-26-5a,f-r1-26-5b,f-r1-26-6a,f-r1-26-6b,f-r1-26-7,f-r1-26-8,f-r1-26-9');
				$data = array_merge($data, d9_20($rid, '69', $rzd_id3->id, $rzd_id6->id, $rzd_id9->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-27-10,f-r1-27-11,f-r1-27-12,f-r1-27-13,f-r1-27-14,f-r1-27-15,f-r1-27-16,f-r1-27-17,f-r1-27-18,f-r1-27-19,f-r1-27-20,f-r1-27-21,f-r1-27-22,f-r1-27-23,f-r1-27-24,f-r1-27-25,f-r1-27-26,f-r1-27-27,f-r1-27-28,f-r1-27-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '58','59', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-28-10,f-r1-28-11,f-r1-28-12,f-r1-28-13,f-r1-28-14,f-r1-28-15,f-r1-28-16,f-r1-28-17,f-r1-28-18,f-r1-28-19,f-r1-28-20,f-r1-28-21,f-r1-28-22,f-r1-28-23,f-r1-28-24,f-r1-28-25,f-r1-28-26,f-r1-28-27,f-r1-28-28,f-r1-28-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '0','59', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-29-10,f-r1-29-11,f-r1-29-12,f-r1-29-13,f-r1-29-14,f-r1-29-15,f-r1-29-16,f-r1-29-17,f-r1-29-18,f-r1-29-19,f-r1-29-20,f-r1-29-21,f-r1-29-22,f-r1-29-23,f-r1-29-24,f-r1-29-25,f-r1-29-26,f-r1-29-27,f-r1-29-28,f-r1-29-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '60','70', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-30-10,f-r1-30-11,f-r1-30-12,f-r1-30-13,f-r1-30-14,f-r1-30-15,f-r1-30-16,f-r1-30-17,f-r1-30-18,f-r1-30-19,f-r1-30-20,f-r1-30-21,f-r1-30-22,f-r1-30-23,f-r1-30-24,f-r1-30-25,f-r1-30-26,f-r1-30-27,f-r1-30-28,f-r1-30-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '61','71', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-31-10,f-r1-31-11,f-r1-31-12,f-r1-31-13,f-r1-31-14,f-r1-31-15,f-r1-31-16,f-r1-31-17,f-r1-31-18,f-r1-31-19,f-r1-31-20,f-r1-31-21,f-r1-31-22,f-r1-31-23,f-r1-31-24,f-r1-31-25,f-r1-31-26,f-r1-31-27,f-r1-31-28,f-r1-31-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '62','72', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-32-10,f-r1-32-11,f-r1-32-12,f-r1-32-13,f-r1-32-14,f-r1-32-15,f-r1-32-16,f-r1-32-17,f-r1-32-18,f-r1-32-19,f-r1-32-20,f-r1-32-21,f-r1-32-22,f-r1-32-23,f-r1-32-24,f-r1-32-25,f-r1-32-26,f-r1-32-27,f-r1-32-28,f-r1-32-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '63','73', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-33-10,f-r1-33-11,f-r1-33-12,f-r1-33-13,f-r1-33-14,f-r1-33-15,f-r1-33-16,f-r1-33-17,f-r1-33-18,f-r1-33-19,f-r1-33-20,f-r1-33-21,f-r1-33-22,f-r1-33-23,f-r1-33-24,f-r1-33-25,f-r1-33-26,f-r1-33-27,f-r1-33-28,f-r1-33-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '64','74', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-34-10,f-r1-34-11,f-r1-34-12,f-r1-34-13,f-r1-34-14,f-r1-34-15,f-r1-34-16,f-r1-34-17,f-r1-34-18,f-r1-34-19,f-r1-34-20,f-r1-34-21,f-r1-34-22,f-r1-34-23,f-r1-34-24,f-r1-34-25,f-r1-34-26,f-r1-34-27,f-r1-34-28,f-r1-34-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '65','75', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-35-10,f-r1-35-11,f-r1-35-12,f-r1-35-13,f-r1-35-14,f-r1-35-15,f-r1-35-16,f-r1-35-17,f-r1-35-18,f-r1-35-19,f-r1-35-20,f-r1-35-21,f-r1-35-22,f-r1-35-23,f-r1-35-24,f-r1-35-25,f-r1-35-26,f-r1-35-27,f-r1-35-28,f-r1-35-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '66','76', $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-36-10,f-r1-36-11,f-r1-36-12,f-r1-36-13,f-r1-36-14,f-r1-36-15,f-r1-36-16,f-r1-36-17,f-r1-36-18,f-r1-36-19,f-r1-36-20,f-r1-36-21,f-r1-36-22,f-r1-36-23,f-r1-36-24,f-r1-36-25,f-r1-36-26,f-r1-36-27,f-r1-36-28,f-r1-36-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '67','77', $rzd_id4->id, $field, -1));

				$data['f-r1-37-10'] = $data['f-r1-27-10'] + $data['f-r1-29-10'] + $data['f-r1-30-10'] + $data['f-r1-31-10'] + $data['f-r1-32-10'] + $data['f-r1-33-10'] + $data['f-r1-34-10'] + $data['f-r1-35-10'] + $data['f-r1-36-10'];
				$data['f-r1-37-11'] = $data['f-r1-27-11'] + $data['f-r1-29-11'] + $data['f-r1-30-11'] + $data['f-r1-31-11'] + $data['f-r1-32-11'] + $data['f-r1-33-11'] + $data['f-r1-34-11'] + $data['f-r1-35-11'] + $data['f-r1-36-11'];
				$data['f-r1-37-12'] = $data['f-r1-27-12'] + $data['f-r1-29-12'] + $data['f-r1-30-12'] + $data['f-r1-31-12'] + $data['f-r1-32-12'] + $data['f-r1-33-12'] + $data['f-r1-34-12'] + $data['f-r1-35-12'] + $data['f-r1-36-12'];
				$data['f-r1-37-13'] = $data['f-r1-27-13'] + $data['f-r1-29-13'] + $data['f-r1-30-13'] + $data['f-r1-31-13'] + $data['f-r1-32-13'] + $data['f-r1-33-13'] + $data['f-r1-34-13'] + $data['f-r1-35-13'] + $data['f-r1-36-13'];
				$data['f-r1-37-14'] = $data['f-r1-27-14'] + $data['f-r1-29-14'] + $data['f-r1-30-14'] + $data['f-r1-31-14'] + $data['f-r1-32-14'] + $data['f-r1-33-14'] + $data['f-r1-34-14'] + $data['f-r1-35-14'] + $data['f-r1-36-14'];
				$data['f-r1-37-15'] = $data['f-r1-27-15'] + $data['f-r1-29-15'] + $data['f-r1-30-15'] + $data['f-r1-31-15'] + $data['f-r1-32-15'] + $data['f-r1-33-15'] + $data['f-r1-34-15'] + $data['f-r1-35-15'] + $data['f-r1-36-15'];
				$data['f-r1-37-16'] = $data['f-r1-27-16'] + $data['f-r1-29-16'] + $data['f-r1-30-16'] + $data['f-r1-31-16'] + $data['f-r1-32-16'] + $data['f-r1-33-16'] + $data['f-r1-34-16'] + $data['f-r1-35-16'] + $data['f-r1-36-16'];
				$data['f-r1-37-17'] = $data['f-r1-27-17'] + $data['f-r1-29-17'] + $data['f-r1-30-17'] + $data['f-r1-31-17'] + $data['f-r1-32-17'] + $data['f-r1-33-17'] + $data['f-r1-34-17'] + $data['f-r1-35-17'] + $data['f-r1-36-17'];
				$data['f-r1-37-18'] = $data['f-r1-27-18'] + $data['f-r1-29-18'] + $data['f-r1-30-18'] + $data['f-r1-31-18'] + $data['f-r1-32-18'] + $data['f-r1-33-18'] + $data['f-r1-34-18'] + $data['f-r1-35-18'] + $data['f-r1-36-18'];
				$data['f-r1-37-19'] = $data['f-r1-27-19'] + $data['f-r1-29-19'] + $data['f-r1-30-19'] + $data['f-r1-31-19'] + $data['f-r1-32-19'] + $data['f-r1-33-19'] + $data['f-r1-34-19'] + $data['f-r1-35-19'] + $data['f-r1-36-19'];
				$data['f-r1-37-20'] = $data['f-r1-27-20'] + $data['f-r1-29-20'] + $data['f-r1-30-20'] + $data['f-r1-31-20'] + $data['f-r1-32-20'] + $data['f-r1-33-20'] + $data['f-r1-34-20'] + $data['f-r1-35-20'] + $data['f-r1-36-20'];
				$data['f-r1-37-21'] = $data['f-r1-27-21'] + $data['f-r1-29-21'] + $data['f-r1-30-21'] + $data['f-r1-31-21'] + $data['f-r1-32-21'] + $data['f-r1-33-21'] + $data['f-r1-34-21'] + $data['f-r1-35-21'] + $data['f-r1-36-21'];
				$data['f-r1-37-22'] = $data['f-r1-27-22'] + $data['f-r1-29-22'] + $data['f-r1-30-22'] + $data['f-r1-31-22'] + $data['f-r1-32-22'] + $data['f-r1-33-22'] + $data['f-r1-34-22'] + $data['f-r1-35-22'] + $data['f-r1-36-22'];
				$data['f-r1-37-23'] = $data['f-r1-27-23'] + $data['f-r1-29-23'] + $data['f-r1-30-23'] + $data['f-r1-31-23'] + $data['f-r1-32-23'] + $data['f-r1-33-23'] + $data['f-r1-34-23'] + $data['f-r1-35-23'] + $data['f-r1-36-23'];
				$data['f-r1-37-24'] = $data['f-r1-27-24'] + $data['f-r1-29-24'] + $data['f-r1-30-24'] + $data['f-r1-31-24'] + $data['f-r1-32-24'] + $data['f-r1-33-24'] + $data['f-r1-34-24'] + $data['f-r1-35-24'] + $data['f-r1-36-24'];
				$data['f-r1-37-25'] = $data['f-r1-27-25'] + $data['f-r1-29-25'] + $data['f-r1-30-25'] + $data['f-r1-31-25'] + $data['f-r1-32-25'] + $data['f-r1-33-25'] + $data['f-r1-34-25'] + $data['f-r1-35-25'] + $data['f-r1-36-25'];
				$data['f-r1-37-26'] = $data['f-r1-27-26'] + $data['f-r1-29-26'] + $data['f-r1-30-26'] + $data['f-r1-31-26'] + $data['f-r1-32-26'] + $data['f-r1-33-26'] + $data['f-r1-34-26'] + $data['f-r1-35-26'] + $data['f-r1-36-26'];
				$data['f-r1-37-27'] = $data['f-r1-27-27'] + $data['f-r1-29-27'] + $data['f-r1-30-27'] + $data['f-r1-31-27'] + $data['f-r1-32-27'] + $data['f-r1-33-27'] + $data['f-r1-34-27'] + $data['f-r1-35-27'] + $data['f-r1-36-27'];
				$data['f-r1-37-28'] = $data['f-r1-27-28'] + $data['f-r1-29-28'] + $data['f-r1-30-28'] + $data['f-r1-31-28'] + $data['f-r1-32-28'] + $data['f-r1-33-28'] + $data['f-r1-34-28'] + $data['f-r1-35-28'] + $data['f-r1-36-28'];
				$data['f-r1-37-29'] = $data['f-r1-27-29'] + $data['f-r1-29-29'] + $data['f-r1-30-29'] + $data['f-r1-31-29'] + $data['f-r1-32-29'] + $data['f-r1-33-29'] + $data['f-r1-34-29'] + $data['f-r1-35-29'] + $data['f-r1-36-29'];

				unset($field);
				$field = explode(',', 'f-r1-38-10,f-r1-38-11,f-r1-38-12,f-r1-38-13,f-r1-38-14,f-r1-38-15,f-r1-38-16,f-r1-38-17,f-r1-38-18,f-r1-38-19,f-r1-38-20,f-r1-38-21,f-r1-38-22,f-r1-38-23,f-r1-38-24,f-r1-38-25,f-r1-38-26,f-r1-38-27,f-r1-38-28,f-r1-38-29,f-r4-01-4,f-r4-02-4,f-r4-03-4,f-r4-04-4,f-r4-05-4,f-r4-06-4,f-r4-07-4,f-r4-08-4,f-r4-09-4,f-r4-10-4,f-r4-11-4,f-r4-12-4,f-r4-13-4,f-r4-14-4,f-r4-15-4,f-r4-16-4,f-r4-17-4,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '58,60,61,62,63,64,65,66,67', '59,70,71,72,73,74,75,76,77,68,79,80', $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-39-10,f-r1-39-11,f-r1-39-12,f-r1-39-13,f-r1-39-14,f-r1-39-15,f-r1-39-16,f-r1-39-17,f-r1-39-18,f-r1-39-19,f-r1-39-20,f-r1-39-21,f-r1-39-22,f-r1-39-23,f-r1-39-24,f-r1-39-25,f-r1-39-26,f-r1-39-27,f-r1-39-28,f-r1-39-29,f-r4-01-5,f-r4-02-5,f-r4-03-5,f-r4-04-5,f-r4-05-5,f-r4-06-5,f-r4-07-5,f-r4-08-5,f-r4-09-5,f-r4-10-5,f-r4-11-5,f-r4-12-5,f-r4-13-5,f-r4-14-5,f-r4-15-5,f-r4-16-5,f-r4-17-5,f-r4-01-9,f-r4-02-9,f-r4-03-9,f-r4-04-9,f-r4-05-9,f-r4-06-9,f-r4-07-9,f-r4-08-9,f-r4-09-9,f-r4-10-9,f-r4-11-9,f-r4-12-9,f-r4-13-9,f-r4-14-9,f-r4-15-9,f-r4-16-9,f-r4-17-9,f-r4-01-11,f-r4-02-11,f-r4-03-11,f-r4-04-11,f-r4-05-11,f-r4-06-11,f-r4-07-11,f-r4-08-11,f-r4-09-11,f-r4-10-11,f-r4-11-11,f-r4-12-11,f-r4-13-11,f-r4-14-11,f-r4-15-11,f-r4-16-11,f-r4-17-11,f-r4-01-13,f-r4-02-13,f-r4-03-13,f-r4-04-13,f-r4-05-13,f-r4-06-13,f-r4-07-13,f-r4-08-13,f-r4-09-13,f-r4-10-13,f-r4-11-13,f-r4-12-13,f-r4-13-13,f-r4-14-13,f-r4-15-13,f-r4-16-13,f-r4-17-13');
				$data = array_merge($data, d9_27($rid, '58,60,61,62,63,64,65,66,67', '59,70,71,72,73,74,75,76,77', $rzd_id4->id, $field, -1));

				$data['f-r1-39-10'] = $data['f-r1-37-10'] - $data['f-r1-39-10'];
				$data['f-r1-39-11'] = $data['f-r1-37-11'] - $data['f-r1-39-11'];
				$data['f-r1-39-12'] = $data['f-r1-37-12'] - $data['f-r1-39-12'];
				$data['f-r1-39-13'] = $data['f-r1-37-13'] - $data['f-r1-39-13'];
				$data['f-r1-39-14'] = $data['f-r1-37-14'] - $data['f-r1-39-14'];
				$data['f-r1-39-15'] = $data['f-r1-37-15'] - $data['f-r1-39-15'];
				$data['f-r1-39-16'] = $data['f-r1-37-16'] - $data['f-r1-39-16'];
				$data['f-r1-39-17'] = $data['f-r1-37-17'] - $data['f-r1-39-17'];
				$data['f-r1-39-18'] = $data['f-r1-37-18'] - $data['f-r1-39-18'];
				$data['f-r1-39-19'] = $data['f-r1-37-19'] - $data['f-r1-39-19'];
				$data['f-r1-39-20'] = $data['f-r1-37-20'] - $data['f-r1-39-20'];
				$data['f-r1-39-21'] = $data['f-r1-37-21'] - $data['f-r1-39-21'];
				$data['f-r1-39-22'] = $data['f-r1-37-22'] - $data['f-r1-39-22'];
				$data['f-r1-39-23'] = $data['f-r1-37-23'] - $data['f-r1-39-23'];
				$data['f-r1-39-24'] = $data['f-r1-37-24'] - $data['f-r1-39-24'];
				$data['f-r1-39-25'] = $data['f-r1-37-25'] - $data['f-r1-39-25'];
				$data['f-r1-39-26'] = $data['f-r1-37-26'] - $data['f-r1-39-26'];
				$data['f-r1-39-27'] = $data['f-r1-37-27'] - $data['f-r1-39-27'];
				$data['f-r1-39-28'] = $data['f-r1-37-28'] - $data['f-r1-39-28'];
				$data['f-r1-39-29'] = $data['f-r1-37-29'] - $data['f-r1-39-29'];

				unset($field);
				$field = explode(',', 'f-r1-40-10,f-r1-40-12,f-r1-40-13,f-r1-40-14,f-r1-40-15,f-r1-40-16,f-r1-40-17,f-r1-40-18,f-r1-40-19,f-r1-40-20,f-r1-40-21,f-r1-40-22,f-r1-40-23,f-r1-40-24,f-r1-40-25,f-r1-40-26');
				$data = array_merge($data, d9_40($rid, '11,12,13,18,27,28,32,40,59,70,71,72,73,74,75,76,77,68,79,80', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-41-10,f-r1-41-12,f-r1-41-13,f-r1-41-14,f-r1-41-15,f-r1-41-16,f-r1-41-17,f-r1-41-18,f-r1-41-19,f-r1-41-20,f-r1-41-21,f-r1-41-22,f-r1-41-23,f-r1-41-24,f-r1-41-25,f-r1-41-26');
				$data = array_merge($data, d9_40($rid, '11,12,13,18,27,28,32,40,59,70,71,72,73,74,75,76,77,68,79,80', 0, $rzd_id4->id, $field, 1));

				unset($field);
				$field = explode(',', 'f-r1-42-10,f-r1-42-12,f-r1-42-13,f-r1-42-14,f-r1-42-15,f-r1-42-16,f-r1-42-17,f-r1-42-18,f-r1-42-19,f-r1-42-20,f-r1-42-21,f-r1-42-22,f-r1-42-23,f-r1-42-24,f-r1-42-25,f-r1-42-26');
				$data = array_merge($data, d9_40($rid, '11,12,13,18,27,28,32,40,59,70,71,72,73,74,75,76,77,68,79,80', 1, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-43-10,f-r1-43-12,f-r1-43-13,f-r1-43-14,f-r1-43-15,f-r1-43-16,f-r1-43-17,f-r1-43-18,f-r1-43-19,f-r1-43-20,f-r1-43-21,f-r1-43-22,f-r1-43-23,f-r1-43-24,f-r1-43-25,f-r1-43-26');
				$data = array_merge($data, d9_40($rid, '11,12,13,18,27,28,32,40,59,70,71,72,73,74,75,76,77,68,79,80', 2, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-44-10,f-r1-44-12,f-r1-44-13,f-r1-44-14,f-r1-44-15,f-r1-44-16,f-r1-44-17,f-r1-44-18,f-r1-44-19,f-r1-44-20,f-r1-44-21,f-r1-44-22,f-r1-44-23,f-r1-44-24,f-r1-44-25,f-r1-44-26');
				$data = array_merge($data, d9_40($rid, '11,12,13,18,27,28,32,40,59,70,71,72,73,74,75,76,77,68,79,80', 3, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-45-10,f-r1-45-12,f-r1-45-13,f-r1-45-14,f-r1-45-15,f-r1-45-16,f-r1-45-17,f-r1-45-18,f-r1-45-19,f-r1-45-20,f-r1-45-21,f-r1-45-22,f-r1-45-23,f-r1-45-24,f-r1-45-25,f-r1-45-26');
				$data = array_merge($data, d9_40($rid, '31,78', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-46-10,f-r1-46-12,f-r1-46-13,f-r1-46-14,f-r1-46-15,f-r1-46-16,f-r1-46-17,f-r1-46-18,f-r1-46-19,f-r1-46-20,f-r1-46-21,f-r1-46-22,f-r1-46-23,f-r1-46-24,f-r1-46-25,f-r1-46-26');
				$data = array_merge($data, d9_40($rid, '78', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-47-10,f-r1-47-12,f-r1-47-13,f-r1-47-14,f-r1-47-15,f-r1-47-16,f-r1-47-17,f-r1-47-18,f-r1-47-19,f-r1-47-20,f-r1-47-21,f-r1-47-22,f-r1-47-23,f-r1-47-24,f-r1-47-25,f-r1-47-26');
				$data = array_merge($data, d9_40($rid, '68,79,80', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-48-10,f-r1-48-12,f-r1-48-13,f-r1-48-14,f-r1-48-15,f-r1-48-16,f-r1-48-17,f-r1-48-18,f-r1-48-19,f-r1-48-20,f-r1-48-21,f-r1-48-22,f-r1-48-23,f-r1-48-24,f-r1-48-25,f-r1-48-26');
				$data = array_merge($data, d9_40($rid, '79', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-49-10,f-r1-49-12,f-r1-49-13,f-r1-49-14,f-r1-49-15,f-r1-49-16,f-r1-49-17,f-r1-49-18,f-r1-49-19,f-r1-49-20,f-r1-49-21,f-r1-49-22,f-r1-49-23,f-r1-49-24,f-r1-49-25,f-r1-49-26');
				$data = array_merge($data, d9_40($rid, '80', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-50-10,f-r1-50-12,f-r1-50-13,f-r1-50-14,f-r1-50-15,f-r1-50-16,f-r1-50-17,f-r1-50-18,f-r1-50-19,f-r1-50-20,f-r1-50-21,f-r1-50-22,f-r1-50-23,f-r1-50-24,f-r1-50-25,f-r1-50-26');
				$data = array_merge($data, d9_40($rid, '69', 0, $rzd_id4->id, $field, -1));

				unset($field);
				$field = explode(',', 'f-r1-51-10,f-r1-51-12,f-r1-51-13,f-r1-51-14,f-r1-51-15,f-r1-51-16,f-r1-51-17,f-r1-51-18,f-r1-51-19,f-r1-51-20,f-r1-51-21,f-r1-51-22,f-r1-51-23,f-r1-51-24,f-r1-51-25,f-r1-51-26');
				$data = array_merge($data, d9_40($rid, '69', 0, $rzd_id4->id, $field, 1));

			break;
			case 2:
				unset($field);
				$field = explode(',', 'f-r2-01-3,f-r2-02-3,f-r2-03-3,f-r2-04-3,f-r2-05-3,f-r2-06-3,f-r2-07-3,f-r2-08-3,f-r2-09-3,f-r2-10-3,f-r2-11-3,f-r2-12-3,f-r2-13-3,f-r2-14-3,f-r2-15-3,f-r2-16-3,f-r2-17-3,f-r2-18-3,f-r2-19-3,f-r2-20-3,f-r2-21-3,f-r2-22-3,f-r2-23-3,f-r2-24-3,f-r2-25-3,f-r2-26-3,f-r2-27-3,f-r2-28-3,f-r2-29-3,f-r2-30-3,f-r2-31-3,f-r2-32-3,f-r2-33-3,f-r2-34-3,f-r2-35-3');
				$data = d9_2($rid, '58,60,61,62,63,64,65,66,67,', '59,70,71,72,73,74,75,76,77', $rzd_id4->id, $rzd_id8->id, $rzd_id13->id, $field);

				unset($field);
				$field = explode(',', 'f-r2-01-4,f-r2-02-4,f-r2-03-4,f-r2-04-4,f-r2-05-4,f-r2-06-4,f-r2-07-4,f-r2-08-4,f-r2-09-4,f-r2-10-4,f-r2-11-4,f-r2-12-4,f-r2-13-4,f-r2-14-4,f-r2-15-4,f-r2-16-4,f-r2-17-4,f-r2-18-4,f-r2-19-4,f-r2-20-4,f-r2-21-4,f-r2-22-4,f-r2-23-4,f-r2-24-4,f-r2-25-4,f-r2-26-4,f-r2-27-4,f-r2-28-4,f-r2-29-4,f-r2-30-4,f-r2-31-4,f-r2-32-4,f-r2-33-4,f-r2-34-4,f-r2-35-4');
				$data = d9_2($rid, '', '59,70,71,72,73,74,75,76,77', $rzd_id4->id, $rzd_id8->id, $rzd_id13->id, $field);
			break;
		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

function sv1_4($rid, $rzdid4, $field) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	for($i=0;$i<count($field);$i++) {
		$sum.= "sum(`$field[$i]`) as f$i,";
	}

	$sum = substr($sum,0,-1);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum from {$CFG->prefix}monit_bkp_table_$rzdid4 where (listformid in ($listschool))");

			$result[$field[0]]= $sqls->f0;
			$result[$field[1]]= $sqls->f1;
			$result[$field[2]]= $sqls->f2;
			$result[$field[3]]= $sqls->f3;
			$result[$field[4]]= $sqls->f4;
			$result[$field[5]]= $sqls->f5;
			$result[$field[6]]= $sqls->f6;
			$result[$field[7]]= $sqls->f7;
			$result[$field[8]]= $sqls->f8;
			$result[$field[9]]= $sqls->f9;
			$result[$field[10]]= $sqls->f10;
			$result[$field[11]]= $sqls->f11;
			$result[$field[12]]= $sqls->f12;
			$result[$field[13]]= $sqls->f13;
			$result[$field[14]]= $sqls->f14;
			$result[$field[15]]= $sqls->f15;
			$result[$field[16]]= $sqls->f16;
			$result[$field[17]]= $sqls->f17;
			$result[$field[18]]= $sqls->f18;
			$result[$field[19]]= $sqls->f19;
			$result[$field[20]]= $sqls->f20;
		}
	}
	return $result;
}

function sv1_6($rid, $rzdid5, $field, $field1) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field);$i++) {
		$result[$field[$i]] = 0;
	}

	for($i=0;$i<count($field1);$i++) {
		$sum.= "sum(`$field1[$i]`) as f$i,";
	}

	$sum = substr($sum,0,-1);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum from {$CFG->prefix}monit_bkp_table_$rzdid5 where (listformid in ($listschool))");

			$result[$field[0]]= $sqls->f0;
			$result[$field[1]]= $sqls->f1;
			$result[$field[2]]= $sqls->f2;
			$result[$field[3]]= $sqls->f3;
			$result[$field[4]]= $sqls->f4;
			$result[$field[5]]= $sqls->f5;
			$result[$field[6]]= $sqls->f6;
			$result[$field[7]]= $sqls->f7;
			$result[$field[8]]= $sqls->f8;
			$result[$field[9]]= $sqls->f9;
			$result[$field[10]]= $sqls->f10;
			$result[$field[11]]= $sqls->f11;
			$result[$field[12]]= $sqls->f12;
			$result[$field[13]]= $sqls->f13;
			$result[$field[14]]= $sqls->f14;
			$result[$field[15]]= $sqls->f15;
		}
	}
	return $result;
}

function sv1_9($rid, $rzdid9, $field, $field1) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($field1);$i++) {
		$result[$field1[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(`$field[0]`) as f from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool)) and `$field[0]`>0");
			$result[$field1[0]] = $sqls->f;
			$sqls = get_record_sql("select count(`$field[2]`) as f from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool)) and `$field[2]`>0");
			$result[$field1[3]] = $sqls->f;
			$sqls = get_record_sql("select count(`$field[4]`) as f from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool)) and `$field[4]`>0");
			$result[$field1[6]] = $sqls->f;


			$sqls = get_record_sql("select sum(`$field[0]`) as f, sum(`$field[1]`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			$result[$field1[1]] = $sqls->f;
			$result[$field1[2]] = $sqls->f1;
			$sqls = get_record_sql("select sum(`$field[2]`) as f, sum(`$field[3]`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			$result[$field1[4]] = $sqls->f;
			$result[$field1[5]] = $sqls->f1;
			$sqls = get_record_sql("select sum(`$field[4]`) as f, sum(`$field[5]`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid9 where (listformid in ($listschool))");
			$result[$field1[7]] = $sqls->f;
			$result[$field1[8]] = $sqls->f1;


			$result[$field1[9]] = $result[$field1[0]] + $result[$field1[3]] + $result[$field1[6]];
			$result[$field1[10]] = $result[$field1[1]] + $result[$field1[4]] + $result[$field1[7]];
			$result[$field1[11]] = $result[$field1[2]] + $result[$field1[5]] + $result[$field1[8]];
		}
	}
	return $result;
}

function sv1_8($rid, $rzdid8, $fieldoh, $fieldsv) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
		$result[$fieldsv[$i]] = 0;
	}

	for($i=0;$i<count($fieldoh);$i++) {
		$sum.= "sum(`$fieldoh[$i]`) as f$i,";
	}
	$sum = substr($sum,0,-1);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum from {$CFG->prefix}monit_bkp_table_$rzdid8 where (listformid in ($listschool))");

			$result[$fieldsv[0]]= $sqls->f0;
			$result[$fieldsv[1]]= $sqls->f1;
			$result[$fieldsv[2]]= $sqls->f2;
			$result[$fieldsv[3]]= $sqls->f3;
			$result[$fieldsv[4]]= $sqls->f4;
			$result[$fieldsv[5]]= $sqls->f5;
			$result[$fieldsv[6]]= $sqls->f6;
			$result[$fieldsv[7]]= $sqls->f7;
			$result[$fieldsv[8]]= $sqls->f8;
			$result[$fieldsv[9]]= $sqls->f9;
			$result[$fieldsv[10]]= $sqls->f10;
			$result[$fieldsv[11]]= $sqls->f11;
			$result[$fieldsv[12]]= $sqls->f12;
			$result[$fieldsv[13]]= $sqls->f13;
			$result[$fieldsv[14]]= $sqls->f14;
			$result[$fieldsv[15]]= $sqls->f15;
		}
	}
	return $result;
}

function sv1_7($rid, $rzdid7, $fieldoh, $fieldsv) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
		$result[$fieldsv[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select count(`$fieldoh[0]`) as f from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool)) and `$fieldoh[0]`>0");
			$result[$fieldsv[0]] = $sqls->f;

			$sqls = get_record_sql("select sum(`$fieldoh[0]`) as f, sum(`$fieldoh[1]`) as f1 from {$CFG->prefix}monit_bkp_table_$rzdid7 where (listformid in ($listschool))");
			$result[$fieldsv[1]] = $sqls->f;
			$result[$fieldsv[2]] = $sqls->f1;
		}
	}
	return $result;
}

function sv1_3($rid, $rzdid3, $fieldoh, $fieldsv) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
//print "$i=$fieldsv[$i]				$fieldoh[$i]<br>";
		$result[$fieldsv[$i]] = 0;
	}

	for($i=0;$i<count($fieldoh);$i++) {
		$sum.= "sum(`$fieldoh[$i]`) as f$i,";
	}
	$sum = substr($sum,0,-1);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum from {$CFG->prefix}monit_bkp_table_$rzdid3 where (listformid in ($listschool))");

			$result[$fieldsv[0]]= $sqls->f0;
			$result[$fieldsv[1]]= $sqls->f1;
			$result[$fieldsv[2]]= $sqls->f2;
			$result[$fieldsv[3]]= $sqls->f3;
			$result[$fieldsv[4]]= $sqls->f4;
			$result[$fieldsv[5]]= $sqls->f5;
			$result[$fieldsv[6]]= $sqls->f6;
			$result[$fieldsv[7]]= $sqls->f7;
			$result[$fieldsv[8]]= $sqls->f8;
			$result[$fieldsv[9]]= $sqls->f9;
			$result[$fieldsv[10]]= $sqls->f10;
			$result[$fieldsv[11]]= $sqls->f11;
			$result[$fieldsv[12]]= $sqls->f12;
			$result[$fieldsv[13]]= $sqls->f13;
			$result[$fieldsv[14]]= $sqls->f14;
			$result[$fieldsv[15]]= $sqls->f15;
			$result[$fieldsv[16]]= $sqls->f16;
			$result[$fieldsv[17]]= $sqls->f17;
			$result[$fieldsv[18]]= $sqls->f18;
			$result[$fieldsv[19]]= $sqls->f19;
			$result[$fieldsv[20]]= $sqls->f20;
			$result[$fieldsv[21]]= $sqls->f21;
			$result[$fieldsv[22]]= $sqls->f22;
			$result[$fieldsv[23]]= $sqls->f23;
			$result[$fieldsv[24]]= $sqls->f24;
			$result[$fieldsv[25]]= $sqls->f25;
			$result[$fieldsv[26]]= $sqls->f26;
			$result[$fieldsv[27]]= $sqls->f27;
			$result[$fieldsv[28]]= $sqls->f28;
			$result[$fieldsv[29]]= $sqls->f29;
			$result[$fieldsv[30]]= $sqls->f30;
			$result[$fieldsv[31]]= $sqls->f31;
			$result[$fieldsv[32]]= $sqls->f32;
			$result[$fieldsv[33]]= $sqls->f33;
			$result[$fieldsv[34]]= $sqls->f34;
			$result[$fieldsv[35]]= $sqls->f35;
			$result[$fieldsv[36]]= $sqls->f36;
			$result[$fieldsv[37]]= $sqls->f37;
			$result[$fieldsv[38]]= $sqls->f38;
			$result[$fieldsv[39]]= $sqls->f39;
			$result[$fieldsv[40]]= $sqls->f40;
			$result[$fieldsv[41]]= $sqls->f41;
			$result[$fieldsv[42]]= $sqls->f42;
			$result[$fieldsv[43]]= $sqls->f43;
			$result[$fieldsv[44]]= $sqls->f44;
			$result[$fieldsv[45]]= $sqls->f45;
			$result[$fieldsv[46]]= $sqls->f46;
			$result[$fieldsv[47]]= $sqls->f47;
			$result[$fieldsv[48]]= $sqls->f48;
			$result[$fieldsv[49]]= $sqls->f49;
			$result[$fieldsv[50]]= $sqls->f50;
			$result[$fieldsv[51]]= $sqls->f51;
			$result[$fieldsv[52]]= $sqls->f52;
			$result[$fieldsv[53]]= $sqls->f53;
			$result[$fieldsv[54]]= $sqls->f54;
			$result[$fieldsv[55]]= $sqls->f55;


			$result[$fieldsv[56]]=$result[$fieldsv[24]]+$result[$fieldsv[32]]+$result[$fieldsv[40]];
			$result[$fieldsv[57]]=$result[$fieldsv[25]]+$result[$fieldsv[33]]+$result[$fieldsv[41]];
			$result[$fieldsv[58]]=$result[$fieldsv[26]]+$result[$fieldsv[34]]+$result[$fieldsv[42]];
			$result[$fieldsv[59]]=$result[$fieldsv[27]]+$result[$fieldsv[35]]+$result[$fieldsv[43]];
			$result[$fieldsv[60]]=$result[$fieldsv[28]]+$result[$fieldsv[36]]+$result[$fieldsv[44]];
			$result[$fieldsv[61]]=$result[$fieldsv[29]]+$result[$fieldsv[37]]+$result[$fieldsv[45]];
			$result[$fieldsv[62]]=$result[$fieldsv[30]]+$result[$fieldsv[38]]+$result[$fieldsv[46]];
			$result[$fieldsv[63]]=$result[$fieldsv[31]]+$result[$fieldsv[39]]+$result[$fieldsv[47]];

			$result[$fieldsv[64]]=$result[$fieldsv[0]]+$result[$fieldsv[8]]+$result[$fieldsv[16]]+$result[$fieldsv[56]];
			$result[$fieldsv[65]]=$result[$fieldsv[1]]+$result[$fieldsv[9]]+$result[$fieldsv[17]]+$result[$fieldsv[57]];
			$result[$fieldsv[66]]=$result[$fieldsv[2]]+$result[$fieldsv[10]]+$result[$fieldsv[18]]+$result[$fieldsv[58]];
			$result[$fieldsv[67]]=$result[$fieldsv[3]]+$result[$fieldsv[11]]+$result[$fieldsv[19]]+$result[$fieldsv[59]];
			$result[$fieldsv[68]]=$result[$fieldsv[4]]+$result[$fieldsv[12]]+$result[$fieldsv[20]]+$result[$fieldsv[60]];
			$result[$fieldsv[69]]=$result[$fieldsv[5]]+$result[$fieldsv[13]]+$result[$fieldsv[21]]+$result[$fieldsv[61]];
			$result[$fieldsv[70]]=$result[$fieldsv[6]]+$result[$fieldsv[14]]+$result[$fieldsv[22]]+$result[$fieldsv[62]];
			$result[$fieldsv[71]]=$result[$fieldsv[7]]+$result[$fieldsv[15]]+$result[$fieldsv[23]]+$result[$fieldsv[63]];
		}
	}
	return $result;
}

function sv1_5($rid, $rzdid1, $fieldoh, $fieldsv) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
//print "$i=$fieldsv[$i]				$fieldoh[$i]<br>";
		$result[$fieldsv[$i]] = 0;
	}

	for($i=0;$i<count($fieldoh);$i++) {
		$sum.= "sum(`$fieldoh[$i]`) as f$i,";
	}
	$sum = substr($sum,0,-1);

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");
		if($sqls) {
			unset($lists);
		    foreach ($sqls as $sql)  {
		        $lists[] = $sql->id;
		    }
		    $listschool = implode(',', $lists);

			$sqls = get_record_sql("select $sum from {$CFG->prefix}monit_bkp_table_$rzdid1 where (listformid in ($listschool))");

//print "select $sum from {$CFG->prefix}monit_bkp_table_$rzdid0 where (listformid in ($listschool))";

			$result[$fieldsv[1]]= $sqls->f0;
			$result[$fieldsv[2]]= $sqls->f1;
			$result[$fieldsv[0]] = $result[$fieldsv[1]] + $result[$fieldsv[2]] + $result[$fieldsv[3]] + $result[$fieldsv[4]];
		}
	}
	return $result;
}

function sv1_2($rid, $rzdid0, $rzdid5, $fieldsv, $education) {
	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
		$result[$fieldsv[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in (7,10))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($listarray);
			foreach ($sqls as $sql)  {
				$listarray[] = $sql->id;
			}
			$listform = implode(',', $listarray);

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid0 where (listformid in ($listform) and `education`=$education)");
			if($sqls) {				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listschool1 = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listschool1))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listform = implode(',', $listarray);
					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))and(shortname=$rzdid5)");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform = implode(',', $listarray);

						$sqls = get_record_sql("select sum(`f-r5-01-3`) as c from {$CFG->prefix}monit_bkp_table_$rzdid5 where (listformid in ($listform))");
						$result[$fieldsv[0]] = $sqls->c;
					}
				}
			}
		}
	}
	return $result;
}

function sv1_1($rid, $rzdid0, $rzdid2, $rzdid5, $rzdid6, $fieldsv, $education, $stateinstitution) {	global $yid, $CFG, $datemodified;

	for($i=0;$i<count($fieldsv);$i++) {
		$result[$fieldsv[$i]] = 0;
	}

	$sqls = get_records_sql("select id from {$CFG->prefix}monit_school where (isclosing=0)and(yearid=$yid)and(rayonid=$rid)and(stateinstitution in ($stateinstitution))");

    if($sqls) {
    	unset($listarray);
		foreach ($sqls as $sql)  {
			$listarray[] = $sql->id;
		}

		$listform = implode(',', $listarray);

		$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))");

		if($sqls) {
			unset($listarray);
			foreach ($sqls as $sql)  {
				$listarray[] = $sql->id;
			}
			$listform = implode(',', $listarray);

			$sqls = get_records_sql("select listformid from {$CFG->prefix}monit_bkp_table_$rzdid0 where (listformid in ($listform)) and (education in ($education))");
//print
			if($sqls) {				$result[$fieldsv[0]] = count($sqls);
				unset($listarray);
				foreach ($sqls as $sql)  {
					$listarray[] = $sql->listformid;
				}
				$listschool1 = implode(',', $listarray);
				$sqls = get_records_sql("select schoolid from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(id in ($listschool1))");
				if($sqls) {
					unset($listarray);
					foreach ($sqls as $sql)  {
						$listarray[] = $sql->schoolid;
					}
					$listform = implode(',', $listarray);
					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))and(shortname=$rzdid5)");
					if($sqls) {
						unset($listarray);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform1 = implode(',', $listarray);

						$sqls = get_record_sql("select sum(`f-r5-01-3`) as c from {$CFG->prefix}monit_bkp_table_$rzdid5 where (listformid in ($listform1))");
						$result[$fieldsv[1]] = $sqls->c;
					}

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))and(shortname=$rzdid6)");
					if($sqls) {
						unset($listarray);
						unset($listform1);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform1 = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r6-03`) as c from {$CFG->prefix}monit_bkp_table_$rzdid6 where (listformid in ($listform1))");
						$result[$fieldsv[2]] = $sqls->c;
					}

					$sqls = get_records_sql("select id from {$CFG->prefix}monit_school_listforms where ($datemodified=datemodified)and(schoolid in ($listform))and(shortname=$rzdid2)");
//print_r($sqls);
					if($sqls) {
						unset($listarray);
						unset($listform1);
						foreach ($sqls as $sql)  {
							$listarray[] = $sql->id;
						}
						$listform1 = implode(',', $listarray);
						$sqls = get_record_sql("select sum(`f-r2-01`) as c, sum(`f-r2-02`) as cc from {$CFG->prefix}monit_bkp_table_$rzdid2 where (listformid in ($listform1))");
						$result[$fieldsv[3]] = $sqls->c;
						$result[$fieldsv[4]] = $sqls->cc;
					}
				}
			}
		}
	}

	return $result;
}

function sv1($table_rzds, $rid)
{
	global $yid, $CFG, $datemodified, $nm;

	$form_id = get_record_sql("select id from {$CFG->prefix}monit_form where fullname='osh-5'");
	$rzd_id0 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=0");
	$rzd_id1 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=1");
	$rzd_id2 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=2");
	$rzd_id3 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=3");
	$rzd_id4 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=4");
	$rzd_id5 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=5");
	$rzd_id6 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=6");
	$rzd_id7 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=7");
	$rzd_id8 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=8");
	$rzd_id9 = get_record_sql("select id from {$CFG->prefix}monit_razdel where formid=$form_id->id and shortname=9");

	foreach ($table_rzds as $table_rzd)  {
		switch ($table_rzd->shortname)	{
			case 0:
			break;
			case 1:
				unset($field);
				$fieldsv = explode(',', 'f-r1-02-3,f-r1-02-4,f-r1-02-5,f-r1-02-6,f-r1-02-7');
				$data = sv1_1($rid, $rzd_id0->id, $rzd_id2->id, $rzd_id5->id, $rzd_id6->id, $fieldsv, '1', '7,10');

				$fieldsv = explode(',', 'f-r1-03-3,f-r1-03-4,f-r1-03-5,f-r1-03-6,f-r1-03-7');
				$data = array_merge($data, sv1_1($rid, $rzd_id0->id, $rzd_id2->id, $rzd_id5->id, $rzd_id6->id, $fieldsv, '3', '7,10'));

				$fieldsv = explode(',', 'f-r1-04-3,f-r1-04-4,f-r1-04-5,f-r1-04-6,f-r1-04-7');
				$data = array_merge($data, sv1_1($rid, $rzd_id0->id, $rzd_id2->id, $rzd_id5->id, $rzd_id6->id, $fieldsv, '2', '7,10'));

				$fieldsv = explode(',', 'f-r1-06-3,f-r1-06-4,f-r1-06-5,f-r1-06-6,f-r1-06-7');
				$data = array_merge($data, sv1_1($rid, $rzd_id0->id, $rzd_id2->id, $rzd_id5->id, $rzd_id6->id, $fieldsv, '1,2,3', '7'));

				$fieldsv = explode(',', 'f-r1-07-3,f-r1-07-4,f-r1-07-5,f-r1-07-6,f-r1-07-7');
				$data = array_merge($data, sv1_1($rid, $rzd_id0->id, $rzd_id2->id, $rzd_id5->id, $rzd_id6->id, $fieldsv, '1,2,3', '10'));





				$data['f-r1-01-3'] = $data['f-r1-02-3'] + $data['f-r1-03-3'] + $data['f-r1-04-3'] + $data['f-r1-05-3'];
				$data['f-r1-01-4'] = $data['f-r1-02-4'] + $data['f-r1-03-4'] + $data['f-r1-04-4'] + $data['f-r1-05-4'];
				$data['f-r1-01-5'] = $data['f-r1-02-5'] + $data['f-r1-03-5'] + $data['f-r1-04-5'] + $data['f-r1-05-5'];
				$data['f-r1-01-6'] = $data['f-r1-02-6'] + $data['f-r1-03-6'] + $data['f-r1-04-6'] + $data['f-r1-05-6'];
				$data['f-r1-01-7'] = $data['f-r1-02-7'] + $data['f-r1-03-7'] + $data['f-r1-04-7'] + $data['f-r1-05-7'];
			break;
			case 2:
				unset($field);
//				$fieldoh =  explode(',', 'education');
				$fieldsv = explode(',', 'f-r2-01-3');
				$data = sv1_2($rid, $rzd_id0->id, $rzd_id5->id, $fieldsv, 1);
				$fieldsv = explode(',', 'f-r2-02-3');
				$data = array_merge($data, sv1_2($rid, $rzd_id0->id, $rzd_id5->id, $fieldsv, 2));
				$fieldsv = explode(',', 'f-r2-03-3');
				$data = array_merge($data, sv1_2($rid, $rzd_id0->id, $rzd_id5->id, $fieldsv, 3));

			break;
			case 3:
				unset($field);
				$fieldoh =  explode(',', 'f-r3-01-3,f-r3-01-4,f-r3-01-5,f-r3-01-6,f-r3-01-7,f-r3-01-8,f-r3-01-9,f-r3-01-10,f-r3-02-3,f-r3-02-4,f-r3-02-5,f-r3-02-6,f-r3-02-7,f-r3-02-8,f-r3-02-9,f-r3-02-10,f-r3-03-3,f-r3-03-4,f-r3-03-5,f-r3-03-6,f-r3-03-7,f-r3-03-8,f-r3-03-9,f-r3-03-10,f-r3-04-3,f-r3-04-4,f-r3-04-5,f-r3-04-6,f-r3-04-7,f-r3-04-8,f-r3-04-9,f-r3-04-10,f-r3-05-3,f-r3-05-4,f-r3-05-5,f-r3-05-6,f-r3-05-7,f-r3-05-8,f-r3-05-9,f-r3-05-10,f-r3-06-3,f-r3-06-4,f-r3-06-5,f-r3-06-6,f-r3-06-8,f-r3-06-8,f-r3-06-9,f-r3-06-10,f-r3-08,f-r3-09,f-r3-10,f-r3-11,f-r3-12,f-r3-13,f-r3-14,f-r3-15');
				$fieldsv = explode(',', 'f-r3-01-3,f-r3-01-4,f-r3-01-5,f-r3-01-6,f-r3-01-7,f-r3-01-8,f-r3-01-9,f-r3-01-10,f-r3-02-3,f-r3-02-4,f-r3-02-5,f-r3-02-6,f-r3-02-7,f-r3-02-8,f-r3-02-9,f-r3-02-10,f-r3-03-3,f-r3-03-4,f-r3-03-5,f-r3-03-6,f-r3-03-7,f-r3-03-8,f-r3-03-9,f-r3-03-10,f-r3-04-3,f-r3-04-4,f-r3-04-5,f-r3-04-6,f-r3-04-7,f-r3-04-8,f-r3-04-9,f-r3-04-10,f-r3-05-3,f-r3-05-4,f-r3-05-5,f-r3-05-6,f-r3-05-7,f-r3-05-8,f-r3-05-9,f-r3-05-10,f-r3-06-3,f-r3-06-4,f-r3-06-5,f-r3-06-6,f-r3-06-7,f-r3-06-8,f-r3-06-9,f-r3-06-10,f-r3-09,f-r3-10,f-r3-11,f-r3-12,f-r3-13,f-r3-14,f-r3-15,f-r3-16,f-r3-07-3,f-r3-07-4,f-r3-07-5,f-r3-07-6,f-r3-07-7,f-r3-07-8,f-r3-07-9,f-r3-07-10,f-r3-08-3,f-r3-08-4,f-r3-08-5,f-r3-08-6,f-r3-08-7,f-r3-08-8,f-r3-08-9,f-r3-08-10');
				$data = sv1_3($rid, $rzd_id3->id, $fieldoh, $fieldsv);
			break;
			case 4:
				unset($field);
				$field = explode(',', 'f-r4-01-3,f-r4-01-4,f-r4-01-5,f-r4-01-6,f-r4-01-7,f-r4-01-8,f-r4-01-9,f-r4-01-10,f-r4-01-11,f-r4-01-12,f-r4-01-13,f-r4-01-14,f-r4-02a,f-r4-02b,f-r4-03a,f-r4-03b,f-r4-03c,f-r4-02b,f-r4-04,f-r4-05,f-r4-06');
				$data = sv1_4($rid, $rzd_id4->id, $field);
			break;
			case 5:
				unset($field);
				$fieldoh =  explode(',', 'f-r1-01,f-r1-02');
				$fieldsv = explode(',', 'f-r5-01,f-r5-02,f-r5-03,f-r5-04,f-r5-05,f-r5-06,f-r5-07,f-r5-08');
				$data = sv1_5($rid, $rzd_id1->id, $fieldoh, $fieldsv);
			break;
			case 6:
				unset($field);
				$field =  explode(',', 'f-r6-01-3,f-r6-01-4,f-r6-01-5,f-r6-01-6,f-r6-01-7,f-r6-02-3,f-r6-02-4,f-r6-02-5,f-r6-02-6,f-r6-02-7,f-r6-03-3,f-r6-03-4,f-r6-03-5,f-r6-03-6,f-r6-03-7,f-r6-04');
				$field1 = explode(',', 'f-r5-01-3,f-r5-01-4,f-r5-01-5,f-r5-01-6,f-r5-01-7,f-r5-02-3,f-r5-02-4,f-r5-02-5,f-r5-02-6,f-r5-02-7,f-r5-03-3,f-r5-03-4,f-r5-03-5,f-r5-03-6,f-r5-03-7,f-r5-04');
				$data = sv1_6($rid, $rzd_id5->id, $field, $field1);
			break;
			case 7:
				unset($field);
				$fieldoh =  explode(',', 'f-r7-01,f-r7-02');
				$fieldsv = explode(',', 'f-r7-01-2,f-r7-01-3,f-r7-01-4');
				$data = sv1_7($rid, $rzd_id7->id, $fieldoh, $fieldsv);
			break;
			case 8:
				unset($field);
				$fieldoh =  explode(',', 'f-r8-01-3,f-r8-02-3,f-r8-03-3,f-r8-04-3,f-r8-05-3,f-r8-06-3,f-r8-07-3,f-r8-08-3,f-r8-01-4,f-r8-02-4,f-r8-03-4,f-r8-04-4,f-r8-05-4,f-r8-06-4,f-r8-07-4,f-r8-08-4');
				$fieldsv = explode(',', 'f-r8-01-3,f-r8-01-4,f-r8-01-5,f-r8-01-6,f-r8-01-7,f-r8-01-8,f-r8-01-9,f-r8-01-10,f-r8-02-3,f-r8-02-4,f-r8-02-5,f-r8-02-6,f-r8-02-7,f-r8-02-8,f-r8-02-9,f-r8-02-10');
				$data = sv1_8($rid, $rzd_id8->id, $fieldoh, $fieldsv);
			break;
			case 9:
				unset($field);
				$field =  explode(',', 'f-r9-01-3,f-r9-01-4,f-r9-02-3,f-r9-02-4,f-r9-03-3,f-r9-03-4');
				$field1 = explode(',', 'f-r9-01-3,f-r9-01-4,f-r9-01-5,f-r9-02-3,f-r9-02-4,f-r9-02-5,f-r9-03-3,f-r9-03-4,f-r9-03-5,f-r9-04-3,f-r9-04-4,f-r9-04-5');
				$data = sv1_9($rid, $rzd_id9->id, $field, $field1);
			break;
		}

		$table = get_record_sql("select id from {$CFG->prefix}monit_rayon_listforms where rayonid=$rid and shortname=$table_rzd->id and datemodified=$datemodified");
		if(!$table)  {
			$bkp->rayonid = $rid;
			$bkp->status = 2;
			$bkp->shortname = $table_rzd->id;
			$bkp->datemodified = $datemodified;
			$listformid = insert_record("monit_rayon_listforms", $bkp);
		}  else  {
			$listformid = $table->id;
		}

	    $table = get_records_sql("select id from {$CFG->prefix}monit_bkp_table_$table_rzd->id where listformid=$listformid");
	    if(!$table)  {
	    	$lf->listformid=$listformid;
	    	insert_record("monit_bkp_table_$table_rzd->id", $lf);
	    }
		updaterzd($table_rzd->id, $data, $listformid);
	}
}

?>