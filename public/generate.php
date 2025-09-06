<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Faker\Factory;

$faker = Factory::create();

$registros = isset($_POST['registros']) ? max(1, (int)$_POST['registros']) : 10;

$progressFile = __DIR__ . "/progress.json";
file_put_contents($progressFile, json_encode(["current" => 0, "total" => $registros]));

$dados = [];
for ($i = 0; $i < $registros; $i++) {
    $password = $faker->password(12);
    $truncatedPassword = substr($password, 0, 12);

    $dados[] = [
        'id' => $i + 1,
        'uuid' => $faker->uuid,
        'name' => $faker->name,
        'password' => $truncatedPassword,
        'password_hash' => password_hash($truncatedPassword, PASSWORD_DEFAULT)
    ];

    file_put_contents($progressFile, json_encode([
        "current" => $i + 1,
        "total"   => $registros
    ]));

    usleep(20000);
}

file_put_contents(__DIR__ . "/output.json", json_encode($dados, JSON_PRETTY_PRINT));
