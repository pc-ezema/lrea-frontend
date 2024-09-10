<?php 

if (!$database_config) {
	include_once('../../connections/config.php');
}

if (!function_exists('createDataURI')) { /* Check_for "createDataURI" */
function createDataURI($imgfile, $mimetype = ''){
	$filecontents = file_get_contents($imgfile);
	$encoding = base64_encode($filecontents);
	
	if (preg_match("/^.+\.(jpg|jpeg|png|gif)$/", $imgfile, $matches)) $mimetype = 'image/'.$matches[1]; // Auto-detect mimetype via filename extension
	if (!$mimetype) $mimetype = 'image/png'; // If mimetype is not known, set a default of PNG
	
	return ('data:'.(function_exists('mime_content_type') ? mime_content_type($imgfile) : $mimetype).';base64,'.$encoding); 
	/* 
	   Sample usage
	   <img src="<?php echo createDataURI('logo.png', 'image/png') ?>" alt="" />
	*/
}
} /* End_check for "createDataURI" */

if (!function_exists('returnpix')) { /* Check_for "returnpix" */
function returnpix($picture='msbm-portal-avatar.png', $mode='not-set', $prefix="") {
	if ($mode == 'forum') {
		if (!$picture) {
			return "img/avatar_dark_64x64.png";
		} elseif (!file_exists("content/photos/".$prefix.$picture)) {
			return "img/avatar_dark_64x64.png";
		} else {
			return "content/photos/".$prefix.$picture;
		}
	} elseif ($mode == 'chat') {
		if (!$picture) {
			return "img/avatar_dark_64x64.png";
		} elseif (!file_exists("content/photos/".$prefix.$picture)) {
			return "img/avatar_dark_64x64.png";
		} else {
			return "content/photos/".$prefix.$picture;
		}
	} elseif ($mode == 'not-set') {
		return "img/avatar_dark_64x64.png";
	}
}
} /* End_check for "returnpix" */

if (!function_exists('square_crop')) { /* Check_for "square_crop" */
function square_crop($src_image, $dest_image, $thumb_size = 64, $jpg_quality = 90) {

// Get dimensions of existing image
$image = getimagesize($src_image);

// Check for valid dimensions
if( $image[0] <= 0 || $image[1] <= 0 ) return false;

// Determine format from MIME-Type
$image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));

// Import image
switch( $image['format'] ) {
    case 'jpg':
    case 'jpeg':
        $image_data = imagecreatefromjpeg($src_image);
    break;
    case 'png':
        $image_data = imagecreatefrompng($src_image);
    break;
    case 'gif':
        $image_data = imagecreatefromgif($src_image);
    break;
    default:
        // Unsupported format
        return false;
    break;
}
} /* End_check for "square_crop" */

// Verify import
if( $image_data == false ) return false;

// Calculate measurements
if( $image[0] > $image[1] ) {
    // For landscape images
    $x_offset = ($image[0] - $image[1]) / 2;
    $y_offset = 0;
    $square_size = $image[0] - ($x_offset * 2);
} else {
    // For portrait and square images
    $x_offset = 0;
    $y_offset = ($image[1] - $image[0]) / 2;
    $square_size = $image[1] - ($y_offset * 2);
}

// Resize and crop
$canvas = imagecreatetruecolor($thumb_size, $thumb_size);
if( imagecopyresampled(
    $canvas,
    $image_data,
    0,
    0,
    $x_offset,
    $y_offset,
    $thumb_size,
    $thumb_size,
    $square_size,
    $square_size
)) {

    // Create thumbnail
    switch( strtolower(preg_replace('/^.*\./', '', $dest_image)) ) {
        case 'jpg':
        case 'jpeg':
            return imagejpeg($canvas, $dest_image, $jpg_quality);
        break;
        case 'png':
            return imagepng($canvas, $dest_image);
        break;
        case 'gif':
            return imagegif($canvas, $dest_image);
        break;
        default:
            // Unsupported format
            return false;
        break;
    }

     } else {
    return false;
     }
}

