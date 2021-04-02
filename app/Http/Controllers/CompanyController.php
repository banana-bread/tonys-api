<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends ApiController
{
    public function index()
    {
        // TODO: figure out pagination
    }

    /*
     TODO: 
        - (done) create CreateCompanyRequest form request class
        - (done) tests for creating and getting company
        - updates on companies and resulting actions *Some of these actions may be in different controllers.  
    */
    public function store(CreateCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->all());

        /* TODO: were going to need to create a company_schedules table which gets created from
                 operating hours that get passed in when creating company.

                 - create schedules based on operating hours
                 - create a field on employee and company tables called other_settings. make it a json and store 
                        'base_schedule': {
                            'monday': {
                                'start_time': time,
                                'end_time': time,
                            },
                            'tuesday': {
                                'start_time': time,
                                'end_time': time,
                            }
                            etc
                        },
                        'exceptions': 
                 - then on employees and companies tables add foreign base_schedule_id
        */


        return $this->created(['company' => $company], 'Company created.');
    }

    public function show(string $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        return $this->ok(['company' => $company], 'Company retrieved.');
    }

    // TODO: this will need to be a cascading delete or soft delete?
    // public function destroy(string $id): JsonResponse
    // {
    //     $service = new BookingService();
    //     $service->cancel($id);

    //     return $this->deleted('Booking cancelled.');
    // }
}
