# WPU Internal Links

Handle internal links in content


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
* [ ] Admin page.
* [ ] Only on first link ?
* [ ] Add multiples spellings.
* [ ] Disable on a post.
* [ ] Select post types.
* [ ] Apply on content/excerpt.
* [ ] Custom layout on links ( class )
* [ ] Handle accents.
* [ ] Hook for default attributes.
