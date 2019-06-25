# Google Recaptcha to any form
- It can display a Google Recaptcha v2 in any custom form with flexible settings and no affection of your existing code.
- How to install
```php
    composer require alexstack/google-recaptcha-to-any-form dev-master
```

# Usage example for SilverStripe 4.x/3.x
- Include it in your controller php file first:
```php
use AlexStack\GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```
- Create a function to display the recapacha in your controller. eg.:
```php
    public function showGoogleRecaptcha()   {
        return GoogleRecaptcha::show($SiteConfig.GoogleRecaptchaSiteKey, 'Form_ContactForm_Message', 'debug');
    }
```
- Display the recaptcha in the fontend.ss form, add below code to the end of a fontend.ss template. eg. :
```php
    $showGoogleRecaptcha.RAW
```
- Verify the recaptcha in the controller php file, add below code to the formAction function of your controller. eg.:
```php
    GoogleRecaptcha::verify($config->GoogleRecaptchaSecretKey, 'Google Recaptcha Validation Failed!!');
```

# License
- MIT
