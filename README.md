# Plurall SDK

Software Developer Kit - PHP - Plurall

# Table of Contents

* [Install](#install)
* [Plurall Integration](#plurall-integration)

---

# Install

```sh
composer install plurall/sdk
```

---
## Plurall Integration

### ThirdParty

#### Preparing environment

These are all available environment variables:

* **PLURALL_ENV**: Environment to make the request (staging|production) - *default staging*
* **PLURALL_TIMEOUT**: Time to wait before timeout (ms) - *default 20000*
* **DEBUG**: Enable / disable debug mode (true|false) - *default false*

#### How to get user data?

```php
<?php
require_once('./src/Integration/ThirdParty.php');

use Plurall\Integration\ThirdParty;

define("PLURALL_ENV", "staging");
define("PLURALL_TIMEOUT", 30000);
define("DEBUG", true);

$token = $_GET["token"];
$secret = "<YOUR_AMAZING_SECRET>";

try {
  $hash = ThirdParty::encryptToken($token, $secret);
  $userData = ThirdParty::getUser("YOUR_APP_NAME_ON_PLURALL", $hash);

  var_dump($userData);
} catch (Exception $e) {
  var_dump($e);
}
```
