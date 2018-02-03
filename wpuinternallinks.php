<?php

/*
Plugin Name: WPU Internal links Metas
Plugin URI: https://github.com/WordPressUtilities/wpuinternalinks
Description: Handle internal links in content
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUInternalLinks {
    private $links = array();
    private $stored_strings = array();

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
    }

    public function plugins_loaded() {

        /* Set links */
        $this->links = apply_filters('wpuinternallinks__links', array());

        /* - No links : stop plugin */
        if (empty($this->links) || !is_array($this->links)) {
            return;
        }

        /* - Clean links */
        $links = $this->links;
        $this->links = array();
        foreach ($links as $link) {
            if (!isset($link['url'])) {
                continue;
            }
            if (!isset($link['string'])) {
                continue;
            }
            $this->links[] = $link;
        }

        /* Add replacement in content */
        add_filter('the_content', array(&$this, 'the_content'));
    }

    public function the_content($content) {
        foreach ($this->links as $link) {
            $content = $this->insert_link_in_text($link, $content);
        }
        return $content;
    }

    /* ----------------------------------------------------------
      Link replacement
    ---------------------------------------------------------- */

    public function insert_link_in_text($link, $text) {
        $this->stored_strings = array();

        /* Isolate some tags without interest */
        $text = $this->isolate_html_tags($text, 'a');
        $text = $this->isolate_html_tags($text, 'button');

        /* Isolate tag content to avoid ID or attribute replacement */
        $text = $this->isolate_tag_contents($text);

        /* Replace terms */
        $link_html = '<a href="' . $link['url'] . '">' . $link['string'] . '</a>';
        $text = str_replace($link['string'], $link_html, $text);

        /* Remove isolated strings */
        $text = $this->deisolate_strings($text);

        return $text;
    }

    public function isolate_html_tags($text, $tag = 'a') {
        preg_match_all('/<a(.*)<\/a>/is', $text, $matches);
        foreach ($matches[0] as $i => $match) {
            $match_id = '#_' . $tag . $i . '_#';
            $text = str_replace($match, $match_id, $text);
            $this->stored_strings[$match_id] = $match;
        }
        return $text;
    }

    public function isolate_tag_contents($text) {
        preg_match_all('/<([^>]*)>/is', $text, $matches);
        foreach ($matches[0] as $i => $match) {
            $match_id = '#_' . $i . '_#';
            $text = str_replace($match, $match_id, $text);
            $this->stored_strings[$match_id] = $match;
        }
        return $text;
    }

    public function deisolate_strings($text) {
        foreach ($this->stored_strings as $match_id => $match) {
            $text = str_replace($match_id, $match, $text);
        }
        return $text;
    }
}

$WPUInternalLinks = new WPUInternalLinks();
