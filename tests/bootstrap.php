<?php

define('WPUINTERNALLINKS_DEBUG', 1);
function add_action() {
    return 1;
}
function esc_attr($str) {
    return $str;
}
function get_post(){
    return false;
}
function get_locale() {
    return 'en_EN';
}

include dirname(__FILE__) . '/../wpuinternallinks.php';

function wpuinternallinks_test_content_after($links, $content){
    $WPUInternalLinks = new WPUInternalLinks;
    $WPUInternalLinks->set_links($links);
    return $WPUInternalLinks->the_content($content);
}
