<?php

    function cartera($conn, $user_email)
    {
        $sql = "SELECT cartera FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['cartera'];
    }

    function five_past_fights($conn, $user_email)
    {
        $sql = "SELECT luchador1_nombre, luchador2_nombre, id_categoria, id_ganador, num_rondas, fecha, hora_inicio, hora_final, estado, ubicacion, id_lucha
                FROM lucha
                WHERE (luchador1_email = ? OR luchador2_email = ?) AND estado = 'Finalizada'
                ORDER BY fecha DESC, hora_inicio DESC
                LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_email, $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

