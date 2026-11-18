<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return view('Admin.Events.index');
    }

    public function data(Request $request)
    {
        $query = Event::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($event) {
                $checked = $event->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only event-status-switch" data-routekey="' . $event->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($event) {
                $edit = '<a href="' . route('admin.events.edit', ['event' => $event->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.events.show', ['event' => $event->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="events" data-title="Event" data-route-key="' . $event->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                if ($event->isStatic()) {
                    return $edit . ' ' . $show;
                }
                $delete = '<a href="#" class="badge bg-danger fs-1 event-delete-btn" data-routekey="' . $event->route_key . '" data-name="' . e($event->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = Event::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.Events.form');
    }

    public function store(EventStoreRequest $request)
    {
        $data = $request->validated();

        $event = Event::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Event Created Successfully',
            'event' => $event
        ], 201);
    }

    public function show(Event $event)
    {
        return view('Admin.Events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('Admin.Events.form', compact('event'));
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $data = $request->validated();

        $event->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Event Updated Successfully',
            'event' => $event
        ], 201);
    }

    public function destroy(Event $event)
    {
        if ($event->isStatic()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This is a fixed portal section and cannot be deleted.',
            ], 422);
        }

        if ($event->eventTypes()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This event has event types. Delete them first.',
            ], 422);
        }

        $name = $event->name;
        try {
            $event->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => 'error', 'message' => 'This item is used by existing requests or records, so it cannot be deleted. Mark it Inactive instead.'], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $event = Event::findByKey($request->route_key);
        abort_if(! $event, 404);

        $event->status = $request->status;
        $event->save();

        return response()->json([
            'status' => 'success',
            'message' => $event->name . ' has marked ' . $event->status . ' successfully',
            'event' => $event
        ], 201);
    }
}
