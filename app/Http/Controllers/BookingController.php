<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSlotRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateSlotRequest;
use App\Models\Booking;
use App\Models\BookingSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()->with('slots')->latest()->get();
        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = DB::transaction(function () use ($request) {
            $booking = Auth::user()->bookings()->create();

            $slotsData = collect($request->validated()['slots'])->map(function ($slot) use ($booking) {
                return array_merge($slot, ['booking_id' => $booking->id]);
            });

            BookingSlot::insert($slotsData->toArray());

            return $booking;
        });

        return response()->json($booking->load('slots'), 201);
    }

    public function updateSlot(UpdateSlotRequest $request, Booking $booking, BookingSlot $slot)
    {
        $slot->update($request->validated());
        return response()->json($booking->load('slots'));
    }

    public function addSlot(AddSlotRequest $request, Booking $booking)
    {
        $booking->slots()->create($request->validated());
        return response()->json($booking->load('slots'), 201);
    }

    public function destroy(Booking $booking)
    {
        if (Auth::id() !== $booking->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $booking->delete();

        return response()->json(null, 204);
    }
}
