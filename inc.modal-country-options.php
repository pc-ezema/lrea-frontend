<div class="modal fade" id="miniCountryModal" tabindex="-1" aria-labelledby="miniCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-8 bg-white">
            <div class="modal-header border-0">
                <h1 class="modal-title text-bold text-dark fs-5" id="miniCountryModalLabel">Choose country</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="country-list-container text-dark">
                    <div class="row">
                        <?php /*if ($coresite_curr_country_name) { ?>
                            <div class="col-lg-12">
                                <p>Selected country: <em><?php echo $coresite_curr_country_name; ?></em></p>
                            </div>
                        <?php }*/ ?>

                        <div class="col-lg-6">
                            <div class="mb-5">
                                <h5 class="text-bold text-green">Europe</h5>
                                <ul class="list-unstyled col-count-country-2 country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>united-kingdom" class="text-decoration-none">United Kingdom</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>ireland" class="text-decoration-none">Ireland</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>portugal" class="text-decoration-none">Portugal</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>netherlands" class="text-decoration-none">Netherland</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>germany" class="text-decoration-none">Germany</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>turkey" class="text-decoration-none">Turkey</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>malta" class="text-decoration-none">Malta</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>cyprus" class="text-decoration-none">Cyprus</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>france" class="text-decoration-none">France</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>spain" class="text-decoration-none">Spain</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>austria" class="text-decoration-none">Austria</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>greece" class="text-decoration-none">Greece</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>italy" class="text-decoration-none">Italy</a></li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h5 class="text-bold text-green">Asia</h5>
                                <ul class="list-unstyled col-count-country-2 ft-sm country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>singapore" class="text-decoration-none">Singapore</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>india" class="text-decoration-none">India</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>philippines" class="text-decoration-none">Philippines</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>malaysia" class="text-decoration-none">Malaysia</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>nepal" class="text-decoration-none">Nepal</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>pakistan" class="text-decoration-none">Pakistan</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>sri-lanka" class="text-decoration-none">Sri Lanka</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>hong-kong" class="text-decoration-none">Hong Kong</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>thailand" class="text-decoration-none">Thailand</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>maldives" class="text-decoration-none">Maldives</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>vietnam" class="text-decoration-none">Vietnam</a></li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h5 class="text-bold text-green">Middle East</h5>
                                <ul class="list-unstyled col-count-country-2 ft-sm country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>uae" class="text-decoration-none">United Arab Emirates</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>saudi-arabia" class="text-decoration-none">Saudi Arabia</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>jordan" class="text-decoration-none">Jordan</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>bahrain" class="text-decoration-none">Bahrain</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>qatar" class="text-decoration-none">Qatar</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>lebanon" class="text-decoration-none">Lebanon</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>kuwait" class="text-decoration-none">Kuwait</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>oman" class="text-decoration-none">Oman</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-5">
                                <h5 class="text-bold text-green">Africa</h5>
                                <ul class="list-unstyled col-count-country-2 country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>nigeria" class="text-decoration-none">Nigeria</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>south-africa" class="text-decoration-none">South Africa</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>angola" class="text-decoration-none">Angola</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>kenya" class="text-decoration-none">Kenya</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>ghana" class="text-decoration-none">Ghana</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>rwanda" class="text-decoration-none">Rwanda</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>egypt" class="text-decoration-none">Egypt</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>morocco" class="text-decoration-none">Morocco</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>algeria" class="text-decoration-none">Algeria</a></li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h5 class="text-bold text-green">The Americans</h5>
                                <ul class="list-unstyled col-count-country-2 ft-sm country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>usa" class="text-decoration-none">USA</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>mexico" class="text-decoration-none">Mexico</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>canada" class="text-decoration-none">Canada</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>brazil" class="text-decoration-none">Brazil</a></li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h5 class="text-bold text-green">Australia</h5>
                                <ul class="list-unstyled col-count-country-2 ft-sm country-list-view">
                                    <li><a href="<?php echo ROOT_DIR; ?>australia" class="text-decoration-none">Australia</a></li>
                                    <li><a href="<?php echo ROOT_DIR; ?>new-zealand" class="text-decoration-none">New Zealand</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>