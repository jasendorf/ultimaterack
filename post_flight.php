<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<title>UltimateRack - Your Online Military Rack Builder</title>
<link rel="stylesheet" type="text/css" href="includes/ur_style.css" />
</head>
<body>
<script src="includes/ur_scripts.js"></script>
<img src="images/ur_banner.png" alt="UltimateRack" align="right">

<p class="info_box"><b>Step 2:</b> select your options and ribbons.  <b>Hint:</b> For amounts, enter the number of times the medal or ribbon was awarded.  Don't forget to select your current or last service as it can make a difference in the order of precedence.</p>
<table border="0" width="100%">
<tr><td align="left" valign="top">
<?PHP

include ("includes/ur_config.inc.php");
include ("includes/ur_functions.inc.php");

get_and_display_forms_from_select ( $_POST['filelist'] );

?>
</td>
<td valign="top">

</td></tr>
</table>
</body>
</html>
