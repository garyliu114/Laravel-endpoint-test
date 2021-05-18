<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_name',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'phone_no1',
        'phone_no2',
        'zip',
        'start_validity',
        'end_validity',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $appends = ['totalUser'];

    public function users(){
    	return $this->hasMany(User::class, 'client_id', 'id');	
    }

	public function getTotalUserAttribute()
	{
	 	$res = [];
	 	$res['all'] = $this->users()->count();
		$res['active'] = $this->users()->where('status','=', 'Active')->count();
		$res['inactive'] = $this->users()->where('status','=', 'Inactive')->count();
	 	return $res;
	}
}
