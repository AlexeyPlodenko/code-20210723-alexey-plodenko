<?php
namespace App\Services;

class ResponseService extends AbstractService
{
    public function respond(array $response = null, int $status = 200): \Illuminate\Http\Response
                                                                        |\Illuminate\Contracts\Foundation\Application
                                                                        |\Illuminate\Contracts\Routing\ResponseFactory
    {
        $finalResponse = [
            'status' => $status >= 200 && $status < 300,
        ];
        if ($response) {
            $finalResponse['response'] = $response;
        }

        return response($finalResponse, $status);
    }
}
