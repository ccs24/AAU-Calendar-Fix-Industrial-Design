<?php
$allVariables = 1;
$error = "";

// CHECK IF ALL THE REQUIRED VARIABLES ARE SENT WITH THE PAGE REQUEST
if (!isset($_GET["userid"])){
	$error .= "Variable userid is missing in url. ";
	$allVariables = 2;
}
if (!isset($_GET["authtoken"])){
	$error .= "Variable authtoken is missing in url. ";
	$allVariables = 3;
}
if (!isset($_GET["preset_what"])){
	$error .= "Variable preset_what is missing in url. ";
	$allVariables = 4;
}
if (!isset($_GET["preset_time"])){
	$error .= "Variable preset_time is missing in url. ";
	$allVariables = 5;
}
   
// DISPLAY ERROR AND USAGE EXPLANATION IF VARIABLES ARE MISSING
if ($allVariables != 1){
	echo "<div style=\"margin-left:20px;font-family: 'Ubuntu light','Open Sans Light',Verdana,Geneva,sans-serif\"><font color=\"red\">".$error."</font><br /><h1>Given url is not correct.</h1>\nPlease check the url. URL should look like:<br \>
	https://gerkevangarderen.nl/icalAAU.php?userid=XXXX&authtoken=XXXXX&preset_what=XXX&preset_time=XXXX <br /><br />URL correct, but still not working? Sorry! :( <br /> Contact me via <a href=\"https://gerkevangarderen.nl#contact\">my website</a> and I will try to solve it asap.";
	?>
	<p>If you have no idea what you're doing, please check the <a href="https://gerkevgarderen.github.io/AAU-Calendar-Fix-Industrial-Design/">explanation at GitHub.io</a>.</p>
	</div>
	<?php
}
else{
	// SET THE HEADER OF THE DOCUMENT TO ALLOW ICAL-READERS TO THINK IT IS A REGULAR .ICAL FILE
	header('Content-Type: text/plain');
	
	// GET THE ORIGINAL CALENDAR DATA FROM THE AAU MOODLE BOARD
	$data = file_get_contents('https://www.moodle.aau.dk/calendar/export_execute.php?userid='.htmlspecialchars($_GET["userid"]).'&authtoken='.htmlspecialchars($_GET["authtoken"]).'&preset_what='.htmlspecialchars($_GET["preset_what"]).'&preset_time='.htmlspecialchars($_GET["preset_time"]));

	// REMOVE UNNESSECARY INFORMATION OUT OF THE TOP OF THE iCAL
	$re = "/METHOD:PUBLISH\\r\\n/"; 
	$data = preg_replace($re, "", $data, 1);

	// REPLACE ALL NEWLINES TO SAME FORMAT
	$data = str_replace(array("\r\n"), "\n", $data);

	// REMOVE THE DESCRIPTION TEXT OF EVENTS (INCORRECT USE BY UNIVERSITY)
	$re = "/DESCRIPTION:\\n/"; 
	$data = preg_replace($re, "", $data);

	// SPLIT OP THE INFORMATION BY THE NEWLINE INDICATOR
	$keywords = preg_split("/\\n/", $data);
	$count = -1;
	// CYCLE TRHOUGH EACH KEYWORD AND REMOVE KEYWORD 
	// IF IT CONTAINS A TAB CHARACTER OR DESCRIPTION FIELD
	foreach ( $keywords as $value ){
		$count++;
		if (!strcmp($value,"\t") || (!strcmp($value,"DESCRIPTION:")) ){ 
			unset($keywords[$count]);
		}
	}
	
	// STICH THE KEYWORDS BACK TOGETHER TO ONE DATA VARIABLE
	$keywords = array_values($keywords);
	$data = implode("\r\n",$keywords);

	// SPLIT UP PER EVENT
	$array = explode("BEGIN:VEVENT",$data);

	// CYCLE THROUGH EACH EVENT AND FIX THE INFORMATION
	$count = -1;
	foreach ( $array as $string ){
		$count++;
		$bool = 0;
		$boolTwo = 0;
		if ( substr_count($string,"DESCRIPTION") > 0){
			$re = "/(?<=DESCRIPTION:)(?s)(.*)(?=CLASS:PUBLIC)/"; 
			preg_match($re, $string, $matches);
			
			$re = "/DESCRIPTION:(?s)(.*)(?=CLASS:PUBLIC)/"; 
			$subst = ""; 
			$string = preg_replace($re, $subst, $string);
			
			$bool = 1;
		}
		
		// SAVE THE TEACHER INFORMATION FROM THE TITLE,
		// THEN REMOVE. IT WILL BE ADDED LATER.
		$re = "/(?<=Teacher:)(?s)(.*)(?=CLASS:PUBLIC)/";
		preg_match($re, $string, $teacherMatch);
		
		$re = "/ - Teacher:(?s)(.*)(?=CLASS:PUBLIC)/"; 
		$subst = "\r\n"; 
		$string = preg_replace($re, $subst, $string, 1);
		
		// REMOVE THE END OF TIME AND PLACE DESCRIPTION AND REPLACE WITH PROPER
		// ICAL NOTATION FOR LOCATION
		$re = "/ - Time: (?s)(.*) - Place: /U"; 
		$subst = "\nLOCATION:"; 
		$string = preg_replace($re, $subst, $string);

		// IN CASE THE LOCATION IS WRITTEN DIFFERENTLY
		// ALSO REPLACES WITH THE CORRECT ICAL NOTATION
		$re = "/ - Place: /"; 
		$subst = "\nLOCATION:"; 
		$string = preg_replace($re, $subst, $string);
		
		$re = "/ - \\r\\nDESCRIPTION: /"; 
		$subst = "\nDESCRIPTION:"; 
		$string = preg_replace($re, $subst, $string);

		// REMOVE INFORMATION IN THE WRONG PLACE
		$re = "/Time.*(?=(LOCATION:))/sU";
		$string = preg_replace($re, "\r\n", $string);
		
		
		$substring = "\nDESCRIPTION:";
		if ($bool == 1){
			$substring .= trim($matches[0]) . "\\n\r\n";
		}
		if (!empty($teacherMatch)){ 
			if ($bool == 1){
				$substring .= "\t\\n";
			}
			$substring .= trim($teacherMatch[0]) ."\\n\t";
		}
		
		$re = "/ - Note(?s)(.*): \\n?/U"; 
		if (preg_match($re,$string) == 0){ 
			$re = "/(?=END:VEVENT)/"; 
			$string = preg_replace($re, $substring . "\r\n", $string);
		}
		else{
			$string = preg_replace($re, $substring . "\t", $string);
		}		
		
		if ( $count > 0){
			$re = "/DESCRIPTION:[[:blank:]](?=\\S)/"; 
			$subst = "DESCRIPTION:";
			$string = preg_replace($re, $subst, $string);
			
			$re = "/\\\\n[[:blank:]][[:blank:]]?(?=\\S)/"; 
			$subst = "\\n"; 
			$string = preg_replace($re, $subst, $string);
			
			$array[$count] = "BEGIN:VEVENT" . rtrim($string);
		}
		else{
			$array[$count] = rtrim($string);
		}
	}
	
	// ADD ALL THE EVENTS BACK TO ONE LONG VARIABLE
	$data = implode("\r\n",$array);
	// PRINT ALL ICAL DATA
	print_r ($data);
}
?>