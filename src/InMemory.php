<?php
declare(strict_types=1);

namespace Interop\Stream\ByteStream;

use Interop\Stream\Duplex;
use Interop\Stream\Seekable;
use Interop\Stream\Writable;
use InvalidArgumentException;

/**
 * Manipulate 'in-memory' byte streams.
 *
 * @author Nathan Bishop (nbish11)
 * @copyright 2019 Nathan Bishop
 * @license The MIT license.
 */
final class InMemory implements Duplex, Seekable
{
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
    public function pipe(Writable $destination): Writable
    {
        $destination->write($this->data);

        return $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $data): int
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
    public function seek(int $offset, int $whence = Seekable::SEEK_SET): void
    {
        switch ($whence) {
            case Seekable::SEEK_SET:
                $this->position = $offset;
                break;

            case Seekable::SEEK_CURRENT:
                $this->position += $offset;
                break;

            case Seekable::SEEK_END:
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
}
