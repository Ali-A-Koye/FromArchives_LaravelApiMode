<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Http\Request;

use App\Http\Requests;

class AdminCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        //
        $categories = Category::all();

        return response()->json([
            'Result' => 'Success',
            'result' => count($categories),
            'Data' => $categories
        ]);;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $Category= Category::Create($request->all());
        return response()->json([
            'Result' => 'Success',
            'result' =>'Category Created',
            'Data' => $Category
        ]);
    }

    public function getOne($id)
    {
        //
        $categories = Category::findOrFail($id);

        return response()->json([
            'Result' => 'Success',
            'result' => 'Category Found',
            'Data' => $categories
        ]);;
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

        $category = Category::findOrFail($id);

       $category->update($request->all());

       $categoryUpdated=Category::findOrFail($id);

        return response()->json([
            'Result' => 'Success',
            'result' => 'Category Updated',
            'Data' => $categoryUpdated
        ]);;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //

        Category::findOrFail($id)->delete();
        return response()->json([
            'Result' => 'Success',
            'result' => 'Category deleted'
        ]);
    }
}
