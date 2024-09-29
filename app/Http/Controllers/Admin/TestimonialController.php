<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    public function publish()
    {
        $testimonials = Testimonial::where('status', 'published')->get();
        return view('admin.testimonial.publish', compact('testimonials'));
    }

    public function draft()
    {
        $testimonials = Testimonial::where('status', 'draft')->get();
        return view('admin.testimonial.draft', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonial.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published', // Validate that status is either 'draft' or 'published'
        ]);

        // Create a new Testimonial instance
        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->text = $request->text;

        // Handle the image upload if an image is provided
        if ($request->hasFile('image')) {
            $testimonial->image = $request->file('image')->store('testimonials', 'public');
        }

        // Set the status dynamically based on the form submission
        $testimonial->status = $request->status;
        if (Auth::guard('admin')->user()->user_type == 1 || Auth::guard('admin')->user()->user_type == 2) {
            $testimonial->created_by = Auth::guard('admin')->user()->id;
        } else {
            $testimonial->created_by = Auth::guard('admin')->user()->id;
        }

        // Save the testimonial
        $testimonial->save();

        // Redirect back to the form with a success message
        return redirect()->route('admin.testimonial.create')->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonial.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        // Define validation for the form fields
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        // Update testimonial fields
        $testimonial->name = $request->name;
        $testimonial->text = $request->text;

        // Check if an image is provided and update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            // if ($testimonial->image) {
            //     \Storage::delete($testimonial->image);
            // }
            // Store the new image
            $testimonial->image = $request->file('image')->store('testimonials', 'public');
        }

        // Update status (draft or published)
        $testimonial->status = $request->status;

        // Save the updated testimonial
        $testimonial->save();

        // Redirect back to the appropriate page with success message
        return redirect()->route('admin.testimonial.publish')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        // Check if the testimonial exists
        if (!$testimonial) {
            return redirect()->route('admin.testimonial.publish')->with('error', 'Testimonial not found.');
        }

        // Optionally, delete the associated image if it exists
        if ($testimonial->image) {
            \Storage::disk('public')->delete($testimonial->image);
        }

        // Delete the testimonial
        $testimonial->delete();

        // Redirect back to the testimonials list with a success message
        return redirect()->route('admin.testimonial.publish')->with('success', 'Testimonial deleted successfully.');
    }

    public function changeStatus($id, $status)
    {
        // Validate the status input (must be 'draft' or 'published')
        if (!in_array($status, ['draft', 'published'])) {
            return redirect()->back()->with('error', 'Invalid status provided.');
        }

        // Find the testimonial by ID
        $testimonial = Testimonial::findOrFail($id);

        // Update the status
        $testimonial->status = $status;
        $testimonial->save();

        // Success message and redirect depending on the new status
        if ($status === 'draft') {
            return redirect()->route('admin.testimonial.publish')->with('success', 'Testimonial moved to draft successfully.');
        } else {
            return redirect()->route('admin.testimonial.draft')->with('success', 'Testimonial published successfully.');
        }
    }

    public function getPublishedTestimonials()
    {
        // Get all testimonials with status 'published'
        $testimonials = Testimonial::where('status', 'published')->get();

        return response()->json($testimonials);
    }

    public function fetchPublished()
    {
        $testimonials = Testimonial::where('status', 'published')->get();
        return response()->json($testimonials);
    }
}
