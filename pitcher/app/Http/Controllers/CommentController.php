<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function getCommentLike($id) 
    {
        $comment = Comment::find($id);

        if (!isset($comment)){
            return response(['Alert' => 'Comment not found!'], 404);
        }

        $like = Like::where('comment_id', '=', $id);
        
        return response([
            "============POST============", $comment,
            "============LIKES============",$like->get()]);
        
    }

    public function store_likes(Request $request, int $id) 
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $comment = Comment::find($id);

        if (!isset($comment)) {
            return response(['error' => 'Post not found!'], 403);
        }

        $validate = $request->validate([
            'type' => 'required|in:like,dislike'
        ]);

        $likecheck = Like::where('user_id', $user->id)->where('comment_id', $comment->id)->first();

        if (empty($likecheck)) {
            $like = new Like();
            $like->type = $validate['type'];

            if ($validate['type'] === 'like') {
                Comment::where('id', $id)->increment('rating', 1);
                $like->user()->associate($user)->comment()->associate($comment)->save();
                return response(['message' => 'Comment Liked'], 201);
            } elseif(($validate['type'] === 'dislike')) {
                Comment::where('id', $id)->decrement('rating', 1);
                $like->user()->associate($user)->comment()->associate($comment)->save();
                return response(['message' => 'Post disliked'], 201);
            }
        }

        $likecheck->type = $validate['type'];
        $likecheck->save();

        
        if ($validate['type'] === 'like') {
            if ($likecheck->type === 'dislike') {
                Comment::where('id', $id)->increment('rating', 2);
                return response(['message' => 'Comment Liked'], 201);
            }elseif ($likecheck->type = 'like'){
                return response(['message' => 'Comment already liked'], 201);
            }
        } elseif($validate['type'] === 'dislike'){
            if($likecheck->type === 'like'){
                Comment::where('id', $id)->decrement('rating', 2);
                return response(['message' => 'Comment disliked'], 201);
            }elseif($likecheck->type = 'dislike'){
                return response(['message' => 'Comment already disliked'], 201);
            }
        }else {
            return response(['error' => 'Something went wrong!'], 404);
        }$likecheck->update();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::find($id);

        if(!isset($comment)){
            return response(['error' => 'Comment not found!'], 404);
        }

        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,int $id)
    {
        $user = Auth::id();
        $comment = Comment::find($id);

        if (!isset($comment)){
            return response(['Alert' => 'Comment not found!'], 404);
        }

        $validate = $request->validate([
            'content' => 'string|max:1800|min:0'
        ]);

        if($comment->user_id != $user){
            return response(['Alert' => 'You\'re not the Author to update the comment.']);
        }

        $comment->update($validate);

        return response(['message' => 'Comment updated successfully'], 201);
    }

    public function deleteCommentLike(Request $request, int $id) {
        $user = Auth::user();
        $user = User::find($user->id);
        $comment = Comment::find($id);

        if (!isset($comment)) {
            return response(['error' => 'Comment not found!'], 403);
        }

        $likecheck = Like::where('user_id', $user->id)->where('comment_id', $comment->id)->first();
        
        if(empty($likecheck)){
            return response(['Error' => 'Like doesn\'t exist.'], 201);
        }
        if($likecheck->type === 'like'){
            Comment::where('id', $id)->decrement('rating', 1);
            Like::destroy($likecheck->id);
            return response(['message' => 'Like deleted successfully!'], 201);
        }
        elseif ($likecheck->type === 'dislike'){
            Comment::where('id', $id)->increment('rating', 1);
            Like::destroy($likecheck->id);
            return response(['message' => 'Dislike deleted successfully!'], 201);
        } 

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment, $id)
    {
        $user = Auth::id();
        $comment = Comment::find($id);

        if (!isset($comment)){
            return response(['Alert' => 'Comment not found!'], 404);
        }

        if($comment->user_id != $user) {
            return response(['Alert' => 'You\'re not the Author to delete the comment.']);
        }

        Comment::destroy($id);
        return response(['Message' => 'Comment deleted successfully!'], 201);
    }
}
