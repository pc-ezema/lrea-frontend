<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('get_url_name')) { /* Check_for "get_url_name" */
function get_url_name($id, $table, $column) {
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_urlQuest = "SELECT url_name FROM $table WHERE $column = $id LIMIT 1";
	$urlQuest = mysqli_query($config, $query_urlQuest) or die(mysqli_error($config));
	$row_urlQuest = mysqli_fetch_assoc($urlQuest);
	$totalRows_urlQuest = mysqli_num_rows($urlQuest);
	
	return $row_urlQuest['url_name'];
}
} /* End_check for "get_url_name" */

if (!function_exists('profile_who')) { /* Check_for "profile_who" */
function profile_who($who_url = "###") {
	global $database_config, $config;
	if ($who_url == "###") {
	  return 0;
	} 
	else {
	  mysqli_select_db($config, $database_config);
	  $query_WhoToShow = "SELECT personal.user_id FROM personal JOIN login ON login.user_id = personal.user_id WHERE url_name = '".sql_string($who_url)."' AND login.status = 1 LIMIT 1";
	  $WhoToShow = mysqli_query($config, $query_WhoToShow) or die(mysqli_error($config));
	  $row_WhoToShow = mysqli_fetch_assoc($WhoToShow);
	  $totalRows_WhoToShow = mysqli_num_rows($WhoToShow);
	  
	  if ($totalRows_WhoToShow) {
		  return $row_WhoToShow['user_id'];
	  } 
	  else return 0;
	}
}
} /* End_check for "profile_who" */

if (!function_exists('profile_whatjob')) { /* Check_for "profile_whatjob" */
function profile_whatjob($whatjob_url = "###") {
	global $database_config, $config;
	if ($whatjob_url == "###") {
	  return 0;
	} 
	else {
	  mysqli_select_db($config, $database_config);
	  $query_WhatJobToShow = "SELECT job_id FROM job_postings WHERE url_name = '".sql_string($whatjob_url)."' OR code = '".sql_string($whatjob_url)."' AND job_approval = 'approved' LIMIT 1";
	  $WhatJobToShow = mysqli_query($config, $query_WhatJobToShow) or die(mysqli_error($config));
	  $row_WhatJobToShow = mysqli_fetch_assoc($WhatJobToShow);
	  $totalRows_WhatJobToShow = mysqli_num_rows($WhatJobToShow);
	  
	  if ($totalRows_WhatJobToShow) {
		  return $row_WhatJobToShow['job_id'];
	  } 
	  else return 0;
	}
}
} /* End_check for "profile_whatjob" */

if (!function_exists('profile_whatcomp')) { /* Check_for "profile_whatcomp" */
function profile_whatcomp($whatcomp_url = "###") {
	global $database_config, $config;
	if ($whatcomp_url == "###") {
	  return 0;
	} 
	else {
	  mysqli_select_db($config, $database_config);
	  $query_whatcompToShow = "SELECT company_id FROM employer WHERE url_name = '".sql_string($whatcomp_url)."' AND status = 'active' LIMIT 1";
	  $whatcompToShow = mysqli_query($config, $query_whatcompToShow) or die(mysqli_error($config));
	  $row_whatcompToShow = mysqli_fetch_assoc($whatcompToShow);
	  $totalRows_whatcompToShow = mysqli_num_rows($whatcompToShow);
	  
	  if ($totalRows_whatcompToShow) {
		  return $row_whatcompToShow['company_id'];
	  } 
	  else return 0;
	}
}
} /* End_check for "profile_whatcomp" */

if (!function_exists('make_slug')) { /* Check_for "make_slug" */
function make_slug($string, $table, $column) {
	$slug = create_slug($string);
	$new_slug = check_slug($slug, $table, $column);
	return $new_slug;
}
} /* End_check for "make_slug" */

if (!function_exists('check_slug')) { /* Check_for "check_slug" */
function check_slug($slug, $table, $column) {
	global $database_config, $config;
	
	if ($table == 'prospects') {
		if (illegal_url("checkit", $slug)) {
			$found_illegal = true;
		}
	}
	
	if (!$found_illegal) {
		mysqli_select_db($config, $database_config);
		$query_checking = "SELECT $column FROM $table WHERE $column = '$slug' LIMIT 1";
		$checking = mysqli_query($config, $query_checking) or die(mysqli_error($config));
		$row_checking = mysqli_fetch_assoc($checking);
		$totalRows_checking = mysqli_num_rows($checking);
	}
	
	if ($found_illegal || $totalRows_checking) {
		$slug = append_slug($slug);
		return check_slug($slug, $table, $column);
	} else return $slug;
	
	/*if ($found_illegal || $totalRows_checking) {
		return false;
	} else return $slug;*/
}
} /* End_check for "check_slug" */

