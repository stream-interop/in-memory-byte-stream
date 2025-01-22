<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ValueError;

class ReadableFileStreamTest extends TestCase
{
    public function newReadableFileStream() : ReadableFileStream
    {
        return new ReadableFileStream($this->fopenFakeFile('r'));
    }

    public function testNotSeekable() : void
    {
        $resource = popen('ls', 'r');
        assert(is_resource($resource));
        fread($resource, 1); // avoid broken pipe by reading at least one char
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Resource is not seekable.');
        $stream = new ReadableFileStream($resource);
    }

    public function testNotReadable() : void
    {
        $resource = fopen($this->fakeFile(), 'a');
        assert(is_resource($resource));
        $this->expectException(ValueError::CLASS);
        $this->expectExceptionMessage('Resource is not readable.');
        $stream = new ReadableFileStream($resource);
    }

    public function test__toString() : void
    {
        $stream = $this->newReadableFileStream();
        $stream->seek(8);
        $expect = file_get_contents($this->fakeFile());
        $actual = (string) $stream;
        $this->assertSame($expect, $actual);
    }

    public function testPosition() : void
    {
        $stream = $this->newReadableFileStream();
        $this->assertFalse($stream->eof());
        $this->assertSame(0, $stream->tell());
        $stream->seek(8);
        $this->assertSame(8, $stream->tell());
        $stream->rewind();
        $this->assertSame(0, $stream->tell());
    }

    public function testRead() : void
    {
        $stream = $this->newReadableFileStream();
        $expect = 'The quick brown fox';
        $actual = $stream->read(19);
        $this->assertSame($expect, $actual);
    }

    public function testGetContents() : void
    {
        $stream = $this->newReadableFileStream();
        $expect = file_get_contents($this->fakeFile());
        $actual = $stream->getContents();
        $this->assertSame($expect, $actual);
    }

    public function testEof() : void
    {
        $stream = $this->newReadableFileStream();
        $this->assertFalse($stream->eof());
        $size = $stream->getSize();
        assert($size > 0);
        $stream->read($size);
        $this->assertFalse($stream->eof());
        $stream->read(1);
        $this->assertTrue($stream->eof());
    }
}
