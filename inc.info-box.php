<div class="info-sect position-relative py-5" style="background-color: cornsilk;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <p class="text-green text-bold mb-1">Take the next step</p>
                <p class="card-title text-bold ft-big text-dark-2 mb-4">With LREA, take the next step in achieving your personal and professional objectives.</p>
                <p class="card-text text-dark-2 mb-3">Join now to receive personalized recommendations from the full LREA catalog.</p>

                <div class="row">
                    <div class="col-lg-4 mb-lg-0 mb-3">
                        <a class="btn btn-custom-size text-bold bg-green w-100 text-white btn-custom" href="<?php echo ROOT_DIR.$location_slug; ?>category/<?php if (isset($explore_category) && $explore_category) echo $explore_category; else echo 'short-courses'; ?>">Explore all courses</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="box-2 rounded-8 py-5 mt-3 position-relative text-white z-index-1 bg-main-3" style="background-image: url(<?php echo ROOT_DIR; ?>images/banner-cover-4.jpg);">
                    <!-- <div class="overlay-hero rounded-8"></div> -->
                    <div class="container-fluid position-relative z-index-1">
                        <div class="row align-items-center">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="subscribe-sect position-relative bg-dark-2 py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-white text-center mb-lg-0 mb-2">
                <div class="text-center">
                    <h2 class="text-bold text-white">Ready to accelerate your career? Apply now</h2>
                    <p class="">Stay up to date with the latest news, announcements, and articles.</p>
                    <div class="alert alert-success email-settings" style="display: none;">
                        <h5>Thanks, you've signed up!</h5>
                        <p>You'll receive our email newsletter with news about new courses and recommendations.</p>
                    </div>
                    <div class="d-flex align-items-center bg-white text-black border rounded-8 p-1 flex-grow-1">
                        <div class="form-floating w-100">
                            <input type="search" required class="form-control rounded-0 border-0" id="subscribeEmail" placeholder="Email address">
                            <label for="subscribeEmail">Enter your email address</label>
                        </div>
                        <button id="subscribeBtn" class="btn btn-custom-size-2 text-bold bg-color-3 text-white btn-custom rounded-8">Subscribe</button>
                    </div>
                    <div id="subMsg" style="background: #000000; display: none;" class="py-2 text-center"></div>
                </div>
                <hr />
                <div class="avatar-group mb-3">
                    <!-- avatar -->
                    <span class="avatar-circle-2 avatar-lg ">
                        <img alt="avatar" src="<?php echo ROOT_DIR; ?>images/avatar-1.jpg" class="object-fit-cover object-position-center w-100 h-100">
                    </span>
                    <!-- avatar -->
                    <span class="avatar-circle-2 avatar-lg ">
                        <img alt="avatar" src="<?php echo ROOT_DIR; ?>images/avatar-2.jpg" class="object-fit-cover object-position-center w-100 h-100">
                    </span>
                    <!-- avatar -->
                    <span class="avatar-circle-2 avatar-lg ">
                        <img alt="avatar" src="<?php echo ROOT_DIR; ?>images/avatar-3.jpg" class="object-fit-cover object-position-center w-100 h-100">
                    </span>
                    <!-- avatar -->
                    <span class="avatar-circle-2 avatar-lg ">
                        <img alt="avatar" src="<?php echo ROOT_DIR; ?>images/avatar-4.jpg" class="object-fit-cover object-position-center w-100 h-100">
                    </span>
                </div>
                <p>At LREA, career progress shows up when you do. We’re here to guide you from where you are to the workplaces you want to be.</p>

                <a href="<?php echo ROOT_DIR.$location_slug; ?>category/<?php if (isset($explore_category) && $explore_category) echo $explore_category; else echo 'short-courses'; ?>" class="text-green text-bold text-decoration-none">Explore courses <i data-feather="arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>