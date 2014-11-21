<?PHP
function determine_rack_width ( $number_of_ribbons, $ribbon_x, $max_rack_ribbons = 3 ) {
	global $space_between_ribbons;

	$rack_width = ($number_of_ribbons >= $max_rack_ribbons) ? (($max_rack_ribbons * $ribbon_x)+(($space_between_ribbons*$max_rack_ribbons)-$space_between_ribbons)) : (($number_of_ribbons * $ribbon_x)+(($space_between_ribbons*$number_of_ribbons)-$space_between_ribbons));

  return $rack_width;
}  //END Function determine_rack_width

function determine_rack_height ( $number_of_ribbons, $ribbon_y, $max_rack_ribbons = 3 ) {
	global $space_between_ribbons;
  $rack_height = ((ceil($number_of_ribbons/$max_rack_ribbons)) * $ribbon_y)+(($space_between_ribbons*(ceil($number_of_ribbons/$max_rack_ribbons)))-$space_between_ribbons);
  return $rack_height;
}  //END Function determine_rack_width

function compare($x, $y) { //function to sort multidimensional array
	global $service_number, $once;
		if ( !$once ) { 
			$service_number += 5;
			$once = true; 
		}
    if ( $x[$service_number] == $y[$service_number] )
     return 0;
    else if ( $x[$service_number] < $y[$service_number] )
     return -1;
    else
     return 1;
} //END FUNCTION compare

function compare_final ($x, $y) { //function to compare ribbon order for different services
	global $master_ribbon_array, $service_input;
  if ( !is_array($master_ribbon_array) ) { 
  	$master_ribbon_array = make_master_ribbon_array ();
  }
	if ( !isset($_REQUEST['service']) && !isset($this_service) ) {
		$this_service = 5;
	} 
	else {
		$this_service = $_REQUEST['service'] + 4;
	}
	if ( $master_ribbon_array[$x][$this_service] == $master_ribbon_array[$y][$this_service] )
		return 0;
	else if ( $master_ribbon_array[$x][$this_service] < $master_ribbon_array[$y][$this_service] )
		return 1;
	else
		return -1;
}


function create_set_selector () {
  global $path_to_ribbon_sets, $ribbon_directory_name, $set_name, $$file;
	if ($handle = opendir($path_to_ribbon_sets)) {
		$temp_count = 0;
    while (false !== ($file = readdir($handle))) {
    	if ( $file != "." && $file != ".." && $file != "index.php" ) {
    		$filelist[] = $file;
				$temp_count++;
    	}
    }
		//closedir($handle);
		if ( $temp_count == 0 ) {
			die ("<p><em>There are no ribbon sets installed, you will need to install at least one ribbon set for UltimateRack to work.</b></em>");
		}
		unset ($temp_count);
	}
	
	echo "<form name=\"ribbons\" action=\"post_flight.php\" method=\"post\">\n";
	echo "<table>\n";
	asort ($filelist);
	$left_right = 0;
	foreach ($filelist as $file) { //bring back each ribbon set
			$left_right=$left_right%2;
      $ribbon_inc_file = $ribbon_directory_name .  $file . "/ribbon_set.inc.php";
			include($ribbon_inc_file);
      $this_directory = $ribbon_directory_name .  $file . "/";
			echo ($left_right==0)?"<tr>":"";
			echo "<td class=\"set_selector\"><input type=\"checkbox\" name=\"filelist[]\" value=\"" . $file . "\"/>";
			echo "<a href=\"javascript:void(0);\" onclick=\"return overlib('";
			$quotes = array ("'",'"');
			foreach ( $$file as $key=>$ribbon ) {
				echo str_replace($quotes," ",$ribbon[0]) . "<br />";
			}
			echo "', STICKY, MOUSEOFF, CAPTION, 'Ribbons in " . $set_name . "')\" onmouseout=\"return nd();\">";
			echo $set_name;
			echo "</a>";
			if ( $support_doc_type != "" ) {
				echo " (Ref)";
				unset ($support_doc_type);
			}
			echo "\n";
			echo ($left_right==0)?"</td>":"</td></tr>\n";
      $left_right++;
		}
		echo "					<tr><td><input type=\"submit\" value=\"Proceed\" />&nbsp;&nbsp;<input type=\"reset\"  onclick=\"resetAllElements()\" /></td></tr>\n";
		echo "				</form>\n";
		echo "</table>";
}


function get_and_display_forms_from_select ( $filelist ) {
	global $path_to_ribbon_sets, $ribbon_directory_name, $set_name, $$file;
	if ( $filelist ) {
    if ($handle = opendir($path_to_ribbon_sets)) {
  		echo "<form name=\"ribbons\" action=\"showrack.php\" method=\"post\" target=\"newWin\"";
  		echo " onSubmit=\"newWindow()\">\n";
  		echo "<input type=\"hidden\" name=\"action\" value=\"make_rack\" />\n";
  		echo "<p align=\"left\">Space (in pixels) between ribbons): <input type=\"text\" name=\"space\" value=\"2\" size=\"2\" /><br />\n";
  
  		echo "Number of Ribbons Across: 3<input type=\"radio\" name=\"max_across\" value=\"3\" CHECKED />&nbsp;&nbsp;4<input type=\"radio\" name=\"max_across\" value=\"4\" /><br />\n";
  		echo "Add UR tagline? <input type=\"checkbox\" name=\"tagline\" value=\"1\" CHECKED /> <font size=\"-2\">(By checking this you can spread the word about UltimateRack)</font></p>\n";
  		echo "<table>\n      <tr>\n<td class='small'>Your Current or Last Service: </td>\n";
  		echo "<td class='army'>U.S. Army<input type=\"radio\" name=\"service\" value=\"1\" CHECKED /></td>\n";
  		echo "<td class='navy'>&nbsp;&nbsp;Navy<input type=\"radio\" name=\"service\" value=\"2\" /></td>\n";
  		echo "<td class='marines'>&nbsp;&nbsp;Marines<input type=\"radio\" name=\"service\" value=\"3\" /></td>\n";
  		echo "<td class='af'>&nbsp;&nbsp;Air Force<input type=\"radio\" name=\"service\" value=\"4\" /></td>\n";
  		echo "<td class='cg'>&nbsp;&nbsp;Coast Guard<input type=\"radio\" name=\"service\" value=\"5\" /></td>\n";
  		echo "<td class='civ'>&nbsp;&nbsp;Civilian/Other<input type=\"radio\" name=\"service\" value=\"6\" />\n";
  		echo "</tr></table>";
  
  		asort ($filelist); //srt directories alphabetically
  
  		foreach ($filelist as $file) { //bring back each ribbon set
        $ribbon_inc_file = $ribbon_directory_name .  $file . "/ribbon_set.inc.php";
        include($ribbon_inc_file);
        
        $this_directory = $ribbon_directory_name .  $file . "/";
        make_ribbon_form ( $$file, $file, $set_name, $this_directory, $contributor, $support_doc_filename, $support_doc_title, $support_doc_date, $support_doc_type ) ;
				unset ($support_doc_filename, $support_doc_title, $support_doc_date, $support_doc_type );
  		}
  		
  		echo "					<input type=\"submit\" value=\"Build My Rack\" />&nbsp;&nbsp;<input type=\"reset\"  onclick=\"resetAllElements()\" />\n";
  		echo "				</form>\n";
    }
  	else {
  		echo "<p>Unable to open the ribbon sets directory.  Something's misconfigured.</p>";
  	}
	}
	else {
		echo "<p><b>You didn't select any sets!</b></p>\n";
		create_set_selector ();
	}
}




