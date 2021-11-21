<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\User;
use App\Photo;
use App\OauthAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['loggedIn']]);
    }



    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($login)) {
            return response()->JSON(['error' => 'Invalid Login Informations']);
        }

        $accessToken = Auth::user()->createToken('AuthToken')->accessToken;

        return response()->JSON([
            'user' => Auth::user(),
            'access_Token' => $accessToken
        ]);
    }




    public function register(Request $request)
    {

        $valid = validator($request->only('email', 'name', 'password', 'mobile'), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            $jsonError = response()->json($valid->errors()->all(), 400);
            return $jsonError;
        }

        $data = request()->only('email', 'name', 'password', 'mobile');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 3
        ]);

        Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        $user = Auth::user();
        $accessToken = Auth::user()->createToken('AuthToken')->accessToken;

        return response()->JSON([
            'result' => 'success : Account Created',
            'user' => Auth::user(),
            'access_Token' => $accessToken
        ]);
    }

    public function logout()
    {
        if (Auth::check()) {

            OauthAccessToken::where('user_id', Auth::user()->id)->delete();
            return response()->JSON([
                'result' => 'Success',
                'data' => 'User Logged Out'
            ], 200);
        } else {
            return response()->JSON([
                'result' => 'Fail',
                'data' => 'User Should Login'
            ]);
        }
    }

    public function loggedIn()
    {
        $user = Auth::user();
        if (!$user->accesible) {
            return response()->JSON([
                'Status' => 'success',
                'data' => 'user not logged in'
            ]);
        }
        return response()->JSON([
            'Status' => 'success',
            'data' => $user
        ]);
    }


    public function DeleteMe(Request $request)
    {

        User::findOrFail(Auth::user()->id)->delete();
        OauthAccessToken::where('user_id', Auth::user()->id)->delete();

        return response()->JSON([
            'Result' => 'Success',
            'Data' => 'user deleted!'
        ], 200);
    }

    public function updateMe(Request $request)
    {
        $input = $request->except(['role', 'password']);

        $user = user::findOrFail(Auth::user()->id);


        if ($file = $request->file('photo_id')) {

            if(!is_null($user->photo)){
                unlink(public_path().'/userimg/'.$user->photo->file);
            }
            $name = time() . $file->getClientOriginalName();
            $file->move('userimg', $name); //saving the file into the images folder

            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        };

        $user->update($input);

        return response()->JSON([
            'Result' => 'Success',
            'Data' => $user
        ], 200);
    }


    public function updatePassword(Request $request)
    {
        $request_data = $request->only(['current-password','password']);
        if (Auth::Check()) {
            $validator =validator($request_data, [
                'current-password' => 'required|string|min:6',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
            } else {
                $current_password = Auth::User()->password;
                if (Hash::check($request_data['current-password'], $current_password)) {
                    $user_id = Auth::User()->id;
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['password']);
                    $obj_user->save();

                    OauthAccessToken::where('user_id', $obj_user->id)->delete();
                    $LoginAgainToken = Auth::user()->createToken('AuthToken')->accessToken;
                    return response()->JSON([
                        'Result' => 'Success',
                        'Data' => 'Password Changed',
                        'access_Token' => $LoginAgainToken
                    ], 200);
                } else {
                    $error = array('current-password' => 'Please enter correct current password');
                    return response()->json(array('error' => $error), 400);
                }
            }
        }
    }







}
