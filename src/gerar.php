<?php


// Verificar se foi enviado um número via POST
$registros = isset($_POST['registros']) ? (int)$_POST['registros'] : 10;

// Se for maior que 50000, limitar para evitar problemas
if ($registros > 50000) {
    $registros = 50000;
}

// Array para armazenar os dados
$dados = [];
echo "<h1 class='text-center p-5'>Fake Data Generator</h1>";

// Formulário para inserir quantidade de registros
echo "
<div class='container'>
    <form method='POST' class='mb-4'>
        <div class='row'>
            <div class='col-md-6'>
                <label for='registros' class='form-label'>Número de registros:</label>
                <input type='number' class='form-control' id='registros' name='registros' value='$registros'
min='1' max='50000' required>
            </div>
            <div class='col-md-6 mt-4'>
                <button type='submit' class='btn btn-primary'>Gerar Dados</button>
                <a href='#' onclick='gerarJSON()' class='btn btn-success'>Gerar JSON</a>
            </div>
        </div>
    </form>";

// Gerar dados para tabela
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

// Gerar dados para a tabela (máximo 10 registros por vez)
$tabela_registros = min($registros, 10);

for ($i = 0; $i < $tabela_registros; $i++) {
    $password = $faker->password(12);
    $truncatedPassword = substr($password, 0, 12);

    // Armazenar para JSON
    $dados[] = [
        'id' => $i + 1,
        'uuid' => $faker->uuid(),
        'name' => $faker->name(),
        'password' => $truncatedPassword,
        'password_hash' => password_hash($truncatedPassword, PASSWORD_DEFAULT)
    ];

    echo "
        <tr>
           <td>". ($i + 1) ."</td>
            <td>". $faker->uuid() ."</td>
            <td>". $faker->name() ."</td>
            <td>". $truncatedPassword ."</td>
            <td>". password_hash($truncatedPassword, PASSWORD_DEFAULT) ."</td>
        </tr>";
}

echo "
        </tbody>
    </table>
</div>";

// Botão para gerar JSON
if ($registros > 0) {
    echo "
    <div class='mt-4'>
        <h3>JSON Gerado:</h3>
        <pre id='jsonOutput' style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>
". json_encode($dados, JSON_PRETTY_PRINT) ."
        </pre>
        <button class='btn btn-secondary' onclick='copyToClipboard()'>Copiar JSON</button>
        <a href='data:text/json;charset=utf-8,". urlencode(json_encode($dados, JSON_PRETTY_PRINT))."'
download='fake_data.json' class='btn btn-success'>Download JSON</a>
    </div>";
}

echo "
<script>
function copyToClipboard() {
    const text = document.getElementById('jsonOutput').textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('JSON copiado para a área de transferência!');
    });
}

function gerarJSON() {
    // Atualiza o formulário com o número desejado e submete
    const registros = prompt('Quantos registros deseja gerar?', '10000');
    if (registros !== null && registros > 0) {
        document.getElementById('registros').value = registros;
        document.querySelector('form').submit();
    }
}
</script>
</div>";

// Se for mais de 10 registros, gere o JSON completo
if ($registros > 10) {
    $dados_completos = [];

    // Gerar dados adicionais para o JSON
    for ($i = 10; $i < $registros; $i++) {
        $password = $faker->password(12);
        $truncatedPassword = substr($password, 0, 12);

        $dados_completos[] = [
            'id' => $i + 1,
            'uuid' => $faker->uuid(),
            'name' => $faker->name(),
            'password' => $truncatedPassword,
            'password_hash' => password_hash($truncatedPassword, PASSWORD_DEFAULT)
        ];
    }

    // Adicionar os dados iniciais
    $dados = array_merge($dados, $dados_completos);
}
?>
```

## Versão mais simples e funcional:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// Aumentar limite de tempo e memória
ini_set('max_execution_time', 600); // 10 minutos
ini_set('memory_limit', '1024M');

$faker = Factory::create();

// Verificar se foi enviado um número via POST
$registros = isset($_POST['registros']) ? (int)$_POST['registros'] : 10;

echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">';
echo "<h1 class='text-center p-5'>Fake Data Generator</h1>";

// Formulário para inserir quantidade de registros
echo "
<div class='container'>
    <form method='POST' class='mb-4'>
        <div class='row'>
            <div class='col-md-6'>
                <label for='registros' class='form-label'>Número de registros:</label>
                <input type='number' class='form-control' id='registros' name='registros' value='$registros'
min='1' max='50000' required>
            </div>
            <div class='col-md-6 mt-4'>
                <button type='submit' class='btn btn-primary'>Gerar Dados</button>
            </div>
        </div>
    </form>";

// Gerar dados para tabela (máximo 10 registros)
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

$dados = [];

for ($i = 0; $i < min($registros, 10); $i++) {
    $password = $faker->password(12);
    $truncatedPassword = substr($password, 0, 12);

    $dados[] = [
        'id' => $i + 1,
        'uuid' => $faker->uuid(),
        'name' => $faker->name(),
        'password' => $truncatedPassword,
        'password_hash' => password_hash($truncatedPassword, PASSWORD_DEFAULT)
    ];

    echo "
        <tr>
           <td>". ($i + 1) ."</td>
            <td>". $faker->uuid() ."</td>
            <td>". $faker->name() ."</td>
            <td>". $truncatedPassword ."</td>
            <td>". password_hash($truncatedPassword, PASSWORD_DEFAULT) ."</td>
        </tr>";
}

echo "
        </tbody>
    </table>
</div>";

// Mostrar JSON
if ($registros > 0) {
    echo "
    <div class='mt-4'>
        <h3>JSON Gerado:</h3>
        <pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>
". json_encode($dados, JSON_PRETTY_PRINT) ."
        </pre>
        <a href='data:text/json;charset=utf-8,". urlencode(json_encode($dados, JSON_PRETTY_PRINT))."'
download='fake_data.json' class='btn btn-success'>Download JSON</a>
    </div>";
}

echo "</div>";
?>
```

## Funcionalidades incluídas:

1. **Formulário para inserir quantidade de registros**
2. **Mostrar apenas 10 registros na tabela por padrão**
3. **Gerar JSON completo com todos os registros**
4. **Botão para download do JSON**
5. **Botão para copiar o JSON para a área de transferência**
6. **Limitação de registros para evitar problemas de memória**

## Como usar:

1. Acesse a página
2. Digite o número de registros desejados (ex: 1000)
3. Clique em "Gerar Dados"
4. Os dados aparecem na tabela (primeiros 10 registros)
5. O JSON completo é exibido abaixo
6. Use o botão de download para salvar o arquivo JSON

A versão mais simples é recomendada se você não quiser recursos avançados, mas a primeira versão oferece uma
experiência mais completa com todos os recursos solicitados.

>>> Send a message (/? for help)