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
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use \Illuminate\Http\JsonResponse
use Illuminate\Support\Facades\DB;


class ApiAuthController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['userLogin', 'userRegister']]);
    }

    ## Private Function
    private function response($data){
        return ['status'=>'1','data'=>$data];
    }
    
    private function error($code, $description){
        return ['status'=>-1,'code'=>$code,'error'=>$description];
    }

    /**
     * Sign up user
     *
     * @param Illuminate\Http\Request $request
     * @return array $data
     */
    public function userRegister(Request $request){ 
        
        if(empty($request->name)){
            return $this->error('-2',trans('main.enter_user_name'));
        }

        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'password' => 'required|min:4',
        //     'email' => 'required|email',
        // ]);

        $validator_password = Validator::make(array('password'=>$request->password), [
            'password' => 'required|min:4',
        ]);        
        if ($validator_password->fails()) {
            return $this->error(-2,trans('main.password_validation_error'));
        }

        if($request->password != $request->re_password) {
            return $this->error('-2',trans('main.pass_confirmation_same'));
        }

        if(empty($request->email)){
            return $this->error('-2',trans('main.enter_email'));
        }

        $validator_email = Validator::make(array('email'=>$request->email), [
            'email' => 'required|email',
        ]);        
        if ($validator_email->fails()) {
            return $this->error(-2,trans('main.email_validation_error'));
        }

        $duplicateEmail = User::where('email',$request->email)->first();
        if($duplicateEmail) {
            return $this->error(-2,trans('main.user_exists'));
        }
                
        $newUser = [
            'name'          => $request->name,            
            'password'      => Hash::make($request->password),
            'email'         => $request->email,
            'is_admin'      => 0,
            'status'      => 0,
            'token'         => Str::random(24),
            'created_at'    => time()            
        ];
        
        $regUser = User::create($newUser);
        return $this->response(['message'=>trans('main.thanks_reg')]);
    }

    /**
     * Sign in user
     *
     * @param Illuminate\Http\Request $request
     * @return array $data
     */
    public function userLogin(Request $request){
        $email = $request->email;
        $password = $request->password;
        auth()->factory()->setTTL(60*24); 
        if ($token = auth()->attempt(['email' => $email, 'password' => $password, 'status' => 1])) {
            return $this->createNewToken($token);
        }else{
            return $this->error(-2,trans('main.incorrect_login'));
        }	
    }

    /**
     * Sign out user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLogout()
    {
        auth()->logout();
        return $this->response(['message'=>trans('main.signed_out')]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
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
     * Get user list.
     *
     * @param  
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserList(){
        if (auth()->check()) {
            $user = auth()->user();
            if($user->is_admin){
                $user_list = DB::table('users')
                    ->selectRaw('users.*')
                    ->get();
                return $this->response($user_list);
            }else{
                return $this->error(-2,trans('main.not_allowed_admin_permission'));
            }            
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }

    /**
     * Change user status.
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeUserStatus(Request $request){
        if (auth()->check()) {
            $user = auth()->user();
            if($user->is_admin){
                $id = 0;
                if(!empty($request->user_id)){
                    $id = $request->user_id;
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
     * Change user password.
     *
     * @param  Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeUserPassword(Request $request){
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
                return $this->response(['message'=>trans('main.updated_success')]);
            }         
        }else{
            return $this->error(-1,trans('main.please_login'));
        }
    }
}
