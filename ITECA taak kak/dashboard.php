<?php
session_start();
require_once 'ppwerk.php'; 

$loggedInUserId = null;
$loggedInUsername = 'Guest';
$loggedInFirstname = '';
$loggedInLastname = '';

$loggedInProfilePicture = 'loginavatar.png';

if (isset($_SESSION['user_id'])) {
    $loggedInUserId = $_SESSION['user_id'];

    
    $stmt = $conn->prepare("SELECT Username, Firstname, Lastname, ProfilePicture FROM Users WHERE User_ID = ?");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $stmt->bind_result($dbUsername, $dbFirstname, $dbLastname, $dbProfilePicture);
    $stmt->fetch();
    $stmt->close();

    $loggedInUsername = $dbUsername ?? 'User';
    $loggedInFirstname = $dbFirstname ?? '';
    $loggedInLastname = $dbLastname ?? '';

    
    if (!empty($dbProfilePicture) && file_exists($dbProfilePicture)) {
        $loggedInProfilePicture = htmlspecialchars($dbProfilePicture);
    }
    
} else {
    
    header("Location: Trade4US.html");
    exit();
}

$message = null;
$messageType = null;
if (isset($_SESSION['message'])) {
    $message = htmlspecialchars($_SESSION['message']);
    $messageType = htmlspecialchars($_SESSION['message_type'] ?? 'info');
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}


$products = [];
$sqlProducts = "SELECT
                    p.ProductID,
                    p.ProductName,
                    p.Description,
                    p.Price,
                    p.ImagePath,
                    p.SubmissionDate,
                    u.Username AS SellerUsername,
                    u.Firstname AS SellerFirstname,
                    u.Lastname AS SellerLastname,
                    u.ProfilePicture AS SellerProfilePicture
                FROM
                    Products p
                JOIN
                    Users u ON p.UserID = u.User_ID
                WHERE
                    p.Status = 'available'
                ORDER BY
                    p.SubmissionDate DESC";

$resultProducts = $conn->query($sqlProducts);

