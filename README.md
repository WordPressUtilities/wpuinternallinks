# WPU Internal Links

Handle internal links in content


## How to

```php
add_filter('wpuinternallinks__links', 'example_wpuinternallinks__links', 10, 1);
function example_wpuinternallinks__links($links) {
    $links[] = array(
        'string' => 'Github',
        'url' => 'https://github.com'
    );
    return $links;
}
```

## Todo

* [ ] Admin page.
* [ ] Only on first link ?
* [ ] Add custom attributes to link.
* [ ] Add multiples spellings.
* [ ] Handle uppercase letters.
* [ ] Disable on a post.
* [ ] Select post types.
* [ ] Apply on content/excerpt.
* [ ] Choose target language.
