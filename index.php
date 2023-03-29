<?php
require_once('s3.php');

$uploader = new S3Uploader('eu-central-1', 'gp-site');
$result = $uploader->uploadImage('./img/2.jpg', './layout/test.jpg');
if ($result) {
    echo "Image uploaded successfully.";
} else {
    echo "Error uploading image.";
}
$url = $uploader->getImageUrl('./layout/test.jpg');

echo "<img src='$url'/>";
