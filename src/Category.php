<?php

namespace Armincms\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Armincms\Localization\Concerns\HasTranslation;
use Armincms\Localization\Contracts\Translatable; 
use Core\User\Contracts\Ownable;
use Core\User\Concerns\HasOwner;  
use Core\Crud\Contracts\SearchEngineOptimize as SEO;
use Core\Crud\Concerns\SearchEngineOptimizeTrait as SEOTrait;
use Core\HttpSite\Contracts\Linkable;   
use Core\HttpSite\Contracts\Hitsable;
use Core\HttpSite\Concerns\Visiting; 
use Core\HttpSite\Concerns\IntractsWithSite;
use Core\HttpSite\Component;  
use Armincms\Concerns\IntractsWithMedia;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Cviebrock\EloquentSluggable\Sluggable; 
use Laravel\Nova\Http\Requests\NovaRequest; 


class Category extends Model implements Translatable, HasMedia
{ 
    use SoftDeletes, HasTranslation, IntractsWithMedia; 

    protected $with = [
    	'translations'
    ];

    protected $casts = [
    	'config' => 'json'
    ];

    protected $guarded = [];

    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'category', 'category.list', '*'
            ]
        ], 

        'logo' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'log', '*'
            ]
        ],
        
        'app_image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'category.list', '*'
            ]
        ], 

        'app_logo' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'logo', '*'
            ]
        ], 
    ];

    public static function boot()
    {
    	parent::boot();

    	static::saving(function($instance) {  
    		$instance->depth = $instance->guessDepth();
    	});
    }

    public function category()
    {
    	return $this->belongsTo(static::class);
    }

    public function guessDepth()
    {
    	if($category = static::find($this->category_id)) {
    		return $category->depth + 1;
    	}

    	return 0;
    }


    /**
     * Get Translation model instance.
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getTranslationModel()
    {
    	return new Translation;
    } 
}
