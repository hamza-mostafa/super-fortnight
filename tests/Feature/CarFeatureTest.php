<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CarFeatureTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function test_full_cycle_car(){
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

        // Create user and login
        $register = $this->json('post', 'api/register', $registerPayload)
            ->assertStatus(Response::HTTP_CREATED);
        $login = $this->json('post', 'api/login', $payload)
            ->assertStatus(Response::HTTP_OK);

        $token = $login->decodeResponseJson()['data']['access_token'];
        $header = ['Authentication' => 'Bearer '.$token];
        $payload = Car::factory()->definition();
        $BrandPayload = Brand::factory()->definition();

        $createBrand = $this->json('post','/api/brands',$BrandPayload , $header)
            ->assertStatus(Response::HTTP_CREATED);
        $payload['brand_id'] = $createBrand->decodeResponseJson()['data']['id'];

        // create car using login token
        $create = $this->json('post','/api/cars', $payload, $header)
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals(Car::find(1)->model_name, $payload['model_name']);

        // fetch all car data using login token
        $get = $this->json('get','/api/cars', [], $header)
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(Car::all()[0]->model_name, $get->decodeResponseJson()['data'][0]['model_name']);

        // update car using login token
        $newName = 'new name';
        $carArray = $create->decodeResponseJson()['data'];
        $carId = $create->decodeResponseJson()['data']['id'];
        $carArray['model_name'] = $newName;
        $this->json('put','/api/cars/'.$carId, $carArray, $header)
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('cars',['id'=> $carId , 'model_name' => $newName]);

        // delete car data using login token
        $delete = $this->json('delete','/api/cars/'.$carId, [], $header)
            ->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('cars',['id'=> $carId]);


    }
}
