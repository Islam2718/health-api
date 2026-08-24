<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreRatingRequest;
use App\Infrastructure\Persistence\Models\Post;
use App\Infrastructure\Persistence\Models\Comment;
use App\Infrastructure\Persistence\Models\PostRating;
use Illuminate\Http\Request;
// auth 
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::where('is_public', true)
            ->with(['user', 'comments.user', 'comments.replies.user', 'ratings'])
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'comments.user', 'comments.replies.user', 'ratings.user'])->findOrFail($id);
        return response()->json($post);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $post = Post::create($data);
        return response()->json($post, 201);
    }

    public function comment(StoreCommentRequest $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $data = $request->validated();
        $data['post_id'] = $post->id;
        $data['user_id'] = Auth::id();
        $comment = Comment::create($data);
        return response()->json($comment, 201);
    }

    public function rate(StoreRatingRequest $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $data = $request->validated();
        $rating = PostRating::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['rating' => $data['rating'], 'review' => $data['review'] ?? null]
        );
        return response()->json($rating, 200);
    }
}
