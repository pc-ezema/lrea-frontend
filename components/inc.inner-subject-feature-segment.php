<?php 
    $array_of_categories_to_pick = array("masters", "postgraduate diploma", "semp online", "advanced professional", "short course");
    shuffle($array_of_categories_to_pick);
?>
<div class="side-nav-list find course-list-msbm main-thumbnail-view font-fam mb-0">
    <div class="row">
        <?php 
            $subject_feature_segment = getSubjectFeatureProgramme($array_of_categories_to_pick[0], $subject_of_focus);
        ?>
        <?php if (is_array($subject_feature_segment) && count($subject_feature_segment)) { ?>
            <?php 
                if (defined("ROOT_PATH_TO_PORTAL")) { // Main website
                    if (file_exists('inc.subject-mega-menu-variables.php')) {
                        include('inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('../inc.subject-mega-menu-variables.php')) {
                        include('../inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('components/inc.subject-mega-menu-variables.php')) {
                        include('components/inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('../components/inc.subject-mega-menu-variables.php')) {
                        include('../components/inc.subject-mega-menu-variables.php'); 
                    }
                }
                else { // Portal
                    if (defined("FILE_PATH_TO_UK") && file_exists(FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php')) {
                        include(FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists('../'.FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php')) {
                        include('../'.FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists(FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php')) {
                        include(FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists('../'.FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php')) {
                        include('../'.FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php'); 
                    }
                }
            ?>

            <div class="col-lg-12 col-md-6 mb-3">
                <a href="<?php echo $link_root_path.'programme/'.$subject_prog_url; ?>" class="card-ahref-flex position-relative h-100">
                    <div class="card-img-wrap">
                        <img src="<?php echo $subject_prog_thumbnail; ?>" class="card-img-top" alt="<?php echo $subject_feature_segment['programme_name']; ?>">
                    </div>
                    <div class="card-body">
                        <?php /* ?><p class="card-text card-uni">UK Partner University</p><?php */ ?>
                        <h6 class="card-title"><?php echo $subject_feature_segment['programme_name']; ?></h6>
                    </div>
                </a>
            </div>
        <?php } ?>

        <?php 
            $subject_feature_segment = getSubjectFeatureProgramme($array_of_categories_to_pick[1], $subject_of_focus);
        ?>
        <?php if (is_array($subject_feature_segment) && count($subject_feature_segment)) { ?>
            <?php 
                if (defined("ROOT_PATH_TO_PORTAL")) { // Main website
                    if (file_exists('inc.subject-mega-menu-variables.php')) {
                        include('inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('../inc.subject-mega-menu-variables.php')) {
                        include('../inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('components/inc.subject-mega-menu-variables.php')) {
                        include('components/inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (file_exists('../components/inc.subject-mega-menu-variables.php')) {
                        include('../components/inc.subject-mega-menu-variables.php'); 
                    }
                }
                else { // Portal
                    if (defined("FILE_PATH_TO_UK") && file_exists(FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php')) {
                        include(FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists('../'.FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php')) {
                        include('../'.FILE_PATH_TO_UK.'inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists(FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php')) {
                        include(FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php'); 
                    }
                    elseif (defined("FILE_PATH_TO_UK") && file_exists('../'.FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php')) {
                        include('../'.FILE_PATH_TO_UK.'components/inc.subject-mega-menu-variables.php'); 
                    }
                }
            ?>

            <div class="col-lg-12 col-md-6 mb-3">
                <a href="<?php echo $link_root_path.'programme/'.$subject_prog_url; ?>" class="card-ahref-flex position-relative h-100">
                    <div class="card-img-wrap">
                        <img src="<?php echo $subject_prog_thumbnail; ?>" class="card-img-top" alt="<?php echo $subject_feature_segment['programme_name']; ?>">
                    </div>
                    <div class="card-body">
                        <?php /* ?><p class="card-text card-uni">UK Partner University</p><?php */ ?>
                        <h6 class="card-title"><?php echo $subject_feature_segment['programme_name']; ?></h6>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>