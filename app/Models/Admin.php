<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    const SuperSuperAdmin = 1;
    const SuperAdmin = 2;
    const Admin = 3;
    const SubAdmin = 4;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }

    /**
     *  create/update admin and assingn type
     */
    protected static function createOrUpdateAdmin(Request $request, $isUpdate = false){
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');
        $adminId = $request->get('admin_id');
        $type = $request->get('type');
        if($isUpdate && !empty($adminId)){
            $admin = static::find($adminId);
            if(!is_object($admin)){
                return 'false';
            }
            if(!empty($password)){
                $admin->password = bcrypt($password);
            }
        } else {
            $admin = new static;
            $admin->password = bcrypt($password);
        }
        $admin->name = $name;
        $admin->email = $email;
        $admin->type = $type;
        $admin->save();
        return $admin;
    }
}
