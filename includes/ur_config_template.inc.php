<?PHP
//To debug, uncomment the following line
//error_reporting(E_ALL);

/*base path of the installation */
$site_path = "/path/to/installation/";

/* Allow users to save their racks on your website.  This function has problems at the moment and should not be used yet. Values - true,false */
$allow_save = false;

/* Default space, in pixels, between ribbons on finished rack.  This can be changed by the user at the time of rack creation, this is simply a default */
$space_between_ribbons = 2;

/* Directory in which racks will be stored temporarily */
$rack_directory = "racks/";

/* Clean-up Frequency of the rack directory.  This determines how often the rack directory is purged. Recommended values: 20-50 (20 would mean, on average, the directory be cleaned out once every 20 racks... 50 would mean once every 50 */
$clean_up_frequency = 40;

/* When cleaning up the racks directory, the amount of hours old a rack has to be to be deleted */
$rack_longevity = 6; //racks older this many hours will be deleted when clean-up is performed

/* default text for tagline */
$tagline_text_default = "ultimaterack.sf.net";

/* default url for tagline */
$tagline_default_url = "http://ultimaterack.sf.net";

/* default url for tagline */
$tagline_popup_text = "Click Here to visit the UltimateRack Project Page";

/* Directory to find appurtenances and site graphics */
$images_directory = "images/";  // where ribbon and device images are stored

/* Temporary directory for storing temporary graphics in the creation of full racks */
$temp_directory = "temp/";  //relative path from index.php where created racks are stored

/* I have no idea what this is or was intended for */
$hold_file = "5"; 

/* allows for a wiki (or other I suppose) link to be dynamically created for each ribbon in the ribbon selector */
$wiki_link = false; //used to connect to a wiki
$wiki_URL_prepend = "http://www.yoursite.com/wiki/index.php/";


//DO NOT change below this line unless you've changed directory names and know what you're doing

/* Directory where ribbon sets are placed */
$ribbon_directory_name = "ribbon_sets/";

/* Array of devices.  This needs a major reqork IMHO */
$device_types = array ( 0=>"" , 
												1=>"star" , 
												2=>"oak_leaf" , 
												3=>"loop" , 
												4 =>"numeral", 
												5 => "AFRAM", 
												6=>"small_stars", 
												7=>"navy_battle_e", 
												8=>"campaign_stars", 
												9=>"V_device", 
												10=>"olc_stars", 
												11=>"gold_border", 
												12=>"arrowhead", 
												13=>"lozenge", 
												14=>"silver_stars_maine", 
												15=>"wintered_over", 
												16=>"laurel_leaf", 
												17=>"fmf", 
												18=>"fmf_arrowhead", 
												19=>"M_only",
												20=>"berlin_airlift_device",
												21=>"A_device" );

$temp_full_path = $site_path . $temp_directory; //path to temp files

$racks_full_path = $site_path . $rack_directory; //path to rack files

$path_to_ribbon_sets = $site_path . $ribbon_directory_name . "/"; //path to ribbon sets

?>
