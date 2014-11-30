<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->word." Simulator",
    'description' => $faker->paragraph(3),
    'flight_duration' => 30, //TODO
    'price_simulation' => $faker->randomNumber(3),
];