<?php 
    $show_module_details = true;
    
    $dis_single_course_modules = array();

    $cacheBurstingDeadline = date("Y-m-d H:i:s", strtotime("+1 hour"));
    $cache_session_id = 'course_module_query';
    $target_key = md5(curPageURL());
    $cached_value = get_entry_cached_in_memory($cache_session_id, $target_key);

    if ($cached_value !== NULL) {
        $dis_single_course_modules = $cached_value;
    }
    else {
        // Get the old prog ID
        $modules_prog_id = get_backend_prog_id($dis_programme_url);

        if ($modules_prog_id) {
            // Check for modules
            mysqli_select_db($config, $database_config);
            $query_DisModules = "SELECT m.* 
                                FROM modules m 
                                WHERE m.prog_id = ".sql_int($modules_prog_id)." 
                                    AND m.status = 'active' 
                                    AND m.deleted = '0'
                                ORDER BY m.sorting DESC, m.module_code ASC";
            $DisModules = mysqli_query($config, $query_DisModules) or die(mysqli_error($config));
            $row_DisModules = mysqli_fetch_assoc($DisModules);
            $totalRows_DisModules = mysqli_num_rows($DisModules);

            if ($totalRows_DisModules) {
                do {
                    $dis_single_course_modules[] = $row_DisModules;
                } while ($row_DisModules = mysqli_fetch_assoc($DisModules));
            }
        }

        add_entry_cached_in_memory($cache_session_id, $target_key, $dis_single_course_modules, $cacheBurstingDeadline);
    }
?>

