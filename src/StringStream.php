<?php
declare(strict_types=1);

namespace StreamInterop\Impl;

use InvalidArgumentException;
use StreamInterop\Interface\ReadableStream;
use StreamInterop\Interface\SeekableStream;
use StreamInterop\Interface\SizableStream;
use StreamInterop\Interface\WritableStream;
use Stringable;

/**
 * Manipulate 'in-memory' byte streams; example of a stream not backed by a resource.
 *
 * @author Nathan Bishop (nbish11)
 * @author Paul M. Jones (pmjones)
 * @copyright 2019-2025, Nathan Bishop and Paul M. Jones
 * @license The MIT license.
 */
final class StringStream implements ReadableStream, SeekableStream, SizableStream, WritableStream
{
    public array $metadata {
        get {
            return [
                'stream_type' => self::CLASS,
                'mode' => 'wb+',
                'unread_bytes' => $this->getSize() - $this->tell(),
                'seekable' => true,
            ];
        }
    }

    /**
     * @var integer The current byte to be read from the buffer
     */
    private $position;

    /**
     * @var string The in-memory data.
     */
    private $data;

    /**
     * Create a new 'buffer' stream.
     *
     * Providing data through the constructor will automatically
     * rewind the internal pointer to the start of the buffer.
     *
     * @param string $data The in-memory data to be put in the buffer.
     */
    public function __construct(string $data = '')
    {
        $this->position = 0;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function read(int $length): string
    {
        $buffer = substr($this->data, $this->position, $length);
        $this->position += ($length >= strlen($buffer) ? strlen($buffer) : $length);

        return $buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string|Stringable $data): int
    {
        $length = strlen($data);

        $this->data .= $data;
        $this->position += $length;

        return $length;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        return $this->position >= strlen($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return strlen($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        switch ($whence) {
            case SEEK_SET:
                $this->position = $offset;
                break;

            case SEEK_CUR:
                $this->position += $offset;
                break;

            case SEEK_END:
                $this->position = strlen($this->data) + $offset;
                break;

            default:
                throw new InvalidArgumentException('Invalid seek operation');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        $data = '';

        while (!$this->eof()) {
            $data .= $this->read(512);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isClosed(): bool
    {
        return false;
    }
}
