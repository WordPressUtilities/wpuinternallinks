<?php

/*
Plugin Name: WPU Internal links Metas
Plugin URI: https://github.com/WordPressUtilities/wpuinternalinks
Description: Handle internal links in content
Version: 0.2.0
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
        if (!defined('WPUINTERNALLINKS_DEBUG') && (empty($this->links) || !is_array($this->links))) {
            return;
        }

        $this->links = $this->set_links($this->links);

        /* Add replacement in content */
        add_filter('the_content', array(&$this, 'the_content'));
    }

    public function set_links($links_tmp) {
        /* - Clean links */
        $links = array();
        foreach ($links_tmp as $link) {
            if (!isset($link['url'])) {
                continue;
            }
            if (!isset($link['string'])) {
                continue;
            }
            if (!isset($link['case_insensitive'])) {
                $link['case_insensitive'] = true;
            }
            if (!isset($link['target_word'])) {
                $link['target_word'] = true;
            }
            if (!isset($link['link_attributes'])) {
                $link['link_attributes'] = '';
            }
            if (!isset($link['link_classname'])) {
                $link['link_classname'] = 'wpuinternallink';
            }
            if (!isset($link['locales']) || !is_array($link['locales'])) {
                $link['locales'] = array();
            }
            $links[] = $link;
        }
        if (defined('WPUINTERNALLINKS_DEBUG')) {
            $this->links = $links;
        }
        return $links;
    }

    public function get_links() {
        return $this->links;
    }

    /* ----------------------------------------------------------
      Link replacement
    ---------------------------------------------------------- */

    public function the_content($content) {

        $this->stored_strings = array();

        /* Isolate some tags without interest */
        $content = $this->isolate_html_tags($content, 'a');
        $content = $this->isolate_html_tags($content, 'button');

        /* Isolate tag content to avoid ID or attribute replacement */
        $content = $this->isolate_tag_contents($content);

        /* Insert links terms */
        foreach ($this->links as $link) {
            $content = $this->insert_link_in_text($link, $content);
        }

        /* Remove isolated strings */
        $content = $this->deisolate_strings($content);

        return $content;
    }

    public function insert_link_in_text($link, $text) {
        /* Limit to certain locales */
        if (!empty($link['locales']) && !in_array(get_locale(), $link['locales'])) {
            return $text;
        }

        /* Build regex to target string */
        $string_regex = '/';
        if ($link['target_word']) {
            $string_regex .= '[^A-Za-z0-9-_]+';
        }
        $string_regex .= '(' . addslashes($link['string']) . ')';
        if ($link['target_word']) {
            $string_regex .= '[^A-Za-z0-9-]+';
        }
        $string_regex .= '/';
        if ($link['case_insensitive']) {
            $string_regex .= 'i';
        }

        /* Replace all occurrences by a link */
        preg_match_all($string_regex, $text, $matches);
        foreach ($matches[0] as $i => $match) {
            $match_text = $matches[1][$i];

            $link_classname = (!empty($link['link_classname']) ? 'class="' . esc_attr($link['link_classname']) . '"' : '');
            $link_html = '<a ' . $link_classname . ' ' . $link['link_attributes'] . ' href="' . $link['url'] . '">' . $match_text . '</a>';

            /* Replace targetted text in this match only */
            $match_original = $match;
            $match = str_replace($match_text, $link_html, $match);

            /* Then replace the matched string by the new string in full text */
            $text = str_replace($match_original, $match, $text);
        }
        return $text;
    }

    public function isolate_html_tags($text, $tag = 'a') {
        preg_match_all('/<' . $tag . '(.*)<\/' . $tag . '>/is', $text, $matches);
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
