<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'Clarilens\\Http\\';
    if (str_starts_with($class, $prefix)) {
        require dirname(__DIR__) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    }
});

use Clarilens\Http\Model\Headers;
use Clarilens\Http\Model\HTTPMethod;
use Clarilens\Http\Model\HTTPStatus;
use Clarilens\Http\Model\Request;
use Clarilens\Http\Model\Response;
use Clarilens\Http\Model\StatusCode;
use Clarilens\Http\Model\Url\Host;
use Clarilens\Http\Model\Url\Port;
use Clarilens\Http\Model\Url\Url;

function check(bool $value, string $message): void
{
    if (!$value) {
        throw new RuntimeException($message);
    }
}

function invalid(callable $call): void
{
    try {
        $call();
    } catch (InvalidArgumentException) {
        return;
    }
    throw new RuntimeException('Expected invalid argument');
}

$url = Url::fromString('HTTP://[::1]:8080/a%20b?q=x#part');
check((string) $url === 'http://[::1]:8080/a%20b?q=x#part', 'URL round trip');
check($url->host->isIp, 'IPv6 is an IP');
check(Host::fromString('127.0.0.1')->isIp, 'IPv4 is an IP');
foreach (['', 'bad host', '[nope]', '::1'] as $host) {
    invalid(fn () => new Host($host));
}
foreach ([0, -1, 65536] as $port) {
    invalid(fn () => new Port($port));
}
invalid(fn () => Port::fromString('garbage'));
invalid(fn () => Url::fromString('https://example.com/a b'));

$headers = new Headers(['Set-Cookie' => ['a=1', 'b=2'], 'Accept' => 'text/plain']);
check($headers->get('set-cookie') === ['a=1', 'b=2'], 'Repeated headers');
check($headers->with('ACCEPT', 'application/json')->get('accept') === ['application/json'], 'Header replacement');
check($headers->get('accept') === ['text/plain'], 'Headers immutable');
invalid(fn () => new Headers(['Bad Name' => 'x']));
invalid(fn () => new Headers(['X-Test' => "ok\r\nInjected: yes"]));

$request = new Request(HTTPMethod::GET, $url, $headers);
check($request->method->value === 'GET', 'Known method');
check((new Request('PROPFIND', $url))->method->value === 'PROPFIND', 'Extension method');
check((new Response(299))->status->isSuccessful(), 'Unlisted success status');
check((new StatusCode(HTTPStatus::Ok))->value === 200, 'Named status');
check(StatusCode::from(HTTPStatus::NotFound)->value === 404, 'Named status factory');
check((new Response(HTTPStatus::Created))->status->value === 201, 'Response named status');
check((new StatusCode(599))->value === 599, 'Unlisted status');
check((new StatusCode(HTTPStatus::NetworkConnectTimeoutError))->value === 599, 'Non-standard 599');
invalid(fn () => new StatusCode(99));
invalid(fn () => new Response(600));

$outbound = new Request('POST', Url::fromString('https://example.com/a?q=1'), $headers, '');
$expectedOptions = ['headers' => $headers->all(), 'body' => ''];
check($outbound->toSymfonyArguments() === ['POST', 'https://example.com/a?q=1', $expectedOptions], 'Symfony arguments');
check($outbound->toGuzzleArguments() === ['POST', 'https://example.com/a?q=1', [...$expectedOptions, 'http_errors' => false]], 'Guzzle arguments');
check(!array_key_exists('body', (new Request('GET', $url))->toGuzzleArguments()[2]), 'Null body omitted');

$registered = [
    100, 101, 102, 103, 104,
    200, 201, 202, 203, 204, 205, 206, 207, 208, 226,
    300, 301, 302, 303, 304, 305, 306, 307, 308,
    400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413,
    414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
    500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511,
];
check(array_map(static fn (HTTPStatus $status): int => $status->value, HTTPStatus::cases()) === [...$registered, 599], 'IANA status coverage plus 599');

echo "TypeHTTP tests passed.\n";
