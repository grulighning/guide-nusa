<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Pemandu;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string',
            'destination_id' => 'required|exists:destinasis,id',
            'guide_id'       => 'required|exists:pemandus,id',
        ]);

        Review::create([
            'user_id'        => auth()->id(),
            'destination_id' => $validated['destination_id'],
            'guide_id'       => $validated['guide_id'],
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'],
        ]);

        $this->updateAverageRatings($validated['guide_id'], $validated['destination_id']);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    public function update(Request $request, Review $review)
    {
        abort_if($review->user_id !== auth()->id(), 403, 'Anda tidak berhak mengubah ulasan ini.');

        $validated = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'nullable|string',
        ]);

        $review->update([
            'rating'  => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        $this->updateAverageRatings($review->guide_id, $review->destination_id);

        return redirect()->back()->with('success', 'Ulasan berhasil diperbarui!');
    }

    public function destroy(Review $review)
    {
        abort_if($review->user_id !== auth()->id(), 403, 'Anda tidak berhak menghapus ulasan ini.');

        $guideId = $review->guide_id;
        $destId  = $review->destination_id;

        $review->delete();

        $this->updateAverageRatings($guideId, $destId);

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus.');
    }

    /**
     * Update rating rata-rata pemandu dan destinasi berdasarkan review yang ada.
     */
    private function updateAverageRatings(int $guideId, int $destId): void
    {
        $avgGuideRating = Review::where('guide_id', $guideId)->avg('rating');
        Pemandu::where('id', $guideId)->update(['rating' => $avgGuideRating]);

        $avgDestRating = Review::where('destination_id', $destId)->avg('rating');
        Destinasi::where('id', $destId)->update(['rating' => $avgDestRating]);
    }
}
