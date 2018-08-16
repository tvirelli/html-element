<?php

namespace Lukaswhite\HtmlElement;

/**
 * Class HtmlElement
 *
 * Represents, just as the name suggests, an HTML element. This allows developers to create
 * HTML elements programmatically.
 *
 * e.g.
 *
 *   $heading = new HtmlElement( 'h2' );
 *   $heading->set( 'class', 'heading-2' )
 *     ->setContent( 'Heading Two' );
 *
 *   print $heading->render( );
 *   // outputs <h2 class="heading-2">Heading Two</h2>
 *
 * or
 *
 * $heading = new HtmlElement( 'h2', 'Heading Two );
 *   $heading->set( 'class', 'heading-2' );
 *
 * or
 *
 * $p = new HtmlElement( 'p' );
 * $p->setContent( new HtmlElement( 'span', 'the content' ) );
 *
 *
 * @package Lukaswhite\HtmlElement
 */
class HtmlElement
{
    /**
     * The type of element (e.g. p, h1, div etc)
     * @var string
     */
    private $type;

    /**
     * The element's attributes
     *
     * @var array
     */
    private $attributes;

    /**
     * The content of the element, if applicable
     *
     * @var string
     */
    private $content;

    /**
     * An array of elements that are self closing; e.g. <input type="text" />
     *
     * @var array
     */
    private $selfClosers = [
        'input',
        'img',
        'hr',
        'br',
        'meta',
        'link',
        'iframe',
    ];

    /**
     * HtmlElement constructor.
     *
     * @param string $type
     * @param HtmlElement|string $content
     */
    public function __construct( $type, $content = null )
    {
        $this->type = trim( strtolower( $type ) );

        if ( $content ) {
            $this->setContent( $content );
        }
    }

    /**
     * Add a tag to the list of self closers.
     *
     * @param string $element
     * @return $this
     */
    public function addSelfCloser( $element )
    {
        if ( ! in_array( $element, $this->selfClosers ) ) {
            $this->selfClosers[ ] = $element;
        }
        return $this;
    }

    /**
     * Set the content
     *
     * @param HtmlElement|string $content
     * @return $this
     */
    public function setContent( $content )
    {
        $this->content = '';
        $this->appendContent( $content );
        return $this;
    }

    /**
     * Append content
     *
     * @param HtmlElement|string $content
     * @return $this
     */
    public function appendContent( $content )
    {
        if ( is_object( $content ) && $content instanceof self )
        {
            $this->content .= $content->render( );
        } else {
            $this->content .= $content;
        }
        return $this;
    }

    /**
     * Get the value of an attribute
     *
     * @param string $attribute
     * @return string|null
     */
    public function get( $attribute )
    {
        return isset( $this->attributes[ $attribute ] ) ? $this->attributes[ $attribute ] : null;
    }

    /**
     * Set the values of one or more attributes
     *
     * @param string|array $attribute
     * @param string $value
     * @return $this
     */
    public function set( $attribute, $value = '' )
    {
        if( ! is_array( $attribute ) )
        {
            $this->attributes[ $attribute ] = $value;
        }
        else
        {
            $this->attributes = $this->attributes ? array_merge( $this->attributes, $attribute ) : $attribute;
        }
        return $this;
    }

    /**
     * Remove an attribute
     *
     * @param string $attribute
     * @return $this
     */
    public function remove( $attribute )
    {
        if ( isset( $this->attributes[ $attribute ] ) )
        {
            unset( $this->attributes[ $attribute ] );
        }
        return $this;
    }

    /**
     * Clear the element (i.e. its attributes and / or content)
     *
     * @return $this
     */
    public function clear()
    {
        $this->attributes = [ ];
        $this->content = null;
        return $this;
    }

    /**
     * Build the element
     *
     * @return string
     */
    public function build()
    {
        // Open the tag
        $build = sprintf( '<%s', $this->type );

        //add attributes
        if ( $this->attributes && count( $this->attributes ) )
        {
            foreach( $this->attributes as $key => $value)
            {
                $build.= sprintf(
                    ' %s="%s"',
                    $key,
                    $value
                );
            }
        }

        //closing
        if( ! in_array( $this->type, $this->selfClosers ) )
        {
            if ( $this->content && strlen( $this->content ) ) {
                $build.= sprintf( '>%s</%s>', $this->content, $this->type );
            } else {
                $build.= sprintf( '></%s>', $this->type );
            }
        }
        else
        {
            $build .= ' />';
        }

        //return it
        return $build;
    }

    /**
     * Output the element, as a string
     *
     * @return string
     */
    public function render()
    {
        return $this->build( );
    }

    /**
     * Magic to string method
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->build( );
    }

}