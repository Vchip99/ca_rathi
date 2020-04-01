<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use File;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name', 'm_name', 'l_name', 'phone', 'password', 'email', 'photo', 'address'
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
     * add new user
     */
    protected static function addNewUser(Request $request){
        $fName = InputSanitise::inputString($request->get('f_name'));
        $mName = InputSanitise::inputString($request->get('m_name'));
        $lName = InputSanitise::inputString($request->get('l_name'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        $phone = InputSanitise::inputString($request->get('phone'));

        $newUser = static::where('id', $userId)->first();
        if(!is_object($newUser)){
            return static::create([
                'f_name' => $fName,
                'm_name' => $mName,
                'l_name' => $lName,
                'phone' => $phone,
                'password' => bcrypt('rathiclasses'),
            ]);
        } else {
            return $newUser;
        }
    }

    protected static function getUserByUserId(Request $request){
        $user = $request->user;
        // return static::where('id', $userId)->get();
        if($user > 0){
            return static::where('id', $user)->get();
        } else {
            return static::where('f_name', 'like', '%'.$user.'%')->orWhere('l_name', 'like', '%'.$user.'%')->get();
        }
    }

    /**
     * add new user
     */
    protected static function addNewAdmission(Request $request){
        $fName = InputSanitise::inputString($request->get('f_name'));
        $mName = InputSanitise::inputString($request->get('m_name'));
        $lName = InputSanitise::inputString($request->get('l_name'));
        $userId = InputSanitise::inputString($request->get('user_id'));
        $phone = InputSanitise::inputString($request->get('phone'));
        $email = InputSanitise::inputString($request->get('email'));
        $address = InputSanitise::inputString($request->get('address'));

        $newUser = static::where('id', $userId)->first();
        if(!is_object($newUser)){
            $newUser = static::create([
                'f_name' => $fName,
                'm_name' => $mName,
                'l_name' => $lName,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'password' => bcrypt('rathiclasses'),
            ]);
            if( is_object($newUser) && $request->exists('photo')){
                $userStoragePath = "user-storage/".$newUser->id;
                if(!is_dir($userStoragePath)){
                   File::makeDirectory($userStoragePath, $mode = 0777, true, true);
                }
                $userImage = $request->file('photo')->getClientOriginalName();
                $request->file('photo')->move($userStoragePath, $userImage);
                $newUser->photo = $userStoragePath."/".$userImage;
                $newUser->save();
            }
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getUsersByCourseIdBySubCourseIdByBatchId($courseId,$subcourseId,$batchId){
        return static::join('user_courses', 'user_courses.user_id', '=', 'users.id')
                ->where('user_courses.course_id', $courseId)
                ->where('user_courses.sub_course_id', $subcourseId)
                ->where('user_courses.batch_id', $batchId)->select('users.*')->get();
    }
}
