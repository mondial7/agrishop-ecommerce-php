<?php

$currentPage="";

$page_title="About - Agrishop v2.0";
$metatags=[
      			[
      				"kind"=>"link",
      				"type"=>"text/css",
      				"rel"=>"stylesheet",
      				"href"=>"app/assets/css/about.css"
      			]
			];
$footer_scripts=[];

// Include header and footer controllers
include 'page__init.php';


// Set template variables
$template_variables['page_title'] = $page_title;
$template_variables['metatags'] = $metatags;
$template_variables['footer_scripts'] = $footer_scripts;

// Render the template
EKETwig::show("about.twig", $template_variables);
