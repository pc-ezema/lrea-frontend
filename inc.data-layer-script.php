<?php if ($site_version == "online" && $enable_data_layer_scripts && $gtag_pixel_ID) { ?>
    <script>
        if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
            dataLayer.push({
                'event': 'productImpression',
                'ecommerce': {
                    'currencyCode': '<?php echo $coresite_curr_currency ? $coresite_curr_currency : "GBP"; ?>',
                    'impressions': <?php echo json_encode(iso88591_to_utf8($data_layer_programme_items)); ?>
                }
            });
        }
    </script>
<?php } ?>