<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    use HasFactory;

    public function translations()
    {
        return $this->hasMany(PageTranslations::class, 'page_id');
    }
}
