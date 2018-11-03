<?php

header("Content-Type: text/html; charset=utf-8");

$filename = dirname(__FILE__)."/payPublicKey.pem";
	
	@chmod($filename, 0777);
	@unlink($filename);

$devPubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjLN8Mr4l7ruQy2Z0napLDHuCLYMaQfKxbv34184FHkm/c/hpKqC8Yzr1zMtcgnvh63U9BLJFV5xKHSYDr4PO6QwkHcjZMoMe0A35JZcMCXTMesGCfqsYvOjaJvHiT9o8swkq9qVyp/KEvmGbDvg3j5Z6S0LvmGsErEvRgfBQ9N3+HZBoq8NLWkUM0KqpkiNDdGUwiHIYN7mbKXte0xEQxwBDxWLJpGKtLg8Ib5xb7eQb2qMCkiqGrEBx+5sEV2RPEtz8tbrzfUONJSVNyW2BXR6SMga9SQOqjnKNAWbxWYgw2JGR1d7ORdtiJvNwpLu26rfjxj3qalZaChN3n1WgdwIDAQAB";
$begin_public_key = "-----BEGIN PUBLIC KEY-----\r\n";
$end_public_key = "-----END PUBLIC KEY-----\r\n";


$fp = fopen($filename,'ab');
fwrite($fp,$begin_public_key,strlen($begin_public_key)); 

$raw = strlen($devPubKey)/64;
$index = 0;
while($index <= $raw )
{
	$line = substr($devPubKey,$index*64,64)."\r\n";
	if(strlen(trim($line)) > 0)
	fwrite($fp,$line,strlen($line)); 
	$index++;
}
fwrite($fp,$end_public_key,strlen($end_public_key)); 
fclose($fp);
?>

