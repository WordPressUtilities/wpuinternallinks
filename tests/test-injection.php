<?php

class WPUInternalLinks_Injection_Test extends PHPUnit_Framework_TestCase {

    /* Links are correctly injected */
    public function testLinksInjectionOK() {
        $WPUInternalLinks = new WPUInternalLinks;
        $WPUInternalLinks->set_links(array(array('url' => 'http://google.fr', 'string' => 'test')));
        $links = $WPUInternalLinks->get_links();
        $this->assertTrue(count($links) == 1);
    }

    /* Invalid injection is not accepted */
    public function testLinksInjectionNOK() {
        $WPUInternalLinks = new WPUInternalLinks;
        $WPUInternalLinks->set_links(array(array('url' => 'http://google.fr')));
        $links = $WPUInternalLinks->get_links();
        $this->assertTrue(count($links) == 0);
    }


}
