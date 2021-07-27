<?php

use App\Services\ResponseService;

function respond(array $response = null, int $status = 200): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
{
    return app(ResponseService::class)->respond($response, $status);
}
