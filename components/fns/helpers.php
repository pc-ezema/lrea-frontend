<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('my_is_int')) { /* Check_for "my_is_int" */
function my_is_int($var) {
    return preg_match('/^\d+$/', $var);
}
} /* End_check for "my_is_int" */

if (!function_exists('my_is_float')) { /* Check_for "my_is_float" */
function my_is_float($var) {
    return preg_match('/^\d+(\.\d+)?$/', $var);
}
} /* End_check for "my_is_float" */

if (!function_exists('my_is_amount')) { /* Check_for "my_is_amount" */
function my_is_amount($var) {
	$var = preg_replace("/[^0-9\.]/", "", $var);
    return preg_match('/^\d+(\.\d{1,2})?$/', $var);
}
} /* End_check for "my_is_amount" */

if (!function_exists('my_empty')) { /* Check_for "my_empty" */
function my_empty($value) {
	$new_val = trim($value);
	if (empty($new_val)) {
		return true;
	}
	elseif (!empty($new_val)) {
		return false;
	}
}
} /* End_check for "my_empty" */

if (!function_exists('my_trim')) { /* Check_for "my_trim" */
function my_trim($value) {
	if (preg_match("/^(\+|-)?[0-9]+[.]?[0-9]*?$/i", $value)) { // If value is a number, do nothing
		return $value;
	}
	elseif (preg_match("/^\s+(\+|-)?[0-9]+[.]?[0-9]*?$/i", $value)) { // If value is a number with space(s) before it, remove space(s)
		$value = preg_replace(array("/^\s+/i"), array(""), $value);
		return $value;
	}
	elseif (preg_match("/^(\+|-)?[0-9]+[.]?[0-9]*?\s+$/i", $value)) { // If value is a number with space(s) after it, remove space(s)
		$value = preg_replace(array("/\s+$/i"), array(""), $value);
		return $value;
	}
	else { // Whatever the value, remove all spaces before and after it
		$value = preg_replace(array("/^\s+/i", "/\s+$/i"), array("", ""), $value);
		return $value;
	}
}
} /* End_check for "my_trim" */

if (!function_exists('strlen_sort')) { /* Check_for "strlen_sort" */
function strlen_sort($a,$b){
    return strlen($b)-strlen($a);
}
} /* End_check for "strlen_sort" */

if (!function_exists('stripslashes_all')) { /* Check_for "stripslashes_all" */
function stripslashes_all($value) {
		$value = stripslashes($value);
		$newValue = preg_replace("/(\\\){1,}/","", $value);
    	return $newValue;
}
} /* End_check for "stripslashes_all" */

if (!function_exists('my_stripslashes')) { /* Check_for "my_stripslashes" */
function my_stripslashes($value) {
    	$value = stripslashes($value);
		$newValue = preg_replace("/(\\\){1,}[\']/","'", $value);
    	return $newValue;
}
} /* End_check for "my_stripslashes" */

if (!function_exists('nice_name')) { /* Check_for "nice_name" */
function nice_name($string) {
	$string = strtolower($string);
	$string = ucwords($string);
	
	return $string;
}
} /* End_check for "nice_name" */

if (!function_exists('course_name')) { /* Check_for "course_name" */
function course_name($string) {
	return ucwords($string);
}
} /* End_check for "course_name" */

if (!function_exists('clean_name')) { /* Check_for "clean_name" */
function clean_name($string) {
	$string = str_replace('&Acirc;&nbsp;',' ',htmlentities($string)); // Replace Â&nbsp; which is in the string
	$string = str_replace('&nbsp;',' ',$string); // Replace &nbsp; which is in the string
	$string = str_replace('&Acirc;','',$string); // Remove Â which is in the string
	$string = preg_replace("/\s+/i", " ", $string); // Reduce multiple spaces to 1
	$string = html_entity_decode($string); // Decode all html entities
	$string = strtolower($string); // Prepare name for first word capitalizing
	
	return ucwords(trim($string)); // Capitalize words
}
} /* End_check for "clean_name" */

if (!function_exists('clean_email')) { /* Check_for "clean_email" */
function clean_email($string) {
	$string = str_replace('&Acirc;&nbsp;',' ',htmlentities($string)); // Replace Â&nbsp; which is in the string
	$string = str_replace('&nbsp;',' ',$string); // Replace &nbsp; which is in the string
	$string = str_replace('&Acirc;','',$string); // Remove Â which is in the string
	$string = preg_replace("/\s+/i", " ", $string); // Reduce multiple spaces to 1
	$string = html_entity_decode($string); // Decode all html entities
	$string = strtolower($string); 
	
	return trim($string);
}
} /* End_check for "clean_email" */

if (!function_exists('shorten')) { /* Check_for "shorten" */
function shorten($string, $length = 5) {
	if (strlen($string) <= $length) {
		return $string;
	} elseif (strlen($string) > $length) {
		return substr($string, 0, $length-3)."...";
	}
}
} /* End_check for "shorten" */

