# Google Recaptcha to any form


# Usage example:
- Display the recaptcha in the fontend.ss form, add below code to the end of a fontend.ss template:
```php
GoogleRecaptchaToAnyForm::displayGoogleRecaptcha($SiteConfig.GoogleRecaptchaSiteKey, 'Form_ContactForm_Comment', 'no_debug').RAW
```
- Verify the recaptcha in the controller php file, add below code to the formAction function of your controller:
```php
GoogleRecaptchaToAnyForm::checkGoogleRecaptcha($config->GoogleRecaptchaSecretKey, 'Google Recaptcha Validation Failed! Please refresh the page and re-submit the form!');
```
