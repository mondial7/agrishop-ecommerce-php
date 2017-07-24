<?php

class Credentials extends EKEModel {

    /**
     * @var int
     */
    private $temporary_id;


    /**
     * @var string
     */
    private $hashed_password;


    function __construct() {

        parent::__construct();

        // Declare database connection
        $this->connectDB();

    }


    /**
     * Evaluate login
     *
     * @param Profile account to log in
     * @return array account data
     *
     */
    public function login(Profile $user) {

        // set profile attributes
        $userkey = $user->getUserkey();
        $password = $user->getPassword();

        $query = "SELECT id, username, email, password, role, created
                  FROM agrishop__profile
                  WHERE email = ? OR username = ? ";

        $options = [ 'types' => ['ss'], 'params' => [$userkey, $userkey] ];

        $users = $this->db->directQuery($query, $options);

        if ($this->db->getResultNum() !== 1) {

          // We must have only one account
          $user = null;

          // Handle Error/Exception

        } else {

          // get only first result of the array list
          // since we are looking for only one matched account
          $user = $users[0];

          // Match passwords
          require_once MODELS_DIR . '/PasswordUtility.php';
          if (PasswordUtility::match($password, $user['password'])) {

            // store hashed password to generate cookie token if required
            $this->hashed_password = $user['password'];

            // remove from result the password
            unset($user['password']);
            $result = $user;

          }

        }

        // if no account has been found
        // return an empty array
        return $result ?? [];

    }


    /**
     * Evaluate autologin
     *
     * @param string
     * @return array empty or with user data
     */
    public function autoLogin($cookie_token){

        $query = "SELECT a.id, a.email, a.username, a.role, a.created, a.password
                  FROM agrishop__profile a
                  JOIN agrishop__profile_logged al
                  ON a.id = al.profile_id
                  WHERE al.cookie_token = ? ";

        $options = [ 'types' => ['s'], 'params' => [$cookie_token] ];

        // get only first result (since only one result shoud be there)
        $user = $this->db->directQuery($query, $options)[0];

        if ($this->db->getResultNum() !== 1) {

          // We must have only one account
          $user = null;

          // Handle Error/Exception

        } else {

          // match cookie token
          require_once MODELS_DIR . '/PermaLogin.php';
          $PermaLogin = new PermaLogin($user['id'], $user['created'], $user['password']);

          if (!$PermaLogin->match($cookie_token)) {

            // leave result empty

          } else {

            // unset password from result
            unset($user['password']);
            $result = $user;

          }

        }

        // if no account has been found
        // return an empty array
        return $result ?? [];

    }


    /**
     * Store Permanent Login
     *
     * First check if the cookie was already insert
     * to prevent the case of user deleting cookies
     * without logging out
     *
     * @param int account id
     * @param string cookie token
     * @return boolean success status
     */
    public function addPermaLogin($cookie_token = null) {
  
        $PermaLogin = $this->retrievePermaLogin();

        if (is_null($cookie_token)) {

          $cookie_token = $PermaLogin->getToken();

        }

        // if cookie already exists we don't execute the insert query
        if ($this->existsPermaLogin($cookie_token)) {

          $PermaLogin->addCookie();
          return true;

        }

        $query = "INSERT INTO agrishop__profile_logged (profile_id, cookie_token)
                  VALUES ( ? , ? );";

        $options = [ 'types' => ['is'], 'params' => [Session::get('id'), $cookie_token] ];

        $this->db->directQuery($query, $options);

        if ($result = ($this->db->getAffectedNum() === 1)) {

          // Set Cookie
          $PermaLogin->addCookie();

        }

        return $result;

    }


    /**
     * Remove permanent login
     *
     * Delere account logged record
     *
     * @param string
     * @return boolean
     */
    public function removePermaLogin($cookie_token) {

        $query = "DELETE FROM agrishop__profile_logged
                  WHERE profile_id = ? AND cookie_token = ? ;";

        $options = [ 'types' => ['is'], 'params' => [Session::get('id'), $cookie_token] ];

        $this->db->directQuery($query, $options);

        return ($this->db->getAffectedNum() === 1);

    }


    /**
     * Check if perma login has already been stored
     *
     * @param string cookie_token
     * @return boolean
     */
    private function existsPermaLogin($cookie_token = null) {

      if (is_null($cookie_token)) {

        $cookie_token = ($this->retrievePermaLogin())->getToken();

      }

      $query = "SELECT id FROM agrishop__profile_logged WHERE cookie_token = ? ";

      $options = [ 'types' => ['s'], 'params' => [$cookie_token] ];

      $this->db->directQuery($query, $options)[0];

      if ($this->db->getResultNum() === 1) {

        return true;

      }

      return false;

    }


    /**
     * Get instance of PermaLogin model
     *
     * @return PermaLogin
     */
    private function retrievePermaLogin() {

      require_once MODELS_DIR . '/PermaLogin.php';
      return (new PermaLogin(Session::get('id'),
                             Session::get('created'),
                             $this->hashed_password));

    }

    /**
     * Register new user
     *
     * @param Account new account to register
     * @return boolean status of registration
     *
     */
    public function register(Profile $user){

        $email = $user->getEmail();
        $username = $user->getUsername();
        $password = $user->getPassword();
        $role = $user->getRole();

        /************************************
         *
         * WARNING
         *
         * Password is stil in plain here
         * just take care
         ************************************
         */

        // hash password
        require_once MODELS_DIR . '/PasswordUtility.php';
        $password = PasswordUtility::hash($password);

        $query = "INSERT INTO agrishop__profile (email, username, password, role)
                  VALUES ( ? , ? , ? , ? );";

        $options = ['types' => ['ssss'], 'params' => [$email, $username, $password, $role]];

        $this->db->directQuery($query, $options);

        return ($this->db->getAffectedNum() === 1);

    }


