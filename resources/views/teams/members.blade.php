<div class="row pt-4 pl-4">
    <div class="card col-12 col-xl-10">
        <div class="card-header">
        <h2>Team Members</h2>
        </div>

        <div class="card-body">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Avatar</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Kobotoolbox Account</th>
                        <th scope="col">Access Type</th>
                        @can('update', $team)
                            <th scope="col">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                        @foreach($team->users as $user)
                        <tr>
                            <td>
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }} avatar" height="50px">

                            </td>
                            <td>

                                    {{ $user->name }}

                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->kobo_id }}</td>
                            <td>{{  $user->pivot->is_admin ? 'Admin' : 'Member' }}</td>
                            @can('update', $team)
                                <td>
                                    <a href="{{ route('teammembers.edit', [$team, $user]) }}" class="btn btn-dark btn-sm" name="edit_member{{ $user->id }}" onclick="">EDIT</a>
                                    <button class="btn btn-dark btn-sm remove-button" data-user="{{ $user->id }}" data-toggle="modal" data-target="#removeUserModal{{ $user->id }}">REMOVE</button>
                                </td>
                            @endcan
                        </tr>
                        @endforeach

                </tbody>
            </table>
            <hr/>
            <h4>Pending Invites</h4>
            <ul class="list-group">
                @foreach($team->invites as $invite)
                    <li class="list-group-item list-group-flush d-flex">
                        <div class="w-50">{{ $invite->email }}</div>
                        <div class="w-25">Invited on {{ $invite->invite_day }}</div>
                    </li>
                @endforeach
            </ul>
            @can('update', $team)
                <a class="btn btn-dark btn-sm mt-5" href="{{ route('teammembers.create', $team) }}">INVITE MEMBERS</a>
            @endcan
        </div>
    </div>
</div>

@push('after_scripts')
@foreach($team->users as $user)
<div class="modal fade" id="removeUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="removeUserModalLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="removeUserModalLabel{{ $user->id }}">Remove {{ $user->email }} from {{ $team->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you wish to remove {{ $user->name }} from {{ $team->name }} After removing, they will no longer have access to any team data or forms on Kobotoolbox.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <form action="{{ route('teammembers.destroy', [$team, $user]) }}" method="POST">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-primary">Confirm Remove</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endforeach
@endpush