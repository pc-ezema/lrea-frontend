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
                            <p><span style="font-size: 18px;"><strong>Education powered by Technology</strong></span> <br /><br /><?php echo SITE_NAME; ?> is an Ed-tech pathway college offering affordable and qualitative International regulated diplomas and executive education courses to the world, with a unique focus on Africa, Asia and the Middle East.<br /> <br /><?php echo SITE_NAME; ?>, as a Pathway College offers diploma degrees (level 3, 4, 5, 6 and 7) via OFQUAL regulated awarding bodies leading to Degrees from top UK Universities. <br /> <br /><?php echo SITE_NAME; ?> is a private for-profit business school providing International Students, access to outstanding British Diplomas at almost half the fee of the Universities.</p>
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