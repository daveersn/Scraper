<?php

namespace App\Models;

use Cknow\Money\Casts\MoneyIntegerCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPrice extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => MoneyIntegerCast::class,
    ];

    /**
     * @return BelongsTo<Item, ItemPrice>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
