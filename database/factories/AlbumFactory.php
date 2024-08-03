<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Album;
use App\Models\Serie;

class AlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Album::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'summary' => $this->faker->text(),
            'pages' => $this->faker->word(),
            'cover' => $this->faker->text(),
            'isbn' => $this->faker->word(),
            'comment' => $this->faker->text(),
            'read' => $this->faker->word(),
            'serie_id' => Serie::factory(),
        ];
    }
}
