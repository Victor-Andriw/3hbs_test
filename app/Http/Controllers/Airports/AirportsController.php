<?php

namespace App\Http\Controllers\Airports;

use App\Http\Controllers\Controller;
use App\Models\Airports;
use App\Models\Flights;
use Exception;
use Illuminate\Http\Request;

class AirportsController extends Controller
{
    //
    function getAirports(){
        try{
            extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn', 'sort']));
            $json = json_decode($query);
            
            $list = Airports::select('*');
            
            if(isset($json->name)){
                $list->where('name', 'LIKE', "%{$json->name}%");
            }

            if(isset($json->code)){
                $list->where('code', 'LIKE', "%{$json->code}%");
            }

            if(isset($json->city)){
                $list->where('city', 'LIKE', "%{$json->city}%");
            }
                
            $result['count'] = $list->count();
            $result["data"]  = $list->take($limit)->skip($limit * ($page - 1) )->get();
        }catch(Exception $e){
            $result['count'] =0;
            $result['data']=array();
            $result['error']= "Sorry!";
            $result['msg'] = $e->getMessage(); 
        }
        return $result; 
    }

    function store(Request $request){
        try {
            
            $id = $request->id ?? null;

            $airports = Airports::find($id);
            if(is_null($airports)){
                $airports = new Airports();
            }

            $airports->name = $request->name;
            $airports->code = $request->code;
            $airports->city = $request->city;
            $airports->touch();
            $airports->save();

            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }

    function delete(Request $request){
        try {

            $flights = Flights::where('departure_id', $request->id)->orWhere('destination_id', $request->id)->first();

            if(!is_null($flights)){
                throw new Exception('the airport is registered on a flight', 111);
            }

            $airports = Airports::find($request->id)->delete();

            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => ($e->getCode() == 111) ? $e->getMessage() : 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }
}
