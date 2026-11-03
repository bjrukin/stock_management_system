@extends('layout.admin')
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="form-section">
                    <form enctype="multipart/form-data" method="post" action="{{ $action }}" id="form">
                        @csrf
                        <input type="hidden" class="form-control" name="id" value="{{ ($row) ? $row->id : 0 }}">
                        <div class="card">
                            <div class="card-body">
                                <fieldset class="border rounded-3 p-3">
                                    <legend>{{$page_title}}</legend>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select class="form-control select2" name="role">
                                            @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @if($row && $row->role_id == $role->id)
                                                selected
                                                @endif>{{  $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="table-responsive clear">
                                            <table class="table table-striped table-borderless mb-0">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="50">SN.</th>
                                                        <th>Module</th>
                                                        @foreach($permissions as $permission)
                                                        <th> {{ ucfirst($permission) }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($modules as $module)
                                                    <tr>
                                                        <td>{{ $count++ }}</td>
                                                        <td><input type="hidden" name="module[]"
                                                                value="{{ $module->id }}">{{ $module->name }}</td>
                                                        @foreach($permissions as $permission)
                                                        <td> <label class="switch">
                                                                <input type="checkbox" class="switch-input"
                                                                    name="{{ $permission }}[]" value="{{ $permission }}"
                                                                    {{ ($row && $row->hasPermission($row->role_id, $module->id, $permission)) ? 'checked' : '' }} />
                                                                <span class="switch-label" data-on="Yes"
                                                                    data-off="No"></span>
                                                                <span class="switch-handle"></span> </label></td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="mt-3"> <button type="button" class="main_btn btn-loading mb-3"
                                        onclick="saveData(this.form)">Submit</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('footer-scripts')
@include('admin.permission.js.form')
@endpush