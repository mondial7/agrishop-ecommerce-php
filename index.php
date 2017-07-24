<?php

/**
 * Set session cookie properties
 */
// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
ini_set('session.cookie_secure', 1);

/**
 *
 * Define headers for security issues
 * NOTE already defined in the htaccess
 */
//header("X-Frame-Options: SAMEORIGIN");
//header("X-XSS-Protection: 1");

/**
 * Define development mode
 */
define("DEV_MODE", "off");


/**
 * Prevent showing errors and warnings
 * (uncomment while developing)
 */
//error_reporting(0);
//ini_set('display_errors', 'on');


/**
 * Load application core
 */
require __DIR__ . '/app/core/autoload.php';

/**
 * Store memory usage
 */
if (defined("DEV_MODE") && DEV_MODE === "on") {

  $core_memory = round((memory_get_usage()/1024/1024), 3);
  $core_peak = round((memory_get_peak_usage(true)/1024/1024), 3);

}

/**
 * Handle user sessions
 *
 */
require CONTROLLERS_DIR . '/session.php';


/**
 * Define routes
 */
Dump_Router::route('/',[
  'controller' => "landing"
]);

Dump_Router::route('api',[
  'pretty_parameters' => ['feature','action','api_key'] // @todo api_key has to be implemented
]);

Dump_Router::route('search',[
  'pretty_parameters' => ['category','area','owner']
]);

Dump_Router::manyRoute(['about','help','options','add','contacts','login','register','logout','checkout']);

Dump_Router::route('try',[
  'controller' => "orm_try"
]);


Dump_Router::route('show', [
  'pretty_parameters' => ['_s', '_t', '_f']
]);

/*
Dump_Router::route('reset', [
  'controller' => 'reset_password',
  'pretty_parameters' => ['user_id','secret_hash']
]);*/

/**
 * Declare routes where router will not apply
 * Currently helpful for ajax direct calls to controllers
 */
Dump_Router::noRoute('app');


/**
 * Trigger the router and evaluate the uri path
 */
require Dump_Router::loadController($_SERVER['REQUEST_URI'],
                                    "./app/controllers/pages/");


/**
 * Store and show memory usage
 */
if (defined("DEV_MODE") && DEV_MODE === "on") {

  $template_variables = [

    'core_memory' => $core_memory,
    'core_peak' => $core_peak,
    'total_memory' => round((memory_get_usage()/1024/1024), 3),
    'total_peak' => round((memory_get_peak_usage(true)/1024/1024), 3)

  ];

  EKETwig::setDir(VIEWS_DIR . "/components/");
  EKETwig::show("memory_monitor_dev.twig", $template_variables);

}
