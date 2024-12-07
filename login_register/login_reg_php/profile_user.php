<?php
    session_start();
    require_once 'db_connect.php';

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit();
    }

    $user_email = $_SESSION['user_email'];

    // Consulta SQL para obtener nombre y apellido del usuario
    $sql = "SELECT nombre, apellido FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Usuario no encontrado.");
    }

    $sql = "SELECT cartera FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartera = $result->fetch_assoc()['cartera'];

    // Consulta para determinar si el usuario es luchador
    $sql = "SELECT * FROM luchador WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_fighter = $result->num_rows > 0;

    // Consulta para obtener peleas pasadas si es luchador
    $fights = [];
    if ($is_fighter) {
        $sql = "SELECT * FROM lucha WHERE id_luchador1 = ? OR id_luchador2 = ? AND estado = 'finalizada'";
        $stmt->prepare($sql);
        $stmt->bind_param("ss", $user_email, $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $fights[] = $row;
        }
    }

    // Consulta para obtener apuestas pasadas del usuario
    $bets = [];
    $sql = "SELECT * FROM apuesta WHERE email_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bets[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Perfil de Usuario</title>
        <link rel="stylesheet" href="styles.css">
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f0f0f0;
            }

            .container {
                display: flex;
                width: 80%;
                background-color: white;
                border: 2px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .left-side {
                flex: 1;
                padding: 20px;
                border-right: 2px solid #ddd;
            }

            .right-side {
                flex: 2;
                padding: 20px;
            }

            .header-button, .button {
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px 15px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
                margin-bottom: 10px;
                display: block;
                text-align: left;
            }

            .header-button:hover, .button:hover {
                background-color: #0056b3;
            }
            a.button {
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px 15px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
                margin-bottom: 10px;
                display: block;
                text-align: left;
            }
            a.button:hover {
                background-color: #0056b3;
            }
            h2 {
                margin-bottom: 20px;
                color: black;
            }

            .fights-table, .bets-table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
            }

            .fights-table th, .fights-table td, .bets-table th, .bets-table td {
                border: 1px solid #ddd;
                padding: 8px;
                color: black;
            }

            .fights-table th, .bets-table th {
                background-color: #f2f2f2;
            }

            .popup {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 40%;
                background-color: white;
                padding: 20px;
                border: 2px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                z-index: 1000;
            }

            .popup img {
                width: 80px;
                height: 80px;
                border-radius: 50%;
            }

            .popup h3 {
                margin-top: 0;
            }

            .popup .close-button {
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                padding: 10px 15px;
                cursor: pointer;
                font-size: 16px;
                text-decoration: none;
                display: block;
                margin-top: 10px;
                text-align: center;
            }

            .popup .close-button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="left-side">
                <h2>Bienvenido, <?php echo htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']); ?></h2>
                <button class="button" onclick="window.location.href='change_password.php'">Cambiar Contraseña</button>
                <button class="button" onclick="window.location.href='change_email.php'">Cambiar Correo</button>
                <?php if (!$is_fighter): ?>
                    <button class="button" onclick="window.location.href='register_fighter.php'">Registrarse como Luchador</button>
                <?php endif; ?>
                <button class="button" onclick ="window.location.href='index.php'" >Home</button>
            </div>
            <div class="right-side">
                <h3>Cartera</h3>
                    <p><?php echo htmlspecialchars($cartera); ?></p>
                    <?php if ($is_fighter): ?>
                        <h3>Peleas Pasadas</h3>
                        <?php if (empty($fights)): ?>
                            <p>Aún no has luchado.</p>
                        <?php else: ?>
                            <table class="fights-table">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Contrincante</th>
                                    <th>Ganador</th>
                                    <th>Rondas</th>
                                    <th>Ubicacion</th>
                                </tr>
                                <?php foreach ($fights as $fight): ?>
                                    <tr onclick="showFightDetails('<?php echo htmlspecialchars(json_encode($fight)); ?>')">
                                        <td><?php echo htmlspecialchars($fight['fecha']); ?></td>
                                        <td><?php echo htmlspecialchars($fight['id_luchador1'] == $user_email ? $fight['id_luchador2'] ?? '' : $fight['id_luchador1'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($fight['id_ganador']); ?></td>
                                        <td><?php echo htmlspecialchars($fight['num_rondas']); ?></td>
                                        <td><?php echo htmlspecialchars($fight['ubicacion']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    <?php endif; ?>

                    <h3>Apuestas Pasadas</h3>
                    <?php if (empty($bets)): ?>
                        <p>No has realizado apuestas.</p>
                    <?php else: ?>
                        <table class="bets-table">
                            <tr>
                                <th>ID Lucha</th>
                                <th>Luchador Apostado</th>
                                <th>Ganadas</th>
                                <th>Perdidas</th>
                                <th>Empates</th>
                                <th>Total</th>
                            </tr>
                            <?php foreach ($bets as $bet): ?>
                                <tr onclick="showBetDetails('<?php echo htmlspecialchars(json_encode($bet)); ?>')">
                                    <td><?php echo htmlspecialchars($bet['id_lucha']); ?></td>
                                    <td><?php echo htmlspecialchars($bet['luchador_apostado']); ?></td>
                                    <td><?php echo htmlspecialchars($bet['w']); ?></td>
                                    <td><?php echo htmlspecialchars($bet['l']); ?></td>
                                    <td><?php echo htmlspecialchars($bet['d']); ?></td>
                                    <td><?php echo htmlspecialchars($bet['total']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
            </div>
        </div>

        <div id="fightPopup" class="popup">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="text-align: center;">
                    <img src="imagenes/user.jpg" alt="Fighter 1">
                    <p>YOU</p>
                    <p id="winnerLabel" style="color: green; font-weight: bold;">WINNER</p>
                </div>
                <div style="text-align: center;">
                    <img src="imagenes/user.jpg" alt="Fighter 2">
                    <p id="opponentEmail"></p>
                    <p id="loserLabel" style="color: red; font-weight: bold;">LOSER</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <p><strong>ROUNDS:</strong> <span id="fightRounds"></span></p>
                <p><strong>LOCATION:</strong> <span id="fightLocation"></span></p>
                <p><strong>DATE:</strong> <span id="fightDate"></span></p>
            </div>
            <button class="close-button" onclick="closePopup('fightPopup')">Cerrar</button>
        </div>

        <div id="betPopup" class="popup">
            <h3>Detalles de la Apuesta</h3>
            <div style="text-align: center;">
                <p><strong>ID Lucha:</strong> <span id="betId"></span></p>
                <p><strong>Luchador Apostado:</strong> <span id="betFighter"></span></p>
                <p><strong>Ganadas:</strong> <span id="betWins"></span></p>
                <p><strong>Perdidas:</strong> <span id="betLosses"></span></p>
                <p><strong>Empates:</strong> <span id="betDraws"></span></p>
                <p><strong>Total:</strong> <span id="betTotal"></span></p>
            </div>
            <button class="close-button" onclick="closePopup('betPopup')">Cerrar</button>
        </div>

        <script>
            function showFightDetails(fight) {
                var fightDetails = JSON.parse(fight);
                var isWinner = fightDetails.id_ganador === "<?php echo $user_email; ?>";
                var opponent = fightDetails.id_luchador1 === "<?php echo $user_email; ?>"
                    ? fightDetails.id_luchador2
                    : fightDetails.id_luchador1;

                document.getElementById("winnerLabel").textContent = isWinner ? "WINNER" : "LOSER";
                document.getElementById("winnerLabel").style.color = isWinner ? "green" : "red";
                document.getElementById("loserLabel").textContent = isWinner ? "LOSER" : "WINNER";
                document.getElementById("loserLabel").style.color = isWinner ? "red" : "green";
                document.getElementById("opponentEmail").textContent = opponent;
                document.getElementById("fightRounds").textContent = fightDetails.num_rondas;
                document.getElementById("fightLocation").textContent = fightDetails.ubicacion;
                document.getElementById("fightDate").textContent = fightDetails.fecha;

                document.getElementById("fightPopup").style.display = "block";
            }

            function showBetDetails(bet) {
                var betDetails = JSON.parse(bet);
                document.getElementById("betId").textContent = betDetails.id_lucha;
                document.getElementById("betFighter").textContent = betDetails.luchador_apostado;
                document.getElementById("betWins").textContent = betDetails.w;
                document.getElementById("betLosses").textContent = betDetails.l;
                document.getElementById("betDraws").textContent = betDetails.d;
                document.getElementById("betTotal").textContent = betDetails.total;

                document.getElementById("betPopup").style.display = "block";
            }

            function closePopup(popupId) {
                document.getElementById(popupId).style.display = "none";
            }
        </script>
        <?php include 'footer.php'; ?>
    </body>
</html>
