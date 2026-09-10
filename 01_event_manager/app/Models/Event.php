<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

#[Fillable(['title', 'description', 'event_date'])]
class Event extends Model
{
    use HasFactory;


    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',  // az event_date mezőt datetime típusra konvertálja
        ];
    }
}
