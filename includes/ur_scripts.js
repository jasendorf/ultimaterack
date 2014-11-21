/*
UltimateRack - Javascript file
John Asendorf - john.asendorf@us.army.mil
*/

function enablefield(objButton) {
  var strName=objButton.name;
  var arrTmp=strName.split("_");
  var index=arrTmp[0];
  var strInputFieldName="amounts["+index+"][amount]";
	var strAltInputFieldName="";
	var objAltInputField="";
	
	if (index=='AFRAM') {strInputFieldName="amounts[AFRAM][H]";}
	if (index=='AntSM') {strInputFieldName="amounts[AntSM][H]";}
  var objInputField=objButton.form.elements[strInputFieldName];
	var itschecked=(objButton.checked)?'Y':'';
	if (strInputFieldName=="amounts[AFRAM][H]"||strInputFieldName=="amounts[AntSM][H]") { //specifically for the AFRAM and AntSM
		for (var i=0; i < 4; i++){ //enable/disable the radio buttons
			objInputField[i].disabled=!objInputField[i].disabled;
			objInputField[i].checked=(i==0&&itschecked=='Y')?'1':'';
		}
	}	
	else{
  	objInputField.disabled=(itschecked=='Y')?false:true;
  	objInputField.value=(itschecked=='Y')?'1':'';
  	objInputField.style.backgroundColor=(itschecked=='Y')?'#ffff33':'';
  	objInputField.style.fontWeight=(itschecked=='Y')?'bold':'normal';
  	if (itschecked=='Y') { objInputField.select(); }
	}
	
	var possibleFields = new Array(2);
	possibleFields[0] = "V";
	possibleFields[1] = "M";
	possibleFields[2] = "GB";
	possibleFields[3] = "AH";
	possibleFields[4] = "FMF";
	possibleFields[5] = "BAD";
	possibleFields[6] = "A";

	for (var i=0;i<7;i++){
  	strAltInputFieldName="amounts["+index+"]["+possibleFields[i]+"]";
  	if ( objAltInputField=objButton.form.elements[strAltInputFieldName] ) {
    	objAltInputField.disabled=(itschecked=='Y')?false:true;
			objAltInputField.checked=(itschecked=='Y')?false:false;
    	objAltInputField.value=(itschecked=='Y')?'1':'';
    	objAltInputField.style.backgroundColor=(itschecked=='Y')?'#ffff33':'';
    	objAltInputField.style.fontWeight=(itschecked=='Y')?'bold':'normal';
      if (itschecked=='Y') { objAltInputField.select(); }
		}
	}
}


function checkconditions(value, name) {
  var hiddenformname='java-'+name;
  var hiddenidname='display-'+name;
  if (document.getElementById(name).checked) {
  	document.getElementById(hiddenformname).value='Y';}
  else {
  	document.getElementById(hiddenformname).value='';} 
  if ((document.getElementById(hiddenformname).value == 'Y')){
   document.getElementById(hiddenidname).style.display='';}
  else{document.getElementById(hiddenidname).style.display='none';}
}

function resetAllElements() {
	for(i=0;i<document.ribbons.elements.length;i++) {
		if(document.ribbons.elements[i].getAttribute('canDisable')=='1') {
			document.ribbons.elements[i].style.backgroundColor='#ECE9D8';
			document.ribbons.elements[i].disabled=true;
		}
	}
}

function change_bg_image(color) {
  var x=document.getElementById('changeable').rows;
  var y=x[1].cells;
  var bgimage="url(http://ultimaterack.ajandj.com/images/cloth_swatch_" + color + ".png)";
  y[0].style.backgroundImage=bgimage;
}

function doTooltip(e, msg) {
  if ( typeof Tooltip == "undefined" || !Tooltip.ready ) return;
  Tooltip.show(e, msg);
}

function hideTip() {
  if ( typeof Tooltip == "undefined" || !Tooltip.ready ) return;
  Tooltip.hide();
}

function newWindow() {
	window.open('', 'newWin' ,'scrollbars=1,menubar=0,toolbar=0,location=0,status=0,height=600,width=800');
}


