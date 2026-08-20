<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $users = [];

        if ($query) {
            $users = User::where('id', '!=', Auth::id())
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('username', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get();
        }

        return view('user.search', [
            'users' => $users,
            'query' => $query
        ]);
    }

    public function publicProfile($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent viewing own profile through public route, redirect to standard profile
        if ($user->id === Auth::id()) {
            return redirect()->route('user.profile');
        }

        // Actual stats for public profile
        $streak = $user->current_streak ?? 0;
        $displayedAchievements = $user->displayedAchievements();
        $levelInfo = $user->getLevelInfo();
        $xpProgress = $user->getXpProgress();
        
        return view('user.public-profile', [
            'user' => $user,
            'streak' => $streak,
            'followers' => $user->followers()->count(),
            'following' => $user->followings()->count(),
            'joinedAt' => $user->created_at->translatedFormat('d F Y'),
            'displayedAchievements' => $displayedAchievements,
            'levelInfo' => $levelInfo,
            'xpProgress' => $xpProgress
        ]);
    }

    public function toggleFollow($id)
    {
        $userToFollow = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($userToFollow->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak bisa mengikuti diri sendiri.');
        }

        if ($currentUser->isFollowing($userToFollow)) {
            $currentUser->followings()->detach($userToFollow->id);
            $status = 'Batal mengikuti ' . $userToFollow->name;
        } else {
            $currentUser->followings()->attach($userToFollow->id);
            $status = 'Mulai mengikuti ' . $userToFollow->name;
        }

        return back()->with('status', $status);
    }
}
