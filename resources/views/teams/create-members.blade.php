@extends('backpack::layouts.top_left')
@section('header')
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@include('kobo::teams.header')

<div class="card">
    <div class="card-header">
        <b>Add new members to {{ $team->name }}</b>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('teammembers.store', $team)}}">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group row required">
                <label for="select-users" class="col-md-4 col-form-label text-md-right">
                    Add existing platform users to the team
                </label>
                <div class="col-md-8">
                    <select
                        data-placeholder="Select users to invite"
                        multiple
                        id="select-users"
                        name="users[]"
                        class="select2 form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        >
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('users')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <hr/>
            <h5>Invite New Users</h5>
            <p>If you cannot find the users on the platform, you can send them an email invitation to join the platform (and this team).</p>
            <div class="form-group row required">
                <label for="description" class="col-md-4 col-form-label text-md-right">
                    Enter the email addresses to send invites to. You can add as many email addresses as you need.
                </label>
                <div class="col-md-8">
                    <div id="repeatingEmailFields">
                        <div class="entry input-group">
                            <input class="form-control" name="emails[]" type="email" placeholder="Email address" />
                            <span class="input-group-append">
                                <button type="button" class="btn btn-success btn-add">
                                    <span class="las la-plus" aria-hidden="true"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                    <br>
                    <small>Press <span class='las la-plus gs'></span>  for another field</small>
                    @error('emails')
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

@push('after_scripts')

    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    @if (app()->getLocale() !== 'en')
        <script src="{{ asset('packages/select2/dist/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    @endif


    <script>
        //shamelessly copied from https://nddt-webdevelopment.de/bootstrap/repeater-field-bootstrap
        $(function()
        {
            $(document).on('click', '.btn-add', function(e)
            {
                e.preventDefault();
                var controlForm = $('#repeatingEmailFields:first'),
                    currentEntry = $(this).parents('.entry:first'),
                    newEntry = $(currentEntry.clone()).appendTo(controlForm);
                newEntry.find('input').val('');
                controlForm.find('.entry:not(:last) .btn-add')
                    .removeClass('btn-add').addClass('btn-remove')
                    .removeClass('btn-success').addClass('btn-danger')
                    .html('<span class="las la-minus"></span>');
            }).on('click', '.btn-remove', function(e)
            {
                e.preventDefault();
                $(this).parents('.entry:first').remove();
                return false;
            });
        });

        $(document).ready(function() {
            $('.select2').select2();
        });

    </script>
@endpush