if (!function_exists('list_sentence')) { /* Check_for "list_sentence" */
function list_sentence($arrVal) {
    if (is_array($arrVal)) {
        if (count($arrVal) == 2) {
            $value = $arrVal[0]." and ".$arrVal[1];
            return $value;
        }
         elseif (count($arrVal) > 2)
        {
            $newArrVal = array();
            for ($i=0; $i < count($arrVal)-1; $i++) {
                $newArrVal[] = $arrVal[$i];
            }
            $value = implode(", ", $newArrVal);
            $value .= " and ".$arrVal[count($arrVal)-1];
            return $value;
        }
         else return $arrVal[0];
    }
     else
    {
        return $arrVal;
    }
}
} /* End_check for "list_sentence" */

if (!function_exists('time_passed')) { /* Check_for "time_passed" */
function time_passed($timevalue){
	if (!$timevalue) return "";
	
	if (preg_match("/^\d{4}-\d{2}-\d{2}/", $timevalue)) $timevalue = strtotime($timevalue);
	
    $nowtime = time();
	//Calculate difference in secs
	$secs = $nowtime - $timevalue;
	
	if ($secs < 10) return "just now";
	
	$bit = array(
        ' year'        => $secs / 31556926 % 12,
		' month'        => $secs / 2629744 % 12,
        ' week'        => $secs / 604800 % 52,
        ' day'        => $secs / 86400 % 7,
        ' hr'        => $secs / 3600 % 24,
        ' min'    => $secs / 60 % 60,
        ' sec'    => $secs % 60
        );
        
    foreach($bit as $k => $v){
        if($v > 1)$ret[] = $v . $k . 's';
        if($v == 1)$ret[] = $v . $k;
    }
	array_splice($ret, 2);
    //array_splice($ret, count($ret)-1, 0, 'and');
    $ret[] = 'ago';
    
    return join(' ', $ret);
}
} /* End_check for "time_passed" */

if (!function_exists('access_time_left')) { /* Check_for "access_time_left" */
function access_time_left($end_time){
	if (!$end_time) return "";
	
	if (preg_match("/^\d{4}-\d{2}-\d{2}/", $end_time)) $end_time = strtotime($end_time);
	
	$nowtime = time();
	
	//Calculate difference in secs
	$secs = $end_time - $nowtime;
	
	if ($secs < 1800) { // 30 mins
		return "Course Expired";
	}
	
	$bit = array(
        ' year'        => $secs / 31556926 % 12,
		' month'        => $secs / 2629744 % 12,
        ' week'        => $secs / 604800 % 52,
        ' day'        => $secs / 86400 % 7,
        ' hr'        => $secs / 3600 % 24,
        ' min'    => $secs / 60 % 60,
        ' sec'    => $secs % 60
        );
        
    foreach($bit as $k => $v){
        if($v > 1)$ret[] = $v . $k . 's';
        if($v == 1)$ret[] = $v . $k;
    }
	array_splice($ret, 3);
    //array_splice($ret, count($ret)-1, 0, 'and');
    //$ret[] = 'left';
    
    return join(' ', $ret);
}
} /* End_check for "access_time_left" */

if (!function_exists('three_in_array')) { /* Check_for "three_in_array" */
function three_in_array($one, $two, $three) {
	$final_array = array();
	if (!my_empty($one)) {
		$final_array[] = trim($one);
	}
	if (!my_empty($two)) {
		$final_array[] = trim($two);
	}
	if (!my_empty($three)) {
		$final_array[] = trim($three);
	}
	for ($i=0; $i<=2; $i++) {
		if (!$final_array[$i] || my_empty($final_array[$i])) {
			$final_array[$i] = "NULL";
		}
	}
	return $final_array;
}
} /* End_check for "three_in_array" */

if (!function_exists('prepare_search_keywords')) { /* Check_for "prepare_search_keywords" */
function prepare_search_keywords($string) {
	$string = sql_string($string);
	$string = str_ireplace('"', "", $string);
	$string = strtolower($string);
	
	$string = str_ireplace(" or ", ", ", $string);
	
	if (stripos($string, ",")) {
		$words = explode(",",$string);
	} elseif (!stripos($string, " and ") && !stripos($string, " or ")) {
		$words = explode(" ",$string);
		if (count($words) > 1) {
			$words[] = $string;
		}
	} else {
		$words[] = $string;
	}
	return $words;
}
} /* End_check for "prepare_search_keywords" */

if (!function_exists('remove_common_words')) { /* Check_for "remove_common_words" */
function remove_common_words($string_array) {
	if (is_array($string_array)) { // If value is an array
		$search_words = array();
		$common_words = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "and", "or", "not", "if", "it", "he", "she", "when", "at", "of", "in", "is", "an", "as", "the", "be", "to", "that", "have", "for", "on", "with", "you", "do", "this", "but", "by", "from", "say", "my", "all", "would", "there", "what", "so", "about", "who", "get", "which", "go", "me", "make", "can", "like", "time", "no", "yes", "just", "know", "take", "into", "your", "some", "could", "then", "them", "see", "other", "than", "now", "look", "only", "come", "its", "think", "also", "after", "use", "how", "our", "well", "way", "even", "want", "because", "any", "these", "give", "most", "us", "it's", "find", "ask", "seem", "feel", "try", "leave", "own", "same", "did", "they", " ");
		
		foreach ($string_array as $ind_word) {
			$space_check = trim($ind_word);
			if (!in_array($ind_word, $common_words) && !empty($space_check)) {
				$search_words[] = $space_check;
			}
		}
		return $search_words;
	} else { // If value is a string
		return $string_array;
	}
}
} /* End_check for "remove_common_words" */

