<?php 

namespace Armincms\Sofre\Nova\Fields;

use Laraning\NovaTimeField\TimeField;
use Laravel\Nova\Fields\Field;  
use Laravel\Nova\Fields\Heading;  
use Armincms\Sofre\Restaurant;
use Armincms\Json\Json;
use Laravel\Nova\Panel;


class MealTimeFields
{
    /**
     * Get the pivot fields for the relationship.
     *
     * @return array
     */
    public function __invoke()
    {
        return 
            collect(Restaurant::meals())->map(function($meal) {  
                return [
                	Heading::make(__($meal)),
                	Json::make(mb_strtolower($meal), [
	                    TimeField::make(__("From"), 'from')->rules('required'),
	                    TimeField::make(__("Until"), 'until')->rules('required'),  
	                ]),
                ];   
            })->flatten()->all();
    }
} 