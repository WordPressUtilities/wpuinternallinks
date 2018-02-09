<?php

class WPUInternalLinks_Conversion_Test extends PHPUnit_Framework_TestCase {

    /* Basic conversion */
    public function testBasicString() {
        $content = 'this <a href="#">is</a> a Test <a href="#">in</a> my tests.';
        $links = array(array('url' => 'http://google.fr', 'string' => 'test'));
        $content_after = 'this <a href="#">is</a> a <a class="wpuinternallink" href="http://google.fr">Test</a> <a href="#">in</a> my tests.';
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
        $content_after = 'this is a string for my <a class="wpuinternallink" href="http://google.fr">test</a>s.';
        $links = array(array('url' => 'http://google.fr', 'string' => 'test', 'target_word' => false));
        $content_test = wpuinternallinks_test_content_after($links, $content);
        $this->assertEquals($content_test, $content_after);
    }

    /* Multiple strings */
    public function testMultipleStrings() {
        $content = 'it’s not git_hub but github.';
        $links = array(array('url' => 'http://google.fr', 'strings' => array('github','git_hub')));
        $content_after = 'it’s not <a class="wpuinternallink" href="http://google.fr">git_hub</a> but <a class="wpuinternallink" href="http://google.fr">github</a>.';
        $content_test = wpuinternallinks_test_content_after($links, $content);
        $this->assertEquals($content_test, $content_after);
    }

}
