<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');
    require_once(ROOT_PATH_TO_PORTAL.'PHPMailer/PHPMailerAutoload.php');

    $use_phpmailer = true;
    $use_captcha_on_this_page = true;

    require_once('inc.no-location-in-url.php');

    if (!$show_contact_us_feature) { 
        redirect(ROOT_DIR);
    }

    $recipient_Email = array(
        "feedback" => "info@lrea.co.uk",
        "support" => "students.support@lrea.co.uk, info@lrea.co.uk",
        "technical" => "technical.support@lrea.co.uk, info@lrea.co.uk",
        "complaint" => "info@lrea.co.uk",
        "press" => "info@lrea.co.uk",
        "story" => "info@lrea.co.uk",
        "callback" => "info@lrea.co.uk",
        "affiliate" => "info@lrea.co.uk",
    );

    $proceed_to_send_the_mail = true;

    if ($_POST) { // Form submitted
        $uniqueness_param = sha1($_POST['subject'].$_POST['msg']);

        validate('message_type');
        validate('full_name', 'strlen', 2);
        validate('subject', 'strlen', 4);
        validate('msg', 'strlen', 4);
        validate('email', 'email');

        if ($enable_captcha_here && CAPTCHA_SITE_KEY) {
            if ($_POST["g-recaptcha-response"]) {
                // connect to the captcha api
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => "https://www.google.com/recaptcha/api/siteverify",
                    CURLOPT_POST            => 1,
                    CURLOPT_POSTFIELDS      => [
                        "secret"    => CAPTCHA_SECRET_KEY,
                        "response"  => $_POST["g-recaptcha-response"],
                    ],
                ]);

                $response = json_decode(curl_exec($curl));

                if (!$response->success) { // if captcha fails, display error message
                    $form_errors["captcha"] = '<p class="help-block text-danger">Invalid captcha</p>';
                }
            }
            else {
                $form_errors["captcha"] = '<p class="help-block text-danger">Invalid captcha</p>';
            }
        }

        $individual_info = array();

        // Add the reason to the email
        $individual_info[] = "REASON : ".$_POST['message_type']."\r\n";

        // Add the URL to the email
        $individual_info[] = "URL : ".curPageURL()."\r\n";

        foreach ($_POST as $key => $value) { // Iterate through values
            if ($key != 'message_type') {
                if ($key != 'g-recaptcha-response') { // remove google captcha response code from the retrieved form info
                    $individual_info[] = strtoupper($key)." : \r\n".trim($value)."\r\n";

                    foreach($malicious_keywords_to_lookout_for as $malicious_content) { // Scan through the contents for any malicious content.
                        if (in_array($key, array("subject", "msg")) && preg_match("/{$malicious_content}/i", $value)) { // Scan the subject and body of the message.
                            $proceed_to_send_the_mail = false;
                            break;
                        }
                    }
                }
            }
        }

        // Prevent multiple sending if the exact same copy of this content was the last one sent
        if ($_SESSION['contact_us_uniqueness'] == $uniqueness_param) {
            $proceed_to_send_the_mail = false;
        }

        // get recipient email address
        if (array_key_exists($_POST['message_type'], $recipient_Email)) {
            $to_email = $recipient_Email[$_POST['message_type']];
        } 
        
        if (!$form_errors) {
            $subject = ucfirst($_POST['subject']).' at '.date("Y-m-d H:i:s");
            
            //email body
            $message_body = implode("\r\n", $individual_info);
            
            if ($site_version == 'localhost') {
                echo '<pre>'; var_dump($_POST["g-recaptcha-response"], $to_email, $subject, $message_body, $proceed_to_send_the_mail); echo '</pre>'; exit();  
            }

            if ($proceed_to_send_the_mail) { // If there's no malicious content in the contents of the mail.
                if ($use_phpmailer) {
                    if ($temporarily_use_database_for_email_sending_process) {
                        $email_parameters_to_serialize = array();

                        $all_recipients = explode(",", $to_email);

                        $from_name_to_use = 'LREA Contact Form';
                        $from_email_to_use = 'no-reply@lrea.co.uk';

                        $email_parameters_to_serialize['subject'] = $subject;
                        $email_parameters_to_serialize['recipient'] = $all_recipients;
                        $email_parameters_to_serialize['from_name'] = $from_name_to_use;
                        $email_parameters_to_serialize['from_email'] = $from_email_to_use;
                        $email_parameters_to_serialize['reply_to_name'] = $from_name_to_use;
                        $email_parameters_to_serialize['reply_to_email'] = $from_email_to_use;
                        $email_parameters_to_serialize['mail_body'] = $message_body;

                        $serialized_mail_content = serialize($email_parameters_to_serialize);
                        $cron_name = 'contact-us-form '.$time_now;
                        
                        // Insert into the cron_execution table
                        $insertSQL = "INSERT INTO cron_execution (cron_name, last_run, additional_info)
                                    VALUES (
                                        '".sql_string($cron_name)."',
                                        '".sql_string($time_now)."',
                                        '".sql_string_br_extended($serialized_mail_content)."'
                                    )                                
                                    ON DUPLICATE KEY UPDATE last_run = VALUES(last_run),
                                                            additional_info = VALUES(additional_info)";
                        mysqli_select_db($config, $database_config);
                        $Result1 = mysqli_query($config, $insertSQL) or die(mysqli_error($config));
                        $insertStatus = mysqli_affected_rows($config);

                        if ($insertStatus > 0) { // If INSERT is successful
                            $send_mail = true;
                        }
                        else { // If INSERT failed
                            $send_mail = false;
                        }
                    }
                    else {
                        // Initialize SMTP stuff
                        $mail                = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth      = true;
                        //$mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent, reduces SMTP overhead 
                        $mail->Host          = MAILGUN_2_HOST_NAME; 
                        $mail->Port          = MAILGUN_2_PORT; 
                        $mail->SMTPSecure    = MAILGUN_2_ENCRYPTION; 
                        $mail->Username      = MAILGUN_2_STAFF_EMAILS_USERNAME;
                        $mail->Password      = MAILGUN_2_STAFF_EMAILS_PASSWORD;
                        
                        $mail->XMailer 		 = 'Metropolitan School Of Business & Management'; // To replace "Using PHPMailer 5.2.14 (https://github.com/PHPMailer/PHPMailer)"
                                        
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        ); //PHP 5.6 verifies SSL certificates by default, and if your cert doesn't match, it will fail. This overrides the verification.
                        
                        $mail->IsHTML(true); // Set email format to HTML
            
                        /*$mail->SMTPDebug  = 2;
                        $mail->Debugoutput = 'html';*/

                        // From and Reply email address must match 
                        // otherwise the remote server won't 
                        // send the email out
                        $mail->SetFrom('no-reply@lrea.co.uk', 'LREA Contact Form');
                        $mail->AddReplyTo('no-reply@lrea.co.uk', 'LREA Contact Form');
                                        
                        $mail->Subject    = $subject;
                        $mail->MsgHTML(nl2br($message_body));
                        $mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!';

                        $all_recipients = explode(",", $to_email);
                        foreach ($all_recipients as $curr_recipient) {
                            $mail->AddAddress(trim($curr_recipient));
                        }

                        $send_mail = $mail->Send();
                    }
                }
                else {
                    $send_mail = @mail($to_email, $subject, $message_body);
                }
                
                if (!$send_mail) {
                    //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
                    $form_errors[] = 'Sorry, something went wrong! Please try again later';
                } else {
                    // Record the last sent in session to prevent multiple sending
                    $_SESSION['contact_us_uniqueness'] = $uniqueness_param;

                    $form_completed = true;
                }

                if ($use_phpmailer && !$temporarily_use_database_for_email_sending_process) {
                    // Close the SMTP Connection
                    $mail->smtpClose();
                }
            }
            else { // Just display the success message but no mail would be sent since there's malicious content in it.
                $form_completed = true;
            }
        }
    }

	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = "Contact Us";
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
      
    <!-- Main Content -->
    <div class="main-content">
        <div class="jumbotron position-relative py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="text-bold text-white ft-custom-hero-3 mb-4">Contact us</h1>
                        <p class="text-white">Please provide your contact information and we will contact you. For admissions-related inquiries, our skilled learning consultant can assist you in charting a career path.</p>
                    </div>
                </div>
            </div>
        </div>

        <?php /*
            <div class="container position-relative z-index-1" style="margin-top: -30px">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="box-details p-lg-5 p-3 text-dark rounded-8 bg-white">
                            <div class="row">
                                <div class="col-lg-4 mb-lg-0 mb-3">
                                    <div class="media-view d-flex">
                                        <div class="media-view-img flex-shrink-0 me-2">
                                            <i data-feather="globe"></i>
                                        </div>
                                        <div class="media-view-body">
                                            <div>
                                                <h5 class="text-bold">Address 1</h5>
                                                <p class="mb-0 ft-normal-2">7804 Academy Trl NE
                                                    Albuquerque, New Mexico(NM), 87109</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 mb-lg-0 mb-3">
                                    <div class="media-view d-flex">
                                        <div class="media-view-img flex-shrink-0 me-2">
                                            <i data-feather="globe"></i>
                                        </div>
                                        <div class="media-view-body">
                                            <div>
                                                <h5 class="text-bold">Address 2</h5>
                                                <p class="mb-0 ft-normal-2">7804 Academy Trl NE
                                                    Albuquerque, New Mexico(NM), 87109</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 mb-lg-0 mb-3">
                                    <h5 class="text-bold">Social</h5>
                                    <ul class="list-unstyled d-flex footer-social-icon align-items-center justify-content-start h-100 mb-0" style="gap: 15px">
                                        <li><a href="#"><i data-feather="facebook" style="width: 20px; height: 20px;"></i></a></li>
                                        <li><a href="#"><i data-feather="linkedin" style="width: 20px; height: 20px;"></i></a></li>
                                        <li><a href="#"><i data-feather="instagram" style="width: 20px; height: 20px;"></i></a></li>
                                        <li><a href="#"><i data-feather="twitter" style="width: 20px; height: 20px;"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> */
        ?>

        <div class="contact-container py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($_GET['message'] == 'received' || $form_completed) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 27px">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <h4 style="color: #000000"><strong>Your message has been received</strong></h4>
                                <p style="margin-top: 30px">We will be in touch with you soon.</p>
                            </div>
                        <?php } else { ?>
                            <div class="card card-body border rounded-0 p-lg-5">
                                <h5 class="text-bold text-green">Submit a request</h5>
                                <form action="" method="post">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control rounded-0" name="full_name" placeholder="Full Name" pattern=".{2,}" title="Must be at least 2 characters." required>
                                                <label>Full Name</label>
                                                <?php if (isset($form_errors['full_name'])) echo $form_errors['full_name']; ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" name="email" class="form-control rounded-0" placeholder="Email Address" pattern="[a-zA-Z0-9]+(([_+.-]{1})?[a-zA-Z0-9]+)*@[a-zA-Z0-9]+((-{1})?[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]{2,})+" title="Kindly confirm that there is no error in your email address." required>
                                                <label>Email Address</label>
                                                <?php if (isset($form_errors['email'])) echo $form_errors['email']; ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" name="subject" class="form-control rounded-0" placeholder="Subject" pattern=".{4,}" title="Must be at least 4 characters." required>
                                                <label>Subject</label>
                                                <?php if (isset($form_errors['subject'])) echo $form_errors['subject']; ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-floating mb-3">
                                                <select name="message_type" required class="form-select rounded-0" id="floatingSelect" aria-label="Floating label select">
                                                    <option selected>--Choose--</option>
                                                    <option value="support">Support</option>
                                                    <option value="feedback">Feedback</option>
                                                    <option value="complaint">Complaint</option>
                                                    <option value="support">Technical Issues</option>
                                                    <option value="story">Story</option>
                                                    <option value="callback">Require a call-back</option>
                                                </select>
                                                <label for="floatingSelect">Enquiry Type</label>
                                                <?php if (isset($form_errors['message_type'])) echo $form_errors['message_type']; ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-floating mb-3">
                                                <textarea required name="msg" class="form-control rounded-0" placeholder="Leave a message here" id="floatingTextarea2" style="height: 100px"></textarea>
                                                <label for="floatingTextarea2">Your Message</label>
                                                <?php if (isset($form_errors['msg'])) echo $form_errors['msg']; ?>
                                            </div>
                                        </div>

                                        <?php if ($enable_captcha_here && CAPTCHA_SITE_KEY) { ?>
                                            <div class="form-group">
                                                <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?php echo CAPTCHA_SITE_KEY; ?>"></div>
                                                <?php echo $form_errors["captcha"]; ?>
                                                <p id="myCaptchaError" class="text-danger"></p>
                                            </div>
                                        <?php } ?>

                                        <div class="col-lg-12">
                                            <p>By submitting the form, you agree to our <a class="text-green" href="<?php echo $link_root_path; ?>terms">Terms and Conditions</a> and our <a  class="text-green" href="<?php echo $link_root_path; ?>terms/privacy-policy">Privacy Policy</a>.</p>
                                            <button type="submit" class="btn btn-custom-size btn-custom-color-1 text-white btn-custom w-100 submit-button">Submit</button>
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