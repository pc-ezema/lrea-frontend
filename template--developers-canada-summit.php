<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

require_once(ROOT_PATH_TO_PORTAL . 'PHPMailer/PHPMailerAutoload.php');

$use_phpmailer = true;

require_once('inc.no-location-in-url.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize an array to store form errors
    $form_errors = [];

    // Get form data and sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['phoneNumber']));
    $whatsapp_number = htmlspecialchars(trim($_POST['whatsapp']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $country_of_residence = htmlspecialchars(trim($_POST['country']));
    $company = htmlspecialchars(trim($_POST['company']));
    $job_title = htmlspecialchars(trim($_POST['job']));
    $require_visa = htmlspecialchars(trim($_POST['visa']));
    $registration_type = htmlspecialchars(trim($_POST['registration']));
    $payment_method = htmlspecialchars(trim($_POST['payment']));
    $proposed_payment_date = htmlspecialchars(trim($_POST['proposedPaymentDate']));
    $payment_terms = isset($_POST['paymentTerms']) ? 'accepted' : '';


    // Perform validation
    if (empty($fullname)) {
        $form_errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }
    if (empty($phone_number)) {
        $form_errors[] = "Phone number is required.";
    }
    if (empty($gender)) {
        $form_errors[] = "Gender is required.";
    }
    if (empty($country_of_residence)) {
        $form_errors[] = "Country of residence is required.";
    }
    if (empty($require_visa)) {
        $form_errors[] = "Visa invitation requirement is required.";
    }
    if (empty($registration_type)) {
        $form_errors[] = "Registration type is required.";
    }
    if (empty($payment_method)) {
        $form_errors[] = "Payment method is required.";
    }
    if (empty($proposed_payment_date)) {
        $form_errors[] = "Proposed payment date is required.";
    }
    if (empty($payment_terms)) {
        $form_errors[] = "You must agree to the payment terms and conditions.";
    }

    // If there are no errors, proceed with form processing
    if (empty($form_errors)) {
        $all_went_well = false;

        mysqli_begin_transaction($config) or die(mysqli_error($config)); // Start transaction

        $query_Insert = "INSERT INTO brg_canada_registrations (
            fullname, 
            email, 
            phone_number, 
            whatsapp_number, 
            gender, 
            country_of_residence, 
            company, 
            job_title, 
            require_visa, 
            registration_type, 
            payment_method,
            proposed_payment_date,
            payment_terms
        ) VALUES (
            '" . mysqli_real_escape_string($config, $fullname) . "',  
            '" . mysqli_real_escape_string($config, $email) . "',  
            '" . mysqli_real_escape_string($config, $phone_number) . "',  
            '" . mysqli_real_escape_string($config, $whatsapp_number) . "',  
            '" . mysqli_real_escape_string($config, $gender) . "',  
            '" . mysqli_real_escape_string($config, $country_of_residence) . "',  
            '" . mysqli_real_escape_string($config, $company) . "',  
            '" . mysqli_real_escape_string($config, $job_title) . "',  
            '" . mysqli_real_escape_string($config, $require_visa) . "',  
            '" . mysqli_real_escape_string($config, $registration_type) . "',  
            '" . mysqli_real_escape_string($config, $payment_method) . "',
            '" . mysqli_real_escape_string($config, $proposed_payment_date) . "',  
            '" . mysqli_real_escape_string($config, $payment_terms) . "' 
        )";

        mysqli_select_db($config, $database_config);
        $Insert = mysqli_query($config, $query_Insert) or die(mysqli_error($config));
        $insertStatus = mysqli_affected_rows($config);

        if ($insertStatus > 0) { // If INSERT is successful
            mysqli_commit($config) or die(mysqli_error($config));
            $all_went_well = true;

            // Success feedback message
            $success_message = "Thank you for registering, $fullname! Your registration is successful. 
                <br><br>Click <a href='{$link_root_path}canada-visiting-visa-form' id='nextFormLink'>here</a> to go to the next form. 
                <br><span id='countdown'>15</span> seconds to redirect...";

            // JavaScript for countdown and redirection
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                let countdown = 15; // Set countdown time
                const countdownElement = document.getElementById("countdown");
                const linkElement = document.getElementById("nextFormLink");

                // Function to update countdown and redirect
                const countdownTimer = setInterval(() => {
                countdown--; // Decrease countdown by 1
                countdownElement.textContent = countdown; // Update countdown display

                // Check if countdown has reached zero
                if (countdown <= 0) {
                clearInterval(countdownTimer); // Stop the timer
                window.location.href = linkElement.href; // Redirect to the next form
                }
                }, 1000); // Update every second
                });
            </script>';

            $_SESSION['picture_modal'] = "Turn off model";
        } else {
            mysqli_rollback($config) or die(mysqli_error($config));
            $form_errors[] = "Registration failed. Please try again.";
            $_SESSION['picture_modal'] = "Turn off model";
        }
    } else {
        // Store errors in session for displaying later
        $form_errors[] = "Registration failed. Please try again.";
        $_SESSION['picture_modal'] = "Turn off model";
    }
}

