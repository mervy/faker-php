<form id="form-gerar" method="POST" action="">
    <div class="row mb-3 justify-content-center">
        <div class="col-md-3">
            <label for="registros" class="form-label">Número de registros:</label>
            <input type="number" class="form-control" id="registros" name="registros" value="<?= $registros ?>" min="1" max="50000" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Modo de geração:</label><br>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="modo" id="modoFlush" value="flush" <?= $modo === 'flush' ? 'checked' : '' ?>>
                <label class="form-check-label" for="modoFlush">Flush (imediato)</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Gerar Dados</button>
</form>

<div class="mb-3 mt-3">
    <div class="progress">
        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width:0%">0%</div>
    </div>
</div>

<div class="mt-2 text-end">
    <strong>Tempo gasto: </strong><span id="timer">00:00:00</span>
</div>

<div id="saida"></div>
