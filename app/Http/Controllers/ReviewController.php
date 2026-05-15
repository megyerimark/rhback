<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($eventId)
    {
       $event = Event::findOrFail($eventId);
       $revievs = Review::with('user:id,name')->where('event_id', $eventId)->latest()->get();

       return response()->json($revievs, 200);
    }

    public function store ( Request $request, $eventId){
        $event = Event::findOrFail($eventId);

        $validated = $request->validate([
            'comment' => 'nullable|string',
            'rating_sound' => 'required|integer|min:1|max:5',
            'rating_vibe' => 'required|integer|min:1|max:5',
        ]);
        $review = Review::create([
            'user_id' => $request->user()->id,
            'event_id' => $event->id,
            'comment' => $validated['comment'],
            'rating_sound' => $validated['rating_sound'],
            'rating_vibe' => $validated['rating_vibe'],
        ]);
        return response()->json([
            'message' => 'Köszönjük a véleményedet!',
            'review' => $review
        ], 201);

    }
}
