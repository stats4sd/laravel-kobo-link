# Manage KoboToolBox from your Laravel Project

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/run-tests?label=tests)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/stats4sd/laravel-kobo-link/Check%20&%20fix%20styling?label=code%20style)](https://github.com/stats4sd/laravel-kobo-link/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/stats4sd/laravel-kobo-link.svg?style=flat-square)](https://packagist.org/packages/stats4sd/laravel-kobo-link)

This package turns your Laravel / Laravel Backpack platform into a management system for data collected through [KoboToolBox](https://kf.kobotoolbox.org). It is designed to support research or survey data collection, and provides features beyond the scope of what KoboToolBox or other ODK Services can provide on their own. 

## Who is it for? 
Platforms built with this package can help with the following scenarios:

1. Multiple teams need to use the same set of ODK forms provided by a central team, but need to retain ownership of their data (i.e. the data of all teams cannot simply be pooled together and made accessible to everyone) 
2. The data collection is complex, where data from some forms needs to be processed and then shared back to other forms as customised CSV files. 
   1. Including the possibility that each team requires different data to be made available in their ODK forms.

> ### IMPORTANT NOTE
> This is not an off-the-shelf data management solution! It requires you to build your own Laravel platform and pull in this package via composer. It does not handle the processing of the data collected through your ODK forms, but it provides hooks to enable you to write your own processing scripts to run automatically when ODK submissions are pulled in from KoboToolBox. You can provide your own database structures / data models to organise the processed data however you see fit.

## Installation

You can install the package via composer:

```bash
composer require stats4sd/laravel-kobo-link
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="kobo-link-migrations"
php artisan migrate
```

### Setup Required Configuration Variables

In order to link up to a KoBoToolbox server, you must provide the following environment variables:

```
KOBO_ENDPOINT=
KOBO_OLD_ENDPOINT=
KOBO_USERNAME=
KOBO_PASSWORD=
DATA_PROCESSING_CLASS=
```

The two endpoint variables should be the full url to the server you are using. For example:
```
## If you use the 'for everyone else' server provided by the team at https://kobotoolbox.org:
KOBO_ENDPOINT=https://kf.kobotoolbox.org,
KOBO_OLD_ENDPOINT=https://kc.kobotoolbox.org

## If you use their humanitarian server, use:
KOBO_ENDPOINT=https://kobo.humanitarianresponse.info
KOBO_OLD_ENDPOINT=https://kc.humanitarianresponse.info
```

The platform requires a 'primary' user account on the KoboToolbox server to manage deployments of ODK forms. This account will *own* every form published by the platform. We **highly** recommend creating an account specifically for the Laravel platform. If the platform uses an account also used by others, there is a chance that your database will become out of sync with the forms present on KoBoToolbox, and the form management functions may stop working correctly.

## Setup Data Models

This packages assumes that the following models exist in the platform:
- `\App\Models\User`

The package provides the following models:

| Feature / Purpose| Model | Database Table | Comments
| --- | --- | --- | ---
| Team Management | Team | teams | If the platform will be used by a single group who share data (e.g. one research project, or a single survey), you can simply create 1 team.   
| Team Management | Invite | invites | This package includes a system of inviting users to specific teams. This feature will hopefully be seperated into a separate package in the future, as it is not a core part of the ODK / KoboToolbox system.  
| ODK Form Management | Xlsform | xlsforms | This stores every ODK form present in the platform. Forms can be either deployed to *every* team, or to a single team, based on needs. This allows most teams to use a 'common' form, but gives you the option to add a custom version of a form for a specific team if required.
| ODK Form Management | TeamXlsform | team_xlsform | The link table for ODK forms and teams. This table will contain 1 record for every unique form that gets deployed to KoboToolbox.  
| Submission Processing | Submission | submissions | Every submission that is pulled from KoboToolbox is stored in this table. The main contents is stored as a JSON field, so the table structure is the same regardless of the form's structure. You can then write the processing scripts to 'unpack' this JSON into whatever formats you require.
| Submission Processing | Datamap | datamaps | Datamaps handle the link to the actual processing scripts. Each ODK form can be linked to 1 or more data maps, and submissions will be processed using *all* linked data maps.

TODO: add section explaining how the data maps work, and include real examples. 


### Publishing The config

If you add the required ENV variables to your application, there should be no need to publish the config file. 

However, you may wish to do so anyway. To publish the file, use:

```bash
php artisan vendor:publish --provider="Stats4sd\KoboLink\KoboLinkServiceProvider" --tag="kobo-link-config"
```


## Add the Front-end
This package assumes you are using Laravel Backpack as your admin panel. As such, it comes with a set of CrudControllers for managing your XLS forms and submissions. It also assumes that you are able to build your own front-end to allow team members to access their data, manage forms etc. 

You can add links to these crud panels into your sidebar file located at resourcs\views\vendor\backpack\base\inc\sidebar_content.blade.php:

TODO: Add example team UI for team members to manage their own forms, submissions and team members/invites.

```
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('team') }}'><i class="lab la-wpforms nav-icon"></i> Teams</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('xlsform') }}'><i class="lab la-wpforms nav-icon"></i> XLSForms</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('submission') }}'><i class="lab la-wpforms nav-icon"></i> Submissions</a></li>

```


## Writing Data Processing Scripts
The Datamap model includes a `process(Submission $submission)` method. This hooks into a Datamap Service class, which is designed to be overwritten by you. Each platform will require a different set of data processing scripts tailored to the data being collected, so we have tried to make it easy to include these scripts in your platform. Here is the short version:

1. Create a "DatamapService" class, and add the fully qualified path to this class into your .env file. E.g.: `DATA_PROCESSING_CLASS="\App\Services\DatamapService::class"`
2. Write the methods you want to use to process a submisison. The method should accept a single Submission parameter, and can then do anything you want to 'process' the submission. e.g.: 
```php
    public function testForm(Stats4sd\KoboLink\Models\Submission $submission)
    {
        /* PROCESS SUBMISSION DATA */
        
        /* get the submission contents */ 
        $data = $submission->content;

        // the Datamap model includes a helper function to remove the lengthy group names from the submission:        
        $data = $this->removeGroupNames($data);
        
        /** Now $data is a set of key-value pairs that can be processed however you need, including :
         * - creating new database entries via Eloquent, 
         * - manual SQL querying, 
         * - passing the submission to an external process like R or Python running on the server.
         *
         * Repeat groups need to be handled manually - they will be left with the 'value' as a nested json array.  
        **/    
        
        /* At the end, you should update the $submission entry: */
        $submission->processed = 1;
        
        /* If your processing throws errors, e.g. validation errors, you can add those to the "errors" array: */ 
        $submission->errors = [
            'variable_name' => 'Error message',
            'variable_2' => 'Error message',
        ];
        
        /** If your processing has created new Eloquent models, you can add those to the "entries" array.
         * - This allows you to easily identify what records each submission created;
         * - It is used in the 'reprocessSubmissions()' method to delete previously created entries and avoid duplication. 
         **/
          
        // example, if your submission created 1 Household entry and 2 HouseholdMember entries:
        $submission->entries = [
            "App\Models\Household" => [$household->id],
            "App\Models\HouseholdMember" => [$memberOne->id, $memberTwo->id], 
       ];

        $submission->save();       
    }
```

TODO: include real examples :)


3. Now, you should create a Datamap entry with an ID of the method name. For the example above, you would add the following record to the `datamaps` table:
    - `INSERT INTO datamaps SET id = "testForm", title = "Test Form Processing";`

It is vital to match the datamap ID to the method name, as this is how the datamap chooses which method to run during processing. 


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dave Mills](https://github.com/stats4sd)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
