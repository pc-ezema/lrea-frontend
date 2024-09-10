<?php 
    foreach ($changes as $changes_key => $changes_value) {
        if (count($changes_value["value"])) {
            $form_id = $changes_value["value"]["form_id"];
            $leadgen_id = $changes_value["value"]["leadgen_id"]; // The leadgen is what is needed to make a request to Facebook which fetches details of the data that was filled.
            
            if ($leadgen_id) {
                // Request to get the information submitted by the lead.
                $leads_form_curl = curl_init();

                curl_setopt_array($leads_form_curl, array(
                    CURLOPT_URL => "https://graph.facebook.com/v11.0/".$leadgen_id."?access_token=".WEBHOOK_PAGE_ACCESS_TOKEN,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));

                $result = curl_exec($leads_form_curl);
                $err = curl_error($leads_form_curl);

                curl_close ($leads_form_curl);
                
                if ($result) {
                    $result = json_decode($result, true);

                    $new_leads_data = $result["field_data"];
                    
                    $columns_to_insert = array();

                    if (count($new_leads_data)) {
                        foreach ($new_leads_data as $lead_data_key => $lead_data_value) { // Extract the details of the new lead.
                            if ($lead_data_value['name'] == "full_name") {
                                $columns_to_insert["full_name"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "email") {
                                $columns_to_insert["email"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "phone") {
                                $columns_to_insert["phone"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "nationality") {
                                $columns_to_insert["country"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "country_of_residence") {
                                $columns_to_insert["country_of_residence"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "highest_level_of_education") {
                                $columns_to_insert["highest_education"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "years_of_experience") {
                                $columns_to_insert["years_experience"] = $lead_data_value['values'][0];
                            }
                            elseif ($lead_data_value['name'] == "gender") {
                                if ($lead_data_value['values'][0] == "male" || $lead_data_value['values'][0] == "Male") {
                                    $columns_to_insert["gender"] = 'm';
                                }
                                elseif ($lead_data_value['values'][0] == "female" || $lead_data_value['values'][0] == "Female") {
                                    $columns_to_insert["gender"] = 'f';
                                }
                                else {
                                    $columns_to_insert["gender"] = $lead_data_value['values'][0];
                                }
                            }
                            elseif ($lead_data_value['name'] == "job_title/position") {
                                $columns_to_insert["position"] = $lead_data_value['values'][0];
                            }
                        }
                    }

                    if (count($columns_to_insert)) {                                
                        $columns_to_insert["programme"] = "";
                        $columns_to_insert["url"] = "";

                        if (array_key_exists($form_id, $array_of_advertised_programmes)) { // Determine which programme this lead is interested in.
                            $pro_url_name = $array_of_advertised_programmes[$form_id];
                            $columns_to_insert["url"] = ROOT_DIR."programme/".$array_of_advertised_programmes[$form_id];
                            
                            // Get the programme name.
                            mysqli_select_db($config, $database_config);
                            $query_Prog_Name =  "SELECT np.programme_name
                                                FROM option_relationships oprel 
                                                JOIN options op ON oprel.option_two = op.option_id 
                                                JOIN new_programmes np ON oprel.option_one  = np.programme_id 
                                                WHERE oprel.relationship_name = 'programme type' 
                                                    AND np.programme_status = 'active' 
                                                    AND np.url_name = '".sql_string($pro_url_name)."'
                                                    AND np.deleted = '0'
                                                LIMIT 1";
                            $Prog_Name = mysqli_query($config, $query_Prog_Name) or die(mysqli_error($config));
                            $row_Prog_Name = mysqli_fetch_assoc($Prog_Name);
                            $totalRows_Prog_Name = mysqli_num_rows($Prog_Name);

                            if ($totalRows_Prog_Name) {
                                $columns_to_insert["programme"] = $row_Prog_Name["programme_name"];
                            }
                        }

                        // Submit the lead details into the database
                        $insertSQL = "INSERT IGNORE INTO landing_page_forms (full_name, email, phone, country, programme, highest_education, years_experience, how_did_hear, 
                                                                            utm_source, recipients, page_url, entry_date, source, gender, position, branch, country_of_residence, from_webhook)
                                    VALUES (
                                        '".sql_string($columns_to_insert['full_name'])."',
                                        '".sql_string($columns_to_insert['email'])."',
                                        '".sql_string($columns_to_insert['phone'])."',
                                        '".sql_string($columns_to_insert['country'])."',
                                        '".sql_string($columns_to_insert['programme'])."',
                                        '".sql_string($columns_to_insert["highest_education"])."',
                                        '".sql_int($columns_to_insert["years_experience"])."',
                                        '".sql_string($source)."',
                                        '".sql_string($utm_source)."',
                                        '".sql_string($recipients)."',
                                        '".sql_string($columns_to_insert["url"])."',
                                        '".sql_string($time_now)."',
                                        '".sql_string($source)."',
                                        '".sql_string($columns_to_insert["gender"])."',
                                        '".sql_string($columns_to_insert["position"])."',
                                        '".sql_string($branch)."',
                                        '".sql_string($columns_to_insert["country_of_residence"])."',
                                        '1'
                                    )";
                        mysqli_select_db($config, $database_config);
                        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                        $insertStatus = mysqli_affected_rows($config);
                    }
                }
            }
        } 
    }