<?php

namespace App\Http\Controllers\Api;

use File;
use App\User;
use App\Cult;
use App\FriendRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    public $successStatus = 200;

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();
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
        return response()->json(['result' => 'success', 'user' => $user], $this-> successStatus);
    }

    public function imagUpload($path, $image, $image_name)
    {
        if($image->move($path, $image_name)) {
            return 'success';
        }
        return "fail";
    }

    public function findFriend(Request $request)
    {
        $emails = $request->fri_emails;
        $friends = array();
        foreach ($emails as $email) {
            $friend = User::where('email', $email)->where('id', '!=', Auth::user()->id)->first();
            if($friend) {
                $friend['avatar_url'] = asset('uploads/avatars/default.png');
                if ($friend->avatar != "default.png" && File::exists('uploads/avatars/'.$friend->name.'/'.$friend->avatar)) {
                    $friend['avatar_url'] = asset('uploads/avatars/'.$friend->name.'/'.$friend->avatar);
                }
                array_push($friends, $friend);
            }
        }

        return response()->json(['friends' => $friends],  $this-> successStatus);
    }

    public function newCult(Request $request)
    {
        $user = Auth::user();
        $new_cult = new Cult;
        $new_cult->cult_name = $request->cultname;
        $new_cult->user_id = $user->id;
        $new_cult->save();

        $cults = Cult::whereIn('user_id', [$user->id, 0])->get();

        return response()->json(['result' => 'success', 'cults' => $cults], $this-> successStatus);
    }

    public function fetAllCult()
    {
        $user = Auth::user();
        $cults = Cult::whereIn('user_id', [$user->id, 0])->get();

        return response()->json(['result' => 'success', 'cults' => $cults], $this-> successStatus);
    }

    public function friendRequest(Request $request)
    {
        $user = Auth::user();
        $to_user = $request->user_id;
        $cult = $request->cult_id;

        $newRequest = new FriendRequest;
        $newRequest->from_user_id = $user->id;
        $newRequest->to_user_id = $to_user;
        $newRequest->cult_id = $cult;
        $newRequest->save();

        return response()->json(['result' => 'success'], $this-> successStatus);
    }
}
