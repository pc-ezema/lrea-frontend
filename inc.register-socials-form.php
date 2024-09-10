<div class="card card-body rounded-0 p-0">
    <div class="row">
        <div class="col-lg-5 d-lg-block d-none">
            <div class="img-box-cover h-100 w-100 overflow-hidden position-relative" style="max-height: 650px;">
                <img src="<?php echo ROOT_DIR; ?>images/semp-mini.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="p-lg-5 p-3">
                <?php if ($websiteLogoURL) { // Will be declared in the portal ?>
                    <?php $main_website_url = defined('ROOT_DIR_UK') ? ROOT_DIR_UK : ROOT_DIR; ?>
                    <a href="<?php echo $main_website_url; ?>" class="d-lg-none d-block">
                        <img src="<?php echo $websiteLogoURL; ?>" class="img-fluid mb-3" width="150">
                    </a>
                <?php } ?>

                <h3 class="text-bold mb-3">Create an account</h3>
                <p class="mb-3">Already have an account? <a href="<?php echo $this_signin_link; ?>" class="text-bold text-decoration-none">Sign in</a></p>

                <form action="<?php echo $this_signup_link; ?>" id="register_form" method="post" class="append-loader">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-floating mb-3">
                                <input name="first_name" type="text" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" class="form-control rounded-0 <?php if (isset($form_errors["first_name"])) echo 'is-invalid'; ?>" id="floatingFirstName" required>
                                <label for="floatingFirstName">First Name</label>
                                <?php if (isset($form_errors["first_name"])) echo $form_errors["first_name"]; ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating mb-3">
                                <input name="last_name" type="text" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" class="form-control rounded-0 <?php if (isset($form_errors["last_name"])) echo 'is-invalid'; ?>" id="floatingLastName" required>
                                <label for="floatingLastName">Last Name</label>
                                <?php if (isset($form_errors["last_name"])) echo $form_errors["last_name"]; ?>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating mb-3">
                                <input name="email" type="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" class="form-control rounded-0 <?php if (isset($form_errors["email"])) echo 'is-invalid'; ?>" id="floatingEmail" required>
                                <label for="floatingEmail">Email Address</label>
                                <?php if (isset($form_errors["email"])) echo $form_errors["email"]; ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating mb-3">
                                <input name="password" type="password" value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" class="form-control rounded-0" id="floatingPassword1" required>
                                <label for="floatingPassword1">Password</label>
                                <?php if (isset($form_errors["password"])) echo $form_errors["password"]; ?>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating mb-3">
                                <input name="confirm_password" id="confirm_password" type="password" class="form-control rounded-0" id="floatingPassword2" required>
                                <label for="floatingPassword">Confirm Password</label>
                                <?php if (isset($form_errors["confirm_password"])) echo $form_errors["confirm_password"]; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($termsConditionURL && $privacyPolicyURL) { ?>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input">
                            <label class="form-check-label">By clicking 'Create Account', you agree to our <a href="<?php echo $termsConditionURL; ?>" class="text-bold">Terms &amp; Conditions</a> and <a href="<?php echo $privacyPolicyURL; ?>" class="text-bold">Privacy Policy</a>.</label>
                        </div>
                    <?php } ?>

                    <button name="signup" value="Sign Up" class="btn btn-custom-size w-100 text-white bg-green text-white btn-custom">Create Account</button>
                </form>                        
            </div>
        </div>
    </div>
</div>