<?php
$progressFile = __DIR__ . "/progress.json";
if (!file_exists($progressFile)) {
    echo json_encode(["current" => 0, "total" => 1]);
    exit;
}
header("Content-Type: application/json");
echo file_get_contents($progressFile);
