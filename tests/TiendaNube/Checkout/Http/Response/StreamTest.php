<?php

namespace TiendaNube\Checkout\Http\Response;

use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testSetContent()
    {
        $stream = new Stream();

        $content = 'teste';
        $size = $stream->write($content);

        $this->assertEquals($content, $stream->getContents());
        $this->assertEquals($size, $stream->getSize());
        $this->assertEquals(strlen($content), $size);
    }
}