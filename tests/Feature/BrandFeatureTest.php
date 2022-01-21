<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BrandFeatureTest extends TestCase
{
    public function test_non_auth_user_cannot_fetch()
    {
        $this->json('get','/api/brands')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_auth_user_can_fetch()
    {
        $user = User::factory()->create()->first();
        $user->brands()->create(Brand::factory()->definition());
        $this->actingAs($user);
        $this->json('get','/api/brands')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'id',
                            'brand_name',
                            'user_id'
                        ]
                    ]

                ]);
    }

    public function test_non_auth_user_cannot_update()
    {
        $id = 1;
        $this->json('put','/api/brands/'.$id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_auth_user_can_update()
    {
        $newName = 'new name';
        $user = User::factory()->create()->first();
        $brand = $user->brands()->create(Brand::factory()->definition())->first();
        $this->actingAs($user);
        $brand->brand_name = $newName;
        $this->json('put','/api/brands/'.$brand->id, $brand->toArray())
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                            'id',
                            'brand_name',
                            'user_id'
                    ]
                ]);
        $this->assertDatabaseHas('brands',['id'=> $brand['id'] , 'brand_name' => $newName]);
    }

    public function test_auth_user_cannot_update_with_wrong_data()
    {
        $newName = 'new name';
        $fakeId = 1115;
        $user = User::factory()->create()->first();
        $user->brands()->create($brand = Brand::factory()->definition());
        $this->actingAs($user);
        $brand['brand_name'] = $newName;
        $this->json('put','/api/brands/'.$fakeId, $brand)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_non_auth_user_cannot_create()
    {
        $this->json('post','/api/brands')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_auth_user_can_create()
    {
        $user = User::factory()->create()->first();
        $payload = Brand::factory()->definition();
        $this->actingAs($user);
        $this->json('post','/api/brands', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'brand_name',
                        'user_id'
                    ]

                ]);
        $this->assertEquals(1,Brand::all()->count());
        $this->assertEquals(Brand::find(1)->brand_name, $payload['brand_name']);
    }

    public function test_non_auth_user_cannot_fetch_by_id()
    {
        $id = 1;
            // create a brand
        $this->json('get','/api/brands/'.$id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_auth_user_can_fetch_by_id()
    {
        $user = User::factory()->create()->first();
        $brand = $user->brands()->create(Brand::factory()->definition())->first();
        $this->actingAs($user);
        $get = $this->json('get','/api/brands/'.$brand->id, $brand->toArray())
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(Brand::find(1)->brand_name, $get->decodeResponseJson()['data']['brand_name']);
    }

    public function test_non_auth_user_cannot_delete_by_id()
    {
        $id = 1;
        $this->json('delete','/api/brands/'.$id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_auth_user_can_delete_by_id()
    {
        $user = User::factory()->create()->first();
        $brand = $user->brands()->create(Brand::factory()->definition())->first();
        $this->actingAs($user);
        $this->json('delete','/api/brands/'.$brand->id, $brand->toArray())
            ->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('brands',['id'=> $brand->id]);
    }

    public function test_auth_user_can_fetch_brand_by_car_model()
    {
        $user = User::factory()->create()->first();
        $brand = $user->brands()->create(Brand::factory()->definition())->first();
        $this->actingAs($user);
        $this->json('delete','/api/brands/'.$brand->id, $brand->toArray())
            ->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('brands',['id'=> $brand->id]);
    }

    /**
     * @throws \Throwable
     */
    public function test_full_cycle_brand(){
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
        $payload = Brand::factory()->definition();

        // create brand using login token
        $create = $this->json('post','/api/brands', $payload, $header)
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals(Brand::find(1)->brand_name, $payload['brand_name']);

        // fetch all brand data using login token
        $get = $this->json('get','/api/brands', [], $header)
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(Brand::all()[0]->brand_name, $get->decodeResponseJson()['data'][0]['brand_name']);

        // update brand using login token
        $newName = 'new name';
        $brandArray = $create->decodeResponseJson()['data'];
        $brandId = $create->decodeResponseJson()['data']['id'];
        $brandArray['brand_name'] = $newName;
        $this->json('put','/api/brands/'.$brandId, $brandArray)
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('brands',['id'=> $brandId , 'brand_name' => $newName]);

        // fetch brand by car model

        // create cars using login token
        $carPayload = Car::factory()->definition();
        $carPayload['brand_id'] = $brandId;
        $createCars = $this->json('post','/api/cars', $carPayload, $header)
            ->assertStatus(Response::HTTP_CREATED);

        // fetch the brand by the car model
        $brandOrCarPayload = [
            'brand_name' => $brandArray['brand_name']
        ];
        $this->json('post','/api/brands/fetchByCarModelOrBrand', $brandOrCarPayload)
            ->assertStatus(Response::HTTP_OK);
        $brandOrCarPayload = [
            'model_name' => $carPayload['model_name'],
        ];
        $this->json('post','/api/brands/fetchByCarModelOrBrand', $brandOrCarPayload)
            ->assertStatus(Response::HTTP_OK);

        // delete brand data using login token
        $delete = $this->json('delete','/api/brands/'.$brandId, [], $header)
            ->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('brands',['id'=> $brandId]);


    }
}
