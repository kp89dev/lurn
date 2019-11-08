<?php
namespace App\Http\Controllers\Admin\GeneralSettings;

use App\Models\Course;
use App\Models\CourseFeature;
use App\Models\InfusionsoftMerchantId;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class GeneralSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:general-settings,read')->only('read');
        $this->middleware('admin.role.auth:general-settings,write')->only('write');
    }

    public function index()
    {
        $isAccounts = array_flip(explode(',', env('IS_ACCOUNTS')));

        foreach ($isAccounts as $account => &$value) {
            $dbRecord = InfusionsoftMerchantId::where('account', $account)->first();

            if ($dbRecord) {
                $value = $dbRecord->ids;
            }
        }

        return view('admin.general-settings.index', compact('isAccounts'));
    }

    public function store(Request $request)
    {
        $isAccounts = explode(',', env('IS_ACCOUNTS'));

        foreach ($isAccounts as $isAccount) {
            $dbRecord = (new InfusionsoftMerchantId)->firstOrNew(['account' => $isAccount]);
            $dbRecord->ids = array_values(array_filter($request->get('id_' . $isAccount, [])));
            $dbRecord->save();

            Cache::tags(['is_merchant'])->flush();
        }

        return redirect()->route('view.settings');
    }
}
