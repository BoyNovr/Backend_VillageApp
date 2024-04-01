<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users=User::when(request()->search, function($users){
            $users=$users->where('name','like','%'. request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search'=>request()->search]);
    
        //return with api resource
        return new UserResource(true,'List Data Users',$users);
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:users',
            'password'=>'required|confirmed'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //create user
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole($request->roles);

        if($user){
            //return succes with api resource
            return new UserResource(true,'Data user berhasil disimpan',$user);
        }
        //return failed with api resource
        return new UserResource(false,'Data User Gagal Disimpan',null);
    }

    public function show($id)
    {
        $user=User::with('roles')->whereId($id)->first();
        if($user){
            //return succes with api resource
            return new UserResource(true,'detail data user!',$user);
        }
        //return failed with api resource
        return new UserResource(false,'detail data user tidak ditemukan',null);
    }

    public function update(Request $request,User $user)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:users,email,'.$user->id,
            'password'=>'confirmed'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        if($request->password==""){
            //updare user without password
            $user->update([
                'name'=>$request->name,
                'email'=>$request->email,
            ]);
        }else{
            //update user with new password
            $user->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
        }
        //assign roles to user
        $user->syncRoles($request->roles);
        if($user){
            //return succes with api resource
            return new UserResource(true,'Data User Berhasil Diupdate',$user);
        }
        //return failed with api resource
        return new UserResource(false,'Data user Gagal Diupdate',null);
    }

    public function destroy(User $user)
    {
        if($user->delete()){
            //return success with api resource
            return new UserResource(true,'data User berhasil di hapus',null);
        }
        //return failed with api resource
        return new UserResource(false,'Data user gagal di hapus',null);
    }
}
