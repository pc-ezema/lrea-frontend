<?php 
    if (defined('ROOT_DIR_UK')) {
        $js_files_path = defined('ASSETS_ROOT_DIR') ? ASSETS_ROOT_DIR : ROOT_DIR_UK;
        $link_root_default = ROOT_DIR_UK;
        $link_root_path = ROOT_DIR_UK.$location_slug;
        $uk_ajax_include_path = ROOT_DIR; // Make sure to create an exact copy of the file in the other folder that then includes the target php file
    }
    else {
        $js_files_path = defined('ASSETS_ROOT_DIR') ? ASSETS_ROOT_DIR : ROOT_DIR;
        $link_root_default = ROOT_DIR;
        $link_root_path = ROOT_DIR.$location_slug;
        $uk_ajax_include_path = ROOT_DIR;
    }
?>

<?php if ($site_version == "online" && !defined('ROOT_DIR_UK')) { /* Place google analytics javascript code here */  ?>
    <?php $linkedin_partner_id = ''; ?>

    <script type="text/javascript">
        _linkedin_partner_id = "<?php echo $linkedin_partner_id; ?>";
        window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
        window._linkedin_data_partner_ids.push(_linkedin_partner_id);
    </script>
    <script type="text/javascript">
        (function () {
            var s = document.getElementsByTagName("script")[0];
            var b = document.createElement("script");
            b.type = "text/javascript"; b.async = true;
            b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
            s.parentNode.insertBefore(b, s);
        })();
    </script>
    <noscript>
        <img height="1" width="1" style="display:none;" alt=""
            src="https://dc.ads.linkedin.com/collect/?pid=<?php echo $linkedin_partner_id; ?>&fmt=gif" />
    </noscript>


    <!-- Google Tag Manager (noscript) -->
    <?php $gtm_id_for_this = ""; ?>
    <?php if ($gtm_id_for_this) { ?>
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtm_id_for_this; ?>" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
    <?php } ?>
    <!-- End Google Tag Manager (noscript) -->

    <!-- START Google Code for Dynamic Retargeting -->

    <?php $ecomm_target_id = ""; ?>
    <?php if ($ecomm_target_id) { ?>
        <script type="text/javascript">
            gtag('event', 'page_view', {
                'send_to': '<?php echo $ecomm_target_id; ?>',
                'ecomm_pagetype': 'home'
            });
        </script>
    <?php } ?>   
    <!-- END Google Code for Dynamic Retargeting -->


    <?php if (in_array($coresite_curr_country_name, array('United Kingdom'))) { ?>

    <?php } ?>
<?php } ?>

<?php if ($push_notifications_activated) { ?>
    <?php // push notification codes ?>
    <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-messaging.js"></script>
<?php } ?>

<script src="<?php echo $js_files_path; ?>js/jquery.js"></script>
<script src="<?php echo $js_files_path; ?>js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $js_files_path; ?>js/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
<script src="<?php echo $js_files_path; ?>OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>

<?php if ($thisIsTheMainWebsiteSegment) { ?>
    <script src="<?php echo $js_files_path; ?>js/script.js?v=<?php echo $fileCacheVersion ?>"></script>
<?php } ?>

<?php if ($enable_captcha_here && $use_captcha_on_this_page) { // Captcha ?>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        $("body").on("click", ".submit-dynamic-button", function(e) {
        var captchaClicked = false;

        if (grecaptcha && grecaptcha.getResponse().length > 0) { <?php // check if the captcha box was ticked on the individual programme page ?>
            captchaClicked = true;
        } 
        else { // captcha wasn't ticked
            $("#myCaptchaError").html("Prove you're not a robot by clicking on the captcha.");
            captchaClicked = false;

            setTimeout(function() { // clear the error message after 5 seconds
                $("#myCaptchaError").html('');
            }, 5000);
        }

        if (!captchaClicked) { // if captcha box is not ticked, do not submit the form
            e.preventDefault();
        }
        });
    </script>
<?php } ?>

<?php if ($useStudentNewStyling) { ?>
    <script>
        function initNoteTinyMce(){
            // Reload the Tinymce script for the notes side menu
            tinymce.init({
                selector: ".sidebarNotesModal textarea",
                theme: "modern",
                plugins: [
                        "advlist autolink lists charmap print preview hr anchor pagebreak",
                        "searchreplace visualblocks visualchars code",
                        "insertdatetime media nonbreaking directionality",
                        "emoticons template paste textcolor colorpicker textpattern"
                ],
                
                menubar: false,
                
                font_formats: 'Sans Serif=sans-serif;Arial=arial,sans-serif,helvetica;Courier New=courier new,courier,monospace',
                fontsize_formats: '6px 7px 8px 9px 10px 11px 12px 13px 14px 16px 18px 24px 36px',
                forced_root_block : false, // Use <br> instead of <p> for new lines
                
                toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify",
                relative_urls : false,
                remove_script_host : false
            });
        }
    </script>
<?php } ?>

<?php if ($push_notifications_activated) { ?>
    <?php // required for push notification ?>
    <link rel="manifest" href="<?php echo $link_root_default; ?>js/manifest.json">

    <script>
        <?php // Your web app's Firebase configuration ?>
        var firebaseConfig = {
            apiKey: "<?php echo PUSH_NOTIF_API_KEY; ?>",
            authDomain: "<?php echo PUSH_NOTIF_AUTH_DOMAIN; ?>",
            databaseURL: "<?php echo PUSH_NOTIF_DATABASE_URL; ?>",
            projectId: "<?php echo PUSH_NOTIF_PROJECT_ID; ?>",
            storageBucket: "<?php echo PUSH_NOTIF_STORAGE_BUCKET; ?>",
            messagingSenderId: "<?php echo PUSH_NOTIF_SENDER_ID; ?>",
            appId: "<?php echo PUSH_NOTIF_APP_ID; ?>"
        };
        <?php // Initialize Firebase ?>
        firebase.initializeApp(firebaseConfig);
        
        <?php // Retrieve Firebase Messaging object. ?>
        let messaging = firebase.messaging();
        
        messaging.usePublicVapidKey('<?php echo PUSH_NOTIF_PUBLIC_VAPID_KEY; ?>');
                
        messaging.requestPermission()
        .then (function() {
            <?php //console.log('Notification permission granted.'); ?>
            if ('serviceWorker' in navigator) { 
                navigator.serviceWorker.register('<?php echo $link_root_default; ?>js/firebase-messaging-sw.js')
                .then(function(registration) {
                    console.log("Service Worker Registered");
                    messaging.useServiceWorker(registration);  
                });
            }
            
            if (isTokenSentToServer()) { 
                console.log('Token already saved.');
            } else {
                getRegToken();
            }
        })
        .catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        });
        
        // [START refresh_token]
        // Callback fired if Instance ID token is updated.
        messaging.onTokenRefresh(() => {
            messaging.getToken().then((refreshedToken) => {
            console.log('Token refreshed.');
            // Indicate that the new Instance ID token has not yet been sent to the
            // app server.
            setTokenSentToServer(false);

            // update the token in the database
            if (window.localStorage.getItem('push_notif_token') !== null) {
                var row_to_update = window.localStorage.getItem('push_notif_token');
                    updateTokenInDb(row_to_update, refreshedToken);
                } 

                window.localStorage.setItem('push_notif_token', refreshedToken); // save the token in the user's browser.

                // Send Instance ID token to app server.
                sendTokenToServer(refreshedToken);
                // [END_EXCLUDE]
            }).catch((err) => {
                console.log('Unable to retrieve refreshed token ', err);
                showToken('Unable to retrieve refreshed token ', err);
            });
        });
        // [END refresh_token]

        function getRegToken(argument) {
            messaging.getToken()
            .then(function(currentToken) {
                if (currentToken) {
                    saveToken(currentToken);
                    console.log(currentToken);
                    setTokenSentToServer(true);
                } else {
                console.log('No Instance ID token available. Request permission to generate one.');
                    setTokenSentToServer(false);
                }
            })
            .catch(function(err) {
                console.log('An error occurred while retrieving token. ', err);
                setTokenSentToServer(false);
            });
        }
        
        function setTokenSentToServer(sent) {
            window.localStorage.setItem('sentToServer', sent ? 1 : 0);
        }

        function isTokenSentToServer() {
            return window.localStorage.getItem('sentToServer') == 1;
        }

        function saveToken(currentToken) {
            window.localStorage.setItem('push_notif_token', currentToken); // save the token in the user's browser.

            $.ajax({
                type: 'POST',					
                data: {
                    'token' : currentToken,
                    'action' : 'new'
                },
                url: '<?php echo ROOT_DIR; ?>push_notification_ajax.php',

            }).done(function(result){
                console.log(result);
            });
        }

        function updateTokenInDb(oldToken, newToken) {
            $.ajax({
                type: 'POST',					
                data: {
                    'old_Token' : oldToken,
                    'new_Token' : newToken,
                    'action' : 'update'
                },
                url: '<?php echo ROOT_DIR; ?>push_notification_ajax.php',

            }).done(function(result){
                console.log(result);
            });
        }
            
        function deleteToken() {
            messaging.getToken().then((currentToken) => {
                messaging.deleteToken(currentToken).then(() => {
                    console.log('Token deleted.');
                    setTokenSentToServer(false);
                }).catch((err) => {
                    console.log('Unable to delete token. ', err);
                });
                // [END delete_token]
            }).catch((err) => {
                console.log('Error retrieving Instance ID token. ', err);
                showToken('Error retrieving Instance ID token. ', err);
            });
        }

        messaging.onMessage(function(payload) {
            console.log("Message received. ", payload);
            notificationTitle = payload.notification.title;
            notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
                image:  payload.notification.image,
                url: payload.notification.click_action
            };
            var notification = new Notification(notificationTitle,notificationOptions);
        });			
    </script>
    <?php // End of Push notification code ?>
<?php } ?>

<script>
    function toggleShowPassword() {
        var x = document.getElementById("password2");
        if (x.type === "password") {
            x.type = "text";
            $(".password-eye").addClass("fa-eye-slash");
            $(".password-eye").removeClass("fa-eye");
        } 
        else {
            x.type = "password";
            $(".password-eye").addClass("fa-eye");
            $(".password-eye").removeClass("fa-eye-slash");
        }
    }
