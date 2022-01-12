<?php // $Id: bkp_kbo.php,v 1.1 2010/05/20 11:36:33 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../lib.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $nm  = required_param('nm', PARAM_INT);     // Month number
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
  $razdel = get_record ('monit_razdel', 'shortname', 'bkp_kbo');
  $pieces = explode(",", $formslist);
//  print_r($pieces);
  $countsform = count($pieces);
//  echo '<br>'.$countsform;
  if ($razdel)	{
  		$fields = get_records ('monit_razdel_field', 'razdelid', $razdel->id);
 	    // print_r($fields);
  		if ($fields) {
  		    $num_I = 0;  $num_II = 1;
            foreach ($fields as $field)  {
            	switch ($field->edizm)	{
            		case 'null': $num_I++; $num_II = 1;
            					 echo '<tr valign="top">';
								 echo "<TD align=left>$num_I</TD>";
   								 echo "<TD align=left><B>$field->name</B></TD>";
							     echo '<TD></TD></tr>';
            		break;

            		case 'item':
								echo '<tr valign="top">';
								if (isset($field->name_field)) {
									$_num = translitfield($field->name_field);
								} else {
									$_num = $num_I.$num_II;
								}
								echo "<td align=left>$_num</td>";
							    echo "<td align=left>$field->name</td>";
							    echo "<td align=left> ";

	               		        if ($formslist != '')	{
									$rec = get_record_sql("SELECT Sum($field->name_field) AS sum
															FROM {$CFG->prefix}monit_form_bkp_kbo
															WHERE listformid in ($formslist)");
									echo "{$rec->sum}&nbsp;" . get_string($field->edizm, 'block_monitoring');
								}	else {
									echo '0';
								}
								echo '</td></tr>';

								$num_II++;
            		break;
            	}
            }
  		}
  }

 echo '</div></table>';

 print_simple_box_end();

 print_footer();

?>

