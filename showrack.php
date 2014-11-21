<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<title>Your UltimateRack</title>
<link rel="stylesheet" type="text/css" href="includes/ur_style.css" />
</head>
<body>
<script src="includes/ur_scripts.js" type="text/javascript"></script>
<script src="includes/overlib.js" type="text/javascript"></script>
<h1>Your UltimateRack</h1>
<?PHP

include ("includes/ur_config.inc.php");
include ("includes/ur_functions.inc.php");

if ( $_REQUEST['action'] == "make_rack" ) {
	$html_code = "";
	$rack = make_rack ($_REQUEST['amounts'], $_REQUEST['max_across']);
	?>

	<?PHP
	echo "<p><font color=\"red\">REMEMBER: This program is not a substitute for any regulation!  The absolute best tool for your rack is the reg.</font><br />To download this rack, <a href=\"$rack\">right click here</a> and select, 'Save Target As' or 'Save Link As'</p>\n";
	if ( $allow_save ) {
		echo "<p>If you are registered on the UltimateRack Discussion Board, you can <a href=\"saverack.php?rack=$rack\">click here</a> to save your rack.  <font color=\"red\">WARNING!  Clicking this link will save this rack.  Any previously saved rack will be overwritten, you have been warned.</font></p>\n\n";
	}

switch ($_REQUEST[service]) {
	case "1":
		$swatch_color1 = "light_green";
		$swatch_color2 = "dark_blue";
		break;
	case "2":
		$swatch_color1 = "khaki";
		$swatch_color2 = "white";
		break;
	case "3":
		$swatch_color1 = "dark_blue";
		$swatch_color2 = "olive";
		break;
	case "4":
		$swatch_color1 = "brighter_blue";
		$swatch_color2 = "white";
		break;
	case "5":
		$swatch_color1 = "white";
		$swatch_color2 = "khaki";
		break;
}
	
	?>

	<table id="changeable">
	<tr><td style="background-image: url(http://ultimaterack.ajandj.com/images/cloth_swatch_<?php echo $swatch_color1; ?>.png); border-width: 6px; border-style: groove; border-color: #c0c0c0; vertical-align: middle;">&nbsp;&nbsp;<br />
	<?PHP
	echo "&nbsp;&nbsp;&nbsp;<img usemap=\"#rack\" border=\"0\" src=\"$rack\" />";
	?>
	</td>
	
	
	<td rowspan="4">
	<p>&nbsp;</p><p>&nbsp;</p>
	<H3>Display Your Rack On Your Site!</H3>
	<p><a href="#instructions">Instructions</a></p>
    <form NAME="copy">
      <input type=button value="Highlight All" onClick="javascript:this.form.txt.focus();this.form.txt.select();"><br />
      <textarea NAME="txt" ROWS=15 COLS=35 WRAP=VIRTUAL><?PHP  echo $html_code; ?>
</textarea>
      <br>
    </form>
		<a name="instructions"></a>
		<p><b>Instructions for displaying your rack on your site:</b></p>
		<div align="left">
		<ol>
			<li>Download <a href="includes/overlib.js">this link (javascript file)</a> and save this <font color="red">necessary</font> file to your computer</li>
			<li>Right click the second or third rack on the left and save the rack graphic to your computer.  You should rename the rack but make sure the '.png' ending is retained</li>
			<li>Save the javascript file and rack graphic you just downloaded to your website in the <b>same directory</b> as the page you will be including your rack on</li>
			<li>Click the "Highlight All" button above the text area above and copy the entirety of the code in the textbox above</li>
			<li>Paste the code you just copied to the web page you want the rack displayed on.  Change, on the last line, 'WHAT_YOU_NAMED_YOUR_RACK_GRAPHIC' to the new name you gave your graphic.</li>
			<li>Enjoy your rack!</li>
		</ol>
		</div>
	</td>
	</tr>
	<tr><td class="rackcell"><div>&nbsp;&nbsp;<br />
	<?PHP
	echo "&nbsp;&nbsp;&nbsp;<img border=\"0\" src=\"$rack\" />";
	?>
	</div></td>
	</tr>
	<tr><td><form>Change bottom cell fabric: 
	<select onChange="change_bg_image(this.value);">
		<option value="">No Fabric</option>
		<option value="light_green">Army Green</option>
		<option value="brighter_blue">Blue</option>
		<option value="dark_blue">Dark Blue</option>
		<option value="khaki">Khaki</option>
		<option value="white">White</option>
		<option value="olive">Olive</option>
	</form>
	</td></tr>
	<tr><td bgcolor=white>&nbsp;&nbsp;<br /><br />
	<?PHP
	echo "&nbsp;&nbsp;&nbsp;<img border=\"0\" src=\"$rack\" />";
	?>
	&nbsp;<br /><br /></td></tr>
	</table>
	<?PHP
}

?>
</body>
</html>
