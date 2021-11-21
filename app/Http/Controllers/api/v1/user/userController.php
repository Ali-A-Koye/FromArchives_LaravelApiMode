<?php

namespace App\Http\Controllers\api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Photo;

class userController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function FindMe()
    {
        Auth::user()->role_id = Auth::user()->role()->get('name')->first()->name;
        unset(Auth::user()->role);
        return response()->JSON([
            'Result' => 'Success',
            'Data' => Auth::user()
        ], 200);
    }



    public function getAll()
    {
        $users = User::all();
        return response()->JSON([
            'status' => 'Success',
            'result' => count($users),
            'Data' =>$users
        ], 200);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $valid = validator($request->only('email', 'name', 'password', 'mobile'), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            $jsonError = response()->json($valid->errors()->all(), 400);
            return $jsonError;
        }
        $data = request()->only('email', 'name', 'password');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 3
        ]);


        return response()->JSON([
            'status' => 'success',
            'result' => 'Account Created',
            'data'=>$user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOne($id)
    {
        $user=User::findOrFail($id);

        return response()->JSON([
            'Result' => 'Success',
            'Data' => $user
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $input = $request->all();

        $user=User::findOrFail($id);



        if ($file = $request->file('photo_id')) {

            if(!is_null($user->photo)){
                unlink(public_path().'/userimg/'.$user->photo->file);
            }
            $name = time() . $file->getClientOriginalName();
            $file->move('userimg', $name); //saving the file into the images folder

            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        };

        if(isset($input['password'])){
            $input['password']=bcrypt($input['password']);
        }



        $user->update($input);

        return response()->JSON([
            'status' => 'Success',
            'data' => 'Accounted Updated',
            'Data' => $user
        ], 200);
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return response()->JSON([
            'Result' => 'success',
            'Data' => 'user deleted!'
        ], 200);
        //
    }
}
