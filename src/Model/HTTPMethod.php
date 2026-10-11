<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

enum HTTPMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case QUERY = 'QUERY';
    case TRACE = 'TRACE';
    case CONNECT = 'CONNECT';
}
