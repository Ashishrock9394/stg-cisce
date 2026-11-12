<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
// use App\Models\ApiClientMeta;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Token;


class ApiRegistrationController extends Controller
{

    public function register(Request $request) {

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
        ]);

        $client = app(ClientRepository::class)->create(
            null, // user_id (null for machine-to-machine)
            $validated['client_name'],
            '',   // redirect URL not needed for client_credentials
            false, // personal access client
            false, // password grant client
            true   // confidential client (requires client_secret)
        );

        return response()->json([
            'message' => 'Client registered successfully.',
            'client_id' => $client->id,
            'client_secret' => $client->plainSecret,
            'grant_type' => 'client_credentials',
            'token_url' => route('passport.token'),
            'note' => 'Store the client secret securely. It will not be shown again.'
        ], 201);

    }
    


    public function getAuthorisedToken(Request $request) {
        $validated = $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string'
        ]);

        \Log::info("Token Request", $validated); // Debugging

        $client = Client::where('id', $validated['client_id'])
                      ->where('revoked', false)
                      ->first();

        if (!$client) {
            \Log::error("Client not found or revoked", ['client_id' => $validated['client_id']]);
            return response()->json([
                'error' => 'invalid_client',
                'message' => 'Client not found or revoked'
            ], 401);
        }

        if (!hash_equals($client->secret, hash('sha256', $validated['client_secret']))) {
            \Log::error("Invalid secret", ['client_id' => $validated['client_id']]);
            return response()->json([
                'error' => 'invalid_client',
                'message' => 'Invalid client secret'
            ], 401);
        }

        $tokenResult = $client->createToken(
            'API Access Token',
            ['*'],
            now()->addDays(7)
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_in' => $tokenResult->token->expires_at->diffInSeconds(now())
        ]);
    }


    


}
