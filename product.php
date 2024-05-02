<?php
// product.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raichen_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .quantity-container {
            display: flex;
            align-items: center;
        }

        .quantity-button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }

        .quantity-button:hover {
            background-color: #0056b3;
        }

        .quantity-count {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 0;
        }

        .quantity-label {
            margin-right: 10px;
        }

        a.admin-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            padding: 10px;
            border: 2px solid #007bff;
            border-radius: 5px;
            width: 150px;
            margin: 20px auto;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }

        a.admin-link:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Product List</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $index = 0;
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["title"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";
                        echo "<td id='price_" . $index . "'>" . $row["price"] . "</td>";
                        echo "<td class='quantity-container'>";
                        echo "<button class='quantity-button' onclick='decrementQuantity(" . $index . ")'>-</button>";
                        echo "<span class='quantity-count' id='quantity_" . $index . "'>1</span>";
                        echo "<button class='quantity-button' onclick='incrementQuantity(" . $index . ")'>+</button>";
                        echo "</td>";
                        echo "<td id='totalPrice_" . $index . "'>" . $row["price"] . "</td>";
                        echo "<td><img src='./images/" . $row["image"] . "' height='100'></td>";
                        echo "</tr>";
                        $index++;
                    }
                } else {
                    echo "<tr><td colspan='6'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="admin-product.php" class="admin-link">Admin Product</a>
    </div>
    <script>
        // Function to calculate total price based on quantity
        function calculateTotalPrice(index) {
            var price = parseFloat(document.getElementById('price_' + index).innerText);
            var quantity = parseInt(document.getElementById('quantity_' + index).innerText);
            var totalPrice = price * quantity;
            document.getElementById('totalPrice_' + index).innerText = totalPrice.toFixed(2);
        }

        // Function to handle quantity increment
        function incrementQuantity(index) {
            var quantityCount = document.getElementById('quantity_' + index);
            var quantity = parseInt(quantityCount.innerText);
            quantityCount.innerText = quantity + 1;
            calculateTotalPrice(index);
        }

        // Function to handle quantity decrement
        function decrementQuantity(index) {
            var quantityCount = document.getElementById('quantity_' + index);
            var quantity = parseInt(quantityCount.innerText);
            if (quantity > 1) {
                quantityCount.innerText = quantity - 1;
                calculateTotalPrice(index);
            }
        }
    </script>
</body>
</html>