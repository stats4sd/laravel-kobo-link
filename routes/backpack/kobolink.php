<?php

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin'),
    ),
    'namespace' => 'Stats4sd\KoboLink\Http\Controllers\Admin',
], function () {
    Route::crud('xlsform', 'XlsformCrudController');
    Route::crud('submission', 'SubmissionCrudController');
    Route::crud('teamxlsform', 'TeamXlsformCrudController');

    // XLS Form <--> KoBoToolbox handling
    Route::post('teamxlsform/{form}/deploytokobo', 'TeamXlsformCrudController@deployToKobo');
    Route::post('teamxlsform/{form}/syncdata', 'TeamXlsformCrudController@syncData');
    Route::post('teamxlsform/{form}/archive', 'TeamXlsformCrudController@archiveOnKobo');
    Route::post('teamxlsform/{form}/csvgenerate', 'TeamXlsformCrudController@regenerateCsvFileAttachments');
    Route::get('teamxlsform/{form}/downloadsubmissions', 'TeamXlsformCrudController@downloadSubmissions')->name('team_xlsforms.submissions');
});
