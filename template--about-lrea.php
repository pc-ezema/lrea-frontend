<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

// User Feedback works like this
// $_SESSION['userfeedback'] = "Your topic has been posted";
// $_SESSION['feedbacktype'] = "success";
// Options for type are 'success' and 'error'
// This will only work for the last entries into those session parameters. Both parameters need to be supplied.

$page_title = 'About LREA';
require_once('components/doctype.inc.php');
?>

<style>
    .section-title {
        font-weight: bold;
        color: #000;
        margin-bottom: 20px;
    }

    .card {
        border: none;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    }

    .core-values-list {
        list-style: none;
        padding-left: 0;
    }

    .core-values-list li {
        background-color: #ea383e;
        color: #fff;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        display: inline-block;
        width: 45%;
        margin-right: 10px;
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
                            <h1 class="text-bold text-white ft-custom-hero mb-2">About us</h1>
                            <p class="text-white"><?php echo SITE_NAME; ?> offers you a powerful new way to learn online. Every course has been designed according to principles of effective learning.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="policy-container py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Who we are</h3>
                        <p><span style="font-size: 18px;"><strong>Education powered by Technology</strong></span> <br /><br /><?php echo SITE_NAME; ?> is an Ed-tech pathway college revolutionizing education through technology. We provide affordable, high-quality, internationally recognized diplomas and executive education courses, with a focus on Africa, Asia, and the Middle East.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-5">
            <div class="row">
                <!-- Community Section -->
                <div class="col-md-6">
                    <h2 class="section-title">Our Community</h2>
                    <p class="">
                        LREA is a premier international membership community, fostering interdisciplinary collaboration, knowledge sharing, educational resources, and career advancement.
                        Our diverse membership spans various sectors and professions.
                    </p>
                </div>
                <!-- Philosophy Section -->
                <div class="col-md-6">
                    <div class="card p-4">
                        <h2 class="section-title">Our Philosophy</h2>
                        <p>
                            We believe professional growth knows no boundaries. That's why we offer tailored support, guidance, and resources to help members achieve their goals and make meaningful connections worldwide.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Core Values Section -->
            <div class="row mt-5">
                <div class="col-md-12">
                    <h2 class="section-title text-center">Core Values</h2>
                    <ul class="core-values-list text-center">
                        <li>Excellence</li>
                        <li>Innovation</li>
                        <li>Collaboration</li>
                        <li>Diversity</li>
                        <li>Professionalism</li>
                    </ul>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="row mt-5 text-center">
                <div class="col-md-12">
                    <h2 class="section-title">Join Our Community</h2>
                    <p>
                        Discover how LREA can empower your professional journey. Explore our membership benefits, education programs, and networking opportunities today.
                    </p>
                    <a href="<?php echo $link_root_path; ?>landing" class="cta-btn">Become a Member</a>
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