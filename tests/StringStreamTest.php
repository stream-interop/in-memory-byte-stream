<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StreamInterop\Interface\ReadableStream;
use StreamInterop\Interface\SeekableStream;
use StreamInterop\Interface\SizableStream;
use StreamInterop\Interface\WritableStream;

final class StringStreamTest extends TestCase
{
    /**
     * @test
     */
    public function is_a_readable_stream(): void
    {
        $stream = new StringStream();

        $this->assertInstanceOf(ReadableStream::class, $stream);
    }

    /**
     * @test
     */
    public function is_a_seekable_stream(): void
    {
        $stream = new StringStream();

        $this->assertInstanceOf(SeekableStream::class, $stream);
    }

    /**
     * @test
     */
    public function is_a_sizable_stream(): void
    {
        $stream = new StringStream();

        $this->assertInstanceOf(SizableStream::class, $stream);
    }

    /**
     * @test
     */
    public function is_a_writable_stream(): void
    {
        $stream = new StringStream();

        $this->assertInstanceOf(WritableStream::class, $stream);
    }

    /**
     * @test
     */
    public function position_for_new_stream_without_data_is_at_start(): void
    {
        $stream = new StringStream();

        $this->assertEquals(0, $stream->tell());
    }

    /**
     * @test
     */
    public function position_for_new_stream_with_data_is_at_start(): void
    {
        $stream = new StringStream('hello');

        $this->assertEquals(0, $stream->tell());
    }

    /**
     * @test
     */
    public function reading_from_empty_stream_does_not_advance_pointer(): void
    {
        $stream = new StringStream();

        $stream->read(2);

        $this->assertEquals(0, $stream->tell());
    }

    /**
     * @test
     */
    public function reading_from_stream_advances_pointer_by_amount_of_bytes_read(): void
    {
        $stream = new StringStream('hello');

        $stream->read(2);

        $this->assertEquals(2, $stream->tell());
    }

    /**
     * @test
     */
    public function reading_from_stream_does_not_advance_pointer_more_than_what_is_read(): void
    {
        $stream = new StringStream('hello');

        $stream->read(512);

        $this->assertEquals(5, $stream->tell());
    }

    /**
     * @test
     */
    public function reading_from_empty_stream_returns_no_data(): void
    {
        $stream = new StringStream();

        $dataRead = $stream->read(512);

        $this->assertEquals('', $dataRead);
    }

    /**
     * @test
     */
    public function reading_from_stream_returns_only_the_amount_of_bytes_asked_for(): void
    {
        $stream = new StringStream('hello');

        $readData = $stream->read(2);

        $this->assertEquals('he', $readData);
    }

    /**
     * @test
     */
    public function writing_data_advances_position(): void
    {
        $stream = new StringStream();

        $stream->write('hello');

        $this->assertEquals(5, $stream->tell());
    }

    /**
     * @test
     */
    public function writing_data_is_added_to_end_of_stream(): void
    {
        $stream = new StringStream();

        $stream->write('hello');
        $stream->rewind();

        $this->assertEquals('hello', $stream->read(8));
    }

    /**
     * @test
     */
    public function writing_data_returns_amount_of_bytes_added_to_end_of_stream(): void
    {
        $stream = new StringStream();

        $bytesWritten = $stream->write('hello');

        $this->assertEquals(5, $bytesWritten);
    }

    /**
     * @test
     */
    public function an_empty_stream_is_at_eof(): void
    {
        $stream = new StringStream();

        $this->assertTrue($stream->eof());
    }

    /**
     * @test
     */
    public function after_writing_data_stream_is_at_eof(): void
    {
        $stream = new StringStream();

        $stream->write('hello');

        $this->assertTrue($stream->eof());
    }

    /**
     * @test
     */
    public function stream_is_at_eof_after_reading_last_of_data(): void
    {
        $stream = new StringStream('hello');

        $stream->read(512);

        $this->assertTrue($stream->eof());
    }

    /**
     * @test
     */
    public function stream_is_at_eof_after_reading_contents(): void
    {
        $stream = new StringStream('hello');

        $stream->getContents();

        $this->assertTrue($stream->eof());
    }

    /**
     * @test
     */
    public function stream_is_no_longer_at_eof_after_rewinding(): void
    {
        $stream = new StringStream();
        $stream->write('hello');

        $stream->rewind();

        $this->assertFalse($stream->eof());
    }

    /**
     * @test
     */
    public function empty_stream_has_no_size(): void
    {
        $stream = new StringStream();

        $this->assertEquals(0, $stream->getSize());
    }

    /**
     * @test
     */
    public function can_get_size_of_data_in_stream(): void
    {
        $stream = new StringStream('hello');

        $this->assertEquals(5, $stream->getSize());
    }

    /**
     * @test
     */
    public function throws_error_for_invalid_seek_operation(): void
    {
        $stream = new StringStream('hello');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid seek operation');

        $stream->seek(2, 546);
    }

    /**
     * @test
     */
    public function seek_set_op_sets_to_offset(): void
    {
        $stream = new StringStream('hello');

        $stream->seek(2, SEEK_SET);

        $this->assertEquals(2, $stream->tell());
    }

    /**
     * @test
     */
    public function seek_current_op_adds_offset(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(1);

        $stream->seek(2, SEEK_CUR);

        $this->assertEquals(3, $stream->tell());
    }

    /**
     * @test
     */
    public function seek_end_op_adds_offset_to_eof(): void
    {
        $stream = new StringStream('hello');

        $stream->seek(-1, SEEK_END);

        $this->assertEquals(4, $stream->tell());
    }

    /**
     * @test
     */
    public function rewinding_stream_returs_position_to_start(): void
    {
        $stream = new StringStream();
        $stream->write('hello');

        $stream->rewind();

        $this->assertEquals(0, $stream->tell());
    }

    /**
     * @test
     */
    public function stream_with_no_data_has_no_contents(): void
    {
        $stream = new StringStream();

        $remainingData = $stream->getContents();

        $this->assertEquals('', $remainingData);
    }

    /**
     * @test
     */
    public function stream_with_position_at_start_returns_all_data(): void
    {
        $stream = new StringStream('hello');

        $this->assertEquals('hello', $stream->getContents());
    }

    /**
     * @test
     */
    public function stream_with_position_not_at_start_returns_remaming_contents_from_position_to_eof(): void
    {
        $stream = new StringStream('hello');
        $stream->seek(2);

        $remainingData = $stream->getContents();

        $this->assertEquals('llo', $remainingData);
    }

    /**
     * @test
     */
    public function getting_contents_from_stream_advances_position(): void
    {
        $stream = new StringStream('hello');

        $stream->getContents();

        $this->assertEquals(5, $stream->tell());
    }
}
