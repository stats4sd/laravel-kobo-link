<?php

use Stats4sd\KoboLink\Http\Controllers\Admin\DataMapCrudController;
use Stats4sd\KoboLink\Http\Controllers\Admin\InviteCrudController;
use Stats4sd\KoboLink\Http\Controllers\Admin\SubmissionCrudController;
use Stats4sd\KoboLink\Http\Controllers\Admin\TeamCrudController;
use Stats4sd\KoboLink\Http\Controllers\Admin\TeamXlsformCrudController;
use Stats4sd\KoboLink\Http\Controllers\Admin\XlsformCrudController;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin'),
    ),
], function () {

    // Admin module CRUD controllers
    Route::crud('invite', InviteCrudController::class);
    Route::crud('xlsform', XlsformCrudController::class);
    Route::crud('teamxlsform', TeamXlsformCrudController::class);
    Route::crud('submission', SubmissionCrudController::class);
    Route::crud('datamap', DataMapCrudController::class);
    Route::crud('team', TeamCrudController::class);
    
    // XLS Form <--> KoBoToolbox handling
    Route::post('teamxlsform/{form}/deploytokobo', [TeamXlsformCrudController::class, 'deployToKobo']);
    Route::post('teamxlsform/{form}/syncdata', [TeamXlsformCrudController::class, 'syncData']);
    Route::post('teamxlsform/{form}/archive', [TeamXlsformCrudController::class, 'archiveOnKobo']);
    Route::post('teamxlsform/{form}/csvgenerate', [TeamXlsformCrudController::class, 'regenerateCsvFileAttachments']);
    Route::get('teamxlsform/{form}/downloadsubmissions', [TeamXlsformCrudController::class, 'downloadSubmissions'])->name('team_xlsforms.submissions');
    
    Route::get('team/{team}/xlsforms', [TeamCrudController::class, 'getForms']);

    Route::post('submission/{submission}/reprocess', [SubmissionCrudController::class, 'reprocessSubmission']);
});


