@extends('backpack::layouts.top_left')

@section('content')

<div class=" row container">

    @include('kobo::teams.header')

    <ul class="nav nav-tabs" id="team-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="#team-members" data-target="#members" class="nav-link team-tab" role="tab" data-toggle="tab" id="members-tab" aria-controls="home" aria-selected="false">Team Members</a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#team-forms" data-target="#forms" class="nav-link team-tab" role="tab" data-toggle="tab" id="forms-tab" aria-controls="home" aria-selected="false">ODK Surveys</a>
        </li>
    </ul>
    <div class="tab-content w-100" id="team-tabs-content">
        <div class="tab-pane fade  w-100" id="members" role="tabpanel" aria-labeledby="members-tab">
            @include('kobo::teams.members')
        </div>
        <div class="tab-pane fade  w-100" id="forms" role="tabpanel" aria-labeledby="forms-tab">
            @include('kobo::teams.forms')
        </div>
    </div>



</div>
@endsection

@section('after_scripts')
<script src="{{ mix('js/app.js') }}"></script>

<script>
    $(document).ready(() => {

        let url = location.href.replace(/\/$/, "");

        if (location.hash) {
            const hash = url.split("#");
            $('#team-tabs a[href="#' + hash[1] + '"]').tab("show");
            url = location.href.replace(/\/#/, "#");
            history.replaceState(null, null, url);
            setTimeout(() => {
                $(window).scrollTop(0);
            }, 400);
        } else {
            $('#team-tabs a[href="#team-members"]').tab("show");
            url = location.href.replace(/\/#/, "#");
            history.replaceState(null, null, url);
            setTimeout(() => {
                $(window).scrollTop(0);
            }, 400);
        }

        $('a.team-tab').on("click", function() {
            console.log('what');
            let newUrl;
            const hash = $(this).attr("href");
            if (hash == "#forms") {
                newUrl = url.split("#")[0];
            } else {
                newUrl = url.split("#")[0] + hash;
            }
            newUrl += "/";
            history.replaceState(null, null, newUrl);
        });
    });
</script>
@endsection