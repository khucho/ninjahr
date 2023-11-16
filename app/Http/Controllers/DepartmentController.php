<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Carbon\Carbon;
use App\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreEmployee;
use App\Http\Requests\UpdateEmployee;
use App\Http\Requests\StoreDepartment;
use App\Http\Requests\UpdateDepartment;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
    public function index(){
        if(!auth()->user()->can('view_department'))
        {
            return abort(403,'Unauthorized');
        }
        return view('department.index');
    }

    public function create(){
        if(!auth()->user()->can('create_department'))
        {
            return abort(403,'Unauthorized');
        }
        return view('department.create');
    }

    public function store(StoreDepartment $request){ // validate request
        if(!auth()->user()->can('create_department'))
        {
            return abort(403,'Unauthorized');
        }
        $department = new Department();
        $department->title = $request->title;
        $department->save();

        return redirect()->route('department.index')->with('create','Department is successfully created');
    }

    public function edit($id){
        if(!auth()->user()->can('edit_department'))
        {
            return abort(403,'Unauthorized');
        }
        $department = Department::findOrFail($id);
        return view('department.edit',compact('department'));
    }

    public function update($id,UpdateDepartment $request){
        if(!auth()->user()->can('edit_department'))
        {
            return abort(403,'Unauthorized');
        }
        $department = Department::findOrFail($id);
        $department->title = $request->title;
        $department->update();

        return redirect()->route('department.index')->with('update','Department is successfully updated'); 
    }
    
    public function destroy($id){
        if(!auth()->user()->can('delete_department'))
        {
            return abort(403,'Unauthorized');
        }
        $department = Department::findOrFail($id);
        $department->delete();

        return 'success';
    }

    //Datatable
    public function ssd(Request $request){
        if(!auth()->user()->can('view_department'))
        {
            return abort(403,'Unauthorized');
        }
        $department = Department::query(); //query နည်းဖို့အတွက်

        return DataTables::of($department)
        ->addColumn('action',function($each){
            $edit_icon = '';
            $delete_icon = '';
            
            if(auth()->user()->can('edit_department'))
            {
                $edit_icon = '<a href= "' . route('department.edit', $each->id ) . '" class="text-warning">
                <i class="far fa-edit"></i></a>';  
            }
            if(auth()->user()->can('delete_department'))
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