</script>

<script>
    /* Detect the platform the user is browsing from either Desktop or Mobile browser*/
    function isMobileDevice() {
        var is_mobile =false
        
        if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        // true for mobile device
        var is_mobile =true;
        return is_mobile;
        }else{
        // false for not mobile device
        return is_mobile;
        }
    };

    /* Function to set Cookie if the User is browing from desktop for persistent sidebar Only*/
    function js_setcookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }

    function js_getcookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    function js_endcookie(name) {
        setcookie(name,"",-1);
    }

    $('body').on('click', '.open-mega-menu', function(e) {
        e.preventDefault(); e.stopPropagation();

        courseName = $(this).attr('subject');

        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.querySelectorAll(".tab-nav-new");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.querySelectorAll(".tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        if(document.getElementById(courseName)){
            // Show the current tab, and add an "active" class to the button that opened the tab
            document.getElementById(courseName).style.display = "block";
        }

        if(document.querySelector(".default-nav-list")){
            document.querySelector(".default-nav-list").style.display = "none";
        }
        
        $(this).addClass('active');

        if (window.innerWidth < 1024) {
            document.querySelector(".main-default-list-link").style.display = "none"
        }
    });

    $("body").on("click", ".backToSubject", function(e) {
        e.stopPropagation();
        
        $(".main-default-list-link").css("display", "block")
        $(".default-nav-list").css("display", "block")
        $(".tab-nav-new").css("display", "none")
    });

    <?php if ($thisIsThePortalSegment) { ?>
        /* Set the width of the side navigation to 250px */
        function openNav(id) {
            document.getElementById(id).classList.add("sidebar-module-width");
        }

        /* Set the width of the side navigation to 0 */
        function closeNav(id) {
            document.getElementById(id).classList.remove("sidebar-module-width");
        }

        function openNote(id) {
            document.getElementById(id).classList.add("sidebar-module-width");
        }

        /* Set the width of the side Noteigation to 0 */
        function closeNote(id) {
            document.getElementById(id).classList.remove("sidebar-module-width");
        }

        function openChat() {
            document.getElementById("mysidenavmodules").classList.add("sidebar-module-width");
        }

        /* Set the width of the side navigation to 0 */
        function closeChat() {
            document.getElementById("mysidenavmodules").classList.remove("sidebar-module-width");
        }

        function sidebarCourse(){
            document.querySelector("#sidebar-course-display").classList.toggle("sidebar-content-width");
            document.querySelector("#content-main-video").classList.toggle("sidebar-main-content-margin-left");
        }

        function sidebarNote(){
            document.querySelector("#sidebar-note-display").classList.toggle("sidebar-content-width");
            document.querySelector("#content-main-video").classList.toggle("sidebar-main-content-margin-right");
        }

        let courseOutlineBtn = document.querySelector(".courseOutlineBtn")
        let noteOutlineBtn = document.querySelector(".noteOutlineBtn")
        let courseOutlinex = document.querySelector(".courseOutlinex")

        var is_mobile_platform = isMobileDevice();

        if (courseOutlineBtn){
            courseOutlineBtn.addEventListener("click", function () {
                if (courseOutlineBtn.textContent === "← Show Course Outline") {
                    courseOutlineBtn.textContent = "← Hide Course Outline";
                    
                    js_setcookie("persistent_course","opened", "1");
                } else {
                    courseOutlineBtn.textContent = "← Show Course Outline";
                    
                    js_setcookie("persistent_course","closed", "1");
                }
            });
        }

        var persistent_course_state = js_getcookie("persistent_course");

        if (courseOutlinex){
            courseOutlinex.addEventListener("click", function () {
                if (courseOutlineBtn.textContent === "← Show Course Outline") {
                    courseOutlineBtn.textContent = "← Hide Course Outline";
                } else {
                    courseOutlineBtn.textContent = "← Show Course Outline";
                }
            });
        }

        if (noteOutlineBtn){
            noteOutlineBtn.addEventListener("click", function () {
                if (noteOutlineBtn.textContent === "Show Notes") {
                    noteOutlineBtn.textContent = "Hide Notes";

                    js_setcookie("persistent_note","opened", "1");
                } else {
                    noteOutlineBtn.textContent = "Show Notes";

                    js_setcookie("persistent_note","closed", "1");
                }
            });
        }

        var persistent_note_state = js_getcookie("persistent_note");

        if (persistent_note_state == 'closed') {
            sidebarNote()

            noteOutlineBtn.textContent = "Show Notes";
        }


        if (window.innerWidth < 700 || persistent_course_state == 'closed') {
            if ($("#sidebar-course-display").hasClass("sidebar-content-width")) { 
                document.querySelector("#sidebar-course-display").classList.remove("sidebar-content-width");
            }

            if ($("#content-main-video").hasClass("sidebar-main-content-margin-left")) {
                document.querySelector("#content-main-video").classList.remove("sidebar-main-content-margin-left");
            }

            if (courseOutlineBtn) {
                courseOutlineBtn.textContent = "← Show Course Outline"
            }
        }

        $('body').on("click", ".delete_note", function(e){
            var delete_url = $(this).attr('href'), sidenote_parent = $('.js-notes-segment');

            if (confirm('Are you sure you want to delete this Note?')) {
                $.ajax({
                    url : delete_url,
                    method : "GET",
                    success:function(data) {
                        var replacementData = $(data).find('.js-notes-segment');

                        if (replacementData.length) {
                        sidenote_parent.html(replacementData.html());
                        }
                    }
                });
            }

            e.preventDefault();
        });

        $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
            }
            var $subMenu = $(this).next('.dropdown-menu');
            $subMenu.toggleClass('show');

            $(this).parents('.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show').removeClass('show');
            });

            return false;
        });

        $("body").on("click", ".like-click", function(e) {
            var post_liked = $(this).attr("post");
            var post_status = $(this).text().trim();
            var $ajaxURL = "";
            var currentLikeCount = $(this).closest(".post-like-segment").find(".number_of_likes").text();
            var likesAction, addLikesHTML;

            if (currentLikeCount) { <?php // if the count being displayed has a valid value ?>
                var likeValue = parseInt(currentLikeCount);
            }
            else { <?php // it has no value, we can either add its segment or ignore it where necessary ?>
                var likesHousingDiv = $(this).closest(".post-like-segment").find(".post-action-count");
            }

            if (post_status == "Liked") { <?php // if the action is to unlike a liked post ?>
                $(this).html("<i class='fa fa-thumbs-up'></i> Like");
                <?php if (!in_array($_SESSION['user_type'], array('student')) && $groupChatID) { ?>
                    $ajaxURL = "<?php echo $link_portal_path."admin/group-messages/".$groupChatID.'/'; ?>"+post_liked+"?action=unlike";
                <?php } else { ?>
                    $ajaxURL = "<?php echo $link_portal_path."student/discussions/"; ?>"+post_liked+"?action=unlike";
                <?php } ?>
                likesAction = 'remove';

                if (likeValue) { <?php // Must have a value greater than 1 to prevent zero or negative values ?>
                    likeValue = likeValue - 1;
                }
            }
            else if (post_status == "Like") {
                $(this).html("<i class='fa fa-thumbs-up text-primary'></i> Liked");
                <?php if (!in_array($_SESSION['user_type'], array('student'))) { ?>
                    $ajaxURL = "<?php echo $link_portal_path."admin/group-messages/".$groupChatID.'/'; ?>"+post_liked+"?action=like";
                <?php } else { ?>
                    $ajaxURL = "<?php echo $link_portal_path."student/discussions/"; ?>"+post_liked+"?action=like";
                <?php } ?>

                likesAction = 'add';

                if (likeValue) { <?php // Increase the likes count ?>
                    likeValue = likeValue + 1;
                }
            }
        
            if (likeValue) { <?php // If after action, there us still a value, then update the UI ?>
                $(this).closest(".post-like-segment").find(".number_of_likes").text(likeValue);
            }
            else {
                if (likesAction == 'add') { <?php // If a like was given to the post, then display new count to the user ?>
                addLikesHTML = '<div class="status-like-effect d-flex"><div class="mr-3 d-flex align-items-center"><div class="small-circle-icon-holder status-like mr-1"><i class="fa fa-thumbs-up text-white"></i></div> <span class="text-dark make-bold number_of_likes">1</span> </div></div>';
                }
                else { <?php // If a like was revoked, then remove the view from the dom ?>
                addLikesHTML = '';
                }

                $(this).closest(".post-like-segment").find(".post-action-count").html(addLikesHTML);
            }

            <?php // AJAX to then perform the like or unlike action ?>
            $.ajax({
                url : $ajaxURL,
                success:function(response) {
                
                }
            });
        });
        

        function showMoreLessTrigger() {
            $("div.more").each(function(){
                let maxheight = 100;
                let div_height = $(this).height();

                if (div_height > maxheight) {
                    $(this).css({'height': '100px', 'overflow': 'hidden'});
                    $(this).after(`<button class="expandcontent btn btn-default text-info btn-sm pt-2 pl-0" style="font-size:0.7rem;">+ Show More</button>`)
                }
            });
            
            $(".expandcontent").off().on('click', function(e){
                e.preventDefault();
                let $this = $(this).closest(".media-body").find(".more")
                $this.toggleClass("showmore");

                let replaceText = $this.hasClass("showmore") ? "- Show Less" : "+ Show More"
                $(this).text(replaceText);
            });
        }

        $("body").on("click", ".toggle-menu", function(e) {
            e.preventDefault();

            $(this).closest(".btn-group").find(".toggled-menu").toggleClass("show")
        });

        $("body").on("click", " button[action]", function(e) {
            e.preventDefault();
            
            var action_taken = $(this).attr("action");

            if (action_taken == 'edit') {
                window.location = $(this).attr("url");
            }
            else if (action_taken == 'delete') {
                if (confirm("Are you sure you want to delete this post?")) {
                    window.location = $(this).attr("url");
                }
            }
        });

        $("body").on("click", "button[reply_msg='true']", function(e) {
            var this_post = $(this).attr("post");
            var this_reply = $(this).attr("reply");
            var use_limit = $(this).attr("limit");

            $("form.submit-my-post").attr("action", "<?php echo ROOT_DIR."student/discussions/"; ?>"+this_post+"/"+this_reply);
            $("#networkModalLabel").text("Reply a Comment");
            $(".user-info-preview").hide();

            $("form.submit-my-post").attr("limit", "true");

            $("form.submit-my-post").attr("post", this_post);
            $("form.submit-my-post").attr("action_taken", "reply");
            $("button.submit_post_js").text("Reply");
        });

        $("body").on("click", "button[write-post='true']", function(e) {
            $("form.submit-my-post").attr("action", "<?php echo ROOT_DIR."student/discussions"; ?>");
            $("#networkModalLabel").text("Create a Post");
            $(".user-info-preview").show();

            $("form.submit-my-post").attr("post", "create");
            $("form.submit-my-post").removeAttr("limit");

            $("button.submit_post_js").text("Post");
            $("input.wp_submit_post").val("Post");
        });

        $("body").on("submit", "form[action_taken='reply']", function(e) {
            e.preventDefault();

            var thisPost = $(this).attr("post");
            var trigger_click = $(this).attr("trigger_click");
            var use_limit = $(this).attr("limit");
            var craftedURL = "<?php echo ROOT_DIR."student/discussion-ajax/"; ?>"+thisPost;

            if (use_limit || trigger_click) {
                craftedURL = "<?php echo ROOT_DIR."student/discussion-ajax/"; ?>"+thisPost+"?limit=true";
            }

            $("button.submit_post_js").append(" <i class='fa fa-spin fa-spinner'></i>");
            $("input[name='wp_submit_post']").val("Ajax-Reply");

            var formsubmit = $(this).serializeArray(), formUrl = $(this).attr("action");

            $.ajax({
                url : formUrl,
                method : "POST",
                data : formsubmit,
                success:function(response) {
                    if (trigger_click) {
                        document.getElementById('comment'+thisPost).click();
                    }

                    var $comment_count = $(".number_of_comments_js").text();
                    if ($comment_count) {
                        var comment_count_array = $comment_count.split(" ");
                        var comment_count_number = parseInt(comment_count_array[0]);
                        var newCommentCount = comment_count_number + 1;
                        $(".number_of_comments_js").text(newCommentCount+" Comments");
                    }

                    $("form[action_taken='reply']")[0].reset();
                    $("#reply-segment"+thisPost).html('<p class="modalwait text-center"><i class="fa fa-spinner fa-3x fa-spin"></i></p>').load(craftedURL+" .reply-segment", function(e){
                        if (!use_limit && !trigger_click) {
                            $("html, body").animate({ scrollTop: $("#reply-segment"+thisPost).height() }, 1000);
                        }
                    });
                    $("#networkModal").modal("hide");
                },
                error:function(e) {

                },
                complete:function(data) {
                    $("button.submit_post_js").remove("i");
                }
            });
        });
    <?php } ?>
