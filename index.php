<?php

include 'includes/db.php';
// Iniciar a sessão
session_start();

// Consultar produtos em destaque ou mais recentes
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4PETS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Matemasie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Matemasie&family=Rubik+Bubbles&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>

<style>
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

</style>

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

/*<!-- Barra de pesquisa -->*/

/* Estilos para a barra de navegação */


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
    .Desconto{
        
        justify-content: space-between;
        align-items: center;
        padding: -10px;
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

    .user-info {
        display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    }

    .profile-picture {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .user-info span {
        font-size: 18px;
        font-weight: bold;
     
    }

    .btn {
        
        color: white;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 5px;
    }
    .user-info img {
    margin-right: 10px;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
   




.logo{
    width: 6px; 
    height: auto;
    padding:70px;
    border-radius: 10px; /* Aplica o mesmo raio de borda às imagens */
}

.oi {
    background: #0FB2BC;
}

.Fonte-menu{
    font-family: "Rubik Bubbles", system-ui;
    font-weight: 300;
    font-size: 100px;
    
}

.image-container {
    display: flex;
    justify-content: space-around; 
   
    margin-top: 0px;
}

.image-item {
    text-align: center;
    margin: 10px;
    overflow: hidden;
    border-radius: 10px; /* Arredonda os cantos do contêiner */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra inicial */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    
}

.image-item img {
    width: 96px; 
    height: auto;
    padding:70px;
    border-radius: 10px; /* Aplica o mesmo raio de borda às imagens */
}

.image-item:hover{
    background: #0FB2BC;
    padding: auto;
    transform: scale(1.1) rotate(2deg); 
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.9); /* Adiciona uma sombra ao redor da imagem */
}

.image-item p {
    margin-top: 5px;
    font-size: 30px; 
    color: #333;
    font-weight: 500; /* Deixa o texto um pouco mais robusto */
}

.hover-message {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.7); /* Fundo semi-transparente */
    color: #fff;
    padding: 10px;
    font-size: 14px;
    text-align: center;
    opacity: 0;
    transform: translateY(100%); /* Esconde o texto abaixo da imagem */
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.image-item:hover .hover-message {
    opacity: 1;
    transform: translateY(0); /* Traz o texto para a visualização */
}

.user-info {
    display: flex;
    align-items: center;
}

.profile-picture {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.btn {
    margin-left: 20px;
    color: #ff0000;
    text-decoration: none;
}

.btn:hover {
    text-decoration: underline;
}

.hero img{
    
    width: 90%; 
 
  border-radius: 10px;
  
}


.product-card img {
    width: 150px; /* Define a largura desejada */
    height: 150px; /* Define a altura desejada */
    object-fit: cover; /* Ajusta a imagem para cobrir o espaço sem distorção */
    border-radius: 10px; /* Bordas arredondadas (opcional) */
    
}


</style>

<center>
    <main>
        <section class="hero">
            <div class="container">
                <h2 class="Fonte-menu">Bem-vindo à nossa loja!</h2>
                <p class="Fonte-menu">Encontre os melhores produtos com as melhores ofertas.</p>
                <br><br>   
               <a href="https://wa.me/5511983479012" target="_blank"> <img src="images/Banner.jpg" href="????" ></a>
            </div>
  

        
         
           


        </section>

        <div class="image-container">
            <div class="image-item">
                <img src="icones/gato.png" href="????" alt="gato">
                <p>Gato</p>
                <div class="hover-message">Tudo para o seu GATO</div>
            </div>
            <div class="image-item">
                <img src="icones/cachorro.png" href="????" alt="cachorro">
                <p>Cachorro</p>
                <div class="hover-message">Tudo para o seu DOG</div>
            </div>
            <div class="image-item">
                <img src="icones/urso-teddy.png" href="????" alt="Brinquedo">
                <p>Brinquedo</p>
                <div class="hover-message">Tudo para o seu animal se divertir</div>
            </div>
        </div>
</center>

<section class="featured-products">
    <div class="container">
        <h2>Produtos em Destaque</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price">R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                </a>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Detalhes </a>
              
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
</main>
<center>
<img src="images/frete.png">
            </center>
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
                    <a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/5511983479012" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="footer-section links">
                <h2>LINKS </h2>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
                    <li><a href="login.php"><i class="fas fa-user"></i> Login</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contato</a></li>
                    <li><a href="Faq.php"><i class="fas fa-question-circle"></i> Sobre nós</a></li>
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


</body>
</html>

