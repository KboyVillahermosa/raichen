<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $image = $_POST['image'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];

        $sql = "INSERT INTO products (name, image, description, price, quantity) VALUES ('$name', '$image', '$description', '$price', '$quantity')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Product added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>

<form method="post" action="admin.php">
    <label>Name:</label><br>
    <input type="text" name="name"><br>

    <label>Image URL:</label><br>
    <input type="text" name="image"><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br>

    <label>Price:</label><br>
    <input type="text" name="price"><br>

    <label>Quantity:</label><br>
    <input type="text" name="quantity"><br>

    <input type="submit" name="add_product" value="Add Product">
</form>

</body>
</html>

<?php
$conn->close();
?>
