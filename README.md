# Google Recaptcha v2 or v3 to any form

- It can display a Google Recaptcha v2 or v3 in any custom form with flexible settings and no affection to your existing code. Also works well for SilverStripe 4.x/3.x/2.x & Larave & Wordpress & other CMS.

# Basic example codes

- Display Google Recaptcha v2 or v3 after a Form_Field_ID:

```php
// For Google recaptcha v2
GoogleRecaptcha::show('SiteKey', 'Form_Field_ID');

// For Google recaptcha v3
GoogleRecaptcha::showV3('SiteKey', 'Form_Field_ID');
```

- Verify it in the backend php:

```php
GoogleRecaptcha::verify('SecretKey');
```

# How to install

```php
composer require alexstack/google-recaptcha-to-any-form
```

# Contents

- [x] [How to display it on frontend page](#frontend)
- [x] [How to verify it in the backend script](#backend)
- [x] [Usage example for SilverStripe 4.x/3.x](#silverstripe)
- [x] [Usage example for Laravel](#laravel)
- [x] [Usage example for Wordpress](#wordpress)

# <a name="frontend"></a>How to display it on frontend page?

![How to display it on frontend page](https://developers.google.com/recaptcha/images/newCaptchaAnchor.gif "Google Recaptcha")

- Set up your Google Recaptcha v2 or v3 account for you website and get the site key and secret key
- Import the GoogleRecaptcha class:

```php
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Put below php code in your frontend template/page for **Google Recaptcha v2**.

```php
GoogleRecaptcha::show($GoogleRecaptchaSiteKey, 'Form_ContactForm_Message', 'no_debug', 'mt-4 mb-1', 'Please tick the reCAPTCHA checkbox first!');
```

- Description for above code:
  - '\$GoogleRecaptchaSiteKey': The Google Recaptcha Site Key of your website. You can directly put site key here or a variable or from database.
  - 'Form_ContactForm_Message': Form_Field_ID, via html code: ... `<input type="text" id="Form_ContactForm_Message" />` ... Your Google Recaptcha will display after this form field.
  - 'no_debug': Change to debug and not tick the I'm not a robot checkbox will continue submit the form, then you will see the failed message.
  - 'mt-4 mb-1': Extra css class name for the Google Recaptcha area.
  - 'Please tick the reCAPTCHA checkbox first': Frontend alert message if the end user does not tick the checkbox. Tips: You can change this value to "v3", it will automatically switch to use Google Recaptcha v3
- Default value of the parameters of the show() method

```php
show($site_key,$after_field_id='Form_ContactForm_Comment', $debug='no_debug', $extra_class="mt-4 mb-4", $please_tick_msg="Please tick the I'm not robot checkbox");
```

- Put below php code in your frontend template/page for **Google Recaptcha v3**
```php
GoogleRecaptcha::showV3($GoogleRecaptchaSiteKey, 'Form_Field_ID', 'no_debug');
```
- Our Google Recaptcha v3 will automatically get g-recaptcha-response value after the page load 10 seconds or the Form_Field_ID(eg. Form_ContactForm_Message) was clicked.
- 'no_debug': Change to "debug" will always submit an empty g-recaptcha-response to the backend.
- By default recaptcha v3 verify score < 0.5 as a failed result. You can set the score if you want a different value. eg.

```php
GoogleRecaptcha::verify($SecretKey, 'Google Recaptcha Validation Failed!!', 0.36);
```

- If you do not want to use the show() method, You can also use your own code to display the recaptcha for a custom style. Just make sure the form action method is POST, then you can still use below verify() method in your backend script.

# <a name="backend"></a>How to verify it in the backend script

- Import the GoogleRecaptcha class:

```php
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Put below php code into the form-submitted-method() of your backend php file.

```php
GoogleRecaptcha::verify($GoogleRecaptchaSecretKey, 'Google Recaptcha Validation Failed!!');
```

- Description for above code:
  - '\$GoogleRecaptchaSecretKey': The Google Recaptcha Secret Key of your website. You can directly put secret key here or a variable or from database.
  - 'Google Recaptcha Validation Failed': Javascript alert message if the verification failed. Set it null or false if you don't want a javascript alert. It will return true or false by the Google recaptcha verification result. Then you can show your own error message.
- Default value of the parameters of the verify() method

```php
verify($secret_key, $break_msg = null, $recaptcha_score=0.5)
```

# <a name="silverstripe"></a>Usage example for SilverStripe 4.x/3.x

- Import the GoogleRecaptcha class:

```php
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Create a function to display the recaptcha in your controller. eg.:

```php
public function showGoogleRecaptcha()   {
    return GoogleRecaptcha::show($GoogleRecaptchaSiteKey, 'Form_ContactForm_Message', 'no_debug', 'mt-4 mb-1', 'Please tick the reCAPTCHA checkbox first!');
}
```

- Display the recaptcha in the frontend.ss form, add below code to the end of a frontend.ss template. eg. :

```php
$showGoogleRecaptcha.RAW
```

- Verify the recaptcha in the controller php file, add below code to the formAction function of your controller. eg.:

```php
GoogleRecaptcha::verify($GoogleRecaptchaSecretKey, 'Google Recaptcha Validation Failed!!');
```

# <a name="laravel"></a>Usage example for Laravel 5.x custom login form

- Include it in your LoginController.php file first:

```php
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Create a function to display the recaptcha in your LoginController.php eg.:

```php
public function showLoginForm()
{
    $showRecaptcha = GoogleRecaptcha::show('site_key', 'password', 'no_debug', 'mt-4 mb-3 col-md-6 offset-md-4', 'Please tick the reCAPTCHA checkbox first!');
    return view('auth.login', compact('showRecaptcha'));
}
```

- Display the recaptcha in the auth/login.blade.php, add below code to the end of the auth/login.blade.php template. eg. :

```php
{!! $showRecaptcha !!}
```

- Verify the recaptcha in the LoginController.php file, add below code for validateLogin. eg.:

```php
protected function validateLogin(Request $request)
{

    GoogleRecaptcha::verify('secret_key', 'Google Recaptcha Validation Failed!!');

    $request->validate([
        $this->username() => 'required|string',
        'password' => 'required|string',
    ]);
}
```

# <a name="wordpress"></a>Usage example for Wordpress custom form

- Include it in your custom form template php file first. Note: Change the correct vendor path for require_once:

```php
require_once(__DIR__ . '/../../../../vendor/autoload.php');
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Display the recaptcha in the form template. eg. :

```php
echo GoogleRecaptcha::show('site_key', 'input_2_3', 'no_debug', 'mt-4 mb-3 col-md-6 offset-md-4', 'Please tick the reCAPTCHA checkbox first!');
```

- Verify the recaptcha in the handle form submission method . eg.:

```php
require_once(__DIR__ . '/../../vendor/autoload.php');
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;

GoogleRecaptcha::verify('secret_key', 'Google Recaptcha Validation Failed!!');
```

# License

- MIT
