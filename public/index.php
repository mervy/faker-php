<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Faker\Factory;

$faker = Factory::create();

echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">';

$registros = isset($_POST['registros']) ? max(1, (int)$_POST['registros']) : 5;
$modo = $_POST['modo'] ?? 'flush';

echo "<h1 class='text-center p-5'>Fake Data Generator</h1>";

echo "
<div class='container'>
    <form id='form-gerar' method='POST' action='' class='mb-4'>
        <div class='row mb-3'>
            <div class='col-md-6'>
                <label for='registros' class='form-label'>Número de registros:</label>
                <input type='number' class='form-control' id='registros' name='registros' value='$registros' min='1' max='50000' required>
            </div>
            <div class='col-md-6'>
                <label class='form-label'>Modo de geração:</label><br>
                <div class='form-check'>
                    <input class='form-check-input' type='radio' name='modo' id='modoFlush' value='flush' " . ($modo === 'flush' ? 'checked' : '') . ">
                    <label class='form-check-label' for='modoFlush'>Flush (imediato)</label>
                </div>
                <div class='form-check'>
                    <input class='form-check-input' type='radio' name='modo' id='modoAjax' value='ajax' " . ($modo === 'ajax' ? 'checked' : '') . ">
                    <label class='form-check-label' for='modoAjax'>AJAX / Polling</label>
                </div>
            </div>
        </div>
        <button type='submit' class='btn btn-primary'>Gerar Dados</button>
    </form>

    <div class='mb-3'>
        <div class='progress'>
            <div id='progress-bar' class='progress-bar progress-bar-striped progress-bar-animated' 
                 role='progressbar' style='width:0%'>0%</div>
        </div>
    </div>

    <div id='saida'></div>
</div>

<iframe name='hidden-frame' style='display:none;'></iframe>
";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $modo === 'flush') {
    // ========================
    // 🔹 SOLUÇÃO FLUSH
    // ========================
    @ob_end_flush();
    ob_implicit_flush(true);

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

        $percent = round((($i + 1) / $registros) * 100);
        echo "<script>
                document.getElementById('progress-bar').style.width = '{$percent}%';
                document.getElementById('progress-bar').innerText = '{$percent}%';
              </script>";
        flush();
        usleep(10000);
    }

    // Mostrar saída (primeiros 5 + JSON + botão download)
    echo "<script>
        document.getElementById('saida').innerHTML = " . json_encode(renderSaida($dados, $registros)) . ";
    </script>";
}

function renderSaida(array $dados, int $total): string {
    $html = "
    <div class='table-responsive mt-4'>
        <table class='table table-striped'>
            <thead><tr><th>Id</th><th>UUID</th><th>Name</th><th>Password</th><th>Password Hash</th></tr></thead>
            <tbody>";
    foreach (array_slice($dados, 0, 5) as $registro) {
        $html .= "<tr>
            <td>" . htmlspecialchars($registro['id']) . "</td>
            <td>" . htmlspecialchars($registro['uuid']) . "</td>
            <td>" . htmlspecialchars($registro['name']) . "</td>
            <td>" . htmlspecialchars($registro['password']) . "</td>
            <td>" . htmlspecialchars($registro['password_hash']) . "</td>
        </tr>";
    }
    $html .= "</tbody></table></div>";

    $html .= "<div class='mt-4'><h3>Exemplo dos Primeiros 5 Registros (JSON):</h3>
              <pre style='background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;'>" .
              htmlspecialchars(json_encode(array_slice($dados, 0, 5), JSON_PRETTY_PRINT)) . "</pre>";

    $jsonCompleto = json_encode($dados, JSON_PRETTY_PRINT);
    $base64 = base64_encode($jsonCompleto);
    $html .= "<a href='data:application/json;charset=utf-8;base64,{$base64}' 
                 download='fake_data_{$total}_registros.json' 
                 class='btn btn-success mt-3'>
                 Download JSON Completo ({$total} registros)
              </a></div>";

    return $html;
}
?>

<script>
document.getElementById('form-gerar').addEventListener('submit', function(e) {
    let modo = document.querySelector('input[name="modo"]:checked').value;
    if (modo === 'ajax') {
        e.preventDefault();
        // reset barra
        let bar = document.getElementById('progress-bar');
        bar.style.width = '0%'; bar.innerText = '0%';
        document.getElementById('saida').innerHTML = '';

        // envia form para generate.php via iframe
        this.action = 'generate.php';
        this.target = 'hidden-frame';
        this.submit();

        // iniciar polling
        setTimeout(checkProgress, 500);
    }
});

async function checkProgress() {
    let res = await fetch('progress.php?_=' + Date.now());
    let data = await res.json();
    let percent = (data.current / data.total) * 100;

    let bar = document.getElementById('progress-bar');
    bar.style.width = percent + '%';
    bar.innerText = Math.round(percent) + '%';

    if (percent < 100) {
        setTimeout(checkProgress, 300);
    } else {
        document.getElementById('saida').innerHTML =
            `<a href="output.json" download="fake_data.json" class="btn btn-success mt-3">Download JSON Completo</a>`;
    }
}
</script>
