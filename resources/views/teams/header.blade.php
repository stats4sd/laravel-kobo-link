<div class="row pl-4 pt-4 w-100">
<div class="col-12 col-xl-12 card">
<div class="card-header d-flex align-items-flex-end justify-content-between">
    <div>
        <h1><b>Viability Case Study: {{$team->name}}</b></h1>
    </div>
    @can('edit', $team)
        <div>
            <a href="{{ route('team.edit', $team->id) }}" class="btn btn-primary">Edit Team</a>
        </div>
    @endcan
</div>
<div class="card-body">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 col-lg-4">
            <div class="rect-image">
                <img src="{{ $team->avatar_url }}" width="80%" height="auto">
            </div>
        </div>
        <div class="col-sm-6 col-lg-5">
            <br>
            <div id="description">
                <p>{{ $team->description }}</p>
            </div>
        </div>
        <div class="col-sm-12 col-lg-3 border border-right-0 border-bottom-0 border-top-0 mt-sm-4">
            <div class="admin_group">
                <h3 class="pb-2"><b>Admins</b></h3>
                @foreach ($team->admins as $admin)
                <a href="#" data-toggle="tooltip" title="{{ $admin->username }}" class="d-flex flex-row justify-content-start align-items-center">
                    <img src="{{ $admin->avatar_url }}" width="50px">
                    <h5 class="pl-3">{{ $admin->name }}</h5>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>