<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
    ];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $slug = Str::slug($name);

        while ($this->isSlugExists($slug)) {
            $slug = $this->generateUniqueSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }

    private function generateUniqueSlug($slug): string
    {
        if (is_numeric($slug[-1])) {
            return preg_replace_callback('/(\d+)$/', function ($matches) {
                return $matches[1] + 1;
            }, $slug);
        }

        return "{$slug}-2";
    }

    private function isSlugExists(string $slug): bool
    {
        if ($this->id) {
            return (bool)DB::table('podcasts')->where('slug', $slug)->whereNot('id', $this->id)->count();
        } else {
            return (bool)DB::table('podcasts')->where('slug', $slug)->count();
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
