<?php

require_once MODELS_DIR . '/Cookie.php';

class PermaLogin {


  /**
   * @var string perma login key
   */
  private static $key = 'agrilog';


  /**
   * @var string md5 value of cookie
   */
  private $token;


  public function __construct($id, $created, $password) {

    // initialize the token
    $this->token = $this->generateToken($id, $created, $password);

  }

  /**
   * Generate a new cookie token
   *
   * the cookie is generated as the hash
   * of the account id, time and user_agent
   *
   * @param int account id
   * @return string md5 cookie token value
   */
  private function generateToken($id, $created, $password) {

    // user agent, registration date, hash(id, hashed_password)
    return md5( $_SERVER['HTTP_USER_AGENT'] . $created . md5($id . $password) );

  }


  /**
   * Match given cookie value with generated one
   *
   * @param string cookie value
   * @return boolean
   */
  public function match($cookie_token) {

    return $this->token === $cookie_token;

  }


  /**
   * Get generated token
   */
  public function getToken() {

    return $this->token;

  }

  /**
   * Add permanent login cookie ("Stay logged in")
   *
   * @return sting cookie token
   */
  public function addCookie(){

      return Cookie::add(self::$key, $this->token, 120);

  }


  /**
   * Get permanent login cookie
   *
   * @return string
   */
  public static function getCookie(){

      return Cookie::get(self::$key);

  }


  /**
   * Delete perma login cookie
   *
   * @return boolean
   */
  public static function removeCookie(){

      return Cookie::remove(self::$key);

  }


}
