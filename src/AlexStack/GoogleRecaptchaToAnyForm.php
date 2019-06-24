<?php
/**
 * Created by Alex Zeng
 */

namespace AlexStack\GoogleRecaptchaToAnyForm;

class GoogleRecaptchaToAnyForm
{

    public function displayGoogleRecaptcha($site_key,$after_field_id='Form_ContactForm_Comment', $debug='no_debug', $please_tick_msg="Please tick the I'm not robot checkbox")    {
        $debug_alert = ($debug=='no_debug') ? 'false' : 'true';
        $str = <<<EOF
        <!-- Start of the Google Recaptcha code -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <script>
        
        // Display google recaptcha
        $('#$after_field_id').parent().after('<div class="g-recaptcha mt-4 mb-5" data-sitekey="$site_key"></div> ');
        
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

    /**
     * checkGoogleRecaptcha function
     *
     * @param [string] $secret_key
     * @param [string] $break_msg, if set, pop up as an javascript alert and exit
     * @return true or false
     */
    public function checkGoogleRecaptcha($secret_key, $break_msg = null)  {
        $valid = false;
        if ( isset($_POST['g-recaptcha-response']) && strlen($_POST['g-recaptcha-response'])>20 ) {
            $valid = $this->getGoogleRecaptchaResult($secret_key, $_POST['g-recaptcha-response']);
        }
        
        if ( !$valid && $break_msg){
            if ( strlen($break_msg) < 10 ){
                $break_msg = "Google Recaptcha Validation Failed!!";
            }
            echo '<script>alert("' . $break_msg . '");history.back();</script>';
            exit();
        }  
        return $valid;      
    }

    /**
     * getGoogleRecaptchaResult function
     *
     * @param [type] $secret_key
     * @param [type] $g_recaptcha_response
     * @return void
     */
    public function getGoogleRecaptchaResult($secret_key, $g_recaptcha_response)
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
}
