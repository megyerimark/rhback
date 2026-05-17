namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLoadout;

class AvatarController extends Controller
{
    // A felszerelés mentése
    public function saveLoadout(Request $request)
    {
        // Megkeresi a felhasználó eddigi felszerelését, vagy létrehoz egy újat
        $loadout = UserLoadout::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                // Az Angular payloadjából kiolvassuk az ID-kat (ha van rajtuk valami)
                'headgear_id' => $request->input('headgear.id'),
                'top_id' => $request->input('top.id'),
                'bottom_id' => $request->input('bottom.id'),
                'accessory_id' => $request->input('accessory.id'),
                'background_id' => $request->input('background.id'),
            ]
        );

        return response()->json(['message' => 'Felszerelés sikeresen mentve!', 'loadout' => $loadout]);
    }
}