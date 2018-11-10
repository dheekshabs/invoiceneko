<?php

namespace App\Models;

use Log;
use App\Traits\UniqueSlug;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Iatstuti\Database\Support\CascadeSoftDeletes;

class Company extends Model
{
    use Notifiable, SoftDeletes, CascadeSoftDeletes, UniqueSlug;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'crn',
        'domain_name',
        'phone',
        'email',
        'country_code',
        'timezone',
    ];

    protected $attributes = [
        'invoice_index' => 1,
        'quote_index' => 1
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            $company->slug = str_slug($company->name);
            static::generateSlug($company);
        });

        //Auto Creation of Settings per Company;
        static::created(function ($company) {
            $settings = new CompanySettings;
            $company->settings()->save($settings);
        });
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function niceInvoiceID()
    {
        $companysettings = $this->settings;
        if($companysettings->invoice_prefix)
        {
            $prefix = $companysettings->invoice_prefix . '-';
        }
        else
        {
            $prefix = $this->slug . '-';
        }
        //Retrieve latest version of the company model otherwise it will use the old index value
        $company = $this->fresh();
        return $prefix . sprintf('%06d', $company->invoice_index);
    }

    public function niceQuoteID()
    {
        $companysettings = $this->settings;
        if($companysettings->quote_prefix)
        {
            $prefix = $companysettings->quote_prefix . '-';
        }
        else
        {
            $prefix = $this->slug . 'Q-';
        }
        return $prefix . sprintf('%06d', $this->quote_index);
    }

    public function isOwner(User $user)
    {
        return $this->user_id == $user->id;
    }

    public function lastinvoice()
    {
        return $this->hasOne('App\Models\Invoice')->latest()->limit(1)->first();
    }

    public function lastquote()
    {
        return $this->hasOne('App\Models\Quote')->latest()->limit(1)->first();
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'company_id');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\CompanyUserRequest', 'company_id');
    }

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote', 'company_id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'company_id');
    }

    public function itemtemplates()
    {
        return $this->hasMany('App\Models\ItemTemplate', 'company_id');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client', 'company_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'company_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function address()
    {
        return $this->hasOne('App\Models\CompanyAddress', 'company_id');
    }

    public function settings()
    {
        return $this->hasOne('App\Models\CompanySettings', 'company_id');
    }
}