function make_ribbon_form ( $ribbon_array, $ribbon_group_id, $ribbon_group_name, $images_directory, $contributor = '', $support_doc_filename = '', $support_doc_title = '', $support_doc_date = '', $support_doc_type = '' ) {
	global $wiki_URL_prepend, $wiki_link;
	
	//sort array for service
	$service_number = 1; // assign service to $service_number
	uasort($ribbon_array, 'compare');
	
	echo "\n<table border='0' width='100%'>\n";
	echo "  <tr>\n    <td class='td_group'><input type='checkbox' name='$ribbon_group_id' ";
	echo "id='$ribbon_group_id' value='Y' ";
	echo "onClick='checkconditions(this.value, this.name)' />\n";
	echo "<input type='hidden' name='java-$ribbon_group_id' ";
	echo "id='java-$ribbon_group_id' value=''> <!--place holder for value-->\n";
	echo "    </td>\n  <td width='60%' class='td_group'>$ribbon_group_name</td>\n";
	echo "  <td width='40%' class='td_contributor'>";
	if ($contributor != '' ) {
		echo "Contributed By:<br />$contributor";
	} else { echo "&nbsp;"; }
	echo "</td>\n</tr>\n";
	if ( $support_doc_filename != '' ) {
		echo "<tr><td colspan=\"3\"  class=\"td_support_doc\">Reference Document: <a href=\"";
		if ( $support_doc_type == "url" ) {
			echo  $support_doc_filename;
		}
		else {
			echo $images_directory . $support_doc_filename;
		}
		echo "\" target=\"new.Window\">" .  $support_doc_title . "</a>&nbsp;&nbsp;&nbsp;Dated: " . $support_doc_date . "</td></tr>\n";
	}
	echo "  <tr name='display-$ribbon_group_id' id='display-$ribbon_group_id' style='display: none'>\n    <td colspan='3'>\n";
	$i=0;
	echo "      <table border='0' width='100%' cellpadding='4'>\n";
	foreach ( $ribbon_array as $key=>$ribbon ) {
		if ( $i == 0 ) { echo "        <tr>\n"; }
		echo "          <td width=\"25%\" class=\"l\">\n<input type=\"checkbox\" name=\"" . $key . "_switch\"  onclick=\"enablefield(this);\" />&nbsp;";
		echo "<img src=\"" . $images_directory . $ribbon[1] . "\" />";
		
		//Armed Forces Reserve Achievement mess
		if ( $key == "AFRAM" ) {
			echo "<br />Hourglass: <input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"n\" canDisable=\"1\" />None ";
			echo "<input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"b\" canDisable=\"1\" />Bronze ";
			echo "<br /><input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"s\" canDisable=\"1\" />Silver ";
			echo "<input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"g\" canDisable=\"1\" />Gold<br />";
			echo "Mobilizations &times; <input disabled name=\"amounts[" . $key . "][M]\" type=\"text\" value=\"\" size=\"2\" canDisable=\"1\" /><br />";
		}
		
		elseif ( $key == "AntSM" ) {
			echo "<br />Wintered Over Disc: <br /><input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"n\" canDisable=\"1\" />None ";
			echo "<input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"b\" canDisable=\"1\" />Bronze ";
			echo "<br /><input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"s\" canDisable=\"1\" />Silver ";
			echo "<input class=\"canDisable\" disabled name=\"amounts[" . $key . "][H]\" type=\"radio\" value=\"g\" canDisable=\"1\" />Gold<br />";
			
		}
		
		//All others
		else {
			if ( $ribbon[3] != 0 || $ribbon[4] != 0 ) {
				if ( $key != "GWOTEM" && $key != "ICM"  && $key != "AfCM" && $key != "AOOM" && $key != "NEHDSR" ) {		
						//For ribbons which you can only get one 
  					//but other appurtenances might be worn
    			echo " <input disabled name=\"amounts[";
  				echo $key . "][amount]\" type=\"text\" size=\"2\" default=\"0\"";
  				echo " maxlength=\"2\" canDisable=\"1\" />";
				}
				else {
  				echo " <input disabled type=\"hidden\" name=\"amounts[";
  				echo $key . "][amount]\" value=\"\" canDisable=\"1\" />";
				}				
				if ( $ribbon[4] == 9 ) { //for valor device
					echo "<br />Valor Device: <input disabled name=\"amounts[";
					echo $key . "][V]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[4] == 21 ) { //for valor device
					echo "<br />Arctic Device: <input disabled name=\"amounts[";
					echo $key . "][A]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[4] == 11 ) { //for gold braid border
					echo "<br />Gold Braid Border: <input disabled name=\"amounts[";
					echo $key . "][GB]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[3] == 12 || $ribbon[4] == 12 ) { //for arrowhead device
					echo "<br />Arrowhead Device: <input disabled name=\"amounts[";
					echo $key . "][AH]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[4] == 17 ) { //for FMF device
					echo "<br />FMF Device: <input disabled name=\"amounts[";
					echo $key . "][FMF]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[4] == 18 ) { //for FMF AND Arrowhead device
					echo "<br />Arrowhead Device: <input disabled name=\"amounts[";
					echo $key . "][AH]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
					echo "<br />FMF Device: <input disabled name=\"amounts[";
					echo $key . "][FMF]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[3] == 19 ) { //for M device
					echo "<br />M Device: <input disabled name=\"amounts[";
					echo $key . "][M]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				if ( $ribbon[3] == 20 ) { //for Berlin Airlift Device device
					echo "<br />Berlin Airlift Device: <input disabled name=\"amounts[";
					echo $key . "][BAD]\" type=\"checkbox\" value=\"1\" canDisable=\"1\" />";
				}
				
			}
			else {
				echo " <input disabled type=\"hidden\" name=\"amounts[";
				echo $key . "][amount]\" value=\"\" canDisable=\"1\" />";
			}
  		echo "<br />";
		}
		if ($wiki_link) {
			echo "<a href=\"" . $wiki_URL_prepend . str_replace(" ", "_", $ribbon[0]) . "\">";
		}
		echo $ribbon[0];
		if ($wiki_link) {
			echo "</a>";
		}
		if ( $ribbon[10] == 1) { 
			echo "&nbsp;&nbsp;&nbsp;<font color=\"red\">(Right Side Army)</font>\n";
		}
		
		echo "\n          </td>\n";
		
		if ( $i == 3 ) { 
			echo "        </tr>\n"; 
			$i=-1;
		}
		$i++;
	}
	echo "      </table>\n    </td>\n  </tr>\n</table>";
	unset ($support_doc_filename, $support_doc_title, $support_doc_date, $support_doc_type );
}

