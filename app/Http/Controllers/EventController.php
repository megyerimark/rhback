<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
    use Illuminate\Support\Facades\Http;

use Carbon\Carbon;

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
        'event_date' => 'required|date_format:Y-m-d H:i:s', // Stimmelnie kell a formátumnak!
        'embedded_media_url' => 'nullable|url'
        
        ]);

        $event = Event::create($validated);
       return response()->json([
            'message' => 'A buli sikeresen rögzítve a rendszerben!',
            'event' => $event
        ], 201);
    }


public function syncFacebookEvents()
{
    $pageId = env('FACEBOOK_PAGE_ID');
    $token = env('FACEBOOK_ACCESS_TOKEN');

    // Kérés küldése a Facebook Graph API-nak
    $response = Http::get("https://graph.facebook.com/v20.0/{$pageId}/events", [
        'access_token' => $token,
        'fields' => 'id,name,description,start_time,place,cover' // ezeket a mezőket kérjük el
    ]);

    if ($response->successful()) {
        $fbEvents = $response->json()['data'] ?? [];

        foreach ($fbEvents as $fbEvent) {
            // Megnézzük, hogy a Carbon formázni tudja-e az FB dátumát
            $eventDate = Carbon::parse($fbEvent['start_time']);

            // Az 'updateOrCreate' meggátolja a duplikációt: ha már létezik az FB ID alapján, csak frissíti
            Event::updateOrCreate(
                ['facebook_id' => $fbEvent['id']], // Ehhez kell egy facebook_id mező a tábládba!
                [
                    'title' => $fbEvent['name'],
                    'description' => $fbEvent['description'] ?? '',
                    'date' => $eventDate->toDateString(),
                    'time' => $eventDate->toTimeString(),
                    'location' => $fbEvent['place']['name'] ?? 'Arzenál',
                    // Ha van borítókép, azt is elmentheted
                    'image_url' => $fbEvent['cover']['source'] ?? null 
                ]
            );
        }

        return response()->json(['message' => 'Sikeres Facebook szinkronizáció!']);
    }

    return response()->json(['error' => 'Nem sikerült elérni a Facebook API-t'], 500);
}
}
