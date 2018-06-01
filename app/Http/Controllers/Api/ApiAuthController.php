<?php

namespace App\Http\Controllers\Api;

use File;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ApiAuthController extends Controller
{
    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['username' => request('username'), 'password' => request('password')])){
            $user = Auth::user();
            $token =  $user->createToken('MyApp')-> accessToken;

            if($user->avatar == "default.png") {
                $user['avatar_url'] = asset('uploads/avatars/default.png');
            } else {
                $user['avatar_url'] = asset('uploads/avatars/'.$user->name.'/'.$user->avatar);
            }

            return response()->json(['result' => 'success', 'user' => $user, 'token' => $token], $this-> successStatus);
        }
        else{
            return response()->json(['result' => 'error','msg'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'birth' => 'required',
            'password' => 'required|string|min:6',
            'gender' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'msg'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if($request->image) {
            $data = $request->image;

            $imageRand = time();
            $random_name = $user->id."_".$imageRand.".png";

            if(!is_dir(public_path('uploads/avatars/'.$user->name))){
                mkdir(public_path('uploads/avatars/'.$user->name));
            }

            $dst = public_path('uploads/avatars/'.$user->name.'/');

            if($user->avatar != 'default.png') {
                if (File::exists($dst . $user->avatar)) {
                    File::delete($dst . $user->avatar);
                }
            }

            $path = $dst.'/'.$random_name;

            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

            file_put_contents($path, $data);

            $user->avatar = $random_name;
            $user->save();

            $user['avatar_url'] = asset('uploads/avatars/'.$user->name.'/'.$user->avatar);
        } else {
            $user['avatar_url'] = asset('uploads/avatars/default.png');
        }

        $token =  $user->createToken('MyApp')-> accessToken;

        return response()->json(['result'=>'success', 'user' => $user, 'authToken' =>$token], $this-> successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        if ($user->avatar != "default.png" && File::exists('uploads/avatars/'.$user->name.'/'.$user->avatar)) {
            $user['avatar_url'] = asset('uploads/avatars/'.$user->name.'/'.$user->avatar);
        }
        return response()->json(['result' => 'success', 'user' => $user], $this-> successStatus);
    }
}
