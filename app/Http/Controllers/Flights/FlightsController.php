<?php

namespace App\Http\Controllers\Flights;

use App\Http\Controllers\Controller;
use App\Models\Airlines;
use App\Models\Airports;
use App\Models\Flights;
use Exception;
use Illuminate\Http\Request;

class FlightsController extends Controller
{
    //
    
    function getFlights(){
        try{
            extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn', 'sort']));
            $json = json_decode($query);
            
            $list = Flights::with([ 'departure', 'destination', 'airline' ]);
            
            if(isset($json->type)){
                $list->where('type', 'LIKE', "%{$json->type}%");
            }

            if(isset($json->code)){
                $list->where('code', 'LIKE', "%{$json->code}%");
            }

            if(isset($json->departure)){
                $list->whereHas('departure', function($model) use($json){
                    $model->where('name', 'LIKE', "%{$json->departure}%");
                });
            }
            
            if(isset($json->destination)){
                $list->whereHas('destination', function($model) use($json){
                    $model->where('name', 'LIKE', "%{$json->destination}%");
                });
            }
            
            if(isset($json->airline)){
                $list->whereHas('airline', function($model) use($json){
                    $model->where('name', 'LIKE', "%{$json->airline}%");
                });
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

    function getCatalogs(){
        $airlines = Airlines::get();
        $airports = Airports::get();

        return response()->json([ 'airlines' => $airlines, 'airports' => $airports ]);
    }

    function store(Request $request){
        try {
            
            $form = json_decode($request->form);
        
            $id = $form->id ?? null;

            $flights = Flights::find($id);
            if(is_null($flights)){
                $flights = new Flights();
            }

            $flights->code = $form->code;
            $flights->type = $form->type;

            $flights->departure_id = $form->departure->id;
            $flights->destination_id = $form->destination->id;
            $flights->airline_id = $form->airline->id;
            $flights->touch();
            $flights->save();

            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }

    function delete(Request $request){
        try { 
            $airline = Flights::find($request->id)->delete();
            return response()->json([ 'status' => true ]);
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => 'Sorry!', 'error' => $e->getMessage() ]);
        }
    }
}
