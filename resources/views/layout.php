<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Fake Data Generator' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
  <div class="container py-4">
        <h1 class="mb-4 text-center"><?= $title ?? 'Fake Data Generator' ?></h1>
        <?= $content ?? '' ?>
    </div>
</body>
</html>
