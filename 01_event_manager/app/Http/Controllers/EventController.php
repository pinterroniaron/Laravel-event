<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('event_date', 'asc')->get();  // lekéri az összes eseményt az adatbázisból
        //dd($events);  // kiírja az eseményeket a debug konzolra
        return view('events', ['events' => $events]);  // visszaadja az events.blade.php nézetet
    }
}
