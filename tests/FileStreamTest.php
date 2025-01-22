<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ValueError;

class FileStreamTest extends TestCase
{
    protected function newFileStream()
    {
        return new FileStream($this->fopenFakeFile('r'));
    }

    public function testNotResource() : void
    {
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Expected open resource of type (stream), got string.');
        $stream = new FileStream('foo');
    }

    public function testResourceNotOpen() : void
    {
        $resource = $this->fopenFakeFile('r');
        fclose($resource);
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Expected open resource of type (stream), got resource (closed).');
        $stream = new FileStream($resource);
    }

    public function testResourceNotValid() : void
    {
        $resource = stream_context_create();
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Expected open resource of type (stream), got resource of type (stream-context).');
        $stream = new FileStream($resource);
    }

    public function testGetSize() : void
    {
        $expect = filesize($this->fakeFile());
        $actual = $this->newFileStream()->getSize();
        $this->assertSame($expect, $actual);
    }

    public function testIsOpen() : void
    {
        $stream = $this->newFileStream();
        $this->assertTrue($stream->isOpen());
        fclose($stream->resource);
        $this->assertFalse($stream->isOpen());
    }

    public function testIsClosed() : void
    {
        $stream = $this->newFileStream();
        $this->assertFalse($stream->isClosed());
        fclose($stream->resource);
        $this->assertTrue($stream->isClosed());
    }
}
