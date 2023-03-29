<?php
class S3Uploader
{
    private $awsAccessKey;
    private $awsSecretKey;
    private $bucketName;
    private $region;
    private $s3Endpoint;

    public function __construct($awsAccessKey, $awsSecretKey, $bucketName, $region, $s3Endpoint)
    {
        $this->awsAccessKey = $awsAccessKey;
        $this->awsSecretKey = $awsSecretKey;
        $this->bucketName = $bucketName;
        $this->region = $region;
        $this->s3Endpoint = $s3Endpoint;
    }

    public function uploadFileToS3($filePath, $fileKey)
    {
        $date = gmdate('Ymd\THis\Z');
        $shortDate = gmdate('Ymd');
        $credentialScope = $shortDate . '/' . $this->region . '/s3/aws4_request';

        $contentType = mime_content_type($filePath);
        $contentMD5 = base64_encode(md5_file($filePath, true));
        $contentSha256 = hash_file('sha256', $filePath);

        $canonicalizedAmzHeaders = "x-amz-acl:public-read\n";
        $canonicalizedResource = "/" . $this->bucketName . "/" . $fileKey;

        $stringToSign = "PUT\n$canonicalizedResource\n$date\n$contentType\n$canonicalizedAmzHeaders x-amz-content-sha256:$contentSha256\nx-amz-acl:public-read\nhost:" . $this->bucketName . '.' . $this->s3Endpoint . "\n\nx-amz-acl;host;x-amz-content-sha256;x-amz-date\n$contentSha256";

        $kDate = hash_hmac('sha256', $shortDate, 'AWS4' . $this->awsSecretKey, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $headers = array(
            'Host: ' . $this->bucketName . '.' . $this->s3Endpoint,
            'Content-Type: ' . $contentType,
            'Content-MD5: ' . $contentMD5,
            'x-amz-acl: public-read',
            'x-amz-date: ' . $date,
            'x-amz-content-sha256: ' . $contentSha256,
            'Authorization: AWS4-HMAC-SHA256 Credential=' . $this->awsAccessKey . '/' . $credentialScope . ', SignedHeaders=host;content-type;content-md5;x-amz-acl;x-amz-content-sha256;x-amz-date, Signature=' . $signature,
        );

        $url = 'https://' . $this->bucketName . '.' . $this->s3Endpoint . '/' . $fileKey;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_PUT, true);
        curl_setopt($curl, CURLOPT_INFILE, fopen($filePath, 'r'));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
