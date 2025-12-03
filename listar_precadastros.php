<?php
// Cauê
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel";

// CONEXÃO
$conn = new mysqli($host, $user, $pass, $dbname);

// VERIFICAR ERRO
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// CONSULTA: pegar pré-cadastros aguardando ação da recepção
$sql = "SELECT id, nome_hospede, placa_carro, data_chegada 
        FROM precadastros 
        WHERE status = 'aguardando'
        ORDER BY data_chegada ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pré-Cadastros Aguardando</title>
    <style>
        table {
            border-collapse: collapse;
            width: 70%;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #ddd;
        }
    </style>
</head>
<body>

<h2>Pré-cadastros aguardando ação da recepção</h2>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Nome do Hóspede</th>
                <th>Placa do Carro</th>
                <th>Data de Chegada</th>
                <th>Ação</th>
            </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nome_hospede']}</td>
                <td>{$row['placa_carro']}</td>
                <td>{$row['data_chegada']}</td>
                <td><a href='finalizar_checkin.php?id={$row['id']}'>Fazer Check-in</a></td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>Nenhum pré-cadastro aguardando.</p>";
}

$conn->close();
?>

</body>
</html>
