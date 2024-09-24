<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function create()
    {
        return view('admin.post.create');
    }

    // Store a newly created post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully and is pending approval.');
    }

    // Display all posts (admin view)
    public function index()
    {
        $posts = Post::all();
        return view('admin.post.index', compact('posts'));
    }

    //Show a Post
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.post.show', compact('post'));
    }

    // Approve a post
    public function approve($id)
    {
        $post = Post::findOrFail($id);
        $post->status = 'approved';
        $post->save();
        return response()->json(['danger' => 'Post approved successfully.']);
        // return redirect()->route('admin.posts.index')->with('success', 'Post approved successfully.');
    }

    // Reject a post
    public function reject($id)
    {
        $post = Post::findOrFail($id);
        $post->status = 'rejected';
        $post->save();

        // return redirect()->route('admin.posts.index')->with('success', 'Post rejected successfully.');
        return response()->json(['success' => 'Post rejected successfully.']);
    }

    // Show the form for editing a post
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.post.edit', compact('post'));
    }

    // Update a post
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'status' => 'required|in:pending,approved',
        ]);

        $post->update($request->only('title', 'content', 'author', 'status'));

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    // Soft delete a post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['danger' => 'Post deleted successfully.']);
        // return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }


    
}
