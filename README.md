# Google Recaptcha to any form

- It can display a Google Recaptcha v2 in any custom form with flexible settings and no affection to your existing code. Also works well for SilverStripe 4.x/3.x/2.x & Larave & Wordpress & other CMS. 
- How to install

```php
    composer require alexstack/google-recaptcha-to-any-form dev-master
```

# How to display it on frontend page?

- Set up your Google Recaptcha account for you website and get the site key and secret key
- Import the GoogleRecaptcha class:

```php
use GoogleRecaptchaToAnyForm\GoogleRecaptcha;
```

- Put below php code in your frontend template/page.

```php
GoogleRecaptcha::show($GoogleRecaptchaSiteKey, 'Form_ContactForm_Message', 'no_debug', 'mt-4 mb-1', 'Please tick the reCAPTCHA checkbox first!');
```

- Description for above code:
  - '\$GoogleRecaptchaSiteKey': The Google Recaptcha Site Key of your website. You can directly put site key here or a variable or from database.
  - 'Form_ContactForm_Message': Form field id, via html code: ... `<input type="text" id="Form_ContactForm_Message" />` ... Your Google Recaptcha will display after this form field.
  - 'no_debug': Change to debug and not tick the I'm not a robot checkbox will continue submit the form, then you will see the failed message.
  - 'mt-4 mb-1': Extra css class name for the Google Recaptcha area.
  - 'Please tick the reCAPTCHA checkbox first': Frontend alert message if the end user does not tick the checkbox.
- Default value of the parameters of the show() method

```php
show($site_key,$after_field_id='Form_ContactForm_Comment', $debug='no_debug', $extra_class="mt-4 mb-4", $please_tick_msg="Please tick the I'm not robot checkbox");
```

- If you do not want to use the show() method, You can also use your own code to display the recaptcha for a custom style. Just make sure the form action method is POST, then you can still use below verify() method in your backend script.

# How to verify it in the backend script

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
verify($secret_key, $break_msg = null)
```

# Usage example for SilverStripe 4.x/3.x

- Include it in your controller php file first:

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

# Usage example for Laravel 5.x custom login form

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

# License

- MIT
