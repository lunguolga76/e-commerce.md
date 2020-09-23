@extends('layouts.admin_layout.admin_layout')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Catalogues</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Banners</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-top: 10px">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 10px">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <form name="bannerForm" id="bannerForm" @if(empty($banner['id'])) action="{{url('admin/add-edit-banner')}}"
                      @else action="{{url('admin/add-edit-banner/'.$banner['id'])}}" @endif method="post" enctype="multipart/form-data">@csrf
                <!-- SELECT2 EXAMPLE -->
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{$title}}</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="image">Banner Image</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="image" name="image">
                                                <label class="custom-file-label" for="image">Choose file</label>
                                            </div>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="">Upload</span>
                                            </div>
                                        </div>
                                        @if(!empty($banner['image']))
                                            <div>
                                                <img style="width: 220px;margin-top: 15px;margin-bottom: 5px;" src="{{asset('images/banner_images/'.$banner['image'])}}">
                                                
                                            </div>
                                        @endif
                                        <div>Recommended Image Size:Width:1170px, Height:4800px.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <label for="link">Banner Link</label>
                                    <input type="text" class="form-control" name="link" id="link"
                                           placeholder="Enter Banner Link"@if(!empty($banner['link']))
                                           value="{{$banner['link']}}" @else value="{{old('link')}}" @endif>
                                </div>
                                </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="title">Banner Title</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                   placeholder="Enter Banner Title"@if(!empty($banner['title']))
                                                   value="{{$banner['title']}}" @else value="{{old('title')}}" @endif>
                                        </div>
                                        <div class="form-group">
                                            <label for="alt">Banner Alternative Text</label>
                                            <input type="text" class="form-control" name="alt" id=alt"
                                                   placeholder="Enter Banner Alternative Text"@if(!empty($banner['alt']))
                                                   value="{{$banner['alt']}}" @else value="{{old('alt')}}" @endif>
                                </div>
                              </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                </form>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

@endsection
