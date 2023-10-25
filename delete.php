<?php

if (isset($_GET["submit"])) {
    require_once "dbcon.php";

    $id = $_GET["id"];

    $query = "DELETE  FROM products WHERE id=:id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: index.php');
}