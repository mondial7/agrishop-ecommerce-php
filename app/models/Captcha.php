<?php


class Captcha {

    /**
     * @var string secret key
     */
    private $secret = '6LcTcSEUAAAAAANrIGkAn9qbr-iMzskSCSXwsBWy';


    public function __construct($key) {

      $this->key = $key;

    }

    /**
     * Send post call to google api
     * to check reCaptcha validity
     *
     * @return boolean
     */
    public function isValid() {

      // validate reCaptcha
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = ['secret' => $this->secret,
               'response' => $this->key,
               'remoteip' => $_SERVER['REMOTE_ADDR']];
      $options = [
                  'http' => [
                              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                              'method'  => 'POST',
                              'content' => http_build_query($data)
                            ]
                 ];

      $result = file_get_contents($url, false, stream_context_create($options));

      if ($result === false) {

        /* Handle exception */

      } else {

        $result = json_decode($result,true);

      }

      return ($result['success'] == 'true');

    }

}
