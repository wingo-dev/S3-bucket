<?php
require_once('config.php');
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

class S3Uploader
{

    private $s3;
    private $region;
    private $bucket;

    public function __construct($region, $bucket)
    {
        $this->region = $region;
        $this->bucket = $bucket;
        $credentials = new Credentials(ACCESS_KEY, SECRET_KEY);
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->region,
            'credentials' => $credentials
        ]);
    }

    public function uploadImage($imageFilePath, $imageFileName)
    {
        try {
            $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $imageFileName,
                'SourceFile' => $imageFilePath,
                'ACL'    => 'public-read',
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    public function getImageUrl($imageFileName)
    {
        try {
            $url = $this->s3->getObjectUrl($this->bucket, $imageFileName);
            return $url;
        } catch (AwsException $e) {
            return false;
        }
    }
}
