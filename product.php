<?php
session_start();
include 'includes/db.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Consulta para buscar o produto pelo ID
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    if (!$stmt) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o produto existe
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>Produto não encontrado.</p>";
        exit();
    }
} else {
    echo "<p>ID do produto não fornecido.</p>";
    exit();
}

// Adicionando ao carrinho
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $quantity = 1;

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = array(
            "quantity" => $quantity,
            "name" => $product['name'],
            "price" => $product['price'],
            "image" => $product['image']
        );
    }

    // Redireciona para o carrinho
    header("Location: cart.php");
    exit();
}

// Consulta para buscar produtos relacionados
$related_stmt = $conn->prepare("SELECT * FROM products WHERE id != ? LIMIT 4");
$related_stmt->bind_param("i", $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_products = $related_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<meta charset="UTF-8">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
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
    <br><br>
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
    </style>
    <center>
    <div class="container1">
        <div class="product-details">
            <div class="product-image" onclick="changeImage()">
                <img id="product-image" src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <p class="product-price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
               
                <div class="product-actions">
                    <a href="product.php?action=add&id=<?php echo $product['id']; ?>" class="btn-primary">Adicionar ao Carrinho</a>
                    <a href="index.php" class="btn-secondary">Voltar para a Loja</a>

                </div>
            </div>
        </div>
              
        <div class="related-products">
            <h2>Produtos Relacionados</h2>
            <div class="product-grid">
                <?php foreach ($related_products as $related): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $related['id']; ?>">
                            <img src="images/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        </a>
                        <div class="product-name"><?php echo htmlspecialchars($related['name']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </center>
    <br><br>
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





<style>
body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container1 {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .product-image {
            flex: 1;
            max-width: 40%;
            margin-right: 20px;
            cursor: pointer;
        }

        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        .product-info {
            flex: 3;
            margin-left: 100px; /* Adicionado para mover mais para a direita */
        }

        .product-info h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #0FB2BC;
        }

        .product-info p {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #666;
        }

        .product-price {
            font-size: 2em;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 20px;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-left: 300px;
        }

        .btn-primary {
            background-color: #0FB2BC;
            color: #fff;
            padding: 10px 20px;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-primary:hover {
            background-color: #0a99a6;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 10px;
        }

        .btn-secondary:hover {
            background-color: #565e64;
        }

        .related-products {
            margin-top: 40px;
        }

        .related-products h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .related-products .product-card {
            width: calc(25% - 20px);
            margin-bottom: 20px;
            text-align: center;
        }

        .related-products img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .related-products img:hover {
            transform: scale(1.05);
        }

        .related-products .product-name {
            font-size: 1.2em;
            margin-top: 10px;
            color: #333;
        }
    </style>
</body>
</html>