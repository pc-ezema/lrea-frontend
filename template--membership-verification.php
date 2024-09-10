<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');

    require_once('inc.no-location-in-url.php');

    if ($_POST) { // Form submitted
        validate('membership_number', 'strlen', 32);
        
        if (!$form_errors) {
            if (!preg_match('/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/i', $_POST['membership_number'])) {
                $form_errors['membership_number'] = '<p class="help-block text-danger">Invalid format.</p>';
            }
        }

        if (!$form_errors) {
            // Get the details of the student's subscriptions (if any)
            $membership_info = get_membership_info($_POST['membership_number'], 'membership_info');
        
            if ($response_found = is_array($membership_info)) {
                $has_active_membership = get_membership_info($_POST['membership_number'], 'has_active_subscription');
                $membership_issue_date = get_membership_info($_POST['membership_number'], 'issue_date');

                $status_badge = '<span class="badge rounded-pill bg-danger py-2 px-3" style="font-size: .75em;">Expired</span>';

                if ($has_active_membership) {
                    $status_badge = '<span class="badge rounded-pill bg-success py-2 px-3" style="font-size: .75em;">Active</span>';
                }
            }
            else {
                $didnt_find_result = true;
            }
        }
    }
	
	$page_title = "Membership Verification";
	require_once('components/doctype.inc.php');
?>

    <style>
        .make-text-bold {
            font-family: var(--font-family-bold);
            font-weight: bold;
        }

        .memb-info p strong {
            color: #333;
        }
    </style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>
    <?php include_once('components/beginning-of-body.inc.php'); ?> 

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>
      
    <!-- Main Content -->
    <div class="main-content">
        <div class="jumbotron position-relative py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="text-bold text-white ft-custom-hero-3 mb-4">Membership Verification</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-container py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($response_found) { ?>
                            <div class="card card-body border-rad-15 py-5 mb-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-10 mb-lg-0 mb-3">
                                        <div class="text-start text-secondary memb-info px-2">
                                            <h3 class="pb-4">
                                                Membership Info <?php echo $status_badge; ?>
                                            </h3>

                                            <form action="<?php echo ROOT_DIR_PORTAL.'membership-certificate/'; ?>" method="post" target="_blank">
                                                <p>
                                                    <strong class="make-text-bold">Membership ID:</strong> <?php echo strtoupper($membership_info['membership_id']); ?>
                                                </p>
                                                <p>
                                                    <strong class="make-text-bold">Name:</strong> <?php echo ucwords($membership_info['member_name']); ?>
                                                </p>
                                                <p>
                                                    <strong class="make-text-bold">Membership Type:</strong> <?php echo ucwords($membership_info['membership_type']); ?>
                                                </p>
                                                <p>
                                                    <strong class="make-text-bold">Issue Date:</strong> <?php echo $membership_issue_date; ?>
                                                </p>
                                                <p class="mb-5">
                                                    <strong class="make-text-bold">Expiration Date:</strong> <?php echo $membership_info['membership_expiration']; ?>
                                                </p>

                                                <?php if ($has_active_membership) { ?>
                                                    <input type="hidden" name="mem_id" value="<?php echo strtoupper($membership_info['membership_id']); ?>">

                                                    <button type="submit" class="btn btn-primary me-3">See Copy of Membership Certificate</button>
                                                <?php } ?>

                                                <a href="<?php echo ROOT_DIR."membership-verification" ?>">Verify another certificate</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php if ($didnt_find_result) { ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    <h4 style="color: #000000"><strong>Sorry.</strong></h4>
                                    <p style="margin-top: 10px">We are unable to find any record matching the membership number.</p>
                                </div>
                            <?php } ?>

                            <div class="card card-body border rounded-0 p-lg-5">
                                <form action="" method="post">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control rounded-0" name="membership_number" value="<?php if ($_POST['membership_number']) echo $_POST['membership_number']; ?>" placeholder="e.g 54F39AF0-8039-55E9-8A0E-D8354A2618DE" pattern="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}" title="Must be letters and dashes e.g 54F39AF0-8039-55E9-8A0E-D8354A2618DE" required>
                                                        <label>Membership Number</label>
                                                        <?php if (isset($form_errors['membership_number'])) echo $form_errors['membership_number']; ?>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <p>By continuing, you agree to our <a class="text-green" href="<?php echo $link_root_path; ?>terms">Terms and Conditions</a> and our <a  class="text-green" href="<?php echo $link_root_path; ?>terms/privacy-policy">Privacy Policy</a>.</p>
                                                    <button type="submit" class="btn btn-custom-size btn-custom-color-1 text-white btn-custom w-100 submit-button">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 

    <?php if ($enable_captcha_here) { ?>
        <script src="https://www.google.com/recaptcha/api.js"></script>
    <?php } ?>

    <script>
        $(document).ready(function() {
            $("body").on("click", ".submit-button", function(e) {
                <?php if ($enable_captcha_here) { ?>
                    var captchaClicked = false;

                    if (grecaptcha && grecaptcha.getResponse().length > 0) { // check if the captcha box was ticked
                        captchaClicked = true;
                    } 
                    else { // captcha wasn't ticked
                        $("#myCaptchaError").html("Prove you're not a robot by clicking on the captcha.");
                        captchaClicked = false;

                        setTimeout(function() { // clear the error message after 5 seconds
                            $("#myCaptchaError").html('');
                        }, 5000);
                    }

                    if (!captchaClicked) { // if captcha box is not ticked, do not submit the form
                        e.preventDefault();
                    }
                <?php } ?>
            });
        });
    </script>
  </body>
</html>