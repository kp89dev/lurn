<?php
namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPointActivity;
use App\Models\DeskCom;
use App\Models\EmailStatus;
use App\Models\UserRefund;
use App\Services\Woopra\Woopra;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function index(User $user)
    {
        $analytics = app()->make(Woopra::class);
        
        $result = $analytics->post('profile/visits', [
            'key'         => 'email',
            'value'       => $user->email,
            'date_format' => 'yyyy-MM-dd',
            'start_day'   => date('Y-m-d', time() - (3600 * 24 * 15)),
            'end_day'     => date('Y-m-d'),
            'limit'       => 100
        ]);
        
        $activity = json_decode((string) $result->getBody(), true);
        
        try {
            $desk = new DeskCom();
        
            $support = $desk->getCaseHistory($user);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $support = "error";
        }

        $emails = EmailStatus::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $engagements = UserPointActivity::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $refunds = UserRefund::where('user_id', '=', $user->id)
            ->orderBy('refunded_at', 'DESC')
            ->get();

        return view('admin.users.show', compact('user', 'activity', 'support', 'emails', 'engagements', 'refunds'));
    }
}
