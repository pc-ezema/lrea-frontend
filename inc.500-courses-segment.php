<?php 
    if (isset($category_to_check_subject_programmes) && $category_to_check_subject_programmes) {
        $de_subject_category = $category_to_check_subject_programmes;
    }
    else {
        $de_subject_category = "all";
    }
?>
<div class="cta position-relative bg-white py-5">
    <div class="container z-index-1 position-relative">
        <div class="mb-4">
            <p class="text-green text-bold mb-1">Our categories</p>
            <h3>Explore London Real Estate Academy</h3>
        </div>
        <div class="row">
            <?php 
                $total_course_in_subject = subject_total_courses('real-estate-management', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/real-estate-management" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">RE</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2 ">Real Estate Management</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('business-management', $de_subject_category); 
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/business-management" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">BM</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Business Management</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('health-and-psychology', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/health-and-psychology" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">HP</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Health and Psychology</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('business-law', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/business-law" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">BL</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Business Law</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('information-technology', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/information-technology" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">IT</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Information Technology</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('personal-development', $de_subject_category); 
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/personal-development" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">PD</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Personal Development</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('teaching-and-education', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/teaching-and-education" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">TE</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Teaching and Education</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('health-and-safety', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/health-and-safety" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">HS</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Health and Safety</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('finance-and-accounting', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/finance-and-accounting" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">FA</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Finance and Accounting</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('office-productivity', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/office-productivity" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">OF</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Office Productivity</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('sales-and-marketing', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/sales-and-marketing" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">SM</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Sales and Marketing</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('leadership-and-entrepreneurship', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/leadership-and-entrepreneurship" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">LE</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Leadership and Entrepreneurship</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('human-resources', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/human-resources" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">HR</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Human Resources</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php
                $total_course_in_subject = subject_total_courses('hotel-travel-and-hospitality-management', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/hotel-travel-and-hospitality-management" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">HT</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2">Hotel, Travel and Hospitality Management</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

            <?php 
                $total_course_in_subject = subject_total_courses('logistics-and-supply-chain-management', $de_subject_category);
                $label_to_use = ($total_course_in_subject > 1) ? 'courses' : 'course';
            ?>
            <?php if ($total_course_in_subject) { ?>
                <div class="col-lg-3">
                    <a href="<?php echo ROOT_DIR.$location_slug; ?>subject/logistics-and-supply-chain-management" class="text-decoration-none">
                        <div class="card mb-3 shadow card-rad card-cat border-0">
                            <div class="row g-0">
                                <div class="col-4">
                                    <div class="prog-img-6 border-rad-img-left">
                                        <h4 class="text-bold text-white">LS</h4>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark-2 ">Logistics and Supply Chain Management</h6>
                                        <p class="card-text mb-0 text-muted"><?php echo $total_course_in_subject.' '.$label_to_use; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>