if (!function_exists('query_and_or_like')) { /* Check_for "query_and_or_like" */
function query_and_or_like($string, $column_name) {
	if (!is_array($string)) {
		$string = str_ireplace(',', " and ", $string);
		
		if (stripos($string, " or ")) {
			$words = explode(" or ",$string);
			$final = " (";
			for($i=0;$i<count($words);$i++) {
				if ((count($words) > 2) && (($i <> (count($words) - 1)) && ($i <> 0))) { // If between first and last value
					if (stripos($string, " and ")) { // If there is operator nesting of 'AND' operator
						$final .= " (".$column_name." LIKE ";
						foreach (explode(" and ", $words[$i]) as $val_u) {
							$final .= "'%".sql_string($val_u)."%' AND ".$column_name." LIKE ";
						}
						$final = substr($final,0,strlen($final)-(11 + strlen($column_name))); // Strip extra code from behind
						$final .= ") OR ";
					} else $final .= $column_name." LIKE '%".sql_string($words[$i])."%' OR ";
				} 
				elseif ($i == 0) { // If first value
					if (stripos($string, " and ")) { // If there is operator nesting of 'AND' operator
						$final .= $column_name." LIKE ";
						foreach (explode(" and ", $words[$i]) as $val_u) {
							$final .= "'%".sql_string($val_u)."%' AND ".$column_name." LIKE ";
						}
						$final = substr($final,0,strlen($final)-(11 + strlen($column_name))); // Strip extra code from behind
						$final .= ") OR ";
					} else $final .= $column_name." LIKE '%".sql_string($words[$i])."%' OR ";
				} 
				elseif (count($words) - 1) { // If last value
					if (stripos($string, " and ")) { // If there is operator nesting of 'AND' operator
						$final .= " (".$column_name." LIKE ";
						foreach (explode(" and ", $words[$i]) as $val_u) {
							$final .= "'%".sql_string($val_u)."%' AND ".$column_name." LIKE ";
						}
						$final = substr($final,0,strlen($final)-(11 + strlen($column_name))); // Strip extra code from behind
					} else $final .= $column_name." LIKE '%".sql_string($words[$i])."%'";
				}
			}
			$final .= ") ";
			return $final;
		} elseif (stripos($string, " and ") || stripos($string, ",")) {
			//$string = str_ireplace(',', " and ", $string);
			$words = explode(" and ",$string);
			$final = " (".$column_name." LIKE '%";
			for($i=0;$i<count($words);$i++) {
				if ((count($words) > 2) && (($i <> (count($words) - 1)) && ($i <> 0))) {
					$final .= "'%".sql_string($words[$i])."%' AND ".$column_name." LIKE ";
				} elseif ($i == 0) {
					$final .= sql_string($words[$i])."%' AND ".$column_name." LIKE ";
				} elseif (count($words) - 1) {
					$final .= "'%".sql_string($words[$i]);
				}
			}
			$final .= "%') ";
			return $final;
		} else {
			return " ".$column_name." LIKE '%".sql_string($string)."%' ";
		}
	} else {
		foreach($string as $value) {
			$new_string .= query_and_or_like($value, $column_name)." OR ";
		}
		$new_string = substr($new_string,0,strlen($new_string)-4);
		return $new_string;
	}
}
} /* End_check for "query_and_or_like" */

if (!function_exists('string_echo')) { /* Check_for "string_echo" */
function string_echo($value) {
	if (preg_match("/<\/?[a-z]+[^>]*?>/i", $value)) { 
		echo my_stripslashes($value); 
	} else echo nl2br(my_stripslashes($value));
}
} /* End_check for "string_echo" */

if (!function_exists('string_return')) { /* Check_for "string_return" */
function string_return($value) {
	if (preg_match("/<\/?[a-z]+[^>]*?>/i", $value)) { 
		return my_stripslashes($value); 
	} else return nl2br(my_stripslashes($value));
}
} /* End_check for "string_return" */

if (!function_exists('create_string')) { /* Check_for "create_string" */
function create_string($string='and') {
	$string = str_ireplace("'", "", $string);
	$string = str_ireplace('"', "", $string);
    $string = strtolower($string);
    $words = explode(" ",$string);
    $search_words = array();
	
	$common_words = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "and", "or", "not", "if", "it", "he", "she", "when", "at", "of", "in", "is", "an", "as", "the", "be", "to", "that", "have", "for", "on", "with", "you", "do", "this", "but", "by", "from", "say", "my", "all", "would", "there", "what", "so", "about", "who", "get", "which", "go", "me", "make", "can", "like", "time", "no", "yes", "just", "know", "take", "into", "your", "some", "could", "then", "them", "see", "other", "than", "now", "look", "only", "come", "its", "think", "also", "after", "use", "how", "our", "well", "way", "even", "want", "because", "any", "these", "give", "most", "us", "it's", "find", "ask", "seem", "feel", "try", "leave", "own", "same", "did", "they", " ");
	
    $common_words_old = array("and", "or", "not", "if", "it", "he", "she", "when", "at", "of", "in", "is", "an", "as", "the", "be", "to", "a", "that", "have", "i", "for", "on", "with", "you", "do", "this", "but", "by", "from", "say", "will", "my", "one", "all", "would", "there", "what", "so", "up", "out", "about", "who", "get", "which", "go", "me", "make", "can", "like", "time", "no", "yes", "just", "know", "take", "people", "into", "year", "your", "good", "some", "could", "then", "them", "see", "other", "than", "now", "look", "only", "come", "its", "over", "think", "also", "back", "after", "use", "two", "one", "how", "our", "first", "well", "way", "even", "new", "want", "because", "any", "these", "give", "day", "most", "us", "place", "pay", "number", "case", "it's", "find", "ask", "seem", "feel", "try", "leave", "call", "last", "long", "great", "own", "little", "old", "big", "high", "different", "small", "large", "next", "early", "important", "few", "public", "bad", "same", "able", "above", "under", "above", "more", "many", "every");
    
    foreach ($words as $ind_word) {
        if (!in_array($ind_word, $common_words)) {
            $search_words[] = $ind_word;
        }
    }
    
    return $search_words;
}
} /* End_check for "create_string" */

