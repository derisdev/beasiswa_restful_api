<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Beasiswa;

class RegisterController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'beasiswa_id' => 'required',
            'user_id' => 'required'
        ]);

        $beasiswa_id = $request->input('beasiswa_id');
        $user_id = $request->input('user_id');

        $beasiswa = Beasiswa::findOrFail($beasiswa_id);

        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User Already registered for Beasiswa',
            'user' => $user,
            'beasiswa'=> $beasiswa,
            'unregister' => [
                'href' => 'api/v1/registration/' . $beasiswa->id,
                'method' => 'DELETE',
            ]
        ];

        if ($beasiswa->users()->where('users.id', $user->id)->first()) {
            return response()->json($message, 404);
        }

        $user->beasiswas()->attach($beasiswa);

        $response = [
            'msg' => 'User Registered for Beasiswa',
            'beasiswa' => $beasiswa,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/beasiswa/registration/' . $beasiswa->id,
                'method' => 'DELETE'
            ]
        ];

        return response()->json($response, 201);

        
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
        $beasiswa->users()->detach();

        $response = [
            'msg' => 'User Unregistered',
            'beasiswa' => $beasiswa,
            'register' => [
                'href' => 'api/v1/beasiswa/registration',
                'method' => 'POST',
                'params' => 'user_id, beasiswa_id'
            ]
        ];
        
        return response()->json($response, 200);
    }
}
