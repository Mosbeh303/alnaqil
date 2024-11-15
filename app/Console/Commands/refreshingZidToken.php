<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Zid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class refreshingZidToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zidtoken:refresh';

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

        $tokens = Zid::where('updated_at', '<' ,now()->subDays(360)->timestamp)->get();


        foreach ($tokens as $token){
            // let's request a new access token via refresh token.
            $response = Http::post("https://oauth.zid.sa/" . 'oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token, // your merchant refresh token
                'client_id' => '1800', //application client id
                'client_secret' => "01CNAYFU0e8lSbEBO9Pf1EM24X83HxebFhQkxNCp" ,
                'redirect_uri' => 'https://dev.alnaqil.sa/zid-call-back',
            ]);

            $response = $response->json();

            log::info($response);

            try {

                if($response->access_token ?? null)
                    $token->update([
                        'access_token'  => $response['access_token'],
                        'expires_in'    => $response['expires_in'],
                        'refresh_token' => $response['refresh_token'],
                        'authorization' => $response['authorization']
                    ]);

                log::info('Token refreshed successfully');

            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }


        }

    }
}
