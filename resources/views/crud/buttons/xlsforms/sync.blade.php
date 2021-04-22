@if ($crud->hasAccess('update'))
	<a href="javascript:void(0)" onclick="syncData(this)" data-route="{{ url($crud->route.'/'.$entry->getKey().'/syncdata') }}" class="btn btn-sm btn-success" data-button-type="sync"><i class="la la-trash"></i> Get Data from Kobo</a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>

	if (typeof syncData != 'function') {
	    $("[data-button-type=sync]").unbind('click');

	    function syncData(button) {
		    // ask for confirmation before deleting an item
		    // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = $("#crudTable a[data-route='"+route+"']").closest('tr');

            $.ajax({
                url: route,
                type: 'POST',
                success: function(result) {
                    console.log(result);
                    new Noty({
                        type: "info",
                        text: "Sync Data Successful"
                    }).show();
                },
                error: function(result) {
                    // Show an alert with the result
                    swal({
                        title: "Error",
                        text: "Something went wrong while communicating with Kobotoolbox - please try again or contact the site admin",
                        icon: "error",
                        timer: 4000,
                        buttons: false,
                    });
                }
            });
		}
    }

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif
