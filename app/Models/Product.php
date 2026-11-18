<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use Override;

class Product extends Model
{   


    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id'
    ];
    #[Override]
    public function casts():array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    

    public function getActivitylogOptions(): LogOptions
    {   
    return LogOptions::defaults()
            ->logOnly(['name', 'description', 'price', 'stock', 'category_id'])
            ->useLogName('product')
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Producto {$this->name} ha sido {$eventName}");
        }
}
