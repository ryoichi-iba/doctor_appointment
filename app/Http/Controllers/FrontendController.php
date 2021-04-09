<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Time;
use App\User;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index() {
        $doctors = Appointment::where('date', date('Y-m-d'))->get();
        if((request('date'))) {
            $doctors = $this->findDoctorsBasedOnDate(request('date'));
            return view('welcome', compact('doctors'));
        }

        return view('welcome', compact('doctors'));
    }

    public function show($doctorId, $date) {

        $appointment = Appointment::where('user_id',$doctorId)->where('date',$date)->first();
        $times = Time::where('appointment_id', $appointment->id)->get();
        $user = User::find($doctorId)->first();
        return view('appointment',compact('times','user'));
    }

    public function findDoctorsBasedOnDate($date) {
        $doctors = Appointment::where('date', $date)->get();
        return $doctors;
    }
}
