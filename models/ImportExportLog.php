<?php namespace Waka\ImportExport\Models;

use Model;
use Session;

/**
 * ImportExportLog Model
 */
class ImportExportLog extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_importexport_import_export_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

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
    protected $jsonable = ['results'];

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
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [
        'logeable' => [],
    ];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'excel_file' => 'System\Models\File'
    ];
    public $attachMany = [];

    public function listImportTypes() {
        trace_log($this->logeable_type);
        return Type::where('import', true)->lists('name', 'id');
    }
    public function listImport() {
        $this->logeable_type = Session::pull('modelImportExportLog.targetModel');
        $list = ConfigImport::where('model', '=', $this->logeable_type)->lists('name', 'id');
        return $list;

    }
    public function listExport() {
        $this->logeable_type = Session::pull('modelImportExportLog.targetModel');
        $list = ConfigExport::where('model', '=', $this->logeable_type)->lists('name', 'id');
        return $list;
    }
    public function getCommentImportAttribute() {
        $comment = ConfigImport::find($this->logeable_id)->comment ?? null;
        return $comment;
    }
    public function getCommentExportAttribute() {
        $comment = ConfigExport::find($this->logeable_id)->comment ?? null;
        return $comment;
    }

    public function filterFields($fields, $context = null){
        if ($this->logeable_id) {
            if(isset($fields->excel_file)) $fields->excel_file->hidden = false;
            $fields->_info->hidden = false;

        } else {
            if(isset($fields->excel_file)) $fields->excel_file->hidden = true;
            $fields->_info->hidden = true;
        }
    }
}
