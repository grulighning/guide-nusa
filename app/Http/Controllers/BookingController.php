<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Pemandu;

class BookingController extends Controller
{
    public function create($destination_id, $guide_id)
    {
        $destination = Destinasi::findOrFail($destination_id);
        $guide       = Pemandu::findOrFail($guide_id);

        // Ambil tanggal yang sudah di-booking untuk pemandu ini
        $bookedDates = $guide->getBookedDates();

        // Hari dalam seminggu
        $hari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        return view('bookings.create', compact('destination', 'guide', 'bookedDates', 'hari'));
    }
}
