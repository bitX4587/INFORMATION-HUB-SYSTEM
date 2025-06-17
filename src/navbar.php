<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="./output.css" rel="stylesheet">
    <title>Menu</title>
</head>
<body>
   <div class="fixed top-0 left-0 w-full z-50 bg-blue-500 text-gray-50 shadow-md">
    <div class="px-4 py-4 flex items-center justify-between relative">
        <h1 class="text-2xl font-semibold flex items-center space-x-2 uppercase">
            <i class="fas fa-user mr-2"></i> <?= htmlentities($_SESSION['name']); ?>
        </h1>

        <!-- Hamburger Menu Button for Small Screens -->
        <button id="hamburgerBtn" class="md:hidden p-2 rounded-md bg-blue-700 hover:bg-blue-900 transition duration-300">
            ☰ Menu
        </button>

        <!-- Menu Links (Dropdown) -->
        <div id="menuLinks" class="absolute right-4 top-16 mt-2 bg-blue-500 rounded shadow-md p-4 space-y-2 hidden z-50">
            <a 
                href='dashboard.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 block text-center'
            >
                Dashboard
            </a>

            <a 
                href='notes-dashboard.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 block text-center'
            >
                Notes
            </a>

            <a 
                href='logout.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 block text-center'
            >
                Logout
            </a>
        </div>

        <!-- Menu links for large screens -->
        <div id="menuLinksDesktop" class="hidden md:flex md:items-center md:ml-4 gap-4">
            <a 
                href='dashboard.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 text-center'
            >
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>

            <a 
                href='notes-dashboard.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 text-center'
            >
                <i class="fas fa-sticky-note mr-2"></i> Notes
            </a>

            <a 
                href='logout.php'
                class='bg-blue-700 px-4 py-2 rounded hover:bg-blue-900 transition duration-300 text-center'
            >
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>

    </div>
</div>

<script>
    // Toggle visibility for small screens and show console.log + alert
    document.getElementById('hamburgerBtn').addEventListener('click',(e) => {
        e.stopPropagation();
        console.log("You Clicked Me")
        document.getElementById('menuLinks').classList.toggle('hidden'); 
    });

    // Close drop-down if clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest("#hamburgerBtn") && !e.target.closest("#menuLinks")) {
            document.getElementById("menuLinks").classList.add("hidden");

        }
    });
</script>

</body>
</html>
