<?php

/**
 * EntityModel class
 *
 * @todo add class description
 */
abstract class EKEEntityModel extends EKEModel {

    /**
     * Save current object properties status
     * Used in Entity kind models
     *
     * @var boolean
     */
    protected $valid = true;

    /**
     * Array of properties of the object
     * Instatiated in the constructor
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Clean html text input
     *
     * @param string dirty text
     * @param int quote's option
     * @param string encoding
     * @return string cleaned text
     *
     */
    protected function cleanText($text, $quote = ENT_QUOTES, $encoding = 'utf-8') {

      $text_ = $text;

      $text = htmlspecialchars(strip_tags($text), $quote, $encoding);

      if (!empty(trim($text_)) && empty(trim($text))) {

        $this->valid = false;

      }

      return $text;

    }

    /**
     * Clean number
     *
     * @param numeric
     * @return numeric or null
     */
    protected function cleanNumber($number) {

      if (!is_numeric($number)) {

        $this->valid = false;
        return null;

      }

      return $number;

    }


    /**
     * Validate a date
     *
     * @param string date
     * @return string date
     */
    protected function validateDate($date) {

        if (!strtotime($date)) {

            $this->valid = false;

        }

        return date('Y-m-d', strtotime($date));

    }

    /**
     * Check if input is valid email format
     *
     * @param string
     * @return boolean
     */
    protected function validateEmail($email) {

      // Remove all illegal characters from email
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);

      // Validate e-mail
      if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

        $this->valid = false;

      }

      return $email;

    }

    /**
     * Check if input is valid url format
     *
     * @param string
     * @return boolean
     */
    protected function validateUrl($url) {

      // Remove all illegal characters from a url
      $url = filter_var($url, FILTER_SANITIZE_URL);

      // Validate url
      if (filter_var($url, FILTER_VALIDATE_URL) === false) {

        $this->valid = false;

      }

      return $url;

    }

    /**
     * Check if the article values are correct
     *
     * @return boolean
     */
    public function isValid() {

      return $this->valid;

    }

    /**
     * Load article data from array
     *
     * @param array
     * @return Entity Object
     */
    public function loadFromArray($data) {

      foreach ($data as $key => $value) {

        $key = $this->toCamel($key);

        $this->{'set' . $key}($value);

      }

      return $this;

    }

    /**
     * Generate an associative array with Entity data
     *
     * @return array
     */
    public function toArray() {

      $result_ = [];

      foreach ($this->properties as $value) {

        $camel_value = $this->toCamel($value);

        $result_[$value] = $this->{'get' . $camel_value}();

      }

      return $result_;

    }

    /**
     * Convert string to camel case
     *
     * @param string
     * @return string
     */
    private function toCamel($str){

        $str = str_replace("_", "", $str);

        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");

    }

}
