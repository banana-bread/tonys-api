<?php

namespace App\Http\Controllers;

class RecaptchaController extends ApiController
{
    public function verify()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => config('auth.recaptcha_secret'),
                'response' => request('token'),
            ]
        ])->getBody()->getContents();

        $response = json_decode($response, true);

        return $this->ok($response, 'Captcha response retrieved.');
    }
}
