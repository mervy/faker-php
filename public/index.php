<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$faker = Factory::create();

echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">';

$registros = isset($_POST['registros']) ? max(1, (int)$_POST['registros']) : 10;

echo "<h1 class='text-center p-5'>Fake Data Generator</h1>";

// Formulário
echo "
<div class='container'>
    <form method='POST' class='mb-4'>
        <div class='row'>
            <div class='col-md-6'>
                <label for='registros' class='form-label'>Número de registros:</label>
                <input type='number' class='form-control' id='registros' name='registros' value='$registros' min='1' max='50000' required>
            </div>
            <div class='col-md-6 mt-4'>
                <button type='submit' class='btn btn-primary'>Gerar Dados</button>
            </div>
        </div>
    </form>";

// Gerar TODOS os registros solicitados
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
}



// Exibir apenas os primeiros 5 na tabela
echo "
<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th scope='col'>Id</th>
                <th scope='col'>UUID</th>
                <th scope='col'>Name</th>
                <th scope='col'>Password</th>
                <th scope='col'>Password Hash</th>
            </tr>
        </thead>
        <tbody>";

foreach (array_slice($dados, 0, 5) as $registro) {
    echo "
        <tr>
            <td>" . htmlspecialchars($registro['id']) . "</td>
            <td>" . htmlspecialchars($registro['uuid']) . "</td>
            <td>" . htmlspecialchars($registro['name']) . "</td>
            <td>" . htmlspecialchars($registro['password']) . "</td>
            <td>" . htmlspecialchars($registro['password_hash']) . "</td>
        </tr>";
}

echo "
        </tbody>
    </table>
</div>";

// Mostrar apenas os primeiros 5 no bloco de visualização JSON (para conferência)
echo "
<div class='mt-4'>
    <h3>Exemplo dos Primeiros 5 Registros (JSON):</h3>
    <pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
echo htmlspecialchars(json_encode(array_slice($dados, 0, 5), JSON_PRETTY_PRINT));
echo "
    </pre>";

// Botão de download com TODOS os registros
$jsonCompleto = json_encode($dados, JSON_PRETTY_PRINT);
$base64 = base64_encode($jsonCompleto);
echo "
    <a href='data:application/json;charset=utf-8;base64,{$base64}' 
       download='fake_data_{$registros}_registros.json' 
       class='btn btn-success mt-3'>
       Download JSON Completo ({$registros} registros)
    </a>
</div>";

echo "</div>";

