<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Time;
use App\User;
use App\Booking;
use App\Mail\AppointMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $doctor_id = $doctorId;

        return view('appointment',compact('times','user','date', 'doctor_id'));
    }

    public function findDoctorsBasedOnDate($date) {
        $doctors = Appointment::where('date', $date)->get();
        return $doctors;
    }

    public function store(Request $request)
    {
        $request->validate(['time' => 'required']);
        $check = $this->checkBookingTimeInterval();
        if($check) {
            return redirect()->back()->with('errmessage','You already made an appointment. please wait to make next appointment');
        }
        Booking::create([
            'user_id' => auth()->user()->id,
            'doctor_id'=> $request->doctorId,
            'time'=> $request->time,
            'date'=> $request->date,
        ]);

        Time::where('appointment_id', $request->appointmentId)->where('time',$request->time)->update(['status'=> 1]);

        $doctor = User::where('id',$request->doctorId)->first();

        $mailData = [
            'name' => auth()->user()->name,
            'time' => $request->time,
            'date' => $request->date,
            'doctorName' => $doctor->name
        ];

        try {
            Mail::to(auth()->user()->email)->send(new AppointMail($mailData));
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        return redirect()->back()->with('message' ,'Your appointment was booked');
    }

    public function checkBookingTimeInterval()
    {
        return false;
        // return Booking::orderby('id','desc')->where('user_id',auth()->user()->id)->whereDate('created_at', date('Y-m-d'))->exists(); 
    }
}
