<?php


define('PLURALL_TIMEOUT', 20000);

require_once('CryptoLib/src/CryptoLib.php');
use IcyApril\CryptoLib;

class ThirdParty {
  
  public static function encryptToken($token, $secret) 
  {
    $randomLength = 16;
    $keySize = 256/32;
    $iterations = 10000;
    $hasher = 256;

    $salt = CryptoLib::randomString($randomLength);
    $iv = CryptoLib::randomString($randomLength);
    $key = \hash_pbkdf2("sha256", $secret, $salt, $iterations, 0);
    $encrypted = \openssl_encrypt($token, "aes-256-gcm", $key, $options=0, $iv, $keySize);
    $concatenned =  $salt.$iv.$encrypted;

    return urlencode(base64_encode($concatenned));
  }

  public static function getUser($hash) {
    $ch = curl_init();
    $url = "http://ms-api.local.plurall.net:3000/external-auth/authcheck/dreamshaper?hash=$hash";
    echo $url.PHP_EOL;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PLURALL_TIMEOUT);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
      'x-client: plurall.sdk.nodejs'
    ));

    $data = curl_exec($ch);
    if (curl_errno($ch)) {
      var_dump('Error:' . curl_error($ch));
      return false;
   }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $data;
  }

}

$token = '157e0bc6-c759-443e-857c-f43bc01b444a';

echo "Encypt... ";
$hash = ThirdParty::encryptToken($token, "26ee06fad4785ba3b4a0c2941272bffe043fd69c");
echo $hash.PHP_EOL.PHP_EOL;
echo "Get User... ";
echo ThirdParty::getUser($hash, "26ee06fad4785ba3b4a0c2941272bffe043fd69c");
echo PHP_EOL;
?>
