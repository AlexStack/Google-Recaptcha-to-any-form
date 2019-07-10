<?php
/**
 * Created by Alex Zeng
 * 2019-06-25
 */

namespace GoogleRecaptchaToAnyForm;

class GoogleRecaptcha
{

    /**
     * verify function
     *
     * @param [string] $secret_key
     * @param [string] $break_msg, if set, pop up as an javascript alert and exit
     * @return true or false
     */
    public static function verify($secret_key, $break_msg = null, $recaptcha_score = 0.5)
    {
        $valid = false;
        if (isset($_POST['g-recaptcha-response']) && strlen($_POST['g-recaptcha-response']) > 20) {
            $valid = Self::result($secret_key, $_POST['g-recaptcha-response'], $recaptcha_score);
        }

        if (!$valid && $break_msg) {
            if (strlen($break_msg) < 10) {
                $break_msg = "Google Recaptcha Validation Failed!!";
            }
            echo '<script>alert("' . $break_msg . '");history.back();</script>';
            exit();
        }
        return $valid;
    }

    /**
     * result function
     *
     * @param [type] $secret_key
     * @param [type] $g_recaptcha_response
     * @return void
     */
    public static function result($secret_key, $g_recaptcha_response, $recaptcha_score=0.5)
    {
        $google_recaptcha_uri = 'https://www.google.com/recaptcha/api/siteverify';

        if (!$g_recaptcha_response || strlen($g_recaptcha_response) < 10) {
            return false;
        }

        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_URL => $google_recaptcha_uri,
                CURLOPT_POSTFIELDS => [
                    'secret' => $secret_key,
                    'response' => $g_recaptcha_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]);
            $rs = curl_exec($curl);
            curl_close($curl);
        } else {
            $rs = file_get_contents($google_recaptcha_uri . '?secret=' . $secret_key . '&response=' . $g_recaptcha_response . '&remoteip=' . $_SERVER['REMOTE_ADDR'] . '', true);
        }


        $response = json_decode($rs);

        //var_dump($response); exit();// un-comment for debug

        if (!$response || $response->success == false) {
            return false;
        } else if ( isset($response->score) && $response->score < $recaptcha_score ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * show recaptcha function without jQuery
     *
     * @param string $site_key
     * @param string $after_field_id
     * @param string $debug
     * @param string $extra_class
     * @param string $please_tick_msg
     * @return void
     */
    public static function show($site_key, $after_field_id = 'Form_ContactForm_Comment', $debug = 'no_debug', $extra_class = "mt-4 mb-4", $please_tick_msg = "Please tick the I'm not robot checkbox")
    {
        if ( $please_tick_msg == 'v3'){
            return Self::showV3($site_key, $after_field_id, $debug, $extra_class);
        }

        if ( strpos($extra_class, 'invisible') !== false  ){
            return Self::showInvisible($site_key, $after_field_id, $debug, $extra_class);
        }

        $api_js_str = "<script src='https://www.google.com/recaptcha/api.js'></script>";
        if ( strpos($extra_class, 'no-api-js') !== false  ){
            $api_js_str = "";
        }
        $data_theme = 'light';
        if ( strpos($extra_class, 'theme-dark') !== false  ){
            $data_theme = "dark";
        }

        $debug_alert = ($debug == 'no_debug') ? 'false' : 'true';
        $str = <<<EOF
        <!-- Start of the Google Recaptcha v2 code -->
 
        $api_js_str
        <script>
 
            // Display google recaptcha
            function alexFindClosestNode(el, selector) {
                const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;

                while (el) {
                    if (matchesSelector.call(el, selector)) {
                    return el;
                    } else {
                    el = el.parentElement;
                    }
                }
                return null;
            }

            var alexEL = document.getElementById('$after_field_id');
            if ( alexEL )   {
                alexEL.parentNode.insertAdjacentHTML('afterend', '<div class="g-recaptcha $extra_class" data-sitekey="$site_key" data-theme="$data_theme"></div>');

                // Validate google recaptcha before submit the form
                alexFindClosestNode(alexEL,'form').addEventListener('submit',function(e){
                    var response = grecaptcha.getResponse();
                    //recaptcha failed validation
                    if(response.length == 0) {
                        alert("$please_tick_msg");
                        if (!$debug_alert){
                            e.preventDefault();
                        }
                        return $debug_alert;
                    }
                    return true;
                });
            }
 
        </script>
        <!-- End of the Google Recaptcha code -->
EOF;
        return $str;
    }


    /**
     * show recaptcha v3 function without jQuery
     *
     * @param string $site_key
     * @param string $after_field_id
     * @param string $debug
     * @param string $extra_class
     * @param string $please_tick_msg
     * @return void
     */
    public static function showV3($site_key, $after_field_id = 'Form_ContactForm_Comment', $debug = 'no_debug', $extra_class = "mt-4 mb-4")
    {
        $debug_mode = ($debug == 'no_debug') ? '' : 'return false; // debug mode is on ';

        if ( strpos($extra_class, 'show-inline-badge') !== false ){
            $api_js = "https://www.google.com/recaptcha/api.js?render=explicit&onload=alexGetRecaptchaValue";
            $recaptcha_client = "var recaptchaClient = grecaptcha.render('CustomContactUsForm-inline-badge', {
                'sitekey': '$site_key',
                'badge': 'inline',
                'size': 'invisible'
            });";
        } else {
            $api_js = "https://www.google.com/recaptcha/api.js?render=$site_key&onload=alexRecaptchaReadyCallback";
            $recaptcha_client = "var recaptchaClient =  '$site_key';";
        }
        $str = <<<EOF
        <!-- Start of the Google Recaptcha v3 code -->
 
        <script src="$api_js"></script>


        <script>
 
            // Display google recaptcha v3
            var alexEL = document.getElementById('$after_field_id');
            if ( alexEL )   {
                alexEL.parentNode.insertAdjacentHTML('afterend', '<div id="CustomContactUsForm-inline-badge" class="inline-badge-div $extra_class"></div><input type="hidden" id="CustomContactUsForm-recaptcha" name="g-recaptcha-response">');
            }

            function alexGetRecaptchaValue(id) {
                $debug_mode
                if ( typeof(id)=='undefined' )  {
                    id = 'CustomContactUsForm-recaptcha';
                }
                if ( document.getElementById(id) && document.getElementById(id).value == '' ) {

                    $recaptcha_client

                    grecaptcha.ready(function() {
                        grecaptcha.execute(recaptchaClient, {
                            action: 'CustomContactUsForm'
                        }).then(function (token) {
                            document.getElementById(id).value = token;
                        });
                    });
                }

            }

            setTimeout('alexGetRecaptchaValue("CustomContactUsForm-recaptcha")', 10000);
 
            function alexRecaptchaReadyCallback() {
                if ( alexEL )   {
                    alexEL.addEventListener('click',function(e){
                        alexGetRecaptchaValue("CustomContactUsForm-recaptcha");
                    });
                }
            }  

        </script>

        <!-- End of the Google Recaptcha code -->
