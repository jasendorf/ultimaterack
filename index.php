<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<title>UltimateRack - Your Online Military Rack Builder</title>
<link rel="stylesheet" type="text/css" href="includes/ur_style.css" />
<script type="text/javascript" src="includes/overlib.js">
<!-- overLIB (c) Erik Bosrup --></script>
</head>
<body>
<script src="includes/ur_scripts.js"></script>
<img src="images/ur_banner.png" alt="UltimateRack" align="right">

<p>UltimateRack is an virtual military rack builder.  This is a test page</p>
<hr>
<table border="0" width="100%">
<tr><td align="left" valign="top">
<p class="info_box"><b>Step 1:</b> Select the ribbon sets from which you wish to build your rack. <br /><br /><b>Hint:</b> you can click on the name of each set to see which medals and ribbons are in them.  Hint 2: (Ref) denotes that a reference for this set is availabile on the subsequent step.</p>
<?PHP

include ("includes/ur_config.inc.php");
include ("includes/ur_functions.inc.php");

create_set_selector ();

?>
</td>
<td valign="top">
&nbsp;
</td></tr></table>

</body>
</html>
