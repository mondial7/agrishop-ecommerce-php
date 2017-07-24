<?php

/**
 * Attempts class
 *
 * Attemps are stored in files in the attempts folder
 * the files are formatted in json and named according
 * to the client IP and the client session_id
 */

class Attempts {

	/**
	 * @var int
	 */
	private $MAX_ATTEMPTS = 10,
					$MAX_ATTEMPTS_IN_A_ROW = 3;

	/**
	 * seconds between attempt and current
	 * time to consider it risky
	 * @var int
	 */
	private $lapse = 90;

	/**
	 * @var string ip
	 */
	private $client_ip;

  /**
   * @var string session_id
   */
  private $client_session;

  /**
   * @var string
   */
  private $path, $folder;

  /**
   * @var array
   */
  private $attempts;


  public function  __construct() {

    // set folder
    $this->folder = APP_DIR . '/attempts/';

    // set client ip and session_id
    $this->client_ip = $_SERVER['REMOTE_ADDR'];
    $this->client_session = session_id();

    // set path to store attempts
    $this->path = $this->buildPath();

    // load attempts
    $this->attempts = $this->loadAttempts();

  }

	/**
	 * Track attempts, currently we are calling this
   * on wrong attempts only and we reset once the user logs in.
	 *
   * @return boolean
	 */
	public function track() {

    // append new attempt to attempts array
		$this->attempts[] = time();

    // store array
		return $this->push();

	}

	/**
	 * Call after a successful login is performed.
	 * Empty the the file with log attempts (only prevention).
	 * Delete the file.
	 *
	 * @return boolean
	 */
	public function reset() {

		// reset attempts file
		$this->attempts = [];

    // overrite empty array
		$this->push();

		// delete the file
		$deletion = unlink($this->path);

		return ($deletion !== FALSE && $deletion !== 0);

	}

	/**
	 * Check if the number of attempts is low enough
	 * and the user can proceed to login
	 *
	 * @return boolean
	 */
	public function valid() {

		if (count($this->attempts) >= $this->MAX_ATTEMPTS) {

			return false;

		}

		$count = 0;

    foreach ($this->attempts as $attempt) {

      // time and attempts are given in seconds
      if ((time() - $attempt) <= $this->lapse) {

        $count++;

      }

    }

		return $count < $this->MAX_ATTEMPTS_IN_A_ROW;

	}

	/**
	 * Create the path
	 *
	 * @return string
	 */
	private function buildPath() {

    $filename = $this->client_ip . '_' . $this->client_session . '.json';

		return $this->folder . $filename;

	}

  /**
   * Store array of attempts
   * if file exists then it's overrinden
   *
   * @return boolean
   */
  private function push() {

    // convert in json the array
    $data = json_encode($this->attempts);

    // push the array data in the local file
    return (file_put_contents($this->path, $data) !== FALSE);

  }

  /**
   * Load attempts from the local file
   *
   * @return array
   */
  private function loadAttempts() {

    $exists = file_exists($this->path);

    return $exists ? json_decode(file_get_contents($this->path), true) : [];

  }

}
