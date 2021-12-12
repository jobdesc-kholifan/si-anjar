<?php

namespace App\Models\Investors;

use App\Models\Masters\Config;
use App\Models\Masters\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class Investor extends Model
{
    use HasFactory;

    protected $table = "ms_investor";

    protected $fillable = [
        'investor_name',
        'email',
        'phone_number',
        'phone_number_alternative',
        'address',
        'no_ktp',
        'npwp',
        'place_of_birth',
        'date_of_birth',
        'gender_id',
        'religion_id',
        'relationship_id',
        'job_name',
        'emergency_name',
        'emergency_phone_number',
        'emergency_relationship',
    ];

    public $defaultSelects = [
        'investor_name',
        'email',
        'phone_number',
        'phone_number_alternative',
        'no_ktp',
        'npwp',
        'job_name',
        'place_of_birth',
        'date_of_birth',
        'emergency_name',
        'emergency_phone_number',
        'emergency_relationship',
    ];

    protected static function boot()
    {
        parent::boot();
        self::deleting(function($model) {
            /* @var Investor $model */
            $file = $model->file_ktp()->get();
            fileUnlink($file);
            $model->file_ktp()->delete();

            $file = $model->file_npwp()->get();
            fileUnlink($file);
            $model->file_npwp()->delete();

            $model->banks()->delete();
        });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromTimeString($value)
            ->setTimezone(env('APP_TIMEZONE'))
            ->format('d/m/Y H:i:s');
    }

    public function getDateOfBirthAttribute($value)
    {
        return Carbon::createFromTimestamp(strtotime($value))
            ->setTimezone(env('APP_TIMEZONE'))
            ->format('d/m/Y');
    }

    /**
     * static function yang digunakan ketika memanggil with biar tidak perlu
     * dituliskan lagi
     *
     * @param Relation $query
     * @param array $selects
     *
     * @return Relation
     * */
    static public function foreignWith($query, $selects = null)
    {
        $model = new Investor();
        return $model->defaultWith(is_null($selects) ? $model->defaultSelects : $selects, $query);
    }

    /**
     * function untuk setting default with apa saja yang akan sering dipakai
     * tetapi jangan banyak-banyak karena akan memperngaruhi proses loading page
     *
     * @param Relation|Investor $query
     * @param array $selects
     *
     * @return Relation
     * */
    private function _defaultWith($query, $selects = [])
    {
        return $query->with([
            'gender' => function($query) {
                Config::foreignWith($query, ['name']);
            },
            'religion' => function($query) {
                Config::foreignWith($query, ['name']);
            },
            'relationship' => function($query) {
                Config::foreignWith($query, ['name']);
            }
        ])->select('id', 'gender_id', 'religion_id', 'relationship_id')->addSelect($selects);
    }

    /**
     * function defaultWith yang digunakan untuk dipanggil public
     *
     * @param array $selects
     * @param Relation|Investor|null $query
     *
     * @return Relation
     * */
    public function defaultWith($selects = [], $query = null)
    {
        return $this->_defaultWith(is_null($query) ? $this : $query, $selects);
    }

    public function defaultQuery()
    {
        return $this->defaultWith($this->defaultSelects)
            ->with([
                'banks' => function($query) {
                    InvestorBank::foreignWith($query)
                        ->addSelect('investor_id');
                },
            ])
            ->addSelect('created_at');
    }

    public function gender()
    {
        return $this->hasOne(Config::class, 'id', 'gender_id');
    }

    public function religion()
    {
        return $this->hasOne(Config::class, 'id', 'religion_id');
    }

    public function relationship()
    {
        return $this->hasOne(Config::class, 'id', 'relationship_id');
    }

    public function banks()
    {
        return $this->hasMany(InvestorBank::class, 'investor_id', 'id');
    }

    public function file_ktp()
    {
        return $this->hasOne(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::fileInvestorKTP);
            });
    }

    public function file_npwp()
    {
        return $this->hasOne(File::class, 'ref_id', 'id')
            ->whereHas('ref_type', function($query) {
                /* @var Relation $query */
                $query->where('slug', \DBTypes::fileInvestorNPWP);
            });
    }


}
