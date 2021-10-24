<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTranslations extends Model
{
    use HasFactory;

    protected $fillable=['page_id','lang','meta_description','meta_keywords','title'];
}
