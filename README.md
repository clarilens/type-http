# TypeHTTP

Typed HTTP values for PHP 8.5. This package models URLs, methods, headers,
requests, statuses, and responses. It does not perform network requests.

Install with `composer require clarilens/type-http` after the package is published.

```php
use Clarilens\Http\Model\HTTPMethod;
use Clarilens\Http\Model\Request;
use Clarilens\Http\Model\Url\Url;

$request = new Request(HTTPMethod::GET, Url::fromString('https://example.com/'));
echo $request->url;
```

`Method` supports extension tokens and `StatusCode` accepts codes 100–599.
`HTTPMethod` and `HTTPStatus` provide named common values. Headers are
case-insensitive and can hold multiple values. URL components supplied directly
must already be percent-encoded; hosts must be ASCII DNS names, IPv4 addresses,
or bracketed IPv6 addresses.

## HTTP clients

Both clients accept the arguments produced by `Request`:

```php
$guzzleResponse = $guzzleClient->request(...$request->toGuzzleArguments());
$symfonyResponse = $symfonyClient->request(...$request->toSymfonyArguments());
```

Request headers retain repeated values, and `null` request bodies are omitted.
Guzzle arguments disable `http_errors`, allowing callers to inspect 4xx and 5xx responses.
