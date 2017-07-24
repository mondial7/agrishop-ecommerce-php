<?php

/**
 * Set menu and submenu items
 */
function define_menu() {

    global $userLogged, $currentPage;

    if ($userLogged) {

        return [

            [
                "label" => "Search",
                "link" => "search/"
            ],
            [
                "label" => "Options",
                "link" => "options/"
            ],
            [
                "label" => "Logout",
                "link" => "logout/"
            ]

        ];

    } else {

        return [

            [
                "label" => "Login",
                "link" => "login/"
            ]

        ];

    }

}

/**
 * Add variables to template
 */
$template_variables['menu'] = define_menu();
