<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
    // Get all the active subscriptions
    mysqli_select_db($config, $database_config);
    $query_ActiveSubscriptions = "SELECT * 
                                FROM subscriptions 
                                WHERE status = 'active' 
                                    AND slug <> '' 
                                    AND deleted = '0' 
                                ORDER BY sub_name ASC";
    $ActiveSubscriptions = mysqli_query($config, $query_ActiveSubscriptions) or die(mysqli_error($config));
    $row_ActiveSubscriptions = mysqli_fetch_assoc($ActiveSubscriptions);
    $totalRows_ActiveSubscriptions = mysqli_num_rows($ActiveSubscriptions);

    if (!$totalRows_ActiveSubscriptions) {
        $_SESSION['userfeedback'] = "There are no subscriptions at the moment.";
        $_SESSION['feedbacktype'] = "error";

        redirect(ROOT_DIR);
    }

    $lowest_subscription_amount = '£40.00';

    if ($totalRows_ActiveSubscriptions) {
        $all_ActiveSubscriptions = [];

        do {
            // if the currency was not set previously, then perform the check and set the variable to a value
            if ($coresite_curr_currency && !$symbol_for_programme_currency) {
                $symbol_for_programme_currency = get_passed_currency_symbol($coresite_curr_currency);
            }

            // Initialize the subscription amount
            $unformatted_subscription_amount = $row_ActiveSubscriptions['amount'];

            // Apply the discount if it exists.
            if ($row_ActiveSubscriptions['discount']) { 
                $unformatted_subscription_amount = $row_ActiveSubscriptions['amount'] - $row_ActiveSubscriptions['discount'];
            }

            // Convert currency if necessary
            if ($row_ActiveSubscriptions['currency'] != $coresite_curr_currency) {
                $unformatted_subscription_amount = convert_amount_from_currency($row_ActiveSubscriptions['currency'], $coresite_curr_currency, $row_ActiveSubscriptions['amount']);
            }

            // Format the subscription amount
            $subscription_amount = $symbol_for_programme_currency.' '.number_format($unformatted_subscription_amount);

            // Initialize the monthly amount info
            $monthly_subscription_amount = $unformatted_subscription_amount;

            if ($row_ActiveSubscriptions['duration'] == '12 months') {
                $subscription_duration = 'year';
                $billing_duration_text = 'annually';
                $monthly_subscription_amount = ceil($monthly_subscription_amount/12);
            }
            elseif (in_array($row_ActiveSubscriptions['duration'], array('6 months', '3 months'))) {
                $subscription_duration = $row_ActiveSubscriptions['duration'];

                if ($row_ActiveSubscriptions['duration'] == '6 months') {
                    $billing_duration_text = 'bi-annually';
                    $monthly_subscription_amount = ceil($monthly_subscription_amount/6);
                }
                elseif ($row_ActiveSubscriptions['duration'] == '3 months') {
                    $billing_duration_text = 'quarterly';
                    $monthly_subscription_amount = ceil($monthly_subscription_amount/3);
                }
            }
            else {
                $subscription_duration = 'month';
                $billing_duration_text = 'monthly';
            }

            $monthly_subscription_amount_plus_currency = $symbol_for_programme_currency.' '.number_format($monthly_subscription_amount);
            // Add monthly amount to fetched associative array
            $row_ActiveSubscriptions['subscription_amount'] = $subscription_amount;
            $row_ActiveSubscriptions['monthly_subscription_amount'] = $monthly_subscription_amount;
            $row_ActiveSubscriptions['billing_duration_text'] = $billing_duration_text;
            $row_ActiveSubscriptions['monthly_subscription_amount_plus_currency'] = $monthly_subscription_amount_plus_currency;
            // Push associative array into empty array created earlier
            $all_ActiveSubscriptions[] = $row_ActiveSubscriptions;
        } while ($row_ActiveSubscriptions = mysqli_fetch_assoc($ActiveSubscriptions));
        
        if (count($all_ActiveSubscriptions)) {
            $monthly_amount_column = array_column($all_ActiveSubscriptions, 'monthly_subscription_amount');
            array_multisort($monthly_amount_column, SORT_ASC, $all_ActiveSubscriptions);

            $lowest_subscription_amount = $all_ActiveSubscriptions[0]['monthly_subscription_amount_plus_currency'];
        }
    }
	
	$page_title = SITE_NAME . ' Subscriptions';
	require_once('components/doctype.inc.php');
