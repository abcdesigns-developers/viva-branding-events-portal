<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventTypeStoreRequest;
use App\Http\Requests\EventTypeUpdateRequest;
use App\Models\EventType;
use App\Models\Event;
use App\Models\GiftItem;
use Illuminate\Support\Facades\DB;

class EventTypeController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('name')->get();
        return view('Admin.EventTypes.index', compact('events'));
    }

    public function data(Request $request)
    {
        $query = EventType::query()->with(['event', 'gifts']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        return DataTables::eloquent($query)
            ->addColumn('event', function ($eventType) {
                return $eventType->event?->name;
            })
            ->addColumn('gifts', function ($eventType) {
                return $eventType->gifts->pluck('name')->implode(', ');
            })
            ->addColumn('status', function ($eventType) {
                $checked = $eventType->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only event-type-status-switch" data-routekey="' . $eventType->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($eventType) {
                $edit = '<a href="' . route('admin.event-types.edit', ['event_type' => $eventType->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.event-types.show', ['event_type' => $eventType->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="event-types" data-title="Event Type" data-route-key="' . $eventType->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 event-type-delete-btn" data-routekey="' . $eventType->route_key . '" data-name="' . e($eventType->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = EventType::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        $events = $this->events();
        $giftItems = $this->giftItems();
        return view('Admin.EventTypes.form', compact('events', 'giftItems'));
    }

    public function store(EventTypeStoreRequest $request)
    {
        $data = $request->validated();

        $eventType = DB::transaction(function () use ($data) {
            $eventType = EventType::create($data + ['status' => 'ACTIVE']);
            $eventType->gifts()->sync($data['gift_items'] ?? []);

            return $eventType;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Event Type Created Successfully',
            'event_type' => $eventType
        ], 201);
    }

    public function show(EventType $eventType)
    {
        $eventType->load('gifts');
        return view('Admin.EventTypes.show', compact('eventType'));
    }

    public function edit(EventType $eventType)
    {
        $events = $this->events($eventType);
        $eventType->load('gifts');
        $giftItems = $this->giftItems($eventType);
        return view('Admin.EventTypes.form', compact('eventType', 'events', 'giftItems'));
    }

    public function update(EventTypeUpdateRequest $request, EventType $eventType)
    {
        $data = $request->validated();

        DB::transaction(function () use ($eventType, $data) {
            $eventType->update($data);
            // No ticked gifts means the type gives none.
            $eventType->gifts()->sync($data['gift_items'] ?? []);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Event Type Updated Successfully',
            'event_type' => $eventType
        ], 201);
    }

    public function destroy(EventType $eventType)
    {
        $name = $eventType->name;
        $eventType->delete();

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $eventType = EventType::findByKey($request->route_key);
        abort_if(! $eventType, 404);

        $eventType->status = $request->status;
        $eventType->save();

        return response()->json([
            'status' => 'success',
            'message' => $eventType->name . ' has marked ' . $eventType->status . ' successfully',
            'event_type' => $eventType
        ], 201);
    }

    /** Active gift items for the Gifts checkboxes, plus any already linked when editing. */
    private function giftItems(?EventType $eventType = null)
    {
        $linked = $eventType ? $eventType->gifts->pluck('id')->all() : [];

        return GiftItem::where('status', 'ACTIVE')
            ->when($linked, fn ($query) => $query->orWhereIn('id', $linked))
            ->orderBy('name')
            ->get();
    }

    /** Active events for the dropdown, plus the current one when editing. */
    private function events(?EventType $eventType = null)
    {
        return Event::where('status', 'ACTIVE')
            ->when($eventType, fn ($query) => $query->orWhere('id', $eventType->event_id))
            ->orderBy('name')
            ->get();
    }
}
