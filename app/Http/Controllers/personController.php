<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\data;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class personController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $person = data::all();
        return response()->json([
            'state' => true,
            'data' => $person
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentiales = $request->only('name','last_name','direction','country','postal_code','studies','ocupation');
       
        $validate = Validator::make($credentiales, [
            'name' => 'required|min:3|max:30',
            'last_name' => 'required|min:3|max:30',
            'direction' => 'required|min:5|max:70',
            'postal_code' => 'min:2|max:6',
             'studies' => 'min:5|max:25'
        ], );
        if(!$validate->fails()){

            $person = new data();
            $person->name =  $request->name;
            $person->last_name =  $request->last_name;
            $person->direction =  $request->direction;
            $person->country =  $request->country;
            $person->postal_code =  $request->postal_code;
            $person->studies =  $request->studies;
            $person->ocupation =  $request->ocupation;
          
            if($person->save()){
                 return response()->json([
                    'state' => true,
                    'data' => 'Record added successfully!'
                 ], 200);
            }else{
                return response()->json([
                    'state' => false,
                     'data' => 'The record has not been added!'
                  ], 500);
            } 
        }else{
            return response()->json([
                'state' => false,
                'data' => $validate->errors()
            ],400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        $onePerson = DB::table('datas')->where('name',$name)->first();
        if($onePerson){
             return response()->json([
                'state' => true,
                'data' => $onePerson
             ],200);
        }else{
            return response()->json([
                'state' => false,
                'data' => 'The person with the name '+ $name +' does not exist'
             ],400);
        }
    }

    public function getOneById($id)
    {
        $person = DB::table('datas')->where('id',$id)->first();
        if($person){
             return response()->json([
                'state' => true,
                'data' => $person
             ],200);
        }else{
            return response()->json([
                'state' => false,
                'data' => 'The record with id '+$id+' does not exist'
             ],400);
        }
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
        $credential = $request->only('name','last_name','direction','country','postal_code','studies','ocupation');
        $person = data::find($id);

        // SI NO EXISTE LA PERSONA
        if(!$person){
            return response()->json([
                'state' => false,
                'data' => 'This person does not exist in the registry'
            ], 400);
        }
        $validate = Validator::make($credential ,[
            'name' => 'min:3|max:30',
            'last_name' => 'min:3|max:30',
            'direction' => 'min:5|max:70',
            'postal_code' => 'min:2|max:6',
            'studies' => 'min:5|max:25'
        ]);
        if(!$validate->fails()){

            $updatePerson = $person->fill($request->all())->save();
            if($updatePerson){
                 return response()->json([
                     'state' => true,
                     'data' => 'Updated record'
                 ], 200);
            }else{
                 return response()->json([
                     'state' => false,
                     'data' => 'The record has not been updated'
                 ], 500);
            }
        }else{
            return response()->json([
                'status' => false,
                'error' => $validate->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = data::find($id);
        if($person){
            if($person->delete()){
                return response()->json([
                  'state' => true,
                  'data' => 'Record deleted'
               ],200);
            }else{
                 return response()->json([
                     'state' => false,
                     'data' => 'The record has not been removed'
                 ],500);
            }
        }else{
             return response()->json([
                 'state' => false,
                 'data' => 'The user with id '+$id+' does not exist'
             ],400);
        }
    }
}
