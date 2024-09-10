<?php if (($sess_user_id > 0) && isset($_SESSION['user_type']) && !in_array($_SESSION['user_type'], array('student')) && isset($_SESSION['i_want_to_edit']) && isset($da_editor_link_path)) { // If website editor mode is active and the logged in session is not of student. ?>
    <?php // Determine the country the content can be edited for.
        $content_country_name = $content_country_id = "";

        if (isset($coresite_curr_country_name) && isset($coresite_curr_country_slug)) {
            $content_country_name = $coresite_curr_country_name;
            $content_country_id = is_country($coresite_curr_country_slug, 'countryID');

            if ($content_country_name == 'United Kingdom') {
                $content_country_id = 'GBR';
            }
        }
        elseif (isset($coresite_curr_state_name) && isset($coresite_curr_state_slug)) {
            $content_country_name = $coresite_curr_state_name;
            $content_country_id = is_state($coresite_curr_state_slug, 'countryID');
        }
    ?>

    <?php if (isset($add_container_div) && $add_container_div) { ?>
        <div class="container">
    <?php } elseif (isset($add_container_fluid_div) && $add_container_fluid_div) { ?>
        <div class="container-fluid">
    <?php } ?>
        <div class="dropdown">
            <a class="web-editor-btn dropdown-toggle" style="color: #ffffff !important;" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-pencil"></i>
            </a>

            <div class="dropdown-menu py-3">
                <a class="dropdown-item" href="<?php echo append_to_url($da_editor_link_path, 'co=***'); ?>" target="_blank">All Countries</a>

                <?php if ($content_country_name && $content_country_id) { // Specific country ?>
                    <a class="dropdown-item" href="<?php echo append_to_url($da_editor_link_path, 'co='.$content_country_id); ?>" target="_blank"><?php echo $content_country_name; ?></a>
                <?php } ?>
            </div>
        </div>

    <?php if (isset($add_container_div) || isset($add_container_fluid_div)) { ?>
        </div>
    <?php } ?>
<?php } ?>