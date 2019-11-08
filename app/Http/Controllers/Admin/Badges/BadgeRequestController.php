<?php
namespace App\Http\Controllers\Admin\Badges;

use App\Http\Controllers\Controller;
use App\Models\Badge\BadgeRequest;
use App\Models\Credly;

class BadgeRequestController extends Controller
{
    public function requireAttention()
    {
        $badgeReq = BadgeRequest::with('user', 'badge', 'files')
                            ->where('status', 0)
                            ->latest()
                            ->paginate(20);

        return view('admin.badge-requests.require-attention', compact('badgeReq'));
    }

    public function oldRequests()
    {
        $badgeReq = BadgeRequest::with('user', 'badge', 'files')
            ->where('status', '<>', 0)
            ->latest()
            ->paginate(20);

        return view('admin.badge-requests.old-requests', compact('badgeReq'));
    }

    public function approve(BadgeRequest $badgeRequest)
    {
        $badgeRequest->status = 1;
        $badgeRequest->save();

        $badgeRequest->user->badges()->attach($badgeRequest->badge_id);
        
        $credly = new Credly();
        $credly->giveBadge($badgeRequest->user->id, $badgeRequest->badge_id);
        
        $badgeRequest->save();
        return redirect()->back()->with(['success' => 'Badge request successfully approved']);
    }

    public function reject(BadgeRequest $badgeRequest)
    {
        $badgeRequest->status = 2;
        $badgeRequest->save();

        return redirect()->back()->with(['success' => 'Badge request successfully rejected']);
    }
}
