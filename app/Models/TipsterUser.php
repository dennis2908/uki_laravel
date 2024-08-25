<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TipsterUser extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'tipster_user';

    protected $fillable = ['balance'];

    function TipsterUserIndex()
    {
        return $this::select(["tipster_user.id", DB::RAW('format(balance,3) as balance'), DB::RAW('format(open_bet,3) as open_bet')])
            ->join('user', 'user.id', '=', 'tipster_user.user_id')
            ->latest()
            ->get();
    }

    function getUser($Request)
    {
        return $this::select(['tipster_user.id', DB::RAW('balance'), DB::RAW('open_bet')])
            ->join('user', 'user.id', '=', 'tipster_user.user_id')
            ->where('user.token', $Request->token)
            ->first();
    }
}
