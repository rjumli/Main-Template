<?php

namespace App\Http\Controllers;

use Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as QR;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Spatie\Activitylog\Models\Activity;
use App\Http\Resources\ActivityResource;

class ProfileController extends Controller
{
    public function index(Request $request){
        $options = $request->option;
        switch($options){
            case 'authentication-logs':
                return $this->authenticationLogs($request);
            break;
            case 'activity-logs':
                return $this->activityLogs($request);
            break;
            case 'statistics':
                return $this->statistics();
            break;
            case 'two-factor':
                return $this->statistics();
            break;
            default: 
            return inertia('Modules/Profile/Index');
        }
    }

    public function store(Request $request){
       $option = $request->option;
       switch($option){
            case 'confirm':
                return $this->confirm2fa($request);
            break;
            default:
                return $this->enable2fa();
       }
    }
   
    public function update(ProfileRequest $request){
        $data = User::find(\Auth::user()->id);
        $data->profile()->update($request->except(['username', 'email']));
        
        return back()->with([
            'data' => $data,
            'message' => 'User update was successful!', 
            'info' => "You've successfully update user profile.",
            'status' => true
        ]);
    }

    public function enable2fa(){
        $google2fa = new QR();
        $key = $google2fa->generateSecretKey();
        $url = $google2fa->getQRCodeInline('DOST',\Auth::user()->email, $key);
      
        return [
            'key' => $key,
            'url' => $url
        ];
    }

    public function confirm2fa($request){
        $google2fa = new Google2FA();
        $google2fa->verifyKey($request->key, $request->code);
        return $google2fa;
    }

    public function twoFactor(){
        return 'wew';
    }

    public function authenticationLogs($request){
        return [];
    }

    public function activityLogs($request){
        $data = Activity::with('causer.profile')->orderBy('created_at','DESC')->paginate($request->count);
        return ActivityResource::collection($data);
    }

    public function statistics(){
        return [
            [
                'name' => 'Successful Login',
                'icon' => 'ri-checkbox-circle-fill',
                'color' => 'text-success',
                'total' => 0
            ],
            [
                'name' => 'Suspicious Login',
                'icon' => 'ri-error-warning-fill',
                'color' => 'text-warning',
                'total' => 0
            ],
            [
                'name' => 'Login Attempts',
                'icon' => 'ri-close-circle-fill',
                'color' => 'text-danger',
                'total' => 0
            ]
        ];
    }
}
