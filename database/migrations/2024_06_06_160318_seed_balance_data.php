<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // $users = DB::table('user')->get();

        // foreach($users as $key => $val){
        //     $data['user_id'] = $val->id;
        //     $data['balance'] = 100000;
        //     $data['open_bet'] = 0;

        //     DB::table('tipster_user_balances')->insert($data);
        // }
    }

    public function down(): void
    {
        // DB::table('tipster_user_balances')->truncate();
    }
};