/**
 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
 *
 * @param string $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param string $ending Ending to be appended to the trimmed string.
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param boolean $considerHtml If true, HTML tags would be handled correctly
 *
 * @return string Trimmed string.
 * 
 * source:: http://alanwhipple.com/2011/05/25/php-truncate-string-preserving-html-tags-words/
 */
if (!function_exists('truncateHtml')) { /* Check_for "truncateHtml" */
function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
	if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}
} /* End_check for "truncateHtml" */

if (!function_exists('my_strip_tags')) { /* Check_for "my_strip_tags" */
function my_strip_tags($text) { 
	$text = preg_replace( 
        array( 
          // Remove invisible content 
            '@<head[^>]*?>.*?</head>@siu', 
            '@<style[^>]*?>.*?</style>@siu', 
            '@<script[^>]*?.*?</script>@siu', 
            '@<object[^>]*?.*?</object>@siu', 
            '@<embed[^>]*?.*?</embed>@siu', 
            '@<applet[^>]*?.*?</applet>@siu', 
            '@<noframes[^>]*?.*?</noframes>@siu', 
            '@<noscript[^>]*?.*?</noscript>@siu', 
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), 
        array('', '', '', '', '', '', '', '', ''), $text ); 
      
    return strip_tags($text); 
}
} /* End_check for "my_strip_tags" */

if (!function_exists('textarea_strip_tags')) { /* Check_for "textarea_strip_tags" */
function textarea_strip_tags($text) { 
	$allowed_tags = '<div><span><font><pre><p><br><hr><hgroup><h1><h2><h3><h4><h5><h6>
					<ul><ol><li><dl><dt><dd><strong><em><b><i><u><sup><sub>
					<abbr><address><blockquote><caption>
					<table><tbody><td><tfoot><th><thead><tr>';
	$text = preg_replace( 
        array( 
          // Remove invisible content 
            '@<head[^>]*?>.*?</head>@siu', 
            '@<style[^>]*?>.*?</style>@siu', 
            '@<script[^>]*?.*?</script>@siu', 
            '@<object[^>]*?.*?</object>@siu', 
            '@<embed[^>]*?.*?</embed>@siu', 
            '@<applet[^>]*?.*?</applet>@siu', 
            '@<noframes[^>]*?.*?</noframes>@siu', 
            '@<noscript[^>]*?.*?</noscript>@siu', 
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), 
        array( 
			'', '', '', '', '', '', '', '', ''), $text ); 
      
    return strip_tags($text , $allowed_tags); 
}
} /* End_check for "textarea_strip_tags" */

if (!function_exists('textarea_strip_tags_extended')) { /* Check_for "textarea_strip_tags_extended" */
function textarea_strip_tags_extended($text) { 
	$allowed_tags = '<div><span><font><pre><p><br><hr><hgroup><h1><h2><h3><h4><h5><h6>
					<ul><ol><li><dl><dt><dd><strong><em><b><i><u><sup><sub>
					<abbr><address><blockquote><caption>
					<table><tbody><td><tfoot><th><thead><tr>
					<input><textarea><select><fieldset><label>
					<a><img><iframe><form><area><audio><video><button>';
	$text = preg_replace( 
        array( 
          // Remove invisible content 
            '@<head[^>]*?>.*?</head>@siu', 
            '@<style[^>]*?>.*?</style>@siu', 
            '@<script[^>]*?.*?</script>@siu', 
            '@<object[^>]*?.*?</object>@siu', 
            '@<embed[^>]*?.*?</embed>@siu', 
            '@<applet[^>]*?.*?</applet>@siu', 
            '@<noframes[^>]*?.*?</noframes>@siu', 
            '@<noscript[^>]*?.*?</noscript>@siu', 
            '@<noembed[^>]*?.*?</noembed>@siu',
        ), 
        array( 
			'', '', '', '', '', '', '', '', ''), $text ); 
      
    return strip_tags($text , $allowed_tags); 
}
} /* End_check for "textarea_strip_tags_extended" */

if (!function_exists('dateNice')) { /* Check_for "dateNice" */
function dateNice($date) {
	$nu_date = date_create($date);
	return date_format($nu_date,"M jS, Y");
}
} /* End_check for "dateNice" */

