
<div class="card card-body rounded-0 p-0">
    <div class="row">
        <div class="col-lg-5 d-lg-block d-none">
            <div class="img-box-cover h-100 w-100 overflow-hidden position-relative" style="max-height: 650px;">
                <img src="<?php echo ROOT_DIR; ?>images/semp-mini.jpg" class="img-fluid object-fit-cover object-position-center w-100 h-100">
            </div>
        </div>
        <div class="col-lg-7">
            <div class="p-lg-5 p-3">
                <h3 class="text-bold mb-3">SIGN IN to continue</h3>
                <p class="mb-3">I do not have an account. <a href="<?php echo $this_signup_link; ?>" class="text-bold text-decoration-none">Create an account</a></p>

                <form action="<?php echo $this_signin_link; ?>" method="post" class="append-loader">
                    <?php if (isset($login_error)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">                            
                            <?php echo $login_error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    
                    <div class="form-floating mb-3">
                        <input name="student_username" type="email" value="<?php if (isset($_POST['student_username'])) echo $_POST['student_username']; ?>" class="form-control rounded-0 <?php if (isset($form_errors["student_username"])) echo 'is-invalid'; ?>" id="floatingEmail" required>
                        <label for="floatingEmail">Email Address</label>
                        <?php if (isset($form_errors["student_username"])) echo $form_errors["student_username"]; ?>
                    </div>
                    
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <input name="student_password" type="password" id="password2" class="form-control rounded-0 form-control-lg" placeholder="Password" required>
                                <label for="password2">Password</label>
                            </div>
                            <span class="input-group-text border-dark" id="basic-addon2" onclick="toggleShowPassword();">
                                <i class="password-eye fa fa-eye"></i>
                            </span>
                        </div>
                        <?php if (isset($form_errors["student_password"])) echo $form_errors["student_password"]; ?>
                    </div>
                    
                    <input type="hidden" name="enrol" value="<?php echo $dis_programme_url; ?>" >
                    <button name="signin" value="Log In" class="btn btn-custom-size w-100 text-white bg-green text-white btn-custom append-loader-btn">Sign In</button>
                </form>

                <p class="my-4">I have forgotten my password. <a href="<?php echo ROOT_DIR_PORTAL.'user/forgot-password'; ?>" class="text-bold text-decoration-none">Reset my password</a></p>
            </div>
        </div>                                                                
    </div>
</div>