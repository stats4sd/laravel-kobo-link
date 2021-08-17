<?php

Route::group([
    'prefix' => config('backpack.base.route_previx', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin'),
    ),
    'namespace' => 'Stats4sd\KoboLink\Http\Controllers\Admin',
], function () {
    Route::crud('xlsform', 'XlsformCrudController');
//    Route::crud('submission', 'SubmissionCrudController');

    // XLS Form <--> KoBoToolbox handling
    Route::post('xlsform/{xlsform}/deploytokobo', 'XlsformCrudController@deployToKobo');
    Route::post('xlsform/{xlsform}/syncdata', 'XlsformCrudController@syncData');
    Route::post('xlsform/{xlsform}/archive', 'XlsformCrudController@archiveOnKobo');
    Route::post('xlsform/{xlsform}/csvgenerate', 'XlsformCrudController@regenerateCsvFileAttachments');
    Route::get('xlsform/{xlsform}/downloadsubmissions', 'XlsformCrudController@downloadSubmissions')->name('xlsforms.submissions');
});
