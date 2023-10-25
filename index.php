<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,700;0,800;1,600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
        }

        .form {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
            width: 40%;
            flex-direction: column;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <?php 
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        require_once "dbcon.php";

        $product = $description = $price = "";
        $updateMode = false;

        if (isset($_POST["submit"])) {
            if (isset($_POST["update-mode"])) {
                $id = $_POST["update-mode"];
                $product = $_POST["product"];
                $description = $_POST["description"];
                $price = $_POST["price"];

                $query = "UPDATE products SET product=:product, description=:description, price=:price WHERE id=:id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':product', $product);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':id', $id);

                $stmt->execute();

                header('Location: index.php');
            } else {
                $product = $_POST["product"];
                $description = $_POST["description"];
                $price = $_POST["price"];

                $query = "INSERT INTO products (product, description, price)
                        VALUES (:product, :description, :price)";
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':product', $product);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);

                $stmt->execute();

                header('Location: index.php');
            }
        }

        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $query = "SELECT * FROM products WHERE id=:id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $product = $data["product"];
            $description = $data["description"];
            $price = $data["price"];

            $updateMode = true;
        }
    ?>
    <div class="form">
    <form action="" method="get">
        <input type="text" name="search" placeholder="Search">
        <input type="submit" value="Search">
    </form>
    <br>
    <?php if(!$updateMode) : ?>
        <form action="" method="post">
            <input type="text" name="product" placeholder="Product"><br><br>
            <input type="text" name="description" placeholder="Description"><br><br>
            <input type="text" name="price" placeholder="Price"><br><br>
            <input type="submit" name="submit" Value="Add">
        </form>
    <?php else : ?>
        <form action="?id=<?php echo $id ?>" method="post">
            <input type="hidden" name="update-mode" value="<?php echo $id; ?>">
            <input type="text" name="product" placeholder="Product" value="<?php echo $product ?>"><br><br>
            <input type="text" name="description" placeholder="Description" value="<?php echo $description; ?>"><br><br>
            <input type="text" name="price" placeholder="Price" value="<?php echo $price; ?>"><br><br>
            <input type="submit" name="submit" value="Update Product">
        </form>
    <?php endif; ?>
    </div>
    <?php
        if(isset($_GET["search"])) {
            $search = "%" . $_GET["search"] . "%";
            
            $query = "SELECT * FROM products WHERE product LIKE :search ORDER BY product";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':search', $search);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2> Search Result. </h2>";
        }

        $query = "SELECT * FROM products";
        $stmt = $conn->query($query);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h2> All Result </h2>";
        echo "<table border='2'>";
        echo "<tr>
              <th> ID </th>
              <th> Product </th>
              <th> Description </th>
              <th> Price </th>
              <th> Action </th>
              </tr>";
        if (isset($result)) {
            foreach($result as $row) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["product"] . "</td>";
                echo "<td>" . $row["description"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>
                <form action='' method='get'>
                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                    <input type='submit' value='Update'>
                </form>
                <form action='delete.php' method='get'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <input type='submit' name='submit' value='Delete'>
                </form>
            </td>";
                echo "</tr>";
            }
        } else {
            foreach($data as $row) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["product"] . "</td>";
                echo "<td>" . $row["description"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>
                    <form action='' method='get'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <input type='submit' value='Update'>
                    </form>
                    <form action='delete.php' method='get'>
                        <input type='hidden' name='id' value='" . $row["id"] . "'>
                        <input type='submit' name='submit' value='Delete'>
                    </form>
                </td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    ?>
</body>
</html>