require_once('components/doctype.inc.php');
?>

<style>
    .form-container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        margin: 50px auto;
        max-width: 800px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
    }

    .header {
        position: relative;
        padding: 50px;
        color: #fff;
        text-align: center;
        background-image: url('images/sky.jpg');
        background-size: cover;
        background-position: center;
        border-radius: 10px;
        margin: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        /* Flexbox to allow positioning of logo and title */
        gap: 20px;
        /* Optional spacing between logo and title */
    }

    .header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
        z-index: 1;
    }

    .title {
        color: #fff;
        opacity: 0.9;
        z-index: 2;
        margin: 0;
    }

    .logo-container {
        display: flex;
        justify-content: center;
    }

    .logo {
        height: 100px;
        width: auto;
    }

    .title span {
        display: block;
        /* Make the span display block */
        margin-top: 10px;
        /* Space above the span */
        font-size: 18px;
        /* Font size for the span */
        opacity: 0.9;
        /* Slight transparency */
        z-index: 2;
    }

    .form-group {
        position: relative;
        margin-bottom: 25px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 15px;
        padding-left: 40px;
        border-radius: 50px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        font-size: 14px;
        line-height: 1;
    }


    /* label {
        font-weight: bold;
    } */

    .form-group input:focus,
    .form-group select:focus {
        border-color: #d32f2f;
        box-shadow: 0px 0px 10px rgba(211, 47, 47, 0.2);
        outline: none;
    }

    .form-group i {
        position: absolute;
        left: 15px;
        top: 17px;
        color: #888;
    }

    .btn-custom {
        background-color: #d32f2f;
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #b71c1c;
        color: #fff;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .marquee {
        width: 100%;
        margin: 0px 0px 30px 0px;
        overflow: hidden;
        color: #000;
        white-space: nowrap;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .marquee-text {
        min-width: 90vw;
        display: inline-block;
        padding: 10px;
        animation: marquee 20s linear infinite;
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .spinner-border {
        display: none;
    }

    .spinner-text {
        display: none;
    }

    .button-loading .button-text {
        display: none;
    }

    .button-loading .spinner-border,
    .button-loading .spinner-text {
        display: inline-block;
    }

    .spinner-text {
        margin-left: 10px;
    }

    .iti {
        display: block !important;
    }

    .iti__country-list {
        z-index: 2000 !important;
    }

    .iti__country-name {
        color: #000 !important;
    }

    .iti__dial-code {
        color: #000 !important;
    }

    .hide {
        display: none;
    }

    .error {
        color: red;
    }

    .valid {
        color: green;
    }

    .iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
        background-color: transparent !important;
    }

    .input-container {
        position: relative;
    }

    .date-placeholder {
        position: absolute;
        right: 50px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        pointer-events: none;
        transition: 0.2s;
    }

    /* input[type="date"]:valid + .date-placeholder,
input[type="date"]:focus + .date-placeholder {
    opacity: 0;
} */
</style>

<?php include_once('components/end-of-head.inc.php'); ?>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
</head>

<body>
    <?php include_once('components/beginning-of-body.inc.php'); ?>

    <?php require_once('components/navbar-top.inc.php'); ?>
    <?php require_once('components/header.inc.php'); ?>

    <!-- Picture Modal -->
    <div class="modal fade" id="developerCanadaSubmitModal" tabindex="-1" aria-labelledby="developerCanadaSubmitModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img src="images/brg_real_estate_canada_summit_2025.jpeg" class="img-fluid" alt="REAL ESTATE DEVELOPERS SUMMIT 3.0 CANADA 2025">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-lg-0 mb-3">
                        <div class="">
                            <h1 style="font-size: 2rem;" class="text-bold text-left text-white ft-custom-hero mb-2">ASA PROPERTIES x BRG x LREA (UK) REAL ESTATE DEVELOPERS SUMMIT 3.0 CANADA 2025</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="marquee">
            <div class="marquee-text">
                ASA PROPERTIES x BRG x LREA (UK) REAL ESTATE DEVELOPERS SUMMIT 3.0 CANADA 2025 - Early Bird Fee: $6,000 (Ends December 30TH, 2024) | Regular Fee: $7,000
            </div>
        </div>
        <div class="container">

            <div class="form-container">
                <div class="logo-container">
                    <img src="images/asa.png" alt="ASA Property Management Logo" class="logo" />
                    <!-- <span>X</span> -->
                    <img src="images/BRG-ACADEMY-LOGO.png" alt="BRG Logo" class="logo" />
                    <img src="<?php echo ROOT_DIR . 'images/main-logo-2.png?v=' . $fileCacheVersion; ?>" class="logo" />
                </div>
                <div class="header">
                    <h1 class="title">
                        ASA PROPERTIES x BRG x LREA (UK) REAL ESTATE DEVELOPERS SUMMIT 3.0 CANADA 2025
                        <span>Register now to join industry experts and gain exclusive insights!</span>
                    </h1>
                </div>
                <?php if (!empty($form_errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($form_errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST" id="registrationForm">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="input-container">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <div class="input-container">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div class="input-container">
                            <i class="fas fa-phone-alt"></i>
                            <input type="tel" class="form-control" id="phone" name="phoneNumber" placeholder="Phone Number" required>
                            <div id="error-msg" class="hide error"></div>
                            <div id="valid-msg" class="hide valid">Valid number!</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Whatsapp Number</label>
                        <div class="input-container">
                            <i class="fab fa-whatsapp"></i>
                            <input type="tel" class="form-control" id="whatsapp" name="whatsapp" placeholder="WhatsApp Number" required>
                            <div id="confirmerror-msg" class="hide error"></div>
                            <div id="confirmvalid-msg" class="hide valid">Valid WhatsApp number!</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div class="input-container">
                            <i class="fas fa-venus-mars"></i>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Country of Residence</label>
                        <div class="input-container">
                            <i class="fas fa-globe"></i>
                            <select type="text" class="form-control" id="country" name="country" required>
                                <option value="">Select Country of Residence</option>
                                <?php
                                // Fetch all countries from the database
                                mysqli_select_db($config, $database_config);
                                $query_Countries = "SELECT lc.countryID, lc.countryName FROM loccountries lc GROUP BY lc.countryID";
                                $Countries = mysqli_query($config, $query_Countries) or die(mysqli_error($config));

                                while ($row_Countries = mysqli_fetch_assoc($Countries)) {
                                    // Check if this countryID matches the previously submitted value
                                    $selected = (isset($nationality) && $nationality == $row_Countries['countryID']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row_Countries['countryName']) . '" ' . $selected . '>' . htmlspecialchars($row_Countries['countryName']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Company/Organization</label>
                        <div class="input-container">
                            <i class="fas fa-building"></i>
                            <input type="text" class="form-control" id="company" name="company" placeholder="Company/Organization" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Job Title/Role</label>
                        <div class="input-container">
                            <i class="fas fa-briefcase"></i>
                            <input type="text" class="form-control" id="job" name="job" placeholder="Job Title/Role" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Do you require a visa invitation?</label>
                        <div class="input-container">
                            <i class="fas fa-passport"></i>
                            <select class="form-control" id="visa" name="visa" required>
                                <option value="" disabled selected>Do you require a visa invitation?</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Registration Type</label>
                        <div class="input-container">
                            <i class="fas fa-clipboard-list"></i>
                            <select class="form-control" id="registration" name="registration" required>
                                <option value="" disabled selected>Registration Type</option>
                                <option value="regular">Regular</option>
                                <option value="vip">VIP</option>
                                <option value="speaker">Speaker</option>
                                <option value="sponsor">Sponsor</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Method</label>
                        <div class="input-container">
                            <i class="fas fa-credit-card"></i>
                            <select class="form-control" id="payment" name="payment" required>
                                <option value="" disabled selected>Payment Method</option>
                                <option value="credit">Credit/Debit Card</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Proposed Payment Date Field -->
                    <div class="form-group">
                        <label class="form-label">Proposed Payment Date</label>
                        <div class="input-container">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" class="form-control" id="proposedPaymentDate" name="proposedPaymentDate" required>
                            <!-- <span class="date-placeholder">Select Proposed Payment Date</span> -->
                        </div>
                    </div>

                    <!-- Payment Terms and Conditions Field -->
                    <div class="form-group d-flex align-items-center">
                        <input type="checkbox" style="width: auto !important; margin-right: 1rem;" id="paymentTerms" name="paymentTerms" required>
                        <label for="paymentTerms" class="mb-0">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#paymentTermsModal">Payment Terms and Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-custom btn-block" id="submitButton">
                        <span class="button-text">Submit Registration</span>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="spinner-text">Please wait...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- /Main Content -->
    <!-- Modal for Payment Terms and Conditions -->
    <div class="modal fade" id="paymentTermsModal" tabindex="-1" role="dialog" aria-labelledby="paymentTermsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentTermsModalLabel">Payment Terms</h5>
                </div>
                <div class="modal-body">
                    <h6><strong>Early Bird Registration</strong></h6>
                    <p>Instalment payments are allowed with a 30% down payment, and the remaining balance can be spread over 6 months. Payments must begin before December 31st, 2024.</p>

                    <h6><strong>Cancellation Policy</strong></h6>
                    <p>Cancellations made before June 31st, 2025, will receive a 50% refund on the initiated deposit.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

    <script>
        const initTelInput = (inputSelector, errorMsgSelector, validMsgSelector) => {
            const input = document.querySelector(inputSelector);
            const errorMsg = document.querySelector(errorMsgSelector);
            const validMsg = document.querySelector(validMsgSelector);
            let validationTimeout;

            // Error map for validation
            const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

            // Initialise plugin
            const iti = window.intlTelInput(input, {
                initialCountry: "auto", // Automatically detect the user's country
                geoIpLookup: function(callback) {
                    fetch('https://ipinfo.io/json?token=YOUR_API_TOKEN') // Replace with a valid API token if needed
                        .then(response => response.json())
                        .then(data => callback(data.country))
                        .catch(() => callback("us")); // Default to 'us' if there's an error
                },
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
            });

            const updateMessages = () => {
                clearTimeout(validationTimeout);
                reset();
                if (input.value.trim()) {
                    validationTimeout = setTimeout(() => {
                        if (iti.isValidNumber()) {
                            validMsg.classList.remove("hide");
                            input.classList.add("valid");
                        } else {
                            input.classList.add("error");
                            const errorCode = iti.getValidationError();
                            errorMsg.innerHTML = errorMap[errorCode];
                            errorMsg.classList.remove("hide");
                        }
                    }, 300); // Adjust the delay time as needed (in milliseconds)
                }
            };

            const reset = () => {
                input.classList.remove("error", "valid");
                errorMsg.innerHTML = "";
                errorMsg.classList.add("hide");
                validMsg.classList.add("hide");
            };

            // Event listener for country change
            input.addEventListener("countrychange", function(e) {
                const countryData = iti.getSelectedCountryData(); // Get selected country data
                input.value = `+${countryData.dialCode}`; // Set input value to the dial code
            });

            // Event listeners
            input.addEventListener('input', updateMessages);
            input.addEventListener('change', reset);
            input.addEventListener('keyup', reset);
        };

        // Initialize the phone and WhatsApp input fields
        initTelInput("#phone", "#error-msg", "#valid-msg");
        initTelInput("#whatsapp", "#confirmerror-msg", "#confirmvalid-msg");
    </script>
    <script>
        $(document).ready(function() {
            <?php if (!isset($_SESSION['picture_modal'])): ?>
                $('#developerCanadaSubmitModal').modal('show');
                <?php unset($_SESSION['picture_modal']); ?>
            <?php endif; ?>
        });

        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var button = document.getElementById('submitButton');
            button.classList.add('button-loading');
            button.disabled = true;
        });
    </script>
    <?php require_once('components/footer.inc.php'); ?>

    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>
</body>

</html>