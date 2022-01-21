<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return[
            'brand_name' => $this->faker->name
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */

    public static function newFactory()
    {
        return BrandFactory::new();
    }
}