if ($resultProducts && $resultProducts->num_rows > 0) {
    while ($row = $resultProducts->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Trade4US</title>

    <link rel="stylesheet" href="BillieStylish.css">
    <link href="https://fonts.googleapis.com/css2?family=Yrsa:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

    </head>
<body>
    <div class="gooeyElements">
        <div class="left-section">
            <img id="Logo" src="TRADE4US_1.png" alt="Trade4US Logo">
            <h1 id="titlecard">Trade4US</h1>
            <textarea placeholder="Search..."></textarea>
        </div>

        <div class="nav-buttons">
            <?php if ($loggedInUserId):?>
                <a href="#" id="openUploadProductModalBtn">
                    <button id="uploadProductBtn">
                        <span>Sell Product</span>
                    </button>
                </a>

                <div class="header-profile-container" id="openProfileModalBtn">
                    <img src="<?php echo $loggedInProfilePicture; ?>" alt="Your Profile" class="header-profile-pic">
                </div>

                <a href="logout.php">
                    <button id="logoutBtn">
                        <span>Logout</span>
                    </button>
                </a>
            <?php else:?>
                <button type="button" id="login">
                    <img id="loginIcon" src="loginavatar.png" alt="Login" />
                    <span>Login</span>
                </button>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div id="flashMessage" class="flash-message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="main-content-wrapper">
        <div class="Itemware">
            <?php if (empty($products)): ?>
                <p class="no-products-message">No products available for display yet. Check back soon!</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img class="product-image" src="<?php echo htmlspecialchars($product['ImagePath']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        <div class="product-details">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                            <p class="product-price">R<?php echo htmlspecialchars(number_format($product['Price'], 2)); ?></p>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['Description'], 0, 100)) . (strlen($product['Description']) > 100 ? '...' : ''); ?></p>
                            <div class="seller-info">
                                <?php
                                
                                $profilePicPath = (!empty($product['SellerProfilePicture']) && file_exists($product['SellerProfilePicture']))
                                                            ? htmlspecialchars($product['SellerProfilePicture'])
                                                            : 'loginavatar.png'; 
                                ?>
                                <img class="seller-profile-pic" src="<?php echo $profilePicPath; ?>" alt="<?php echo htmlspecialchars($product['SellerUsername']); ?> Profile">
                                <span class="seller-name">Listed by: <?php echo htmlspecialchars($product['SellerFirstname'] . ' ' . $product['SellerLastname']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="loginSection" class="auth-section active">
                <h2>Login</h2>
                <form action="Login.php" method="POST">
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required><br><br>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                    <button type="submit">Login</button>
                </form>
                <p style="text-align: center; margin-top: 15px;">Don't have an account? <a href="#" id="switchToRegister" class="modal-link">Register here</a></p>
            </div>
            <div id="registerSection" class="auth-section">
                <h2>Register</h2>
                <form action="Register.php" method="POST">
                    <label for="regFirstName"><u>First Name</u>:</label><br>
                    <input type="text" id="regFirstName" name="firstname" required>
                    <label for="regLastName"><u>Last Name</u>:</label><br>
                    <input type="text" id="regLastName" name="lastname" required>
                    <label for="regUsername"><u>Username</u>:</label><br>
                    <input type="text" id="regUsername" name="username" required>
                    <label for="regEmail"><u>Email</u>:</label><br>
                    <input type="email" id="regEmail" name="email" required>
                    <label for="regPassword"><u>Password</u>:</label><br>
                    <input type="password" id="regPassword" name="password" required>
                    <label for="confirmRegPassword"><u>Confirm Password</u>:</label><br>
                    <input type="password" id="confirmRegPassword" name="confirm_password" required>
                    <button type="submit">Register</button>
                </form>
                <p style="text-align: center; margin-top: 15px;">Already have an account? <a href="#" id="switchToLogin" class="modal-link">Login here</a></p>
            </div>
        </div>
    </div>

    <div id="profilePicModal" class="modal">
        <div class="modal-content">
            <span class="closeProfilePicModalBtn">&times;</span>
            <div class="profile-upload-section">
                <h2>Your Profile</h2>
                <div class="profile-pic-container">
                    <img id="modalUserProfilePicDisplay" src="<?php echo $loggedInProfilePicture; ?>" alt="Your Profile Picture" class="seller-profile-pic large-profile-pic">
                </div>

                <?php
                
                if ($loggedInProfilePicture == 'loginavatar.png'):
                ?>
                <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data">
                    <label for="profilePicUpload" class="custom-file-upload">
                        Choose New Picture
                    </label>
                    <input type="file" name="profilePic" id="profilePicUpload" accept="image/*" required>
                    <button type="submit" class="upload-btn">Add Profile Picture</button>
                </form>
                <?php else: ?>
                    <p>Your profile picture is set.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div id="uploadProductModal" class="modal">
        <div class="modal-content">
            <span class="closeUploadProductModalBtn">&times;</span>
            <h2>Upload a New Product</h2>
            <form action="upload_product.php" method="POST" enctype="multipart/form-data">
                <label for="productName">Product Name:</label><br>
                <input type="text" id="productName" name="productName" required><br><br>

                <label for="productDescription">Description:</label><br>
                <textarea id="productDescription" name="productDescription" rows="5" required></textarea><br><br>

                <label for="productPrice">Price (R):</label><br>
                <input type="number" id="productPrice" name="productPrice" step="0.01" min="0" required><br><br>

                <label for="productImage">Product Image:</label><br>
                <input type="file" id="productImage" name="productImage" accept="image/*" required><br><br>

                <button type="submit">List Product</button>
            </form>
        </div>
    </div>

    <script src="admortum.js"></script>
    <script src="mainscript.js"></script>
</body>
</html>