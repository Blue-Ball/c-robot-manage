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
        $robot_serial = $request->robot_serial;
        if(empty($robot_serial) || $robot_serial == ""){
            return $this->error('-2',trans('main.enter_robot_serial'));
        }
        
        if(empty($request->unit)){
            return $this->error('-2',trans('main.enter_unit'));
        }
        if(empty($request->floor)){
            return $this->error('-2',trans('main.enter_floor'));
        }
        if(empty($request->corridor_number)){
            return $this->error('-2',trans('main.enter_corridor_number'));
        }
        if(empty($request->spots_count)){
            return $this->error('-2',trans('main.enter_spots_count'));
        }
        if(empty($request->duration)){
            return $this->error('-2',trans('main.enter_duration'));
        } 

        if(empty($request->date)){
            return $this->error('-2',trans('main.enter_date'));
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
            'date'              => $request->date,
            'created_at'        => time()            
        ];    

        $result = CorridorDisinfection::create($newData);
        
        return $this->response($result);       
    }

    public function change_status_corridor_disinfection(Request $request){
        if(empty($request->id)){
            return $this->error('-2',trans('main.enter_table_id'));
        }

        $is_completed = 0;
        if(!empty($request->is_completed)){
            $is_completed = 1;
        }

        DB::table('corridor_disinfection_table')
            ->where('corridor_disinfection_table.id', $request->id)
            ->update(['is_completed' => $request->is_completed]);
        return $this->response(['message'=>trans('main.updated_success')]);
    }

    public function insert_room_disinfection(Request $request){
        $robot_serial = $request->robot_serial;
        if(empty($robot_serial) || $robot_serial == ""){
            return $this->error('-2',trans('main.enter_robot_serial'));
        }
        
        if(empty($request->unit)){
            return $this->error('-2',trans('main.enter_unit'));
        }
        if(empty($request->floor)){
            return $this->error('-2',trans('main.enter_floor'));
        }
        if(empty($request->room_number)){
            return $this->error('-2',trans('main.enter_room_number'));
        }
        if(empty($request->spots_count)){
            return $this->error('-2',trans('main.enter_spots_count'));
        }
        if(empty($request->duration)){
            return $this->error('-2',trans('main.enter_duration'));
        } 
        
        if(empty($request->date)){
            return $this->error('-2',trans('main.enter_date'));
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
            'date'              => $request->date,
            'created_at'        => time()            
        ]; 
        $result = RoomDisinfection::create($newData);            
        return $this->response($result);               
    }

    public function change_status_room_disinfection(Request $request){
        if(empty($request->id)){
            return $this->error('-2',trans('main.enter_table_id'));
        }

        $is_completed = 0;
        if(!empty($request->is_completed)){
            $is_completed = 1;
        }

        DB::table('room_disinfection_table')
            ->where('room_disinfection_table.id', $request->id)
            ->update(['is_completed' => $request->is_completed]);
        return $this->response(['message'=>trans('main.updated_success')]);        
    }

    public function insert_hospital_rooms(Request $request){ 
        if (auth()->check()) {
            $user = auth()->user();
            $user_id = $user->id;

            if(empty($request->unit)){
                return $this->error('-2',trans('main.enter_unit'));
            }
            if(empty($request->floor)){
                return $this->error('-2',trans('main.enter_floor'));
            }
            if(empty($request->room_number)){
                return $this->error('-2',trans('main.enter_room_number'));
            }
            if(empty($request->room_size)){
                return $this->error('-2',trans('main.enter_room_size'));
            } 

            $result = DB::table('hospital_rooms_table')->insert([
                'unit'              => $request->unit,
                'floor'             => $request->floor,
                'room_number'       => $request->room_number,
                'room_size'         => $request->room_size,
                'user_id'           => $user_id,
                'creation_date'     => date('Y-m-d H:i:s'),
                'last_edit_date'    => date('Y-m-d H:i:s')
            ]);            
            return $this->response($result); 
        }else{
            return $this->error(-1,trans('main.please_login'));
        }      
    }

    public function getRobotList(Request $request){
        $robot_infos = DB::table('robots_info_table')
            ->select("robots_info_table.*")->get();
        return $this->response($robot_infos);
    }

    public function getRoomDisinfectionInfo(Request $request){
        $query = DB::table('room_disinfection_table')
            ->select("room_disinfection_table.*");                
        if(!empty($request->unit)){
            $query->where("unit", "=", $request->unit);
        }
        if(!empty($request->floor)){
            $query->where("floor", "=", $request->floor);
        }
        if(!empty($request->room)){
            $query->where("room", "=", $request->room);
        }
        if(!empty($request->spots_count)){
            $query->where("spots_count", "=", $request->spots_count);
        }
        if(!empty($request->date)){
            $query->whereRaw('DATEDIFF("'.$request->date.'", date) = ?', 0);
        }
        if(!empty($request->duration)){
            $query->where("duration", "=", $request->duration);
        }
        if(!empty($request->is_completed)){
            if($request->is_completed == 'completed'){
                $query->where("is_completed", "=", "1");
            }else{
                $query->where("is_completed", "!=", "1");
            }                
        }
        if(!empty($request->robot_serial)){
            $query->where("robot_serial", "=", $request->robot_serial);
        }
        $result = $query->get();
        return $this->response($result);
    }

    public function getCorridorDisinfectionInfo(Request $request){
        $query = DB::table('corridor_disinfection_table')
            ->select("corridor_disinfection_table.*");                
        if(!empty($request->unit)){
            $query->where("unit", "=", $request->unit);
        }
        if(!empty($request->floor)){
            $query->where("floor", "=", $request->floor);
        }
        if(!empty($request->corridor_number)){
            $query->where("corridor_number", "=", $request->corridor_number);
        }
        if(!empty($request->spots_count)){
            $query->where("spots_count", "=", $request->spots_count);
        }
        if(!empty($request->date)){
            $query->whereRaw('DATEDIFF("'.$request->date.'", date) = ?', 0);
        }
        if(!empty($request->duration)){
            $query->where("duration", "=", $request->duration);
        }
        if(!empty($request->is_completed)){
            if($request->is_completed == 'completed'){
                $query->where("is_completed", "=", "1");
            }else{
                $query->where("is_completed", "!=", "1");
            }                
        }
        if(!empty($request->robot_serial)){
            $query->where("robot_serial", "=", $request->robot_serial);
        }
        $result = $query->get();
        return $this->response($result);        
    }

    public function robotRegister(Request $request){ 
        if (auth()->check()) {
            
            $user = auth()->user();
            $user_id = $user->id;
        
            if(empty($request->serial)){
                return $this->error('-2',trans('main.enter_robot_serial'));
            }

            if(empty($request->name)){
                return $this->error('-2',trans('main.enter_robot_name'));
            }

            if(empty($request->password)) {
                return $this->error('-2',trans('main.pass_confirmation_same'));
            }

            if(empty($request->number)){
                return $this->error('-2',trans('main.enter_robot_number'));
            }

            $duplicateSerial = RobotInfo::where('robot_serial',$request->serial)->first();
            if($duplicateSerial) {
                return $this->error(-2,trans('main.robot_exists'));
            }

            $newRobot = [
                'robot_serial'      => $request->serial,
                'robot_name'        => $request->name,
                'robot_password'    => $request->password,
                'robot_number'      => $request->number,
                'user_id'           => $user_id,
                'created_at'        => time() 
            ];
            RobotInfo::create($newRobot);
            
            return $this->response(['message'=>trans('main.thanks_reg')]);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }
}
