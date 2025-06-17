<?php

$servername = "localhost";
$username = "root";
$password = ""; // Use your password
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle insert
if (isset($_POST['add'])) {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO credentials (Name, Email, PasswordHash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['email'], $hashed);
    $stmt->execute();

    if ($stmt->affected_rows == 1) {
        $msg = "User added successfully.";
        $msgClass = "bg-green-100 border border-green-400 text-green-900 mt-4 px-4 py-2 rounded mb-4";
    } else {
        $msg = "Error adding user. Email might already exist.";
        $msgClass = "bg-red-100 border border-red-400 text-red-900 mt-4 px-4 py-2 rounded mb-4";
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
    <h1 class="text-4xl text-center font-semibold mb-8 mt-2 dm-serif-text-regular">Register User</h1>

    <form action="" method="POST" class="flex flex-col space-y-4">
        <input 
            type="text" 
            name="name" 
            placeholder="Name" 
            class="p-2 rounded-md border border-blue-200 focus:outline-none focus:ring-2 focus:ring-black"
            required>
        

        <input 
            type="email" 
            name="email" 
            placeholder="Email (Unique)" 
            class="p-2 rounded-md border border-blue-200 focus:outline-none focus:ring-2 focus:ring-black"
            required>
        
        <input 
            type="password" 
            name="password" 
            placeholder="Password" 
            class="p-2 rounded-md border border-blue-200 focus:outline-none focus:ring-2 focus:ring-black"
            required>
        
        <button 
            type="submit" 
            name="add" 
            value="Login" 
            class="bg-blue-600 cursor-pointer text-gray-50 px-4 py-2 rounded-md hover:bg-blue-900 transition duration-300">
        Submit
        </button>
    </form>

    <button class="w-auto mt-4 ">
    <a 
        href="index.php" 
        class="text-gray-800 font-semibold hover:text-blue-100 transition duration-200">
        Back
    </a>
    </button>
</div>
    
   <?php if (isset($msg)): ?>
    <div class="<?php echo $msgClass; ?> fixed top-4 right-4 z-50 shadow-md">
        <?php echo htmlentities($msg); ?>
    </div>
<?php endif; ?>

</body>
</html>