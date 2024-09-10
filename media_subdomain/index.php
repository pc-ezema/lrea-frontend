<?php 
	/*require_once('../connections/config.php');
	require_once('../components/misc.inc.php');

    $allowedExts = array(
        "jpg" => "image/jpeg", 
        "jpeg" => "image/jpeg", 
        "gif" => "image/gif", 
        "png" => "image/png", 
        "svg" => "image/svg+xml", 
    );

    $the_file_type = @end(explode(".", $_GET['filename']));

    if ($the_file_type && array_key_exists($the_file_type, $allowedExts)) {
        header("Content-type: ".$allowedExts[$the_file_type]);
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
