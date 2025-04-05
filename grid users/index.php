<?php
include('conexao.php');

$ordem = "total DESC";
$campo = $_GET['campo'] ?? '';
$sentido = $_GET['sentido'] ?? '';

if (in_array($campo, ['nome', 'email']) && in_array($sentido, ['ASC', 'DESC'])) {
    $ordem = "$campo $sentido";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome, $email);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Erro ao inserir dados: " . $conn->error;
    }
}

$sql = "SELECT nome, email, COUNT(id) AS total 
        FROM usuarios
        GROUP BY nome, email
        ORDER BY $ordem
        LIMIT 25";


$result = $conn->query($sql);

if (!$result) {
    die("Erro ao executar a consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários Agrupados</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="Document.ico" type="image/x-icon">
</head>

<body>
    <!-- ADICIONAR DADOS DE USUSARIO-->
     
    <h2>Adicionar Novo Usuário</h2>

<form method="POST">

    <input type="text" name="nome" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>    
    <button type="submit">Adicionar</button>
</form>
    <h2>Lista de Clientes</h2>

    <table>
        <tr>
            <th>Nome</th>
            <th>Email</th>
        </tr>
        <div style="width: 50%; margin: 0 auto; text-align: right;">

        <?php
        // alter  ASC e DESC tabela
        $novaOrdem = ($sentido === 'ASC') ? 'DESC' : 'ASC';
        $icone = ($sentido === 'ASC') ? '↓' : '↑';
        ?>
        <a href="?campo=nome&sentido=<?= $novaOrdem ?>" style="text-decoration: none; font-weight: bold;">
            Ordenar Nome A/z<?= $icone ?>
        </a>
    </div>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Nenhum resultado encontrado</td></tr>";
        }

        $conn->close();
        ?>
    </table>



    

</body>
</html>
