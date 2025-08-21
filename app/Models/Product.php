<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    // ajuste conforme suas colunas
    protected $fillable = [
        'name',
        'description',
        'price',
        'photo_path',  // se usar foto
    ];

    // expõe "image_url" automaticamente em arrays/json
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        $path = $this->photo_path;
        if (!$path) {
            return asset('img/placeholder.png'); // opcional: crie esse arquivo
        }
        // normaliza caso tenha sido salvo com "public/"
        $path = ltrim(str_replace('public/', '', $path), '/');

        // gera /storage/products/arquivo.jpg
        return Storage::disk('public')->url($path);
    }
}

