<?php

require "./vendor/autoload.php";
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$profile = 'campbest-testcampbest';                                       //this specifies which profile inside of credential file to use
$path = '/var/www/.aws/credentials';        //path to credential file

$provider = CredentialProvider::ini($profile, $path);
$provider = CredentialProvider::memoize($provider);

$s3Client = new S3Client([
	'region' => 'us-east-2',
	'version' => '2006-03-01',
	'credentials' => $provider
]);

header("content-type: text/plain");

$bucket = "testcampbest";

// Use the plain API (returns ONLY up to 1000 of your objects).
try {
    $objects = $s3Client->listObjects([
        'Bucket' => $bucket
]);
    
    foreach ($objects['Contents']  as $object) {
	    
	    $key = $object['Key'];


	    $result = $s3Client->getObject([
	    	
		    'Bucket' => $bucket,
		    'Key' => $key
	    	
	    ]);

	echo $result['Body']->getContents();
	print PHP_EOL;

    }


} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}




?>