<div class="courses-tabs <?php if (isset($course_details_type)) echo 'bg-white'; ?> py-5">
    <div class="container">
        <div class="row">            
            <div class="<?php echo isset($course_details_type) ? 'mb-3' : 'order-lg-0 order-1'; ?> col-lg-12 mb-lg-0">
                <div class="tabs-programme mb-5">
                    <ul class="nav nav-pills <?php echo (isset($course_details_type)) ? 'pb-4 nav-fill' : 'p-2'; ?> mb-3" id="pills-tab" role="tablist" style="gap: 15px;">
                        <?php $show_next_next_tab_by_default = true;  ?>

                        <?php if ($dis_programme_overview) { ?> 
                            <?php $show_next_next_tab_by_default = false; ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-about-tab" data-bs-toggle="pill" data-bs-target="#pills-about" type="button" role="tab" aria-controls="pills-about" aria-selected="true">About the course</button>
                            </li>
                        <?php } ?>

                        <?php if ($dis_programme_course_details) { ?> 
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if ($show_next_next_tab_by_default) echo 'active'; ?>" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="false">Course details</button>
                            </li>
                            <?php if ($show_next_next_tab_by_default) $show_next_next_tab_by_default = false; ?>
                        <?php } ?>

                        <?php if ($dis_programme_accreditation) { ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if ($show_next_next_tab_by_default) echo 'active'; ?>" id="pills-accredit-tab" data-bs-toggle="pill" data-bs-target="#pills-accredit" type="button" role="tab" aria-controls="pills-accredit" aria-selected="false">Accreditation</button>
                            </li>
                            <?php if ($show_next_next_tab_by_default) $show_next_next_tab_by_default = false; ?>
                        <?php } ?>

                        <?php if ($dis_programme_entry_requirements) { ?> 
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php if ($show_next_next_tab_by_default) echo 'active'; ?>" id="pills-entry-tab" data-bs-toggle="pill" data-bs-target="#pills-entry" type="button" role="tab" aria-controls="pills-entry" aria-selected="false">Entry requirements</button>
                            </li>
                        <?php } ?>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-certificate-tab" data-bs-toggle="pill" data-bs-target="#pills-certificate" type="button" role="tab" aria-controls="pills-certificate" aria-selected="false">Sample Certificate</button>
                        </li>
                    </ul>

                    <div class="tab-content mb-4" id="pills-tabContent">
                        <?php $show_next_next_tab_content_by_default = true; ?>

                        <?php if ($dis_programme_overview) { ?>
                            <?php $show_next_next_tab_content_by_default = false; ?>
                            <div class="tab-pane fade show active" id="pills-about" role="tabpanel" aria-labelledby="pills-about-tab" tabindex="0">
                                <h3 class="text-bold">About the course</h3>
                                <?php echo $dis_programme_overview; ?>
                            </div>
                        <?php } ?>
                        
                        <?php if ($dis_programme_course_details) { ?> 
                            <div class="tab-pane fade <?php if ($show_next_next_tab_content_by_default) echo 'show active'; ?>" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab" tabindex="0">
                                <h3 class="text-bold">Course details</h3>
                                <?php echo $dis_programme_course_details; ?>
                            </div>
                            <?php if ($show_next_next_tab_content_by_default) $show_next_next_tab_content_by_default = false; ?>
                        <?php } ?>

                        <?php if ($dis_programme_accreditation) { ?>
                            <div class="tab-pane fade <?php if ($show_next_next_tab_content_by_default) echo 'show active'; ?>" id="pills-accredit" role="tabpanel" aria-labelledby="pills-accredit-tab" tabindex="0">
                                <h3 class="text-bold">Accreditation</h3>
                                <?php echo $dis_programme_accreditation; ?>
                            </div>
                            <?php if ($show_next_next_tab_content_by_default) $show_next_next_tab_content_by_default = false; ?>
                        <?php } ?>

                        <?php if ($dis_programme_entry_requirements) { ?> 
                            <div class="tab-pane fade <?php if ($show_next_next_tab_content_by_default) echo 'show active'; ?>" id="pills-entry" role="tabpanel" aria-labelledby="pills-entry-tab" tabindex="0">
                                <h3 class="text-bold">Entry requirement</h3>
                                <?php echo $dis_programme_entry_requirements; ?>
                            </div>
                        <?php } ?>

                        <div class="tab-pane fade" id="pills-certificate" role="tabpanel" aria-labelledby="pills-certificate-tab" tabindex="0">
                            <h3 class="text-bold">Sample Certificate</h3>
                            <div class="row align-items-center">
                                <div class="col-lg-5 mb-lg-0 mb-4">
                                    <img src="<?php echo ROOT_DIR.'images/certificate-verified.jpg?v='.$fileCacheVersion; ?>" class="img-fluid border rounded">
                                </div>

                                <div class="col-lg-6 offset-lg-1">

                                    <div class="mb-4">
                                        <?php if (isset($course_details_type)) { // masters, IPGD ?>
                                            <h4 class="text-bold text-green">Boost your career and your talents</h4>
                                        <?php } else { // short courses ?>
                                            <h5 class="text-bold">Boost your career and your talents</h5>
                                        <?php } ?>
                                        <p>With a validated certificate that demonstrates your learning, wow your employer.</p>
                                    </div>

                                    <div class="mb-4">
                                        <?php if (isset($course_details_type)) { // masters, IPGD ?>
                                            <h4 class="text-bold text-green">Take up a challenge</h4>
                                        <?php } else { // short courses ?>
                                            <h5 class="text-bold">Take up a challenge</h5>
                                        <?php } ?>
                                        <p>All of us occasionally need an additional push. You stay motivated by working for a credential.</p>
                                    </div>

                                    <div class="mb-4">
                                        <?php if (isset($course_details_type)) { // masters, IPGD ?>
                                            <h4 class="text-bold text-green">Let the world know about it</h4>
                                        <?php } else { // short courses ?>
                                            <h5 class="text-bold">Let the world know about it</h5>
                                        <?php } ?>
                                        <p>Using the special URL we give you, you may safely distribute your validated certificate to others.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($programme_covered_by_subscription) { ?>
                        <div class="col-lg-12 mb-2">
                            <a href="<?php echo $course_ecomm_link_action_url; ?>" class="btn btn-custom-size bg-green btn-custom text-white"><?php echo $course_ecomm_link_action_text; ?></a>
                        </div>
                    <?php } else { ?>
                        <?php if (!isset($course_details_type)) { // short courses ?>
                            <?php require('inc.enrol-add-to-cart-buttons.php'); ?>
                        <?php } else { ?>
                            <?php
                                $request_follow_up_button_outline_dark = true;
                                require('inc.enrol-request-buttons-new-2.php');
                            ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>