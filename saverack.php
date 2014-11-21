<?PHP
/*This file only works with PHPBB
It is not ready for mass distribution really, so don't use it */
define('IN_PHPBB', true); 

$site_root_path = '/path/to/site/root'; //<-- Modify
$phpbb_root_path2 = '/phpbbdirectory/'; //<-- Modify
$phpbb_root_path = $site_root_path . $phpbb_root_path2;
include($phpbb_root_path . 'extension.inc'); 
include($phpbb_root_path . 'common.php'); 

$userdata = session_pagestart($user_ip, PAGE_INDEX); 
init_userprefs($userdata);

if($userdata['session_logged_in']){

//print_r ($userdata);


$bad_filename_characters = array(" ", "!", "/", "\\", "\"", "'", "*", "$", "&", "?");
$dest = "saved/" . str_replace($bad_filename_characters, "_", $userdata['username']) . ".png";

if (!copy($_REQUEST[rack], $dest)) {
   echo "failed to save your rack, sorry...\n";
}
else {
	echo "<p>Your rack has been saved at <a href=\"$dest\">http://ultimaterack.ajandj.com/$dest</a> and will be retained there forever or until you overwrite it.  You may only save one rack per user.  Abuse of this function will not be tolerated and abusers will be banned. <A HREF=\"javascript:history.back()\">Return to your rack</a></p>";
}	

}

else{
?>
If you would like to save your rack on the UltimateRack website, you must either 
<?php 

echo "<a href=\"";
echo $phpbb_root_path2;
echo "login.php?redirect=../saverack.php?rack=";
echo $_REQUEST[rack];
echo "\">Login</a>";

?>
 or 
<a href="<?php echo $phpbb_root_path2; ?>profile.php?mode=register">Register</a> 

<?php
}

?>