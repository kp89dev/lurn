<?php
namespace App\Http\Controllers\Admin\PushNotifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PushNotification\StorePushNotificationRequest;
use Illuminate\Support\Facades\Redirect;
use App\Models\PushNotifications;
use Carbon\Carbon;

class PushNotificationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:push-notifications,read')
            ->only('index');
        $this->middleware('admin.role.auth:push-notifications,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $pushNotifications = PushNotifications::orderBy('start_date', 'DESC')->simplePaginate(20);

        return view('admin.push-notifications.index', compact('pushNotifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $pushNotification = new PushNotifications();
        $timezones = $this->getTimezones();
        $action = route('push-notifications.store');
        $method = null;

        return view('admin.push-notifications.create-edit', compact('pushNotification', 'timezones', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePushNotificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePushNotificationRequest $request)
    {

        $start = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('start_date') . ' ' . $request->get('start_time'));
        $startUTC = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('start_date') . ' ' . $request->get('start_time'), $request->get('timezone'))->setTimezone(0);
        $end = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('end_date') . ' ' . $request->get('end_time'));
        $endUTC = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('end_date') . ' ' . $request->get('end_time'), $request->get('timezone'))->setTimezone(0);
        $allVisitors = ($request->get('all_visitors') ?: 0);

        if ($start->gte($end)) {
            return Redirect::back()
                    ->withErrors([
                        'msg' => 'Start date and time must be before the end date and time',
                    ])
                    ->withInput($request->except(['start_date', 'end_date', 'start_time', 'end_time']));
        }

        $request->merge([
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'start_utc' => $startUTC,
            'end_utc' => $endUTC,
            'all_visitors' => $allVisitors
        ]);

        $pushNotification = new PushNotifications;
        $pushNotification->fill($request->all());
        $pushNotification->save();

        return redirect()
                ->route('push-notifications.index')
                ->with('alert-success', 'Push Notifications succesfully added');
    }

    /**
     * @param int $pushNotificationId
     *
     */
    public function edit($pushNotificationId)
    {
        $pushNotification = PushNotifications::find($pushNotificationId);
        $timezones = $this->getTimezones();
        $action = route('push-notifications.update', ['pushNotification' => $pushNotification->id]);
        $method = method_field('PUT');

        return view('admin.push-notifications.create-edit', compact('pushNotification', 'timezones', 'action', 'method'));
    }

    /**
     * @param StorePushNotificationRequest $request
     *
     */
    public function update(StorePushNotificationRequest $request, $pushNotificationId)
    {
        $pushNotification = PushNotifications::find($pushNotificationId);
        $start = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('start_date') . ' ' . $request->get('start_time'), $request->get('timezone'));
        $startUTC = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('start_date') . ' ' . $request->get('start_time'), $request->get('timezone'))->setTimezone(0);
        $end = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('end_date') . ' ' . $request->get('end_time'), $request->get('timezone'));
        $endUTC = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('end_date') . ' ' . $request->get('end_time'), $request->get('timezone'))->setTimezone(0);
        $allVisitors = ($request->get('all_visitors') ?: 0);

        $request->merge([
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'start_utc' => $startUTC,
            'end_utc' => $endUTC,
            'all_visitors' => $allVisitors
        ]);
        $pushNotification->fill($request->all());
        $pushNotification->save();

        return redirect()->route('push-notifications.index')->with('alert-success', 'Push Notifications succesfully modified');
    }

    public function getTimezones()
    {
        $timezoneTable = array(
            "-8" => "Pacific Time",
            "-7" => "Mountain Time",
            "-6" => "Central Time",
            "-5" => "Eastern Time",
            "-4" => "Atlantic Time",
            "0" => "Western Europe Time",
            "1" => "Brussels, Copenhagen, Madrid, Paris",
            "2" => "Kaliningrad, South Africa",
            "3" => "Baghdad, Riyadh, Moscow, St. Petersburg",
            "4" => "Abu Dhabi, Muscat, Baku, Tbilisi",
            "6" => "Almaty, Dhaka, Colombo",
            "7" => "Bangkok, Hanoi, Jakarta",
            "8" => "Beijing, Perth, Singapore, Hong Kong",
            "11" => "Magadan, Solomon Islands, New Caledonia",
        );

        return $timezoneTable;
    }
    
    /* Remove an event from the database
     * 
     * @param int $id
     */
    public function destroy($id)
    {
        PushNotifications::find($id)->delete();
        request()->session()->flash('alert-success', 'The Push Notification was successfully deleted!');
        return redirect()->back();
    }
}
