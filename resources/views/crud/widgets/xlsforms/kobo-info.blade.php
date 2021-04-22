<div class="row">
    <div class="col-md-6">
    <ul class="list-group">
        <li class="list-group-item d-flex p-0">
            <div class="w-50 m-0 p-3">Test Form on Kobo?</div>
            <div class="w-50 m-0 p-3 {{ $widget['form']->kobo_id ? 'bg-success' : 'bg-secondary' }}">
                <b id="kobo_url">
                    @if($widget['form']->kobo_id)
                        <a target="_blank" href="{{ config('services.kobo.endpoint') }}/#/forms/{{ $widget['form']->kobo_id }}/" class="btn btn-link text-white font-weight-bold text-center">Yes - View on Kobo</a>
                    @else
                        No
                    @endif
                </b>
            </div>
        </li>
        <li class="list-group-item d-flex p-0">
            <div class="w-50 m-0 p-3">Test Active on Kobo?</div>
            <div class="w-50 m-0 p-3 {{ $widget['form']->is_active ? 'bg-success' : 'bg-secondary' }}">
                <b id="enketo_url">
                    @if($widget['form']->is_active)
                        <a target="_blank" href="{{ $widget['form']->enketo_url }}/" class="btn btn-link text-white font-weight-bold text-center">Yes - Show Webform</a>
                    @else
                        No
                    @endif
                </b>
            </div>
        </li>
        <li class="list-group-item d-flex p-0">
            <div class="w-50 m-0 p-3">No. of Submissions:</div>
            <div class="w-50 m-0 p-3 d-flex">
                <b>{{ $widget['form']->submissions->count() }}</b>
            </div>
        </li>
    </ul>
    </div>
</div>

<script src="{{ asset('js/echo.js') }}"></script>

@if (auth() -> check())
<script>
    Echo.private("App.Models.User.{{ auth()->user()->id }}")

        // DEPLOYMENT MESSAGES
        .listen('KoboDeploymentReturnedSuccess', (e) => {
            new Noty({
                type: "success",
                text: "<b>Form: " + e.form.title + "</b><br/><br/>The form has been successfully uploaded and deployed to Kobotools.",
                timeout: false
            }).show();

            console.log(e);

            $('#kobo_url')
                .html('<a target="_blank" href="{{ config('services.kobo.endpoint') }}/#/forms/' + e.form.kobo_id + '/" class="btn btn-link text-white font-weight-bold text-center">Yes - View on Kobo</a>');

            $('#kobo_url').parent().class('w-50 m-0 p-3 bg-success');

            $('#enketo_url')
                .html('<a target="_blank" href=' + e.form.enketo_url + '"/" class="btn btn-link text-white font-weight-bold text-center">Yes - View on Kobo</a>')

            $('#enketo_url').parent().class('w-50 m-0 p-3 bg-success');
        })
        .listen('KoboDeploymentReturnedError', (e) => {
            new Noty({
                type: "error",

                timeout: false
            }).show();

            console.log(e);

            $('#kobo_url')
                .html('No')
            $('#kobo_url').parent().class('w-50 m-0 p-3 bg-secondary');

            $('#enketo_url')
                .html('No')
            $('#enketo_url').parent().class('w-50 m-0 p-3 bg-secondary');
        })

        // UPLOAD MESSAGES
        .listen('KoboUploadReturnedSuccess', (e) => {
            new Noty({
                type: "info",
                text: "<b>Form: " + e.form.title + "</b><br/><br/>The form has been successfully uploaded to Kobotools. It will now be deployed and shared with the users of the platform",
                timeout: false
            }).show();

            console.log(e);
        })

        .listen('KoboUploadReturnedError', (e) => {
            new Noty({
                type: "error",
                text: "<b>Form: " + e.form.title + "</b><br/><br/> The form could not be deployed to Kobotools. An error was returned<hr/>Error Type: <b>" + e.errorType + "</b><hr/>Error Message: <b>" + e.errorMessage + "</b><br/><br/>This error may indicate errors in the XLSX form.",
                timeout: false
            }).show();


            console.log(e);
        })
        .listen('KoboGetDataReturnedError', (e) => {
            new Noty({
                type: "error",
                text: "<b>Form: " + e.form.title + "</b><br/><br/> Submissions could not be retrieves from the form. An error was returned<hr/>Error Type: <b>" + e.errorType + "</b><hr/>Error Message: <b>" + e.errorMessage + "</b> }}",
                timeout: false
            }).show();


            console.log(e);
        })

        // ARCHIVE MESSAGES
        .listen('KoboArchiveRequestReturnedSuccess', (e) => {
            new Noty({
                type: "success",
                text: "<b>Form: " + e.form.title + "</b><br/><br/>The form has been successfully archived on Kobotools, and is no longer available for data collection",
                timeout: false
            }).show();

            console.log(e);

            $('#enketo_url')
                .html('No')
            $('#enketo_url').parent().class('w-50 m-0 p-3 bg-secondary');
        })

        .listen('KoboArchiveRequestReturnedError', (e) => {
            new Noty({
                type: "error",
                text: "<b>Form: " + e.form.title + "</b><br/><br/> The form could not be archived. An error was returned<hr/>Error Type: <b>" + e.errorType + "</b><hr/>Error Message: <b>" + e.errorMessage + "</b>",
                timeout: false
            }).show();

            console.log(e);
        })
</script>
@endif
