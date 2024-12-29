<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Obter informações do usuário
$stmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Usuário não encontrado.";
    exit();
}

// Processar o formulário de edição
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = htmlspecialchars($_POST['username']);
    $new_email = htmlspecialchars($_POST['email']);
    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);

    // Validar e mover a foto de perfil se fornecida
    if ($profile_picture) {
        // Excluir a foto antiga se houver
        if ($user['profile_picture'] && file_exists($target_dir . $user['profile_picture'])) {
            unlink($target_dir . $user['profile_picture']);
        }
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
    } else {
        $profile_picture = $user['profile_picture']; // Manter a foto atual se não for alterada
    }

    // Atualizar informações do usuário no banco de dados
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE username = ?");
    $stmt->bind_param("ssss", $new_username, $new_email, $profile_picture, $username);
    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username; // Atualizar a sessão com o novo nome de usuário
        header("Location: profile.php");
        exit();
    } else {
        echo "Erro ao atualizar o perfil.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Loja Online</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem 0;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            max-width: 1200px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1.1rem;
        }
        .edit-profile {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            text-align: center;
        }
        .edit-profile form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .edit-profile input {
            margin-bottom: 1rem;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            max-width: 400px;
        }
        .edit-profile input[type="file"] {
            border: none;
            padding: 0;
        }
        .edit-profile button {
            padding: 0.5rem 1rem;
            color: #fff;
            background-color: #0FB2BC;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .edit-profile button:hover {
            background-color: #09a2b3;
        }
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Loja Online</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="cart.php">Carrinho</a>
                <a href="profile.php">Perfil</a>
                <a href="logout.php" class="btn">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container edit-profile">
            <h2>Editar Perfil</h2>
            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <label for="username">Nome de Usuário:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="profile_picture">Foto de Perfil:</label>
                <input type="file" id="profile_picture" name="profile_picture">

                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Loja Online. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
