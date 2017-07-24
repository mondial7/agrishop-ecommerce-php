<?php

if (!$userLogged) {
    header("Location: ../");
    exit;
}



// check if farm is active
require_once MODELS_DIR . '/FarmManager.php';
$manager = new FarmManager();
$farm_active = $manager->isActive();

$template_variables['farm_is_active'] = $farm_active;

if ($farm_active) {

  require_once MODELS_DIR . '/Farm.php';
  $farm = new Farm();
  $farm->setId(Session::get('id'));

  // get farm details
  $template_variables['farm'] = $manager->getInfo($farm);

} else {

  $template_variables['farm'] = [];

}


// Include header and footer controllers
include 'page__init.php';

// Set template variables
$template_variables['page_title'] = "Options - Agrishop";

// Render the template
EKETwig::show("options.twig", $template_variables);
