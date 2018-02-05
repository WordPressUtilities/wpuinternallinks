# WPU Internal Links

Handle internal links in content

[![Build Status](https://travis-ci.org/WordPressUtilities/wpuinternallinks.svg?branch=master)](https://travis-ci.org/WordPressUtilities/wpuinternallinks)

## How to

```php
add_filter('wpuinternallinks__links', 'example_wpuinternallinks__links', 10, 1);
function example_wpuinternallinks__links($links) {
    $links[] = array(
        /* Limit to some locales */
        'locales' => array('fr_FR'),
        /* Add an attribute to the converted links */
        'link_attributes' => 'target="_blank"',
        /* Custom classname for converted links */
        'link_classname' => 'my-internal-link',
        'string' => 'github',
        'url' => 'https://github.com'
    );
    return $links;
}
```

## Todo

* [x] Choose exact word ( no letter before or after )
* [x] Add custom attributes to link.
* [x] Handle uppercase letters.
* [x] Choose target language.
* [x] Custom layout on links ( class )
* [x] Hook for default attributes.
* [x] Select post types.
* [ ] Admin page.
* [ ] Only on first link ?
* [ ] Add multiples spellings.
* [ ] Disable on a post (admin).
* [ ] Apply on content/excerpt.
* [ ] Handle accents.
