<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{


public function getNotifications(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
    }

    // Récupérer toutes les notifications de l'utilisateur connecté
    $notifications = Notification::where('nom', $user->nom)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($notifications);
}

//nombre 
public function markAsRead(Request $request)
{
    $user = $request->user();
    $user->notifications()->where('is_read', false)->update(['is_read' => true]);
    return response()->json(['success' => true]);
}

public function unreadCount()

    {
        $user = Auth::user();

        // Supposons que tu as une relation notifications() dans le modèle User
        $count = $user->notifications()->whereNull('read_at')->count();

        return response()->json(['count' => $count]);
    }

    //suprrimer le notification 
    public function destroy($id)
    {
    $notification = Notification::find($id);

    if (!$notification) {
        return response()->json(['message' => 'Notification non trouvée.'], 404);
    }

    $notification->delete();

    return response()->json(['message' => 'Notification supprimée avec succès.']);
    }


}
