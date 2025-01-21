# StreamInterop Reference Implementations

> An implementation of the [stream-interop](https://github.com/stream-interop/interface) interfaces for dealing with byte-only in-memory streams.

## Installation

The recommended way of installing this package is via [Composer]():

```cli
$ composer require stream-interop/impl
```

## Usage

```php
$byteStream = new \StreamInterop\Impl\StringStream('hello');

$byteStream->read(2);   // returns 'he'
$byteStream->getContents();  // returns 'llo'
$byteStream->write(' world');

$byteStream->rewind();
$byteStream->getContents(); // returns 'hello world'
```

## Contributing

See the [CONTRIBUTING.md](CONTRIBUTING.md) file for more information.

## Credits/Authors

## License

The MIT License.

See the [LICENSE.md](LICENSE.md) file for more information.
