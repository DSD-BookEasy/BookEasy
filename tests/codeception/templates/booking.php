<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'status' => $faker->randomDigitNotNull()%3,
    'name' => $faker->firstName,
    'surname' => $faker->lastName,
    'telephone' => $faker->phoneNumber,
    'email' => $faker->email,
    'address' => $faker->address,
];