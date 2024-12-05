<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $countries = \DB::table('countries')->orderBy('name', 'ASC')->get();
        $data['countries'] = $countries;
        return view('users.create', $data);
    }

    public function fetchStates($country_id = null)
    {
        $states = \DB::table('states')->where('country_id', $country_id)->get();

        return response()->json([
            'status' => 1,
            'states' => $states
        ]);
    }

    public function fetchCities($state_id = null)
    {
        $cities = \DB::table('cities')->where('state_id', $state_id)->get();

        return response()->json([
            'status' => 1,
            'cities' => $cities
        ]);
    }

    public function save(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->passes()) {

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => "123456",
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
            ]);

            $request->session()->flash('success', 'User added successfully.');

            return response()->json([
                'status' =>  1
            ]);
        } else {
            // return error 

            return response()->json([
                'status' =>  0,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function list()
    {
        $users = DB::table('users')->get();
        $data['users'] = $users;
        return view('users.list', $data);
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->first();

        $countries = \DB::table('countries')->orderBy('name', 'ASC')->get();
        $data['countries'] = $countries;

        $states = \DB::table('states')->where('country_id', $user->country)->orderBy('name', 'ASC')->get();
        $data['states'] = $states;

        $cities = \DB::table('cities')->where('state_id', $user->state)->orderBy('name', 'ASC')->get();
        $data['cities'] = $cities;

        if ($user == null) {
            return redirect(url('/list'));
        }

        $data['user'] = $user;

        return view('users.edit', $data);
    }

    public function update($id, Request $request)
    {

        $user = User::find($id);

        if ($user == null) {

            $request->session()->flash('error', 'Enither user deleted or not found.');

            return response()->json([
                'status' =>  '400'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->country = $request->country;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->save();

            $request->session()->flash('success', 'User updated successfully.');

            return response()->json([
                'status' =>  1
            ]);
        } else {
            // return error 

            return response()->json([
                'status' =>  0,
                'errors' => $validator->errors()
            ]);
        }
    }
}
