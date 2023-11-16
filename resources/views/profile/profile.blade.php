@extends('layouts.app')
@section('title','Profile')
@section('content')
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <img src="{{$employee->profile_img_path()}}" alt="" class="profile_img">
                        <div class="py-4 px-3">
                            <h4>{{$employee->name}}</h4>
                            <p class="text-muted mb-2"><span class="text-muted">{{$employee->employee_id}}</span> | <span class="text-theme">{{$employee->phone}}</span></p>
                            <p class="text-muted mb-2"><span class="badge badge-pill badge-light border">{{$employee->department ? $employee->department->title : '-'}}</span></p>
                            <p class="text-muted mb-2"><span class="badge badge-pill badge-primary">
                                @foreach ($employee->roles as $role)
                                    {{$role->name}}
                                @endforeach
                            </span></p>
                        </div>
                    </div>              
                </div>
                <div class="col-md-6 dash-border px-3">
                        <p class="mb-1"><strong>Phone</strong> : <span class="text-muted">{{$employee->phone}}</span></p>
                        <p class="mb-1"><strong>Email</strong> : <span class="text-muted">{{$employee->email}}</span></p>
                        <p class="mb-1"><strong>NRC Number</strong> : <span class="text-muted">{{$employee->nrc_number}}</span></p>
                        <p class="mb-1"><strong>Gender</strong> : <span class="text-muted">{{ucfirst($employee->gender)}}</span></p>
                        <p class="mb-1"><strong>Birthday</strong> : <span class="text-muted">{{$employee->birthday}}</span></p>
                        <p class="mb-1"><strong>Address</strong> : <span class="text-muted">{{$employee->address}}</span></p>
                        <p class="mb-1"><strong>Date of Join</strong> : <span class="text-muted">{{$employee->date_of_join}}</span></p>
                        <p class="mb-1"><strong>Is Present?</strong> : 
                            <span class="text-muted">
                                @if ($employee->is_present == 1)
                                <span class="badge badge-pill badge-success">Present</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">Leave</span>
                                    @endif
                            </span>
                        </p>
                </div>
            </div>
           
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5>Biometric Authentication</h5>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <a href="#" class="logout-btn btn btn-theme btn-block"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            $('.logout-btn').on('click',function(e){
                e.preventDefault();

                swal({
                    text: "Are you sure you want to delete?",
                    buttons: true,
                    dangerMode: true,
                    })
                    .then((willDelete) => {
                        if(willDelete)
                        {
                            $.ajax({
                                url:'/logout',
                                method: 'POST',
                            }).done(function(response){
                                window.location.reload();
                            });
                        }
                    });
              
            })
        });
    </script>

@endsection