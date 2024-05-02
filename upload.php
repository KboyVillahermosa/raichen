<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define upload directory
    $target_dir = __DIR__ . "../images/";
    
    // Get form data
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    
    // Get file details
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    // Check file size (max 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow only certain file formats
    $allowed_formats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_formats)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Upload file and insert product data into database
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert product data into database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "raichen_store";
            
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "INSERT INTO products (title, description, price, image) VALUES ('$title', '$description', '$price', '$file_name')";
            
            if ($conn->query($sql) === TRUE) {
                // Redirect to product.php after successful upload
                echo '<script>';
                echo 'alert("Upload successful!");';
                echo 'window.location.href = "product.php";';
                echo '</script>';
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            $conn->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
