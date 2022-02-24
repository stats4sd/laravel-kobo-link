@extends('backpack::layouts.top_left')
@section('header')
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@include('teams.header')

<div class="card">
    <div class="card-header">
        Edit Access to Team $team->name
    </div>
    <div class="card-body">
    <form method="POST" action="{{ route('teammembers.update', [$team, $user])}}">
            @csrf
            @method('put')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group row">
                <label class="col-md-6 col-form-label text-md-right">
                    User Name
                </label>
                <p class="col-md-6 col-form-label">{{ $user->name }}</p>
            </div>
            <div class="form-group row">
                <label class="col-md-6 col-form-label text-md-right">
                    User Email
                </label>
                <p class="col-md-6 col-form-label">{{ $user->email }}</p>
            </div>
            <div class="form-group row required">
                <label for="select-users" class="col-md-6 col-form-label text-md-right">
                    Assign access level for team $team->name
                </label>
                <div class="col-md-6">
                    <select
                        id="access-level"
                        name="is_admin"
                        class="select2 form-control @error('name') is-invalid @enderror"
                        value="{{ $user->pivot->is_admin }}"
                        >
                            <option value="0" {{ !$user->pivot->is_admin ? 'selected' : '' }}>Team Member</option>
                            <option value="1" {{ $user->pivot->is_admin ? 'selected' : '' }}>Team Administrator</option>

                    </select>
                    @error('users')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group row mb-0">
                <div class="col-md-10 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection