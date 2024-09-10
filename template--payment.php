<?php 
	require_once('connections/config.php');
	require_once('components/misc.inc.php');
	
	$page_title = SITE_NAME . ' Payment Information';
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

    <div class="main-content">

        <div class="container my-5 px-4">
            <section>
                <div class="container">
                    <div class="row no-gutters border shadow-sm rounded-9">
                        <!-- Bank Information Section -->
                        <div class="card border-0 col-lg-6 bg-light py-4">
                            <h4 class="text-center mt-4">Bank Information</h4>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 row justify-content-center">
                                        <div class="col-lg-10 d-flex justify-content-center">
                                            <img class="fit-image" src="<?php echo ROOT_DIR; ?>images/transfer-3.svg" alt="payment confirmed">
                                        </div>
                                        <h4 class="text-center mt-4">N150,000 (Naira)</h4>
                                        <h4 class="text-center">One Year Tuition Subscription Fee</h4>
                                        <!-- <h5 class="text-center text-green">(Valid till July 31st, 2024)</h5> -->
                                    </div>
                                </div>
                                <div class="row my-5 pt-4 text-center border-top">
                                    <div class="col-12 mb-4">
                                        <h6>Account Name</h6>
                                        <h4 class="font-weight-bold">London Real Estate Academy</h4>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <h6>Account Number</h6>
                                        <h4 class="font-weight-bold">1304554697</h4>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <h6>Bank</h6>
                                        <h4 class="font-weight-bold">Providus Bank</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- How To Pay Section -->
                        <div class="card border-0 col-lg-6 py-4 animated fadeIn">
                            <h4 class="text-center mt-4">How To Pay</h4>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 row justify-content-center">
                                        <div class="col-lg-10 d-flex justify-content-center">
                                            <img class="fit-image" src="<?php echo ROOT_DIR; ?>images/todo-3.svg" alt="payment confirmed">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4" style="font-size: 16px;">
                                    <div class="d-flex align-items-start border-bottom py-3 px-2 mb-2">
                                        <img src="<?php echo ROOT_DIR; ?>images/finger-right.svg" style="width: 22px; margin-top: 3px;" alt="finger right">
                                        <span class="ms-2">
                                            Initiate a bank transfer or deposit the tuition fee amount to the provided bank account.
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-start border-bottom py-3 px-2 mb-2">
                                        <img src="<?php echo ROOT_DIR; ?>images/finger-right.svg" style="width: 22px; margin-top: 3px;" alt="finger right">
                                        <span class="d-flex flex-column ms-2">
                                            After completing the transaction, send us your payment proof along with the following details: <br />
                                            <span class="d-inline-block mb-1 mt-2 bold-font">Name.</span>
                                            <span class="d-inline-block mb-1 bold-font">Email.</span>
                                            <span class="d-inline-block bold-font">Phone Number.</span>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-start border-bottom py-3 px-2 mb-2">
                                        <img src="<?php echo ROOT_DIR; ?>images/finger-right.svg" style="width: 22px; margin-top: 3px;" alt="finger right">
                                        <span class="ms-2">
                                            Send payment proof and details to <a href="mailto:finance@lrea.co.uk" class="text-decoration-none text-primary">finance@lrea.co.uk</a>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-start py-3 px-2 mb-2">
                                        <img src="<?php echo ROOT_DIR; ?>images/finger-right.svg" style="width: 22px; margin-top: 3px;" alt="finger right">
                                        <span class="ms-2">
                                            Upon completing your payment, ensure to register on our official website <a href="https://lrea.co.uk" class="text-decoration-none text-light-blue">www.lrea.co.uk</a> using the <span class="bold-font text-green text-decoration-underline">same email address</span> used for sending payment proof. <br><em>This will facilitate easy tracking of your payment</em>.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>

    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>  
    
    <?php require_once('components/javascripts.inc.php'); ?>
    <?php include_once('components/end-of-body.inc.php'); ?> 

    <style>
        /* Add some padding and margin for better readability */
        .container {
            padding: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .border-bottom {
            border-bottom: 1px solid #ddd;
        }

        .bold-font {
            font-weight: bold;
        }

        .text-green {
            color: #ea383e;
        }

        /* Animation for fadeIn */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        .fadeIn {
            animation-name: fadeIn;
        }
    </style>
</body>
</html>