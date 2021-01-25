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
                                <div class="col-lg-9">
                                    <h1>All Categories</h1><br>
                                </div>
                                <div>
                                   <a href ="{{route('category.create')}}" class="btn btn-primary"><i class="fa fa-plus"> Add new</i></a>
                                    <a href = "{{ url('categories/import-export') }}" class="btn btn-custon-four btn-success"><i class="fa fa-upload"></i> Import</a>
                                </div>
                            </div>
                        </div>
                        <div class="sparkline7-graph">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div
                                        class="datatable-dashv1-list custom-datatable-overright dashtwo-project-list-data">
                                        @include('includes.alerts')
                                        <div id="toolbar">
                                            <select class="form-control">
                                                <option value="all">Export All</option>
                                            </select>
																				</div>
																				<div class="form-row">
																					<div class="form-group col-md-3">

																						<select class="form-control select2-options mass-delete">
																								<option selected>Bulk Action</option>
																								<option value="delete">Delete</option>
																						</select>
																					</div>
																						<div class="form-group col-md-2">
																								<button type="submit" class="mass-delete-action btn btn-default btn-custom-four btn-block" style="height: 100%" disabled>Apply</button>
																						</div>
																					<div class="form-group col-md-9">
																					</div>
																			</div>
                                        <table id="table" data-toggle="table" data-pagination="true" data-search="true"
                                            data-show-columns="true" data-resizable="true"
                                            data-page-size="500" data-page-list="[500, 1000, 1500, 2000]"
                                            data-cookie-id-table="saveId" data-show-export="true">
                                            <thead>
                                                <tr>
																									<th scope="col" class="">
																										<input type="checkbox" class="form-check-input float-left position-relative mass-delete-group" style="margin: 0"></th>
                                                    <th data-field="id">S/No</th>
                                                     <th>Title</th>
                                                    <th>Company</th>
                                                    <th>Role</th>
                                                   
                                                    <th>Default</th>
                                                    <th>Questions</th>
                                                    
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categories as $category)
                                                <tr>
																									<td>
                                                    <form action="{{ route('category.destroy', $category->id) }}"
                                                        class="hidden" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        </form>
                                                        <input type="checkbox" class="form-check-input mass-delete-item" style="margin: 0">
                                                    </td>
                                                    <td>{{$i++}}</td>
                                                     <td> <strong>{{$category->title}}</strong></td>
                                                    <td>{{$category->company->title}}</td>
                                                    <td>{{ isset($category->role->title) ? $category->role->title : '' }}</td>
                                                  
                                                   
                                                    <td>{{$category->makeDefault == '0' ? 'No' : 'Yes' }}</td>
                                                    <td>{{ $category->questions->count() }}</td>
                                                      <td>
                                                        <div class="button-style-four btn-mg-b-10">
                                                            <form action="{{ route('category.destroy', $category->id) }}"
                                                                class="btn btn-custon-four btn-bg-cl-social" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete forever?');">
                                                                {{ csrf_field() }}
                                                                {{method_field('DELETE')}}
            
                                                                <button type="submit"
                                                                    class="btn btn-custon-four btn-bg-cl-social btn-danger"
                                                                    data-toggle="tooltip" title="Delete category"> <i
                                                                        class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                            <a data-toggle="tooltip" data-placement="top" title="Edit category"
                                                                class="btn btn-custon-four btn-bg-cl-social btn btn-info"
                                                                href="{{ route('category.edit', $category->id)}}"><i class="fa fa-edit"></i>
                                                            </a>
                                                        </div>  
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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