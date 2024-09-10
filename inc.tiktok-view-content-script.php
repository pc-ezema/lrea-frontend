<?php // GENERATE TIKTOK SERVER SCRIPT FOR VIEW CONTENT EVENT.
    $tiktok_view_content_event = "";

    if ($enable_tiktok_events_tracking) {
        include('inc.tiktok-events-general-parameters.php');

        $tiktok_contents[] = array(
                            'price' => round($row_DisProg['discounted_fee'], 2),
                            'quantity' => 1,
                            'content_type' => 'course',
                            'content_id' => $row_DisProg['course_code'],
                        );

        $tiktok_view_content_event = "<script>
                                        $(function() {
                                            $.ajax({
                                                url: '".$link_root_default."tiktok/se',
                                                method: 'POST',
                                                data: {
                                                    en: 'ViewContent',
                                                    eip: '".$tiktok_ip_addr."',
                                                    eagent: '".$tiktok_user_agent."',
                                                    eurl: '".$tiktok_evt_url."',
                                                    eval: ".round($row_DisProg['discounted_fee'], 2).",
                                                    ecur: 'GBP',
                                                    cont: ".json_encode(iso88591_to_utf8($tiktok_contents)).",
                                                    ".$tiktok_clid."
                                                    ".$tiktok_email."
                                                }
                                            });
                                        });
                                    </script>";
    }
?>