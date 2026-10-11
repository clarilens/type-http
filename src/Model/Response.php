<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

final readonly class Response
{
    public StatusCode $status;
    public Headers $headers;

    public function __construct(
        StatusCode|HTTPStatus|int $status,
        ?Headers $headers = null,
        public ?string $body = null,
    ) {
        $this->status = $status instanceof StatusCode ? $status : StatusCode::from($status);
        $this->headers = $headers ?? new Headers();
    }
}
