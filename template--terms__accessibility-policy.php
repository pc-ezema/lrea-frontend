<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');
	
	// User Feedback works like this
	// $_SESSION['userfeedback'] = "Your topic has been posted";
	// $_SESSION['feedbacktype'] = "success";
	// Options for type are 'success' and 'error'
	// This will only work for the last entries into those session parameters. Both parameters need to be supplied.
	
	$page_title = 'Accessibility Policy';
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
                                <h1 class="text-bold text-white ft-custom-hero mb-2">Accessibility Policy</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="policy-container py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="my-5">
                                <p>Welcome to <?php echo SITE_NAME_WWW; ?></p>
                                <p>We are committed to making <a href="<?php echo ROOT_DIR; ?>"><?php echo SITE_NAME; ?></a> website accessible to as many different people as possible. We want everyone to enjoy the benefits of good clear communication. We are committed to helping improve accessibility by doing everything we can to make our services easier to use.</p>
                                <p>We have designed <?php echo SITE_NAME_WWW; ?>, our corporate website, to be informative and accessible to a wide range of users. We hope you find the content about <?php echo SITE_NAME; ?> easy to find and useful. If you have any comments or suggestions regarding this website, please let us know by emailing: <a href="mailto:info@<?php echo SITE_EMAIL_DOMAIN; ?>">info@<?php echo SITE_EMAIL_DOMAIN; ?></a></p>
                            </div>
                            <div class="my-5">
                                <h2>Help with navigating our website</h2>
                                <p>All pages contain a link to the home page by clicking on the <?php echo SITE_NAME; ?> logo and all pages include a search box. Breadcrumbs underneath the University sentence navigation should help users retrace their steps through the site.</p>
                            </div>
                            <div class="my-5">
                                <h2>Improving accessibility</h2>
                                <p>As part of our commitment to continuously improve the level of accessibility within our website, we have recently re-designed all aspects of the site. It's hoped that the design, navigation and functionality will be available and inclusive for all user groups regardless of their location, internet experience, and type of technology used.</p>
                                <p>The website has been developed in line with the priority levels of the Web Accessibility Initiative (WAI) guidelines, part of the World Wide Web Consortium (W3C).</p>
                                <p>This section outlines some of the accessibility features that we have provided for users and guidance on how to use them. Please refer to your web browser and operating system 'Help' for their additional accessibility features.</p>
                            </div>
                            <div class="my-5">
                                <h2>Design</h2>
                                <p>The <?php echo SITE_NAME; ?> website has been optimised to be viewed at a minimum screen resolution of 1024 x 768 within the following browsers or, their corresponding higher version numbers:</p>
                                <p>Internet Explorer 5.5</p>
                                <p>Netscape Communicator 7.2</p>
                                <p>Firefox 1.0</p>
                                <p>Opera 8.0</p>
                                <p>Safari 2.0</p>
                                <p>The colour combinations have been checked to ensure appropriate colour contrast and colour difference. The pages are not based on frames and can therefore be printed easily.</p>
                                <p>The text within the content pages has been set to relative sizes, which can be increased or decreased for more comfortable screen reading.</p>
                                <p>Text equivalents have been provided for every non-text element, ALT tags provided for all images and long descriptions for charts and graphs. Page and table header tags have also been included.</p>
                            </div>
                            <div class="my-5">
                                <h2>Access Keys</h2>
                                <p>Access keys allow users to navigate around the most frequently used areas of <?php echo SITE_NAME_WWW; ?> without using the mouse. To use the Access key on a PC, hold down the Alt key and press the appropriate number, then Enter/Return. To be reminded of our Access keys, press Alt 8 and Return from any page in the site to display a full list. Access key functionality on a Mac system is triggered by pressing ctrl + 'h'.</p>
                            </div>
                            <div class="my-5">
                                <h2>Alternative formats</h2>
                                <p>In order to make our website fully accessible, we provide our information in a number of alternative formats. However, the website does include a number of PDF files, which we are unable to re-produce in alternative formats.</p>
                                <p>We recommend that users download the latest version of Adobe Acrobat to view Adobe PDF files at: www.adobe.com Adobe Acrobat 5.0 and 6.0 software provides a number of capabilities that improve access for users. For further information about Adobe's PDF accessibility, please visit: access.adobe.com</p>
                                <p>We encourage any users, experiencing problems with our site to email us at: feedback@<?php echo SITE_EMAIL_DOMAIN; ?>. We will work to solve any issues as a priority and where possible, will respond to individual requests for information in alternative formats.</p>
                            </div>
                            <div class="my-5">
                                <h2>Fonts</h2>
                                <p>All text sizes within the content pages and navigation have been set to relative sizes, which can be increased or decreased for more comfortable screen reading. Colours can be changed by users for improved readability. These can be altered in your Web browser.</p>
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