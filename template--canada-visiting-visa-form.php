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
    $surname = htmlspecialchars(trim($_POST['surname']));
    $given_name = htmlspecialchars(trim($_POST['givenName']));
    $date_of_birth = htmlspecialchars(trim($_POST['dateOfBirth']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $present_address = htmlspecialchars(trim($_POST['presentAddress']));
    $national_id = htmlspecialchars(trim($_POST['nationalID']));
    $nin_date_issue = htmlspecialchars(trim($_POST['ninDateIssue']));
    $biometrics = htmlspecialchars(trim($_POST['biometrics']));

    // Post-secondary education details
    $post_secondary = htmlspecialchars(trim($_POST['postSecondary']));
    $school_name = htmlspecialchars(trim($_POST['schoolName']));
    $school_address = htmlspecialchars(trim($_POST['schoolAddress']));
    $school_year_start = htmlspecialchars(trim($_POST['schoolYearStart']));
    $school_year_end = htmlspecialchars(trim($_POST['schoolYearEnd']));

    $work_experience = htmlspecialchars(trim($_POST['workExperience']));
    $travel_history = htmlspecialchars(trim($_POST['travelHistory']));
    $refusal_history = htmlspecialchars(trim($_POST['refusalHistory']));

    // Marital Status and spouse details
    $marital_status = htmlspecialchars(trim($_POST['maritalStatus']));
    $spouse_name = htmlspecialchars(trim($_POST['spouseName']));
    $spouse_dob = htmlspecialchars(trim($_POST['spouseDob']));
    $spouse_address = htmlspecialchars(trim($_POST['spouseAddress']));
    $spouse_occupation = htmlspecialchars(trim($_POST['spouseOccupation']));

    $children = htmlspecialchars(trim($_POST['children']));
    $parent_details = htmlspecialchars(trim($_POST['parentDetails']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Perform validation
    if (empty($surname)) {
        $form_errors[] = "Surname is required.";
    }
    if (empty($given_name)) {
        $form_errors[] = "Given name is required.";
    }
    if (empty($date_of_birth)) {
        $form_errors[] = "Date of birth is required.";
    }
    if (empty($gender)) {
        $form_errors[] = "Gender is required.";
    }
    if (empty($present_address)) {
        $form_errors[] = "Present address is required.";
    }
    if (empty($national_id)) {
        $form_errors[] = "National ID is required.";
    }
    if (empty($nin_date_issue)) {
        $form_errors[] = "NIN date of issue is required.";
    }
    if (empty($biometrics)) {
        $form_errors[] = "Biometrics are required.";
    }
    // Conditional validation for post-secondary education
    if ($post_secondary == 'yes') {
        if (empty($school_name)) {
            $form_errors[] = "School name is required.";
        }
        if (empty($school_address)) {
            $form_errors[] = "School address is required.";
        }
        if (empty($school_year_start)) {
            $form_errors[] = "School year start is required.";
        }
        if (empty($school_year_end)) {
            $form_errors[] = "School year end is required.";
        }
    }
    if ($post_secondary === 'yes' && empty($school_name)) {
        $form_errors[] = "School name is required for post-secondary education.";
    }
    if ($post_secondary === 'yes' && empty($school_address)) {
        $form_errors[] = "School address is required for post-secondary education.";
    }
    if (empty($work_experience)) {
        $form_errors[] = "Work experience is required.";
    }
    if (empty($travel_history)) {
        $form_errors[] = "Travel history is required.";
    }
    if (empty($refusal_history)) {
        $form_errors[] = "Refusal history is required.";
    }
    // Conditional validation for marital status and spouse details
    if ($marital_status == 'married' || $marital_status == 'divorced') {
        if (empty($spouse_name)) {
            $form_errors[] = "Spouse name is required.";
        }
        if (empty($spouse_dob)) {
            $form_errors[] = "Spouse date of birth is required.";
        }
        if (empty($spouse_address)) {
            $form_errors[] = "Spouse address is required.";
        }
        if (empty($spouse_occupation)) {
            $form_errors[] = "Spouse occupation is required.";
        }
    }
    if (empty($telephone)) {
        $form_errors[] = "Telephone number is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Valid email is required.";
    }

    // If there are no errors, proceed with form processing
    if (empty($form_errors)) {
        $all_went_well = false;

        mysqli_begin_transaction($config) or die(mysqli_error($config)); // Start transaction

        $query_Insert = "INSERT INTO visa_applicant_details (
            surname, 
            given_name, 
            date_of_birth, 
            gender, 
            present_address, 
            national_id, 
            nin_date_issue, 
            biometrics, 
            post_secondary, 
            school_name, 
            school_address, 
            school_year_start, 
            school_year_end, 
            work_experience, 
            travel_history, 
            refusal_history, 
            marital_status, 
            spouse_name, 
            spouse_dob, 
            spouse_address, 
            spouse_occupation, 
            children, 
            parent_details, 
            telephone, 
            email
        ) VALUES (
            '" . mysqli_real_escape_string($config, $surname) . "',  
            '" . mysqli_real_escape_string($config, $given_name) . "',  
            '" . mysqli_real_escape_string($config, $date_of_birth) . "',  
            '" . mysqli_real_escape_string($config, $gender) . "',  
            '" . mysqli_real_escape_string($config, $present_address) . "',  
            '" . mysqli_real_escape_string($config, $national_id) . "',  
            '" . mysqli_real_escape_string($config, $nin_date_issue) . "',  
            '" . mysqli_real_escape_string($config, $biometrics) . "',  
            '" . mysqli_real_escape_string($config, $post_secondary) . "',  
            '" . mysqli_real_escape_string($config, $school_name) . "',  
            '" . mysqli_real_escape_string($config, $school_address) . "',  
            '" . mysqli_real_escape_string($config, $school_year_start) . "',  
            '" . mysqli_real_escape_string($config, $school_year_end) . "',  
            '" . mysqli_real_escape_string($config, $work_experience) . "',  
            '" . mysqli_real_escape_string($config, $travel_history) . "',  
            '" . mysqli_real_escape_string($config, $refusal_history) . "',  
            '" . mysqli_real_escape_string($config, $marital_status) . "',  
            '" . mysqli_real_escape_string($config, $spouse_name) . "',  
            '" . mysqli_real_escape_string($config, $spouse_dob) . "',  
            '" . mysqli_real_escape_string($config, $spouse_address) . "',  
            '" . mysqli_real_escape_string($config, $spouse_occupation) . "',  
            '" . mysqli_real_escape_string($config, $children) . "',  
            '" . mysqli_real_escape_string($config, $parent_details) . "',  
            '" . mysqli_real_escape_string($config, $telephone) . "',  
            '" . mysqli_real_escape_string($config, $email) . "' 
        )";

        mysqli_select_db($config, $database_config);
        $Insert = mysqli_query($config, $query_Insert) or die(mysqli_error($config));
        $insertStatus = mysqli_affected_rows($config);

        if ($insertStatus > 0) { // If INSERT is successful
            mysqli_commit($config) or die(mysqli_error($config));
            $all_went_well = true;

            // Success feedback message
            $success_message = "Dear $given_name, thank you for completing your application! We have successfully received your details. Our team will review your submission and get in touch with you shortly. If you have any further questions, please feel free to contact us.";
        } else {
            mysqli_rollback($config) or die(mysqli_error($config));
            $form_errors[] = "Submission failed. Please try again.";
        }
    } else {
        // Store errors in session for displaying later
        $_SESSION['form_errors'] = $form_errors;
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
    .form-group select,
    .form-group textarea {
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
    .form-group select:focus,
    .form-group textarea:focus {
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

    textarea {
        width: 100%;
        padding: 20px;
        resize: vertical; /* Allows only vertical resizing */
        min-height: 100px !important; /* Ensures minimum height for long text */
        max-height: 500px; /* Set a max height limit */
        overflow-y: auto; /* Adds a scrollbar if text overflows */
    }

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

    <!-- Main Content -->
    <div class="main-content">
        <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-lg-0 mb-3">
                        <div class="">
                            <h1 style="font-size: 2rem;" class="text-bold text-left text-white ft-custom-hero mb-2">BRG x LREA (UK) Canada Visiting Visa Form</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">

            <div class="form-container">
                <div class="logo-container">
                    <img src="images/BRG-ACADEMY-LOGO.png" alt="BRG Logo" class="logo" />
                    <img src="<?php echo ROOT_DIR . 'images/main-logo-2.png?v=' . $fileCacheVersion; ?>" class="logo" />
                </div>
                <div class="header">
                    <h1 class="title">
                        BRG x LREA (UK) <br> Canada Visiting Visa Form
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
                    <!-- Surname / last name -->
                    <div class="form-group">
                        <label class="form-label">Surname / Last Name</label>
                        <div class="input-container">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="surname" placeholder="Enter your Last name" value="<?php echo isset($surname) ? htmlspecialchars($surname) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- Given name / first name -->
                    <div class="form-group">
                        <label class="form-label">Given Name / First Name</label>
                        <div class="input-container">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="givenName" placeholder="Enter your first name" value="<?php echo isset($givenName) ? htmlspecialchars($givenName) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <div class="input-container">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" class="form-control" name="dateOfBirth" value="<?php echo isset($dateOfBirth) ? htmlspecialchars($dateOfBirth) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div class="input-container">
                            <i class="fas fa-venus-mars"></i>
                            <select class="form-control" name="gender" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Present Address -->
                    <div class="form-group">
                        <label class="form-label">Present Address</label>
                        <div class="input-container">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" class="form-control" name="presentAddress" placeholder="Enter your address" value="<?php echo isset($presentAddress) ? htmlspecialchars($presentAddress) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- National Identity Number -->
                    <div class="form-group">
                        <label class="form-label">National Identity Number</label>
                        <div class="input-container">
                            <i class="fas fa-id-card"></i>
                            <input type="text" class="form-control" name="nationalID" placeholder="Enter your NIN" value="<?php echo isset($nationalID) ? htmlspecialchars($nationalID) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- NIN Date of Issue -->
                    <div class="form-group">
                        <label class="form-label">NIN Date of Issue</label>
                        <div class="input-container">
                            <i class="fas fa-calendar-check"></i>
                            <input type="date" class="form-control" name="ninDateIssue" required>
                        </div>
                    </div>

                    <!-- Biometrics -->
                    <div class="form-group">
                        <label class="form-label">Have you done Canada biometrics, if yes, are they still valid?</label>
                        <div class="input-container">
                            <i class="fas fa-fingerprint"></i>
                            <input type="text" class="form-control" name="biometrics" required>
                        </div>
                    </div>

                    <!-- Post-Secondary Education -->
                    <div class="form-group">
                        <label class="form-label">Have you ever studied at a post-secondary school (university, college, or vocational school)?</label>
                        <div class="input-container">
                            <i class="fas fa-school"></i>
                            <select class="form-control" name="postSecondary" id="postSecondary" required>
                                <option value="" disabled selected>Select an option</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- School Details (Initially Hidden) -->
                    <div class="form-group" id="schoolDetails" style="display: none;">
                        <label class="form-label">If yes, please provide the following details:</label>
                        <div class="input-container">
                            <i class="fas fa-university"></i>
                            <input type="text" class="form-control" name="schoolName" placeholder="School Name">
                            <input type="text" class="form-control mt-2" name="schoolAddress" placeholder="School Address">
                            <input type="number" class="form-control mt-2" name="schoolYearStart" placeholder="Year Start" min="1900" max="2099" step="1">
                            <input type="number" class="form-control mt-2" name="schoolYearEnd" placeholder="Year End" min="1900" max="2099" step="1">
                        </div>
                    </div>

                    <!-- Work Experience -->
                    <div class="form-group">
                        <label class="form-label">Work Experience (Past 10 years)</label>
                        <div class="input-container">
                            <i class="fas fa-briefcase"></i>
                            <textarea class="form-control" name="workExperience" placeholder="Your role, company, and address" required></textarea>
                        </div>
                    </div>

                    <!-- Travel Experience -->
                    <div class="form-group">
                        <label class="form-label">Have you travelled to any country in the past 5 years?</label>
                        <div class="input-container">
                            <i class="fas fa-plane-departure"></i>
                            <select class="form-control" name="travelHistory" required>
                                <option value="" disabled selected>Select an option</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Refusal History -->
                    <div class="form-group">
                        <label class="form-label">Have you been refused Canada or any other country before, If yes state the reason.</label>
                        <div class="input-container">
                            <i class="fas fa-ban"></i>
                            <textarea class="form-control" name="refusalHistory" placeholder=" If yes state the reason" required></textarea>
                        </div>
                    </div>

                    <!-- Marital Status -->
                    <div class="form-group">
                        <label class="form-label">Marital Status</label>
                        <div class="input-container">
                            <i class="fas fa-heart"></i>
                            <select class="form-control" name="maritalStatus" id="maritalStatus" required>
                                <option value="" disabled selected>Select your status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                            </select>
                        </div>
                    </div>

                    <!-- Spouse Details (Initially Hidden) -->
                    <div class="form-group" id="spouseDetails" style="display: none;">
                        <label class="form-label">If married/divorced, spouse details:</label>
                        <div class="input-container">
                            <i class="fas fa-user-friends"></i>
                            <input type="text" class="form-control" name="spouseName" placeholder="Spouse Name">
                            <input type="date" class="form-control mt-2" name="spouseDob" placeholder="Spouse Date of Birth">
                            <input type="text" class="form-control mt-2" name="spouseAddress" placeholder="Spouse Address">
                            <input type="text" class="form-control mt-2" name="spouseOccupation" placeholder="Spouse Occupation">
                        </div>
                    </div>


                    <!-- Children -->
                    <div class="form-group">
                        <label class="form-label">Children [Name, Date of birth, Relationship]</label>
                        <div class="input-container">
                            <i class="fas fa-child"></i>
                            <textarea class="form-control" name="children" placeholder="Name, Date of birth, Relationship" required></textarea>
                        </div>
                    </div>

                    <!-- Parent Details -->
                    <div class="form-group">
                        <label class="form-label">Parent Details [Name, Occupation, Date of birth, If deceased please add the death date]</label>
                        <div class="input-container">
                            <i class="fas fa-users"></i>
                            <textarea class="form-control" name="parent" placeholder="Name, Occupation, Date of birth, If deceased please add the death date" required></textarea>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="form-group">
                        <label class="form-label">Telephone Number</label>
                        <div class="input-container">
                            <i class="fas fa-phone"></i>
                            <input type="tel" class="form-control" id="phone" name="telephone" placeholder="Enter your phone number" value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>" required>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-container">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-custom btn-block" id="submitButton">
                        <span class="button-text">Submit Form</span>
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
                    <p>An initial payment of $2,000 - $3,000 must be made on or before December 31st, 2024.</p>
                    <p>The remaining balance must be settled before March 31st, 2025.</p>

                    <h6><strong>Cancellation Policy</strong></h6>
                    <p>Cancellations made before March 31st, 2025, will receive a 50% refund on the initiated deposit.</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('maritalStatus').addEventListener('change', function() {
                var spouseDetails = document.getElementById('spouseDetails');
                if (this.value === 'married' || this.value === 'divorced') {
                    spouseDetails.style.display = 'block';
                } else {
                    spouseDetails.style.display = 'none';
                }
            });

            document.getElementById('postSecondary').addEventListener('change', function() {
                var schoolDetails = document.getElementById('schoolDetails');
                if (this.value === 'yes') {
                    schoolDetails.style.display = 'block';
                } else {
                    schoolDetails.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var button = document.getElementById('submitButton');
            button.classList.add('button-loading');
            button.disabled = true;
        });
    </script>
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
    <?php require_once('components/footer.inc.php'); ?>

    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?>
</body>

</html>