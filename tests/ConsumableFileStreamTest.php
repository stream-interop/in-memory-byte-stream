<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use ValueError;

class ConsumableFileStreamTest extends TestCase
{
    public function newConsumableFileStream()
    {
        return new ConsumableFileStream($this->fopenFakeFile('r'));
    }

    public function testNotReadable() : void
    {
        $resource = fopen($this->fakeFile(), 'a');
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
        $stream->read($stream->getSize());
        $this->assertFalse($stream->eof());
        $stream->read(1);
        $this->assertTrue($stream->eof());
    }
}