function temp_with_accouterments ( $award , $number_of_awards , $input_array) {
	//global the correct input array;
	global ${$input_array}, $images_directory, $temp_directory, $temp_full_path, $device_types;

	if ( !is_array(${$input_array}) ) { 
		$input_array = "master_ribbon_array";
		$master_ribbon_array = make_master_ribbon_array ();
	}
	
	if ( rand(1,25) == 1 ) {  //clean up the temp directory (1 in 25 chance)
		clean_up_temp_directory ( $temp_full_path );
	}

	//get award URL
	if ( array_key_exists($award, ${$input_array})){
		$medal_image_url = ${$input_array}[$award][1];
	}
	else { echo "Medal key does not exist?"; }

	//get accouterment type and URL
	$accouterment_1 = $device_types[${$input_array}[$award][3]];
	$accouterment_2 = $device_types[${$input_array}[$award][4]];
	
	//echo "<p><b>$accouterment_2</b></p>";

	switch ($accouterment_1) {
		case "oak_leaf":
		case "star":
		case "small_stars":
		case "campaign_stars":
		case "olc_stars":
		case "lozenge":
		case "silver_stars_maine":
 		  $calculation = "div5";
			break;
		case "loop":
			$calculation = "loop";
			break;
		case "M_only":
		case "berlin_airlift_device":
			$calculation = "one_and_only_one";
			break;
		case "numeral":
			$calculation = "numeral";
			break;
		case "AFRAM":
			$calculation = "AFRAM";
			break;
		case "navy_battle_e":
			$calculation = "navy_battle_e";
			break;
		case "wintered_over":
			$calculation = "wintered_over";
			break;
		case "laurel_leaf":
			$calculation = "nbsg"; //none - bronze - silver - gold
			break;
		default:
			$calculation = "";
			break;
	}
	
	//echo "<p><b>" . $accouterment_2 ."</b></p>";

	if ( $accouterment_2 == "V_device" && $number_of_awards[V]=='1') { 
		//need to determine if valor device was checked
		$valor = true;
	}
	elseif ( $accouterment_2 == "A_device" && $number_of_awards[A]=='1') { 
		//need to determine if A device was checked
		$arctic = true;
	}
	elseif ( $accouterment_2 == "gold_border" && $number_of_awards[GB]=='1') { 
		//need to determine if gold braid
		$gold_border = true;
	}
	elseif ( $accouterment_2 == "arrowhead" && $number_of_awards[AH]=='1') { 
		//need to determine if it gets an arrowhead
		$arrowhead = true;
	}
	elseif ( $accouterment_2 == "fmf_arrowhead" || $accouterment_2 == "fmf" ) { 
		if ( $number_of_awards[AH]=='1' ) {
  		$arrowhead = true;
		}
		if ( $number_of_awards[FMF]=='1') { 
			$fmf = true;
		}
	}

	if ( ${$input_array}[$award][10] == 1) { 
		//need to determine if gold braid
		if ($_REQUEST[service]==1 || $award == "JMUA"){
			$gold_border = true;
		}
		else { //$_REQUEST[service]>1
			$gold_border = false;
		}
		
	}

	//create main image pallete to work on, start with the base ribbon
	$temp_graphic_name = "temp_image_" . mt_rand() . mt_rand() . ".png";
	
	$medal_sz = getimagesize($medal_image_url);
	
	${$temp_graphic_name} = imagecreate($medal_sz[0], $medal_sz[1])
		or die("Cannot Initialize new GD image stream");  //make palette for medal

	$medal = imagecreatefrompng($medal_image_url)
		or die ("nope - medal");  //get the base medal
		
	imagecopy(${$temp_graphic_name},$medal,0,0,0,0,$medal_sz[0],$medal_sz[1]); //put base medal on palette
	
	$m_img_w  =  $medal_sz[0];	 	## width
	$m_img_h  =  $medal_sz[1];		## height

	//add accouterments	
	if ( $calculation == "div5" || $award=="GWOTEM" || $award=="AFSM" || $award=="ICM" || $award=="AfCM" ) {
	
		//those medals with campaign stars get one for all awards others get one less
		if ( $accouterment_1 != "campaign_stars" ) {
			$number_of_awards[amount]--; //number of accouterments is 1 less than number of awards
		}

		switch ($accouterment_1) {
			case "oak_leaf":
				$five = $images_directory . "silver_oak_leaf.png";
				$single = $images_directory . "bronze_oak_leaf.png";
				break;
			case "star":
				$five = $images_directory . "silver_star_accouterment.png";
				$single = $images_directory . "bronze_star_accouterment.png";
				break;
			case "small_stars":
			case "campaign_stars":
				$five = $images_directory . "small_silver_star_appurtenance.png";
				$single = $images_directory . "small_bronze_star_appurtenance.png";
				break;
			case "lozenge":
				$five = $images_directory . "bronze_lozenge.png";
				$single = $images_directory . "bronze_lozenge.png";
				break;
			case "silver_stars_maine":
				$five = $images_directory . "small_silver_star_appurtenance.png";
				$single = $images_directory . "small_silver_star_appurtenance.png";
				break;
			case "olc_stars":
				if ($_REQUEST[service]==1 || $_REQUEST[service]==4){
					$five = $images_directory . "silver_oak_leaf.png";
					$single = $images_directory . "bronze_oak_leaf.png";
				}
				elseif ($_REQUEST[service]>1 && $_REQUEST[service]!=4){
					$five = $images_directory . "silver_star_accouterment.png";
					$single = $images_directory . "bronze_star_accouterment.png";
				}
				break;
		}

		//accouterments single and five MUST BE same width!!!!!!!!
		$accouterment_sz =  getimagesize($single);
		  
		$a_img_w  =  $accouterment_sz[0];	 	## width
		$a_img_h  =  $accouterment_sz[1];		## height
			
		if ( $number_of_awards[amount] > 0 ) { 

				//checking against zero because we've already -- the amount 
				//to line up with the number of appurtenances
				
      $total_items = (floor($number_of_awards[amount]/5))+$number_of_awards[amount]%5;
      $offset = $total_items / 2;
			
			$number_of_fives = floor($number_of_awards[amount]/5);
    	$number_of_singles = $number_of_awards[amount]%5;
		}
		else {
			$offset = 0;
		}
    
    $placement_x = ($m_img_w/2)-($a_img_w*$offset);
    $placement_y = ($m_img_h/2)-($a_img_h/2);
    

		
		//add some more offset for the valor device 
		//and put the valor device on the ribbon
		if ( $valor ) {
			$valor_image = $images_directory . "bronze_v_device.png";
			
			$valor_image_sz =  getimagesize($valor_image);
			
			$valor_w  =  $valor_image_sz[0];	 	## width
			$valor_h  =  $valor_image_sz[1];		## height
			
			$valor_placement_x = $placement_x - ($valor_w/2);
			$valor_placement_y = ($m_img_h/2)-($valor_h/2);
			
			$temp_valor = imagecreatefrompng($valor_image)
				or die ("nope - cannot get valor device");
			imagecolortransparent ($temp_valor, imagecolorat ($temp_valor, 0, 0));
			ImageCopyMerge(${$temp_graphic_name}, $temp_valor, $valor_placement_x, $valor_placement_y, 0,0, $valor_w, $valor_h, 100);

			$placement_x = $valor_placement_x + $valor_w;
		}
		
		if ( $arctic ) {
		//echo "Where is arctic?<br />";
			$A_image = $images_directory . "bronze_A_device.png";
			
			$A_image_sz =  getimagesize($A_image);
			
			$A_w  =  $A_image_sz[0];	 	## width
			$A_h  =  $A_image_sz[1];		## height
			
			$A_placement_x = $placement_x - ($A_w/2);
			$A_placement_y = ($m_img_h/2)-($A_h/2);
			
			$temp_A = imagecreatefrompng($A_image)
				or die ("nope - cannot get 'A' device");
			imagecolortransparent ($temp_A, imagecolorat ($temp_A, 0, 0));
			ImageCopyMerge(${$temp_graphic_name}, $temp_A, $A_placement_x, $A_placement_y, 0,0, $A_w, $A_h, 100);

			$placement_x = $A_placement_x + $A_w;
		}

		if ( $arrowhead || $fmf ) {
			if ( $arrowhead ) {
				$ah_image = $images_directory . "arrowhead.png";
			
				$ah_image_sz =  getimagesize($ah_image);
			
				$ah_w  =  $ah_image_sz[0] + 2;	 	## width
				$ah_h  =  $ah_image_sz[1];		## height
			
				$ah_placement_y = ($m_img_h/2)-($ah_h/2);
				$placement_x = $placement_x - ($ah_w/2);
			}
			if ( $fmf ) {
				$fmf_image = $images_directory . "fmf.png";
			
				$fmf_image_sz =  getimagesize($fmf_image);
			
				$fmf_w  =  $fmf_image_sz[0] + 2;	 	## width
				$fmf_h  =  $fmf_image_sz[1];		## height
			
				$fmf_placement_y = ($m_img_h/2)-($fmf_h/2);
				$placement_x = $placement_x - ($fmf_w/2);
			}
			
			if ( $arrowhead ) {
				$temp_ah = imagecreatefrompng($ah_image)
					or die ("nope - cannot get arrowhead image");
				imagecolortransparent ($temp_ah, imagecolorat ($temp_ah, 0, 0));
				ImageCopyMerge(${$temp_graphic_name}, $temp_ah, $placement_x, $ah_placement_y, 0,0, $ah_w-2, $ah_h, 100);

				$placement_x += $ah_w;
			}
			if ( $fmf ) {
				$temp_fmf = imagecreatefrompng($fmf_image)
					or die ("nope - cannot get arrowhead image");
				imagecolortransparent ($temp_fmf, imagecolorat ($temp_fmf, 0, 0));
				ImageCopyMerge(${$temp_graphic_name}, $temp_fmf, $placement_x, $fmf_placement_y, 0,0, $fmf_w-2, $fmf_h, 100);

			$placement_x += $fmf_w;

			} 
		}
		
		//place the div5 items
		if ( $number_of_fives > 0 ) { 
			//appertenances single and five MUST BE same width!!
			$accouterment_five = imagecreatefrompng($five)
				or die ("nope - accouterment_five");
			imagecolortransparent ($accouterment_five, imagecolorat ($accouterment_five, 0, 0));
			
			while ( $number_of_fives != 0 ) {
				ImageCopyMerge(${$temp_graphic_name}, $accouterment_five, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
				$placement_x += $a_img_w;
				$number_of_fives--;
			}
		}
		if ( $number_of_singles > 0 ) {
			$accouterment_one = imagecreatefrompng($single)
				or die ("nope - accouterment_one");
			imagecolortransparent ($accouterment_one, imagecolorat ($accouterment_one, 0, 0));
			
			while ( $number_of_singles != 0 ) {
				ImageCopyMerge(${$temp_graphic_name}, $accouterment_one, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
				$placement_x += $a_img_w;
				$number_of_singles--;
			}
		}
	}
	
	if ( $calculation == "one_and_only_one" ) {
		//echo "got to the calc";
			if ( $accouterment_1 == "M_only" ) {
				$image = $images_directory . "bronze_m_device.png";
			}
			if ( $accouterment_1 == "berlin_airlift_device" ) {
				$image = $images_directory . "berlin_airlift_device.png";
			}
			
			$image_sz =  getimagesize($image);
			
			$i_w  =  $image_sz[0];	 	## width
			$i_h  =  $image_sz[1];		## height
			
			$i_placement_x = ($m_img_w/2) - ($i_w/2);
			$i_placement_y = ($m_img_h/2)-($i_h/2);
			
			$temp_image = imagecreatefrompng($image)
				or die ("nope - cannot get image for device");
			imagecolortransparent ($temp_image, imagecolorat ($temp_image, 0, 0));
			ImageCopyMerge(${$temp_graphic_name}, $temp_image, $i_placement_x, $i_placement_y, 0,0, $i_w, $i_h, 100);

		}

	elseif ( $calculation == "loop" ) { //for loop device such as Army Good Conduct
	
		$loop_image_url =  $images_directory . "good_conduct_clasp_" . $number_of_awards[amount] . ".png";
		$accouterment_sz =  getimagesize($loop_image_url);
		  
		$a_img_w  =  $accouterment_sz[0];	 	// width
		$a_img_h  =  $accouterment_sz[1];		// height
		
		$placement_x = ($m_img_w/2)-($a_img_w/2);
		$placement_y = ($m_img_h/2)-($a_img_h/2);
		
		$accouterment = imagecreatefrompng($loop_image_url)
				or die ("nope - loops_file");
				
		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);

	}
	
	elseif ( $calculation == "nbsg" ) { //for none-bronze-silver-gold
		switch ($accouterment_1) {
			case "laurel_leaf":
				$bronze = $images_directory . "laurel_leaf_bronze.png";
				$silver = $images_directory . "laurel_leaf_silver.png";
				$gold = $images_directory . "laurel_leaf_gold.png";
				break;
		}
		switch ($number_of_awards[amount]) {
			case 2:
				$nbsg_image_url = $bronze;
				break;
			case 3:
				$nbsg_image_url = $silver;
				break;
			case 4:
				$nbsg_image_url = $gold;
				break;
			default:
				$nbsg_image_url = "none";
				break;
		}
		
		if ( $nbsg_image_url != "none" ) {
  		$accouterment_sz =  getimagesize($nbsg_image_url);
  		  
  		$a_img_w  =  $accouterment_sz[0];	 	// width
  		$a_img_h  =  $accouterment_sz[1];		// height
  		
  		$placement_x = ($m_img_w/2)-($a_img_w/2);
  		$placement_y = ($m_img_h/2)-($a_img_h/2);
  		
  		$accouterment = imagecreatefrompng($nbsg_image_url)
  				or die ("nope - laurel_file");
  				
  		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
  		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
		}
	}
	
	elseif ( $calculation == "numeral" ) { //for numerals
		if ( $number_of_awards != 1 ) {
			$number_of_numerals = $number_of_awards[amount]; // always true????
  		for ($i = 0 ; $i < strlen($number_of_numerals) ; $i++){
     		$numbers[]= $number_of_numerals{$i};
   		}
  		//print_r ($numbers);
  		
  		foreach ($numbers as $not_used=>$this_number) { //determine the offset
  			$numeral_image_url =  $images_directory . "numeral" . $this_number . ".png";
  			$accouterment_sz =  getimagesize($numeral_image_url);
    		  
      		$a_img_w  =  $accouterment_sz[0];	 	// width of numeral
      		
      		$offset_x += $a_img_w/2;
  		}
  		reset($numbers);
  		
  		$placement_x = ($m_img_w/2) - $offset_x - (strlen($number_of_numerals) - 1);
  		
  		foreach ($numbers as $this_number) { //put the numbers on ribbon
  			$numeral_image_url =  $images_directory . "numeral" . $this_number . ".png";
      		$accouterment_sz =  getimagesize($numeral_image_url);
      		  
      		$a_img_w  =  $accouterment_sz[0];	 	// width
      		$a_img_h  =  $accouterment_sz[1];		// height
      		
      		$placement_y = ($m_img_h/2)-($a_img_h/2);
  			
  			$accouterment = imagecreatefrompng($numeral_image_url)
    				or die ("nope - no numeral exists");
    				
    			imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
    			ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
  
  			$placement_x += ($a_img_w + 2);	
  		}
		}
	}
	
	elseif ( $calculation == "navy_battle_e" ) { //for numerals
		if ( $number_of_awards[amount] != 1 ) {
			if ( $number_of_awards[amount] < 4 ) { //add Es appropriately
			
				$battle_e =  $images_directory . "navy_battle_e.png";
				$accouterment_sz =  getimagesize($battle_e);
  		  
  			$a_img_w  =  $accouterment_sz[0];	 	// width
  			$a_img_h  =  $accouterment_sz[1];		// height
				
				$placement_x = ($m_img_w/2)-(($number_of_awards[amount]*$a_img_w)/2);
				$placement_y = ($m_img_h/2)-($a_img_h/2);
				
				$accouterment = imagecreatefrompng($battle_e)
				or die ("nope - battle_e");
				
				while ( $number_of_awards[amount] != 0 ) {
					ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
					$placement_x += $a_img_w;
					$number_of_awards[amount]--;
				}
			}
			else { //add wreath to battle e
				$battle_e_wreath =  $images_directory . "navy_battle_e_wreath.png";
				$accouterment_sz =  getimagesize($battle_e_wreath);
  		  
  			$a_img_w  =  $accouterment_sz[0];	 	// width
  			$a_img_h  =  $accouterment_sz[1];		// height
				
				$placement_x = ($m_img_w/2)-($a_img_w/2);
				$placement_y = ($m_img_h/2)-($a_img_h/2);
				
				$accouterment = imagecreatefrompng($battle_e_wreath)
				or die ("nope - battle_e_wreath");

				ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
			
			}
		}
	}
	
	elseif ( $calculation == "AFRAM" ) { //for numerals
		if ( $number_of_awards[M] != '' && $number_of_awards[M] > 0 ) { //gotta add the M device, center!
			
  		$image_url =  $images_directory . "bronze_m_device.png";
  		$accouterment_sz =  getimagesize($image_url);
  		  
  		$a_img_w  =  $accouterment_sz[0];	 	// width
  		$a_img_h  =  $accouterment_sz[1];		// height
  		
  		$placement_x = ($m_img_w/2)-($a_img_w/2);
  		$placement_y = ($m_img_h/2)-($a_img_h/2);
  		
  		$accouterment = imagecreatefrompng($image_url)
  				or die ("nope - M device");
  				
  		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
  		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);

			if ( $number_of_awards[M] > 1 ) {
				$number_of_numerals = $number_of_awards[M]; // always true????
    		$numeral_image_url =  $images_directory . "numeral" . $number_of_numerals . ".png";
    		$accouterment_sz =  getimagesize($numeral_image_url);
    		  
    		$a_img_w  =  $accouterment_sz[0];	 	// width
    		$a_img_h  =  $accouterment_sz[1];		// height
    		
    		$placement_x = 3*($m_img_w/4)-($a_img_w/2);
    		$placement_y = ($m_img_h/2)-($a_img_h/2);
    		
    		$accouterment = imagecreatefrompng($numeral_image_url)
    				or die ("nope_numeral");
    				
    		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
    		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
			}
		}

		if ( $number_of_awards[H] != 'n' ) {
			switch ($number_of_awards[H]) {
    		case "b":
					$image_url =  $images_directory . "bronze_hourglass.png";
					break;
    		case "s":
     		  $image_url =  $images_directory . "silver_hourglass.png";
    			break;
				case "g":
     		  $image_url =  $images_directory . "gold_hourglass.png";
    			break;
				}
			$accouterment_sz =  getimagesize($image_url);
    		  
  		$a_img_w  =  $accouterment_sz[0];	 	// width
  		$a_img_h  =  $accouterment_sz[1];		// height
  		
			
  		$placement_x = ( $number_of_awards[M] > 0 ) ? ($m_img_w/4)-($a_img_w/2) : ($m_img_w/2)-($a_img_w/2);
  		$placement_y = ($m_img_h/2)-($a_img_h/2);
  		
  		$accouterment = imagecreatefrompng($image_url)
  				or die ("nope - hourglass");
  				
  		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
  		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
			
		
		}

	}
	
	elseif ( $calculation == "wintered_over" ) { //for wintered_over discs
		if ( $number_of_awards[H] != 'n' ) {
			switch ($number_of_awards[H]) {
    		case "b":
					$image_url =  $images_directory . "WObronzesmall.png";
					break;
    		case "s":
     		  $image_url =  $images_directory . "WOsilversmall.png";
    			break;
				case "g":
     		  $image_url =  $images_directory . "WOgoldsmall.png";
    			break;
				}
			$accouterment_sz =  getimagesize($image_url);
    		  
  		$a_img_w  =  $accouterment_sz[0];	 	// width
  		$a_img_h  =  $accouterment_sz[1];		// height
  		
			
  		$placement_x =  $m_img_w/2 - $a_img_w/2;
  		$placement_y = ($m_img_h/2)-($a_img_h/2);
  		
  		$accouterment = imagecreatefrompng($image_url)
  				or die ("nope - wintered_over");
  				
  		imagecolortransparent ($accouterment, imagecolorat ($accouterment, 0, 0));
  		ImageCopyMerge(${$temp_graphic_name}, $accouterment, $placement_x, $placement_y, 0,0, $a_img_w, $a_img_h, 100);
			
		
		}

	}
	
	else {	}
	
	if ( $gold_border ) {
    $gb_image = $images_directory . "gold_border_small.png";
    
    $temp_gb = imagecreatefrompng($gb_image)
    	or die ("nope - cannot get gold border");
    imagecolortransparent ($temp_gb, imagecolorat ($temp_gb, 50, 15));
    ImageCopyMerge(${$temp_graphic_name}, $temp_gb, 0, 0, 0,0, 105, 30, 100);
	}
	
	if ( calculation == "" ) { 
		return $medal_image_url;
	}
	else {
  	$storage_url = $temp_full_path . $temp_graphic_name;
  
  	ImagePNG(${$temp_graphic_name}, $storage_url) or 
  		die("nope - could not store");

  	$temp_graphic_url = $temp_directory . $temp_graphic_name;
  	return $temp_graphic_url;
	}
	
	@imagedestroy(${$temp_graphic_name});
	@imagedestroy($medal);
	@imagedestroy($accouterment);
}  //END FUNCTION temp_with_accouterments


