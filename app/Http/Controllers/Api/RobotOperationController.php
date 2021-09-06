<?php

namespace App\Http\Controllers\Api;

use http\Url;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RobotInfo;
use App\Models\CorridorDisinfection;
use App\Models\RoomDisinfection;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class RobotOperationController extends Controller
{
    ## Private Function
    private function response($data){
        return ['status'=>'1','data'=>$data];
    }
    
    private function error($code, $description){
        return ['status'=>-1,'code'=>$code,'error'=>$description];
    }

    public function insert_corridor_disinfection(Request $request){
        if (auth()->check()) {

            $user = auth()->user();
            $robot_serial = $user->robot_serial;
            if(empty($robot_serial) || $robot_serial == ""){
                return $this->error('-1',trans('main.not_robot'));
            }
            
            if(empty($request->unit)){
                return $this->error('-1',trans('main.enter_unit'));
            }
            if(empty($request->floor)){
                return $this->error('-1',trans('main.enter_floor'));
            }
            if(empty($request->corridor_number)){
                return $this->error('-1',trans('main.enter_corridor_number'));
            }
            if(empty($request->spots_count)){
                return $this->error('-1',trans('main.enter_spots_count'));
            }
            if(empty($request->duration)){
                return $this->error('-1',trans('main.enter_duration'));
            }    
            
            $is_completed = 0;
            if(!empty($request->is_completed)){
                $is_completed = $request->is_completed;
            }

            $newData = [
                'unit'              => $request->unit,            
                'floor'             => $request->floor,
                'corridor_number'   => $request->corridor_number,
                'duration'          => $request->duration,
                'spots_count'       => $request->spots_count,
                'is_completed'      => $is_completed,
                'robot_serial'      => $robot_serial,
                'date'              => date('Y-m-d H:i:s'),
                'created_at'        => time()            
            ];    

            $result = CorridorDisinfection::create($newData);
            
            return $this->response($result);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }        
    }

    public function change_status_corridor_disinfection(Request $request){
        if (auth()->check()) {
            $user = auth()->user();
            $robot_serial = $user->robot_serial;
            if(empty($robot_serial) || $robot_serial == ""){
                return $this->error('-1',trans('main.not_robot'));
            }

            DB::table('corridor_disinfection_table')
                ->where('corridor_disinfection_table.id', $request->id)
                ->update(['is_completed' => $request->is_completed]);
            return $this->response(['message'=>trans('main.updated_success')]);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }

    public function insert_room_disinfection(Request $request){
        if (auth()->check()) {

            $user = auth()->user();
            $robot_serial = $user->robot_serial;
            if(empty($robot_serial) || $robot_serial == ""){
                return $this->error('-1',trans('main.not_robot'));
            }
            
            if(empty($request->unit)){
                return $this->error('-1',trans('main.enter_unit'));
            }
            if(empty($request->floor)){
                return $this->error('-1',trans('main.enter_floor'));
            }
            if(empty($request->room_number)){
                return $this->error('-1',trans('main.enter_room_number'));
            }
            if(empty($request->spots_count)){
                return $this->error('-1',trans('main.enter_spots_count'));
            }
            if(empty($request->duration)){
                return $this->error('-1',trans('main.enter_duration'));
            }    
            
            $is_completed = 0;
            if(!empty($request->is_completed)){
                $is_completed = $request->is_completed;
            }

            $newData = [
                'unit'              => $request->unit,            
                'floor'             => $request->floor,
                'room'              => $request->room_number,
                'duration'          => $request->duration,
                'spots_count'       => $request->spots_count,
                'is_completed'      => $is_completed,
                'robot_serial'      => $robot_serial,
                'date'              => date('Y-m-d H:i:s'),
                'created_at'        => time()            
            ];    

            $result = RoomDisinfection::create($newData);
            
            return $this->response($result);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }        
    }

    public function change_status_room_disinfection(Request $request){
        if (auth()->check()) {
            $user = auth()->user();
            $robot_serial = $user->robot_serial;
            if(empty($robot_serial) || $robot_serial == ""){
                return $this->error('-1',trans('main.not_robot'));
            }

            DB::table('room_disinfection_table')
                ->where('room_disinfection_table.id', $request->id)
                ->update(['is_completed' => $request->is_completed]);
            return $this->response(['message'=>trans('main.updated_success')]);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }

    public function insert_hospital_rooms(Request $request){
        if (auth()->check()) {
            
            if(empty($request->unit)){
                return $this->error('-1',trans('main.enter_unit'));
            }
            if(empty($request->floor)){
                return $this->error('-1',trans('main.enter_floor'));
            }
            if(empty($request->room_number)){
                return $this->error('-1',trans('main.enter_room_number'));
            }
            if(empty($request->room_size)){
                return $this->error('-1',trans('main.enter_room_size'));
            } 

            $result = DB::table('hospital_rooms_table')->insert([
                'unit'              => $request->unit,
                'floor'             => $request->floor,
                'room_number'       => $request->room_number,
                'room_size'         => $request->room_size,
                'creation_date'     => date('Y-m-d H:i:s'),
                'last_edit_date'    => date('Y-m-d H:i:s')
            ]);            
            return $this->response($result);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }        
    }
}