if (!function_exists('append_slug')) { /* Check_for "append_slug" */
function append_slug($slug) {
	return $slug.'_'.rand(1,9);
}
} /* End_check for "append_slug" */

///////////////////////////////////////////////////////////////////////////////////////////////////
 
/* Call this function to create a slug from $string */
if (!function_exists('create_slug')) { /* Check_for "create_slug" */
function create_slug($string){
 $string = remove_accents($string);
 $string = symbols_to_words($string);
 $string = strtolower($string); // Force lowercase
 $space_chars = array(
  " ", // space
  "…", // ellipsis
  "–", // en dash
  "—", // em dash
  "/", // slash
  "\\", // backslash
  ":", // colon
  ";", // semi-colon
  ".", // period
  "+", // plus sign
  "#", // pound sign
  "~", // tilde
  "_", // underscore
  "|", // pipe
 );
 foreach($space_chars as $char){
  $string = str_replace($char, '-', $string); // Change spaces to dashes
 }
 // Only allow letters, numbers, and dashes
 $string = preg_replace('/([^a-zA-Z0-9\-]+)/', '', $string);
 $string = preg_replace('/-+/', '-', $string); // Clean up extra dashes
 if(substr($string, -1)==='-'){ // Remove - from end
  $string = substr($string, 0, -1);
 }
 if(substr($string, 0, 1)==='-'){ // Remove - from start
  $string = substr($string, 1);
 }
 return $string;
}
} /* End_check for "create_slug" */

/**
 * Borrowed from WordPress
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @since 1.2.1
 *
 * @param string $string Text that might have accent characters
 * @return string Filtered string with replaced "nice" characters.
 */
 
if (!function_exists('seems_utf8')) { /* Check_for "seems_utf8" */
function seems_utf8($str) {
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}
} /* End_check for "seems_utf8" */

if (!function_exists('remove_accents')) { /* Check_for "remove_accents" */
function remove_accents($string) {
 if(!preg_match('/[\x80-\xff]/', $string)){
  return $string;
 }
 if(seems_utf8($string)){
  $chars = array(
  // Decompositions for Latin-1 Supplement
  chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
  chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
  chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
  chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
  chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
  chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
  chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
  chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
  chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
  chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
  chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
  chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
  chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
  chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
  chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
  chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
  chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
  chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
  chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
  chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
  chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
  chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
  chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
  chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
  chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
  chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
  chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
  chr(195).chr(191) => 'y',
  // Decompositions for Latin Extended-A
  chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
  chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
  chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
  chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
  chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
  chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
  chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
  chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
  chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
  chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
  chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
  chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
  chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
  chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
  chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
  chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
  chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
  chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
  chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
  chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
  chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
  chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
  chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
  chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
  chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
  chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
  chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
  chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
  chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
  chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
  chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
  chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
  chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
  chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
  chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
  chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
  chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
  chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
  chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
  chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
  chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
  chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
  chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
  chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
  chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
  chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
  chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
  chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
  chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
  chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
  chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
  chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
  chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
  chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
  chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
  chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
  chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
  chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
  chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
  chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
  chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
  chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
  chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',

  chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
  // Euro Sign
  chr(226).chr(130).chr(172) => 'E',
  // GBP (Pound) Sign
  chr(194).chr(163) => '');
  $string = strtr($string, $chars);
 } else {
  // Assume ISO-8859-1 if not UTF-8
  $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
   .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
   .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
   .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
   .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
   .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
   .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
   .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
   .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
   .chr(252).chr(253).chr(255);
  $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
  $string = strtr($string, $chars['in'], $chars['out']);
  $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
  $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
  $string = str_replace($double_chars['in'], $double_chars['out'], $string);
 }
 return $string;
}
} /* End_check for "remove_accents" */

if (!function_exists('symbols_to_words')) { /* Check_for "symbols_to_words" */
function symbols_to_words($output){
 $output = str_replace('@', ' at ', $output);
 $output = str_replace('%', ' percent ', $output);
 $output = str_replace('&', ' and ', $output);
 return $output;
}
} /* End_check for "symbols_to_words" */

