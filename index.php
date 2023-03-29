<?php

require_once('config.php');

// Include the AWS SDK for PHP
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Set your AWS credentials
$credentials = new Aws\Credentials\Credentials(ACCESS_KEY, SECRET_KEY);

// Set your region and bucket name
$region = 'eu-central-1'; // Update with your region
$bucket = 'gp-site'; // Update with your bucket name

// Create an S3Client instance
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    'credentials' => $credentials
]);

// Set the image file path and name
$imageFilePath = './img/2.jpg'; // Update with your image file path
$imageFileName = 'test.jpg'; // Update with your image file name

// Upload the image file to S3
try {
    $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $imageFileName,
        'SourceFile' => $imageFilePath,
        'ACL'    => 'public-read', // Set the file permission to public-read
    ]);
    echo "Image uploaded successfully.";
} catch (AwsException $e) {
    echo "Error uploading image: " . $e->getMessage();
}

/*
it helped me to configure the bucket permission to upload image
https://stackoverflow.com/questions/70333681/for-an-amazon-s3-bucket-deployment-from-github-how-do-i-fix-the-error-accesscont*/