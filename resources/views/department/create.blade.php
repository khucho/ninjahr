@extends('layouts.app')
@section('title','Create Department')
@section('content')
   <div class="card">
        <div class="card-body">
            <form action="{{route('department.store')}}" method="post" autocomplete="off" id="create-form" enctype="multipart/form-data">  {{-- autocomplete="off" => no suggestion in chrome --}}
                @csrf
                        <div class="md-form">
                            <label for="" class="form-label">Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>
                        <div class="d-flex justify-content-center mt-5 mb-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-theme btn-sm btn-block">Confirm</button>
                            </div>
                        </div>
            </form>
        </div>
   </div>
@endsection
@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreDepartment', '#create-form') !!}
    <script>
    $(document).ready(function(){
        
    })
    </script>
   
@endsection