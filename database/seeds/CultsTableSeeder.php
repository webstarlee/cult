<?php

use App\Cult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CultsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::table('cults')->delete();

        $cults = [
            [
                'cult_name' => 'INNER CIRCLE(bests friends)',
                'user_id' => 0,
            ],[
                'cult_name' => 'SCHOOL FRIENDS',
                'user_id' => 0,
            ],[
                'cult_name' => 'MUSIC FRIENDS',
                'user_id' => 0,
            ],[
                'cult_name' => 'FUNNY FRIENDS',
                'user_id' => 0,
            ],[
                'cult_name' => 'INSPRIING FRIENDS',
                'user_id' => 0,
            ],
        ];

        foreach ($cults as $cult) {
            Cult::create($cult);
        }
        Model::reguard();
    }
}
