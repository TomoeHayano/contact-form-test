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
        return [

            'last_name'   => $this->faker->lastName(),
            'first_name'  => $this->faker->firstName(),
            'gender'      => $this->faker->randomElement([1,2,3]),
            'email'       => $this->faker->unique()->safeEmail(),
            // 教材要件に合わせ 5桁以内の数字文字列
            'tel'         => (string)$this->faker->numberBetween(10000, 99999),
            'address'     => $this->faker->address(),
            'building'    => $this->faker->optional()->secondaryAddress(),
            'category_id' => Category::inRandomOrder()->value('id') ?? 1,
            'detail'      => $this->faker->text(60),
        ];
    }
}
