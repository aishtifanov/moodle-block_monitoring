<?php // $Id: bkp_f.php,v 1.4 2008/09/12 08:44:28 Shtifanov Exp $

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
	<th valign="top" nowrap="nowrap" ><?php print_string('valueofpokazatel', 'block_monitoring') ?></th>
</tr>
<?php
  $razdel = get_record ('monit_razdel', 'shortname', 'bkp_f');
  // print_r($razdel);
  if ($razdel)	{
  		$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id);
 	    // print_r($fields);
  		if ($fields) {
            foreach ($fields as $field)  {
            	switch ($field->edizm)	{
            		case 'null': echo '<tr valign="top">';
								 $_num = translitfield($field->name_field);
								 echo "<TD>$_num</TD>";
   								 echo "<TD align=left><B>$field->name</B></TD>";
   								 echo '<TD align=left>';
	               		        if ($formslist != '')	{
									 $rec = get_record_sql("SELECT Sum($field->name_field) AS sum
															FROM {$CFG->prefix}monit_form_bkp_f
															WHERE listformid in ($formslist)");
									if ($rec->sum > 0.0001)
										 $sum = number_format($rec->sum, 3, ',', '');
									else $sum = 0;
									 // echo "{$sum}&nbsp;" . get_string('trub', 'block_monitoring');
									 echo "<b>{$sum}</b>";
								} else {									echo '0';
								}

							     echo '</TD></tr>';
            		break;

            		case 'trub':
								echo '<tr valign="top">';
								$_num = translitfield($field->name_field);
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$field->name</td>";
							    echo "<td align=left> ";
	               		        if ($formslist != '')	{
									$rec = get_record_sql("SELECT Sum($field->name_field) AS sum
															FROM {$CFG->prefix}monit_form_bkp_f
															WHERE listformid in ($formslist)");
									if ($rec->sum > 0.0001)
										 $sum = number_format($rec->sum, 3, ',', '');
									else $sum = 0;
									// echo "{$sum}&nbsp;" . get_string('trub', 'block_monitoring');
									echo $sum;
								}	else {
									echo '0';
								}
								echo '</td></tr>';
								// $num_II++;
            		break;
            	}
            }
  		}
  }

 echo '</div></table>';

 print_simple_box_end();

 print_footer();

?>


