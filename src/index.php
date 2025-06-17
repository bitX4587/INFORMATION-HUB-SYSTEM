<?php
// index.php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Store messages here

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $input_pass = $_POST['password'];

    // Prepare to select id, password, and name
    $stmt = $conn->prepare("SELECT id, PasswordHash, Name FROM credentials WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed, $name);
        $stmt->fetch();

        if (password_verify($input_pass, $hashed)) {
            // Success
            $_SESSION['id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;

            // Increment view counter upon login
            $counter_file = 'counter.txt';
            if (file_exists($counter_file)) {
                $counter = (int)file_get_contents($counter_file);
            } else {
                $counter = 0;
            }
            $counter++;
            file_put_contents($counter_file, $counter);

            header("Location: dashboard.php");

            exit();

        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "User not found.";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information Hub</title>
    <link href="./output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&display=swap" rel="stylesheet">
    <style>
    .dm-serif-text-regular {
        font-family: "DM Serif Text", serif;
        font-weight: 400;
        font-style: normal;
    }

    .dm-serif-text-regular-italic {
        font-family: "DM Serif Text", serif;
        font-weight: 400;
        font-style: italic;
    }
    </style>
</head>
<body class="min-h-screen bg-[linear-gradient(220deg,#1E3A8A_50%,#9333EA_50%)] flex flex-col backdrop-blur-sm items-center justify-center">
    <div class="bg-blue-300/70 backdrop-filter backdrop-blur-md p-7 rounded-xl shadow-md max-w-sm w-full">
    <h1 class="text-4xl text-center font-semibold mb-3 mt-2 dm-serif-text-regular">Information Hub</h1>
    <img src="./assets/img-edited.png" class="rounded w-full" alt="image">
    <form action="" method="POST" class="flex flex-col space-y-4 mt-3">
        <input 
            type="email" 
            name="email" 
            placeholder="Email" 
            class="p-2 rounded-md border border-blue-200 focus:outline-none focus:ring-2 focus:ring-black"
            required>
        
        <input 
            type="password" 
            name="password" 
            placeholder="Password" 
            class="p-2 rounded-md border border-blue-200 focus:outline-none focus:ring-2 focus:ring-black"
            required>
        
        <input 
            type="submit" 
            name="login" 
            value="Login" 
            class="bg-blue-600 cursor-pointer text-gray-50 px-4 py-2 rounded-md hover:bg-blue-900 transition duration-300">
    </form>

    <button class="w-auto mt-4 ">
    <a 
        href="register.php" 
        class="text-gray-800 font-semibold hover:text-blue-100 transition duration-200">
        Register
    </a>
    </button>
</div>
    <?php if ($message): ?>
    <div class="bg-red-100 border border-red-400 text-red-900 px-4 py-2 rounded shadow-md fixed top-4 right-4 z-50">
        <?= htmlentities($message) ?>
    </div>
<?php endif; ?>
</body>
</html>
