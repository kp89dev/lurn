<?php
namespace App\Http\Controllers\Admin\Stats;

use App\Http\Requests\Admin\Course\StoreCategoryRequest;
use App\Http\Controllers\Controller;
use App\Models\QueryBuilder\RoiCalculator;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        return view('admin.stats.index');
    }

    /**
     * @param Request $request
     * @return View
     */
    public function detailed(Request $request)
    {
        $newUsers = User::where('created_at', '>', Carbon::now()
            ->sub(new CarbonInterval(0, 0, 0, $request->get('days'))))
            ->orderBy('created_at', 'asc')
            ->get();

        switch ($request->get('type')) {
            case 'user':
                return view('admin.stats.detailed', compact(['newUsers', 'days']));
                break;
            case 'countries':
                $countries = [];
                if (!empty($newUsers)) {
                    foreach ($newUsers as $user) {
                        if (empty($countries[$user->getCountry()])) {
                            $countries[$user->getCountry()] = 0;
                        }
                        $countries[$user->getCountry()]+= $user->getTotalSpendings();
                    }
                }
                return view('admin.stats.countries', compact('countries', 'days'));
                break;
        }
        return view('admin.stats.index');
    }

    /**
     * Get average revenue per user
     *
     * @param Request $request
     *
     * @return View
     */
    public function average(Request $request)
    {
        $days = (int) $request->get('days');
        $stats = (new RoiCalculator($days))->get();

        return view('admin.stats.average', compact('stats'));
    }
}
