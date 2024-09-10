<!-- Alert -->
<?php if ($_SESSION['userfeedback']) { ?>
    <div>
        <?php if ($_SESSION['feedbacktype'] == 'error') { ?>
            <div class="alert alert-danger alert-dismissible fade show userfeedback" style="margin-top: 110px; z-index: 10000; margin-bottom: -70px;">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <span><?php echo $_SESSION['userfeedback']; ?></span>
            </div>
            <?php unset($_SESSION['userfeedback']); unset($_SESSION['feedbacktype']); // Always clear the alerts after showing them ?>
        <?php } elseif ($_SESSION['feedbacktype'] == 'success') { ?>
            <div class="alert alert-success alert-dismissible fade show userfeedback" style="margin-top: 110px; z-index: 10000; margin-bottom: -70px;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $_SESSION['userfeedback']; ?></span>
            </div>
            <?php unset($_SESSION['userfeedback']); unset($_SESSION['feedbacktype']); // Always clear the alerts after showing them ?>
        <?php } else { // In case the type of message is not set ?>
            <div class="alert alert-info alert-dismissible fade show userfeedback" style="margin-top: 110px; z-index: 10000; margin-bottom: -70px;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <span><?php echo $_SESSION['userfeedback']; ?></span>
            </div>
            <?php unset($_SESSION['userfeedback']); unset($_SESSION['feedbacktype']); // Always clear the alerts after showing them ?>
        <?php } ?>
    </div>
<?php } ?>  
<!-- /Alert -->
  
<?php if ($i_need_big_banner) { ?>
    <div class="i_nid_big_banr">
        <img src="<?php echo MEDIA_ROOT_DIR ?>/img/major-banner.png" style="margin:auto;display:block;width:100%" alt="Major Banner" />
    </div>
<?php } ?>