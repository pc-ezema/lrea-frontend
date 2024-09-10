<?php 
    if (isset($_SESSION['location_of_choice_before'])) {
        $_SESSION['location_of_choice'] = $_SESSION['location_of_choice_before'];
        unset($_SESSION['location_of_choice_before']);
    }
?>