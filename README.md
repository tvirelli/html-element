# HTML Element

A _really_ simple PHP class for creating HTML elements.

```php
$heading = new HtmlElement( 'h2', 'This is a heading' );
$heading->set( 'class', 'is-title' );
print $heading->render( );
// or, because it implements __toString( )
print $heading
```

This will output the following:

```html
<h2 class="is-title">This is a heading</h2>
```

You can also provide another instance when setting content:

```php
$span = new HtmlElement( 'span', 'span content' );
$p = new HtmlElement( 'p' );
$p->setContent( $span );
```