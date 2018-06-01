<?php

namespace App\Http\Controllers\Api;

use File;
use App\User;
use App\Cult;
use App\PostPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiPostController extends Controller
{
    public $successStatus = 200;

    public function postPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'caption' => 'required|string',
            'cult_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'msg'=>$validator->errors()], 401);
        }

        $user = Auth::user();

        $data = $request->image;

        $imageRand = time();
        $random_name = $user->id."_".$imageRand.".png";

        if(!is_dir(public_path('uploads/photos/'.$user->username))){
            mkdir(public_path('uploads/photos/'.$user->username));
        }

        $dst = public_path('uploads/photos/'.$user->username.'/');

        $path = $dst.'/'.$random_name;

        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

        file_put_contents($path, $data);

        $post = new PostPhoto;
        $post->user_id = $user->id;
        $post->photo_file = $random_name;
        $post->photo_caption = $request->caption;
        $post->cult_id = $request->cult_id;
        $post->save();

        $post['image_url'] = asset('uploads/photos/'.$user->username.'/'.$post->photo_file);

        return response()->json(['result' => 'success', 'post' => $post], $this-> successStatus);
    }
}
