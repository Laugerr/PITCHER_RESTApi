<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post = Post::all();
        if(!(Post::exists())){
            return response(['No post created yet. Create One!'], 403);
        };
        return $post;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Auth::user()){
        $validate = $request->validate([
            'title' => 'required|string|max:60|min:3',
            'content' => 'required|string|max:8000',
            'categories' => 'nullable|array',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $validate['title'],
            'content' => $validate['content'],
        ]);

        $response = [
            '' => '============Post Created Successfully !============',
            'post' => $post,
        ];

        return response($response, 201);}
        else{
            return response(['You\'re not Logged in!'], 403);
        };
    }

    public function commentCreate(Request $request, int $id) {
        $post = Post::find($id);

        $validate = $request->validate([
            'content' => 'required|string|max:1800|min:0'
        ]);

        if (!isset($post)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $id,
            'content' => $validate['content'],
        ]);

        return response(['message' => 'You created a Comment!'], 201);
    }

    public function indexComment($id) {
        $post = Post::find($id);

        if (!isset($post)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        $comment = Comment::where('post_id', '=', $id);

        if(!isset($comment)){
            return response(['message' => 'No comment yet. Be the first one to comment!'], 201);
        }

        return [
            "============POST============", $post,
            "============COMMENTS============",$comment->get()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
