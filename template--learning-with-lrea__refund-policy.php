<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

// User Feedback works like this
// $_SESSION['userfeedback'] = "Your topic has been posted";
// $_SESSION['feedbacktype'] = "success";
// Options for type are 'success' and 'error'
// This will only work for the last entries into those session parameters. Both parameters need to be supplied.

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
        <div class="jumbotron position-relative z-index-1 py-5 bg-dark-2 add-bg-image-2 position-relative">
            <div class="container position-relative" style="z-index: 1">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-lg-0 mb-3">
                        <div class="">
                            <h1 class="text-bold text-white ft-custom-hero mb-2">General Refund Policy</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="policy-container py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div>
                            <h4>Non-refundable Fees.</h4>
                        </div>
                        <div>
                            <div>
                                <p>There would be no refund for any application (for Tuition Fee refund) made less than&nbsp;30 days to the commencement date of the Course. (Please read your <a href="<?php echo ROOT_DIR; ?>nigeria/terms/study-contract" target="_blank">Study Contract with <?php echo SITE_NAME; ?></a>).<br /><br /></p>
                            </div>
                        </div>
                        <div>
                            <h4>Administrative Charge:</h4>
                            <div>
                                <p>All applications for a Tuition Fee refund are subject to an administrative charge. This would be detailed on your Invoice or Payment Page.<br /><br /></p>
                            </div>
                        </div>
                        <div>
                            <h4>Method of Payment:</h4>
                            <div>
                                <p>If for exceptional cases, a refund request is approved; it will strictly be paid in the same method and to the same account as the money was&nbsp;originally received. Evidence of payment and a copy of the bank statement will be required&nbsp;to verify details. This is to comply with money laundering regulations and no&nbsp;exceptions can be made.<br /><br /></p>
                            </div>
                        </div>
                        <div>
                            <h4>Disbursements to 3rd parties:</h4>
                            <div>
                                <p>Any fees payable by the School on behalf of the student or in connection with the student&rsquo;s application to a third party (for example, courier fees, Registration fees payable to the accrediting bodies, Progression Fees etc.) are non-refundable and non-transferable.<br /><br /></p>
                            </div>
                        </div>
                        <div>
                            <h4>Refund Policy - Executive Education Courses (CPD)</h4>
                            <div>
                                <p>Once payment is received and immediate access to the <?php echo SITE_NAME; ?> Online Study Portal is available to the student, refunds are not available.&nbsp; This does not affect your statutory rights.</p>
                                <p>The immediate accessibility of the <?php echo SITE_NAME; ?> Online Study Portal is only available to the student when payment has been made and funds cleared and any variance of this term shall be upon such terms as <?php echo SITE_NAME; ?> dictates.</p>
                                <p>Please ensure that you choose the exact Course you would like to study before paying for it.</p>
                                <p>The assessment for the Special Executive Master Programmes is assignment based. <br /><br />The assessment for the Professional Certificate and Advanced Professional Certificate Courses is Quiz based. The Quiz is of a multiple-choice questions variety.&nbsp;</p>
                                <p>There are only three (3) attempts for quizzes as long as payments have been cleared. In exceptional circumstances, the Quiz access can be re-activated if the 80% score is not achieved within the 3 trial attempts.&nbsp;<br /><br /></p>
                            </div>
                        </div>
                        <div>
                            <h4>Refund Policy &ndash; Special Executive Master Programmes (SEMP) - Classroom Training</h4>
                            <div>
                                <p>Registration: On the submission of the Registration for any of our Online or Classroom SEMP Courses, you are required to pay a Registration Fee. It is only after the payment of your Registration Fee that your Conditional Offer Letter would be processed.<br /><br />Tuition Fees: On receiving your Conditional Offer Letter, you are required to pay your Tuition Fee. Please note that full payment must be received prior to the course commencement in order to confirm your place. Only those who have paid the fees in full will be admitted to the course.<br /><br />The tuition fee stated for each programme only covers specifically what has been stated that it covers and <?php echo SITE_NAME; ?> is only responsible for this. <br /><br /><?php echo SITE_NAME; ?> is not liable for any other external factors like health certificate required to travel, transit visas, travel insurance, lost baggage, missed flights, missed connecting flights, inability to be admitted into any of your destinations, cancelled flights and any other external factors that are not within what is specifically stated to be covered by your tuition fee and invariably what <?php echo SITE_NAME; ?> is responsible for.<br /><br />For programme fees that include flight and accommodation bookings, no refund is possible once the flight ticket or accommodation has been booked and paid for. If for any reason the student is unable to attend the programme, the student can be rescheduled to another upcoming session with an attendant change and administrative fee payable by the student.<br /><br />A student is only entitled to reschedule a programme up to three (3) times after the programme is missed (within our training calendar) at the same tuition fee with the attendant change and administrative fee. The student is liable to pay the balance of any new tuition fee applicable if the student reschedules to the fourth (4th) scheduled programme on our training calendar and this would also include the attendant change and administrative fee.<br /><br />Every student must ensure they have all of the documentation required to fly or enter the country where they are taking any of our Programmes. <br /><br /><?php echo SITE_NAME; ?> only takes responsibility for the application of the UAE two (2) weeks Tourist Visas and only if the student has taken advantage of such a service. All other Visa applications to all other countries where the SEMP Classroom Programmes may run are the responsibility of the student. <br /><br />The determination of any Visa application to any country or the UAE two (2) weeks Tourist Visa handled on behalf of the student by <?php echo SITE_NAME; ?> is only at the discretion of the country's immigration office and <?php echo SITE_NAME; ?> is not liable for any result of such application and the effect on the student. <br /><br />Every student is urged to ensure first that they have the required Visa needed to enter any country they may transit through or of which their final destination is. In the case of the Programmes held in the UAE, students must ensure that they do not have any reason why the UAE Immigration may reject their Visa application. <br /><br />No refunds will be made in the case of a Visa rejection that <?php echo SITE_NAME; ?> determines could have been reasonably avoided by the student.<br /><br /><strong>Cancellations &amp; Refund:</strong> cancellations and refund must be received in writing at least four (4) weeks before the commencement date of the Session you are registered for as long as the above terms and conditions does not apply.&nbsp;<br /><br />A refund is only possible if <?php echo SITE_NAME; ?> fails to organise the programme when promised or reschedule another session. </p>
                                <p>&nbsp;</p>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div>
                            <h4>Refund Policy - Degree Programmes&nbsp;</h4>
                            <div>
                                <p>Once payment is received and immediate access to the <?php echo SITE_NAME; ?> Online Study Portal is available to the student, refunds are not available. This does not affect your statutory rights.</p>
                                <p>The immediate accessibility of the <?php echo SITE_NAME; ?> Online Study Portal is only available to the student when payment has been made either in full or on an instalment basis. Once the access to the course enrolled for, is activated, there can no longer be any refund.<br /><br />Refund Policy &ndash; Top-up Degree</p>
                            </div>
                        </div>
                        <div>
                            <div>
                                <ul>
                                    <li>All Top-up fees are subject to change by the Universities without prior notice to colleges.</li>
                                    <li>All certificates are issued directly by the Universities.</li>
                                    <li>Top-up may be delivered by 3rd Party colleges.</li>
                                    <li>None of the top-up courses is delivered by <?php echo SITE_NAME; ?>.</li>
                                    <li><?php echo SITE_NAME; ?> is only responsible for the delivery of Level 4, 5, and 7 Diploma Programmes.</li>
                                </ul>
                                <p>All Top-up Fees paid on behalf of students to the Universities are inclusive of an Administrative charge. This Fee might therefore vary from what the Universities have listed on their website. <br /><br />We do not offer a refund for the administrative charge once the service has been delivered, i.e once the student has been successfully placed on to the Top-up Degree.</p>
                                <p>Once payment is received and remitted to the affiliate University as well, there is no refund available.&nbsp;</p>
                                <p>This does not affect your statutory rights.<br /><br /><br /><br /><strong>Refund Policy &ndash; Corporate/Business Accounts<br /></strong><br />Once payment is received and the Corporate/Business Account Wallet is funded on the <?php echo SITE_NAME; ?> Online Study Portal; refunds are not available. This does not affect your statutory rights.<br /><br />The Corporate/Business Account Wallet is only funded when payment has been made in full and funds cleared in the <?php echo SITE_NAME; ?> Bank Account. Any variance of this term shall be upon such terms as <?php echo SITE_NAME; ?> dictates.<br /><br />The Business/Corporate Account Administrators as chosen by the Company must ensure that the Programmes or courses, they want to assign to their employees are activated on their Account. <br />If required by the Administrator; additional Programmes or Courses can be activated on any Corporate/Business Account once a request for this has been made in writing.<br /><br />The Administrators must ensure that the exact Course they want to assign to an employee is the one chosen before they apply the funds to their Wallet for the course. Once this process is completed, the amount applied from the funds on their Wallet can no longer be refunded back into their Wallet.<br />While payments made that have been funded into the Corporate/Business Account Wallet are non-refundable, the funds in this Wallet do not expire.<br /> <br />The funds in the Corporate/Business Wallet can be allocated at any time for any of the Programmes or Courses activated on the <?php echo SITE_NAME; ?> Online Study Portal.<br /><br /></p>
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