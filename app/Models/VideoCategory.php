<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon_class',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * 获取该分类下的所有视频
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'category_id');
    }

    /**
     * 获取活跃的分类列表
     */
    public static function getActiveCategories()
    {
        return static::where('is_active', true)
                     ->orderBy('sort_order')
                     ->get();
    }
}