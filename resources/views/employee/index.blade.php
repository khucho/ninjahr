@extends('layouts.app')
@section('title','Employees')
@section('content')
    @can('create_employee')
    <div>
        <a href="/employee/create" class="btn btn-theme btn-sm"><i class="fas fa-plus-circle"></i> Create Employee</a>
    </div>  
    @endcan
   
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered datatable" style="width:100%">
                <thead>
                    <th class="text-center no-sort no-search"></th>
                    <th class="text-center no-sort"></th>
                    <th class="text-center">Employee ID</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Phone</th>
                    <th class="text-center">Department</th>
                    <th class="text-center">Role (or) Designation</th>
                    <th class="text-center">Is Present</th>
                    <th class="text-center hidden">Updated At</th>
                    <th class="text-center">Action</th>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>
    $(document).ready(function(){
            var table = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "/employee/datatable/ssd",
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', class: 'text-center'},
                    {data: 'profile_img', name: 'profile_img', class: 'text-center'},
                    {data: 'employee_id', name: 'employee_id', class: 'text-center'},
                    {data: 'email', name: 'email', class: 'text-center'},
                    {data: 'phone', name: 'phone', class: 'text-center'},
                    {data: 'department', name: 'department', class: 'text-center'},
                    {data: 'role_name', name: 'role_name', class: 'text-center'},
                    {data: 'is_present', name: 'is_present', class: 'text-center'},
                    {data: 'updated_at', name: 'updated_at', class: 'text-center'},
                    {data: 'action', name: 'action', class: 'text-center'},
                ],
                order:[[8,'desc']],
                columnDefs: [
                                {
                                    target: 8,
                                    visible: false
                                },
                                {
                                    target: 0,
                                    class: 'control'
                                },
                                {
                                    target: 'no-sort',
                                    orderable: false
                                },
                                {
                                    target: 'no-search',
                                    searchable: false
                                },
                                {
                                    target: 'hidden',
                                    visible: false
                                }
                            ],
                language: {
                        "paginate": {
                        "previous": "<i class='far fa-arrow-alt-circle-left'></i>",
                        "next": "<i class='far fa-arrow-alt-circle-right'></i>",
                        
                        },
                        // "processing": "<img src='/image/loading.gif' style='width:100px' />",
                    },
               
            });
            $(document).on('click','.delete-btn',function(e){
                e.preventDefault();

                var id = $(this).data('id');

                swal({
                    text: "Are you sure you want to delete?",
                    buttons: true,
                    dangerMode: true,
                    })
                    .then((willDelete) => {
                        if(willDelete)
                        {
                            $.ajax({
                                method: "DELETE",
                                url: `/employee/${id}`,
                                })
                                .done(function( response ) {
                                    table.ajax.reload();
                                });
                        }
                    });
            })
        })
    </script>
   
@endsection