</script>


<?php if ($use_page_preloader) { //enble preloader on student profile pages ?>  
    <script>
        var overlay = document.getElementById("overlay");

        if (overlay) {
            window.addEventListener("load", function(){
                overlay.classList.add("display-none");
            })
        }
    </script>
<?php } ?>

<?php // Facebook CAPI scripts
    $fb_event_code_display = 'below';
    include("inc.facebook-event-js-code-syntax.php"); 

    // TikTok Events
    include("inc.tiktok-event-js-code-syntax.php"); 
?>

<script>
    $(function() {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 70) {
                $(".pro-sticky-top.navigator .navbar-brand img").attr(
                    "src",
                    "<?php echo $link_root_default.$fileCacheVersion.'-'; ?>images/White-Logo-1.png"
                );
            }
            if ($(this).scrollTop() < 70) {
                $(".pro-sticky-top.navigator .navbar-brand img").attr(
                    "src",
                    "<?php echo $link_root_default.$fileCacheVersion.'-'; ?>images/White-Logo-1.svg"
                );
            }
        });

        $('.carousel-item', '.show-neighbors').each(function(){
            var next = $(this).next();

            if (! next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }).each(function(){
            var prev = $(this).prev();

            if (! prev.length) {
                prev = $(this).siblings(':last');
            }
            prev.children(':nth-last-child(2)').clone().prependTo($(this));
        });

        <?php if ($use_new_website_design) { ?>
            $(window).scroll(function() {
                if ($(this).scrollTop() > 70) {
                    $(".navbar-main-container .navbar-brand img").attr(
                        "src",
                        "<?php echo ROOT_DIR.$fileCacheVersion.'-'.'images/'.$navigation_menu_logo; ?>"
                    );
                }
                if ($(this).scrollTop() < 70) {
                    $(".navbar-main-container .navbar-brand img").attr(
                        "src",
                        "<?php echo ROOT_DIR.$fileCacheVersion.'-'.'images/'.$navigation_menu_logo; ?>"
                    );
                }
            });
        <?php } ?>

        // Fit text 
        /*$('.fitme').flowtype({
            minFont   : 12,
            maxFont   : 13,
            fontRatio: 30
        });*/

        // hide the subscription success message
        $(".email-settings").hide();

        // Newsletter subscription feature

        $("#loader").hide(); // hide the spinner

        $('body').on('click', '#subscribeBtn', function(e) {
            e.preventDefault(); e.stopPropagation();

            $('#subMsg').html('');

            const email = $('#subscribeEmail').val();

            if (email) {
                var data = {
                    email: email,
                    use_new: true
                };
                        
                $.ajax({
                    url: "<?php echo ROOT_DIR."subscribe-email-ajax.php"; ?>",
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    beforeSend: function() { // show spinner before attempting to submit the email 
                        $('#subscribeBtn').append(' <i class="fa fa-spinner fa-spin fa-lg"></i>');
                    },
                    success: function(data) {
                        if (data.status == "subscribed") {
                            $(".email-settings").hide();
                            $("#subMsg").html(data.message);
                            $('#subMsg').show();
                        }
                        else if (data.status == "failed") {
                            $("#subMsg").html(data.message);
                        $('#subMsg').show();
                        }
                        else if (data.status == 'invalid') { // Displays validation error messages.
                            $('#subMsg').html(data.message);   
                            $('#subMsg').show();         
                        } else {
                            $(".email-settings").show(); // show the subscription success message
                            $(".newsletter-form").hide(); // hide the newsletter form
                            $('#subMsg').hide();
                        }

                        $('#subscribeBtn').find('.fa-spinner.fa-spin').remove();
                    },
                    complete: function() { // hide spinner after the request.
                        $('#subscribeBtn').find('.fa-spinner.fa-spin').remove(); 
                    }
                });
            }
        });

        <?php if ($thisIsThePortalSegment) { ?>
            // Function for loading javascript files from cache
            jQuery.cachedScript = function( url, options ) {
                // Allow user to set any option except for dataType, cache, and url
                options = $.extend( options || {}, {
                    dataType: "script",
                    cache: true,
                    url: url
                });

                // Use $.ajax() since it is more flexible than $.getScript
                // Return the jqXHR object so we can chain callbacks
                return jQuery.ajax( options );
            };
        <?php } ?>

        <?php if (($sess_user_id < 1) || in_array($_SESSION['user_type'], array('student', 'applicant'))) { // Cart, Wishlist and Cookie Consent ?>
            // ===================== Cookie consent banner script ===============================
            const close_cookie = document.querySelector(".close-cookie");
            const privacy_banner = $("#cookieBanner");

            $("#acceptCookie").click(function() { // accept cookie button was clicked
                console.log('Cookie clicked');
                $.ajax({
                    url: "<?php echo ROOT_DIR."cookie-banner-ajax.php"; ?>",
                    method: "POST",
                    data: { cookieButton: "approveCookie" },
                    success: function (data) {
                        if (data == "ok") {
                            // console.log("Closed");
                            privacy_banner.hide();
                        }
                    }
                });
            });
            // ===================== End of Cookie consent banner script ===============================
        <?php } ?>

        // ===================== Add to cart feature ===============================================
        function load_cart_items_in_navbar() {
            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    fetch_item: 'fetch',
                    url_location: '<?php echo $location_slug; ?>'
                },
                success: function(data){
                    $('.cart-dropdown').html(data);
                }
            });
        }
        
        function send_facebook_add_to_cart_event() {
            <?php // Facebook AddToCart browser and server event.
                if ($enable_facebook_capi_events_tracking && $facebook_add_to_cart_js_amount) {
                    $facebook_pixel_unique_event_id = array();

                    foreach ($conversion_api_tokens_and_pixels as $key => $value) {
                        $facebook_pixel_unique_event_id[$key] = generate_facebook_event_id();

                        $fb_add_to_cart_browser_script = $fb_add_to_cart_server_script = $start_script_comment = $end_script_comment = "";

                        include('inc.facebook-capi-general-parameters.php'); // The variable $capi_additional_object is initialized in this file.

                        $capi_additional_object["value"] = $facebook_add_to_cart_js_amount;
                        $capi_additional_object["currency"] = $facebook_add_to_cart_js_currency;
                        $capi_additional_object["content_type"] = 'product';
                        $capi_additional_object["content_ids"] = array($facebook_add_to_cart_js_course_code);
                        
                        if ($enable_facebook_capi_events_tracking) { // Generate the browser and server events codes to output.
                            if (count($capi_additional_object)) {
                                $fb_capi_init_string = ", ".json_encode(iso88591_to_utf8($capi_additional_object));
                            }        

                            if ($site_version == "online") {
                                $fb_add_to_cart_browser_script = "fbq('init', '".$value['pixel']."');
                                                                fbq('track', 'AddToCart'".$fb_capi_init_string.", {eventID: '".$facebook_pixel_unique_event_id[$key]."'});";
                            }

                            $fb_add_to_cart_server_script = "$.ajax({
                                                                url: '".$link_root_default."capi/se',
                                                                method: 'POST',
                                                                data: {
                                                                en: 'AddToCart',
                                                                eid: '".$facebook_pixel_unique_event_id[$key]."',
                                                                eurl: '".$fb_evt_url."',
                                                                eip: '".$capi_ip_addr."',
                                                                eagent: '".$capi_user_agent."',
                                                                epage: '".addslashes($facebook_add_to_cart_js_prog_name)."',
                                                                eval: ".$facebook_add_to_cart_js_amount.",
                                                                ecur: '".$facebook_add_to_cart_js_currency."',
                                                                ecoid: ".json_encode(array($facebook_add_to_cart_js_course_code)).",
                                                                etype: 'product',
                                                                ".$capi_fbp."
                                                                ".$capi_email."
                                                                ".$capi_fbc."
                                                                }
                                                            });";

                            if ($debug_facebook_capi_script) { // For debug purpose.
                                $start_script_comment = "/*";
                                $end_script_comment = "*/";
                            }                                              
                        }
                    }

                    if (($fb_add_to_cart_browser_script || $fb_add_to_cart_server_script) && $enable_facebook_capi_events_tracking) {
                        echo $start_script_comment; // This is used to turn this segment to a comment for debug purpose
                            echo $fb_add_to_cart_browser_script.PHP_EOL;
                            echo $fb_add_to_cart_server_script.PHP_EOL;
                        echo $end_script_comment;
                    }
                }
            ?>
        }

        function send_tiktok_add_to_cart_event() {
            <?php // TikTok AddToCart API event.
                if ($enable_tiktok_events_tracking && $facebook_add_to_cart_js_amount) {
                    $tiktok_add_to_cart_api_event = $start_script_comment = $end_script_comment = "";

                    include('inc.tiktok-events-general-parameters.php');

                    $tiktok_contents[] = array(
                                        'price' => $facebook_add_to_cart_js_amount,
                                        'quantity' => 1,
                                        'content_type' => 'course',
                                        'content_id' => $facebook_add_to_cart_js_course_code,
                                        );

                    $tiktok_add_to_cart_api_event = "$.ajax({
                                                        url: '".$link_root_default."tiktok/se',
                                                        method: 'POST',
                                                        data: {
                                                        en: 'AddToCart',
                                                        eip: '".$tiktok_ip_addr."',
                                                        eagent: '".$tiktok_user_agent."',
                                                        eurl: '".$tiktok_evt_url."',
                                                        eval: ".$facebook_add_to_cart_js_amount.",
                                                        ecur: '".$facebook_add_to_cart_js_currency."',
                                                        cont: ".json_encode(iso88591_to_utf8($tiktok_contents)).",
                                                        ".$tiktok_clid."
                                                        ".$tiktok_email."
                                                        }
                                                    });";
                    
                    if ($debug_tiktok_script) { // For debug purpose.
                        $start_script_comment = "/*";
                        $end_script_comment = "*/";
                    }

                    if ($tiktok_add_to_cart_api_event) {
                        echo $start_script_comment; // This is used to turn this segment to a comment for debug purpose
                        echo $tiktok_add_to_cart_api_event.PHP_EOL;
                        echo $end_script_comment;
                    }
                }
            ?>
        }

        function load_cart_content() { <?php // this takes care of the contents on the cart page when an item is removed, it recalculate the total fees, and display appropriate items ?>
            $.ajax({
                url: "<?php echo $link_root_default."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    fetch_cart: 'on_page',
                    url_location: '<?php echo $location_slug; ?>'
                },
                success: function(data) {
                    $('.cart-content').html(data);

                    <?php if ($enable_prog_certificate_extra_fees && $execute_this_on_cart_page) { ?>
                        show_shipping_address_segment();
                    <?php } ?>
                }
            });
        }

        function get_cart_count() {
            $.ajax({
                url: "<?php echo $uk_ajax_include_path . 'add-to-cart-ajax.php'; ?>",
                method: "POST",
                dataType: "json",
                data: {
                    action: "count"
                },
                success: function (data) {
                    const count = parseInt(data.count, 10) || 0;
                    const $badge = $(".cart-badge");

                    if (!$badge.length) return; // safety

                    if (count > 0) {
                        $badge.text(count).show();
                    } else {
                        $badge.text(0).hide();
                    }
                },
                error: function () {
                    const fallback = parseInt($(".count-wish").first().text(), 10) || 0;
                    return fallback;
                }
            });
        }

        function loadMiniCart() {
            var currency = '<?php echo $_SESSION['coresite_curr_currency'] ?? PREFERRED_CURRENCY; ?>';

            $.ajax({
                url: '<?php echo $uk_ajax_include_path; ?>load-mini-cart.php', // adjust path as needed
                method: 'GET',
                success: function(html) {
                    // Replace the content of the container
                    $(".mini-cart-wrapper").html(html);
                    
                },
                error: function(xhr) {
                    console.error("Mini cart error:", xhr.status);
                }
            });
        }

        <?php // Function to load card item(s) into the modal ?>
        function loadCartItemInModal() {
            $.ajax({
                url : "<?php echo $uk_ajax_include_path."cart-full-information-details.php?display_cart_info=true&mycurr=".$coresite_curr_currency; ?>",
                method: "GET",
                success:function(response) {
                    <?php if (($sess_user_id > 0) && in_array($_SESSION['user_type'], array('student'))) { ?>
                        $("#incartModalScrollableTitle").html('<i class="fa fa-check-circle text-info"></i> Item added to cart');
                    <?php } ?>
                    $("#incartModalScrollableContent").html(response);
                },
                error: function(){
                    $("#incartModalScrollableTitle").html("");
                    $("#incartModalScrollableContent").html('<div class="text-center"><p class="make-bold"><i class="fa fa-times-circle fa-lg fa-3x text-danger"></i> <br/><br/> We could not complete your request. Please try again.</p></div>');
                },
                complete:function() {

                }
            });
        }

        $('body').on('click', '#visitURL', function(e) {
            var this_url = $(this).attr('href');
            window.location = this_url;
        });

        get_cart_count();

        <?php if (!isset($clients_can_enrol_for_this_course) || $clients_can_enrol_for_this_course) { ?>
            $('body').on('click', '.save-to-cart', function(e){ <?php // adds item to cart ?>
                e.preventDefault();
                let $this = $(this);
                let longitude = $(this).closest(".cart-info").attr("data-id");
                let addToCartCounter = parseInt($(".count-wish").first().text(), 10);
                var cartLoadingSegment = '<div class="text-center"><img src="<?php echo MEDIA_ROOT_DIR; ?>images/banners/loader.gif" class="img-fluid" width="20%" /> <p class="make-bold">Loading...</p></div>';

                $this.append(' <i class="fa fa-spinner fa-spin fa-lg"></i>');
                $("#incartModalScrollableContent").html(cartLoadingSegment);
                <?php if (($sess_user_id > 0) && in_array($_SESSION['user_type'], array('student'))) { ?>
                    $("#incartModalScrollableTitle").html('<i class="fa fa-spinner fa-spin"></i> Adding item to cart...');
                <?php } ?>

                <?php if ($execute_add_to_cart_dataHash && $site_version == "online" && $enable_data_layer_scripts && $gtag_pixel_ID) { // DataHash addToCart code (This variable is declared in the "inc.individual-prog-page-datalayer-script.php" file ?>
                    if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
                        dataLayer.push({
                            'event': 'addToCart',
                            'ecommerce': {
                                'currencyCode': '<?php echo $datalayer_prog_currency; ?>',
                                'add': {
                                    'products': [{
                                        'name': '<?php echo addslashes($datalayer_prog_name); ?>',
                                        'id': '<?php echo $datalayer_prog_code; ?>',
                                        'price': '<?php echo $datalayer_programme_fee; ?>',
                                        'brand': '<?php echo $datalayer_subjects; ?>',
                                        'category': '<?php echo ucwords(addslashes($datalayer_programme_type)); ?>',
                                        'variant': '<?php echo $datalayer_programme_level; ?>',
                                        'list': '<?php echo $dataHash_source; ?>',
                                        'position': 1,
                                        'dimension1': '<?php echo $datalayer_programme_duration; ?>',
                                        'dimension2': '<?php echo $datalayer_programme_format; ?>'
                                    }]
                                }
                            }
                        });
                    }
                <?php } ?>

                $.ajax({
                    url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                    method: "POST",
                    data: {
                        longitude : longitude,
                        url_location: '<?php echo $location_slug; ?>',
                        action: "addToCart"
                    },
                    success: function(data) {
                        if (data == "ok") {
                            $(".show-cart-button").css("cssText", "display: none !important;");
                            if (!$(".move-to-center").hasClass("col-lg-4")) {
                                $(".move-to-center").toggleClass("col-lg-4 mx-auto");
                            }
                            if ($this.hasClass("btn-purple")) {
                                $this.removeClass("btn-purple");
                                $this.addClass("btn-outline-purple");
                                $this.attr("disabled", "true");
                                $this.removeAttr('data-toggle');
                                $this.removeAttr('data-target');
                                $this.text("Added to Cart");
                            }
                            // $this.closest(".cart-info").find(".show-cart-button").html('<div class="course-b-sect mt-2 remove-from-cart"><button class="btn btn-outline-danger btn-block btn-sm lt-sp"><i class="fa fa-times"></i> Remove</button></div>');

                            get_cart_count();
                            // loadMiniCart();

                            load_cart_items_in_navbar(); <?php // call the function that populates the navbar with the cart items ?>
                            loadCartItemInModal();
                            send_facebook_add_to_cart_event(); <?php // Send facebook AddToCart Conversion API Event ?>
                            send_tiktok_add_to_cart_event(); <?php // Send TikTok AddToCart API Event ?>

                            if ($this.hasClass("move-wishlist-to-cart")) { <?php // Remove the item from the wishlist when the add to cart button on the wishlist dropdown is clicked. ?>
                            remove_from_wishlist(longitude);

                            <?php if ($execute_this_on_cart_page) { ?>
                                load_cart_content();
                            <?php } ?>
                            }

                            $this.find('.fa-spinner.fa-spin').remove();

                            if ($this.hasClass('one-click-enrol')) {
                                window.location = "<?php echo $link_root_path; ?>cart";
                            }

                            location.reload(); // force full refresh
                        }
                        else {
                            $this.find('.fa-spinner.fa-spin').remove(); 
                        }
                    },
                    error: function(){
                        $this.find('.fa-spinner.fa-spin').remove();
                    }
                });
            });
        <?php } ?>
        
        $('body').on('click', '.remove-from-page', function() { <?php // button that triggers item removal on cart page ?>
            let $this = $(this);
            let longitude = $(this).attr('id');
            let cartCounter = parseInt($(".count-wish").first().text(), 10);

            $this.append(' <i class="fa fa-spinner fa-spin fa-lg"></i>');

            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    longitude : longitude,
                    url_location: '<?php echo $location_slug; ?>',
                    action: "removeFromCart"
                },
                success: function(data) {
                    if (data == "ok") {
                        load_cart_content();
                        load_cart_items_in_navbar();
                        
                        get_cart_count();
                        // loadMiniCart();

                        const el = document.querySelector('.total-in-checkout');

                        if (el) {
                            el.dataset.progtotal = data.total_raw;
                            el.dataset.fbcur     = data.currency;
                            el.dataset.fbval     = data.total_value;
                            el.dataset.fbnum     = data.count;

                            el.textContent = data.symbol + data.total_value;
                        }

                        location.reload(); // force full refresh
                    }
                },
                error: function(){
                    $this.find('.fa-spinner.fa-spin').remove();
                }
            });
        });

        $('body').on('click', '.remove-dropdown-cart-item', function() { <?php // removes item from cart dropdown menu ?>
            let $this = $(this);
            let longitude = $(this).attr('id');
            let cartCounter = parseInt($(".count-wish").first().text(), 10);
            var cancel_button = $this.find('.fa');
            cancel_button.removeClass('fa-times');
            cancel_button.addClass('fa-spinner fa-spin fa-lg');

            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    longitude : longitude,
                    action: "removeFromCart"
                },
                success: function(data) {
                    if (data == "ok") {
                        load_cart_items_in_navbar(); <?php // call the function that populates the navbar with the cart items ?>
                        
                        <?php if ($execute_this_on_cart_page) { ?>
                            load_cart_content();
                        <?php } ?>
                        
                        get_cart_count();
                        // loadMiniCart();

                        if (longitude == $('.cart-info').attr('data-id')) { <?php // if the programme that was removed is the current one on the individual programme page, show the 'add to cart' button ?>
                            $('.show-cart-button').show();
                        }

                        $(".cart-info").each(function() {
                            if ($(this).attr('data-id') == longitude) { <?php // Change the display of the cart button on the programme box. ?>
                                var where_to_update = $(this).find('.purple-cart-btn');

                                if (where_to_update) {
                                    where_to_update.removeClass('btn-outline-purple');
                                    where_to_update.addClass('btn-purple');
                                    where_to_update.removeAttr('disabled');
                                    where_to_update.attr('data-toggle', 'modal');
                                    where_to_update.attr('data-target', '#incartModalScrollable');
                                    where_to_update.text('Add to Cart');
                                }
                            }
                        });

                        location.reload(); // force full refresh
                    }
                }
            });
        });

        $("body").on("click", ".clear-cart", function(e) {
            if (confirm("Are you sure you want to clear your cart?")) {
                e.preventDefault();
                $(this).append(" <i class='fa fa-spin fa-spinner'></i>");

                $.ajax({
                    url : "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php?action=clear_cart"; ?>",
                    method : "GET",
                    success:function(response) {
                        load_cart_content();
                        load_cart_items_in_navbar();
                        
                        get_cart_count();
                        // loadMiniCart();

                        location.reload(); // force full refresh
                    },
                    complete:function(e) {
                        $(this).remove("i");
                    }
                });
            }
        });

        $('body').on("click", "#discount_btn", function(e){ <?php // Apply coupon discount. ?>
            e.preventDefault();
            let $this = $(this);

            $this.append(' <i class="fa fa-spinner fa-spin fa-lg"></i>');

            let discount_code = $this.closest("div.coupon-apply-sect").find("#discount_code").val();

            $.ajax({
                method : "POST",
                url : "<?php echo ROOT_DIR."add-to-cart-ajax.php"; ?>",
                data: {
                    get_discount: discount_code,
                    url_location: '<?php echo $location_slug; ?>'
                },
                success: function(data){
                    //let replacementData = $(data).find('div.cart-content').closest('div.returned-cart-info');

                    load_cart_content();
                    $this.find('.fa-spinner.fa-spin').remove();
                },
                error: function(){
                    $this.find('.fa-spinner.fa-spin').remove();
                }
            });
        });

        <?php if ($execute_this_on_cart_page) { // Script for Cart page only. ?>
            $('body').on('click', '.checkout_button', function(e) { // Fire this event when the "Proceed to Checkout" button is clicked.
                e.preventDefault();

                var link = $(this).attr('href');
                var fb_cur = $(".total-in-checkout").attr('data-fbCur');
                var fb_val = $(".total-in-checkout").attr('data-fbVal');
                var fb_num = $(".total-in-checkout").attr('data-fbNum');        

                <?php if ($enable_prog_certificate_extra_fees) { ?>
                    var stop_execution = false;

                    if (check_all_shipping_form_fields()) { <?php // Submit the shipping address details or return appropriate errors ?>
                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: $(".shipping_address_form").serializeArray(),
                            dataType: 'json',
                            async: false,
                            success: function(data) {
                                <?php // Clear previous errors ?>
                                $("#validate_postal_code").html('');
                                $("#validate_city").html('');
                                $("#validate_country").html('');
                                $("#validate_address").html('');

                                if (data.field == 'postal_code') {
                                    $("#validate_postal_code").html(data.message);
                                    stop_execution = true;
                                    return;
                                }
                                else if (data.field == 'city') {
                                    $("#validate_city").html(data.message);
                                    stop_execution = true;
                                    return;
                                }
                                else if (data.field == 'country') {
                                    $('#validate_country').html(data.message); 
                                    stop_execution = true; 
                                    return;          
                                }
                                else if (data.field == 'address') {
                                    $('#validate_address').html(data.message); 
                                    stop_execution = true;
                                    return;           
                                }
                            }
                        });
                    }

                    if (stop_execution) {
                        return false;
                    }
                <?php } ?>

                <?php if (count($data_layer_programme_items) && $site_version == "online" && $enable_data_layer_scripts && $gtag_pixel_ID) { // DataHash Checkout 1 event. This denotes the start of the checkout process (i.e step 1) ?>
                    function onCheckout() {
                        if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
                            dataLayer.push({
                                'event': 'checkout',
                                'ecommerce': {
                                'currencyCode': '<?php echo $dataHash_checkout_currency; ?>',
                                'checkout': {
                                    'actionField': {'step': 1},
                                    'products': <?php echo json_encode(iso88591_to_utf8($data_layer_programme_items)); ?>
                                    }
                                }
                            });
                        }
                    }

                    onCheckout();
                <?php } ?>

                <?php // Redirect the user to the checkout page. ?>
                window.location = link; 
            });

            <?php if ($enable_prog_certificate_extra_fees) { ?>
                show_shipping_address_segment();
                check_all_shipping_form_fields();
                check_if_cert_printing_chosen();

                <?php if ($display_single_extra_fees_checkbox) { ?>
                    $("body").on("click", ".cert-extra-fees", function() {
                        var extra_fee = $(this).attr("data-certextrafee"),
                        total = $(".total-in-checkout").attr("data-progtotal"),
                        target = $(this).attr("data-certextrafeeid");

                        if ($(this).is(":checked")) { <?php // If extra fee checkbox is selected ?>
                            var new_total_fee = parseInt(total) + parseInt(extra_fee),
                            formatted_amount = new Intl.NumberFormat().format(new_total_fee); <?php // This was borrowed from https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat ?>
                            
                            $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                            $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                            $.ajax({
                                url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                                method: "POST",
                                data: {
                                longitude: target,
                                action: "addToCertExtraFees"
                                }
                            });
                        }
                        else { <?php // Checkbox is un-selected ?>
                            var new_total_fee = parseInt(total) - parseInt(extra_fee),
                            formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                                
                            $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                            $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                            $.ajax({
                                url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                                method: "POST",
                                data: {
                                    longitude: target,
                                    action: "removeFromCertExtraFees"
                                }
                            });
                        
                            <?php if ($enable_cert_attestation) { // If attestation is checked and the student unchecks the shipping checkbox, uncheck the attestation checkbox too. ?>
                                var attestation_checkbox = $(this).closest(".payment-row").find('.cert-attestation-fees');
                                if (attestation_checkbox.is(":checked")) {
                                attestation_checkbox.trigger('click');
                                }
                            <?php } ?>
                        }

                        check_if_cert_printing_chosen();
                        show_shipping_address_segment();
                    });
                <?php } else { // Different extra fee checkboxes for printing and shipping. ?>
                    $("body").on("click", ".cert_printing_fee", function() {
                        var extra_fee = $(this).attr("data-certfee"),
                        total = $(".total-in-checkout").attr("data-progtotal"),
                        shipping_fee = $(this).closest(".extra-fees-parent").find('.cert_shipping_fee'),
                        shipping_fee_value = shipping_fee.attr('data-certship'),
                        target = $(this).attr("data-certprintingid");

                        if ($(this).is(":checked")) {
                        <?php // Check if shipping fee has not been selected and select it. ?>
                        if (!shipping_fee.is(":checked")) {
                            shipping_fee.prop('checked', true);
                        }

                        var new_total_fee = parseInt(total) + parseInt(extra_fee) + parseInt(shipping_fee_value),
                        formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                            
                        $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                        $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: {
                            longitude: target,
                            action: "addToCertExtraFees"
                            }
                        });
                        }
                        else {
                            <?php // Check if shipping fee has been selected and un-select it. ?>
                            if (shipping_fee.is(":checked")) {
                                shipping_fee.prop('checked', false);
                            }

                            var new_total_fee = parseInt(total) - parseInt(extra_fee) - parseInt(shipping_fee_value),
                            formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                            
                            $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                            $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                            $.ajax({
                                url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                                method: "POST",
                                data: {
                                    longitude: target,
                                    action: "removeFromCertExtraFees"
                                }
                            });
                        }

                        show_shipping_address_segment();
                    });

                    $("body").on("click", ".cert_shipping_fee", function() {                        
                        var extra_fee = $(this).attr("data-certship"),
                        total = $(".total-in-checkout").attr("data-progtotal"),
                        printing_fee = $(this).closest(".extra-fees-parent").find('.cert_printing_fee'),
                        printing_fee_value = printing_fee.attr('data-certfee'),
                        target = $(this).attr("data-certshippingid");

                        if ($(this).is(":checked")) {  
                            <?php // Check if printing fee has not been selected and select it. ?>
                            if (!printing_fee.is(":checked")) {
                                printing_fee.prop('checked', true);
                            }

                            var new_total_fee = parseInt(total) + parseInt(extra_fee) + parseInt(printing_fee_value),
                            formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                                
                            $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                            $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                            $.ajax({
                                url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                                method: "POST",
                                data: {
                                longitude: target,
                                action: "addToCertExtraFees"
                                }
                            });
                        }
                        else {
                            <?php // Check if printing fee has been selected and un-select it. ?>
                            if (printing_fee.is(":checked")) {
                                printing_fee.prop('checked', false);
                            }

                            var new_total_fee = parseInt(total) - parseInt(extra_fee) - parseInt(printing_fee_value),
                            formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                                
                            $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                            $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                            $.ajax({
                                url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                                method: "POST",
                                data: {
                                longitude: target,
                                action: "removeFromCertExtraFees"
                                }
                            });
                        }

                        show_shipping_address_segment();
                    });
                <?php } ?>

                $("body").on("click", ".othm_cert", function() {
                    var extra_fee = $(this).attr("data-othmcertfee"),
                    total = $(".total-in-checkout").attr("data-progtotal"),
                    target = $(this).attr("data-othmcertid");

                    if ($(this).is(":checked")) { <?php // If extra fee checkbox is selected ?>
                        var new_total_fee = parseInt(total) + parseInt(extra_fee),
                        formatted_amount = new Intl.NumberFormat().format(new_total_fee); <?php // This was borrowed from https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat ?>
                        
                        $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                        $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: {
                                longitude: target,
                                action: "addToOTHMExtraFees"
                            }
                        });
                    }
                    else { <?php // Checkbox is un-selected ?>
                        var new_total_fee = parseInt(total) - parseInt(extra_fee),
                        formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                        
                        $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                        $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: {
                                longitude: target,
                                action: "removeFromOTHMExtraFees"
                            }
                        });
                    }   
                });

                function show_shipping_address_segment() {
                    $(".shipping_info_segment").addClass("d-none");

                    <?php if ($display_single_extra_fees_checkbox) { ?>
                        var extra_fee_checkboxes = $(".cert-extra-fees");

                        extra_fee_checkboxes.each(function() {
                            if ($(this).is(":checked")) { <?php // Show the shipping address segment if any of the extra fee checkboxes was checked ?>
                                $(".shipping_info_segment").removeClass("d-none");
                                return false; <?php // Break out of the loop ?>
                            }
                        });
                    <?php } else { // Different extra fee checkboxes for printing and shipping. ?>
                        var cert_printing_fee = $(".cert_printing_fee");

                        cert_printing_fee.each(function() {
                            if ($(this).is(":checked")) { <?php // Show the shipping address segment if any of the extra fee checkboxes was checked ?>
                                $(".shipping_info_segment").removeClass("d-none");
                                return false; 
                            }
                        });
                    <?php } ?>
                            
                    check_all_shipping_form_fields();
                }

                function check_all_shipping_form_fields() {
                    $(".disable_checkout").addClass("d-none");
                    $(".enable_checkout").removeClass("d-none");
                                
                    if (!$(".shipping_info_segment").hasClass('d-none')) { <?php // Only perform this check if the shipping info segment is visible. ?>
                        var shipping_city = $(".shipping_city").val(), 
                        shipping_postal_code = $(".shipping_postal_code").val(),
                        shipping_country = $(".shipping_country").val(),
                        shipping_address = $(".shipping_address").val();

                        if (shipping_city && shipping_postal_code && shipping_country && shipping_address) {
                            $(".disable_checkout").addClass("d-none");
                            $(".enable_checkout").removeClass("d-none");

                            return true;
                        }
                        else {
                            $(".disable_checkout").removeClass("d-none");
                            $(".enable_checkout").addClass("d-none");

                            return false;
                        }
                    }
                }

                function check_if_cert_printing_chosen() {
                    $(".cert-extra-fees").each(function() {
                        if ($(this).is(":checked")) {
                            $(this).closest(".payment-row").find(".cert-attestation-parent").removeClass("d-none");
                        }
                        else {
                            $(this).closest(".payment-row").find(".cert-attestation-parent").addClass("d-none");
                        }
                    });
                }

                $("body").on("blur, keyup, change", ".shipping_city, .shipping_postal_code, .shipping_country, .shipping_address", function() {
                    check_all_shipping_form_fields();
                });
            <?php } ?>

            <?php if ($enable_cert_attestation) { // Cert attestation segment ?>
                $("body").on("click", ".cert-attestation-fees", function() {
                    var attestation_fee = $(this).attr("data-certattestationfee"),
                    total = $(".total-in-checkout").attr("data-progtotal"),
                    target = $(this).attr("data-attestationfeeid");

                    if ($(this).is(":checked")) { <?php // If attestation fee checkbox is selected ?>
                        var new_total_fee = parseInt(total) + parseInt(attestation_fee),
                        formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                            
                        $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                        $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: {
                                longitude: target,
                                action: "addToCertAttestationFee"
                            }
                        });
                    }
                    else { <?php // Checkbox is un-selected ?>
                        var new_total_fee = parseInt(total) - parseInt(attestation_fee),
                        formatted_amount = new Intl.NumberFormat().format(new_total_fee);
                        
                        $(".total-in-checkout").attr('data-progtotal', new_total_fee);
                        $(".total-in-checkout").html('<?php echo $symbol_for_programme_currency; ?> '+formatted_amount);

                        $.ajax({
                            url: "<?php echo ROOT_DIR; ?>add-to-cart-ajax.php",
                            method: "POST",
                            data: {
                                longitude: target,
                                action: "removeFromCertAttestationFee"
                            }
                        });
                    }

                    enforce_attestation_option($(this));
                });

                function enforce_attestation_option(element) {
                    var attestation_checkbox = $(element).closest(".payment-row").find('.cert-attestation-fees'),
                    printing_checkbox = $(element).closest(".payment-row").find(".cert-extra-fees");

                    if (printing_checkbox.length) { <?php // If the course is a short course in which the shipping fee checkbox is available for.  ?>
                        if (attestation_checkbox.is(":checked")) { <?php // If the cert attestation checkbox is checked, make sure the shipping fee checkbox is checked as well. ?>
                            if (!printing_checkbox.is(":checked")) {
                                printing_checkbox.trigger('click');
                            }
                        }
                    }
                }
            <?php } ?>
        <?php } ?>

        //  ==================== End of Add to cart feature ========================================

        // ===================== Wishlist feature ===============================================
        get_wishlist_count();

        function load_wishlist_in_navbar() {
            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php?fetch_Wishlist=In_Dropdown"; ?>",
                method: "GET",
                success: function(data) {
                    $('.wishlist-dropdown').html(data);
                }
            });
        }

        function get_wishlist_count() {
            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                dataType: "json",
                data: {
                    action: "countWishlist"
                },
                success: function(data) {
                    if (data.count > 0) {
                        $(".wishlist-count").show();
                        $(".wishlist-count").html(data.count);
                    }
                    else {
                        $(".wishlist-count").hide();
                        $(".wishlist-count").html('0');
                    }
                },
                error: function(){
                    return parseInt($(".wishlist-count").first().text(), 10);
                }
            });
        }

        function remove_from_wishlist(target) {
            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    longitude : target,
                    action: "removeFromWishlist"
                },
                success: function(data) {
                    if (data == "ok") {
                        load_wishlist_in_navbar();
                        get_wishlist_count();

                        $(".wishlist-action-btn").each(function() {
                            if ($(this).attr('data-wishlistTarget') == target) { <?php // Change the icon of the wishlist on the programme box. ?>
                                var where_to_update = $(this).find('.wishlist-effect');
                                where_to_update.removeClass('fa-heart text-danger');
                                where_to_update.addClass('fa-heart-o');
                            }
                        });

                        <?php if ($display_this_on_wishlist_page) { // This variable is declared on the wishlist page ?>
                            load_wishlist_page_content();
                        <?php } ?>
                    }
                }
            });
        }

        function load_wishlist_page_content() {
            $.ajax({
                url: "<?php echo $link_root_default."add-to-cart-ajax.php?fetch_Wishlist=on_page"; ?>",
                method: "GET",
                success: function(data) {
                    $('.wishlist-page-content').html(data);
                }
            });
        }

        $('body').on('click', '.wishlist-action-btn', function(e){ <?php // Add or remove item from the wishlist based on which icon was clicked on the programme boxes. ?>
            var current_effect_used = $(this).find('.wishlist-effect'),
            loader = $(this).find('.wishlist-loader'),
            longitude = $(this).attr('data-wishlistTarget'),
            action_to_perform = "",
            fb_currency = $(this).attr('data-currency'),
            fb_name = $(this).attr('data-name'),
            fb_contents = $(this).attr('data-ids'),
            fb_value = $(this).attr('data-value');

            current_effect_used.hide();
            loader.show();

            <?php // Determine which action the user is trying to perform ?>
            if (current_effect_used.hasClass('fa-heart-o')) { <?php // Add to wishlist ?>
                action_to_perform = "addToWishlist";
            }
            else if (current_effect_used.hasClass('fa-heart')) { <?php // Remove from wishlist ?>
                action_to_perform = "removeFromWishlist";
            }

            if (action_to_perform) {
                $.ajax({
                    url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                    method: "POST",
                    data: {
                        longitude : longitude,
                        url_location: '<?php echo $location_slug; ?>',
                        action: action_to_perform
                    },
                    success: function(data) {
                        if (data == "ok") {  
                            get_wishlist_count();              
                            load_wishlist_in_navbar();

                            if (action_to_perform == "addToWishlist") { <?php // Change the wishlist icon of the programme to show it's been added to wishlist. ?> 
                                current_effect_used.removeClass('fa-heart-o');
                                current_effect_used.addClass('fa-heart text-danger');

                                <?php if ($enable_facebook_capi_events_tracking && $is_allowed_to_track_fb_add_to_wishlist_event) { // Facebook AddToWishlist events ?>
                                $("#fb_wishlist").html('');
                                
                                $.ajax({
                                    url: "<?php echo $link_root_default."capi/se"; ?>",
                                    method: "POST",
                                    data: {
                                        curr: fb_currency,
                                        name: fb_name,
                                        coids: fb_contents,
                                        value: fb_value,
                                        action: "prepare_add_to_wishlist"
                                    },
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.status == 200) {
                                            <?php if ($debug_facebook_capi_script) { // For debug purpose. ?>
                                            $("#fb_wishlist").html("<!--" + data.result + "-->");
                                            <?php } else { ?>
                                            $("#fb_wishlist").html(data.result);
                                            <?php } ?>
                                        }
                                    }
                                });
                                <?php } ?>
                            }
                            else if (action_to_perform == "removeFromWishlist") { <?php // Change the icon to show it's been removed from wishlist. ?>
                                current_effect_used.removeClass('fa-heart text-danger');
                                current_effect_used.addClass('fa-heart-o');
                            }

                            loader.hide();
                            current_effect_used.show();
                        } 
                    },
                    error: function(){
                        loader.hide();
                    }
                });
            }
        });

        $('body').on('click', '.remove-dropdown-wishlist', function() { <?php // Remove item from wishlist dropdown menu ?>
            var longitude = $(this).attr('id');
            var cancel_button = $(this).find('.fa');
            cancel_button.removeClass('fa-times');
            cancel_button.addClass('fa-spinner fa-spin fa-lg');

            remove_from_wishlist(longitude);
        });

        $('body').on('click', '.remove-wishlist-on-page', function() { <?php // Remove wishlist item on wishlist page ?>
            var $this = $(this)
            longitude = $this.attr('data-wishlistTarget');
            loader = $this.find('.wishlist-loader')
            cancel_icon = $this.find('.fa-times');

            loader.show();
            cancel_icon.hide();

            $.ajax({
                url: "<?php echo $uk_ajax_include_path."add-to-cart-ajax.php"; ?>",
                method: "POST",
                data: {
                    longitude : longitude,
                    action: "removeFromWishlist"
                },
                success: function(data) {
                    if (data == "ok") {
                        load_wishlist_in_navbar();
                        get_wishlist_count();

                        <?php if ($display_this_on_wishlist_page) { ?>
                        load_wishlist_page_content();
                        <?php } ?>
                    }
                },
                error: function(){
                    loader.hide();
                    cancel_icon.show();
                }
            });
        });
        //  ==================== End of Add to wishlist feature ========================================

        <?php if ($enable_data_layer_scripts && $site_version == "online" && $gtag_pixel_ID) { ?>
            // ===================== Data layer productClick event ==========================================
            $("body").on("click", ".data-layer-click", function(e) { <?php // Datalayer productClick event (When a student clicks on a programme to view its full details.) ?>
                e.preventDefault();

                var $this = $(this), 
                target = $(this).attr('data-layerTarget'), 
                target_link = $(this).attr('href'), 
                curr = $(this).attr('data-layerCurr'),
                datalayerProductClickItems = window['datalayer_param_'+target];
                
                if (typeof dataLayer !== 'undefined') { // Javascript Level: If dataLayer is defined
                    dataLayer.push({
                        'event': 'productClick',
                        'ecommerce': {
                            'currencyCode': curr,
                            'click': {
                            'actionField': {'list': 'Home Page'},
                            'products': datalayerProductClickItems
                            }
                        }
                    });
                }

                window.location = target_link;
            });
            // ===================== End of Data layer productClick event ===================================
        <?php } ?>

        <?php if ($thisIsThePortalSegment) { ?>
            $('body').on('click', '.close-payment-notification', function(e){
                var $this = $(this);
                var notification_id = $this.attr('id');

                $.ajax({
                    url : "<?php echo ROOT_DIR."student/dashboard"; ?>",
                    method : "GET",
                    data : {
                        key : notification_id,
                        ajax : true
                    }
                });

            });

            $('body').on('click', '.close-pending-document-notification', function(e){
                $.ajax({
                    url : "<?php echo ROOT_DIR."student/dashboard"; ?>",
                    method : "GET",
                    data : {
                        what : 'pending-docs',
                        ajax : true
                    }
                });
            });

            <?php if (isset($_SESSION['set_cookie_for_pending_docs'])) { ?>
                js_setcookie('portalpendingdocs', 'true', '1');

                <?php unset($_SESSION['set_cookie_for_pending_docs']); ?>
            <?php } ?>

            $("body").on("click", ".confirm_accept", function(e){
                if (!confirm("Are you sure you want to accept this request?")) e.preventDefault();
            });
            
            $("body").on("click", ".confirm_decline", function(e){
                if (!confirm("Are you sure you want to decline this request?")) e.preventDefault();
            });
            
            $("body").on("click", ".confirm_add", function(e){
                if (!confirm("Are you sure you want to add this user to your network?")) e.preventDefault();
            });
            
            $('body').on('click', '#allMsgs', function(e){ 
                if ($(this).is(":checked")) { // check all selected programme boxes
                    $('.mail_action').each(function(){
                        $(this).prop('checked', true);
                    });
                }
                else if($(this).is(":not(:checked)")) { // uncheck all selected programme boxes 
                    $('.mail_action').each(function(){
                        $(this).prop('checked', false);
                    });
                }
            });

            $('body').on('click', '.mail_action', function(e){
                if ($('#allMsgs').is(':checked')) {
                    $('#allMsgs').prop('checked', false);
                }
            });

            $('body').on('click', 'button[name="wp_bulk_delete"]', function(e){
                if($('form#bulk_action input:checked').length > 0) {
                    if (!confirm("Are you sure you want to permanently delete the selected message(s)?")) e.preventDefault();
                }
            });

            $('body').on('submit', 'form#bulk_action', function(e){
                if($('form#bulk_action input:checked').length < 1) {
                    e.preventDefault();
                }
            });
        <?php } ?>

        <?php if ($thisIsTheMainWebsiteSegment) { ?>
            $("body").on("click", "a[full-testimonial]", function(e){
                var testimonialContent = $(this).attr("full-testimonial"),
                testimonialImage = $(this).closest(".user-testify").find(".review-perfect-image").attr("src"),
                testimonialFullName = $(this).closest(".user-testify").find(".student-rating-name").text(),
                testimonialCountryName = $(this).closest(".user-testify").find(".student-rating-country").text(),
                testimonialRating = $(this).closest(".user-testify").find(".student-rating-score").attr("src");

                $(".testimonial-modal-message").html(testimonialContent);
                $(".testimonial-modal-image").attr("src", testimonialImage);
                $(".testimonial-modal-name").text(testimonialFullName);
                $(".testimonial-modal-country").text(testimonialCountryName);
                $(".testimonial-modal-rating").attr("src", testimonialRating);
            });

            $("body").on("click", ".confirm_remove_coupon", function(e){
                if (!confirm("Are you sure you want to remove this coupon?"))  {
                    e.preventDefault();
                }
                else {
                    $(this).append(" <i class='fa fa-spin fa-spinner'></i>");
                }
            });

            <?php if (isset($deactivate_short_courses_category_page_subject_js) && !$deactivate_short_courses_category_page_subject_js) { ?>
                $("body").on("click", ".prog-subject-filter", function(e) {
                    let $this = $(this);
                    let div_to_be_active = $this.attr('id');
                    let active_subject = $('.current-filter');
                    let currently_active_div_id = active_subject.attr('id');

                    if (!$this.hasClass('current-filter')) {
                        active_subject.removeClass('text-danger');
                        active_subject.removeClass('make-bold');
                        active_subject.removeClass('current-filter');

                        $this.addClass("text-danger");
                        $this.addClass("make-bold");
                        $this.addClass("current-filter");

                        $("div[targetfilter='"+currently_active_div_id+"']").addClass('d-none');
                        $("div[targetfilter='"+div_to_be_active+"']").removeClass('d-none');
                    }
                });
            
                <?php if (isset($_GET['subject']) && ($_GET['subject'])) { // Automatically scroll to the allCourses segment on the PCC and APCC pages if a query string of subject is present. ?>
                    window.onload = function() {
                        let queryString = location.search;
                        let key = queryString.substring(queryString.indexOf("?") + 1).split("=")[0];

                        let element = document.getElementById('allCourses');
                        let headerOffset = 80; // navbar.offsetHeight (adjust this value if you have a fixed header)

                        if (key == 'subject') {
                        let elementPosition = element.getBoundingClientRect().top;
                        let offsetPosition = elementPosition - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth"
                        });
                        }
                    }
                <?php } ?>
            <?php } ?>

            function showInstalment1(){
                if(document.querySelector(".instalments-3")){
                    document.querySelector(".instalments-3").style.display = "none";
                }
                if(document.querySelector(".instalments-6")){
                    document.querySelector(".instalments-6").style.display = "none";
                }
                if(document.querySelector(".instalments-1")){
                    document.querySelector(".instalments-1").style.display = "block"; 
                }
                if(document.querySelector(".instalments-6-4")){
                    document.querySelector(".instalments-6-4").style.display ='none';  
                }
                if(document.querySelector(".instalments-3-4")){
                    document.querySelector(".instalments-3-4").style.display ='none';
                } 
            }

            function showInstalment3(){
                if(document.querySelector(".instalments-3")){
                    document.querySelector(".instalments-3").style.display ='table';
                }
                if(document.querySelector(".instalments-6")){
                    document.querySelector(".instalments-6").style.display ='none';
                }
                if(document.querySelector(".instalments-3-2")){
                    document.querySelector(".instalments-3-2").style.display ='table';
                }
                if(document.querySelector(".instalments-6-2")){
                    document.querySelector(".instalments-6-2").style.display ='none'; 
                }
                if(document.querySelector(".instalments-3-3")){
                    document.querySelector(".instalments-3-3").style.display ='inline-block';
                }
                if(document.querySelector(".instalments-3-4")){
                    document.querySelector(".instalments-3-4").style.display ='block';
                }
                if(document.querySelector(".instalments-6-3")){
                    document.querySelector(".instalments-6-3").style.display ='none';  
                } 
                if(document.querySelector(".instalments-6-4")){
                    document.querySelector(".instalments-6-4").style.display ='none';  
                } 
                document.querySelector(".instalments-1").style.display = "none"; 
            }

            $('body').on('click', '#third__instalment', function(e) {
                showInstalment1();
            });

            $('body').on('click', '#first__instalment', function(e) {
                showInstalment3();
                $('.instalment-table-trigger.active').trigger('click');
            });

            $('body').on('click', '.instalment-table-trigger', function() {
                var total_amount = $(this).attr('data-full-pay');
                var num_of_instalments = $(this).attr('data-num_of_instalments');
                var amount_per_instalment = $(this).attr('data-amount_per_instalment');
                var num_of_months_between_instalments = $(this).attr('data-num_of_months_between_instalments');
                var target_container_id = $(this).attr('data-target_container_id');

                if (total_amount) {
                    displayInstalmentsTable(num_of_instalments, amount_per_instalment, num_of_months_between_instalments, total_amount, target_container_id);
                }
            });

            function displayInstalmentsTable(num_of_instalments, amount_per_instalment, num_of_months_between_instalments, total_amount, target_container_id) {
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

            $(".next-btn-click-1").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-2"]').tab('show')
            })

            $(".next-btn-click-2").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-3"]').tab('show')
            })

            $(".next-btn-click-3").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-4"]').tab('show')
            })

            $(".next-btn-click-4").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-5"]').tab('show')
            })

            $(".next-btn-click-5").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-6"]').tab('show')
            })

            $(".next-btn-click-6").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-7"]').tab('show')
            })

            $(".prev-btn-click-2").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-1"]').tab('show')
            })

            $(".prev-btn-click-3").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-2"]').tab('show')
            })

            $(".prev-btn-click-4").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-3"]').tab('show')
            })

            $(".prev-btn-click-5").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-4"]').tab('show')
            })

            $(".prev-btn-click-6").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-5"]').tab('show')
            })

            $(".prev-btn-click-7").click(function(e){
                e.preventDefault()
                $('.tryit button[data-target="#pills-6"]').tab('show')
            })
        <?php } ?>

        //Get the button
        var mybutton = document.getElementById("backtotop");

        function scrollFunction() {
            if (mybutton !== null) {
                if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
                    mybutton.style.display = "block";
                } else {
                    mybutton.style.display = "none";
                }
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }

        const sections = document.querySelectorAll(".added-section");
        const navLi = document.querySelectorAll(".tab-scroll-link");

        function scrollIndividualProg() {
            if (sections.length > 0 && navLi.length > 0) {
                var current = "";

                sections.forEach(function(section) {
                    const sectionTop = section.offsetTop;
                    if (pageYOffset >= sectionTop - 200) {
                        current = section.getAttribute("id"); 
                    }
                });

                navLi.forEach(function(li) {
                    li.classList.remove("active");
                    if (li.classList.contains(current)) {
                        li.classList.add("active");
                    }
                });
            }
        }

        // Handle scroll events
        window.addEventListener('scroll', function() {
            scrollFunction();
            scrollIndividualProg();
        });

        <?php if ($useStudentNewStyling) { // Sidebar toggle scripts on portal ?>
            var sideviewToggleBtn = document.querySelector(".sideviewToggleBtn")
            var sideviewTogglex = document.querySelector(".sideviewTogglex")

            function openSidebarMenu(){
                document.querySelector(".sidebar-profile-sect").classList.toggle("sidebar-width-display");
                document.querySelector(".content-profile-sect").classList.toggle("sidebar-main-margin-left");
            }

            function closeSidebarMenu() {
                document.querySelector(".sidebar-profile-sect").classList.toggle("sidebar-width-display");
                document.querySelector(".content-profile-sect").classList.toggle("sidebar-main-margin-left");
            }


            if(sideviewToggleBtn){
                sideviewToggleBtn.addEventListener("click", function () {
                    if (sideviewToggleBtn.textContent === "← Show side-menu") {
                        sideviewToggleBtn.textContent = "← Hide side-menu";
                        
                        js_setcookie("persistent_sidebar","opened", "1");
                    } else {
                        sideviewToggleBtn.textContent = "← Show side-menu";
                        
                        js_setcookie("persistent_sidebar","closed", "1");
                    }
                });
            }

            var persistent_sidebar_state = js_getcookie("persistent_sidebar");

            if (sideviewTogglex) {
                sideviewTogglex.addEventListener("click", function () {
                    if (sideviewToggleBtn.textContent === "← Hide side-menu") {
                        sideviewToggleBtn.textContent = "← Show side-menu";
                    } else {
                        sideviewToggleBtn.textContent = "← Hide side-menu";
                    }
                });
            }

            if (window.innerWidth < 700 || persistent_sidebar_state == 'closed') {
                if ($(".sidebar-profile-sect").hasClass("sidebar-width-display")) { 
                    document.querySelector(".sidebar-profile-sect").classList.remove("sidebar-width-display");
                }

                if ($(".content-profile-sect").hasClass("sidebar-main-margin-left")) {
                    document.querySelector(".content-profile-sect").classList.remove("sidebar-main-margin-left");
                }

                if (sideviewToggleBtn) {
                    sideviewToggleBtn.textContent = "← Show side-menu";
                }
            }
        <?php } ?>

        <?php // Share course page scroll script 
            // When user scrolls down on a course page the price of the course becomes sticky on the navbar.
        ?>
        $('[data-toggle="tooltip"]').tooltip();

        $("body").on("click", "#share_btn", function(e){
            $("div[data-network]").removeAttr("style");
        });

        var js_course_nav = document.querySelector(".js_course_nav");
        var js_course_nav_2 = document.querySelector(".js_course_nav_2");

        var myScrollFunc = function() {
            if (js_course_nav) {          
                <?php /* Ugochukwu's page scroll function (This uses pixels). ?>
                    var y = window.scrollY;

                    if (y >= 1200) {
                        js_course_nav.classList.remove("d-none")
                    } 
                    else {
                        js_course_nav.classList.add("d-none")
                    }
                <?php }*/ ?>
                
                <?php /* New scrolling script that uses percentage instead of pixels because of various devices. */ ?>
                var scrollTop = window.scrollY, docHeight = document.body.offsetHeight, winHeight = window.innerHeight;
                var scrollPercent = scrollTop / (docHeight - winHeight);
                var scrollPercentRounded = Math.round(scrollPercent * 100);
                
                if (scrollPercentRounded >= 23) { <?php // When the scrolling reaches 23% of the document height ?>
                    js_course_nav.classList.remove("d-none");
                }
                else {
                    js_course_nav.classList.add("d-none");
                }
            }
        };

        var myScrollFunc_2 = function() {
            var y = window.scrollY;
            
            if(js_course_nav_2){
                if (y >= 650) {
                    js_course_nav_2.classList.remove("d-none")
                } else {
                    js_course_nav_2.classList.add("d-none")
                }
            }
        };

        window.addEventListener("scroll", myScrollFunc);
        window.addEventListener("scroll", myScrollFunc_2);
        <?php // End of course page scroll script ?>
        
        <?php if (defined("DISABLE_WEB_PARAMS") || defined("DISABLE_WEB_FOOTER")) { ?>
            $(function() {
                $(window).on('beforeunload', function(e){
                    $("#splash").fadeIn();

                    setTimeout(function() {
                    $("#splash").fadeOut();
                    }, 5000);
                });

                $(window).on("load", function() {
                    $("#splash").fadeOut();
                });
            });
        <?php } ?>

        <?php if ($allow_multiple_of_each_course_at_checkout) { ?>
            $('body').on("click", ".input-group-append", function(e){
                let $this = $(this);
                let formValue = $this.closest(".input-group").find("input.input-number");

                formValue.val(parseInt(formValue.val()) + 1);
            });

            $('body').on("click", ".input-group-prepend", function(e){
                let $this = $(this);
                let formValue = $this.closest(".input-group").find("input.input-number");

                formValue.val(parseInt(formValue.val()) - 1);
            });
        <?php } ?>
    });
