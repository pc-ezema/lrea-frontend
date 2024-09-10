<?php 

if ($prog_box_display_type) {
    $prog_query_type = $prog_box_display_type;
}

if ($prog_query_type || $prog_query_pagination_mode) {
    $unknown_query_type = false;
    
    // Check for query parts
    $prog_query_join_additional = $prog_query_join_additional ? $prog_query_join_additional : "";
    $prog_query_columns_additional = $prog_query_columns_additional ? $prog_query_columns_additional : "";
    $prog_query_where = $prog_query_where ? $prog_query_where : "";
    $prog_query_where_additional = $prog_query_where_additional ? $prog_query_where_additional : "";
    $prog_query_order_by = $prog_query_order_by ? $prog_query_order_by : "ORDER BY RAND()";
    $prog_query_limit = $prog_query_limit ? $prog_query_limit : "LIMIT 4";
    $prog_to_country_join_additional = $thumbnail_to_country_join_additional = $thumbnail_to_country_columns = "";
    $prog_query_relevance_additional = $prog_query_relevance_additional ? $prog_query_relevance_additional : "";
    $prog_query_relevance_where_additional = $prog_query_relevance_where_additional ? $prog_query_relevance_where_additional : "";
    $prog_query_relevance_order_by = ($prog_query_relevance_additional && strpos($prog_query_relevance_additional, "keyword_score") !== false) ? " keyword_score DESC, " : "";
    // IMPORTANT NOTE: Don't forget to reset any value added above this comment at the bottom of this script

    // $ignore_course = "AND op.option_id NOT IN () "; // Comment this out if there is no course to ignore

    // Customize the WHERE clause
    if ($prog_query_type == 'short course') { 
        $prog_query_where = " AND op.option_id = 409 ";
    } 
    elseif ($prog_query_type == 'advanced professional') { 
        $prog_query_where = " AND op.option_id = 883 ";
    } 
    elseif ($prog_query_type == 'degree') { 
        $prog_query_where = " AND op.parent_option_id IN (876, 877) ";
    } 
    elseif ($prog_query_type == 'intl diploma') { 
        $prog_query_where = " AND op.option_id = 880 ";
    } 
    elseif ($prog_query_type == 'semp online') { 
        $prog_query_where = " AND op.option_id = 884 ";
    } 
    elseif ($prog_query_type == 'doctorate') { 
        $prog_query_where = " AND op.option_id = 405 ";
    } 
    elseif ($prog_query_type == 'masters') { 
        $prog_query_where = " AND op.option_id = 406 ";
    } 
    elseif ($prog_query_type == 'bachelors') { 
        $prog_query_where = " AND op.option_id = 407 ";
    }
    elseif ($prog_query_type == 'intl higher diploma') { 
        $prog_query_where = " AND op.option_id = 882 ";
    }
    elseif ($prog_query_type == 'postgraduate diploma') { 
        $prog_query_where = " AND op.option_id = 408 ";
    }
    elseif ($prog_query_type == 'diplomas regulated') { 
        $prog_query_where = " AND op.option_id = 881 ";
    }  
    elseif ($prog_query_type == 'training') { 
        $prog_query_join_additional = " JOIN programme_dates pd ON pd.programme_id = op_other.option_id 
                                        JOIN myip_ms_countries myctry ON pd.countryID = myctry.countryID ";
        $prog_query_columns_additional = ", pd.entry_id prog_date_id, pd.start_date, pd.end_date, pd.countryID, pd.location, myctry.countryName, MONTH(pd.start_date) curr_month, YEAR(pd.start_date) curr_year ";
        
        $prog_query_where = " AND op.parent_option_id = 879 ";
    } 
    elseif ($prog_query_type == 'free courses') {
        $prog_query_where = " AND op.option_id = 617 ";
    }
    elseif ($prog_query_type == 'postgraduate programmes') {
        $prog_query_where = " AND op.parent_option_id = 876 ";
    }
    elseif ($prog_query_type == 'undergraduate programmes') {
        $prog_query_where = " AND op.parent_option_id = 877 ";
    }
    elseif ($prog_query_type == 'postgraduate research') {
        $prog_query_where = " AND op.option_id = 1373 ";
    }
    else {
        $unknown_query_type = true;
    }

    if ($prog_query_pagination_mode) {
        // This prevents double LIMIT constraints, since the pagination will have its own limit parameter.
        $prog_query_limit = "";
  
        if (count($modifier_sql)) {
            $prog_query_order_by = " ORDER BY ".$prog_query_relevance_order_by." np.programme_name ASC ";
        }
    }

    // Execute the query whilst making sure that its backend equivalent is available
    if (!$unknown_query_type) {
        // Check if there is a country of specific interest, and prepare to filter the courses based on that
        if ($override_course_country_value) {
            $sql_course_country = $override_course_country_value;
        }
        elseif ($coresite_curr_country_slug) {
            $sql_course_country = is_country($coresite_curr_country_slug, "countryID");
        }
        else {
            $sql_course_country = "";
        }

        // if the category has courses for only this location, display them
        if ($sql_course_country) { 
            $prog_to_country_join_additional = " JOIN course_to_country ctc ON ctc.prog_id = np.programme_id ";
            $prog_to_country_where_additional = " AND ctc.countryID IN ('".$sql_course_country."', '***') ";

            $thumbnail_to_country_join_additional = " LEFT JOIN content_info_to_country citc ON citc.cic_title = np.programme_id AND citc.cic_country_id = '".$sql_course_country."' AND citc.cic_type = 'thumbnail' AND cic_status = 'active' ";
            $thumbnail_to_country_columns = ", citc.cic_country_id, citc.cic_banner_image as country_image ";

            $country_max_no_of_instalments_join_additional = " LEFT JOIN content_info_to_country citc_install ON citc_install.cic_title = np.programme_id AND citc_install.cic_country_id = '".$sql_course_country."' AND citc_install.cic_type = 'max-instalments' AND citc_install.cic_status = 'active' ";
            $country_max_no_of_instalments_column = ", citc_install.cic_body as country_max_instalment ";
        }

        if ($order_progs_by_partner_schools) {
            $prog_partner_school_join_additional = " LEFT JOIN (
                                                        SELECT option_two, option_one 
                                                        FROM option_relationships 
                                                        WHERE relationship_name = 'programme partner schools' 
                                                        GROUP BY option_one 
                                                    ) option_school ON option_school.option_one = np.programme_id 
                                                    LEFT JOIN partner_schools pschool ON option_school.option_two = pschool.partner_id AND pschool.partner_status = 'current' AND pschool.deleted = '0' ";

            $prog_query_order_by = " ORDER BY ".$prog_query_relevance_order_by." 
                                        CASE 
                                            WHEN pschool.is_masked = '0' THEN 1
                                            ELSE 0
                                        END DESC, 
                                        CASE
                                            WHEN pschool.logo <> '' THEN 1
                                            ELSE 0
                                        END DESC, 
                                        CASE
                                            WHEN pschool.type = 'school' THEN 3
                                            WHEN pschool.type = 'both' THEN 2
                                            WHEN pschool.type = 'awarding' THEN 1
                                            ELSE 0
                                        END DESC, 
                                        CASE 
                                            WHEN pschool.partner_name NOT LIKE '%MSBM%' THEN 1 
                                            ELSE 0
                                        END DESC, RAND() ";
        }

        mysqli_select_db($config, $database_config);
        $query_DisProg = "SELECT np.*, oprel.option_two, op.option_id as subcategory_id, op.parent_option_id as parent_category_id, op.option_value, 
                                op_other.option_id backend_prog_id, op_other.option_value backend_prog_name ".$prog_query_relevance_additional.$prog_query_columns_additional.$thumbnail_to_country_columns.$country_max_no_of_instalments_column." 
                        FROM option_relationships oprel 
                        JOIN options op ON oprel.option_two = op.option_id 
                        JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                        ".$prog_to_country_join_additional.$thumbnail_to_country_join_additional.$country_max_no_of_instalments_join_additional."

                        JOIN option_relationships option_prog_other ON option_prog_other.option_two = np.programme_id 
                        JOIN options op_other ON option_prog_other.option_one = op_other.option_id 

                        ".$prog_query_join_additional.$prog_partner_school_join_additional." 
                        WHERE oprel.relationship_name = 'programme type' 
                            AND np.programme_status = 'active' 
                            ".$prog_to_country_where_additional."

                            AND option_prog_other.relationship_name = 'old prog to new prog' 
                            AND op_other.status = 'active' 
                            AND np.deleted = '0'
                            ".$prog_query_where.$prog_query_where_additional.$prog_query_relevance_where_additional." 

                            ".$ignore_course." 
                        ".$prog_query_order_by." 
                        ".$prog_query_limit."";
                        // echo '<pre>'; var_dump($query_DisProg); echo '</pre>'; exit();
        
        if ($prog_query_pagination_mode) { // Use pagination
            // Set pagination variables
            if (!my_is_int($limit)) $limit = 10;
        
            // Execute first step and assign it to a variable
            $pg_resources = paginate_b4_query($query_DisProg, $limit, $precisepage);
            extract($pg_resources);
        
            // Add LIMIT to query
            $query_DisProg = paginate_limit($query_DisProg, $pgstart, $pglimit);
        
            // Execute query
            $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
            $row_DisProg = mysqli_fetch_assoc($DisProg);
            $totalRows_DisProg = mysqli_num_rows($DisProg);
              
            // Backwards pagination support
            if (!$totalRows_DisProg && $_GET['page'] >= 2) {
              $prev = $page - 1;
            }
        
            // Initialize pagination and assign to a variable. The value of which is the set of pagination links.
            $pagination = paginate_after_limit($page, $total_pages, $targetpage, $pgadjacents, $pglimit);
        
            // Record count
            $pagination_count = paginate_record_count($pglimit, $total_pages, $page);
        }
        else { // No pagination
            $DisProg = mysqli_query($config, $query_DisProg) or die(mysqli_error($config));
            $row_DisProg = mysqli_fetch_assoc($DisProg);
            $totalRows_DisProg = mysqli_num_rows($DisProg);
        }

        // echo '<pre>'; var_dump($query_DisProg); echo '</pre>'; exit();
    }
    else {
        $totalRows_DisProg = 0;
    }
    
    // Reset all query parts for next execution (Since this file can get called multiple times on a single page whilst some values spill into the next run)
    if (!$prog_query_preserve_params_for_next_run) {
        include('inc.reset-programme-query-params.php');
    }

    $prog_query_pagination_mode = false;
    $prog_query_preserve_params_for_next_run = false;
}
