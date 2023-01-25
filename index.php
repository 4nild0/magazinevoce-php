<?php
include_once __DIR__ . "/vendor/autoload.php";
include_once "src/magazinevoce.php";

use MagazineVoce\MagazineVoce;

libxml_use_internal_errors(true);

$magazineVoce = new MagazineVoce("magazine4nild0");
$produtos = $magazineVoce->buscaProdutosMagazineVoce("notebook 256gb 8gb i5");

$produtos = json_decode($produtos);
foreach ($produtos as $produto){
    echo "<a target='blank' href='{$produto->product_link}'>";
    echo "<div class='product'>" . PHP_EOL;
    echo "<p id='nomeProduto'>{$produto->product_name}</p>" . PHP_EOL;
    echo "<b class='product-price'>{$produto->product_price}</b>" . PHP_EOL;
    echo "<div class='product-img'>";
    echo "<img src='{$produto->product_image}'>";
    echo "</div>";
    echo "<p class='product-description'>{$produto->product_description}</p>" . PHP_EOL;
    echo "</div>" . PHP_EOL;
    echo "</a>";
}

?>