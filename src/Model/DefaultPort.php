<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

enum DefaultPort: int
{
    case HTTP = 80;
    case HTTPS = 443;
}
