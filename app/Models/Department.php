<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'departmentID';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'locationcode',
        'officename',
        'accountableper',
        'description',
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $hidden = ['deleted_at'];
    protected $appends = ['supplies_count'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'departmentID';
    }

    /**
     * Set the office name attribute.
     */
    public function setOfficenameAttribute($value)
    {
        $this->attributes['officename'] = $value;
    }

    /**
     * The user that is accountable for this department.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'accountableper', 'id')
            ->withDefault([
                'name' => 'N/A',
                'email' => 'N/A'
            ]);
    }

    /**
     * All supplies for the department.
     */
    public function supplies()
    {
        return $this->hasMany(Supply::class, 'department_id', 'departmentID')
            ->with('category');
    }

    /**
     * All deployed items for the department.
     */
    public function deployedItems()
    {
        return $this->hasMany(DeployedItem::class, 'departmentID', 'departmentID');
    }

    /**
     * Supplies count attribute.
     */
    public function getSuppliesCountAttribute()
    {
        return $this->supplies()->count();
    }

    /**
     * Total value of all supplies in this department.
     */
    public function getTotalValue(): float
    {
        return $this->supplies->sum(function ($supply) {
            return $supply->getTotalValue();
        });
    }

    /**
     * Count of low stock items in this department.
     */
    public function getLowStockCount(): int
    {
        return $this->supplies->filter(function ($supply) {
            return $supply->isLowStock();
        })->count();
    }
}
