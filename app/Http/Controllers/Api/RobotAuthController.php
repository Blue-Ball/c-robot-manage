<?php

namespace App\Http\Controllers\Api;

use http\Url;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RobotInfo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class RobotAuthController extends Controller
{
    ## Private Function
    private function response($data){
        return ['status'=>'1','data'=>$data];
    }
    
    private function error($code, $description){
        return ['status'=>-1,'code'=>$code,'error'=>$description];
    }

    /**
     * Sign up robot
     *
     * @param Illuminate\Http\Request $request
     * @return array $data
     */
    public function robotRegister(Request $request){ 
        
        if(empty($request->serial)){
            return $this->error('-2',trans('main.enter_robot_serial'));
        }

        if(empty($request->name)){
            return $this->error('-2',trans('main.enter_robot_name'));
        }

        $validator_password = Validator::make(array('password'=>$request->password), [
            'password' => 'required|min:4',
        ]);        
        if ($validator_password->fails()) {
            return $this->error(-2,trans('main.password_validation_error'));
        }

        if($request->password != $request->re_password) {
            return $this->error('-2',trans('main.pass_confirmation_same'));
        }

        if(empty($request->number)){
            return $this->error('-2',trans('main.enter_robot_number'));
        }

        $duplicateSerial = User::where('robot_serial',$request->serial)->first();
        if($duplicateSerial) {
            return $this->error(-2,trans('main.robot_exists'));
        }
                
        $newUser = [
            'name'          => $request->name,            
            'password'      => Hash::make($request->password),
            'robot_serial'  => $request->serial,
            'is_admin'      => 0,
            'status'        => 0,
            'token'         => Str::random(24),
            'created_at'    => time()            
        ];
        
        $regUser = User::create($newUser);
        $newRobot = [
            'robot_serial'      => $request->serial,
            'robot_name'        => $request->name,
            'robot_password'    => Hash::make($request->password),
            'robot_number'      => $request->number,
            'created_at'    => time() 
        ];
        RobotInfo::create($newRobot);
        
        return $this->response(['message'=>trans('main.thanks_reg')]);
    }

    /**
     * Sign in robot
     *
     * @param Illuminate\Http\Request $request
     * @return array $data
     */
    public function robotLogin(Request $request){
        
        $serial = $request->serial;
        $password = $request->password;
        auth()->factory()->setTTL(60*24);
        if ($token = auth()->attempt(['robot_serial' => $serial, 'password' => $password, 'status' => 1])) {
            return $this->createNewToken($token);
        }else{
            return $this->error(-2,trans('main.incorrect_login_robot'));
        }	
    } 
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(), 
            'user' => auth()->user()
        ];
        return $this->response($data);
    }

    /**
     * Sign out robot
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function robotLogout()
    {
        auth()->logout();
        return $this->response(['message'=>trans('main.robot_signed_out')]);
    }

    /**
     * Get rogbot list.
     *
     * @param  
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRobotList(){
        if (auth()->check()) {
            $user = auth()->user();
            if($user->is_admin){
                $robot_list = DB::table('users')
                    ->selectRaw('users.*,robots_info_table.robot_number')
                    ->join('robots_info_table', 'users.robot_serial', '=', 'robots_info_table.robot_serial')
                    ->where('users.robot_serial','!=','')
                    ->get();
                return $this->response($robot_list);
            }else{
                return $this->error(-2,trans('main.not_allowed_admin_permission'));
            }            
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }

    /**
     * Change robot status.
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRobotStatus(Request $request){
        if (auth()->check()) {
            $user = auth()->user();
            if($user->is_admin){
                $id = 0;
                if(!empty($request->id)){
                    $id = $request->id;
                }                 
                $status = 0;
                if(!empty($request->status)){
                    $status = $request->status;
                }  
                $userUpdate = User::find($id);              
                if(empty($userUpdate)){
                    return $this->error(-2,trans('main.not_find_user'));
                }else{                    
                    $userUpdate->update(['status' => $status]);
                    return $this->response(['message'=>trans('main.updated_success')]);
                }                
            }else{
                return $this->error(-2,trans('main.not_allowed_admin_permission'));
            }            
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }

    /**
     * Change robot password.
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeRobotPassword(Request $request){
        if (auth()->check()) {

            if(empty($request->new_password)){
                return $this->error(-2,trans('main.enter_new_password'));
            } 
            if($request->new_password != $request->re_password) {
                return $this->error(-2,trans('main.pass_confirmation_same'));
            }

            $user = auth()->user();
            $id = $user->id;
            $new_password = Hash::make($request->new_password);

            $userUpdate = User::find($id);              
            if(empty($userUpdate)){
                return $this->error(-2,trans('main.not_find_user'));
            }else{                    
                $userUpdate->update(['password' => $new_password]);

                DB::table('robots_info_table')
                    ->where('robots_info_table.robot_serial', $user->robot_serial)
                    ->update(['robot_password' => $new_password]);

                return $this->response(['message'=>trans('main.updated_success')]);
            }         
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }
}
