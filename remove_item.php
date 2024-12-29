<?php
session_start();

// Verificar se o ID do produto foi passado
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Verificar se o carrinho existe e o item está presente
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Remover o item do carrinho
    }
}

// Redirecionar de volta para a página do carrinho
header("Location: cart.php");
exit();
?>
