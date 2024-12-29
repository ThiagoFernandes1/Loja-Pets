<?php
include 'includes/db.php';

// Iniciar a sessão
session_start();

// Verificar se o carrinho não está vazio
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Inicializar variáveis
$payment_method = '';
$cpf = '';
$shipping_cost = 0;
$applyDiscount = false; // Verifica se o desconto será aplicado

// Função para calcular o frete (exemplo simples, pode ser substituído por uma API real)
function calculateShippingCost($zip) {
    $shipping_cost = 0;
    if ($zip && strlen($zip) == 8) {
        $shipping_cost = 100.00; // Valor fixo para exemplo
    }
    return $shipping_cost;
}

// Processar o formulário de checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coletar dados do formulário
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $zip = htmlspecialchars($_POST['zip']);
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $cpf = htmlspecialchars($_POST['cpf']);

    // Verificar se o usuário está logado e se é a primeira compra
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Verifica no banco de dados se é a primeira compra
        $query = "SELECT is_first_purchase FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($is_first_purchase);
        $stmt->fetch();
        $stmt->close();

        if ($is_first_purchase) {
            $applyDiscount = true; // Aplica desconto se for a primeira compra
        }
    }

    // Calcular o total
    $total = 0;
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $sql = "SELECT price FROM products WHERE id = " . intval($product_id);
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();
        if ($product) {
            $price = $product['price'];

            // Aplica o desconto de 10% se for a primeira compra
            if ($applyDiscount) {
                $discounted_price = $price * 0.90; // 10% de desconto
            } else {
                $discounted_price = $price;
            }

            // Calcula o total com o desconto aplicado
            $item_total = $item['quantity'] * $discounted_price;
            $total += $item_total;
        }
    }

    // Calcular o frete
    $shipping_cost = calculateShippingCost($zip);
    $total += $shipping_cost;

    // Salvar os dados da compra na sessão para exibir na página de confirmação
    $_SESSION['order'] = array(
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'city' => $city,
        'state' => $state,
        'zip' => $zip,
        'payment_method' => $payment_method,
        'cpf' => $cpf,
        'total' => $total,
        'shipping_cost' => $shipping_cost,
        'cart' => $_SESSION['cart']
    );

    // Limpar o carrinho após o pedido ser finalizado
    $_SESSION['cart'] = array();

    // Redirecionar para a página de confirmação
    header("Location: thank_you.php");
    exit();
}

// Obter os produtos no carrinho
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$total = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div id="discount-banner" class="bannerr">
    <p>Desconto 75% em produtos selecionados <span onclick="closeBanner()" class="close-btn">&times;</span></p>
</div>
<script>
    function closeBanner() {
        document.getElementById("discount-banner").style.display = "none";
    }
</script>


    <header>
    <div class="container">
        <h1>4PETS</h1>
        <nav class="navbar">

        
            <div class="nav-left">
                <a href="index.php">
                <i class="fas fa-home"></i> 
                </a>
                <a href="cart.php">
                <i class="fas fa-shopping-cart"></i> 
                </a>
            </div>
            <div class="nav-right">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="user-info">
                    <a href="profile.php">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Foto de Perfil" class="profile-picture">
                </a>
                <a href="profile.php">

                        <span href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </a>
                        <a href="logout.php" class="btn">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php">
                    <i class="fas fa-user"></i> Login
                    </a>
                    <a href="register.php">Criar Conta</a>
                <?php endif; ?>
            </div>
            
        </nav>
    </div>
    <Center>
    <form action="search.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Pesquisar produtos..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
                </Center>
    </header>

    <style>
    .product-image {
    width: 300px; /* Ajuste o tamanho base da imagem conforme necessário */
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease; /* Adiciona uma transição suave */
}

.product-image:hover {
    transform: scale(1.2); /* Aumenta o tamanho da imagem em 20% quando o cursor passar por cima */
    cursor: zoom-in; /* Muda o cursor para o ícone de zoom */
}


.navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
}
         .nav-left, .nav-right {
        display: flex;
        align-items: center;
    }

    .nav-left a, .nav-right a {
        margin-right: 20px;
        text-decoration: none;
        font-weight: bold;
      
    }

    .nav-left a:hover, .nav-right a:hover {
        color: #0FB2BC;
    }
