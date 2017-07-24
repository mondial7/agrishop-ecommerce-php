<?php

class Farmpicture extends EKEApiController {

  /**
   * Main method, automatically run
   */
  public function run(){

    global $userLogged;

    // logged users only
    if (!$userLogged) {

      $this->response = $this->ERR_NOT_LOGGED;
      return $this;

    }

    if (!isset($_FILES['picture']) || empty($_FILES['picture'])) {

      $this->response = $this->ERR_BAD_REQUEST;
      return $this;

    }

    // Include and Initialize Upload and Account Functions
    require_once MODELS_DIR . '/Upload.php';
    require_once MODELS_DIR . '/ResourcesUtility.php';
    $Upload = new Upload();

    // Real directory is obfuscated to client
    $dir_client = "show/farm-profile/img/";
    $dir = DOCS_DIR . "/farm/profile/";


    // VALIDATION CHECKS

    // Exit with an error if the file content is not set
    if (!isset($_FILES['picture']) || empty($_FILES['picture'])) {

        $this->reponse = $this->setNotify("No file selected. " . $_FILES['picture']["tmp_name"]);
        return $this;

    } else {

        $pic = $_FILES['picture'];

    }


    // Check file extensions
    if (!ResourcesUtility::isPicture($pic['tmp_name'])) {

        $this->response = $this->setNotify("File extension not supported.");
        return $this;

    }

    if (($pic['size'] >= 2097152) || ($pic['size'] == 0)) {

       $this->response = $this->setNotify('File too large. File must be less than 2 megabytes.');
       return $this;

    }

    // Look for errors
    if ($pic["error"] > 0) {

        // Collect errrors
        $errors = "Error: " . $_FILES["picture"]["error"];
        // Send email to notify for uploads errors
        mail("agrishop@thatsmy.name", "Upload errors", $errors);

        $this->response = $this->setNotify($errors);
        return $this;

    }


    // Check directory
    if (!is_dir($dir)) {

        // Send error email to devs
        mail("agrishop@thatsmy.name", "Upload Error", "$dir is not a directory.");

        $message = "We are sorry, at the moment the service is not available. Please try later.";
        $this->response = $this->setNotify($message);
        return $this;

    }


    // STORING PICTURE

    // Rename file with the hash of the farm id (it's not going to change)
    $pic["name"] = $this->renameBasename($pic["name"], md5(Session::get('id')));

    // Save the file
    $Upload->setDir( $dir );
    $Upload->setFileName( $pic["name"] );
    $Upload->setTemporaryName( $pic["tmp_name"] );
    $Upload->setReplace( TRUE );

    if ($Upload->store()) {

      $this->response = $this->renderPictureProfile( $pic["name"], $dir_client );

    } else {

      $this->response = $this->setNotify("Error while uploading..");

    }

  	return $this;

  }

  /**
   * Alert a message in the parent window
   *
   * @param string
   * @return string : js callback
   */
  private function setNotify($text){

      return "<script>parent.notify_callback('$text');</script>";

  }


  /**
   * Rename file without loosing extension
   *
   * @param string filename
   * @param string new basename
   * @return string new filename
   */
  private function renameBasename($filename, $basename){

      return $basename . "." . pathinfo($filename, PATHINFO_EXTENSION);

  }

  /**
   * Render the picture uploaded in the parent window
   *
   * @param string
   * @param string
   * @return string : js callback
   */
  private function renderPictureProfile($filename, $dir){

      return "<script>parent.render_picture_callback('$filename','$dir');</script>";

  }

}
