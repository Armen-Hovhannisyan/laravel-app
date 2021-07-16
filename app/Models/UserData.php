<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserData extends  Model
{
    protected $table='user_dates';
    protected $fillable = [
        'id',
        'name',
        'date'
    ];

    public $timestamps = false;
    protected $dateFormat = 'd-m-Y';

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->get()->groupBy('date')->toArray();
    }

    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('d-m-Y');
    }
}
