<?php

namespace App\Http\Controllers\Airlines;

use App\Http\Controllers\Controller;
use App\Models\Airlines;
use App\Models\Flights;
use Exception;
use Illuminate\Http\Request;

class AirlinesController extends Controller
{
    //
    function getAirlines(){
        try{
            extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn', 'sort']));
            $json = json_decode($query);
            
            $list = Airlines::select('*');
            
            if(isset($json->name)){
                $list->where('name', 'LIKE', "%{$json->name}%");
            }

            if(isset($json->code)){
                $list->where('code', 'LIKE', "%{$json->code}%");
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

            $airlines = Airlines::find($id);
            if(is_null($airlines)){
                $airlines = new Airlines();
            }

            $airlines->name = $request->name;
            $airlines->code = $request->code;
            $airlines->touch();
            $airlines->save();

            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }

    function delete(Request $request){
        try {

            $flights = Flights::where('airline_id', $request->id)->first();

            if(!is_null($flights)){
                throw new Exception('the airline is registered on a flight', 111);
            }

            $airline = Airlines::find($request->id)->delete();

            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => ($e->getCode() == 111) ? $e->getMessage() : 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }
}
