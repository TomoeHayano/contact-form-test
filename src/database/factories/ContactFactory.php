<?php

namespace Database\Factories;

use App\Models\Contact; 
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('ja_JP');

        return [

            'last_name'   => $this->faker->lastName(),
            'first_name'  => $this->faker->firstName(),
            'gender'      => $this->faker->randomElement([1,2,3]),
            'email'       => $this->faker->unique()->safeEmail(),
            'tel'         => '0' . $this->faker->numberBetween(70, 90) . $this->faker->numerify('########'),
            'address'     => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'building'    => $this->faker->optional()->secondaryAddress(),
            'category_id' => Category::inRandomOrder()->value('id') ?? 1,
            'detail'      => $this->faker->text(60),
        ];
    }
}
