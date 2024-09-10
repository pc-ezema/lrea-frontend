<?php 
	/*require_once('../connections/config.php');
	require_once('../components/misc.inc.php');

    if (preg_match('/\.css$/i', $_GET['filename'])) {
        header("Content-type: text/css; charset: UTF-8");
        $file_content = file_get_contents('../'.$_GET['filename']);
        echo $file_content;
        exit();
    }
    elseif (preg_match('/\.js$/i', $_GET['filename'])) {
        header('Content-Type: application/javascript');
        $file_content = file_get_contents('../'.$_GET['filename']);
        echo $file_content;
        exit();
    }*/

    // Show XML error if nothing matched above
    header("Content-type: application/xml");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Error>';
    echo '<Code>InvalidRequest</Code>';
    echo '<Message>Invalid Request</Message>';
    echo '<RequestId>'.strtoupper(md5(date("Y-m-d H:i:s"))).'</RequestId>';
    echo '</Error>';

    exit();
