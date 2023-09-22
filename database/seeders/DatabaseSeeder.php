<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(33)->create()->each(function($book){
            $numberOfReviews = random_int(5, 30);

            Review::factory()->count($numberOfReviews)
                ->good() //overWrite for rating column انا كرييته هذا الكستوم ستيت
                ->for($book) //هاي عشان الريلاشين ولارافيل لحالها بتعرف انه البوك اي دي اللي بدي احطه بالريفيو تيبل بكل كولوم بدي اكرييته
                ->create();
        });

        Book::factory(33)->create()->each(function($book){
            $numberOfReviews = random_int(5, 30);

            Review::factory()->count($numberOfReviews)
                ->average() //overWrite for rating column انا كرييته هذا الكستوم ستيت
                ->for($book) //هاي عشان الريلاشين ولارافيل لحالها بتعرف انه البوك اي دي اللي بدي احطه بالريفيو تيبل بكل كولوم بدي اكرييته
                ->create();
        });

        Book::factory(34)->create()->each(function($book){
            $numberOfReviews = random_int(5, 30);

            Review::factory()->count($numberOfReviews)
                ->bad() //overWrite for rating column انا كرييته هذا الكستوم ستيت
                ->for($book) //هاي عشان الريلاشين ولارافيل لحالها بتعرف انه البوك اي دي اللي بدي احطه بالريفيو تيبل بكل كولوم بدي اكرييته
                ->create();
        });

        //هذول من لارافيل لحالهم تسوو
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
