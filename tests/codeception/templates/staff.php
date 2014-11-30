<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->firstName,
    'surname' => $faker->lastName,
    'telephone' => $faker->phoneNumber,
    'email' => $faker->email,
    'address' => $faker->address,
    'role' => 0, //TODO will be implemented later
    'user_name' => $faker->userName,
    'password' => "123456789",
    'last_login' => $faker->dateTimeBetween($startDate = '-1 week', $endDate = 'now')->format("Y-m-d H:i:s"),
    'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
];