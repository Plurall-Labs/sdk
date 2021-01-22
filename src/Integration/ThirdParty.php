<?php

namespace Plurall\Integration;

class ThirdParty {
	const keySize = 32;
	const iterations = 10000;
	const cipher = "AES-256-CBC";

  public static function encryptToken($token, $secret) 
  {
    $salt = bin2hex(openssl_random_pseudo_bytes(ThirdParty::keySize/2));
    $iv = bin2hex(openssl_random_pseudo_bytes(openssl_cipher_iv_length(ThirdParty::cipher)));
    $key = hash_pbkdf2("sha256", $secret, $salt, ThirdParty::iterations, ThirdParty::keySize, true);
    $encrypted = openssl_encrypt($token, ThirdParty::cipher, $key, 0, substr($iv, 0, ThirdParty::keySize/2));
    $concatenned =  $salt . $iv . base64_decode($encrypted);

    return urlencode(base64_encode($concatenned));
  }

  public static function getUser($app, $hash) {
		if (!defined("PLURALL_TIMEOUT")) {
			define("PLURALL_TIMEOUT", 20000);
		}

		$domain = defined("PLURALL_DOMAIN")
			? PLURALL_DOMAIN
		 	: ("https://ms-api" . (defined("PLURALL_ENV") && PLURALL_ENV === "production" ? "" : ".s") . ".plurall.net"); 

    $ch = curl_init();
    $url = "$domain/external-auth/authcheck/$app?hash=$hash";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_VERBOSE, (defined("DEBUG") && DEBUG));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, PLURALL_TIMEOUT);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-client: plurall.sdk.nodejs"]);

    $data = curl_exec($ch);
    if (curl_errno($ch)) {
      throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $data;
  }
}
