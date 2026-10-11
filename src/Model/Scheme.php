<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

enum Scheme: string
{
    case HTTP = 'http';
    case HTTPS = 'https';
}
