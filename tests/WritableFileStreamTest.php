<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ValueError;

class WritableFileStreamTest extends TestCase
{
    public function newWritableFileStream()
    {
        return new WritableFileStream(fopen('php://memory', 'w+'));
    }

    public function testNotWritable() : void
    {
        $resource = fopen($this->fakeFile(), 'r');
        $this->expectException(ValueError::CLASS);
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
