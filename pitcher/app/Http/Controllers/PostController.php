<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
            return response(['No post created yet. Create One!'], 404);
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
            return response(['You\'re not Logged in!'], 401);
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
            return response(['message' => 'No comment yet. Be the first one to comment!'], 404);
        }

        return response([
            "============POST============", $post,
            "============COMMENTS============",$comment->get()]);
    }

    public function getPostLike($id) 
    {
        $post = Post::find($id);

        if (!isset($post)){
            return response(['Alert' => 'Post not found!'], 404);
        }

        $like = like::where('post_id', '=', $id);
        
        return response([
            "============POST============", $post,
            "============LIKES============",$like->get()]);
    }

    public function store_likes(Request $request, int $id) 
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $post = Post::find($id);

        if (!isset($post)) {
            return response(['error' => 'Post not found!'], 403);
        }

        $validate = $request->validate([
            'type' => 'required|in:like,dislike'
        ]);

        $likecheck = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if (empty($likecheck)) {
            $like = new Like();
            $like->type = $validate['type'];

            if ($validate['type'] === 'like') {
                Post::where('id', $id)->increment('rating', 1);
                $like->user()->associate($user)->post()->associate($post)->save();
                return response(['message' => 'Post liked'], 201);
            } elseif(($validate['type'] === 'dislike')) {
                Post::where('id', $id)->decrement('rating', 1);
                $like->user()->associate($user)->post()->associate($post)->save();
                return response(['message' => 'Post disliked'], 201);
            }
        }

        $likecheck->type = $validate['type'];
        $likecheck->save();

        
        if ($validate['type'] === 'like') {
            if ($likecheck->type === 'dislike') {
                Post::where('id', $id)->increment('rating', 2);
                return response(['message' => 'Post liked'], 201);
            }elseif ($likecheck->type = 'like'){
                return response(['message' => 'Post already liked'], 201);
            }
        } elseif($validate['type'] === 'dislike'){
            if($likecheck->type === 'like'){
                Post::where('id', $id)->decrement('rating', 2);
                return response(['message' => 'Post disliked'], 201);
            }elseif($likecheck->type = 'dislike'){
                return response(['message' => 'Post already disliked'], 201);
            }
        }else {
            return response(['error' => 'Something went wrong!'], 404);
        }$likecheck->update();
    }

    public function deletePostLike(Request $request, int $id) {
        $user = Auth::user();
        $user = User::find($user->id);
        $post = Post::find($id);

        if (!isset($post)) {
            return response(['error' => 'Post not found!'], 403);
        }

        $likecheck = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();
        
        if(empty($likecheck)){
            return response(['Error' => 'Like doesn\'t exist.'], 201);
        }
        if($likecheck->type === 'like'){
            Post::where('id', $id)->decrement('rating', 1);
            Like::destroy($likecheck->id);
            return response(['message' => 'Like deleted successfully!'], 201);
        }
        elseif ($likecheck->type === 'dislike'){
            Post::where('id', $id)->increment('rating', 1);
            Like::destroy($likecheck->id);
            return response(['message' => 'Dislike deleted successfully!'], 201);
        } 

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

    public function postByCategory($id){
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
            return response(['error' => 'Category not found'], 404);
        }
            
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