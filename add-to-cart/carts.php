<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raichen_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($data) {
        $name = $data->name ?? '';
        $address = $data->address ?? '';
        $zipCode = $data->zipCode ?? '';
        $paymentMethod = $data->paymentMethod ?? '';
        $totalAmount = $data->totalAmount ?? 0;

        // Check if the table 'purchases' exists
        $checkTable = $conn->query("SHOW TABLES LIKE 'purchases'");
        if ($checkTable->num_rows == 0) {
            $sql = "CREATE TABLE purchases (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                address TEXT NOT NULL,
                zipCode VARCHAR(10) NOT NULL,
                paymentMethod VARCHAR(50) NOT NULL,
                totalAmount DECIMAL(10, 2) NOT NULL,
                purchaseDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            if ($conn->query($sql) !== TRUE) {
                echo "Error creating table: " . $conn->error;
            }
        }

        $stmt = $conn->prepare("INSERT INTO purchases (name, address, zipCode, paymentMethod, totalAmount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $name, $address, $zipCode, $paymentMethod, $totalAmount);

        if ($stmt->execute() === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No data received";
    }
} else {
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>

<body>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 1em 0;
            text-align: center;
        }

        h1,
        h2 {
            margin: 0;
            padding: 0.5em 0;
        }

        main {
            padding: 2em;
        }

        .cart-items-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1em;
        }

        .cartItem {
            border: 1px solid #ddd;
            padding: 1em;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .productImage {
            width: 100%;
            border-radius: 5px;
            margin-bottom: 1em;
        }

        .itemInfo {
            margin-top: 1em;
        }

        .quantityButton {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 0.5em;
            margin: 0 0.2em;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s ease-in-out;
        }

        .quantityButton:hover {
            background-color: #555;
        }

        .removeButton {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 0.5em;
            margin-top: 1em;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s ease-in-out;
        }

        .removeButton:hover {
            background-color: #c9302c;
        }

        .total-amount {
            margin-top: 2em;
            font-size: 1.2em;
            font-weight: bold;
        }

        .cart-empty-message {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            font-size: 1.2em;
        }

        .hidden {
            display: none;
        }

        .modal-open {
            overflow: hidden;
        }

        /* Modal Styles */
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 2em;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .close-button {
            position: absolute;
            top: 0;
            right: 0;
            cursor: pointer;
            font-size: 1.5em;
            padding: 0.2em 0.5em;
            background-color: #ddd;
            border-radius: 3px;
            transition: background-color 0.3s ease-in-out;
        }

        .close-button:hover {
            background-color: #ccc;
        }

        #paymentForm {
            margin-top: 2em;
        }

        #paymentForm h2 {
            margin-bottom: 1em;
        }

        #checkoutForm div {
            margin-bottom: 1em;
        }

        #checkoutForm label {
            display: block;
            margin-bottom: 0.5em;
        }

        #checkoutForm input,
        #checkoutForm select {
            width: 100%;
            padding: 0.5em;
            border-radius: 3px;
            border: 1px solid #ddd;
        }

        #checkoutForm button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 0.5em 1em;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s ease-in-out;
        }

        #checkoutForm button:hover {
            background-color: #555;
        }
    </style>
    <header>
        <h1>Your Cart</h1>
    </header>
    <main>
        <div id="cartItems" class="cart-items-container">
            <!-- Your PHP and HTML code for displaying cart items -->
        </div>
        <div id="totalHeader">
            <div class="total"></div>
        </div>
        <div id="totalAmount" class="total-amount"></div> <!-- Placeholder for total amount -->
        <div class="cart-empty-message">Your cart is empty.</div>
        <section>
            <main>
                <!-- ...existing code... -->
                <div id="paymentForm">
                    <h2>Payment Details</h2>
                    <form id="checkoutForm">
                        <div>
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div>
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div>   
                            <label for="zipCode">Zip Code:</label>
                            <input type="text" id="zipCode" name="zipCode" required>
                        </div>
                        <div>
                            <label for="paymentMethod">Payment Method:</label>
                            <select id="paymentMethod" name="paymentMethod" required>
                                <option value="gcash">GCash</option>
                                <option value="paypal">PayPal</option>
                                <option value="cashOnDelivery">Cash on Delivery</option>
                            </select>
                        </div>
                        <button type="submit">Complete Purchase</button>
                    </form>
                </div>
                <!-- ...existing code... -->
            </main>

        </section>


        <script>
        // Your JavaScript code here
        document.addEventListener("DOMContentLoaded", () => {
            const checkoutForm = document.getElementById("checkoutForm");
            const paymentForm = document.getElementById("paymentForm");

            checkoutForm.addEventListener("submit", (event) => {
                event.preventDefault();

                const name = document.getElementById("name").value;
                const address = document.getElementById("address").value;
                const zipCode = document.getElementById("zipCode").value;
                const paymentMethod = document.getElementById("paymentMethod").value;
                const totalAmount = parseFloat(document.getElementById("totalAmount").textContent.split(":")[1].trim());

                savePurchaseToDatabase(name, address, zipCode, paymentMethod, totalAmount);
                displayThankYouModal(totalAmount);

                // Clear the cart and update the display
                localStorage.clear();
                displayCartItems();
                updateCartCount();

                // Hide the payment form
                paymentForm.style.display = "none";
            });

            function savePurchaseToDatabase(name, address, zipCode, paymentMethod, totalAmount) {
                fetch('', {  // The same PHP file itself
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        address,
                        zipCode,
                        paymentMethod,
                        totalAmount
                    })
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data); // log the response from the PHP script
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            function displayThankYouModal(totalAmount) {
                const modal = document.createElement("div");
                modal.classList.add("modal-open");
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close-button">&times;</span>
                        <h2>Thank You for Choosing Raichen!</h2>
                        <p>Your receipt and total amount: $${totalAmount.toFixed(2)}</p>
                        <a href="../add-to-cart/products.php"><button>Confirm</button></a>
                    </div>
                `;

                document.body.appendChild(modal);

                const closeButton = modal.querySelector(".close-button");
                closeButton.addEventListener("click", () => {
                    modal.remove();
                });
            }

            // Your other JavaScript functions here
});




            ////#33

            document.addEventListener("DOMContentLoaded", () => {
                const cartItemsContainer = document.getElementById("cartItems");
                const totalHeader = document.getElementById("totalHeader");
                const totalSection = totalHeader.querySelector(".total");
                const cartEmptyMessage = document.querySelector(".cart-empty-message");
                const productImages = getProductImages();
                const productDescriptions = getProductDescription();

                function displayCartItems() {
                    while (cartItemsContainer.firstChild) {
                        cartItemsContainer.firstChild.remove();
                    }

                    let totalAmount = 0;
                    let itemCount = 0;

                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && key.startsWith("product_")) {
                            const productName = localStorage.getItem(key);
                            const quantityKey = `quantity_${key}`;
                            const productQuantity =
                                parseInt(localStorage.getItem(quantityKey)) || 0;
                            itemCount += productQuantity;

                            const cartItem = document.createElement("div");
                            cartItem.classList.add("cartItem");
                            cartItem.setAttribute("data-key", key);

                            const image = document.createElement("img");
                            image.src = productImages[productName];
                            image.alt = productName;
                            image.classList.add("productImage");

                            const itemInfo = document.createElement("div");
                            itemInfo.classList.add("itemInfo");

                            const itemName = document.createElement("span");
                            itemName.textContent = productName;

                            const itemDescription = document.createElement("p");
                            itemDescription.textContent = productDescriptions[productName];

                            const quantity = document.createElement("span");
                            const decreaseButton = document.createElement("button");
                            decreaseButton.textContent = "-";
                            decreaseButton.classList.add("quantityButton");
                            decreaseButton.addEventListener("click", () => {
                                updateQuantity(key, -1);
                            });

                            const increaseButton = document.createElement("button");
                            increaseButton.textContent = "+";
                            increaseButton.classList.add("quantityButton");
                            increaseButton.addEventListener("click", () => {
                                updateQuantity(key, 1);
                            });

                            quantity.appendChild(decreaseButton);
                            quantity.appendChild(document.createTextNode(` ${productQuantity} `));
                            quantity.appendChild(increaseButton);

                            const amount = document.createElement("span");
                            const productPrice = getProductPrice(productName);
                            const productAmount = (productQuantity * productPrice).toFixed(2);
                            amount.textContent = `${productAmount}`;

                            totalAmount += parseFloat(productAmount);

                            const removeButton = document.createElement("button");
                            removeButton.textContent = "Remove";
                            removeButton.classList.add("removeButton");
                            removeButton.addEventListener("click", () => {
                                localStorage.removeItem(key);
                                localStorage.removeItem(quantityKey);
                                displayCartItems();
                                updateCartCount();
                            });

                            itemInfo.appendChild(itemName);
                            itemInfo.appendChild(itemDescription);
                            cartItem.appendChild(image);
                            cartItem.appendChild(itemInfo);
                            cartItem.appendChild(quantity);
                            cartItem.appendChild(amount);
                            cartItem.appendChild(removeButton);
                            cartItemsContainer.appendChild(cartItem);
                        }
                    }

                    const totalAmountElement = document.getElementById("totalAmount");
                    totalAmountElement.textContent = `Total: ${totalAmount.toFixed(2)}`;

                    // Toggle the visibility of the payment section and empty cart message based on cart content
                    totalSection.style.display = itemCount > 0 ? "block" : "none";
                    cartEmptyMessage.style.display = itemCount > 0 ? "none" : "flex";
                }

                function updateQuantity(productKey, quantityChange) {
                    const quantityKey = `quantity_${productKey}`;
                    const currentQuantity = parseInt(localStorage.getItem(quantityKey)) || 0;
                    const newQuantity = Math.max(currentQuantity + quantityChange, 0);
                    localStorage.setItem(quantityKey, newQuantity.toString());
                    displayCartItems();
                    updateCartCount();
                }

                function updateCartCount() {
                    const cartCount = document.getElementById("cartCount");
                    if (cartCount) {
                        let count = 0;
                        for (let i = 0; i < localStorage.length; i++) {
                            const key = localStorage.key(i);
                            if (key && key.startsWith("product_")) {
                                count++;
                            }
                        }
                        cartCount.textContent = count.toString();
                    }
                }

                function getProductImages() {
                    return {
                        "Dog Product 1": "../images/bike1.jpg",
                        "Dog Product 2": "../images/bike2.jpg",
                        "Dog Product 3": "../images/bike3.jpg",
                        "Dog Product 4": "../images/bike4.jpg",
                        "Cat Product 1": "../images/cat-food.webp",
                        "Cat Product 2": "../images/cat-food.webp",
                        "Cat Product 3": "../images/cat-food.webp",
                        "Cat Product 4": "../images/cat-food.webp",
                        "Bird Product 1": "../images/bird-food.jpeg",
                        "Bird Product 2": "../images/bird-food.jpeg",
                        "Bird Product 3": "../images/bird-food.jpeg",
                        "Bird Product 4": "../images/bird-food.jpeg",
                        "Fish Product 1": "../images/fish-food.webp",
                        "Fish Product 2": "../images/fish-food.webp",
                        "Fish Product 3": "../images/fish-food.webp",
                        "Fish Product 4": "../images/fish-food.webp"
                    };
                }

                function getProductDescription() {
                    return {
                        "Dog Product 1":
                            "Engineered for optimal performance, the NimbusFit Pro combines cutting-edge technology with sleek design.",
                        "Dog Product 2":
                            "Designed for comfort and style, the AirFlex 2000 is perfect for your active lifestyle.",
                        "Dog Product 3":
                            "A delightful blend of Mediterranean flavors, topped with fresh vegetables and feta cheese.",
                        "Dog Product 4":
                            "StreetFlex Casuals redefine urban style with their effortlessly cool design..",
                        "Cat Product 1":
                            "StreetFlex Casuals redefine urban style with their effortlessly cool design..",
                        "Cat Product 2":
                            "The TrailBlaze Adventures Duffel Backpack is engineered for the intrepid explorer.",
                        "Cat Product 3":
                            "Elevate your professional image with the Prestige Executive Collection Leather Briefcase. Meticulously crafted from genuine leather, this briefcase exudes sophistication.",
                        "Cat Product 4":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Bird Product 1":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Bird Product 2":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Bird Product 3":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Bird Product 4":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Fish Product 1":
                            "Elevate your casual wardrobe with our premium quality cotton T-shirts.",
                        "Fish Product 2":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Fish Product 3":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag.",
                        "Fish Product 4":
                            "Embrace a relaxed yet stylish look with the UrbanCanvas Co. Casual Canvas Tote Bag."
                    };
                }

                function getProductPrice(productName) {
                    const prices = {
                        "Dog Product 1": 100,
                        "Dog Product 2": 110,
                        "Dog Product 3": 120,
                        "Dog Product 4": 125,
                        "Cat Product 1": 49.99,
                        "Cat Product 2": 39.99,
                        "Cat Product 3": 59.99,
                        "Cat Product 4": 29.99,
                        "Bird Product 1": 119.99,
                        "Bird Product 2": 99.99,
                        "Bird Product 3": 139.99,
                        "Bird Product 4": 89.99,
                        "Fish Product 1": 19.99,
                        "Fish Product 2": 29.99,
                        "Fish Product 3": 24.99,
                        "Fish Product 4": 24.99
                    };
                    return prices[productName] || 0;
                }

                displayCartItems();
                updateCartCount();
            });

        </script> <!-- Link to your JavaScript file -->
</body>

</html>
<?php
}
$conn->close();
?>