    /**
     * Check if email already exists
     *
     * @param string email
     * @return boolean
     */
    public function emailExists($email) {

        $query = "SELECT id FROM agrishop__profile WHERE email = ? ;";

        $options = [ 'types' => ['s'], 'params' => [$email] ];

        $result = $this->db->directQuery($query, $options);

        if ($result && $this->db->getResultNum() > 0) {

            $this->temporary_id = $result['id'];
            return true;

        }

        return false;

    }


    /**
     * Check if username already exists
     *
     * @param string username
     * @return boolean
     */
    public function usernameExists($username) {

        $query = "SELECT id FROM agrishop__profile WHERE username = ? ;";

        $options = [ 'types' => ['s'], 'params' => [$username] ];

        $result = $this->db->directQuery($query, $options);

        if ($result && $this->db->getResultNum() > 0) {

            $this->temporary_id = $result['id'];
            return true;

        }

        return false;

    }

    /**
     * Verify user password
     * User need to input password to proceed
     * for critical operations
     * i.e. delete profile, checkout..
     *
     * @param Profile
     * @return boolean
     *
     */
    public function verify(Profile $user) {

        // get profile attributes
        $password = $user->getPassword();

        $query = "SELECT id, password FROM agrishop__profile
                  WHERE id = ? AND email = ? ";

        $options = [ 'types' => ['is'],
                     'params' => [Session::get('id'), Session::get('email')] ];

        $users = $this->db->directQuery($query, $options);

        if ($this->db->getResultNum() === 1) {

          // Match passwords
          require_once MODELS_DIR . '/PasswordUtility.php';
          if (PasswordUtility::match($password, $users[0]['password'])) {

            return true;

          }

        }

        return false;

    }










    /**
     * Reset Password
     */
    public function reset_password($address){

        if (!$this->emailExists($address)) {
            return false;
        }

        $result = false;

        $old_password = $this->getPassword($address);
        $new_password = $this->generateResetPasswordHash($address);
        $secret_link = "https://startuppuccino.com/reset/" . $this->temporary_id . "/" . $new_password;

        $query = "UPDATE " . _T_ACCOUNT . "
                  SET password = ?
                  WHERE email = ? ";

        // Set email
        $email = new EKEMail();
        $email->setMail($address, "Reset Password - Startuppuccino");
        $email->setMessage("As you asked we are resetting your password, please follow the link in order to set your new password " . $secret_link);
        $email->setFrom("Startuppuccino <info@startuppuccino.com>");

        if ($stmt = $this->db->prepare($query)) {

            $stmt->bind_param('ss', $new_password, $address);
            $stmt->execute();

            if ($stmt->affected_rows == 1) {

                if ($email->send()) {

                    $result = true;

                } else {

                    // Restore the old password
                    $stmt->bind_param('ss', $old_password, $address);
                    $stmt->execute();

                    if ($stmt->affected_rows != 1) {
                        // catch error
                    }

                }

            }

            $stmt->close();

        }

        return $result;

    }

    /**
     * Get account password based on email
     *
     * @param string email
     * @return string password
     */
    private function getPassword($email){

        $query = "SELECT password
                  FROM " . _T_ACCOUNT . "
                  WHERE email = ? ";

        if ($stmt = $this->db->prepare($query)) {

            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {

                $password = $result->fetch_assoc()['password'];

            }

            $stmt->close();

        }

        return $password;

    }

    /**
     * Get account email based on id
     *
     * @param int id
     * @return string password
     */
    public function getEmail($id){

        $query = "SELECT email
                  FROM " . _T_ACCOUNT . "
                  WHERE id = ? ";

        if ($stmt = $this->db->prepare($query)) {

            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {

                $email = $result->fetch_assoc()['email'];

            }

            $stmt->close();

        }

        return $email;

    }

    /**
     * Set new password
     *
     * @param string
     * @return boolean
     */
    public function setNewPassword($password){

        $result = false;
        $account_id = Session::get('temp_user_id');
        $address = Session::get('temp_user_email');

        $old_password = $this->getPassword($address);
        $new_password = $this->generateResetPasswordHash($address);
        $secret_link = "https://startuppuccino.com/reset/" . $this->temporary_id . "/" . $new_password;

        $query = "UPDATE " . _T_ACCOUNT . "
                  SET password = ?
                  WHERE email = ?
                  AND id = ? ";

        // Set email
        $email = new EKEMail();
        $email->setMail($address, "New Password - Startuppuccino");
        $email->setMessage("Your password has been correctly updated.");
        $email->setFrom("Startuppuccino <info@startuppuccino.com>");

        if ($stmt = $this->db->prepare($query)) {

            $stmt->bind_param('ssi', $password, $address, $account_id);
            $stmt->execute();

            if ($stmt->affected_rows == 1) {

                $result = true;

                if (!$email->send()) {
                    // catch error
                }

            }

            $stmt->close();

        }

        return $result;

    }

    /**
     * Generate the hash to reset the password
     *
     * @return string
     */
    private function generateResetPasswordHash($email){

        require_once __DIR__ . "/PasswordUtility.php";

        return PasswordUtility::hash(md5('sp_secret') . $email);

    }


    /**
      * Generate a random password
      */
    public function generatePassword(){

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 8; $i++) {
            $n = mt_rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);

    }


}
