<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Carbon\Carbon;
use App\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreEmployee;
use App\Http\Requests\UpdateEmployee;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(){
        if(!auth()->user()->can('view_employee'))
        {
            return abort(403,'Unauthorized');
        }
        return view('employee.index');
    }

    public function create(){
        if(!auth()->user()->can('create_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $departments = Department::orderBy('title')->get();
        $roles = Role::all();
        return view('employee.create',compact('departments','roles'));
    }

    public function store(StoreEmployee $request){ // validate request
        if(!auth()->user()->can('create_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $profile_img_name = null;
        if($request->hasFile('profile_img'))
        {
            $profile_img_file = $request->file('profile_img');
            $profile_img_name = uniqid().time().'.'.$profile_img_file->getClientOriginalExtension(); // 121212.111111.png
            Storage::disk('public')->put('employee/'.$profile_img_name, file_get_contents($profile_img_file));
        }
        $employee = new User();
        $employee->employee_id = $request->employee_id;
        $employee->name = $request->name;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->nrc_number = $request->nrc_number;
        $employee->gender = $request->gender;
        $employee->birthday = $request->birthday;
        $employee->address = $request->address;
        $employee->department_id = $request->department_id;
        $employee->date_of_join = $request->date_of_join;
        $employee->is_present = $request->is_present;
        $employee->profile_img = $profile_img_name;
        $employee->password = Hash::make($request->password);
        $employee->save();

        $employee->syncRoles($request->roles);

        return redirect()->route('employee.index')->with('create','Employee is successfully created');
    }

    public function edit($id){
        if(!auth()->user()->can('edit_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $employee = User::findOrFail($id);
        $departments = Department::orderBy('title')->get();
        $roles = Role::all();
        $old_roles = $employee->roles->pluck('id')->toArray();

        return view('employee.edit',compact('employee','departments','roles','old_roles'));
    }

    public function update($id,UpdateEmployee $request){
        if(!auth()->user()->can('edit_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $employee = User::findOrFail($id);

        $profile_img_name = $employee->profile_img;
        if($request->hasFile('profile_img'))
        {
            Storage::disk('public')->delete('employee/'.$employee->profile_img); // delete old image

            $profile_img_file = $request->file('profile_img');
            $profile_img_name = uniqid().time().'.'.$profile_img_file->getClientOriginalExtension(); // 121212.111111.png
            Storage::disk('public')->put('employee/'.$profile_img_name, file_get_contents($profile_img_file)); // insert image to app/storage/public/employee
        }
        $employee->employee_id = $request->employee_id;
        $employee->name = $request->name;
        $employee->phone = $request->phone;
        $employee->email = $request->email;
        $employee->nrc_number = $request->nrc_number;
        $employee->gender = $request->gender;
        $employee->birthday = $request->birthday;
        $employee->address = $request->address;
        $employee->department_id = $request->department_id;
        $employee->date_of_join = $request->date_of_join;
        $employee->is_present = $request->is_present;
        $employee->profile_img = $profile_img_name;
        $employee->password = $request->password ? Hash::make($request->password) : $employee->password;
        $employee->save();

        $employee->syncRoles($request->roles);
        
        return redirect()->route('employee.index')->with('update','Employee is successfully updated'); 
    }

    public function show($id){
        if(!auth()->user()->can('view_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $employee = User::findOrFail($id);
        return view('employee.show',compact('employee'));
    }
    
    public function destroy($id){
        if(!auth()->user()->can('delete_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $employee = User::findOrFail($id);
        $employee->delete();

        return 'success';
    }

    //Datatable
    public function ssd(Request $request){
        if(!auth()->user()->can('view_employee'))
        {
            return abort(403,'Unauthorized');
        }
        $employees = User::with('department'); //query နည်းဖို့အတွက်

        return DataTables::of($employees)
        ->filterColumn('department',function($query, $keyword){
            $query -> whereHas('department',function($q1) use($keyword) {
                $q1->where('title','like','%'. $keyword .'%');
            });
        })
        ->editColumn('profile_img',function($each){
            return '<img src="'. $each->profile_img_path() .'" class="profile-thumbnail"><p class="my-1">'.$each->name.'</p>';
        })
        ->addColumn('department',function($each){
            return $each->department ? $each->department->title : '-'; // retrieve eloquent data
        })
        ->addColumn('role_name',function($each){
            $output = '';
            foreach($each->roles as $role){
                $output .= '<span class="badge badge-pill badge-primary">'.$role->name.'</span>';
            }
            return $output;
        })
        ->editColumn('is_present',function($each){
            if($each->is_present == 1)
            {
                return '<span class="badge badge-pill badge-success">Present</span>';
            }
            else
            {
                return '<span class="badge badge-pill badge-danger">Leave</span>';
            }
        })
        ->editColumn('updated_at',function($each){
            return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
        })
        ->addColumn('action',function($each){
            $edit_icon = '';
            $info_icon = '';
            $delete_icon = '';

            if(auth()->user()->can('edit_employee'))
            {
                $edit_icon = '<a href= "' . route('employee.edit', $each->id ) . '" class="text-warning">
                <i class="far fa-edit"></i></a>';
            }
            if(auth()->user()->can('view_employee'))
            {
                $info_icon = '<a href= "' . route('employee.show', $each->id ) . '" class="text-primary">
                <i class="fas fa-info-circle"></i></a>';               
            }
            if(auth()->user()->can('delete_department'))
            {
                $delete_icon = '<a href= "#" class="text-danger delete-btn" data-id="'. $each->id .'">
                <i class="fas fa-trash-alt"></i></a>';
            }

            return '<div class="action-icon">'. $edit_icon . $info_icon .$delete_icon.'</div>';
        })
        ->addColumn('plus-icon',function($each){
            return null;
        })
        ->rawColumns(['profile_img','role_name','is_present','action']) // to work html code
        ->make(true);
    }
}
