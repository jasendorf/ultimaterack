<?PHP
//Name of this Ribbon Set
$set_name = "Sample Ribbon Set";

//Reference Document Info
$support_doc_type = ""; //file or url - file = local file, url = remote url
$support_doc_filename = ""; //file name or complete url (e.g. http://www.army.mil/awards_page.htm )
$support_doc_title = "";
$support_doc_date = ""; //YYYY-MM-DD 

//Ribbon array for ribbon files in this set
//MUST BE A VARIABLE NAMED THE SAME AS THE RIBBON SET'S DIRECTORY
$sample = array ( 
		'HM' => array ( 'Heroism Medal' , 'Heroism_Medal.png' , 1,2,0,1000,1000,1000,1000,1000,0)
		, 'RAM' => array ( 'Really Awesome Medal' , 'Really_Awesome_Medal.png' , 1,2,0,1100,1100,1100,1100,1100,0)
		, 'GGM' => array ( 'Good Guy\'s Medal' , 'Good_Guys_Medal.png' , 1,2,0,1200,1200,1200,1200,1200,0)
		, 'HWM' => array ( 'Hard Worker Medal' , 'Hard_Worker_Medal.png' , 1,4,0,1300,1300,1300,1300,1300,0)
		, 'SGSM' => array ( 'Superiorly Good Service Medal' , 'Superiorly_Good_Service.png' , 1,3,0,1400,1400,1400,1400,1400,0)
		, 'LR' => array ( 'Longevity Ribbon' , 'Longevity_Ribbon.png' , 1,2,9,1500,1500,1500,1500,1500,0)
		, 'TR' => array ( 'Training Ribbon' , 'Training_Ribbon.png' , 1,4,0,1600,1600,1600,1600,1600,0)
);

?>