function clean_up_temp_directory ( $temp_full_path ) { //used to clean up temp files
  $dir = $temp_full_path;
  $expired_time = time() - 60;  //1 minute ago
  
  if (is_dir($dir)) {
  	if ($dh = opendir($dir)) {
  		while (($file = readdir($dh)) !== false) {
  			if ( filetype($dir . $file) == "file" && filemtime($dir . $file) < $expired_time && $file != "index.php" ) {
  				unlink ($dir . $file);
  			}
  		}
  		closedir($dh);
  	}
  }
}  //END FUNCTION clean_up_temp_directory

function clean_up_rack_directory ( $racks_full_path ) { //used to clean up temp files
	global $rack_longevity;
  $dir = $racks_full_path;
  $expired_time = time() - $rack_longevity*60*60;  //6 hours ago

  if (is_dir($dir)) {
  	if ($dh = opendir($dir)) {
  		while (($file = readdir($dh)) !== false) {
  			if ( filetype($dir . $file) == "file" && filemtime($dir . $file) < $expired_time && $file != "index.php" ) {
  				unlink ($dir . $file);
  			}
  		}
  		closedir($dh);
  	}
  }
}  //END FUNCTION clean_up_rack_directory

function make_right_side_rack ($ribbon_array, $max_width_ribbons = 3 ) {
  

}

