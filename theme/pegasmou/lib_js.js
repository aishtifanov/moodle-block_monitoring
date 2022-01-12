// $Id: lib_js.js,v 1.1 2008/05/30 07:22:37 Shtifanov Exp $

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


