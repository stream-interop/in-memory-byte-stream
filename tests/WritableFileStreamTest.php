<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use InvalidArgumentException;

class WritableFileStreamTest extends TestCase
{
    public function newWritableFileStream() : WritableFileStream
    {
        $resource = fopen('php://memory', 'w+');
        assert(is_resource($resource));
        return new WritableFileStream($resource);
    }

    public function testNotWritable() : void
    {
        $resource = fopen($this->fakeFile(), 'r');
        assert(is_resource($resource));
        $this->expectException(InvalidArgumentException::CLASS);
        $this->expectExceptionMessage('Resource is not writable.');
        $stream = new WritableFileStream($resource);
    }

    public function testWrite() : void
    {
        $stream = $this->newWritableFileStream();
        $expect = 'The quick brown fox';
        $stream->write($expect);
        rewind($stream->resource);
        $actual = stream_get_contents($stream->resource);
        $this->assertSame($expect, $actual);
    }
}
