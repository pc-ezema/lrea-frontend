<?php 

if ($coresite_curr_currency != BASE_CURRENCY) {
    // If today's rates for this currency has not been stored
    if (!$todays_conversion_rate = get_todays_conversion_rate($coresite_curr_currency)) {
        // Get today's rate
        $api_conversion_rate = get_api_conversion_rate(BASE_CURRENCY, $coresite_curr_currency); 

        if ($api_conversion_rate) { 
            store_todays_conversion_rate(BASE_CURRENCY, $coresite_curr_currency, $api_conversion_rate);

            $todays_conversion_rate = $api_conversion_rate;
        }
        else { // If request failed, use last known rate
            if (!$todays_conversion_rate = get_last_conversion_rate($coresite_curr_currency, BASE_CURRENCY)) {
                if ($coresite_curr_currency != PREFERRED_CURRENCY) { // Try PREFERRED_CURRENCY, most likely GBP
                    $coresite_curr_currency = PREFERRED_CURRENCY;
                    
                    if (!$todays_conversion_rate = get_todays_conversion_rate($coresite_curr_currency)) {
                        $api_conversion_rate = get_api_conversion_rate(BASE_CURRENCY, $coresite_curr_currency); 

                        if ($api_conversion_rate) { 
                            $todays_conversion_rate = $api_conversion_rate;
                        }
                        else { // If all else fails, default to BASE_CURRENCY
                            $coresite_curr_currency = BASE_CURRENCY;
                            $todays_conversion_rate = DEFAULT_BASE_CURRENCY_CONVERSION_RATE;
                        }
                    }
                }
            }
        }
    }
}
else {
    $todays_conversion_rate = 1.00;
}

