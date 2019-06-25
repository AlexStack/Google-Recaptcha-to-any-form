# Google Recaptcha to any form


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
    GoogleRecaptcha::verify($config->GoogleRecaptchaSecretKey, 'Google Recaptcha Validation Failed! Please refresh the page and re-submit the form!');
```
