<?php

namespace App\Http\Controllers\Api;

use http\Url;
use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiDashboardController extends Controller
{

    ## Private Function
    private function response($data){
        return ['status'=>'1','data'=>$data];
    }
    
    private function error($code, $description){
        return ['status'=>-1,'code'=>$code,'error'=>$description];
    }
    
    /**
     * Get dashboard data.
     *
     * @param  
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request){
        if (auth()->check()) {
            $user = auth()->user();
            if(!empty($user->robot_serial)){
                return $this->error(-1,trans('main.not_user'));
            }
            $dashboard_info = array();
            $robot_serial = $request->robot_serial;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $unit = 0;
            if(!empty($request->unit)){
                $unit = $request->unit;
            }
            $floor = 0;
            if(!empty($request->floor)){
                $floor = $request->floor;
            }
            $room = 0;
            if(!empty($request->room)){
                $room = $request->room;
            }
            
            if(!empty($robot_serial) && !empty($start_date) && !empty($end_date)){
                $robot_info = $this->getRobotInfo($robot_serial);
                $total_info = $this->getTotalInfo($robot_serial,$start_date,$end_date,$unit,$floor,$room);
                $performed_task_info = $this->getPerformedTask($robot_serial,$start_date,$end_date,$unit,$floor,$room);
                $performed_task_day_info = $this->getPerformedTasksByDay($robot_serial,$start_date,$end_date,$unit,$floor,$room);
                $performed_task_unit_info = $this->getPerformedTasksByUnit($robot_serial,$start_date,$end_date,$unit,$floor,$room);
            }else{
                $robot_info = array();
                $total_info = array();
                $performed_task_info = array();
                $performed_task_day_info = array();
                $performed_task_unit_info = array();
            }  
            $dashboard_info['robot_info'] = $robot_info;
            $dashboard_info['total_info'] = $total_info;
            $dashboard_info['performed_task_info'] = $performed_task_info;
            $dashboard_info['performed_task_day_info'] = $performed_task_day_info;
            $dashboard_info['performed_task_unit_info'] = $performed_task_unit_info;  
            // print_r($dashboard_info);        
            return $this->response($dashboard_info);
        }else{
            return $this->error(-1,trans('main.please_login'));
        }        	
    }

    public function getRobotInfo($robot_serial){
        $query = DB::table('robots_info_table')
            ->selectRaw("robots_info_table.*")
            ->where("robots_info_table.robot_serial", "=", $robot_serial);
        $robot_info = $query->first();
        // print_r($robot_info); 
        return $robot_info;
    }

    public function getTotalInfo($robot_serial,$start_date,$end_date,$unit,$floor,$room){        
        // get total_info in room_disinfection_table -------------------
        $query1 = DB::table('room_disinfection_table')
            ->selectRaw("SUM(room_disinfection_table.duration) AS total_duration
                        , COUNT(*) AS total_cnt")
            ->where("room_disinfection_table.robot_serial", "=", $robot_serial)
            ->whereRaw('DATEDIFF("'.$start_date.'", room_disinfection_table.date) <= ?', 0)
            ->whereRaw('DATEDIFF(room_disinfection_table.date, "'.$end_date.'") <= ?', 0);
        if(!empty($unit)){
            $query1->where("room_disinfection_table.unit", "=", $unit);
        }
        if(!empty($floor)){
            $query1->where("room_disinfection_table.floor", "=", $floor);
        }
        if(!empty($room)){
            $query1->where("room_disinfection_table.room", "=", $room);
        }
        $room_total_info = $query1->first();        

        // get total_info in corridor_disinfection_table ---------------
        if(empty($room)){
            $query2 = DB::table('corridor_disinfection_table')
                ->selectRaw("SUM(corridor_disinfection_table.duration) AS total_duration
                            , COUNT(*) AS total_cnt")
                ->where("corridor_disinfection_table.robot_serial", "=", $robot_serial)
                ->whereRaw('DATEDIFF("'.$start_date.'", corridor_disinfection_table.date) <= ?', 0)
                ->whereRaw('DATEDIFF(corridor_disinfection_table.date, "'.$end_date.'") <= ?', 0);        
            $corridor_total_info = $query2->first(); 
        }else{
            $corridor_total_info = array();
        }

        // get completed task in room_disinfection_table -------------------
        $query3 = DB::table('room_disinfection_table')
            ->selectRaw("COUNT(*) AS completed_cnt")
            ->where("room_disinfection_table.robot_serial", "=", $robot_serial)
            ->where("room_disinfection_table.is_completed", "=", 1)
            ->whereRaw('DATEDIFF("'.$start_date.'", room_disinfection_table.date) <= ?', 0)
            ->whereRaw('DATEDIFF(room_disinfection_table.date, "'.$end_date.'") <= ?', 0);
        if(!empty($unit)){
            $query3->where("room_disinfection_table.unit", "=", $unit);
        }
        if(!empty($floor)){
            $query3->where("room_disinfection_table.floor", "=", $floor);
        }
        if(!empty($room)){
            $query3->where("room_disinfection_table.room", "=", $room);
        }
        $room_completed = $query3->first(); 

        // get completed task in corridor_disinfection_table -------------------
        if(empty($room)){
            $query4 = DB::table('corridor_disinfection_table')
                ->selectRaw("COUNT(*) AS completed_cnt")
                ->where("corridor_disinfection_table.robot_serial", "=", $robot_serial)
                ->where("corridor_disinfection_table.is_completed", "=", 1)
                ->whereRaw('DATEDIFF("'.$start_date.'", corridor_disinfection_table.date) <= ?', 0)
                ->whereRaw('DATEDIFF(corridor_disinfection_table.date, "'.$end_date.'") <= ?', 0);
            $corridor_completed = $query4->first();
        }else{
            $corridor_completed = array();
        }          

        $total_useage_time = 0;
        $average_useage_duration = 0;
        $room_total_useage = 0;
        $corridor_total_useage = 0;
        if(!empty($room_total_info->total_duration)){
            $room_total_useage = $room_total_info->total_duration;
        }
        if(!empty($corridor_total_info) && !empty($corridor_total_info->total_duration)){
            $corridor_total_useage = $corridor_total_info->total_duration;
        }

        $total_useage = $room_total_useage + $corridor_total_useage;
        $total_useage = floor($total_useage/1000); //milisecond -> second
        $total_hour = floor($total_useage/3600);
        $total_minute = floor(($total_useage%3600)/60);
        $total_sec = $total_useage % 60;
        $total_useage_time = $total_hour." ".trans('main.hours')." ".$total_minute." ".trans('main.minutes');
        
        $date1=date_create($start_date);
        $date2=date_create($end_date);
        $diff=date_diff($date1,$date2);
        $total_days = $diff->format("%a")+1;
        $average_duration = round($total_useage/$total_days); 
        $average_hour = floor($average_duration/3600);
        $average_minute = floor(($average_duration%3600)/60);
        $average_sec = $average_duration % 60;
        $average_useage_duration = $average_hour." ".trans('main.hours')." ".$average_minute." ".trans('main.minutes');  
        
        $rooms_count = 0;
        $corridor_count = 0;
        if(!empty($room_total_info->total_cnt)){
            $rooms_count = $room_total_info->total_cnt;
        }
        if(!empty($corridor_total_info->total_cnt)){
            $corridor_count = $corridor_total_info->total_cnt;
        }

        $room_completed_count = 0;
        $corridor_completed_count = 0;
        $total_completed_count = 0;
        $completed_tasks = 0;

        if(!empty($room_completed->completed_cnt)){
            $room_completed_count = $room_completed->completed_cnt;
        }
        if(!empty($corridor_completed) && !empty($corridor_completed->completed_cnt)){
            $corridor_completed_count = $corridor_completed->completed_cnt;
        }
        $total_completed_count = $room_completed_count + $corridor_completed_count;
        if($rooms_count>0 || $corridor_count>0){
            $completed_tasks = floor($total_completed_count/($rooms_count+$corridor_count)*100);
        }

        $total_info = array();
        $total_info['total_useage_time'] = $total_useage_time;
        $total_info['average_useage_duration'] = $average_useage_duration;
        $total_info['rooms_disinfected_count'] = $room_completed_count;
        $total_info['corridor_disinfected_count'] = $corridor_completed_count;
        $total_info['completed_tasks'] = $completed_tasks;
        // print_r($total_info);
        return $total_info;        
    }

    public function getPerformedTask($robot_serial,$start_date,$end_date,$unit,$floor,$room){       
        if(empty($room)){
            $performed_task_info = DB::table(function ($sub) {
                    $corridor_sql = DB::table('corridor_disinfection_table')
                        ->selectRaw("corridor_disinfection_table.unit,corridor_disinfection_table.floor
                            ,corridor_disinfection_table.duration,corridor_disinfection_table.date
                            ,corridor_disinfection_table.robot_serial,corridor_disinfection_table.is_completed");

                    $sub->from('room_disinfection_table')
                        ->selectRaw("room_disinfection_table.unit,room_disinfection_table.floor
                            ,room_disinfection_table.duration,room_disinfection_table.date
                            ,room_disinfection_table.robot_serial,room_disinfection_table.is_completed")
                        ->union($corridor_sql);
                }, 'a')
                ->selectRaw("a.*")
                ->where("a.robot_serial", "=", $robot_serial)
                ->where("a.is_completed", "=", 1)
                ->whereRaw('DATEDIFF("'.$start_date.'", a.date) <= ?', 0)
                ->whereRaw('DATEDIFF(a.date, "'.$end_date.'") <= ?', 0)
                ->orderBy('a.date', 'desc')
                ->get();
        }else{
            $performed_task_info = DB::table('room_disinfection_table')
                ->selectRaw("room_disinfection_table.unit,room_disinfection_table.floor
                    ,room_disinfection_table.duration,room_disinfection_table.date
                    ,room_disinfection_table.robot_serial")
                ->where("room_disinfection_table.robot_serial", "=", $robot_serial)
                ->where("room_disinfection_table.is_completed", "=", 1)
                ->whereRaw('DATEDIFF("'.$start_date.'", room_disinfection_table.date) <= ?', 0)
                ->whereRaw('DATEDIFF(room_disinfection_table.date, "'.$end_date.'") <= ?', 0)
                ->where("room_disinfection_table.unit", "=", $unit)
                ->where("room_disinfection_table.floor", "=", $floor)
                ->where("room_disinfection_table.room", "=", $room)
                ->orderBy('room_disinfection_table.date', 'desc')
                ->get();
        }            
        
        $result = array();
        foreach ($performed_task_info as $performed_task) {
            $row = array();
            $row['unit'] = $performed_task->unit;
            $row['floor'] = $performed_task->floor;
            $task_duration = floor($performed_task->duration/1000);
            $minute = floor($task_duration/60);
            $second = $task_duration % 60;
            if(strlen($second) == 1){
                $second = "0".$second;
            }
            $duration = $minute.":".$second;
            $row['duration'] = $duration;
            $c_date = date_create($performed_task->date);
            $date = date_format($c_date, 'd/m/Y H:i');
            $row['date'] = $date;
            $result[] = $row;
        }
        // print_r($result);
        return $result;
    }

    public function getPerformedTasksByDay($robot_serial,$start_date,$end_date,$unit,$floor,$room){
        if(empty($room)){
            $performed_task_info = DB::table(function ($sub) {
                    $corridor_sql = DB::table('corridor_disinfection_table')
                        ->selectRaw("corridor_disinfection_table.spots_count 
                            ,DATE_FORMAT(corridor_disinfection_table.date, '%Y-%m-%d') AS d_date
                            ,corridor_disinfection_table.robot_serial");

                    $sub->from('room_disinfection_table')
                        ->selectRaw("room_disinfection_table.spots_count
                            ,DATE_FORMAT(room_disinfection_table.date, '%Y-%m-%d') AS d_date
                            ,room_disinfection_table.robot_serial")
                        ->union($corridor_sql);
                }, 'a')
                ->selectRaw("SUM(a.spots_count) AS d_cnt,a.d_date")
                ->where("a.robot_serial", "=", $robot_serial)
                ->whereRaw('DATEDIFF("'.$start_date.'", a.d_date) <= ?', 0)
                ->whereRaw('DATEDIFF(a.d_date, "'.$end_date.'") <= ?', 0)
                ->groupBy('a.d_date')
                ->orderBy('a.d_date', 'ASC')
                ->get();
        }else{
            $performed_task_info = DB::table(function ($sub) {
                    $sub->from('room_disinfection_table')
                        ->selectRaw("room_disinfection_table.spots_count
                            ,DATE_FORMAT(room_disinfection_table.date, '%Y-%m-%d') AS d_date
                            ,room_disinfection_table.robot_serial
                            ,room_disinfection_table.unit
                            ,room_disinfection_table.floor
                            ,room_disinfection_table.room");
                }, 'a')
                ->selectRaw("SUM(a.spots_count) AS d_cnt,a.d_date")
                ->where("a.robot_serial", "=", $robot_serial)
                ->whereRaw('DATEDIFF("'.$start_date.'", a.d_date) <= ?', 0)
                ->whereRaw('DATEDIFF(a.d_date, "'.$end_date.'") <= ?', 0)
                ->where("a.unit", "=", $unit)
                ->where("a.floor", "=", $floor)
                ->where("a.room", "=", $room)
                ->groupBy('a.d_date')
                ->orderBy('a.d_date', 'ASC')
                ->get();
        }
        // print_r($performed_task_info);
        return $performed_task_info;
    }

    public function getPerformedTasksByUnit($robot_serial,$start_date,$end_date,$unit,$floor,$room){
        $query = "";
        $query .= " SELECT DISTINCT bb.unit ";
        $query .= " ,IFNULL(aa.d_cnt,0) AS u_cnt ";
        $query .= " FROM hospital_rooms_table bb";
        $query .= " LEFT JOIN ( ";
            $query .= " SELECT SUM(a.spots_count) AS d_cnt,a.unit ";
            $query .= " FROM ( ";
                $query .= " SELECT room_disinfection_table.spots_count ";
                $query .= " ,room_disinfection_table.date ";
                $query .= " ,room_disinfection_table.robot_serial ";
                $query .= " ,room_disinfection_table.unit ";
                if(!empty($room)){
                    $query .= " ,room_disinfection_table.floor ";
                    $query .= " ,room_disinfection_table.room ";
                }
                $query .= " FROM room_disinfection_table ";
                if(empty($room)){
                    $query .= " UNION ";
                    $query .= " SELECT corridor_disinfection_table.spots_count ";
                    $query .= " ,corridor_disinfection_table.date ";
                    $query .= " ,corridor_disinfection_table.robot_serial ";
                    $query .= " ,corridor_disinfection_table.unit ";
                    $query .= " FROM corridor_disinfection_table ";
                }                            
            $query .= " ) a ";            
            $query .= " WHERE a.robot_serial = '".$robot_serial."' ";
            $query .= " AND DATEDIFF('".$start_date."', a.date) <= 0 ";
            $query .= " AND DATEDIFF(a.date, '".$end_date."') <= 0 ";
            if(!empty($room)){
                $query .= " AND a.unit = '".$unit."' ";
                $query .= " AND a.floor = '".$floor."' ";
                $query .= " AND a.room = '".$room."' ";
            }
            $query .= " GROUP BY a.unit ";
            $query .= " ORDER BY a.unit ASC ";
            $query .= " ) aa ON  bb.unit = aa.unit ";

        $performed_task_info = DB::select($query); 
        // print_r($performed_task_info); 
        return $performed_task_info;      
    }
}