function make_rack ( $ribbon_array, $max_width_ribbons = 3 ) { //makes the final rack
	global $rack_directory, $space_between_ribbons, $service_input, $html_code, $clean_up_frequency, $tagline_text_default, $tagline_default_url, $tagline_popup_text;
	$txt_img_map = "<map name=\"rack\" id=\"rack\">\n"; //set holder for image map entity
	$txt_spans = "";  //set holder for the spans which will hold the ribbon names
	
	if ( $_REQUEST['space'] != '' ) {
		$space_between_ribbons = $_REQUEST['space'];
	}
	
	if ( rand(1,$clean_up_frequency) == 1 ) {  //frequency of clean-up of rack directory
		clean_up_rack_directory ( $rack_directory );
	}
	
	$master_ribbon_array = make_master_ribbon_array ();
	
	//how many ribbons we dealing with here?
	//********need to get the right side army ribbons out of array!!!!!!
	//get the right side ribbons out of the left side array 
	//but only if it's service == 1 which is Army
	if ( $_REQUEST['service'] == 1 ) { 
		foreach ( $ribbon_array as $ribbon=>$amount ) {
  		//make sure ribbon belongs in the main rack
  		if ( $master_ribbon_array[$ribbon][10] == 1 ) {
				$right_side_ribbons[$ribbon] = $amount; 
				unset($ribbon_array[$ribbon]);
			}
		}
	}
	
	$number_of_ribbons = count($ribbon_array);

	//print_r ($ribbon_array);
	//echo "<br />";
	//print_r ($right_side_ribbons);
	
	if ( $number_of_ribbons > 0 ) { //we got some ribbons
	
  	//find the number of ribbons on the top row of rack
  	$top_row_count = $number_of_ribbons % $max_width_ribbons; 
  
  	$sample_ribbon = $master_ribbon_array[key($master_ribbon_array)][1];
  
  	$sample_ribbon_size =  getimagesize($sample_ribbon);
  		
  	$rack_width = determine_rack_width ( $number_of_ribbons, $sample_ribbon_size[0], $max_width_ribbons );
  	$rack_height = determine_rack_height ( $number_of_ribbons, $sample_ribbon_size[1], $max_width_ribbons );
  
  	$rack = imagecreate($rack_width, $rack_height) 
  		or die("Cannot Initialize new GD image stream");

		//$background_color = imagecolorallocatealpha($rack, 0, 255, 0, 127);
		
		$background_color = imagecolorallocate($rack, 0, 255, 0);
  	
		imagecolortransparent ( $rack , $background_color);
  	
  	//put ribbons in order
  	uksort($ribbon_array, 'compare_final');
  
  	//starting point of first image to be placed
  	$dst_x = $rack_width - $sample_ribbon_size[0];
  	$dst_y = $rack_height - $sample_ribbon_size[1];
  	
  	//set $row_tracker, tracks how many ribbons have been placed in a row
  	$row_tracker = 1;
  	$top_row_tracker = 1;
  	$number_of_rows = ceil($number_of_ribbons/$max_width_ribbons);

		$k = 1; //variable for holding the PopUp number for image map
  	foreach ( $ribbon_array as $ribbon=>$amount ) {

  		$ribbon_acc1 = $master_ribbon_array[$ribbon][3];
  		$ribbon_acc2 = $master_ribbon_array[$ribbon][4];

  		//see if it needs an accouterment and run if so
  		if ( $amount[amount] == 1 && $ribbon_acc1 != 8 && $ribbon_acc2 != 12 && $ribbon_acc2 != 17 && $ribbon_acc2 != 18 && $amount[V] != 1 && $amount[A] != 1 && $amount[GB] != 1 && $master_ribbon_array[$ribbon][10]!=1 && $amount[BAD] != 1 && $amount[M] != 1 ) {
  			$the_ribbon = $master_ribbon_array[$ribbon][1];
  		}
  		elseif ( $ribbon_acc1 == '' && $ribbon_acc2 == '' && $master_ribbon_array[$ribbon][10]!=1 ) {
  			$the_ribbon = $master_ribbon_array[$ribbon][1];
  		}
  		else {
  			$the_ribbon = temp_with_accouterments ( $ribbon, $amount, "master_ribbon_array" );
  		}
  		
  		//get image into gd
  		$paste_me = imagecreatefrompng($the_ribbon);
  
  		//copy ribbon onto rack
  		ImageCopyMerge($rack, $paste_me, $dst_x, $dst_y, 0,0, $sample_ribbon_size[0], $sample_ribbon_size[1], 100);

			$txt_img_map .= "<area shape=\"rect\" coords=\"" . $dst_x . "," . $dst_y;
			$txt_img_map .= ",";
			$txt_img_map .= $dst_x+$sample_ribbon_size[0]; //bottom right x iamge map coord
			$txt_img_map .= ",";
			$txt_img_map .= $dst_y + $sample_ribbon_size[1]; //bottom right y iamge map coord
			$txt_img_map .= "\" href=\"#\" ";
			$txt_img_map .= "onMouseout=\"nd();\" ";		
			$txt_img_map .= "onMouseover=\"return overlib('";
			$txt_img_map .= str_replace ("'","\'",$master_ribbon_array[$ribbon][0]);
			$txt_img_map .= "')\"";
			$txt_img_map .= " />\n";
  
  		//clean up stuff
  		imagedestroy($paste_me);
			
			//dst_y determiner
			if ( $row_tracker == $max_width_ribbons ) {
    		$row_tracker = 1;
				$dst_y -= $sample_ribbon_size[1]+$space_between_ribbons;
				$top_row_tracker++;
    	} else { $row_tracker ++; }
			
			//dst_x determiner
			//top or only row AND not full?
			if ( $number_of_rows == $top_row_tracker && $top_row_count != 0 ) {
				$top_row_offset = (($rack_width/2) + ((($top_row_count * $sample_ribbon_size[0]) + (($top_row_count-1) * $space_between_ribbons))/2))-$sample_ribbon_size[0];

				if ( $dst_x == 0 ) {
					$dst_x = $top_row_offset;
				}
				else {
					$dst_x -= $sample_ribbon_size[0]+$space_between_ribbons;
				}	
			}
			//not top or only row or full top row
			else {
				if ( $dst_x != 0 ) {
    			$dst_x -= $sample_ribbon_size[0]+$space_between_ribbons;
    		}
				else { 
					$dst_x = $rack_width - $sample_ribbon_size[0]; 
				}
			}
			
			$k++;
  	}
		
		//kick back the rack!
  	$storage_url = $rack_directory . "rack_" . mt_rand() . mt_rand() . ".png";

		if ($_REQUEST[tagline]) {
  		$final_product = imagecreate($rack_width, $rack_height+15) 
    		or die("Cannot Initialize new GD image stream for final image");
    		
    	$background_color = imagecolorallocate($final_product, 0, 255, 0);
    	
    	imagecolortransparent ( $final_product , $background_color);
  		
  		ImageCopyMerge($final_product, $rack, 0, 0, 0,0, $rack_width, $rack_height, 100);
  		
  		$grey = imagecolorallocate($final_product, 172, 172, 172);
  		$white = imagecolorallocate($final_product, 255, 255, 255);
  		
  		imagefilledrectangle ( $final_product, 0, $rack_height+1, $rack_width, $rack_height+15, $grey );
  		$text = $tagline_text_default;
  		$font = "/home/ajandjco/public_html/ultimaterack/includes/arial.ttf";
  		$sz_font = 9;
  		$sz_text = imagettfbbox($sz_font,0,$font,$text);
  		
  		$placement_x = $rack_width/2-(($sz_text[4]-$sz_text[5])/2);
  
  		imagettftext ( $final_product, $sz_font, 0, $placement_x, $rack_height+11, $white, $font, $text );
  		ImagePNG($final_product, $storage_url)
  		 or die("nope - could not store"); // for other, $final_product

			$rh1=$rack_height+1;
			$rh2=$rack_height+15;
  	
			$txt_img_map .= "<area shape=\"rect\" coords=\"0,".$rh1.",";
			$txt_img_map .= $rack_width .  "," . $rh2;
			$txt_img_map .= "\" href=\"" . $tagline_default_url . "\" onMouseout=\"nd();\" onMouseover=\"return overlib('";
			$txt_img_map .= $tagline_popup_text . "')\" target=\"new.Window\"/>\n";

			imagedestroy($final_product);
		}
		else {
			ImagePNG($rack, $storage_url)
  		 or die("nope - could not store");
			 
			imagedestroy($rack);
		}
		
		$txt_img_map .= "</map>\n\n";
		echo $txt_img_map;
		
		$html_code .= "<!-- Begin UltimateRack Code\n";
		$html_code .= "Courtesy of http://ultimaterack.sf.net -->\n\n";
		$html_code .= "<script src=\"overlib.js\" ";
		$html_code .= "type=\"text/javascript\"></script>\n\n";
		
		$html_code .= $txt_img_map;
		
		$html_code .= "<img usemap=\"#rack\" border=\"0\" ";
		$html_code .= "src=\"WHAT_YOU_NAMED_YOUR_RACK_GRAPHIC.png\" />\n";
		$html_code .= "<!-- End UltimateRack Code -->";

  	return $storage_url;
	} // end the if determining that we got some ribbons
	
	else { //we didn't get any ribbons!
		echo "<p class=\"important\"><b>I DIDN'T GET ANY RIBBONS</b></p>";
		return "images/no_ribbons_selected.png";
	}
	
	// MAKE RIGHT SIDE FOR ARMY
	
	
	
	
	
	
} // END FUNCTION make_rack

function make_master_ribbon_array () {
	global $path_to_ribbon_sets, $ribbon_directory_name, $set_name, $$file;
	$master_ribbon_array = array();
	
	if ($handle = opendir($path_to_ribbon_sets)) {
    while (false !== ($file = readdir($handle))) {
			if ( $file != "." && $file != ".." && $file != "index.php" ) {
				$ribbon_inc_file = $ribbon_directory_name .  $file . "/ribbon_set.inc.php";
				include($ribbon_inc_file);
				foreach(${$file} as $key=>$value) {
					array_splice(${$file}[$key], 1, 1, "ribbon_sets/" . $file . "/" . ${$file}[$key][1] );
				}
				$master_ribbon_array = array_merge( $master_ribbon_array, $$file);
			}
    }
	}
	
	return $master_ribbon_array;
	
} //END FUNCTION make_master_ribbon_array

?>

