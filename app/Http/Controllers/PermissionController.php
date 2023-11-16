<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Carbon\Carbon;
use App\Department;
use App\Http\Requests\StorePermission;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRole;
use App\Http\Requests\UpdatePermission;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UpdateRole;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(){
        if(!auth()->user()->can('view_permission'))
        {
            return abort(403,'Unauthorized');
        }
        return view('permission.index');
    }

    public function create(){
        if(!auth()->user()->can('create_permission'))
        {
            return abort(403,'Unauthorized');
        }
        return view('permission.create');
    }

    public function store(StorePermission $request){ // validate request
        if(!auth()->user()->can('create_permission'))
        {
            return abort(403,'Unauthorized');
        }
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->save();

        return redirect()->route('permission.index')->with('create','Permission is successfully created');
    }

    public function edit($id){
        if(!auth()->user()->can('edit_permission'))
        {
            return abort(403,'Unauthorized');
        }
        $permission = Permission::findOrFail($id);
        return view('permission.edit',compact('permission'));
    }

    public function update($id,UpdatePermission $request){
        if(!auth()->user()->can('edit_permission'))
        {
            return abort(403,'Unauthorized');
        }
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->update();

        return redirect()->route('permission.index')->with('update','Permission is successfully updated'); 
    }
    
    public function destroy($id){
        if(!auth()->user()->can('delete_permission'))
        {
            return abort(403,'Unauthorized');
        }
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return 'success';
    }

    //Datatable
    public function ssd(Request $request){
        if(!auth()->user()->can('view_permission'))
        {
            return abort(403,'Unauthorized');
        }
        $permissions = Permission::query(); //query နည်းဖို့အတွက်

        return DataTables::of($permissions)
        ->addColumn('action',function($each){
            $edit_icon = '';
            $delete_icon = '';
            
            if(auth()->user()->can('edit_permission'))
            {
                $edit_icon = '<a href= "' . route('permission.edit', $each->id ) . '" class="text-warning">
                <i class="far fa-edit"></i></a>';    
            }
            
            if(auth()->user()->can('delete_permission'))
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
        ->rawColumns(['action']) // to work html code
        ->make(true);
    }
}