?>

    <style>

    </style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <div class="main-content">

        <div class="container">
            <div class="hero-sections rounded-8 py-100 mt-custom position-relative text-white z-index-1 bg-main-3 add-bg-image-2" style="background-image: url(<?php echo ROOT_DIR; ?>images/subscribe-banner.jpg);">

                <div class="overlay rounded-8"></div>

                <div class="container-fluid position-relative z-index-1">

                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            
                            <h1 class="text-white text-bold ft-custom-hero">World-Class Education</h1>
                            <p class="ft-normal-2 text-white">Gain unlimited access to 1,000+ expert-led courses from top universities. Learn at your own pace, master new skills, and open doors to endless opportunities.</p>

                            <div class="row">
                                <div class="col-lg-6 mb-lg-0 mb-3">
                                    <a class="btn btn-custom-size text-bold bg-green w-100 text-white btn-custom" href="#cta">Start Learning</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta position-relative bg-dark-green pt-5" id="cta">
            <div class="bg-mask"></div>
            <div class="container z-index-1 position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="text-center mb-4">
                            <h2 class="text-bold text-upper-custom ft-custom-hero-2">Find the right plan for you.</h2>
                            <p>Select the plan that fits your needs and access a range of courses, projects, and professional certificates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sub-box pb-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-10 mx-auto">
                        <div class="row">
                        <?php 
                        if (count($all_ActiveSubscriptions)) {
                            foreach ($all_ActiveSubscriptions as $subscription) { 
                                $collapseID = 'collapseExample'.$subscription['sub_id'];
                        
                                // Get the courses available in this subscription
                                $courses_in_sub = get_subscription_courses_breakdown($subscription['sub_id']);
                                $show_expandable = count($courses_in_sub) > 0;
                        
                                // Define includes for each subscription
                                $includes = [];
                        
                                switch ($subscription['sub_name']) {
                                    case 'Foundation Membership':
                                        $includes = [
                                            'Courses and Self-Paced Learning',
                                            'Learning Community',
                                            'Library Access & Study Tests',
                                            'Global Recognition',
                                            'Digital Certificate',
                                            'Virtual Membership Card',
                                            'Lapel Pin',
                                            'Free UK Visa Application Support',
                                            "Free Master's Application Service",
                                            "Official LREA Endorsement Letter",
                                            "Master's Personal Statement & Scholarship Support"
                                        ];
                                        break;
                        
                                    case 'Platinum Membership':
                                        $includes = [
                                            'Courses and Self-Paced Learning',
                                            'Learning Community',
                                            'Library Access & Study Tests',
                                            'Global Recognition',
                                            'Digital Certificate',
                                            'Virtual Membership Card',
                                            'Lapel Pin',
                                            'Free UK Visa Application Support',
                                            "Free Master's Application Service",
                                            "Official LREA Endorsement Letter",
                                            "Master's Personal Statement & Scholarship Support",
                                            'Discounted Exclusive Real Estate Event',
                                            'ISIC Student Card Advantage',
                                            "Vista Holdings & BRG Events Discounts"    
                                        ];
                                        break;
                        
                                    case 'Diamond Elite Membership':
                                        $includes = [
                                            'Courses and Self-Paced Learning',
                                            'Learning Community',
                                            'Library Access & Study Tests',
                                            'Global Recognition',
                                            'Digital Certificate',
                                            'Virtual Membership Card',
                                            'Lapel Pin',
                                            'Free UK Visa Application Support',
                                            "Free Master's Application Service",
                                            "Official LREA Endorsement Letter",
                                            "Master's Personal Statement & Scholarship Support",
                                            'Discounted Exclusive Real Estate Event',
                                            'ISIC Student Card Advantage',
                                            "Vista Holdings & BRG Events Discounts",
                                            "Founders Card Advantage",
                                            "Global Travel Priviledges",
                                            "Global Office Access"
                                        ];
                                        break;
                                }
                        ?>
                            <div class="col-lg-4 mb-3">
                                <div class="card border shadow-sm rounded border-radius-20 h-100 p-3">
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-center">
                                            <h3 class="text-bold text-upper-custom"><?php echo $subscription['sub_name']; ?></h3>
                                            <h2 class="text-bold mb-3">
                                                <span class="text-success-color-2"><?php echo $subscription['subscription_amount']; ?></span>
                                                <span class="ft-sm">/yearly</span>
                                            </h2>
                                        </div>
                        
                                        <h5 class="text-bold">Includes</h5>
                                        <ul class="list-group list-group-flush flex-grow-1 mb-3">
                                            <li class="list-group-item d-flex align-items-start px-0" 
                                                <?php if ($show_expandable) { ?> 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#<?php echo $collapseID; ?>" 
                                                    aria-expanded="false" 
                                                    aria-controls="<?php echo $collapseID; ?>" 
                                                <?php } ?>>
                                                <div class="icon-circle-success me-2"><i data-feather="check"></i></div> 
                                                Courses in this subscription 
                                                <?php if ($show_expandable) { ?><i class="fa fa-caret-down text-secondary ms-2 mt-1"></i><?php } ?>
                                            </li>
                        
                                            <?php if ($show_expandable) { ?>
                                                <div class="collapse" id="<?php echo $collapseID; ?>">
                                                    <ul class="text-secondary" style="list-style-type: square;">
                                                        <?php foreach ($courses_in_sub as $item) { ?>
                                                            <li class="py-1"><?php echo $item['num_of_courses'].' '.$item['course_type'].' '.$item['multiples_wording']; ?></li>
                                                        <?php } ?>   
                                                    </ul>
                                                </div>
                                            <?php } ?>
                        
                                            <!-- Render dynamic includes -->
                                            <?php foreach ($includes as $inc) { ?>
                                                <li class="list-group-item d-flex align-items-start px-0">
                                                    <div class="icon-circle-success me-2"><i data-feather="check"></i></div> <?php echo $inc; ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                        
                                        <a href="<?php echo ROOT_DIR."subscription-courses/".$subscription['slug']; ?>" class="btn btn-outline-dark text-bold w-100">Explore courses</a>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            } // end foreach
                        } // end if 
                        ?>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-light py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mb-5">
                            <h2 class="text-bold text-upper-custom ft-custom-hero-2">Take a step toward your professional ambitions through <?php echo SITE_NAME; ?></h2>
                            <p>Access a vast array of courses, Projects, Specializations, and Professional Certificates on <?php echo SITE_NAME; ?>, with instruction from prestigious universities.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">

                        <div class="card course-new-card bg-white border rounded-0 h-100">
                            <div class="card-body prog-card-body d-flex flex-column">

                                <div class="prog-img-3 mb-3">
                                    <img src="<?php echo ROOT_DIR; ?>images/subscribe-img-1.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
                                </div>

                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-4 text-bold">Grasp everything</h5>
                                    <p class="text-white">Get acquainted with any curiosity or trending subject, meet prerequisites, and boost your skills.</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">

                        <div class="card course-new-card bg-white border rounded-0 h-100">
                            <div class="card-body prog-card-body d-flex flex-column">

                                <div class="prog-img-3 mb-3">
                                    <img src="<?php echo ROOT_DIR; ?>images/subscribe-img-2.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
                                </div>

                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-4 text-bold">Keep more in your pocket</h5>
                                    <p class="text-white">When planning to take multiple courses this year, you'll spend less on your education.</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">

                        <div class="card course-new-card bg-white border rounded-0 h-100">
                            <div class="card-body prog-card-body d-flex flex-column">

                                <div class="prog-img-3 mb-3">
                                    <img src="<?php echo ROOT_DIR; ?>images/subscribe-img-3.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
                                </div>

                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-4 text-bold">Malleable learning</h5>
                                    <p class="text-white">Mold your learning experience to match your pace, switch between multiple courses, or venture into a different course.</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">

                        <div class="card course-new-card bg-white border rounded-0 h-100">
                            <div class="card-body prog-card-body d-flex flex-column">

                                <div class="prog-img-3 mb-3">
                                    <img src="<?php echo ROOT_DIR; ?>images/subscribe-img-4.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
                                </div>

                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-4 text-bold">Certificates without boundaries</h5>
                                    <p class="text-white">Earn a certificate for every completed learning program without any additional fees.</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center bg-white pt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <a href="#cta" class="btn btn-outline-main btn-lg btn-custom w-100"> Subscribe &amp; Save </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="degree-info-accordion bg-white py-5">
            <div class="container">
                <div class="row">

                    <div class="col-lg-8 mx-auto">
                        <div class="text-center mb-5">
                            <h2 class="text-bold">Frequently Asked Questions</h2>
                            <p>We have put together answers to the most common queries about our membership to help you get started.</p>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="accordion info-accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button accordion-text-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Can I cancel at any time?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="wpb_wrapper">
                                            <p>You can cancel your subscription at any time, with no further charges. Your membership will remain active until the end of the current term.</p>

                                            <p>For annual subscriptions, the fee will cover the remaining portion of the year.</p>

                                            <p>You’ll continue to enjoy all member benefits until your membership anniversary. Upon renewal, you’ll regain access to all your courses, even if they were previously restricted.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed accordion-text-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        How is <?php echo SITE_NAME; ?> membership better than just buying each course separately?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p>The <?php echo SITE_NAME; ?> Membership provides the best value across our entire course catalog, along with exclusive benefits to enhance your education and special discounts on online resources and certifications.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed accordion-text-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Can I pay in installments?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p>The <?php echo SITE_NAME; ?> membership is available only as a yearly subscription and cannot be paid in installments.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
</body>
</html>