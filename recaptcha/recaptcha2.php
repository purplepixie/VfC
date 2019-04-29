<?php
// Quick and dirty reCAPTCHA v2 PHP validator
// Source: https://developers.google.com/recaptcha/docs/verify
// David Cutting, dcutting@purplepixie.org, http://davecutting.uk

function validate_recaptcha_2($secret, $response)
{
    // n.b. using CURL-less methods as I'm unsure of the exec environment other than probably PHP5
    $remote = "https://www.google.com/recaptcha/api/siteverify";
    $fields = array(
        "secret" => $secret,
        "response" => $response
    );
    $options = array(
        "http" => array(
            "header" => "Content-type: application/x-www-form-urlencoded\r\n",
            "method" => 'POST',
            "content" => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($remote, false, $context);
    if ($result !== FALSE)
    {
        $json = json_decode($result);
        if ($json->success)
            return true;
    }

    return false; // default failed return
}
