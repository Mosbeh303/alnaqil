<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Salla\OAuth2\Client\Provider\Salla;
use League\OAuth2\Client\Token\AccessToken;
use App\Models\SallaOauthToken;
use Illuminate\Support\Facades\Log;
use Throwable;

class refreshingSallaToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $provider = new Salla([
            'clientId'     => env('SALLA_CLIENT_ID') , // The client ID assigned to you by Salla
            'clientSecret' => env('SALLA_CLIENT_SECRET') , // The client password assigned to you by Salla
            'redirectUri'  => env('SALLA_CALLBACK_URL') , // the url for current page in your service
        ]);

        $tokens = SallaOauthToken::where('expires_in', '<' ,now()->addDays(3)->timestamp)->get();

        foreach ($tokens as $token){

            try {
               // let's request a new access token via refresh token.
                $newToken = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $token->refresh_token
                ]);

                // lets update user tokens
                $token->update([
                    'access_token'  => $newToken->getToken(),
                    'expires_in'    => $newToken->getExpires(),
                    'refresh_token' => $newToken->getRefreshToken()
                ]);

                log::error('Token refreshed successfully');

            }

            //catch exception
            catch(Throwable $e) {
              //echo 'Message: ' .$e->getMessage();
              // continue;
            }


        }

    }
}
