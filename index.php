<?php

require_once('config.php');
require_once('s3.php');
// Usage example:
$uploader = new S3Uploader(ACCESS_KEY, SECRET_KEY, BUCKET_NAME, 'eu-central-1', 's3.amazonaws.com');

$filePath = './img/2.jpg';
$fileKey = 'test.jpg';

$response = $uploader->uploadFileToS3($filePath, $fileKey);

echo "Response: " . $response;
