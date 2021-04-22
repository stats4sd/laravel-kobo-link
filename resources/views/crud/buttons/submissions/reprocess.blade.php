@if ($crud->hasAccess('update'))
    <br/>
	<a href="javascript:void(0)" onclick="reprocessEntry(this)" data-route="{{ url($crud->route.'/'.$entry->getKey().'/reprocess') }}" class="btn btn-sm btn-link" data-button-type="edit"><i class="la la-edit"></i> Reprocess Submission</a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>

	if (typeof reprocessEntry != 'function') {
	    $("[data-button-type=delete]").unbind('click');

	    function reprocessEntry(button) {
		    // ask for confirmation before deleting an item
		    // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = $("#crudTable a[data-route='"+route+"']").closest('tr');

            swal({
                title: "Are you sure?",
                text: "This will delete every Db entry created from this submission, then attempt to reprocess the submission using the existing data maps",
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
                        text: "Yes - Proceed",
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
                                text: "Process complete"
                            }).show();

                            $("#crudTable").DataTable().ajax.reload();
                        },
                        error: function(result) {
                            // Show an alert with the result
                            swal({
                                title: "Error",
                                text: "Something went wrong - please try again or contact the site admin",
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
