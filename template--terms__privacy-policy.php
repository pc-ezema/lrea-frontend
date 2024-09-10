<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Privacy Policy';
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
                                <h1 class="text-bold text-white ft-custom-hero mb-2">Privacy Policy</h1>
                                <p class="text-white">This Privacy Policy explains how we collect, use, and safeguard your data when you visit our website. We value your trust and want you to understand how your information is handled.</p>
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
                                <div id="<?php echo strtolower(SITE_NAME); ?>Accordion" class="accordion mb-5">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Data Protection Statements
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                                            <div class="accordion-body">
                                                <p>Data Protection Act 1998</p>
                                                <p>The information you supply will be used by <?php echo SITE_NAME; ?>, a British business school and her other affiliates, to process your admission into the programme(s) applied for, and to create your Personal Learning Record. For more information about how your information is processed and shared refer to the Extended Privacy Notice available on <a href="<?php echo ROOT_DIR; ?>data-protection"><?php echo ROOT_DIR; ?>data-protection</a>. The information that you provide will be shared with other organizations for the purposes of administration, system requirements, and other guidance and statistical research.</p>
                                                <p>Other organizations with which we will share information include our accrediting bodies, other <?php echo SITE_NAME; ?> branches, and affiliate universities.</p>
                                                <p>At no time will your personal information be passed to organizations for marketing or sales purposes without explicit consent. You may be approached to take part in surveys by mail and phone, which are aimed at enabling <?php echo SITE_NAME; ?> to monitor performance, improve quality, and plan future provision. If you are sponsored by an employer or agency or are a full-time student being looked after by a parent, guardian, or carer, you also consent to the College disclosing information about your attendance and progress with them.</p>
                                                <p>All credit/debit cards' details and personally identifiable information will NOT be stored, sold, shared, rented, or leased to any third parties. <?php echo SITE_NAME; ?> (<a href="<?php echo ROOT_DIR; ?>"><?php echo SITE_NAME_WWW; ?></a>) will not pass any debit/credit card details to 3rd parties. <?php echo SITE_NAME; ?> (<a href="<?php echo ROOT_DIR; ?>"><?php echo SITE_NAME_WWW; ?></a> and all related websites) takes appropriate steps to ensure data privacy and security, including through various hardware and software methodologies. However, we cannot guarantee the security of any information that is disclosed online.</p>
                                                <p>You can access our Data Protection Policy here: <a href="<?php echo ROOT_DIR; ?>data_protection_policy.pdf"><?php echo ROOT_DIR; ?>data_protection_policy.pdf</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                How We Use Your Personal Information
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                                            <div class="accordion-body">
                                                <p>Once you fill out your details on our inquiry form from the website <a href="<?php echo ROOT_DIR; ?>"><?php echo SITE_NAME_WWW; ?></a> or affiliate websites, an account is automatically created for you on the <?php echo SITE_NAME; ?> online study portal. Through this account, you can start the registration process for your preferred course, monitor your application, and access the course you have registered and paid for, once the Session commences.</p>
                                                <p>The personal information you provide will be passed to the recruitment and admissions department for admission processing, the Account and finance department for payment record, the Student Support for student record and support, the academics department for academic support and assessment, and the technical support department for technical support.</p>
                                                <p>The information you provide will also be shared with our accrediting bodies and affiliate universities for the purpose of registration, at any stage during the program. Your information may be shared with other organizations for education, training, employment, and well-being related purposes, including for research.</p>
                                                <p>You may be contacted after you have completed your program of learning to establish whether you have entered employment or moved on to further training. You may also get periodic emails shared with the alumni database.</p>
                                                <p>You may opt-out of sharing your participation and achievement data by contacting the Alumni Relations Department on <a href="mailto:alumni@<?php echo SITE_EMAIL_DOMAIN; ?>">alumni@<?php echo SITE_EMAIL_DOMAIN; ?></a>. You will be required to provide some personal details to confirm your identity, which may include your Learner's Registration Number (LRN) if known. Further information about the use of and access to your personal data, and details of organizations with whom we regularly share data are available at: <a href="<?php echo ROOT_DIR; ?>data_protection_policy.pdf"><?php echo ROOT_DIR; ?>data_protection_policy.pdf</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Extended Privacy Notice Text
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                                            <div class="accordion-body">
                                                <p>This statement is intended to provide you with information as to how the Learning Records Service will collect and use your personal information and data, and how you can exercise choice in respect of the use of your personal data. <?php echo SITE_NAME; ?> collects data relating to learners registering for relevant academic qualifications, Entry to Employment Certificates, and associated units. The recruitment and admissions stores learner participation and achievement data collected directly from awarding organizations. This information is known as the 'Personal Learning Record' (PLR). Permitted organizations will have access to your PLR in order to access your achievements, awards, and to enable advice and guidance to be provided to you. You as the learner will be able to get a copy of your PLR.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                Use of Data
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                                            <div class="accordion-body">
                                                <p>The Learner Registration Number enables <?php echo SITE_NAME; ?> and relevant Awarding Organizations to share information about participation and achievement in a consistent and approved manner, promoting good information management practice, and helping to improve accuracy and efficiency. This will benefit you through enhancing the application processes to Awarding Organizations, learning providers, learning advisors, and other third parties. The PLR will be shared with organizations linked to your education and training, including those organizations specified in Regulations made under section 537A of the Education Act.</p>
                                                <p>Awarding Organizations are able to access limited achievement data that we hold about you, in order to verify its accuracy. All organizations that have control of the personal data about you and that we store in the Learning Records are required to be registered under the Data Protection Act 1998 and to use your personal information in accordance with the requirements of the Act. These organizations are required to sign relevant agreements and control documentation that requires them to manage your data responsibly and only to access information where there is a direct relationship between you and the relevant organization. At no time will your personal information be passed to any organizations for marketing or sales purposes. Permissions for sharing The LRN is required to fulfill statutory requirements for the administration of services within the education and training sector.</p>
                                                <p>You cannot opt-out of being issued with an LRN. However, you can opt-out of sharing your PLR. For details of how you may opt-out of sharing your participation and achievement data, refer to <a href="<?php echo ROOT_DIR; ?>data-protection"><?php echo ROOT_DIR; ?>data-protection</a>: Useful Information for Learners and Parents.</p>
                                                <p>For more information on this, kindly contact <a href="mailto:data-policy@<?php echo SITE_EMAIL_DOMAIN; ?>">data-policy@<?php echo SITE_EMAIL_DOMAIN; ?></a>.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFiveOne">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiveOne" aria-expanded="false" aria-controls="collapseFiveOne">
                                                Sensitive Personal Data
                                            </button>
                                        </h2>
                                        <div id="collapseFiveOne" class="accordion-collapse collapse" aria-labelledby="headingFiveOne">
                                            <div class="accordion-body">
                                                <p>The recruitment and admissions department makes every effort not to display any qualification information which consists of sensitive personal data on the PLR (e.g. data that relates to ethnic origin, physical or mental health, religious beliefs, trade union membership, or any criminal offenses or proceedings). If you consider that any sensitive qualification data has inadvertently been stored in your PLR, you should contact the admissions service desk.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFiveTwo">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiveTwo" aria-expanded="false" aria-controls="collapseFiveTwo">
                                                Sensitive Learners
                                            </button>
                                        </h2>
                                        <div id="collapseFiveTwo" class="accordion-collapse collapse" aria-labelledby="headingFiveTwo">
                                            <div class="accordion-body">
                                                <p>The admissions office recognizes that some learners have specific concerns about privacy, which mean that additional safeguards are required. The admissions office implements a Sensitive Learners Policy to ensure these safeguards are implemented. The admissions office recognizes the need to keep your information secure and has implemented a Security Policy that provides the appropriate technical and organizational measures aimed at preventing loss of, or unauthorized use of, your information in accordance with Government standards.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFiveThree">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiveThree" aria-expanded="false" aria-controls="collapseFiveThree">
                                                How Long Will We Keep Your Data?
                                            </button>
                                        </h2>
                                        <div id="collapseFiveThree" class="accordion-collapse collapse" aria-labelledby="headingFiveThree">
                                            <div class="accordion-body">
                                                <p>The Admissions Records Service is intended to provide a life-long record of learning which will be available to you at any time you choose to participate in education or training. Therefore, the Learning Records Service may continue to hold your LRN, PLR, and Regulated Qualification Framework and associated data for 66 years and will be reviewed periodically. Updates to this policy The Admissions Records Service recognizes that privacy and data protection concerns can evolve over time and will keep this policy under review. Any amendments will be posted here and will be notified to learners when they access their PLR.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFiveFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiveFour" aria-expanded="false" aria-controls="collapseFiveFour">
                                                IP addresses and cookies
                                            </button>
                                        </h2>
                                        <div id="collapseFiveFour" class="accordion-collapse collapse" aria-labelledby="headingFiveFour">
                                            <div class="accordion-body">
                                                <p>We may collect information about your computer, including where available your IP address, operating system, and browser type, for system administration and for aggregate marketing reporting purposes. This is statistical data about our users' browsing actions and patterns and does not identify any individual.</p>
                                                <p>&nbsp;</p>
                                                <p>For the same reason, we may obtain information about your general internet usage by using a cookie file that is stored on the hard drive of your computer. Cookies contain information that is transferred to your computer's hard drive. They help us to improve our site and to deliver a better and more personalized service. They enable us:</p>
                                                <ul>
                                                    <li>To estimate our audience size and usage pattern.</li>
                                                    <li>To store information about your preferences, allowing us to customize our site according to your individual interests.</li>
                                                    <li>To speed up your searches.</li>
                                                    <li>To recognize you when you return to our site.</li>
                                                </ul>
                                                <p>You may refuse to accept cookies by activating the setting on your browser which allows you to refuse the setting of cookies. However, if you select this setting you may be unable to access certain parts of our site. Unless you have adjusted your browser setting so that it will refuse cookies, our system will issue cookies when you log on to our site.</p>
                                                <p>Please note that third-party sites which are linked to on our site may also use cookies, over which we have no control.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFiveFive">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiveFive" aria-expanded="false" aria-controls="collapseFiveFive">
                                                Disclosure of your information
                                            </button>
                                        </h2>
                                        <div id="collapseFiveFive" class="accordion-collapse collapse" aria-labelledby="headingFiveFive">
                                            <div class="accordion-body">
                                                <p>We may disclose your personal information to any member of our group, which means our subsidiaries, our ultimate holding company and its subsidiaries, as defined in section 736 of the UK Companies Act 1985, and other associated companies.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTMTwo">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTMTwo" aria-expanded="false" aria-controls="collapseTMTwo">
                                                We may disclose your personal information to third parties:
                                            </button>
                                        </h2>
                                        <div id="collapseTMTwo" class="accordion-collapse collapse" aria-labelledby="headingTMTwo">
                                            <div class="accordion-body">
                                                <p>In the event that we sell or buy any business or assets, in which case we may disclose your personal data to the prospective seller or buyer of such business or assets.</p>
                                                <p>If <?php echo SITE_NAME; ?> or substantially all of its assets are acquired by a third party, in which case personal data held by it about its customers will be one of the transferred assets.</p>
                                                <p>&nbsp;</p>
                                                <p>If we are under a duty to disclose or share your personal data in order to comply with any legal obligation, or in order to enforce or apply our terms of use&nbsp;<a href="<?php echo ROOT_DIR; ?>terms"><?php echo SITE_NAME_WWW; ?>/terms</a>&nbsp;and other agreements; or to protect the rights, property, or safety of <?php echo SITE_NAME; ?>, our customers, or others. This includes exchanging information with other companies and organizations for the purposes of fraud protection and credit risk reduction. For more information on <?php echo SITE_NAME; ?> Terms and Conditions, contact <a href="mailto:conditions@<?php echo SITE_EMAIL_DOMAIN; ?>">conditions@<?php echo SITE_EMAIL_DOMAIN; ?></a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTMThree">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTMThree" aria-expanded="false" aria-controls="collapseTMThree">
                                                Your rights
                                            </button>
                                        </h2>
                                        <div id="collapseTMThree" class="accordion-collapse collapse" aria-labelledby="headingTMThree">
                                            <div class="accordion-body">
                                                <p>You have the right to ask us not to process your personal data for marketing purposes. We will sometimes inform you (before collecting your data) if we intend to use your data for such purposes or if we intend to disclose your information to any third party for such purposes. You can exercise your right to prevent such processing by checking certain boxes on the forms we use to collect your data, when available. You can also exercise the right at any time by contacting us at <a href="mailto:privacy@<?php echo SITE_EMAIL_DOMAIN; ?>">privacy@<?php echo SITE_EMAIL_DOMAIN; ?></a> or <a href="mailto:data-policy@<?php echo SITE_EMAIL_DOMAIN; ?>">data-policy@<?php echo SITE_EMAIL_DOMAIN; ?></a></p>
                                                <p>Our site may, from time to time, contain links to and from the websites of our partner networks, advertisers, and affiliates. If you follow a link to any of these websites, please note that these websites have their own privacy policies and that we do not accept any responsibility or liability for these policies. Please check these policies before you submit any personal data to these websites.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTMFour">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTMFour" aria-expanded="false" aria-controls="collapseTMFour">
                                                Access to information
                                            </button>
                                        </h2>
                                        <div id="collapseTMFour" class="accordion-collapse collapse" aria-labelledby="headingTMFour">
                                            <div class="accordion-body">
                                                <p>The Act gives you the right to access information held about you. Your right of access can be exercised in accordance with the Act. Any access request may be subject to a fee of &pound;20 to meet our costs in providing you with details of the information we hold about you.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTMFive">
                                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTMFive" aria-expanded="false" aria-controls="collapseTMFive">
                                                Changes to our privacy policy
                                            </button>
                                        </h2>
                                        <div id="collapseTMFive" class="accordion-collapse collapse" aria-labelledby="headingTMFive">
                                            <div class="accordion-body">
                                                <p>The Website Policies and Terms &amp; Conditions may be changed or updated occasionally to meet the requirements and standards. Therefore, the Customers are encouraged to frequently visit these sections to be updated about the changes on the website. Modifications will be effective on the day they are posted&rdquo;.</p>
                                                <p>For further information regarding data protection, please contact data-policy@<?php echo SITE_EMAIL_DOMAIN; ?></p>
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