<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Beasiswa;

class BeasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $beasiswas = Beasiswa::all();
        foreach ($beasiswas as $beasiswa) {
            $beasiswa->view_beasiswa = [
                'href' => 'api/v1/beasiswa/' . $beasiswa ->id,
                'method' => 'GET'
            ];
        }
        

        $response = [
            'msg' => 'List of all beasiswa',
            'beasiswa' => $beasiswas
        ];
        
        return response()->json($response, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required',
        ]);


        $title = $request-> input('title');
        $description = $request-> input('description');
        $time = $request-> input('time');
        $user_id = $request-> input('user_id');
        
       $beasiswa = new Beasiswa([
           'time' => $time,
           'title' => $title,
           'description' => $description
       ]);

       if ($beasiswa->save()) {
           $beasiswa->users()->attach($user_id);
           $beasiswa->view_beasiswa = [
               'href' => 'api/v1/beasiswa/' . $beasiswa->id,
               'method' => 'GET'
           ];

           $message = [
               'msg' => 'Beasiswa Created',
               'beasiswa' => $beasiswa
           ];
           return response()->json($message, 201);
       }

       $message = [
           'msg' => 'Error During Creating'
       ];
       return response()->json($message, 404);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $beasiswa = Beasiswa::with('users')->where('id', $id)->firstOrFail();

        $beasiswa->view_beasiswas = [
            'href' => 'api/v1/beasiswa',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Beasiswa Information',
            'beasiswa' => $beasiswa
        ];

        return response()->json($response, 200);
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required',
        ]);

        $title = $request-> input('title');
        $description = $request-> input('description');
        $time = $request-> input('time');
        $user_id = $request-> input('user_id');

        $beasiswa = Beasiswa::with('users')->findOrFail($id);

        if (!$beasiswa->users()->where('user_id', $user_id)->first()) {
            return response()->json(['msg' => 'user not registered for beasiswa update not succesful'], 401);
        }

        $beasiswa->time = $time;
        $beasiswa->title = $title;
        $beasiswa->description = $description;


        if (!$beasiswa->update()) {
            return response()->json(['msg' => 'Error During Update'], 404);
        }

        $beasiswa->view_beasiswa = [
            'href' => 'api/v1/beasiswa/' . $beasiswa->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Beasiswa Updated',
            'beasiswa' => $beasiswa
        ];

        return response()->json($response, 200);


        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $beasiswa = Beasiswa::findOrFail($id);
        $users = $beasiswa->users;
        $beasiswa->Users()->detach();

        if (!$beasiswa->delete()) {
            foreach($users as $user) {
                $beasiswa->users()->attach($user);
            }

            return response()->json(['msg' => 'Deletion Failed'], 404);
        }

        $response = [
            'msg' => 'Beasiswa Deleted',
            'create' => [
                'href' => 'api/v1/beasiswa',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
            ];
        return response()->json($response, 200);
    }
}
