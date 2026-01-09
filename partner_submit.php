<?php
require_once('connections/config.php');
require_once('components/misc.inc.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role  = $_POST['role'];
    $name  = $_POST['name'];
    $phone = $_POST['number'];
    $email = $_POST['email'];

    $query_partner_Insert = "
        INSERT INTO partner_requests (
            role, name, phone, email, status
        ) VALUES (
            '" . sql_string($role) . "',
            '" . sql_string($name) . "',
            '" . sql_string($phone) . "',
            '" . sql_string($email) . "',
            'pending'
        )";

    mysqli_select_db($config, $database_config);
    $Insert = mysqli_query($config, $query_partner_Insert) or die(mysqli_error($config));
    $insertPartnerStatus = mysqli_affected_rows($config);

    if ($insertPartnerStatus > 0) {
        echo "success";
    } else {
        echo "error";
    }
    exit; // stop here, don’t output HTML
}
?>