if (!function_exists('get_prospect_info')) { /* Check_for "get_prospect_info" */
function get_prospect_info($p_id, $column = 'full_name') {
	global $database_config, $config;
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT prospects.".$column." 
                      FROM prospects 
                      WHERE prospects.p_id = ".sql_int($p_id)." 
                      LIMIT 1";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
    
    if ($totalRows_Actions) {
    	return $row_Actions[$column];
    }    
    else return 0;
}
} /* End_check for "get_prospect_info" */

if (!function_exists('record_stage_history')) { /* Check_for "record_stage_history" */
function record_stage_history($prospect_id, $prog_date_id, $stage_id) {
	global $database_config, $config;
    
	// Get last history
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT stage_id 
					  FROM stages_history 
					  WHERE p_id = ".sql_int($prospect_id)." 
							AND pd_id = ".sql_int($prog_date_id)." 
					  ORDER BY h_id DESC
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if (!$totalRows_Actions || ($row_Actions['stage_id'] != $stage_id)) {
		$insertSQL = "INSERT INTO stages_history (stage_id, p_id, pd_id, h_date) 
					  VALUES (
						  ".sql_int($stage_id).", 
						  ".sql_int($prospect_id).", 
						  ".sql_int($prog_date_id).", 
						  '".sql_string(date("Y-m-d H:i:s"))."' 
					  )";
		mysqli_select_db($config, $database_config);
		$Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
		$insertStatus = mysqli_affected_rows($config);
		
		if ($insertStatus < 0) { // If INSERT failed
			return false;
		}
	}
	
	return true;
}
} /* End_check for "record_stage_history" */

if (!function_exists('last_day_template_was_sent')) { /* Check_for "last_day_template_was_sent" */
function last_day_template_was_sent($h_id, $template_id) { // For instant templates
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT DATE(ah_date) as ah_date_only 
					  FROM automation_history 
					  WHERE h_id = ".sql_int($h_id)." 
					  AND template_id = ".sql_int($template_id)." 
					  ORDER BY ah_date DESC 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['ah_date_only'];
	else return date("Y-m-d 00:00:00");
}
} /* End_check for "last_day_template_was_sent" */

if (!function_exists('next_template_to_send')) { /* Check_for "next_template_to_send" */
function next_template_to_send($h_id, $stage_id, $programme_id, $timing) { // For sequential templates
	global $database_config, $config;
    
	// Get the last template id sent
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT email_templates.template_id, email_templates.priority, email_templates.country  
					  FROM automation_history 
					  JOIN email_templates ON automation_history.template_id = email_templates.template_id 
					  JOIN stages_history ON automation_history.h_id = stages_history.h_id 
					  WHERE email_templates.stage_id = ".sql_int($stage_id)." 
					  AND email_templates.programme_id = ".sql_int($programme_id)." 
					  AND email_templates.timing = '".sql_string($timing)."' 
					  AND email_templates.timing_trigger = 'sequential' 
					  AND stages_history.h_id = ".sql_int($h_id)." 
					  ORDER BY automation_history.ah_date DESC 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	// If a template has ever been sent
	if ($row_Actions['template_id']) {
		// Get the next template id
		mysqli_select_db($config, $database_config);
		$query_NextOne = "SELECT email_templates.template_id 
						  FROM email_templates 
						  WHERE email_templates.stage_id = ".sql_int($stage_id)." 
						  AND email_templates.country = ".sql_int($row_Actions['country'])." 
						  AND email_templates.programme_id = ".sql_int($programme_id)." 
						  AND email_templates.timing = '".sql_string($timing)."' 
						  AND email_templates.timing_trigger = 'sequential' 
						  AND email_templates.priority >= ".sql_int($row_Actions['priority'])." 
						  AND email_templates.template_id > ".sql_int($row_Actions['template_id'])." 
						  ORDER BY email_templates.priority ASC, email_templates.template_id ASC 
						  LIMIT 1";
		$NextOne = mysqli_query($config, $query_NextOne) or die(mysqli_error($config));
		$row_NextOne = mysqli_fetch_assoc($NextOne);
		$totalRows_NextOne = mysqli_num_rows($NextOne);
		
		// If the next template id is found
		if ($row_NextOne['template_id']) {
			return $row_NextOne['template_id'];
		}
		// If the next template id is not found
		else return 0;
	}
	// If a template has never been sent
	else return 0;
}
} /* End_check for "next_template_to_send" */