/*
   ######################################################
   # JAVASCRIPT POPUPS ROUTINE VERSION #7 07-Feb-2001   #        
   # Written by Mike McGrath [mike_mcgrath@lineone.net] # 
   # PC-Tested for Netscape 3.04, 4.61, 6.0, & IE5.5    #
   # Note: Popups may not cover all form field inputs.  #
   # PLEASE RETAIN THIS NOTICE WHEN COPYING MY SCRIPT.  #
   # THIS SCRIPT IS COPYRIGHT OF MIKE MCGRATH 1998-2001 #
   ######################################################
-->
<!-- Original: Mike McGrath  (mike_mcgrath@lineone.net) -->
<!-- Web Site: http://website.lineone.net/~mike_mcgrath -->
*/

var Xoffset=0;        // modify these values to ...
var Yoffset= -100;        // change the popup position.
var popwidth=260;       // popup width
var bcolor="darkgray";  // popup border color
var fcolor="black";     // popup font color
var fface="verdana";    // popup font face

// create content box
document.write("<DIV ID='pup'></DIV>");

// id browsers
var iex=(document.all);
var nav=(document.layers);
var old=(navigator.appName=="Netscape" && !document.layers && !document.getElementById);
var n_6=(window.sidebar);

// assign object
var skin;
if(nav) skin=document.pup;
if(iex) skin=pup.style;
if(n_6) skin=document.getElementById("pup").style;

// park modifier
var yyy=-1000;

// capture pointer
if(nav)document.captureEvents(Event.MOUSEMOVE);
if(n_6) document.addEventListener("mousemove",get_mouse,true);
if(nav||iex)document.onmousemove=get_mouse;

// set dynamic coords
function get_mouse(e)
{
  var x,y;

  if(nav || n_6) x=e.pageX;
  if(iex) x=event.x+document.body.scrollLeft; 
  
  if(nav || n_6) y=e.pageY;
  if(iex)
  {
    y=event.y;
    if(navigator.appVersion.indexOf("MSIE 4")==-1)
      y+=document.body.scrollTop;
  }

  if(iex || nav)
  {
    skin.top=y+yyy;
    skin.left=x+Xoffset; 
  }

  if(n_6)
  {
    skin.top=(y+yyy)+"px";
    skin.left=x+Xoffset+"px";
  }    
  nudge(x);
}

// avoid edge overflow
function nudge(x)
{
  var extreme,overflow,temp;

  // right
  if(iex) extreme=(document.body.clientWidth-popwidth);
  if(n_6 || nav) extreme=(window.innerWidth-popwidth);

  if(parseInt(skin.left)>extreme)
  {
    overflow=parseInt(skin.left)-extreme;
    temp=parseInt(skin.left);
    temp-=overflow;
    if(nav || iex) skin.left=temp;
    if(n_6)skin.left=temp+"px";
  }

  // left
  if(parseInt(skin.left)<1)
  {
    overflow=parseInt(skin.left)-1;
    temp=parseInt(skin.left);
    temp-=overflow;
    if(nav || iex) skin.left=temp;
    if(n_6)skin.left=temp+"px";
  }
}

// write content & display
function popup(msg,bak)
{

  var content="<TABLE WIDTH='"+popwidth+"' BORDER='1' BORDERCOLOR="+bcolor+" CELLPADDING=2 CELLSPACING=0 "+"BGCOLOR="+bak+"><TD ALIGN='center'><FONT COLOR="+fcolor+" FACE="+fface+" SIZE='2'>"+msg+"</FONT></TD></TABLE>";

  if(old)
  {
    alert(msg);
    return;
  } 
   
  yyy=Yoffset; 
  skin.width=popwidth;

  if(nav)
  { 
    skin.document.open();
    skin.document.write(content);
    skin.document.close();
    skin.visibility="visible";
  }

  if(iex)
  {        
    pup.innerHTML=content;
    skin.visibility="visible";
  }  

  if(n_6)
  {   
    document.getElementById("pup").innerHTML=content;
    skin.visibility="visible";
  }
}


// park content box
function kill()
{
  if(!old)
  {
    yyy=-1000;
    skin.visibility="hidden";
    skin.width=0;
  }
}

