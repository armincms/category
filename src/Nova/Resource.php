<?php

namespace Armincms\Category\Nova;

use Armincms\Nova\Resource as ArminResource;
use Laravel\Nova\Http\Requests\NovaRequest; 
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BooleanGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Panel;


abstract class Resource extends ArminResource
{   
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\Category\Category';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [ 
        'category', 'translations'
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id'
    ]; 

    /**
     * The columns that should be searched in the translation table.
     *
     * @var array
     */
    public static $searchTranslations = [
        'name'
    ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Restaurant Services';

    abstract public static function relatableResource();


    /**
     * Fill the given fields for the model.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Support\Collection  $fields
     * @return array
     */
    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        $model::saving(function($model) {
            $model->forceFill(['resource' => static::relatableResource()]); 
        });

        return parent::fillFields($request, $model, $fields);
    }

    public function fields(Request $request)
    {
        return [
            // new Panel(__("Category"), $this->tab(function($tab) {
            //     $tab->group(__('Specification'), [$this, 'specificationFields']);
            //     $tab->group(__('Media'), [$this, 'mediaFields']); 
            // }, 'category')->toArray()),
            
            ID::make()->sortable(),

            BelongsTo::make(__("Category"), 'category', static::class)
                ->nullable()
                ->withoutTrashed(),

            $this->abstracts(),

            new Panel(__("Media"), [$this, 'mediaFields']),

            new Panel(__("Setting"), $this->tab(function($tab) { 
                $tab->group(__('Mobile'), [$this, 'mobileSettingFields']);
                $tab->group(__('Tablet'), [$this, 'tabletSettingFields']);
                $tab->group(__('Desktop'), [$this, 'desktopSettingFields']);
            })->toArray()),
        ];
    } 

    public function mediaFields()
    {
        return [
            $this->imageField(),

            $this->imageField('logo', "Logo")->hideFromIndex(),

            $this->imageField('app_image', "App Image")->hideFromIndex(),

            $this->imageField('app_logo', "App Logo")->hideFromIndex(),
        ]; 
    }

    public function mobileSettingFields()
    {
        return $this->configFields('mobile');
    }

    public function tabletSettingFields()
    {
        return $this->configFields('tablet');
    }

    public function desktopSettingFields()
    {
        return $this->configFields('desktop');
    } 

    public function configFields($agent)
    {
        $columns = 1;
        $count = 5;

        if($agent == 'tablet') {
            $columns = 2;
            $count = 10;
        }

        if($agent == 'desktop') {
            $columns = 3;
            $count = 5;
        }

        return $this->configField([
            $this->jsonField($agent, [ 
                $this->heading(__(Str::title($agent))),

                Select::make(__("Category Layout"), "layout")
                    ->options($this->layouts('category')) 
                    ->rules('required')
                    ->hideFromIndex()
                    ->required()
                    ->default('callisto'),

                Select::make(__("List Layout"), "list")
                    ->options($this->layouts('restaurant.list')) 
                    ->rules('required')
                    ->hideFromIndex()
                    ->required()
                    ->default('callisto'), 

                Select::make(__("Pagination Layout"), "pagination")
                    ->options($this->layouts('pagination'))
                    ->rules('required')
                    ->hideFromIndex()
                    ->required()
                    ->default('simple-pagination'), 

                Select::make(__("Pagination Method"), "paginator")
                    ->options([
                        'simple' => __("Simple Pagination"),
                        'length_aware' => __("Length Aware Pagination")
                    ])
                    ->rules('required')
                    ->hideFromIndex()
                    ->required()
                    ->default('simple'), 

                Number::make(__("Portrait Columns"), "portrait_columns")
                    ->default($columns)
                    ->min(1)
                    ->rules('required')
                    ->hideFromIndex()
                    ->required(),

                Number::make(__("Landscape Columns"), "landscape_columns")
                    ->default($columns)
                    ->min(1)
                    ->rules('required')
                    ->hideFromIndex()
                    ->required(),

                Number::make(__("Count"), "count")
                    ->default($count)
                    ->min(1)
                    ->rules('required')
                    ->hideFromIndex()
                    ->required(), 

                $this->heading(__("Restaurants Display Details")),

                BooleanGroup::make(__("Details"), 'display')
                    ->options([
                        'name' => __("Display Name"),
                        'logo' => __("Display Logo"),
                        'gallery' => __("Display Gallery"),
                        'location' => __("Display Location"),
                        'sending_method' => __("Display Sending Methods"),
                        'payment_method' => __("Display Payment Methods"),
                        'category' => __("Display Category"),
                        'class' => __("Display Class"),
                    ])
                    ->default([
                        'name' => true,
                        'logo' => true,
                        'location' => true,
                        'gallery' => true,
                    ])
                    ->fillUsing(function($request, $attribute, $requestAttribute) {
                        if ($request->exists($requestAttribute)) {
                            return json_decode($request[$requestAttribute], true); 
                        }
                    })
                    ->resolveUsing(function($value, $resource, $attribute) { 
                        return data_get($resource, str_replace('->', '.', $attribute));
                    })
                    ->hideFromIndex(),
            ]),
        ])->saveHistory();
    }

    public function layouts($group)
    {
        $groups = is_array($group) ? $group : func_get_args();

        return layouts($groups)->mapWithKeys(function($layout) {
            return [$layout->name() => $layout->label()];
        });
    }
}
