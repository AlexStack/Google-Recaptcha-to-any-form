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
    public static function verify($secret_key, $break_msg = null)
    {
        $valid = false;
        if (isset($_POST['g-recaptcha-response']) && strlen($_POST['g-recaptcha-response']) > 20) {
            $valid = Self::result($secret_key, $_POST['g-recaptcha-response']);
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
    public static function result($secret_key, $g_recaptcha_response)
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

        if (!$response || $response->success == false) {
            //var_dump($response); // un-comment for debug
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
        $debug_alert = ($debug == 'no_debug') ? 'false' : 'true';
        $str = <<<EOF
        <!-- Start of the Google Recaptcha code -->
 
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script>
 
            // Display google recaptcha
            var alexEL = document.getElementById('$after_field_id');
            alexEL.parentNode.insertAdjacentHTML('afterend', '<div class="g-recaptcha $extra_class" data-sitekey="$site_key"></div>');

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

            // Validate google recaptcha before submit the form

            alexFindClosestNode(alexEL,'form').addEventListener('submit',function(e){
                var response = grecaptcha.getResponse();
                //recaptcha failed validation
                if(response.length == 0) {
                    alert("$please_tick_msg");
                    e.preventDefault();
                    return $debug_alert;
                }
                return true;
            });
 
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
