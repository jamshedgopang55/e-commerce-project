@extends('admin.layout.app')
@section('content')

    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('category.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form method="GET" id="categoryForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name*</label>
                                    <input type="text" name="name" value="{{$category->name}}" id="name" class="form-control"
                                        placeholder="Name">
                                    <p></p>
                                    <input hidden type="text" name="image_id" id="image_id" class="form-control"
                                        placeholder="image_id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">slug*</label>
                                    <input type="text"  value="{{$category->slug}}" readonly name="slug" id="slug" class="form-control"
                                        placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image">slug</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            <br>Drop files here or click to upload.<br><br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" id="">
                                        <option value="1" {{( $category->status==1) ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{($category->status==0) ? 'selected' : ''}} >Blocked</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="showOnHome">Show On Home</label>
                                    <select name="showOnHome" id="status" class="form-control" id="">
                                        <option value="Yes" {{($category->showOnHome=='Yes') ? 'selected' : ''}}>Yes</option>
                                        <option value="No" {{($category->showOnHome=='No') ? 'selected' : ''}} >No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="mb-3">                                       @if ($category->image!='NULL')
                                        <img width="200px" height="200px" src="{{asset('uploads/category/'.$category->image)}}" alt="">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button id="btn" type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('category.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

            </form>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $(document).ready(function() {
            $('#categoryForm').submit('click', function(e) {
                $('#btn').attr('disabled', true)
                const data = $(this).serializeArray()
                let name = document.getElementById('name').value;
                let slug = document.getElementById('slug').value;
                e.preventDefault();

                $.ajax({
                    url: "{{ route('category.update',$category->id) }}",
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        if (response['status'] == true) {
                            $('#btn').attr('disabled', false)
                            window.location.href = "{{ route('category.index') }}"
                            $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html("")
                        } else {
                            let errors = response.errors
                            if (errors['name']) {
                                $('#name').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['name'])
                            } else {
                                $('#name').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }

                            if (errors['slug']) {
                                $('#slug').addClass('is-invalid').siblings('p').addClass(
                                    'invalid-feedback').html(errors['slug'])
                            } else {
                                $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                                    'invalid-feedback').html("")
                            }
                        }

                    }
                })
            })
        })

        $('#name').on('input', function() {
            element = $(this)
            tittle = element.val()
            $.ajax({
                url: "{{ route('getSlug') }}",
                type: 'GET',
                data: {
                    title: element.val()
                },
                success: function(response) {
                    if (response['status'] == true) {
                        $('#slug').val(response['slug'])
                    }
                }
            })
        })



        //   let btn = document.getElementById('btn')
        //   let value = 'sdf';
        //   let form = document.getElementById('categoryForm')
        //   form.addEventListener('click',function(e){
        //     e.preventDefault();
        //   })

        //     btn.addEventListener('click',()=>{

        //         // console.log(value);
        //     const xhr = new XMLHttpRequest();
        //     let _token = document.querySelector("input[name='_token']").value;
        //     let data ={_token : _token,value:value}
        //         console.log(data);
        //    xhr.open("get",'/admin/category/store',true);

        //    xhr.onload = function() {
        //     if(xhr.status==200){
        //         console.log(xhr.responseText)
        //     }
        // }


                //    xhr.send(data);
                //     })

        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function() {
                this.on('addedfile', function(file) {
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                });
            },
            url:  "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(file, response){
                $("#image_id").val(response.image_id);
            }
        })


    </script>
@endsection
