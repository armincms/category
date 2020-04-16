<?php

namespace Armincms\Category; 

use Armincms\Models\Translation as  Model;  

class Translation extends Model  
{    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}