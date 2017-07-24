<?php

if ($userLogged) {
    header("Location: ../");
    exit;
}


$currentPage = "register";
$page_title = "Register - Agrishop";
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



if (isset($_POST['submit'])) {

    // Include register function
    require_once CONTROLLERS_DIR . '/_register.php';
    $registration_ = register($_POST['email'],
                              $_POST['username'],
                              $_POST['role'],
                              $_POST['password'],
                              $_POST['password_check'],
                              $_POST['g-recaptcha-response']);

    if ($registration_["status"]) {

        // automatic login
        require_once CONTROLLERS_DIR . '/_login.php';
        if (login($_POST['email'], $_POST['password'], true)) {

            // Redirect to home page
            header("Location: ../");
            // Client redirect if header fails
            echo "<script>window.location='../'</script>";

        }

    } else {

        $template_variables['error_message'] = $registration_['error_message'];
        $template_variables['placeholder'] = $registration_['data'];

    }

}


// Include header and footer controllers
include 'page__init.php';

// Set template variables
$template_variables['page_title'] = $page_title;
$template_variables['metatags'] = $metatags;
$template_variables['footer_scripts'] = $footer_scripts;

// Render the template
EKETwig::show('register.twig', $template_variables);