</script>

<script><?php // For lazy loading images. Gotten from https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video ?>
    document.addEventListener("DOMContentLoaded", function() {
        let lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
        let active = false;

        const lazyLoad = function() {
        if (active === false) {
            active = true;

            setTimeout(function() {
            lazyImages.forEach(function(lazyImage) {
                if ((lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0) && getComputedStyle(lazyImage).display !== "none") {
                lazyImage.src = lazyImage.dataset.src;
                lazyImage.srcset = lazyImage.dataset.srcset;
                lazyImage.classList.remove("lazy");

                lazyImages = lazyImages.filter(function(image) {
                    return image !== lazyImage;
                });

                if (lazyImages.length === 0) {
                    document.removeEventListener("scroll", lazyLoad);
                    window.removeEventListener("resize", lazyLoad);
                    window.removeEventListener("orientationchange", lazyLoad);
                }
                }
            });

            active = false;
            }, 200);
        }
        };

        document.addEventListener("scroll", lazyLoad);
        window.addEventListener("resize", lazyLoad);
        window.addEventListener("orientationchange", lazyLoad);
    });

    $('body').on('submit', '.append-loader', function(e){ <?php // append loader on registration/login form submit ?>
        let target = $('button.append-loader-btn');
        target.append(' <i class="fa fa-spinner fa-spin fa-lg"></i>');
    });
</script>

<script>function getRoot(){return '<?php echo $link_root_default ?>';} function loggedIn(){return <?php if ($sess_user_id > 0) echo 'true'; else echo 'false'; ?>;} function getCurrPage(){return '<?php echo curPageURL() ?>';}</script>