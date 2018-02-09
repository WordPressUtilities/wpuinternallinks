<?php

/*
Plugin Name: WPU Internal links
Plugin URI: https://github.com/WordPressUtilities/wpuinternalinks
Description: Handle internal links in content
Version: 0.3.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUInternalLinks {
    private $links = array();
    private $stored_strings = array();

    public function __construct() {
        add_action('wp_loaded', array(&$this, 'wp_loaded'));
    }

    public function wp_loaded() {

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
            if (!isset($link['string']) && !isset($link['strings'])) {
                continue;
            }
            if (!isset($link['strings']) || !is_array($link['strings'])) {
                $link['strings'] = array();
            }
            if (!empty($link['string'])) {
                $link['strings'][] = $link['string'];
            }
            if (empty($link['strings'])) {
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
            if (!isset($link['excluded_ids']) || !is_array($link['excluded_ids'])) {
                $link['excluded_ids'] = array();
            }
            if (!isset($link['post_types']) || !is_array($link['post_types'])) {
                $link['post_types'] = array();
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
        $_post = get_post();

        $this->stored_strings = array();

        /* Isolate some tags without interest */
        $content = $this->isolate_html_tags($content, 'a');
        $content = $this->isolate_html_tags($content, 'button');

        /* Isolate tag content to avoid ID or attribute replacement */
        $content = $this->isolate_tag_contents($content);

        /* Insert links terms */
        foreach ($this->links as $link) {
            $content = $this->insert_link_in_text($link, $content, $_post);
        }

        /* Remove isolated strings */
        $content = $this->deisolate_strings($content);

        return $content;
    }

    public function insert_link_in_text($link, $text, $_post) {
        /* Limit to certain locales */
        if (!empty($link['locales']) && !in_array(get_locale(), $link['locales'])) {
            return $text;
        }

        if (is_object($_post)) {
            /* Limit to a post type */
            $_post_type = get_post_type($_post);
            if (!empty($link['post_types']) && !in_array($_post_type, $link['post_types'])) {
                return $text;
            }

            /* Exclude some post IDs */
            if (!empty($link['excluded_ids']) && in_array($_post->ID, $link['excluded_ids'])) {
                return $text;
            }
        }

        /* Build regex to target string */
        foreach ($link['strings'] as $string) {
            $string_regex = $this->build_regex_from_link($link, $string);

            /* Replace all occurrences by a link */
            preg_match_all($string_regex, $text, $matches);
            foreach ($matches[0] as $j => $match) {
                $match_text = $matches[1][$j];

                $link_classname = (!empty($link['link_classname']) ? 'class="' . esc_attr($link['link_classname']) . '"' : '');
                if (!empty($link['link_attributes'])) {
                    $link_classname .= ' ' . $link['link_attributes'];
                }
                $link_html = '<a ' . $link_classname . ' href="' . $link['url'] . '">' . $match_text . '</a>';

                /* Replace targetted text in this match only */
                $match_original = $match;
                $match = str_replace($match_text, $link_html, $match);

                /* Then replace the matched string by the new string in full text */
                $text = str_replace($match_original, $match, $text);
            }
        }
        return $text;
    }

    public function build_regex_from_link($link, $string) {
        $string_regex = '/';
        if ($link['target_word']) {
            $string_regex .= '[^A-Za-z0-9-_]+';
        }
        $string_regex .= '(' . addslashes($string) . ')';
        if ($link['target_word']) {
            $string_regex .= '[^A-Za-z0-9-]+';
        }
        $string_regex .= '/';
        if ($link['case_insensitive']) {
            $string_regex .= 'i';
        }
        return $string_regex;
    }

    public function isolate_html_tags($text, $tag = 'a') {
        preg_match_all('/<' . $tag . '(.*)<\/' . $tag . '>/isU', $text, $matches);
        foreach ($matches[0] as $i => $match) {
            $match_id = '#_' . $tag . $i . '_#';
            $text = str_replace($match, $match_id, $text);
            $this->stored_strings[$match_id] = $match;
        }
        return $text;
    }

    public function isolate_tag_contents($text) {
        preg_match_all('/<([^>]*)>/isU', $text, $matches);
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
