<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'password_masked',
        'status',
        'branch_id',
        'role_id',
        'karyawan_id',
        'image',
        'remember_token',
        'created_at',
        'updated_at',
        'nama_panggilan'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that appends to returned entities.
     *
     * @var array
     */
    // protected $appends = ['image'];

    /**
     * The getter that return accessible URL for user photo.
     *
     * @var array
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->image !== null) {
            return url('media/user/' . $this->id . '/' . $this->image);
        } else {
            return url('media-example/no-image.png');
        }
    }

    public function akses($column, $param = null, $abort = false)
    {
        $hakAkses = new \App\Models\HakAkses();
        $menu = null;
        if (is_null($param)) {
            if (request()->segment(3) == null) {
                $slug = request()->segment(1);
                $menu = \App\Models\Menu::where('url', 'like', $slug . '%')->first();
            } elseif (request()->segment(4) == null) {
                $slug = request()->segment(2);
                $menu = \App\Models\Menu::where('url', 'like', $slug . '%')->first();
            } else {
                $slug = request()->segment(2);
                $menu = \App\Models\Menu::where('url', 'like', $slug . '%')->first();
            }
        } else {
            $menu = \App\Models\Menu::where('name', 'like', $param . '%')->first();
        }

        if (!is_null($menu)) {
            $data = $hakAkses
                ->where(function ($q) use ($menu, $param) {
                    $q->where('menu_id', $menu->id);
                })
                ->where('role_id', Auth::user()->role_id)
                ->where($column, 'true')
                ->first();
        } else {
            $hak_akses = new \App\Models\HakAkses();
            $data = $hak_akses
                ->where(function ($q) {
                    $q->where('menu_id', 1);
                })
                ->where($column, 'true')
                ->where('role_id', Auth::user()->role_id)
                ->first();
        }

        if (is_null($data)) {
            if (Auth::user()->role_id != 1) {
                if ($abort) {
                    abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
                }
                $validation = false;
            } else {
                $validation = true;
            }
        } else {
            $validation = true;
        }


        return $validation;
    }

    public function aksesMenu($column, $param = null)
    {
        $hakAkses = new \App\Models\HakAkses();
        $menu = null;
        $subMenu = null;

        $menu = \App\Models\Menu::where('url', 'like', $param . '%')->first();


        $data = $hakAkses
            ->where(function ($q) use ($menu, $subMenu, $param) {
                $q->where('menu_id', $menu->id);
            })
            ->where('role_id', Auth::user()->role_id)
            ->where($column, 'true')
            ->first();

        if (is_null($data)) {
            $validation = false;
        } else {
            $validation = true;
        }



        if (Auth::user()->role_id == 1) {
            $validation = true;
        }
        return $validation;
    }

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function Role()
    {
        return $this->belongsTo(Role::class);
    }

    public function Karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function Pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'dokter', 'id');
    }

    public function RekamMedisPasien()
    {
        return $this->hasMany(RekamMedisPasien::class, 'created_by', 'id');
    }

    public function RekamMedisRekomendasiTindakanBedah()
    {
        return $this->hasMany(RekamMedisRekomendasiTindakanBedah::class, 'updated_by', 'id');
    }

    public function DokterPeminta()
    {
        return $this->belongsTo(PindahJadwalJaga::class, 'id', 'dokter_peminta');
    }

    public function DokterDiminta()
    {
        return $this->belongsTo(PindahJadwalJaga::class, 'id', 'dokter_diminta');
    }
}
