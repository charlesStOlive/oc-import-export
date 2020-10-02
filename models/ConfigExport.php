<?php namespace Waka\ImportExport\Models;

use Model;

/**
 * ConfigExport Model
 */
class ConfigExport extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_importexport_config_exports';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = ['scopes'];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        // 'data_source' => 'Waka\Utils\Models\DataSource',
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [
        'logs' => ['Waka\ImportExport\Models\ConfigExport', 'name' => 'logeable'],
    ];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * LIST
     */
    public function listDataSource()
    {
        return \Waka\Utils\Classes\DataSourceList::lists();
    }

}
