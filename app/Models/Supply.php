<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'itemID';

    protected $fillable = [
        'name',
        'description',
        'acquired_at',
        'estimated_life',
        'unit_cost',
        'quantity',
        'minimum_stock',
        'amount',
        'category_id',
        'fund_code',
        'pp_sub_account',
        'gl_code',
        'added_by',
        'department_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'acquired_at' => 'datetime',
    ];

    /**
     * Get the category that owns the supply.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'categoryID');
    }

    /**
     * Get the department that owns the supply.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'departmentID');
    }

    /**
     * Get the user who added the supply.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    /**
     * Check if the supply is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }

    /**
     * Get the total value of the supply.
     */
    public function getTotalValue(): float
    {
        return $this->quantity * $this->unit_cost;
    }
    
    /**
     * Get all deployed items for this supply.
     */
    public function deployedItems()
    {
        return $this->hasMany(DeployedItem::class, 'supply_id', 'itemID');
    }
}
