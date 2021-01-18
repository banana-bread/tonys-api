<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\LoginService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestUtils;

// TODO: not working.  Figure out how to test getting tokens from different grants
class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test */
    // public function a_user_can_request_a_token_with_the_password_grant()
    // {
    // }

    // /** @test */
    // public function a_user_can_request_a_token_with_the_social_grant_and_google_provider()
    // {
    //     $password = $this->faker->password(7);

    //     $user = User::factory()->create([
    //         'email' => $this->faker->email,
    //         'password' => Hash::make($password)
    //     ]);

    //     $request = new Request();
    //     $request->setMethod('POST');
    //     $request->headers->set('Accept', '*/*');
    //     $request->headers->set('Host', 'localhost:89');

    //     $request->headers->set('Content-Type', 'multipart/form-data');
    //     $request->request->add([
    //         'username' => $user->email,
    //         'password' => $password,
    //         'client_id' => env('PASSPORT_CLIENT_ID'),
    //         'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    //         'grant_type' => 'password'            
    //     ]);
    //     // \Log::info($request);

    //     $response = TestUtils::callMethod(
    //         new LoginService(),
    //         'requestToken',
    //         [$request]
    //     );

    //     \Log::info($response);

    // }
}