.bannerr {
    background-color: #ffcc00;
    color: #333;
    text-align: center;
    padding: 10px 0;
    font-size: 16px;
    position: relative;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.bannerr p {
    margin: 0;
    padding: 0;
}

.close-btn {
    font-size: 20px;
    cursor: pointer;
    margin-left: 15px;
    color: #333;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}

.close-btn:hover {
    color: #ff0000;
}


/* Estilos da barra de pesquisa */
.search-form {
    display: flex;
    align-items: center;
    position: relative;
    max-width: 300px;
    width: 100%;
}

.search-form input[type="text"] {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #ddd;
    border-radius: 25px;
    outline: none;
    font-size: 16px;
    transition: border-color 0.3s;
}

.search-form input[type="text"]:focus {
    border-color: #007bff;
}

.search-form button {
    position: absolute;
    right: 5px;
    background-color: transparent;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #333;
}

.search-form button i {
    font-size: 18px;
}

.search-form button:hover {
    color: #007bff;
}



header{
    background-color: #002C5A;
    }


    </style>





    <main>
        <div class="container checkout">
            <h2>Finalizar Compra</h2>
            <form action="checkout.php" method="POST">
                <fieldset>
                    <legend>Informações do Cliente</legend>
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" required>
                </fieldset>
                
                <fieldset>
                    <legend>Endereço de Entrega</legend>
                    <label for="address">Endereço:</label>
                    <input type="text" id="address" name="address" required>
                    
                    <label for="city">Cidade:</label>
                    <input type="text" id="city" name="city" required>
                    
                    <label for="state">Estado:</label>
                    <input type="text" id="state" name="state" required>
                    
                    <label for="zip">CEP:</label>
                    <input type="text" id="zip" name="zip" required>
                </fieldset>

                <fieldset>
                    <legend>Forma de Pagamento</legend>
                    <label for="payment_method">Escolha a forma de pagamento:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="credit_card">Cartão de Débito</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank_transfer">Crédito</option>
                    </select>
                </fieldset>

                <fieldset>
                    <legend>Resumo do Pedido</legend>
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Original</th>
                                <th>Preço com Desconto</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($cart as $product_id => $item) {
                                $sql = "SELECT * FROM products WHERE id = " . intval($product_id);
                                $result = $conn->query($sql);
                                $product = $result->fetch_assoc();
                            }
                                if ($product) {
                                    $price = $product['price'];
                                    $discounted_price = $applyDiscount ? $price * 0.90 : $price;

                                    $item_total = $item['quantity'] * $discounted_price;
                                    $total += $item_total;
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><s>R$ <?php echo number_format($price, 2, ',', '.'); ?></s></td>
                                <td>R$ <?php echo number_format($discounted_price, 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item_total, 2, ',', '.'); ?></td>
                            </tr>
                            <?php
                                
                             ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>Subtotal:</strong></td>
                                <td>R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><strong>Frete:</strong></td>
                                <td>R$ <?php echo number_format($shipping_cost, 2, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><strong>Total:</strong></td>
                                <td>R$ <?php echo number_format($total + $shipping_cost, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
                <center>
                <button type="submit" class="btn">Finalizar Compra</button>
                </center>
            </form>
        </div>
    </main>
                           <br> 
    <footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h2>SOBRE A LOJA</h2>
                <p>
                    Bem-vindo à nossa loja a 4Pets! Oferecemos uma ampla variedade de produtos com preços muito baratos, descontos e entrega rápida. Nossa missão é proporcionar a melhor experiência de compra online para nossos clientes e pets.
                </p>
                <div class="contact">
                    <i class="fas fa-phone"></i> (11) 93931-4393
                    <br>
                    <i class="fas fa-envelope"></i> THG
                </div>
                <div class="socials">
                    <a href="https://pt-br.facebook.com/s" target="_blank"><i class="fab fa-facebook" ></i></a>
                    <a href="https://www.instagram.com/_rafhaelcesar_?igsh=anRoemE0NHJ1cndq" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/5511939314393" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="footer-section links">
                <h2>LINKS </h2>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
                    <li><a href="login.php"><i class="fas fa-user"></i> Login</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contato</a></li>
                    <li><a href="faq.php"><i class="fas fa-question-circle"></i> Sobre nós</a></li>
                </ul>
            </div>

            <div class="footer-section hours">
                <h2>HORÁRIOS DE ATENDIMENTO</h2>
                <p><i class="fas fa-clock"></i> Seg-Sex: 9h - 18h</p>
                <p><i class="fas fa-clock"></i> Sáb: 9h - 14h</p>
                <p><i class="fas fa-clock"></i> Dom: Fechado</p>
            </div>
        </div>
        <center>
        <div class="footer-bottom">
            &copy; 2024 4Pets | A melhor loja para o seu pet.
            </center>
        </div>
    </div>
           
        </div>
    </footer>
    <style>
    footer {
    background-color: #002c5a;
    color: #fff;
    padding: 40px 0;
}

footer .container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    width: 100%;
}

.footer-section {
    flex: 1;
    margin: 10px;
}

.footer-section h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #fff;
}

.footer-section p {
    margin-bottom: 15px;
}

.footer-section .contact i,
.footer-section .hours i {
    color: #ff8c00;
    margin-right: 10px;
}

.footer-section .socials a {
    margin-right: 15px;
    color: #fff;
    font-size: 1.2rem;
}

.footer-section .socials a:hover {
    color: #ff8c00;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 10px;
}

.footer-section ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
}

.footer-section ul li a i {
    margin-right: 10px;
}

.footer-section ul li a:hover {
    color: #ff8c00;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    font-size: 0.9rem;
    border-top: 1px solid #444;
}

</style>


</body>
</html>