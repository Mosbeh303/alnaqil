<?php

namespace Database\Seeders;
use App\Models\Setting ;
use App\Models\User ;
use App\Models\Store ;
use App\Models\Shipment ;
use App\Models\Financial ;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Setting::factory()->create([
            'option' => 'base_fee',
            'value' => '25',
        ]);

        Setting::factory()->create([
            'option' => 'exchange_fee',
            'value' => '10',
        ]);

        Setting::factory()->create([
            'option' => 'refrigerated_transport_fee',
            'value' => '15',
        ]);

        User::factory()->create([
            'name' => 'admin',
            'lastname' => 'admin',
            'phone' => '2345678901',
            'email' => 'admin@alnaqil.sa',
            'password' => Hash::make('123456'),
            'username' => 'admin',
            'type' => 'admin'
        ]);

        // $users = User::factory(10)->create()
        //             ->each(function ($user){
        //                 Store::factory(1)
        //                 ->create([
        //                     'user_id' => $user->id,
        //                 ])
        //                 ->each(function ($store) use ($user) {
        //                     Shipment::factory(10)
        //                     ->create()
        //                     ->each(function ($shipment) use ($store) {
        //                         $shipment->update(['store_id' => $store->id]);
        //                         Financial::factory()->create([
        //                             'shipment_id' => $shipment->id,
        //                         ]);
        //                     });
        //                 });
        //             });

        // $users = User::factory(10)->create()
        // ->each(function ($user){
        //     Store::factory(1)
        //     ->create([
        //         'user_id' => $user->id,
        //     ]);
        // });




    }
}
