<?php

/**
 * Profile Model
 *
 * parent of Farm & Customer models
 *
 */
class Profile extends EKEEntityModel {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username,
            $email,
            $password,
            $role;

    /**
     * @var date
     */
    private $created;

    /**
     * @var array of strings
     */
    private $roles = ['farm','customer'];


    function __construct(){

      parent::__construct();

      $this->properties = ['id','username','email','password','role','created'];

    }

    /**
     * Set profile id
     */
    public function setId($id) {

      $this->id = $this->cleanNumber($id);

    }

    /**
     * Setter of the username
     *
     * @param string
     * @return void
     */
    public function setUsername($username) {

      $this->username = $this->cleanText($username);

    }

    /**
     * Validate, sanitize and set the account email and validate
     *
     * @param string
     * @return void
     *
     */
    public function setEmail($email) {

      $this->email = $this->validateEmail($email);

    }

    /**
     * Set the account password
     * no need to sanitize or validate since we
     * are going to hash it
     *
     * @param string
     * @return void
     *
     */
    public function setPassword($password) {

      $this->password = $password;

    }

    /**
     * Validate, sanitize and set the account role
     *
     * @param string
     * @return void
     *
     */
    public function setRole($role) {

        $this->role = $this->cleanText($role);

        // Extra check on input (kind of 'enum')
        if (!in_array($this->role, $this->roles)) {

            $this->valid = false;

        }

    }

    /**
     * Set created date
     */
    public function setCreated($date) {

      $this->created = $this->validateDate($date);

    }

    /**
     * Set userkey
     * The userkey is used in the login form
     * this might be the username or the email
     * of the user logging in.
     */
    public function setUserkey($key) {

      $this->userkey = $this->cleanText($key);

    }

    /**
     * Getters
     */

    public function getId(){

      return $this->id;

    }

    public function getUsername(){

      return $this->username;

    }

    public function getEmail(){

      return $this->email;

    }

    public function getPassword(){

      return $this->password;

    }

    public function getRole(){

      return $this->role;

    }

    public function getCreated(){

      return $this->created;

    }

    public function getUserkey(){

      return $this->userkey;

    }

}