if (!function_exists('template_no_of_sent')) { /* Check_for "template_no_of_sent" */
function template_no_of_sent($h_id, $template_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(template_id) as how_many_times 
					  FROM automation_history 
					  WHERE h_id = ".sql_int($h_id)." 
					  AND template_id = ".sql_int($template_id)."";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "template_no_of_sent" */

if (!function_exists('temps_for_timing')) { /* Check_for "temps_for_timing" */
function temps_for_timing($stage_id, $programme_id, $timing) { // For sequential templates
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT DISTINCT email_templates.template_id 
					  FROM email_templates 
					  WHERE email_templates.stage_id = ".sql_int($stage_id)." 
					  AND email_templates.programme_id = ".sql_int($programme_id)." 
					  AND email_templates.timing = '".sql_string($timing)."' 
					  AND email_templates.timing_trigger = 'sequential'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	return $totalRows_Actions;
}
} /* End_check for "temps_for_timing" */

if (!function_exists('distinct_temps_sent_for_timing')) { /* Check_for "distinct_temps_sent_for_timing" */
function distinct_temps_sent_for_timing($h_id, $stage_id, $programme_id, $timing) { // For sequential templates
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT DISTINCT email_templates.template_id 
					  FROM automation_history 
					  JOIN email_templates ON automation_history.template_id = email_templates.template_id 
					  JOIN stages_history ON automation_history.h_id = stages_history.h_id 
					  WHERE email_templates.stage_id = ".sql_int($stage_id)." 
					  AND email_templates.programme_id = ".sql_int($programme_id)." 
					  AND email_templates.timing = '".sql_string($timing)."' 
					  AND email_templates.timing_trigger = 'sequential' 
					  AND stages_history.h_id = ".sql_int($h_id)."";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	return $totalRows_Actions;
}
} /* End_check for "distinct_temps_sent_for_timing" */

if (!function_exists('total_sent_for_timing')) { /* Check_for "total_sent_for_timing" */
function total_sent_for_timing($h_id, $stage_id, $programme_id, $timing) { // For sequential templates
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(ah_id) as how_many_times 
					  FROM automation_history 
					  JOIN email_templates ON automation_history.template_id = email_templates.template_id 
					  JOIN stages_history ON automation_history.h_id = stages_history.h_id 
					  WHERE email_templates.stage_id = ".sql_int($stage_id)." 
					  AND email_templates.programme_id = ".sql_int($programme_id)." 
					  AND email_templates.timing = '".sql_string($timing)."' 
					  AND email_templates.timing_trigger = 'sequential' 
					  AND stages_history.h_id = ".sql_int($h_id)."";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "total_sent_for_timing" */

if (!function_exists('last_day_sent_for_timing')) { /* Check_for "last_day_sent_for_timing" */
function last_day_sent_for_timing($h_id, $timing, $trigger) { 
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT DATE(automation_history.ah_date) as ah_date_only 
					  FROM automation_history 
					  JOIN email_templates ON automation_history.template_id = email_templates.template_id 
					  WHERE automation_history.h_id = ".sql_int($h_id)." 
					  AND email_templates.timing = '".sql_string($timing)."' 
					  AND email_templates.timing_trigger = '".sql_string($trigger)."' 
					  ORDER BY automation_history.ah_date DESC 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) return $row_Actions['ah_date_only'];
	else return date("Y-m-d 00:00:00");
}
} /* End_check for "last_day_sent_for_timing" */

if (!function_exists('no_template_attachments')) { /* Check_for "no_template_attachments" */
function no_template_attachments($template_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(email_attach_link.attach_id) as how_many_times 
					  FROM email_attach_link 
					  JOIN email_attachments ON email_attach_link.attach_id = email_attachments.attach_id 
					  WHERE email_attach_link.template_id = ".sql_int($template_id);
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return '('.$row_Actions['how_many_times'].')';
}
} /* End_check for "no_template_attachments" */

if (!function_exists('no_blast_attachments')) { /* Check_for "no_blast_attachments" */
function no_blast_attachments($template_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(blast_attach_link.attach_id) as how_many_times 
					  FROM blast_attach_link 
					  JOIN email_attachments ON blast_attach_link.attach_id = email_attachments.attach_id 
					  WHERE blast_attach_link.template_id = ".sql_int($template_id);
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return '('.$row_Actions['how_many_times'].')';
}
} /* End_check for "no_blast_attachments" */

if (!function_exists('next_programme_date')) { /* Check_for "next_programme_date" */
function next_programme_date($entry_id, $type = 'start_date') { 
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT programme_id, start_date, end_date 
					  FROM programme_dates 
					  WHERE entry_id = ".sql_int($entry_id)." 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		mysqli_select_db($config, $database_config);
		$query_Action2 = "SELECT ".$type."
						  FROM programme_dates 
						  WHERE programme_id = ".sql_int($row_Actions['programme_id'])." 
						  AND start_date > '".sql_string($row_Actions['start_date'])."' 
						  AND start_date > '".sql_string(date("Y-m-d"))."' 
						  ORDER BY start_date ASC 
						  LIMIT 1";
		$Action2 = mysqli_query($config, $query_Action2) or die(mysqli_error($config));
		$row_Action2 = mysqli_fetch_assoc($Actions);
		$totalRows_Action2 = mysqli_num_rows($Actions);
		
		if ($row_Action2[$type]) { // If there is an existing upcoming date
			return $row_Action2[$type];
		}
		else { // If there isn't an existing upcoming date
			$start_date_day_of_month = date_format(date_create($row_Actions['start_date']), "d");
			$end_date_day_of_month = date_format(date_create($row_Actions['end_date']), "d");
			$potential_months = array('01', '04', '07', '10');
			$potential_years = array(
				date("Y"), // Current year
				date("Y", strtotime('+1 year')) // Next year
			);

			$guessed_start = $guessed_end = '';

			foreach($potential_years as $x_year) { // Check each year
				foreach($potential_months as $x_month) {
					if (!$guessed_start && !$guessed_end) { // If the next start and end date have not been determined, check the months
						$proposed_start = $x_year.'-'.$x_month.'-'.$start_date_day_of_month;
						$proposed_end = date("Y-m-d", strtotime($proposed_start) + (90 * 24 * 60 * 60)); // 90 days i.e 3 months
						
						// If the proposed date is less than current date, skip to next date
						if ($proposed_start > date("Y-m-d")) {
							$guessed_start = $proposed_start;
							$guessed_end = $proposed_end;
						}
					}
					else {
						continue;
					}
				}
			}

			if ($guessed_start && $guessed_end) {
				if ($type == 'start_date') {
					return $guessed_start;
				}
				else {
					return $guessed_end;
				}
			}
			
			$incrementedDate = strtotime($row_Actions[$type]) + (90 * 24 * 60 * 60); // 90 days i.e 3 months
			
			// If the incremented date is still in the future, then proceed
			if ($incrementedDate > date("Y-m-d")) return date("Y-m-d", $incrementedDate);
		}
	}
	
	return date("Y-m-d", strtotime('+90 days'));
}
} /* End_check for "next_programme_date" */

if (!function_exists('record_log')) { /* Check_for "record_log" */
function record_log($log_type, $log_user, $action_id, $log_info = '') {
	global $database_config, $config;
	
	$insertSQL = "INSERT INTO db_log (log_type, log_date, log_user, action_id, log_info) 
				  VALUES ( 
					  '".sql_string($log_type)."', 
					  '".sql_string(date("Y-m-d H:i:s"))."', 
					  ".sql_int($log_user).", 
					  ".sql_int($action_id).", 
					  '".sql_string($log_info)."' 
				  )";
	mysqli_select_db($config, $database_config);
	$Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
	$insertStatus = mysqli_affected_rows($config);
	
	if ($insertStatus < 0) { // If INSERT failed
		return false;
	}
	
	return true;
}
} /* End_check for "record_log" */

if (!function_exists('stage_to_status')) { /* Check_for "stage_to_status" */
function stage_to_status($stage_id, $p_id) {
	global $database_config, $config, $stage_to_status, $sess_user_id;

	if (array_key_exists($stage_id, $stage_to_status)) {
		// Update the status as required
		mysqli_select_db($config, $database_config);
		$updateSQL = "UPDATE prospects 
					  SET status = ".sql_int($stage_to_status[$stage_id])." 
					  WHERE p_id = ".sql_int($p_id)." 
					  LIMIT 1";
		$updateResult = mysqli_query($config, $updateSQL) or die(mysqli_error($config));
		$updateStatus = mysqli_affected_rows($config);
		
		if ($updateStatus >= 0) { // If UPDATE is successful
			return true;
		}
	}
	
	return false;
}
} /* End_check for "stage_to_status" */

if (!function_exists('does_student_have_login')) { /* Check_for "does_student_have_login" */
function does_student_have_login($p_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT login_id 
					  FROM users_students_prospect
					  WHERE p_id = ".sql_int($p_id)." 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		return $row_Actions['login_id'];
	}
	else return 0;
}
} /* End_check for "does_student_have_login" */

if (!function_exists('get_prog_module_no')) { /* Check_for "get_prog_module_no" */
function get_prog_module_no($prog_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(m.module_id) as how_many_times 
					  FROM modules m
					  WHERE m.prog_id = ".sql_int($prog_id)." 
							  AND m.status = 'active' 
							  AND m.deleted = '0'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "get_prog_module_no" */

if (!function_exists('get_module_component_no')) { /* Check_for "get_module_component_no" */
function get_module_component_no($module_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(c.component_id) as how_many_times 
					  FROM module_components mc
					  JOIN components c ON c.component_id = mc.component_id 
					  WHERE mc.module_id = ".sql_int($module_id)." 
					  		AND c.status = 'active'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "get_module_component_no" */

if (!function_exists('get_module_material_no')) { /* Check_for "get_module_material_no" */
function get_module_material_no($module_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(material_id) as how_many_times 
					  FROM module_materials 
					  WHERE module_id = ".sql_int($module_id)." 
					  		AND status = 'active'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "get_module_material_no" */

if (!function_exists('get_module_assignment_no')) { /* Check_for "get_module_assignment_no" */
function get_module_assignment_no($module_id) {
	global $database_config, $config;
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(alta.assignment_id) as how_many_times
					FROM alt_assignments alta
					WHERE alta.module_id = '".sql_int($module_id)."' 
						AND alta.deleted = '0'";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "get_module_assignment_no" */

if (!function_exists('get_lecture_content_count')) { /* Check_for "get_lecture_content_count" */
function get_lecture_content_count($prog_mod_id = '', $login_id = '', $type = 'pending') {
	global $database_config, $config;

	if ($type == 'pending') {
		//$modifier_sql = "AND lsr.study_status IN ('pending', '') ";
		$modifier_sql = "AND (lsr.study_status = 'pending' OR lsr.study_status IS NULL)";
	} else {
		$modifier_sql = "AND lsr.study_status = '".sql_string($type)."' ";
	}
    
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT COUNT(mc.component_id) as how_many_times 
					FROM users_students_prospect usp 
					JOIN prospects p ON usp.p_id = p.p_id 
					JOIN options progs ON p.prog_interest = progs.option_id 
					LEFT JOIN programme_dates ON p.prog_date = programme_dates.entry_id 
					JOIN programme_modules pm ON pm.prog_id = p.prog_interest AND pm.prog_date = p.prog_date AND pm.branch_id = p.branch 
					JOIN modules m ON pm.module_id = m.module_id 
					JOIN module_components mc ON m.module_id = mc.module_id 
					JOIN components c ON c.component_id = mc.component_id 
					LEFT JOIN lecture_student_records lsr ON pm.prog_mod_id = lsr.prog_mod_id AND lsr.p_id = p.p_id AND lsr.component_id = mc.component_id 
					WHERE usp.login_id = ".sql_int($login_id)." 
						AND p.deleted = '0' 
						AND pm.prog_mod_id = '".sql_int($prog_mod_id)."' 
						".$modifier_sql."
					ORDER BY module_name ASC"; //echo '<pre>'; var_dump($query_Actions); echo '</pre>'; 
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	
	return $row_Actions['how_many_times'];
}
} /* End_check for "get_lecture_content_count" */

if (!function_exists('record_lecture_last_seen')) { /* Check_for "record_lecture_last_seen" */
function record_lecture_last_seen($prog_mod_id = '', $p_id = '', $component_id = '') {
	global $database_config, $config, $sess_user_id;
	
	$insertSQL = "INSERT INTO lecture_student_records (prog_mod_id, p_id, component_id, study_status, last_seen) 
					VALUES ( 
							'".sql_int($prog_mod_id)."', 
							'".sql_int($p_id)."', 
							'".sql_int($component_id)."', 
							'ongoing', 
							'".date("Y-m-d H:i:s")."'
					)
					ON DUPLICATE KEY UPDATE 
							last_seen = VALUES(last_seen)";
	mysqli_select_db($config, $database_config);
	$Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
	$insertStatus = mysqli_affected_rows($config);
	
	if ($insertStatus >= 0) { // If INSERT is successful
		return true;
	}
	else { // If INSERT failed
		return false;
	}
}
} /* End_check for "record_lecture_last_seen" */

if (!function_exists('no_students_in_session')) { /* Check_for "no_students_in_session" */
function no_students_in_session($prog_id, $start_date, $end_date = '') {
	global $database_config, $config, $course_applicable_stages;

	if ($end_date) { // If targeting a specific end_date
		$search_condition = "AND programme_dates.end_date = '".sql_string($end_date)."'";
	}
    
    mysqli_select_db($config, $database_config);
    $query_Actions = "SELECT COUNT(p.p_id) as how_many 
                      FROM prospects p 
					  JOIN programme_dates ON p.prog_date = programme_dates.entry_id 
                      WHERE p.prog_interest = '".sql_int($prog_id)."'  
							AND p.stage IN (".implode(", ", $course_applicable_stages).") 
					  		AND programme_dates.start_date = '".sql_string($start_date)."' ".$search_condition."";
    $Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
    $row_Actions = mysqli_fetch_assoc($Actions);
    $totalRows_Actions = mysqli_num_rows($Actions);
    
    if ($row_Actions['how_many']) {
    	return number_format($row_Actions['how_many']);
    }    
    else return 0;
}
} /* End_check for "no_students_in_session" */

if (!function_exists('has_old_prog_attached')) { /* Check_for "has_old_prog_attached" */
function has_old_prog_attached($new_prog_id) { // Does this new programme have an existing attachment to an old programme
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT option_one
					  FROM option_relationships 
					  WHERE relationship_name = 'old prog to new prog' 
							AND option_two = ".sql_int($new_prog_id)." 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		return $row_Actions['option_one'];
	}
	else return false;
}
} /* End_check for "has_old_prog_attached" */

if (!function_exists('has_new_prog_attached')) { /* Check_for "has_new_prog_attached" */
function has_new_prog_attached($old_prog_id) { // Does this old programme have an existing attachment to a new programme
	global $database_config, $config;
	
	mysqli_select_db($config, $database_config);
	$query_Actions = "SELECT option_two
					  FROM option_relationships 
					  WHERE relationship_name = 'old prog to new prog' 
							AND option_one = ".sql_int($old_prog_id)." 
					  LIMIT 1";
	$Actions = mysqli_query($config, $query_Actions) or die(mysqli_error($config));
	$row_Actions = mysqli_fetch_assoc($Actions);
	$totalRows_Actions = mysqli_num_rows($Actions);
	
	if ($totalRows_Actions) {
		return $row_Actions['option_two'];
	}
	else return false;
}
} /* End_check for "has_new_prog_attached" */

if (!function_exists('simplify_prog_name')) { /* Check_for "simplify_prog_name" */
function simplify_prog_name($string = '') {
	$string = preg_replace(
		array("/ \- classroom$/i", "/ \- online$/i", "/\ \(international\)$/i", "/\ \(local\)$/i"), 
		array("", "", "", ""), 
		trim($string)
	); 

	return $string;
}
} /* End_check for "simplify_prog_name" */

if (!function_exists('slug_to_title')) { /* Check_for "slug_to_title" */
	function slug_to_title($string){
		$string = ucwords(str_replace('-', ' ', $string));
		return str_replace('And', 'and', $string);
	}
} /* End_check for "slug_to_title" */