<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        // Get OAuth client
        $client = DB::table('oauth_clients')->where('password_client', 1)->first();

        if (!$client) {
            return response()->json([
                'error' => 'OAuth client not found'
            ], 500);
        }

        // Create internal request to /oauth/token
        $tokenRequest = Request::create(config('app.url') . '/oauth/token', 'POST', [
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $request->email,
            'password'      => $request->password,
            'scope'         => '',
        ]);

        /** @var \Illuminate\Http\Response $response */
        $response = app()->handle($tokenRequest);

        if ($response->getStatusCode() !== 200) {
            $content = json_decode($response->getContent(), true);
            return response()->json([
                'error' => $content['message'] ?? 'Login failed',
                'message' => $content['message'] ?? 'Invalid credentials'
            ], $response->getStatusCode());
        }

        return response()->json(json_decode($response->getContent(), true), 200);
    }
}
