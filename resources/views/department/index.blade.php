@extends('layouts.app')
@section('title','Department')
@section('content')
    @can('create_department')
    <div>
        <a href="/department/create" class="btn btn-theme btn-sm"><i class="fas fa-plus-circle"></i> Create Department</a>
    </div>  
    @endcan

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered datatable" style="width:100%">
                <thead>
                    <th class="text-center no-sort no-search"></th>
                    <th class="text-center">Title</th>
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
                ajax: "/department/datatable/ssd",
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', class: 'text-center'},
                    {data: 'title', name: 'title', class: 'text-center'},
                    {data: 'updated_at', name: 'updated_at', class: 'text-center'},
                    {data: 'action', name: 'action', class: 'text-center'},
                ],
                order:[[2,'desc']],
                columnDefs: [
                                {
                                    target: 2,
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
                                url: `/department/${id}`,
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