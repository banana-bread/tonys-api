<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateBlockedTimeRequest;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;

class EmployeeBlockedTimeController extends ApiController
{

    public function store(EmployeeCreateBlockedTimeRequest $request): JsonResponse
    {

        logger(request());

        return $this->ok(null);
    }
    public function index(string $companyId)
    {
    }

    // public function show(string $companyId, string $id): JsonResponse
    // {
    // }

    public function destroy(string $id): JsonResponse
    {
        return $this->ok(null);
    }

    // private function _authedUserIsBookingsClient(Booking $booking): bool
    // {
    //     return auth()->user()->isClient() && 
    //            auth()->user()->client->id === $booking->client_id;
    // }

    // private function _authedUserBelongsToBookingsCompany(Booking $booking): bool
    // {
    //     return auth()->user()->isEmployee() && 
    //            auth()->user()->employee->company_id === $booking->employee->company_id;
    // }
}
