<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaderboardPrizes extends Model
{
    protected $table = 'leaderboard_prizes';
    protected $primaryKey = 'id';
    protected $fillable = ['month', 'month_name', 'prize_text', 'status'];
    public $timestamps = false;
}
