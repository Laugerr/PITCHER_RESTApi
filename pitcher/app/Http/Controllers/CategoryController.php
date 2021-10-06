<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string|unique:categories,title|min:3|max:256',
            'description' => 'nullable|string|max:1400',
        ]);

        $category = Category::create([
            'title' => $validate['title'],
            'description' => $validate['description'],
        ]);

        $response = ['============New Category Created!============', $category];
        return response($response, 201);
    }

    public function postsByCategory($id)
    {
        $posts = Post::all();
        $category = Category::find($id);

        if($category){
            $res = [];
            foreach($posts as $post){
                if(str_contains(strtoupper($post->categories), strtoupper($category->title))){
                    array_push($res, $post);
                }
            }
            return $res;
        }else{
            return response(['Alert' => 'Invalid Category!']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);

        if(!isset($category)){
            return response(['error' => 'Category not found!'], 404);
        }

        return $category;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,int $id)
    {
        $category = Category::find($id);

        if (!isset($category)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        $validate = $request->validate([
            'title' => 'string|unique:categories,title|min:3|max:256',
            'description' => 'nullable|string|max:1400',
        ]);

        $category->update($validate);

        return response(['message' => 'Category updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category, $id)
    {
        $category = Category::find($id);

        if (!isset($category)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        Category::destroy($id);
        return response(['Message' => 'Category deleted successfully!'], 201);
    }
}
