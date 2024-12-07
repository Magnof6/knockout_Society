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

