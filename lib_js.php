<?php  /// $Id: lib_js.php,v 1.1 2008/04/01 12:58:27 Shtifanov Exp $
       /// Load up any required Javascript libraries

	require_once($CFG->dirroot.'/lib/javascript.php');

?>

<STYLE>
DIV.mou_prompts {
 padding: 3px;
 border: 1px solid #666;
 border-right-width: 2px;
 border-bottom-width: 2px;
 display: none;
 background-color: #D4E6EA;
 color: #000;
 font-size: 12px;
 position: absolute;
 z-index: 1;
 width: 350px;
}
DIV.mou_prompts_show {
 padding: 3px;
 border: 1px solid #666;
 border-right-width: 2px;
 border-bottom-width: 2px;
 display: block;
 background-color: #D4E6EA;
 color: #000;
 font-size: 12px;
 position: absolute;
 z-index: 1;
 width: 350px;
}

</STYLE>

<script type="text/javascript" >

function getElementPos(TrackOff)
{
	var LeftOff = 0;
	var TopOff = 0;
	while(TrackOff) {
		LeftOff += TrackOff.offsetLeft;
		TopOff += TrackOff.offsetTop;
		TrackOff = TrackOff.offsetParent;
	}
  return { left:LeftOff, top:TopOff }
}

function ClosePrompt()
{
	var div = document.getElementById('curr_prompt');
	div.className = 'mou_prompts';
	div.style.display = 'none';

	return false;
}

function ShowPrompt(evt, id)
{
	var evt = evt || window.event;
	var ob = evt.target || evt.srcElement;
	var div = document.getElementById('curr_prompt');
	var coords = getElementPos(ob);
	div.innerHTML = '<a href="#" onclick="return ClosePrompt()"><img src="../i/btn_close.png" border="0" align="right"/></a>' + document.getElementById(id).innerHTML + '';
	div.className = 'mou_prompts_show';
	div.style.top = (coords.top + ob.offsetHeight+10) + 'px';
	div.style.left = (coords.left-180) + 'px';
	div.style.display = 'none';
	div.style.display = 'block';
	evt.cancelBubble = true;

	return false;
}

</script>

