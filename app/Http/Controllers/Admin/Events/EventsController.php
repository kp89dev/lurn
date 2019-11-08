<?php
namespace App\Http\Controllers\Admin\Events;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\Event\StoreEventRequest;
use App\Models\Event;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class EventsController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:events,read')
            ->only('index');
        $this->middleware('admin.role.auth:events,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }


    public function index()
    {
        $events = Event::orderBy('start_date', 'DESC')->simplePaginate(20);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $event = new Event();
        $action = route('events.store');
        $method = 'POST';

        return view('admin.events.create-edit', compact('event', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        $allDay = (request('all_day')) ? 1 : 0;
        
        $times = $this->getStartAndEndDates($request);
        $start = $times['start'];
        $end = $times['end'];

        if ($start->gte($end)) {
            return Redirect::back()
                    ->withErrors([
                        'msg' => 'Start date and time must be before the end date and time',
                    ])
                    ->withInput($request->except(['start_date', 'end_date', 'start_time', 'end_time']));
        }

        $request->merge(['start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'all_day'    => $allDay
        ]);
        Event::create($request->all());

        return redirect()
                ->route('events.index')
                ->with('alert-success', 'Event succesfully added');
    }

    /**
     * @param int $eventId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($eventId)
    {
        $event = Event::find($eventId);

        $action = route('events.update', ['event' => $event->id]);
        $method = method_field('PUT');

        return view('admin.events.create-edit', compact('event', 'action', 'method'));
    }

    /**
     * @param StoreEventRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreEventRequest $request)
    {
        
        $times = $this->getStartAndEndDates($request);
        $start = $times['start'];
        $end = $times['end'];

        $event = Event::find($request->event);
        $request->merge([
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
        ]);
        if ($start->gte($end)) {
            return Redirect::back()
                    ->withErrors([
                        'msg' => 'Start date and time must be before the end date and time',
                    ])
                    ->withInput($request->except(['start_date', 'end_date', 'start_time', 'end_time']));
        }

        $event->fill($request->all());
        $event->save();

        return redirect()->route('events.index')->with('alert-success', 'Event succesfully modified');
    }
    /* Remove an event from the database
     * 
     * @param int $id
     * @param $request
     */

    public function destroy($id, Request $request)
    {
        Event::find($id)->delete();
        request()->session()->flash('alert-success', 'The Event was successfully deleted!');
        return redirect()->back();
    }
    

    protected function getStartAndEndDates($request) {
        if (request('all_day')) {
            //check that start/end times make sense
            $start_time = '12:00:00 AM';
            $end_time = '12:59:59 PM';
        } else {
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
        }
        
        $start = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('start_date') . ' ' . $start_time);
        $end = Carbon::createFromFormat('m/d/Y g:i:s A', $request->get('end_date') . ' ' . $end_time);
        
        return compact('start','end');
    }
}
