<?php

class Options extends EKEModel {

    // constructor
    public function __construct() {

        parent::__construct();

        // Declare database connection
        $this->connectDB();

    }


    /**
     * Update email of a profile
     *
     * @param Profile
     * @return boolean
     */
    public function updateEmail(Profile $profile) {

      $query = "UPDATE agrishop__profile
                SET email = ? WHERE id = ? ;";

      $options = [ 'types' => ['si'], 'params' => [$profile->getEmail(), Session::get('id')] ];

      $this->db->directQuery($query, $options);

      if ($this->db->getAffectedNum() === 1) {

        $log_data = [
                      "profile" => Session::get('id'),
                      "action" => "update email",
                      "data" => ["email" => $profile->getEmail()]
                    ];

        $this->LOG->save( 'profile', $log_data );

        return true;

      }

      return false;

    }


    /**
     * Update username of a profile
     *
     * @param Profile
     * @return boolean
     */
    public function updateUsername(Profile $profile) {

      $query = "UPDATE agrishop__profile
                SET username = ? WHERE id = ? ;";

      $options = [ 'types' => ['si'], 'params' => [$profile->getUsername(), Session::get('id')] ];

      $this->db->directQuery($query, $options);

      if ($this->db->getAffectedNum() === 1) {

        $log_data = [
                      "profile" => Session::get('id'),
                      "action" => "update username",
                      "data" => ["username" => $profile->getUsername()]
                    ];

        $this->LOG->save( 'profile', $log_data );

        return true;

      }

      return false;

    }


    /**
     * Update User Password
     *
     * @param Profile
     * @param old_password
     * @return boolean
     */
    public function updatePassword(Profile $profile, $old_password) {

      $password = $profile->getPassword();

      if (empty(trim($password))) {

        return false;

      }

      // retrieve and match profile password
      $query = "SELECT id, password
                FROM agrishop__profile
                WHERE id = ? ";

      $options = [ 'types' => ['i'], 'params' => [Session::get('id')] ];

      $users = $this->db->directQuery($query, $options);

      if ($this->db->getResultNum() !== 1) {

        return false;

      } else {

        $user = $users[0];

      }

      // hash passwords model
      require_once MODELS_DIR . '/PasswordUtility.php';

      // Match passwords
      if (!(PasswordUtility::match($old_password, $user['password']))) {

        return false;

      }

      // hash passwords
      $password = PasswordUtility::hash($password);
      $old_password = PasswordUtility::hash($old_password);

      $query = "UPDATE agrishop__profile
                SET password = ? WHERE id = ? ;";

      $options = [ 'types' => ['si'], 'params' => [$password, Session::get('id')] ];

      $this->db->directQuery($query, $options);

      if ($this->db->getAffectedNum() === 1) {

        $log_data = [
                      "profile" => Session::get('id'),
                      "action" => "update password"
                    ];

        $this->LOG->save( 'profile', $log_data );

        return true;

      }

      return false;

    }

}
