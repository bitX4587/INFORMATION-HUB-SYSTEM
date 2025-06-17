<?php
if (!isset($_SESSION['email'])) {
    header("Location: index.php");

    exit();
}

$counter_file = 'counter.txt';
$counter = 0;

if (file_exists($counter_file)) {
    $counter = (int)file_get_contents($counter_file);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="./output.css" rel="stylesheet">
    <!-- Font Awesome Free for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <br> <hr>

    <h1 class="text-4xl font-semibold mt-9 text-center">The Developer</h1>

    <p class="text-center mt-4">
        This page has been viewed <?= $counter; ?> times.
    </p>

    <div class="bg-gray-50 p-6 rounded shadow-md max-w-3xl ml-auto mr-auto mt-6 flex gap-6">
        <!-- Left side with photo and description -->
        <div class="flex-1">
            <img src="./assets/MARK.jpg" alt="Picture of me" class="rounded-full w-32 h-32 object-cover ml-auto mr-auto mb-4">
            <p class="text-center">
                Hello! I'm Mark Daniel Marbella Partoza.  
                I'm passionate about coding, developing new applications, and learning new technologies.
            </p>
        </div>

        <!-- Right side with contacts -->
        <div class="flex-1">
            <h2 class="text-2xl font-semibold">Contact Me</h2>
            <ul class="space-y-4 mt-4">
                <li><i class="fas fa-phone mr-2 text-blue-500"></i> +63 9627865397 (Philippines)</li>
                <li>
                    <i class="fas fa-envelope mr-2 text-blue-500"></i> 
                    <a class="cursor-pointer" target="_blank" href="mailto:partozamark2005@gmail.com">
                        partozamark2005@gmail.com
                    </a>
                </li>
                <li>
                    <a href="#" class="text-blue-500 mr-2"><i class="fab fa-facebook fa-lg mr-1"></i> Facebook</a>
                </li>
                <li>
                    <a href="#" class="text-blue-500 mr-2"><i class="fab fa-instagram fa-lg mr-1"></i> Instagram</a>
                </li>
                <li>
                    <a href="#" class="text-blue-500 mr-2"><i class="fab fa-twitter fa-lg mr-1"></i> X (Twitter)</a>
                </li>
                <li>
                    <a href="#" class="text-blue-500 mr-2"><i class="fab fa-telegram fa-lg mr-1"></i> Telegram</a>
                </li>
            </ul>
        </div>
    </div>

    <br> <br>

    <!-- Bottom section -->
    <div class="bg-blue-500 w-full py-4">
        <p class="text-center text-gray-50">
            &copy; <?= date("Y"); ?> MyProject — All Rights Reserved — Free from Copyright Infringement
        </p>
    </div>

</body>
</html>
