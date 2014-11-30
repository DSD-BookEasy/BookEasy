<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

//* @property integer $status
//* @property string $timestamp
//* @property string $name
//* @property string $surname
//* @property string $telephone
//* @property string $email
//* @property string $address
return [
    'status' => $faker->randomDigitNotNull()%3,
    'name' => $faker->firstName,
    'surname' => $faker->lastName,
    'telephone' => $faker->phoneNumber,
    'email' => $faker->email,
    'address' => $faker->address,
];