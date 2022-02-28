<div class="py-4 pl-4">
    <div class="card">
        <div class="card-header">
            <h2>ODK Survey</h2>
        </div>
        <div class="card-body d-flex">
    </div>

    <ul class="nav nav-tabs mt-4" id="survey-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <div data-target="#survey-forms" class="nav-link active text-dark" role="tab" data-toggle="tab" id="survey-forms-tab" aria-controls="survey-forms" aria-selected="true">ODK Form(s)</div>
        </li>
    </ul>
    <div class="tab-content">
        <div class="card tab-pane fade active show" role="tabpanel" id="survey-forms">
            <div class="card-body">
                <div id="app">
                    <team-forms-table :team="{{ $team->toJson() }}" :user-id="{{ auth()->id() }}"></team-forms-table>
                </div>
            </div>
        </div>

    </div>

</div>
