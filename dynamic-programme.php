<?php 
require_once('connections/config.php');
require_once('components/misc.inc.php');

if ($coresite_page_segment_2_slug) {
    $prog_template_file = '';

    if ($coresite_curr_country_slug) {
        $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
    }
    else {
        $sql_course_country = "";
    }

    /* BEGIN BROKEN LINKS PREVENTION */
        if ($coresite_page_segment_2_slug == 'bachelor-of-science-business-and-management-online-7') {
            if ($coresite_page_segment_1_slug == 'ad') {
                redirect(ROOT_DIR.'ad/bachelor-of-arts-business-and-management-online-7');
            }
            else {
                redirect(ROOT_DIR.'programme/bachelor-of-arts-business-and-management-online-7');
            }
        }
    /* END BROKEN LINKS PREVENTION */

    if ($coresite_page_segment_1_slug == 'ad') {
        $is_ad_mode = $add_no_index_no_follow_tag = $this_is_advert_page = true;
    }
    else {
        $is_ad_mode = $add_no_index_no_follow_tag = $this_is_advert_page = false;
    }

    // if the category has courses for only this location, display them
    if ($sql_course_country) {
        $thumbnail_to_country_join_additional = " LEFT JOIN content_info_to_country citc ON citc.cic_title = np.programme_id AND citc.cic_country_id = '".$sql_course_country."' AND citc.cic_type = 'banner' AND cic_status = 'active'";
        $thumbnail_to_country_columns = ", citc.cic_country_id, citc.cic_banner_image as country_image";

        $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '".$sql_course_country."' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
        $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment "; 
    }
    
    // Check for the programme
    mysqli_select_db($config, $database_config);
    $query_DisProg = "SELECT np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value  ".$thumbnail_to_country_columns.$country_max_no_of_instalments_column."
                    FROM option_relationships oprel 
                    JOIN options op ON oprel.option_two = op.option_id 
                    JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                    ".$thumbnail_to_country_join_additional.$country_max_no_of_instalments_join_additional."
                    WHERE oprel.relationship_name = 'programme type' 
                        AND np.url_name = '".sql_string($coresite_page_segment_2_slug)."' 
                        AND np.deleted = '0'
                    LIMIT 1";
    $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
    $row_DisProg = mysqli_fetch_assoc($DisProg);
    $totalRows_DisProg = mysqli_num_rows($DisProg);

    //echo '<pre>'; var_dump($row_DisProg); echo '</pre>';

    if ($totalRows_DisProg) {
        // Get repeated used values
        $is_programme_online = programme_is_online($row_DisProg['programme_id']);
        $get_programme_category = strtolower(get_programme_relationship($row_DisProg['programme_id'], 'programme category'));
        $dis_programme_subcategory_id = $row_DisProg['subcategory_id'];
        $dis_programme_parent_category_id = $row_DisProg['parent_category_id'];
        
        // Check if short course
        if (in_array($dis_programme_subcategory_id, array(409))) {
            $prog_query_type = 'short course';
            $prog_template_file = 'template--for__individual__short-course.php';
        }
        // Check if advanced professional certificate course
        elseif (in_array($dis_programme_subcategory_id, array(883))) {
            $prog_query_type = 'advanced professional';
            $prog_template_file = 'template--for__individual__advanced-professional-certificate-course.php';
        }
        // Check if semp online
        elseif (in_array($dis_programme_subcategory_id, array(884))) {
            $prog_query_type = 'semp online';
            $prog_template_file = 'template--for__individual__semp-online.php';
        }
        // Check if intl diploma
        elseif (in_array($dis_programme_subcategory_id, array(880))) {
            $prog_query_type = 'intl diploma';
            $prog_template_file = 'template--for__individual__international-postgraduate-diploma.php';
        }
        // Check if masters degree
        elseif (in_array($dis_programme_subcategory_id, array(406))) {
            $prog_query_type = 'masters';
            $prog_template_file = 'template--for__individual__masters-degree.php';
        }
        // Check if doctorate degree
        elseif (in_array($dis_programme_subcategory_id, array(405))) {
            $prog_query_type = 'doctorate';
            $prog_template_file = 'template--for__individual__doctorate-degree.php';
        }
        // Check for postgraduate research diploma
        elseif (in_array($dis_programme_subcategory_id, array(1373))) {
            $prog_query_type = 'postgraduate research';
            $prog_template_file = 'template--for__individual__postgraduate-research-diploma.php';
        }
        // Check if postgraduate diploma degree
        elseif (in_array($dis_programme_subcategory_id, array(408))) {
            $prog_query_type = 'postgraduate diploma';
            $prog_template_file = 'template--for__individual__postgraduate-diploma-regulated.php';
        }
        // Check if bachelors degree
        elseif (in_array($dis_programme_subcategory_id, array(407))) {
            $prog_query_type = 'bachelors';
            $prog_template_file = 'template--for__individual__bachelors-degree.php';
        }
        // Check if intl higher diploma degree
        elseif (in_array($dis_programme_subcategory_id, array(882))) {
            $prog_query_type = 'intl higher diploma';
            $prog_template_file = 'template--for__individual__international-higher-diplomas.php';
        }
        // Check if diploma (regulated) degree
        elseif (in_array($dis_programme_subcategory_id, array(882))) {
            $prog_query_type = 'diplomas regulated';
            $prog_template_file = 'template--for__individual__diplomas-regulated.php';
        }
        // Check if PCC Classroom
        elseif (in_array($dis_programme_subcategory_id, array(1253))) {
            // $prog_query_type = 'training';
            // $prog_template_file = 'template--for__individual__training.php';

            include_once('404.php');
        }
        
        // Check if training
        elseif (in_array($dis_programme_parent_category_id, array(879))) {
            $prog_query_type = 'training';
            $prog_template_file = 'template--for__individual__training.php';
        }
        // Check if free online course
        elseif (in_array($dis_programme_subcategory_id, array(617))) {
            $prog_query_type = 'free courses';
            $prog_template_file = 'template--for__individual__free-course.php';
        }

        // Failsafes to allow newly created programmes to render at least
        
        // Check if Postgraduate Programme
        elseif (in_array($dis_programme_parent_category_id, array(876))) {
            $prog_query_type = 'masters';
            $prog_template_file = 'template--for__individual__masters-degree.php';
        }
        // Check if Undergraduate Programme
        elseif (in_array($dis_programme_parent_category_id, array(877))) {
            $prog_query_type = 'bachelors';
            $prog_template_file = 'template--for__individual__bachelors-degree.php';
        }
        // Check if Executive Education
        elseif (in_array($dis_programme_parent_category_id, array(878))) {
            $prog_query_type = 'short course';
            $prog_template_file = 'template--for__individual__short-course.php';
        }
        // Check if Corporate Training
        elseif (in_array($dis_programme_parent_category_id, array(879))) {
            $prog_query_type = 'training';
            $prog_template_file = 'template--for__individual__training.php';
        }
    }

    if ($prog_template_file && file_exists($prog_template_file)) {
        include_once($prog_template_file);
    }
    else {
        include_once('404.php');
    }
}
else {
    include_once('404.php');
}

exit(); 
