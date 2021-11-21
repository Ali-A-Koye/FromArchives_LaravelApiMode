<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\postCreateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Post;
use App\Photo;
use App\Category;

use App\Http\Requests;

class AdminPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request)
    {
        //
        $post = Post::paginate(1);
        $post[0]->category_id = $post[0]->category()->get();
        return $post;

        return response()->JSON([
            'status' => 'Success',
            'result' => count($post),
            'page' => $request->query('page'),
            'Data' => $post
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //postCreateRequest
    public function create(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        if ($file = $request->file('photo_id')) {

            $name = time() . $file->getClientOriginalName();
            $file->move('images', $name); //saving the file into the images folder

            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        };

        $post= $user->posts()->create($input);

        return response()->json([
            'status' => 'success',
            'result'=>'Post Created',
            'data'=> $post
        ]);


        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function GetOne($id)
    {
        $post = Post::findOrFail($id);

        return response()->json([
            'status' =>'success',
            'result' => 'Post Found',
            'Data' =>  $post
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $input = $request->all();

        if ($file = $request->file('photo_id')) {

            $name = time() . $file->getClientOriginalName();
            $file->move('images', $name); //saving the file into the images folder

            $photo = Photo::create(['file' => $name]);

            $input['photo_id'] = $photo->id;
        };

            Auth::user()->posts()->whereId($id)->first()->update($input);

            $updatedPost = Post::findOrFail($id);
          return response()->json([
            'status' =>'success',
            'result' => 'Post Updated',
            'Data' => $updatedPost
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        $post = post::findOrFail($id);

        unlink(public_path() . "/images/" . $post->photo->file);

        $post->delete();

        Session::flash('deleted_user', "Post has been Deleted");
        return response()->json([
            'status' =>'success',
            'result' => 'Post Deleted'
        ]);    }
}
