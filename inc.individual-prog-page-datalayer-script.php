<?php if ($site_version == "online" && $enable_data_layer_scripts && $gtag_pixel_ID) { ?>
    <?php // Prepare DataHash Variables
        $execute_add_to_cart_dataHash = true;

        $dataHash_source = $datalayer_asset_collection_source ? $datalayer_asset_collection_source : "Category Page";
        $datalayer_programme_subject = get_programme_relationship($dis_programme_id, 'programme subjects', ', ');
        $datalayer_programme_level = get_programme_relationship($dis_programme_id, 'programme difficulty');
        $datalayer_subjects = $datalayer_programme_subject ? $datalayer_programme_subject : "Business";
        $datalayer_programme_fee = $dis_programme_fee;        
        $datalayer_programme_type = strtolower(get_programme_relationship($dis_programme_id, 'programme type'));
        $datalayer_programme_duration = get_programme_relationship($dis_programme_id, 'programme duration');
        $datalayer_programme_format = get_programme_relationship($dis_programme_id, 'programme format');
        $datalayer_prog_currency = $coresite_curr_currency;
        $datalayer_prog_name = $dis_programme_name;
        $datalayer_prog_code = $row_DisProg['course_code'] ? $row_DisProg['course_code'] : "";

        $dataHash_source = "Home Page"; // This enables the "list" object of the dataLayer to be the same across the user's journey.
    ?>

    <script>
        <?php // DataHash Product detail event ?>
        if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
            dataLayer.push({
                'event': 'productdetail',
                'ecommerce': {
                    'currencyCode': '<?php echo $coresite_curr_currency ? $coresite_curr_currency : "GBP"; ?>',
                    'detail': {
                        'actionField': {'list': 'Home Page'},
                        'products': [{
                            'name': '<?php echo addslashes($dis_programme_name); ?>',
                            'id': '<?php echo $datalayer_prog_code; ?>',
                            'price': '<?php echo $datalayer_programme_fee; ?>',
                            'brand': '<?php echo $datalayer_subjects; ?>',
                            'category': '<?php echo ucwords(addslashes($datalayer_programme_type)); ?>',
                            'variant': '<?php echo $datalayer_programme_level ? $datalayer_programme_level : ''; ?>',
                            'list': '<?php echo $dataHash_source; ?>',
                            'position': 1,
                            'dimension1': '<?php echo $datalayer_programme_duration; ?>',
                            'dimension2': '<?php echo $datalayer_programme_format; ?>'
                        }]
                    }
                }
            });
        }
    </script>
<?php } ?>