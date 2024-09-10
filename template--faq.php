<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Frequently Asked Questions';
	require_once('components/doctype.inc.php');
?>

    <style>
        .accordion-button:not(.collapsed) {
            color: #fff;
            background-color: var(--bg-new-dark);
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
            <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
                <div class="container position-relative" style="z-index: 1">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-lg-0 mb-3">
                            <div class="">
                                <h1 class="text-bold text-white ft-custom-hero mb-2">Frequently Asked Questions</h1>
                                <p class="text-white">How can we help?</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <div class="policy-container py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="degree-info-accordion">
                                <h4>GENERAL</h4>
                                <div class="accordion mb-5" id="<?php echo strtolower(SITE_NAME); ?>AccordionGeneral">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                What is <?php echo strtoupper(SITE_NAME); ?>?
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                                            <div class="accordion-body">
                                                <p><?php echo SITE_NAME; ?> is a British educational institution dedicated to providing a wide range of Undergraduate and Postgraduate courses at an affordable cost.</p>
                                                <p>All learning activities, materials, and content are provided online and in some branches of the School, as classroom block teaching styles using modern delivery models to make learning more engaging.</p>
                                                <p>On completion of a <?php echo SITE_NAME; ?> full degree course, it will give you recognized academic credits to then "top up" at one of our affiliate UK Universities, thereby, saving you time and money.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php /*
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Where is <?php echo SITE_NAME; ?> Located?
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                                                <div class="accordion-body">
                                                    <p>While we have numerous branches, we have the main Headquarters and the main Administrative Quarters.</p>
                                                    <address>
                                                        <h5>Academic Head Office:</h5>
                                                        
                                                    </address>
                                                    <address>
                                                        <h5>Administrative Head Quarters</h5>
                                                        
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                    */ ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Does <?php echo SITE_NAME; ?> provide Students UAE Visa?
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                                            <div class="accordion-body">
                                                <p>Yes. <?php echo SITE_NAME; ?> processes UAE Visas for students depending on the duration of their study with us. Students also have the opportunity to search for a suitable job during the course of study if on a long-term study.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                Does <?php echo SITE_NAME; ?> provide Job for Students?
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                                            <div class="accordion-body">
                                                <p>No, <?php echo SITE_NAME; ?> does not provide jobs for students but can assist with the search as requested.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                Will I be writing an Examination?
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                                            <div class="accordion-body">
                                                <p>All <?php echo SITE_NAME; ?> programmes are assessed by assignments only, there are no exams.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                                Will I really get a Bachelor's Degree in 2 years?
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                                            <div class="accordion-body">
                                                <p>Yes, students who successfully complete their <?php echo SITE_NAME; ?>/OTHM Level 4 & 5 (Accelerated Study Model in 1 year) will receive a Level 4 & 5 Diploma from <?php echo SITE_NAME; ?>/OTHM and then proceed to year two with a preferred <?php echo SITE_NAME; ?> affiliate UK University.</p>
                                                <p>After the successful completion of the 2nd year, a Bachelor's Degree will be issued by the preferred affiliate UK University.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSeven">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                                Will I really get a Master's Degree in 12 - 18 months or less?
                                            </button>
                                        </h2>
                                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                                            <div class="accordion-body">
                                                <p>Yes, students who successfully complete their <?php echo SITE_NAME; ?>/OTHM Level 7 Course will receive a Level 7 Diploma from <?php echo SITE_NAME; ?>/OTHM and then proceed to the 3rd Semester with a preferred <?php echo SITE_NAME; ?> affiliate UK University.</p>
                                                <p>After the successful completion of the 3rd Semester, a Master's Degree will be issued by the preferred affiliate UK University.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingEight">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                                Can I go to the UK for graduation?
                                            </button>
                                        </h2>
                                        <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                                            <div class="accordion-body">
                                                <p>Students who have successfully completed their studies will graduate in the UK with the rest of the students at their preferred <?php echo SITE_NAME; ?> affiliate UK University. (Subject to a successful UK Visa application)</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingNine">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                                Who will award my Degree?
                                            </button>
                                        </h2>
                                        <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                                            <div class="accordion-body">
                                                <p>Single Certificates are awarded for our International Postgraduate Diplomas -</p>
                                                <ul>
                                                    <li>International Postgraduate Diploma (Level 7) by OTHM Qualification.</li>
                                                </ul>
                                                <p>Triple Certificates are awarded for the Bachelor's Degree -</p>
                                                <ul>
                                                    <li>Level 4 Diploma by OTHM Qualifications/Ofqual Regulated</li>
                                                    <li>Level 5 Diploma by OTHM Qualifications/Ofqual Regulated</li>
                                                    <li>Bachelor's Degree from a preferred <?php echo SITE_NAME; ?> UK affiliate University</li>
                                                </ul>
                                                <p>Dual Certificates are awarded for the Master's Degree -</p>
                                                <ul>
                                                    <li>Level 7 Diploma by OTHM Qualifications/Ofqual Regulated</li>
                                                    <li>Master's Degree from a preferred <?php echo SITE_NAME; ?> UK affiliate University</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                                What is the UK CPD?
                                            </button>
                                        </h2>
                                        <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                                            <div class="accordion-body">
                                                <p>The UK CPD is an acronym for the United Kingdom Continuing Professional Development Courses.</p>
                                                <p>Our Professional Certificates and the Special Executive Masters Programme Certificates fall under the UK CPD Courses. The UK CPD is Non-credit bearing and Non-Degree Programmes.</p>
                                                <p>The Certificates awarded for our CPD Programmes cannot be used as Full Degree Certificates or disguised as such as it carries no credit and cannot be used to pursue an academic Course. However, the relevancy of the certificate lies in the knowledge of the bearer to demonstrate ground knowledge of the subject the certificate was issued in, and this certificate counts towards the appropriate Continuous Professional Development (CPD).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingEleven">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                                                Are there any English language requirements?
                                            </button>
                                        </h2>
                                        <div id="collapseEleven" class="accordion-collapse collapse" aria-labelledby="headingEleven">
                                            <div class="accordion-body">
                                                <p>For students who cannot communicate or write in English, we have resources they can access for study alongside our courses.</p>
                                                <p>Once you are able to successfully complete our Level 4, 5, 7 or International Postgraduate (Level 7) Diplomas, then you would have met the required English language requirement.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwelve">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                                                Level 4 & 5
                                            </button>
                                        </h2>
                                        <div id="collapseTwelve" class="accordion-collapse collapse" aria-labelledby="headingTwelve">
                                            <div class="accordion-body">
                                                <p>At this Level, you will need to be at least 18 years old and must have completed your secondary education with at least Credits (C grades) in 5 Subjects of which one of the subjects must be English to be eligible to enroll on the course.</p>
                                                <p>The <?php echo SITE_NAME; ?> affiliate UK Universities require a minimum age of 19 before embarking on the Bachelor's Degree Top-up program. No work experience is required for this Level.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFourteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFourteen" aria-expanded="false" aria-controls="collapseFourteen">
                                                Level 7
                                            </button>
                                        </h2>
                                        <div id="collapseFourteen" class="accordion-collapse collapse" aria-labelledby="headingFourteen">
                                            <div class="accordion-body">
                                                <p>At this Level, you will need to have a Bachelor's Degree or a Higher National Diploma Degree.</p>
                                                <p>If you do not have this, you will need to have at least 5 years of Managerial working experience either for an Organization or your own Company and be able to prove this. We will look at each application individually and may consider some exceptions.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirteen" aria-expanded="false" aria-controls="collapseThirteen">
                                                Will there be an interview?
                                            </button>
                                        </h2>
                                        <div id="collapseThirteen" class="accordion-collapse collapse" aria-labelledby="headingThirteen">
                                            <div class="accordion-body">
                                                <p>Learners who achieve a <?php echo SITE_NAME; ?> delivered Level 4, 5 or 7 qualifications are automatically mapped against a standard UK Higher Education qualification framework.</p>
                                                <p>This is a universally recognized framework of credits. Universities can, therefore, award credits against the <?php echo SITE_NAME; ?> Level 4, 5, and 7 qualifications to give exemptions from large parts of their formal programs.</p>
                                                <p><?php echo SITE_NAME; ?> and OTHM Qualifications have pre-agreed arrangements with certain universities to provide credits against programs either on-campus or off-campus. This is termed the 'University Top-up'.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFifteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFifteen" aria-expanded="false" aria-controls="collapseFifteen">
                                                How do I choose the best course for me?
                                            </button>
                                        </h2>
                                        <div id="collapseFifteen" class="accordion-collapse collapse" aria-labelledby="headingFifteen">
                                            <div class="accordion-body">
                                                <p>Learners should first visit the <?php echo SITE_NAME; ?> Courses page online to view the range of courses on offer to decide on what subjects best suit the learner, he/she should:</p>
                                                <p>- Review the modules within the course, do they interest you? Are they relevant to your chosen career path?</p>
                                                <p>- Consider how the course is taught and assessed, do you have the motivation to study via distance learning? Are you able to work independently? Do you thrive better in a classroom study mode?</p>
                                                <p>It is important that learners understand how they learn best and not try to extend themselves too far or too quickly and choose a path that comfortably progresses from their current level of skills and experience.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSixteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSixteen" aria-expanded="false" aria-controls="collapseSixteen">
                                                How do I apply for a course?
                                            </button>
                                        </h2>
                                        <div id="collapseSixteen" class="accordion-collapse collapse" aria-labelledby="headingSixteen">
                                            <div class="accordion-body">
                                                <p>Once you have reviewed your preferred Course, your first step is to pay for the Registration Fees after which the Application Form is made available to you either online or on request.</p>
                                                <p>Once you have filled out the Application Form and submitted the supporting documents, your application should be reviewed and approved within 1 - 3 working days.</p>
                                                <p>Once you have received your Conditional Acceptance, you are then required to pay a Commitment Fee so that you are registered on the <?php echo SITE_NAME; ?> Learning Portal once the Session commences.</p>
                                                <p>Alternatively, you can call email <a href="mailto:admissions@<?php echo SITE_EMAIL_DOMAIN; ?>" target="_top">admissions@<?php echo SITE_EMAIL_DOMAIN; ?></a> or <a href="mailto:<?php echo INFO_SUPPORT_EMAIL; ?>" target="_top"><?php echo INFO_SUPPORT_EMAIL; ?></a> so that they will guide you through the enrolment process.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4>CREDIBILITY</h4>
                                <div class="accordion mb-5" id="<?php echo strtolower(SITE_NAME); ?>AccordionCredibility">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSeventeen">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeventeen" aria-expanded="true" aria-controls="collapseSeventeen">
                                                Who is OTHM Qualifications, UK?
                                            </button>
                                        </h2>
                                        <div id="collapseSeventeen" class="accordion-collapse collapse show" aria-labelledby="headingSeventeen">
                                            <div class="accordion-body">
                                                <p>OTHM Qualification is an Ofqual regulated body that awards qualifications for colleges in Courses in Management, Business, Tourism and Health &amp; Social Care.</p>
                                                <p>Please visit <a href="https://www.othm.org.uk">www.othm.org.uk</a> for more information.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingEighteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEighteen" aria-expanded="false" aria-controls="collapseEighteen">
                                                Who is Qualifi, UK?
                                            </button>
                                        </h2>
                                        <div id="collapseEighteen" class="accordion-collapse collapse" aria-labelledby="headingEighteen">
                                            <div class="accordion-body">
                                                <p>Qualifi UK is a leading 21st-century Awarding Organisation founded to meet the developing education and training needs of learners and employers from the UK and around the globe serving the needs of learners and employers in the UK and worldwide every year.</p>
                                                <p>Please visit <a href="https://www.qualifi.net">www.qualifi.net </a> for more information.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingNineteen">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNineteen" aria-expanded="false" aria-controls="collapseNineteen">
                                                How are Universities linked to the Modules?
                                            </button>
                                        </h2>
                                        <div id="collapseNineteen" class="accordion-collapse collapse" aria-labelledby="headingNineteen">
                                            <div class="accordion-body">
                                                <p>Learners who achieve a <?php echo SITE_NAME; ?> delivered Level 4, 5 and 7 qualifications are automatically mapped against a standard UK Higher Education qualification framework. This is a universally recognized framework of credits.</p>
                                                <p>Universities can, therefore, award credits against the <?php echo SITE_NAME; ?> study to give exemptions from large parts of their formal programs. <?php echo SITE_NAME; ?> and OTHM have pre-agreed arrangements with certain universities to provide credits against programs either on-campus or off-campus. This is termed 'University Top-up'.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwenty">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwenty" aria-expanded="false" aria-controls="collapseTwenty">
                                                How do I change my personal contact details once I have given them to <?php echo SITE_NAME; ?>?
                                            </button>
                                        </h2>
                                        <div id="collapseTwenty" class="accordion-collapse collapse" aria-labelledby="headingTwenty">
                                            <div class="accordion-body">
                                                <p>All details can be edited through the student dashboard. Once a student has enrolled they can log in to view their course progress, edit their profile and communicate with the Students Support, Academic Support, and Technical Support.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyOne">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyOne" aria-expanded="false" aria-controls="collapseTwentyOne">
                                                Can I still access my Student Dashboard if I have finished my course?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyOne" class="accordion-collapse collapse" aria-labelledby="headingTwentyOne">
                                            <div class="accordion-body">
                                                <p>No, access to the <?php echo SITE_NAME; ?> Dashboard is deactivated once you complete your studies with <?php echo SITE_NAME; ?>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyTwo">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyTwo" aria-expanded="false" aria-controls="collapseTwentyTwo">
                                                I have forgotten my username/password and cannot login to my dashboard, how can I get access?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyTwo" class="accordion-collapse collapse" aria-labelledby="headingTwentyTwo">
                                            <div class="accordion-body">
                                                <p>If learners have lost or forgotten their login details, they can request a new password by sending an email to <a href="mailto:<?php echo TECH_SUPPORT_EMAIL; ?>" target="_top"><?php echo TECH_SUPPORT_EMAIL; ?></a> for assistance.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4>PROGRESSION</h4>
                                <div class="accordion mb-5" id="<?php echo strtolower(SITE_NAME); ?>AccordionProgression">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyThree">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyThree" aria-expanded="true" aria-controls="collapseTwentyThree">
                                                Will I be able to gain higher education qualification through <?php echo SITE_NAME; ?>?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyThree" class="accordion-collapse collapse show" aria-labelledby="headingTwentyThree">
                                            <div class="accordion-body">
                                                <p>The <?php echo SITE_NAME; ?> programmes are mapped against UK higher education qualifications which are fully transferable to a range of other higher education routes and professional qualifications.</p>
                                                <p>On completion of the <?php echo SITE_NAME; ?> delivered level 4, 5 and 7 Programmes students will be awarded enough Credits so that they can top up to either a Bachelors or Master's Degree at any of our affiliate UK Universities a <?php echo SITE_NAME; ?> University partner.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyFour" aria-expanded="false" aria-controls="collapseTwentyFour">
                                                What potential job opportunities are there after I finish the course?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyFour" class="accordion-collapse collapse" aria-labelledby="headingTwentyFour">
                                            <div class="accordion-body">
                                                <p>Once you successfully complete the final stage with the affiliate UK University, you will have a Bachelor's Degree or Master's Degree that will open doors to many career options.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4>STUDENTS' SUPPORT</h4>
                                <div class="accordion mb-5" id="<?php echo strtolower(SITE_NAME); ?>AccordionStudentsSupport">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyFive">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyFive" aria-expanded="true" aria-controls="collapseTwentyFive">
                                                What do I do if I am struggling?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyFive" class="accordion-collapse collapse show" aria-labelledby="headingTwentyFive">
                                            <div class="accordion-body">
                                                You can contact our students' support team by sending an email to <a href="mailto:students.support@<?php echo SITE_EMAIL_DOMAIN; ?>" target="_top">students.support@<?php echo SITE_EMAIL_DOMAIN; ?></a>. Students can also interact with the Academic and Technical Support by sending an email to either <a href="mailto:academics@<?php echo SITE_EMAIL_DOMAIN; ?>" target="_top">academics@<?php echo SITE_EMAIL_DOMAIN; ?></a> or <a href="mailto:<?php echo TECH_SUPPORT_EMAIL; ?>" target="_top"><?php echo TECH_SUPPORT_EMAIL; ?></a>.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4>STUDYING ONLINE WITH <?php echo strtoupper(SITE_NAME); ?></h4>
                                <div class="accordion mb-5" id="<?php echo strtolower(SITE_NAME); ?>AccordionStudy">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentySix">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentySix" aria-expanded="true" aria-controls="collapseTwentySix">
                                                What modules do I need to achieve a qualification?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentySix" class="accordion-collapse collapse show" aria-labelledby="headingTwentySix">
                                            <div class="accordion-body">
                                                You need to complete all the modules detailed in your required course option plus all the associated assessments to complete the <?php echo SITE_NAME; ?> delivered course and receive the Level 4, 5, or 7 Diplomas. This will give you the recognized academic credits to top up to either a Bachelor's Degree or Master's Degree. You can study all the modules at your own pace.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentySeven">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentySeven" aria-expanded="false" aria-controls="collapseTwentySeven">
                                                Do I need a specific computer to study with <?php echo SITE_NAME; ?>?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentySeven" class="accordion-collapse collapse" aria-labelledby="headingTwentySeven">
                                            <div class="accordion-body">
                                                As long as you have internet access, any computer or tablet can be used to complete the online modules and courses. You can also study on your phone with our mobile-friendly content.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyEight">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyEight" aria-expanded="false" aria-controls="collapseTwentyEight">
                                                Can I access my dashboard and study on my mobile and tablet?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyEight" class="accordion-collapse collapse" aria-labelledby="headingTwentyEight">
                                            <div class="accordion-body">
                                                Yes, but this will only be accessible with an internet connection. All digital interactivity has been coded for compatibility on touch screens. You can also learn the material by printing it off.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwentyNine">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyNine" aria-expanded="false" aria-controls="collapseTwentyNine">
                                                What study materials do I need for the online program?
                                            </button>
                                        </h2>
                                        <div id="collapseTwentyNine" class="accordion-collapse collapse" aria-labelledby="headingTwentyNine">
                                            <div class="accordion-body">
                                                The modules themselves are sufficient for passing the associated assessments. The modules contain additional accessible resources that learners may wish to follow through to enhance their learning, and these are provided within the text. <?php echo SITE_NAME; ?> also has extra learning resources such as the e-library to aid your study.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirty">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirty" aria-expanded="false" aria-controls="collapseThirty">
                                                How long will my study take?
                                            </button>
                                        </h2>
                                        <div id="collapseThirty" class="accordion-collapse collapse" aria-labelledby="headingThirty">
                                            <div class="accordion-body">
                                                <?php echo SITE_NAME; ?> has Courses ranging from 1 week to 18 months. The actual duration it takes you to complete your study depends on you.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyOne">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyOne" aria-expanded="false" aria-controls="collapseThirtyOne">
                                                Does my previous study and experience count towards a <?php echo SITE_NAME; ?> course?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyOne" class="accordion-collapse collapse" aria-labelledby="headingThirtyOne">
                                            <div class="accordion-body">
                                                There are no exemptions or credits within the <?php echo SITE_NAME; ?> system of study, and you have to study all the <?php echo SITE_NAME; ?> modules to take advantage of our access route. However, if you think you may be exempt from a certain level, then please contact us, and we will advise you. We can also help you with your top-up inquiries if you already have the necessary qualifications.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyTwo">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyTwo" aria-expanded="false" aria-controls="collapseThirtyTwo">
                                                Can I personalize my study?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyTwo" class="accordion-collapse collapse" aria-labelledby="headingThirtyTwo">
                                            <div class="accordion-body">
                                                Students can study the modules in any order and at any pace, to qualify for the University Top-up. It is important to note that all of the modules and assignments required for each level have to be completed in order to earn the Degree for the Level.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyThree">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyThree" aria-expanded="false" aria-controls="collapseThirtyThree">
                                                Can I go to the UK for graduation?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyThree" class="accordion-collapse collapse" aria-labelledby="headingThirtyThree">
                                            <div class="accordion-body">
                                                Students who have successfully completed their studies will graduate in the UK with the rest of the students at their preferred <?php echo SITE_NAME; ?> affiliate UK University. (Subject to a successful UK Visa application)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyFour" aria-expanded="false" aria-controls="collapseThirtyFour">
                                                What do I get when all the modules and assessments are completed?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyFour" class="accordion-collapse collapse" aria-labelledby="headingThirtyFour">
                                            <div class="accordion-body">
                                                Learners who complete the full program of modules as detailed and also complete the assignments are awarded OTHM Level 4, 5, 7, and International Postgraduate (Level 7) Certificates with the official academic credits. These qualifications are recognized by UK higher education authorities and by the university sector. The student can then apply to one of our University partners to complete their degree.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyFive">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyFive" aria-expanded="false" aria-controls="collapseThirtyFive">
                                                Do all modules have an exam?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyFive" class="accordion-collapse collapse" aria-labelledby="headingThirtyFive">
                                            <div class="accordion-body">
                                                All modules have an online assessment. Completion of the requisite number of assessments and written assignments will grant the participant a qualification recognized by a number of world-class universities.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtySix">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtySix" aria-expanded="false" aria-controls="collapseThirtySix">
                                                How do assessments work?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtySix" class="accordion-collapse collapse" aria-labelledby="headingThirtySix">
                                            <div class="accordion-body">
                                                Assessments are research and assignment based.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtySeven">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtySeven" aria-expanded="false" aria-controls="collapseThirtySeven">
                                                What is an interactive learning module?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtySeven" class="accordion-collapse collapse" aria-labelledby="headingThirtySeven">
                                            <div class="accordion-body">
                                                The interactive activities in the modules include tests, exercises, and assessments that provide a guide to a learner's progress through the module.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyEight">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyEight" aria-expanded="false" aria-controls="collapseThirtyEight">
                                                How can I pay for my tuition fees?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyEight" class="accordion-collapse collapse" aria-labelledby="headingThirtyEight">
                                            <div class="accordion-body">
                                                <?php echo SITE_NAME; ?> has Bank accounts that will accept payment directly. You can also pay by credit/debit card via a link on your Invoice or directly online.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirtyNine">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirtyNine" aria-expanded="false" aria-controls="collapseThirtyNine">
                                                Will <?php echo SITE_NAME; ?> provide a Letter for a UK student visa?
                                            </button>
                                        </h2>
                                        <div id="collapseThirtyNine" class="accordion-collapse collapse" aria-labelledby="headingThirtyNine">
                                            <div class="accordion-body">
                                                No, but your preferred <?php echo SITE_NAME; ?> affiliate UK University can.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingForty">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForty" aria-expanded="false" aria-controls="collapseForty">
                                                Do I need a visa to complete modules/course through <?php echo SITE_NAME; ?>?
                                            </button>
                                        </h2>
                                        <div id="collapseForty" class="accordion-collapse collapse" aria-labelledby="headingForty">
                                            <div class="accordion-body">
                                                No visa is required for the online programs. If a student chooses to take the university top-up program on campus in the UK, a student visa will be required. Learners should request this through their preferred <?php echo SITE_NAME; ?> affiliate UK University.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFortyOne">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFortyOne" aria-expanded="false" aria-controls="collapseFortyOne">
                                                Can I study anywhere in the world?
                                            </button>
                                        </h2>
                                        <div id="collapseFortyOne" class="accordion-collapse collapse" aria-labelledby="headingFortyOne">
                                            <div class="accordion-body">
                                                Yes. There are no geographical limitations for <?php echo SITE_NAME; ?> online courses.
                                            </div>
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