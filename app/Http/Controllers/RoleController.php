<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Carbon\Carbon;
use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRole;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UpdateRole;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(){
        if(!auth()->user()->can('view_role'))
        {
            return abort(403,'Unauthorized');
        }
        return view('role.index');
    }

    public function create(){
        if(!auth()->user()->can('create_role'))
        {
            return abort(403,'Unauthorized');
        }
        $permissions = Permission::all();
        return view('role.create',compact('permissions'));
    }

    public function store(StoreRole $request){ // validate request
        if(!auth()->user()->can('create_role'))
        {
            return abort(403,'Unauthorized');
        }
        $role = new Role();
        $role->name = $request->name;
        $role->save();

        $role->givePermissionTo($request->permissions); // give permission to role (laravel spatie)

        return redirect()->route('role.index')->with('create','Role is successfully created');
    }

    public function edit($id){
        if(!auth()->user()->can('edit_role'))
        {
            return abort(403,'Unauthorized');
        }
        $permissions = Permission::all();
        $role = Role::findOrFail($id);

        $old_permission = $role->permissions->pluck('id')->toArray();
        return view('role.edit',compact('role','permissions','old_permission'));
    }

    public function update($id,UpdateRole $request){
        if(!auth()->user()->can('edit_role'))
        {
            return abort(403,'Unauthorized');
        }
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->update();

        $old_permission = $role->permissions->pluck('name')->toArray();
        $role->revokePermissionTo($old_permission); // remove old data
        $role->givePermissionTo($request->permissions); // insert edit data

        return redirect()->route('role.index')->with('update','Role is successfully updated'); 
    }
    
    public function destroy($id){
        if(!auth()->user()->can('delete_role'))
        {
            return abort(403,'Unauthorized');
        }
        $role = Role::findOrFail($id);
        $role->delete();

        return 'success';
    }

    //Datatable
    public function ssd(Request $request){
        if(!auth()->user()->can('view_role'))
        {
            return abort(403,'Unauthorized');
        }
        $roles = Role::query(); //query နည်းဖို့အတွက်

        return DataTables::of($roles)
        ->addColumn('permissions',function($each){
            $output = '';
            foreach ($each->permissions as $permission) {
                $output .= '<span class="badge badge-pill badge-primary m-1">'.$permission->name.'</span>';
            }
            return $output;
        })
        ->addColumn('action',function($each){
            $edit_icon = '';
            $delete_icon = '';

            if(auth()->user()->can('edit_role'))
            {
                $edit_icon = '<a href= "' . route('role.edit', $each->id ) . '" class="text-warning">
                <i class="far fa-edit"></i></a>';
            }
            if(auth()->user()->can('delete_role'))
            {
                $delete_icon = '<a href= "#" class="text-danger delete-btn" data-id="'. $each->id .'">
                <i class="fas fa-trash-alt"></i></a>'; 
            }

            return '<div class="action-icon">'. $edit_icon .$delete_icon.'</div>';
        })
        ->editColumn('updated_at',function($each){
            return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
        })
        ->addColumn('plus-icon',function($each){
            return null;
        })
        ->rawColumns(['permissions','action']) // to work html code
        ->make(true);
    }
}

