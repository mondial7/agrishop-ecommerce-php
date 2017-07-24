<?php

// evaluate the pretty parameters

// Include first set of products ? (based on parameters too)

// Load the whole results
//require_once MODELS_DIR . '/Search.php';
// Call new method for getting the produtcs
//$template_variables['products'] = (new Search())->getProducts();

require_once MODELS_DIR . '/Area.php';
require_once MODELS_DIR . '/Category.php';
//require_once MODELS_DIR . '/Year.php';
//require_once MODELS_DIR . '/Month.php';
$template_variables['areas'] = (new Area())->getAreas();
$template_variables['categories'] = (new Category())->getCategories();
//$template_variables['years'] = (new Year())->getYears();
//$template_variables['months'] = (new Month())->getMonths();


// Include header and footer controllers
include 'page__init.php';

// Set template variables
$template_variables['page_title'] = "Search - Agrishop";

// Render the template
EKETwig::show("search.twig", $template_variables);
