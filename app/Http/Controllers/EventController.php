<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('event_date','asc')->get();
        return response()->json($events, 200);
    }

    //bulik létrehozása
    public function store(Request $request){
        if(!$request->user()->hasRole('admin')){
            return response()->json(['message' => 'Nincs jogosultságod ehhez a művelethez.'], 403);
        }
        //ellenőrzés

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'embedded_media_url' => 'nullable|url',
        
        ]);

        $event = Event::create($validated);
       return response()->json([
            'message' => 'A buli sikeresen rögzítve a rendszerben!',
            'event' => $event
        ], 201);
    }
}
