<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    public function cards()
    {
        return $this->hasMany(Card::class, 'board_id')->orderBy('order', 'ASC');
    }

    public function allCards()
    {
        return $this->hasMany(Card::class, 'board_id')->orderBy('order', 'ASC')->withTrashed();
    }
}
