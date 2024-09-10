<?php 
	require_once('connections/config.php');
    require_once('components/misc.inc.php');

    $_SESSION['initiate_checkout_event'] = true; // This session is used to make sure the Facebook InitiateCheckout event on the checkout page doesn't get fired multiple times as it will get unset over there.

    require('inc.cart-page-query.php');

    $use_the_migrated_criteo_tag = true; // The 'viewBasket' criteo tag was moved from the dynamic-enrolment.php where it had always been before.
    $display_degree_course_instalment_fees = false;
	
	$page_title = 'Cart';
	require_once('components/doctype.inc.php');
?>

    <?php if ($use_the_migrated_criteo_tag && $totalRows_DisProg && $site_version == "online") { // If cart is not empty ?> 
        <?php // Loop through the courses in the cart in order to retreive their course codes and fees.
            $criteo_basket_item = array();

            do { // Prepare the individual course details that are in the cart for the criteo items.
                if ($row_DisProg['course_code']) {                    
                    $criteo_basket_item[] = "{id: '".$row_DisProg['course_code']."', price: ".round($row_DisProg['discounted_fee'], 2).", quantity: 1}";
                }
            } while($row_DisProg = mysqli_fetch_assoc($DisProg));
        
            // Then reset the pointer to the beginning
            $rows = mysqli_num_rows($DisProg); if($rows > 0) { mysqli_data_seek($DisProg, 0); $row_DisProg = mysqli_fetch_assoc($DisProg); }    
        ?>

        <?php if (count($criteo_basket_item)) { // Start Criteo Tag ?>
            <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
            <script type="text/javascript">
                window.criteo_q = window.criteo_q || [];
                var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
                window.criteo_q.push(
                    { event: "setAccount", account: <?php echo $criteo_partner_id; ?>}, // You should never update this line
                    { event: "setEmail", email: "" }, // Can be an empty string 
                    { event: "setSiteType", type: deviceType},
                    { event: "viewBasket", item: [
                        <?php echo implode(', ', $criteo_basket_item); ?>
                    ]}
                );
            </script> 
        <?php } // End Criteo Tag ?>
    <?php } ?>

    <style>

        .bg-warning-2{
            background-color: #ffc1073b
        }

        .ft-lg-2{
            font-size: 1.2rem;
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
                            <div class="text-start">

                                <div class="mb-4">
                                    <a href="<?php echo $link_root_path; ?>" class="btn btn-custom-size-2 btn-light btn-custom"><i data-feather="chevron-left"></i> Continue to shopping</a>
                                </div>

                                <h1 class="text-bold text-white ft-custom-hero mb-4">Your Cart</h1>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-container py-5">
                <div class="container cart-content">
                    <?php require('inc.cart-page-contents.php'); ?>
                </div>
            </div>
        </div>
    <!-- /Main Content -->
    
    <?php require_once('components/footer.inc.php'); ?>
    
    <?php require_once('components/javascripts.inc.php'); ?>

    <script>
        $(document).ready(function(){
            $("body").on("click", "#btnDiscount", function() {
                $(".coupon-apply-sect").slideToggle();
            });

            $('body').on("focus", "#discount_code", function(e) {
                $('#is-invalid').html(' ');
            });

            check_selected_plan();

            $("body").on("change", ".instalment-plans", function() {
                check_selected_plan();
            });
            
            $("body").on("click", ".customSwitchPayment", function () {
                var paymentPlanSegment = $(this).closest(".cart-view-sect"),
                    outerPaymentSegment = $(this).closest(".payment-row"),
                    displayCheckoutSegment = true;

                if ($(this).is(":checked")) {
                    paymentPlanSegment.find(".payment-plan-form").show();

                    paymentPlanSegment.find('.lto-removed').show();
                    paymentPlanSegment.find('.lto-applied').hide();

                    outerPaymentSegment.find('.lto-price-off').show();
                    outerPaymentSegment.find('.lto-price-on').hide();
                } 
                else {
                    paymentPlanSegment.find(".payment-plan-form").hide();

                    paymentPlanSegment.find('.lto-removed').hide();
                    paymentPlanSegment.find('.lto-applied').show();

                    outerPaymentSegment.find('.lto-price-off').hide();
                    outerPaymentSegment.find('.lto-price-on').show();
                }

                $.each($(".customSwitchPayment"), function(x, y){
                    if ($(this).is(":checked")) {
                        displayCheckoutSegment = false;
                    }
                });

                if (displayCheckoutSegment) {
                    $('.lto-checkout').show();
                    $('.lto-instructions').hide();
                }
                else {
                    $('.lto-checkout').hide();
                    $('.lto-instructions').show();
                }

                check_selected_plan();
            });

            
            <?php if ($_GET['plan']) { ?>
                document.getElementById('active-payment-plan').click()
            <?php } ?>

            function check_selected_plan() {
                $('.instalment-submit-button').hide(); <?php // Hide all the submit buttons ?>

                var instalment_plan_fields = $(".instalment-plans:checked");
                
                instalment_plan_fields.each(function() {
                    var selected_instalment_plan = $(this).val();
                    var total_amount = $(this).attr('data-full-pay');
                    var num_of_instalments = $(this).attr('data-num_of_instalments');
                    var amount_per_instalment = $(this).attr('data-amount_per_instalment');
                    var num_of_months_between_instalments = $(this).attr('data-num_of_months_between_instalments');
                    var target_container_id = $(this).attr('data-target_container_id');

                    if (selected_instalment_plan) {
                        $(this).closest('.row').find('.instalment-submit-button').show();
                    }

                    if (total_amount) {
                        $(this).closest('.cart-view-sect').find('.lto-price-off .instalment-full-fee-updated').html(total_amount);
                        displayInstallments(num_of_instalments, amount_per_instalment, num_of_months_between_instalments, total_amount, target_container_id);
                    }
                });
            }

            function displayInstallments(num_of_instalments, amount_per_instalment, num_of_months_between_instalments, total_amount, target_container_id) {
                // Get the current date
                let current_date = new Date();
                
                // Create the table HTML
                let table_html = '<table class="table table-striped ft-sm table-bordered table-sm instalments-' + num_of_instalments + '">' +
                    '<thead class="bg-black text-white">' +
                    '<tr>' +
                    '<th scope="col" style="width: 50px;">#</th>' +
                    '<th scope="col" style="width: 155px;">Due Date</th>' +
                    '<th scope="col">Amount</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';

                let next_due_date = current_date;
                for (let i = 1; i <= num_of_instalments; i++) {
                    table_html += '<tr>' +
                        '<th scope="row">' + i + '</th>';

                    if (i == 1) {
                        table_html += '<td>Now</td>';
                    } else {
                        let months_to_add = Number(num_of_months_between_instalments);
                        next_due_date.setMonth(next_due_date.getMonth() + months_to_add);
                        if (next_due_date.getMonth() < current_date.getMonth()) {
                            next_due_date.setFullYear(next_due_date.getFullYear() + 1);
                        }
                        table_html += '<td>' + next_due_date.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) + '</td>';
                    }

                    table_html += '<td>' + amount_per_instalment + '</td>' +
                        '</tr>';
                }

                table_html += '<tr>' +
                    '<td colspan="2" class="make-bold bg-warning-2 ft-lg-2">Total</td>' +
                    '<td class="make-bold bg-warning-2 ft-lg-2">' + total_amount + '</td>' +
                    '</tr>' +
                    '</tbody>' +
                    '</table>';

                // Replace the contents of the target container with the table HTML
                let container = document.querySelector('#' + target_container_id);
                container.innerHTML = table_html;
            }

        });
    </script>

    <?php include_once('components/end-of-body.inc.php'); ?> 
  </body>
</html>