EOF;
        return $str;
    }



    /**
     * show recaptcha Invisible function without jQuery
     *
     * @param string $site_key
     * @param string $after_field_id
     * @param string $debug
     * @param string $extra_class
     * @param string $please_tick_msg
     * @return void
     */
    public static function showInvisible($site_key, $after_field_id = 'Form_ContactForm_Comment', $debug = 'no_debug', $extra_class = "mt-4 mb-4")
    {
        $debug_mode = ($debug == 'no_debug') ? '' : 'return false; // debug mode is on ';

        $str = <<<EOF
        <!-- Start of the Google Recaptcha Invisible code -->
 
        <script src="https://www.google.com/recaptcha/api.js?onload=alexRecaptchaReadyCallback" async defer></script>


        <script>
 
            // Display google recaptcha Invisible
            var alexEL = document.getElementById('$after_field_id');
            if ( alexEL )   {
                alexEL.parentNode.insertAdjacentHTML('afterend', '<div class="g-recaptcha $extra_class" data-sitekey="$site_key" data-callback="alexGetRecaptchaValue" data-size="invisible"></div><input type="hidden" id="CustomContactUsForm-recaptcha" name="g-recaptcha-response">');
            }

            function alexGetRecaptchaValue(token) {
                $debug_mode

                if ( document.getElementById('CustomContactUsForm-recaptcha').value == '' ) {
                    document.getElementById('CustomContactUsForm-recaptcha').value = token;
                }

            }
 
            function alexRecaptchaReadyCallback() {
                if ( alexEL )   {
                    grecaptcha.execute();
                }
            }  

        </script>

        <!-- End of the Google Recaptcha code -->
EOF;
        return $str;
    }

    /**
     * jqueryShow function
     *
     * @param string $site_key
     * @param string $after_field_id
     * @param string $debug
     * @param string $extra_class
     * @param string $please_tick_msg
     * @return void
     */
    public static function jqueryShow($site_key, $after_field_id = 'Form_ContactForm_Comment', $debug = 'no_debug', $extra_class = "mt-4 mb-4", $please_tick_msg = "Please tick the I'm not robot checkbox")
    {
        $debug_alert = ($debug == 'no_debug') ? 'false' : 'true';
        $str = <<<EOF
        <!-- Start of the Google Recaptcha code -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script>
        
        // Display google recaptcha
        $('#$after_field_id').parent().after('<div class="g-recaptcha $extra_class" data-sitekey="$site_key"></div> ');
        
        // Validate google recaptcha before submit the form
        $('#$after_field_id').closest('form').submit(function(e) {
            var response = grecaptcha.getResponse();
            //recaptcha failed validation
            if(response.length == 0) {
                alert("$please_tick_msg");
                return $debug_alert;
            }
            return true;
        });
        
        </script>
        <!-- End of the Google Recaptcha code -->
EOF;
        return $str;
    }
}
