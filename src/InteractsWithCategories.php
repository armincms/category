<?php

namespace Armincms\Category;  

trait InteractsWithCategories 
{    
    public function categories() 
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }   
}