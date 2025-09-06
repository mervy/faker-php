<div class="table-responsive mt-4">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>UUID</th>
                <th>Name</th>
                <th>Password</th>
                <th>Password Hash</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice($dados, 0, 5) as $registro): ?>
                <tr>
                    <td><?= htmlspecialchars($registro['id']) ?></td>
                    <td><?= htmlspecialchars($registro['uuid']) ?></td>
                    <td><?= htmlspecialchars($registro['name']) ?></td>
                    <td><?= htmlspecialchars($registro['password']) ?></td>
                    <td><?= htmlspecialchars($registro['password_hash']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <h3>Exemplo dos Primeiros 5 Registros (JSON):</h3>
    <a href="data:application/json;charset=utf-8;base64,<?= $base64 ?>"
        download="fake_data_<?= $total ?>_registros.json"
        class="btn btn-success my-3">
        Download JSON Completo (<?= $total ?> registros)
    </a>
    <pre style="background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;">
<?= htmlspecialchars(json_encode(array_slice($dados, 0, 5), JSON_PRETTY_PRINT)) ?>
    </pre>

    <?php
    $jsonCompleto = json_encode($dados, JSON_PRETTY_PRINT);
    $base64 = base64_encode($jsonCompleto);
    ?>

</div>