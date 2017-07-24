<?php

/**
 * Set website base url
 *
 */
if ($_SERVER['HTTP_HOST'] === "localhost" ||
    $_SERVER['HTTP_HOST'] === "localhost:8888") {

    $template_variables['website_url'] = "http://".$_SERVER['HTTP_HOST']."/Information-Security/";

} else {

//    $template_variables['website_url'] = "http://10.0.2.2/Information-Security/";
    $template_variables['website_url'] = "https://thatsmy.name/agrishop/";

}
