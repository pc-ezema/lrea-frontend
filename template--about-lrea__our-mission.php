<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Our Mission';
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
                                <h1 class="text-bold text-white ft-custom-hero mb-2">Our Mission and Vision</h1>
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
                            <h3>Our Mission</h3>
                            <p>Empowering individuals and organizations through education, membership benefits, networking opportunities, and professional development tools, bridging the gap between professionals, industries, and geographies.</p>

                            <br />
                            <h3>Our Vision</h3>
                            <p>Our Vision is to impact Two Million Learners with quality, premium, and affordable International Education annually.</p>

                            <p>We aspire to empower individuals from diverse backgrounds and regions, fostering a global community of lifelong learners. Our commitment to innovation and excellence drives us to continuously expand our educational offerings, leveraging cutting-edge technology and pedagogical approaches to ensure that our learners have access to the finest educational resources available.</p>

                            <p>In our pursuit of this Vision, we aim to not only provide access to education but also to transform lives, helping learners unlock their full potential, pursue their dreams, and contribute to a more interconnected and informed world. Through our dedication to inclusivity, we strive to bridge educational gaps and make quality international education accessible to underserved communities, promoting social equity on a global scale.</p>

                            <p>Furthermore, we envision establishing partnerships with leading educational institutions and organizations worldwide to create a network that promotes cross-cultural exchange and fosters collaborative research and development, enhancing the quality and relevance of our educational programs.</p>

                            <p>Our Vision is not just about the number of learners impacted but also about the enduring impact we leave on the world, making it a better place through the power of education. Together, we are committed to making this Vision a reality and empowering millions to reach new heights of knowledge and opportunity.</p>
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