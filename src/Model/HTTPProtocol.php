<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

enum HTTPProtocol: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}
