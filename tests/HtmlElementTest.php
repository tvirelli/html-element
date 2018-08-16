<?php

use Lukaswhite\HtmlElement\HtmlElement;

class HtmlElementTest extends \PHPUnit\Framework\TestCase
{
    public function testCreatingNewinstance( )
    {
        $el = new HtmlElement( 'h1' );
        $this->assertInstanceOf( HtmlElement::class, $el );
    }

    public function testSettingContentInConstructer( )
    {
        $el = new HtmlElement( 'h1', 'This is the title' );
        $this->assertEquals( '<h1>This is the title</h1>', $el->render( ) );
    }

    public function testSettingStringContent( )
    {
        $el = new HtmlElement( 'h1' );
        $el->setContent( 'This is the title' );
        $this->assertEquals( '<h1>This is the title</h1>', $el->render( ) );
    }

    public function testSettingContentAsElement( )
    {
        $span = new HtmlElement( 'span', 'span content' );
        $p = new HtmlElement( 'p' );
        $p->setContent( $span );
        $this->assertEquals( '<p><span>span content</span></p>', $p->render( ) );
    }

    public function testDefaultSelfCloser( )
    {
        $el = new HtmlElement( 'br' );
        $this->assertEquals( '<br />', $el->render( ) );
    }

    public function testAddingAdditionalSelfClosers( )
    {
        $el = new HtmlElement( 'foo' );
        $el->addSelfCloser( 'foo' );
        $this->assertEquals( '<foo />', $el->render( ) );
    }

    public function testIFrameGetsSelfClosed( )
    {
        $el = new HtmlElement( 'iframe' );
        $this->assertEquals( '<iframe />', $el->render( ) );
    }

    public function testAppendingContent( )
    {
        $el = new HtmlElement( 'p' );
        $el->setContent( 'This is some text' );
        $el->appendContent( ', this is some more' );
        $this->assertEquals( '<p>This is some text, this is some more</p>', $el->render( ) );
    }

    public function testAppendingContentAsObject( )
    {
        $el = new HtmlElement( 'p' );
        $el->setContent( 'This is some text' );
        $el->appendContent( ( new HtmlElement( 'span' ) )->setContent( ', this is some more' ) );
        $this->assertEquals( '<p>This is some text<span>, this is some more</span></p>', $el->render( ) );
    }

    public function testSettingAndGettingAttributes( )
    {
        $el = new HtmlElement( 'div' );
        $el->set( 'class', 'my-class-name' )
            ->set( 'id', 'big-div' );

        $rendered = $el->render( );
        $attributes = $this->getElementAttributes( $rendered );
        $this->assertArrayHasKey( 'class', $attributes );
        $this->assertEquals( 'my-class-name', $attributes[ 'class' ] );
        $this->assertArrayHasKey( 'id', $attributes );
        $this->assertEquals( 'big-div', $attributes[ 'id' ] );

        $this->assertEquals( 'my-class-name',$el->get( 'class' ) );

    }

    public function testSettingMultipleAttributes( )
    {
        $el = new HtmlElement( 'div' );
        $el->set(
            [
                'class'     =>  'my-class-name',
                'id'        =>  'big-div'
            ]
        );

        $rendered = $el->render( );
        $attributes = $this->getElementAttributes( $rendered );
        $this->assertArrayHasKey( 'class', $attributes );
        $this->assertEquals( 'my-class-name', $attributes[ 'class' ] );
        $this->assertArrayHasKey( 'id', $attributes );
        $this->assertEquals( 'big-div', $attributes[ 'id' ] );

    }

    public function testMergingAttributes( )
    {
        $el = new HtmlElement( 'div' );
        $el->set(
            [
                'class'     =>  'my-class-name',
                'id'        =>  'big-div'
            ]
        );

        $el->set(
            [
                'data-id'   =>  '123',
            ]
        );

        $rendered = $el->render( );
        $attributes = $this->getElementAttributes( $rendered );
        $this->assertArrayHasKey( 'class', $attributes );
        $this->assertEquals( 'my-class-name', $attributes[ 'class' ] );
        $this->assertArrayHasKey( 'id', $attributes );
        $this->assertEquals( 'big-div', $attributes[ 'id' ] );
        $this->assertArrayHasKey( 'data-id', $attributes );
        $this->assertEquals( '123', $attributes[ 'data-id' ] );

    }

    public function testRemovingAttributes( )
    {
        $el = new HtmlElement( 'div' );
        $el->set( 'class', 'my-class-name' )
            ->set( 'id', 'big-div' );

        $el->remove( 'id' );

        $rendered = $el->render( );

        $attributes = $this->getElementAttributes( $rendered );

        $this->assertArrayHasKey( 'class', $attributes );
        $this->assertEquals( 'my-class-name', $attributes[ 'class' ] );

        $this->assertArrayNotHasKey( 'id', $attributes );

        $this->assertNull( $el->get( 'id' ) );

    }

    public function testClearing( )
    {
        $el = new HtmlElement( 'div', 'the div content' );
        $el->set( 'class', 'my-class-name' )
            ->set( 'id', 'big-div' );
        $el->clear( );
        $this->assertNull( $el->get( 'class' ) );
        $this->assertNull( $el->get( 'id' ) );
        $this->assertEquals( '<div></div>', $el->render( ) );
    }

    public function testCanCastToString( )
    {
        $el = new HtmlElement( 'h1' );
        $el->setContent( 'This is the title' );
        $this->assertEquals( '<h1>This is the title</h1>', ( string ) $el );
    }

    private function getElementAttributes( $str )
    {

        $xml = simplexml_load_string( $str );
        $attributes = [ ];
        foreach($xml->attributes() as $name => $value) {
            $attributes[ $name ] = $value;
        }
        return $attributes;
    }
}