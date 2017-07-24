<?php

if ($userLogged) {
    header("Location: ../");
    exit;
}

// Include login function
require_once CONTROLLERS_DIR . '/_login.php';


$currentPage = "login";
$page_title = "Login - Agrishop";
$metatags = [
                [
                  "kind"=>"link",
                  "type"=>"text/css",
                  "rel"=>"stylesheet",
                  "href"=>"app/assets/css/general.css"
                ],
                [
                    "kind" => "link",
                    "type" => "text/css",
                    "rel"  => "stylesheet",
                    "href" => "app/assets/css/login.css"
                ],
                [
                    "kind" => "link",
                    "type" => "text/css",
                    "rel"  => "stylesheet",
                    "href" => "app/assets/css/form.css"
                ]
            ];
$footer_scripts = [];


$login_data = ["userkey"=>"","password"=>""];


if (isset($_POST['login'])) {

    $isPermaLogin = isset($_POST['permalogin']) && ($_POST['permalogin'] === "y");

    // check number of attempts before performing the login
    require_once MODELS_DIR . '/Attempts.php';
  	$attempts = new Attempts();

  	if (!isset($_POST['g-recaptcha-response']) && !$attempts->valid()) {

      $attempts->track();

  		$loginOk = false;

      $require_captcha = true;

  	} else {

      $reCaptcha = $_POST['g-recaptcha-response'] ?? 0;

      $loginOk = login($_POST['userkey'], $_POST['password'], $isPermaLogin, $attempts, $reCaptcha);

      if ($loginOk) {

          header("Location: ../");
          // Client redirect if header fails
          echo "<script>window.location='../'</script>";

      }

    }

} else if (isset($_POST['reset_password'])) {

    $resetOk = reset_password($_POST['email']);

    if ($resetOk) {

        // Reload the page with a successfull message
        header("Location: ./?reset&reset_done");
        // Client redirect if header fails
        echo "<script>window.location='./?reset&reset_done'</script>";

    }

}


// Include header and footer controllers
include 'page__init.php';


// Set template variables
$template_variables['page_title'] = $page_title;
$template_variables['metatags'] = $metatags;
$template_variables['footer_scripts'] = $footer_scripts;

$template_variables['reset_password'] = isset($_GET['reset']);
$template_variables['reset_password_success'] = isset($_GET['reset_done']);
$template_variables['resetOk'] = $resetOk ?? true;
$template_variables['loginOk'] = $loginOk ?? true;
$template_variables['login_data'] = $login_data;
$template_variables['require_captcha'] = $require_captcha ?? false;

// Render the template
EKETwig::show('login.twig', $template_variables);
