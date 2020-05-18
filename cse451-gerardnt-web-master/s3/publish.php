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

header("content-type: text/html");

$bucket = "testcampbest";
$information = array();


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

	$information[$key] = $result['Body']->getContents();
    	    
    }


} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}




$contents= '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>S3 CMS Assignment</title>
<!--
 * Nicholas Gerard 
 * CSE 451 Spring 2020 Dr. Campbell
 * 4/14/2020
 * This Assignment is taking all the information uploaded to Dr. Campbells bucket and creating an HTML page to my own s3 bucket
 *
 *
 -->

</head>
<body>';


foreach($information as $userId=> $info){

	$trim = trim($info, "\n");
	$splitUser = explode("\n",$trim);

	if(strpos($splitUser[1] , "<img" ) === false ){
        	$contents .="<div> <h2> ".$userId."</h2> <p>".$splitUser[0]."</p> <img src='".$splitUser[1]."' alt='".$userId." working location'>";
	}else{

	 $contents .="<div> <h2> ".$userId."</h2> <p>".$splitUser[0]."</p>". $splitUser[1];
	}

}

$contents.= ' </body></html> ';

$profile = 'default';                                       //this specifies which profile inside of credential file to use

$provider = CredentialProvider::ini($profile, $path);
$provider = CredentialProvider::memoize($provider);
$bucket = 'gerardnt-451-s20-bucket1';


$s3Client = new S3Client([
	'region' => 'us-east-1',
	'version' => '2006-03-01',
	'credentials' => $provider
]);

$key ='everybody.html';
try {
	$result = $s3Client->putObject([
		'Bucket' => $bucket,
		'Key' => $key,
		'Body' => $contents,
		'ACL' => 'public-read',
		'ContentType' => "text/html"
	]);
	$url = $result['@metadata']['effectiveUri'];

	print '<!Doctype html> <html> <head> </head> <body>';
	print "The Page has successfully been created !<br>";
	print '<a href="'.$url.'">Click here</a>';
	print '</body> </html>';
} catch (S3Exception $e) {
	print "Error " . $e;
}





?>

