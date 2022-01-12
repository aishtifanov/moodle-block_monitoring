<?php // $Id: bkp_dolj.php,v 1.5 2008/09/12 08:44:28 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $nm = required_param('nm', PARAM_INT);       // Month number
    $yid = required_param('yid', PARAM_INT);       		// Year id
    $shortname = required_param('sn');       // Shortname form

    include ("include_bkp.php");

?>
<div align=center>
<table border="1" cellspacing="2" cellpadding="5" align="center" bordercolor=black>
<tr>
	<th valign="top" nowrap="nowrap" ><?php print_string('symbolnumber', 'block_monitoring') ?></th>
	<th valign="top" nowrap="nowrap" ><?php print_string('nameofpokazatel', 'block_monitoring') ?></th>
	<th valign="top" nowrap="nowrap" ><?php print_string('kolphisperson', 'block_monitoring') ?></th>
	<th valign="top" nowrap="nowrap" ><?php print_string('kolstavok', 'block_monitoring') ?></th>
</tr>
<?php
  $razdel = get_record ('monit_razdel', 'shortname', 'bkp_dolj');
  // print_r($razdel);
  if ($razdel)	{  		$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id);
 	    // print_r($fields);
  		if ($fields) {
  		    $num_I = -1;  $num_II = 1;
  		    $SUM = array();
  		    $SUM_st = array();
            foreach ($fields as $field)  {
            	switch ($field->edizm)	{
            		case 'null': $num_I++; $SUM[$num_I] = $SUM_st[$num_I] = 0;
            		break;
            		case 'man': case 'item':
            		      if ($formslist != '')	{
								$rec = get_record_sql("SELECT Sum($field->name_field) AS sum
														FROM {$CFG->prefix}monit_form_bkp_dolj
														WHERE listformid in ($formslist)");
								$SUM[$num_I] += $rec->sum;

								$fieldname_st = $field->name_field . '_st';
								$rec = get_record_sql("SELECT Sum($fieldname_st) AS sum
														FROM {$CFG->prefix}monit_form_bkp_dolj
														WHERE listformid in ($formslist)");
								$SUM_st[$num_I] += $rec->sum;
						  }	 else {
				                $SUM[$num_I] = $SUM_st[$num_I] = 0;
						  }

            		break;
            	}
            }

            $SUM[0] = $SUM_st[0] = 0;
            for ($i=1; $i<=$num_I; $i++) {
            	$SUM[0] += $SUM[$i];
            	$SUM_st[0] += $SUM_st[$i];
            }


   		    $num_I = -1;  $num_II = 1;            foreach ($fields as $field)  {            	switch ($field->edizm)	{            		case 'null': $num_I++; $num_II = 1;
            					 echo '<tr valign="top">';
								 echo "<TD>$num_I</TD>";
   								 echo "<TD align=left><B>$field->name</B>";
   								 echo '</TD>';
							     echo "<TD><b>{$SUM[$num_I]}</b></TD>";
							     $sum = number_format($SUM_st[$num_I], 2, ',', ' ');
							     echo "<TD><b>{$sum}</b></TD></tr>";
            		break;

            		case 'man': case 'item':
								echo '<tr valign="top">';
								echo "<td align=left>$num_I.$num_II</td>";
							    echo "<td align=left>$field->name</td>";
							    echo "<td align=left> ";
	               		        if ($formslist != '')	{
									$rec = get_record_sql("SELECT Sum($field->name_field) AS sum
															FROM {$CFG->prefix}monit_form_bkp_dolj
															WHERE listformid in ($formslist)");
									// echo "{$rec->sum}&nbsp;" . get_string($field->edizm, 'block_monitoring');
									echo $rec->sum;
								}	else {
									echo '0';
								}
								echo '</td>';

								$fieldname_st = $field->name_field . '_st';
							    echo "<td align=left> ";
	               		        if ($formslist != '')	{
									$rec = get_record_sql("SELECT Sum($fieldname_st) AS sum
															FROM {$CFG->prefix}monit_form_bkp_dolj
															WHERE listformid in ($formslist)");
									$sum = number_format($rec->sum, 2, ',', ' ');

									// echo "{$sum}&nbsp; ст.";
									echo $sum;
								}	else {
									echo '0';
								}
								echo '</td></tr>';
								$num_II++;
            		break;
            	}            }  		}  }
 echo '</div></table>';

 print_simple_box_end();

 print_footer();

?>

