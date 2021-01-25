@extends('dashboard')
@section('content')
<div class="income-order-visit-user-area mg-t-40">
    <div class="container">

    </div>
</div>
<!-- Transitions Start-->
<div class="transition-world-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="transition-world-list shadow-reset">
                    <div class="sparkline7-list mg-b-40">
                        <div class="sparkline7-hd">
                            <div class="main-spark7-hd">
                                <h1>Edit Category</h1>
                            </div>
                        </div>
                        <div class="sparkline7-graph">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div
                                        class="datatable-dashv1-list custom-datatable-overright dashtwo-project-list-data">
                                        <div id="toolbar">
                                            @include('includes.alerts')
                                        </div>
                                        <!-- Basic Form Start -->
                                        <div class="basic-form-area mg-b-40">

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="sparkline8-list basic-res-b-30 shadow-reset">

                                                        <div class="sparkline8-graph">
                                                            <div class="basic-login-form-ad">

                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <div class="basic-login-inner">
                                                                            <p>You can update question category details here</p>
                                                                            <form action="{{route('category.update', $category->id)}}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('PATCH')

                                                                                <div class="form-group-inner">
                                                                                    <div
                                                                                        class="form-group-inner">
                                                                                        <label>Title</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="title" required
                                                                                            value="{{ old('title') ?? $category->title }}">
                                                                                        @if ($errors->has('title'))
                                                                                        <span class="help-block">
                                                                                            <strong>{{ $errors->first('title') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <label for="class">Select Company</label>
                                                                                    <select name="company_id" id="company" class="form-control custom-select-value" required>
                                                                                            <option value="">Choose option</option>
                                                                                            @foreach ($companies as $company)
                                                                                            <option value="{{ $company->id }}" {{ $company->id == $category->company_id ? 'selected' : ''}}>
                                                                                                {{$company->title}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @if ($errors->has('company'))
                                                                                        <span class="help-block">
                                                                                            <strong>{{ $errors->first('company') }}</strong>
                                                                                        </span>
                                                                                    @endif
                                                                                     <label for="class">Select Role</label>
                                                                                    <select name="role_id" id="role" class="form-control custom-select-value" required>
                                                                                           
                                                                                        @foreach ($roles as $role)
                                                                                        <option value="{{ $role->id }}" {{ $role->id == $category->role_id ? 'selected' : ''}}>
                                                                                            {{$role->title}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @if ($errors->has('role'))
                                                                                        <span class="help-block">
                                                                                            <strong>{{ $errors->first('role') }}</strong>
                                                                                        </span>
                                                                                    @endif
                                                                                    <label for="class">Make This category default category?</label>
                                                                                    <select name="default" id="default" class="form-control custom-select-value" required>
                                                                                           
                                                                                            <option value="0" {{ $category->makeDefault == '0' ? 'selected' : ''}}>No</option>
                                                                                            <option value="1" {{ $category->makeDefault == '1' ? 'selected' : ''}}>Yes</option>
                                                                                       
                                                                                    </select>
                                                                                    @if ($errors->has('company'))
                                                                                        <span class="help-block">
                                                                                            <strong>{{ $errors->first('company') }}</strong>
                                                                                        </span>
                                                                                    @endif
                                                                                    <div class="login-btn-inner">
                                                                                        <div class="inline-remember-me">
                                                                                            <button
                                                                                                class="btn btn-sm btn-primary submit-button"
                                                                                                type="submit">Update</button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="login-btn-inner" style="margin-top:20px">
                                                                                        <div class="inline-remember-me">
                                                                                            <a style="width:100%" href="{{ route('category.index') }}" class="btn btn-sm btn-info">Back</a>
                                                                                        </div>
                                                                                    </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <!-- Basic Form End-->


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Transitions End-->
@endsection