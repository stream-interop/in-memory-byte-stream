<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ValueError;

class ConsumableFileStreamTest extends TestCase
{
    public function newConsumableFileStream() : ConsumableFileStream
    {
        $resource = $this->fopenFakeFile('r');
        assert(is_resource($resource));
        return new ConsumableFileStream($resource);
    }

    public function testNotReadable() : void
    {
        $resource = fopen($this->fakeFile(), 'a');
        assert(is_resource($resource));
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Resource is not readable.');
        $stream = new ReadableFileStream($resource);
    }

    public function testRead() : void
    {
        $stream = $this->newConsumableFileStream();
        $expect = 'The quick brown fox';
        $actual = $stream->read(19);
        $this->assertSame($expect, $actual);
    }

    public function testGetContents() : void
    {
        $stream = $this->newConsumableFileStream();
        $expect = file_get_contents($this->fakeFile());
        $actual = $stream->getContents();
        $this->assertSame($expect, $actual);
    }

    public function testEof() : void
    {
        $stream = $this->newConsumableFileStream();
        $this->assertFalse($stream->eof());
        $size = $stream->getSize();
        assert($size > 0);
        $stream->read($size);
        $this->assertFalse($stream->eof());
        $stream->read(1);
        $this->assertTrue($stream->eof());
    }
}
