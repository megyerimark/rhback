<?php

namespace App\Http\Controllers;

use App\Models\virtualItem;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $items = virtualItem::all();
        return response()->json($items, 200);
    }

    public function buy(Request $request, $itemId)
    {
         $user = $request->user();
         $item = virtualItem::findOrFail($itemId);

         if($user->virtualItems()->where('virtual_item_id', $itemId)->exists()){
            return response()->json(['message' => 'Már megvásároltad ezt a tárgyat.'], 400);
         }
         if($user->ravecoin_balance < $item->price_ravecoin){
            return response()->json(['message' => 'Nincs elég RaveCoinod a vásárláshoz.'], 400);
         }
         $user->ravecoin_balance -= $item->price_ravecoin;
         $user->save();
         $user->virtualItems()->attach($item->id);

         return response()->json([
            'message' => 'Sikeres vásárlás!',
            'new_balance' => $user->ravecoin_balance,
            'item' => $item
        ], 200);
    }
}