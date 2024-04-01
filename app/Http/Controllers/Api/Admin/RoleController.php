<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
    $roles=Role::when(request()->search,function($roles){
        $roles=$roles->where('name','like','%'. request()->search . '%');
    })->with('permissions')->latest()->paginate(5);
    $roles->appends(['search'=>request()->search]);
    return new RoleResource(true, 'list Data Roles', $roles);    
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name' =>'required',
            'permissions'=>'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        //create role
        $role=Role::create(['name'=>$request->name]);

        //assign permissions to role
        $role->givePermissionTo($request->permissions);
        if($role){
            //return success with api resource
            return new RoleResource(true,'Data Role berhasil disimpan!',$role);
        }
        //return failed with api resource
        return new RoleResource(false,'Data Role Gagal Disimpan!',null);
    }

    public function show($id)
    {
        $role=Role::with('permissions')->findOrFail($id);
        if($role){
            //return success with Api resources
            return new RoleResource(true,'detail data role!',$role);
        }
        return new RoleResource(false,'Detail Data Role tidak Ditemukan!',null);
    }

    public function update(Request $request,Role $role)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'permissions'=>'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        //update role
        $role->update(['name'=>$request->name]);

        //sync permissions
        $role->syncPermissions($request->permissions);
        if($role){
            //return success with Api resource
            return new RoleResource(true,'Data Role berhasil Di update!',$role);
        }
        //return failed with Api resource
        return new RoleResource(false,'Data role gagal Di update',null);
    }

    public function destroy($id)
    {
        //find role by ID
        $role=Role::findOrFail($id);
        
        if($role->delete()){
            //return success with api resource
            return new RoleResource(true,'Data Role Berhasil dihapus',null);
        }
        //return failed with api resources
        return new RoleResource(false,'Data Role Gagal dihapus!',null);
    }
    public function all()
    {
        //get roles
        $roles=Role::latest()->get();

        //return with api resource
        return new RoleResource(true,'List Data Roles',$roles);
    }
}
