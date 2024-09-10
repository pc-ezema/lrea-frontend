<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Page Not Found';
	require_once('components/doctype.inc.php');
?>

	<style>
		body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f6f6f6;
        }
	</style>
    
    <?php include_once('components/end-of-head.inc.php'); ?> 
  </head>

  <body>    
    <!-- Main Content -->
    <nav class="navbar bg-transparent fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand text-white mb-0">
                <a href="<?php echo ROOT_DIR.$location_slug; ?>" class="btn btn-custom-size-2 btn-light btn-custom"><i data-feather="arrow-left"></i> Back home</a>
            </span>
        </div>
    </nav>

    <div class="auth-container d-flex align-items-center w-100 h-100 px-lg-2 px-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="error-content">
                        <h2 class="text-bold ft-custom-hero mb-3">Page not found</h2>
                        <p class="ft-normal-2 mb-5">Uh oh, we can't seem to find the page you're looking for. Try going back to the home page.</p>
                        <a class="btn btn-custom-size-2 text-bold btn-custom-color-1 text-white btn-custom" href="<?php echo ROOT_DIR.$location_slug; ?>">Back to home page</a>
                    </div>
                </div>

                <div class="col-lg-6 offset-lg-1">
                    <img src="<?php echo ROOT_DIR; ?>images/404-error-bg.png" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content --> 
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>