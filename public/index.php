<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Faker\Factory;

$faker = Factory::create();

// Valores padrão
$defaultRegistros = 5;
$registros = $defaultRegistros;
$modo = 'flush';
$dados = [];

// Sobrescreve se houver POST válido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registros = isset($_POST['registros']) ? max(1, (int)$_POST['registros']) : $defaultRegistros;
    $modo = $_POST['modo'] ?? 'flush';
}

// Função para renderizar views
function renderView($view, $params = []) {
    extract($params);
    ob_start();
    include __DIR__ . '/../resources/views/' . $view . '.php';
    return ob_get_clean();
}

// Render layout + form
echo renderView('layout', [
    'title' => 'Fake Data Generator',
    'content' => renderView('form', ['registros' => $registros, 'modo' => $modo])
]);

// ====================
// Geração flush
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $modo === 'flush') {
    // Garante que o buffer está ativo
    if (ob_get_level() === 0) ob_start();
    ob_implicit_flush(true);

    $start = time();

    echo "<script>let bar = document.getElementById('progress-bar');</script>";

    for ($i = 0; $i < $registros; $i++) {
        $password = substr($faker->password(12), 0, 12);

        $dados[] = [
            'id' => $i + 1,
            'uuid' => $faker->uuid,
            'name' => $faker->name,
            'password' => $password,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $percent = round((($i + 1)/$registros) * 100);

        $elapsed = time() - $start;
        $h = str_pad(floor($elapsed/3600),2,'0',STR_PAD_LEFT);
        $m = str_pad(floor(($elapsed%3600)/60),2,'0',STR_PAD_LEFT);
        $s = str_pad($elapsed%60,2,'0',STR_PAD_LEFT);

        echo "<script>
            if(bar) {
                bar.style.width='{$percent}%';
                bar.innerText='{$percent}%';
            }
            if(document.getElementById('timer')) {
                document.getElementById('timer').innerText='{$h}:{$m}:{$s}';
            }
        </script>";

        flush();
        ob_flush();
        usleep(5000);
    }

    // Tempo final
    $elapsed = time() - $start;
    $h = str_pad(floor($elapsed/3600),2,'0',STR_PAD_LEFT);
    $m = str_pad(floor(($elapsed%3600)/60),2,'0',STR_PAD_LEFT);
    $s = str_pad($elapsed%60,2,'0',STR_PAD_LEFT);

    $output = renderView('output', ['dados'=>$dados,'total'=>$registros]);
    $outputJson = json_encode($output);

    echo "<script>
        if(document.getElementById('saida')) {
            document.getElementById('saida').innerHTML = {$outputJson};
        }
        if(document.getElementById('timer')) {
            document.getElementById('timer').innerText='{$h}:{$m}:{$s}';
        }
    </script>";
}
