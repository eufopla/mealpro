<?php

return [
    'GET' => [
        '/meals'        => [MealController::class, 'index'],
        '/meals/create' => [MealController::class, 'create'],
    ],
    'POST' => [
        '/meals' => [MealController::class, 'store'],
    ],
    'DELETE' => [
        '/meals' => [MealController::class, 'destroy'],
    ],
];