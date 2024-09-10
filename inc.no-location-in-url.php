<?php // This will make sure that only UK or NG etc version is displayed
    if (!count($_POST)) { // Only if no form was submitted
        $default_location_of_choice = 'united-kingdom';
        if ($overwrite_domain) {
            $default_location_of_choice = $overwrite_domain;
        }

        if ($_SESSION['location_of_choice'] != $default_location_of_choice) {
            $_SESSION['location_of_choice_before'] = $_SESSION['location_of_choice'];
            $_SESSION['location_of_choice'] = $default_location_of_choice;

            // Process the query strings
            $final_query_string_params = '';
            $available_query_strings = array();
            foreach ($_GET as $key => $value) {
                if (!in_array($key, array('part_1','part_2','part_3','part_4'))) {
                    $available_query_strings[] = $key.'='.rawurlencode($value);
                }
            }
            if (count($available_query_strings)) {
                $final_query_string_params = '?'.implode('&', $available_query_strings);
            }

            // Permanent 301 redirection
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".ROOT_DIR.$coresite_page_segment_slug_combined.$final_query_string_params);
            exit();
        }
    }
?>