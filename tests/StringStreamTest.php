<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use StreamInterop\Interface\ReadableStream;
use StreamInterop\Interface\SeekableStream;
use StreamInterop\Interface\SizableStream;
use StreamInterop\Interface\WritableStream;

final class StringStreamTest extends TestCase
{
    public function testIsAReadableStream(): void
    {
        $stream = new StringStream();
        $this->assertInstanceOf(ReadableStream::class, $stream);
    }

    public function testIsASeekableStream(): void
    {
        $stream = new StringStream();
        $this->assertInstanceOf(SeekableStream::class, $stream);
    }

    public function testIsASizableStream(): void
    {
        $stream = new StringStream();
        $this->assertInstanceOf(SizableStream::class, $stream);
    }

    public function testIsAWritableStream(): void
    {
        $stream = new StringStream();
        $this->assertInstanceOf(WritableStream::class, $stream);
    }

    public function testPositionForNewStreamWithoutDataIsAtStart(): void
    {
        $stream = new StringStream();
        $this->assertEquals(0, $stream->tell());
    }

    public function testPositionForNewStreamWithDataIsAtStart(): void
    {
        $stream = new StringStream('hello');
        $this->assertEquals(0, $stream->tell());
    }

    public function testReadingFromEmptyStreamDoesNotAdvancePointer(): void
    {
        $stream = new StringStream();
        $stream->read(2);
        $this->assertEquals(0, $stream->tell());
    }

    public function testReadingFromStreamAdvancesPointerByAmountOfBytesRead(): void
    {
        $stream = new StringStream('hello');
        $stream->read(2);
        $this->assertEquals(2, $stream->tell());
    }

    public function testReadingFromStreamDoesNotAdvancePointerMoreThanWhatIsRead(): void
    {
        $stream = new StringStream('hello');
        $stream->read(512);
        $this->assertEquals(5, $stream->tell());
    }

    public function testReadingFromEmptyStreamReturnsNoData(): void
    {
        $stream = new StringStream();
        $dataRead = $stream->read(512);
        $this->assertEquals('', $dataRead);
    }

    public function testReadingFromStreamReturnsOnlyTheAmountOfBytesAskedFor(): void
    {
        $stream = new StringStream('hello');
        $readData = $stream->read(2);
        $this->assertEquals('he', $readData);
    }

    public function testWritingDataAdvancesPosition(): void
    {
        $stream = new StringStream();
        $stream->write('hello');
        $this->assertEquals(5, $stream->tell());
    }

    public function testWritingDataIsAddedToEndOfStream(): void
    {
        $stream = new StringStream();
        $stream->write('hello');
        $stream->rewind();
        $this->assertEquals('hello', $stream->read(8));
    }

    public function testWritingDataReturnsAmountOfBytesAddedToEndOfStream(): void
    {
        $stream = new StringStream();
        $bytesWritten = $stream->write('hello');
        $this->assertEquals(5, $bytesWritten);
    }

    public function testAnEmptyStreamIsAtEof(): void
    {
        $stream = new StringStream();
        $this->assertTrue($stream->eof());
    }

    public function testAfterWritingDataStreamIsAtEof(): void
    {
        $stream = new StringStream();
        $stream->write('hello');
        $this->assertTrue($stream->eof());
    }

    public function testStreamIsAtEofAfterReadingLastOfData(): void
    {
        $stream = new StringStream('hello');
        $stream->read(512);
        $this->assertTrue($stream->eof());
    }

    public function testStreamIsAtEofAfterReadingContents(): void
    {
        $stream = new StringStream('hello');
        $stream->getContents();
        $this->assertTrue($stream->eof());
    }

    public function testStreamIsNoLongerAtEofAfterRewinding(): void
    {
        $stream = new StringStream();
        $stream->write('hello');
        $stream->rewind();
        $this->assertFalse($stream->eof());
    }

    public function testEmptyStreamHasNoSize(): void
    {
        $stream = new StringStream();
        $this->assertEquals(0, $stream->getSize());
    }

    public function testCanGetSizeOfDataInStream(): void
    {
        $stream = new StringStream('hello');
        $this->assertEquals(5, $stream->getSize());
    }

    public function testThrowsErrorForInvalidSeekOperation(): void
    {
        $stream = new StringStream('hello');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid seek operation: 546');
        $stream->seek(2, 546);
    }

    public function testSeekSetOpSetsToOffset(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(2, SEEK_SET);
        $this->assertEquals(2, $stream->tell());
    }

    public function testSeekCurrentOpAddsOffset(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(1);
        $stream->seek(2, SEEK_CUR);
        $this->assertEquals(3, $stream->tell());
    }

    public function testSeekEndOpAddsOffsetToEof(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(-1, SEEK_END);
        $this->assertEquals(4, $stream->tell());
    }

    public function testRewindingStreamRetursPositionToStart(): void
    {
        $stream = new StringStream();
        $stream->write('hello');
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    public function testStreamWithNoDataHasNoContents(): void
    {
        $stream = new StringStream();
        $remainingData = $stream->getContents();
        $this->assertEquals('', $remainingData);
    }

    public function testStreamWithPositionAtStartReturnsAllData(): void
    {
        $stream = new StringStream('hello');
        $this->assertEquals('hello', $stream->getContents());
    }

    public function testStreamWithPositionNotAtStartReturnsRemamingContentsFromPositionToEof(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(2);
        $remainingData = $stream->getContents();
        $this->assertEquals('llo', $remainingData);
    }

    public function testGettingContentsFromStreamAdvancesPosition(): void
    {
        $stream = new StringStream('hello');
        $stream->getContents();
        $this->assertEquals(5, $stream->tell());
    }

    public function testOpenAndClosed() : void
    {
        $stream = new StringStream('hello');
        $this->assertTrue($stream->isOpen());
        $this->assertFalse($stream->isClosed());
    }
}
