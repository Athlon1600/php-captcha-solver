# php-captcha-solver

Works for solving captchas when scraping Google, YouTube, and other sites that use ReCaptcha.

## ReCaptcha V2

https://2captcha.com/2captcha-api#solving_recaptchav2_new

```php
<?php

use CaptchaSolver\TwoCaptcha;

$captcha = new TwoCaptcha([
    'key' => 'API_KEY_GOES_HERE',
    'proxy' => null // optional
]);

$id = $captcha->sendReCaptchaV2("SITE_KEY", "PAGE_URL");

sleep(45);

$response = $captcha->getReCaptchaV2($id);

// or combine the two calls into one that polls automatically every 5 seconds and times out after 90 seconds:

$response = $captcha->solveReCaptchaV2("SITE_KEY", "PAGE_URL", 90);
```

`response` should hold a "solution" token that looks something like this:

```bash
03AGdBq24bamED8AtWElXZw9ceNn53tjN7fQ76dhyIbS_LS_5xWJuOXUb9ExnYUe_H3lvpKhZMX0Z7qmA-Ia9OBrbOu4eJYh2kosO89ZyTwADK8VrMXmQ8MD3NzaQeDg5jriopB9FrheDo7BemayGgqfJydtsRoJ_hg-RpDhzcwlUgLxJ9w4FwUd-IYBbGaMHp1wP4lbqMOpOaX21_D908LwZZgK2Dgc0TfJBTi_UL8r01sAYcvj2nouFG7JQCfXuj5LIzB8JL0Rxydig11sLayKIRbea66Jd_VkOj8h2xdC4NgDkY9OGkpRE
```

submit the captcha form given to you with that solution as `g-recaptcha-response` field along with any other relevant form fields, and you are done!

## Installation

```bash
composer require athlon1600/php-captcha-solver dev-master
```

## Testing

A useful mini web-application can be launched via:

```bash
php -S localhost:8000 -t public
```

and then visit:  
[http://localhost:8000/2captcha.php](http://localhost:8000/2captcha.php)


## Known issues

Sometimes solving the captcha will not get you through, and instead you would just get this message at the end:

![](https://i.imgur.com/1aGxvNj.png)

so at this point there is not much else you can do other than not make any requests for a while until Google automatically unblocks your IP address.


### Misc stuff

Make 'g-recaptcha-response' textarea visible on page:

```javascript
document.getElementById('g-recaptcha-response').style = null;
```
