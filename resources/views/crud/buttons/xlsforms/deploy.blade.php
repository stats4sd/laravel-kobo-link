@if ($crud->hasAccess('update'))
	<a href="javascript:void(0)" onclick="deployEntry(this)" data-route="{{ url($crud->route.'/'.$entry->getKey().'/deploytokobo') }}" class="btn btn-sm btn-info" data-button-type="delete"><i class="la la-trash"></i> @if($entry->kobo_id) (Re) @endif Deploy to Kobo</a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>

	if (typeof deployEntry != 'function') {
	    $("[data-button-type=delete]").unbind('click');

	    function deployEntry(button) {
		    // ask for confirmation before deleting an item
		    // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = $("#crudTable a[data-route='"+route+"']").closest('tr');

            swal({
                title: "Are you sure?",
                text: "This will deploy the current version of the XLS File to Kobotools. If the form is marked as live, it will be shared with all users. Otherwise it will be shared with all admin / testers.",
                icon: "info",
                buttons: {
                    cancel: {
                        text: "{!! trans('backpack::crud.cancel') !!}",
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
	        		},
    		      	delete: {
                        text: "Yes - Deploy form to Kobotoolbox",
                        value: true,
                        visible: true,
                        className: "bg-success",
	        		}
		        },
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url: route,
                        type: 'POST',
                        success: function(result) {
                            console.log(result);
                            new Noty({
                                type: "info",
                                text: "Deployment started"
                            }).show();
                        },
                        error: function(result) {
                            // Show an alert with the result
                            swal({
                                title: "Error",
                                text: "Something went wrong with deployment - please try again or contact the site admin",
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        }
                    });
                }
            });
		}
    }

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif
