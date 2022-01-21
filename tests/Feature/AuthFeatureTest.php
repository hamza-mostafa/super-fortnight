<?php

namespace Tests\Feature;

//use App\Http\Controllers\JWTController;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    public function test_register_then_Login()
    {
        $password =  $this->faker->password(6, 20);
        $email = $this->faker->email;
        $registerPayload = [
            'password'  => $password,
            'email'      => $email,
            'name' => $this->faker->firstName,
            'password_confirmation'  => $password
        ];
        $payload = [
            'password'  => $password,
            'email'      => $email
        ];
        $this->json('post', 'api/register', $registerPayload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'message',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            );
        $this->json('post', 'api/login', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                        'access_token',
                        'token_type',
                        'expires_in'
                    ]
                ]
            );
    }

    public function test_register()
    {
        $password =  $this->faker->password(6, 20);
        $payload = [
            'name' => $this->faker->firstName,
            'password'  => $password,
            'password_confirmation'  => $password,
            'email'      => $this->faker->email
        ];
        $this->json('post', 'api/register', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'message',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            );
    }
}
