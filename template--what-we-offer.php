<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

// User Feedback works like this
// $_SESSION['userfeedback'] = "Your topic has been posted";
// $_SESSION['feedbacktype'] = "success";
// Options for type are 'success' and 'error'
// This will only work for the last entries into those session parameters. Both parameters need to be supplied.

$page_title = 'What We Offer';
require_once('components/doctype.inc.php');
?>

<style>
    .section-title {
        font-weight: bold;
        color: #000;
        margin-bottom: 30px;
    }

    .offer-list {
        list-style-type: none;
        padding-left: 0;
        text-align: center;
    }

    .offer-list li {
        font-size: 18px;
        margin-bottom: 15px;
    }

    .offer-list li i {
        color: #007bff;
        margin-right: 10px;
    }

    .card {
        border: none;
        background-color: #fff;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-10px);
    }


    .exclusive-list {
        list-style-type: none;
        padding-left: 0;
    }

    .exclusive-list li {
        margin-bottom: 15px;
        font-size: 18px;
        position: relative;
        padding-left: 30px;
    }

    .exclusive-list li::before {
        content: "‣";
        position: absolute;
        left: 0;
        color: #007bff;
        font-size: 20px;
    }

    .cta-btn {
        background-color: #ea383e;
        color: white;
        text-align: center;
        padding: 12px 30px;
        border-radius: 30px;
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
    }

    .cta-btn:hover {
        background-color: #000;
        text-decoration: none;
        color: white;
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
                            <h1 class="text-bold text-white ft-custom-hero mb-2">What We Offer</h1>
                            <p class="text-white">Unlock prestigious British diplomas and exclusive membership benefits that propel your professional journey forward.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-5">
            <!-- What We Offer Section -->
            <h2 class="section-title text-center">As a private for-profit business school, LREA provides:</h4>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Offer Details -->
                    <ul class="offer-list">
                        <li><i class="bi bi-check-circle-fill"></i>OFQUAL regulated diploma degrees (levels 3-7) via awarding bodies, leading to degrees from top UK Universities.</li>
                        <li><i class="bi bi-check-circle-fill"></i>Access to outstanding British Diplomas at nearly half the university fees.</li>
                    </ul>
                </div>
            </div>

            <!-- Exclusive Membership Benefits Section -->
            <div class="row mt-5">
                <div class="col-md-12 mb-3 text-center">
                    <h3 class="section-title">Exclusive Membership Benefits</h3>
                </div>

                <!-- Cards for Exclusive Benefits -->
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="card-title">Global networking opportunities</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="card-title">Professional development resources</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="card-title">Exclusive events and conferences</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="card-title">Personalized mentorship programs</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="card-title">Industry insights and updates</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="row mt-5 text-center">
                <div class="col-md-12">
                    <h3 class="section-title">Join Us Today</h3>
                    <p>Explore how LREA can help you advance in your career. Become a part of our community and unlock your potential today.</p>
                    <a href="<?php echo $link_root_path; ?>landing" class="cta-btn">Learn More & Apply</a>
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