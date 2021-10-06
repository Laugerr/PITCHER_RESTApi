<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use DB;
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
        $post = DB::table('posts');
        if(!(Post::exists())){
            return response(['No post created yet. Create One!'], 403);
        };
        return $post->paginate(10);
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
            'categories' => 'array|exists:categories,title',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $validate['title'],
            'content' => $validate['content'],
            'categories' => Implode(', ', $validate['categories']),
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

        return response([
            "============POST============", $post,
            "============COMMENTS============",$comment->get()]);
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
        $post = Post::find($id);

        if(!isset($post)){
            return response(['error' => 'Post not found!'], 404);
        }

        return $post;
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
    public function update(Request $request,int $id)
    {
        $user = Auth::id();
        $post = Post::find($id);

        if (!isset($post)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        $validate = $request->validate([
            'title' => 'string|max:60|min:3',
            'content' => 'string|max:8000',
            'categories' => 'nullable|array',
        ]);

        if($post->user_id != $user){
            return response(['Alert' => 'You\'re not the Author to update the post.']);
        }

        $post->update($validate);

        return response(['message' => 'Post updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, $id)
    {
        $user = Auth::id();
        $post = Post::find($id);

        if (!isset($post)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        if($post->user_id != $user) {
            return response(['Alert' => 'You\'re not the Author to delete the post.']);
        }

        Post::destroy($id);
        return response(['Message' => 'Post deleted successfully!'], 201);
    }
}
