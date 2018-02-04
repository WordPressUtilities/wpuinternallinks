<?php

class WPUInternalLinks_Conversion_Test extends PHPUnit_Framework_TestCase {

    /* Basic conversion */
    public function testBasicString() {
        $content = 'this is a Test in my tests.';
        $links = array(array('url' => 'http://google.fr', 'string' => 'test'));
        $content_after = 'this is a <a class="wpuinternallink"  href="http://google.fr">Test</a> in my tests.';
        $content_test = wpuinternallinks_test_content_after($links, $content);
        $this->assertEquals($content_test, $content_after);
    }

    /* Test != test */
    public function testCaseInsensitive() {
        $content = 'That is a Test.';
        $content_after = $content;
        $links = array(array('url' => 'http://google.fr', 'string' => 'test', 'case_insensitive' => false));
        $content_test = wpuinternallinks_test_content_after($links, $content);
        $this->assertEquals($content_test, $content_after);
    }

    /* Target word */
    public function testTargetWord() {
        $content = 'this is a string for my tests.';
        $content_after = 'this is a string for my <a class="wpuinternallink"  href="http://google.fr">test</a>s.';
        $links = array(array('url' => 'http://google.fr', 'string' => 'test', 'target_word' => false));
        $content_test = wpuinternallinks_test_content_after($links, $content);
        $this->assertEquals($content_test, $content_after);
    }

}
