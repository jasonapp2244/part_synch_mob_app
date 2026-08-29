<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Review moderation.
 *
 * Reviews from an unverified purchase land in 'pending' and stay invisible to
 * shoppers until approved here.
 */
class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $reviews = Review::with([
                'user:id,first_name,last_name,email',
                'product:id,name',
                'vendor:id,first_name,last_name,business_name',
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        $counts = [
            'pending' => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('admin.view_reviews', compact('reviews', 'counts', 'status'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $review = Review::findOrFail($id);
        $review->update(['status' => $request->status]);

        return back()->with('success', 'Review marked as ' . $request->status . '.');
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();

        return back()->with('success', 'Review deleted.');
    }
}
