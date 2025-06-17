<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");

    exit;
}

$servername = "localhost";
$username = "root";
$password = ""; // Use your password
$dbname = "student";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle insert
if (isset($_POST['add'])) {
    $note = $_POST['note'];
    $userId = $_SESSION['id']; // Currently authenticated user's id

    $stmt = $conn->prepare("INSERT INTO notes (note, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $note, $userId);
    $stmt->execute();
    $stmt->close();
    $_SESSION['msg'] = "Note added successfully.";
    $_SESSION['msgClass'] = "bg-green-100 border border-green-400 text-green-900 px-4 py-2 rounded mb-4";

    header("Location: notes-dashboard.php");

    exit();

}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();
    $_SESSION['msg'] = "Note deleted successfully.";
    $_SESSION['msgClass'] = "bg-green-100 border border-green-400 text-green-900 px-4 py-2 rounded mb-4";

    header("Location: notes-dashboard.php");

    exit();
}

if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $note = $_POST['note']; // string
    $userId = (int)$_SESSION['id']; // integer

    // First make sure this record belongs to the current user
    $stmt = $conn->prepare("SELECT id FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();

    if ($stmt->get_result()->num_rows == 1) {
        // Update
        $stmt = $conn->prepare("UPDATE notes SET note = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $note, $id, $userId);
        $stmt->execute();
        $_SESSION['msg'] = "Note updated successfully.";
        $_SESSION['msgClass'] = "bg-green-100 border border-green-400 text-green-900 px-4 py-2 rounded mb-4";
    } else {
        $msg = "Not authorized.";
    }
    $stmt->close();

    header("Location: notes-dashboard.php");

    exit();
}

// If we are in edit mode, fetch the record first
$edit = false;
$editId = 0;
$note = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $editId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $note = $row['note'];
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Information Hub</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 pt-15">
    <?php include 'navbar.php'; ?>
    <br> <br> <br>
    <h2 class="text-4xl font-semibold text-center">
        <?= $edit ? "Edit Note 📝" : "Add Note 📝" ?>
    </h2>

    <div class="bg-gray-50 p-6 rounded shadow-md space-y-4 max-w-md ml-auto mr-auto mt-6">
        <form method="POST" class="space-y-4">
            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= htmlentities($editId) ?>"> 
            <?php endif; ?>
            <textarea name="note" rows="5" maxlength="10000" class="p-2 border rounded w-full" required><?= htmlentities($note) ?> </textarea>

            <div class="flex justify-end">
                <button 
                    type="submit" 
                    name="<?= $edit ? "update" : "add" ?>" 
                    class="bg-blue-500 text-gray-50 px-4 py-2 mr-2 rounded hover:bg-blue-600 transition duration-300 cursor-pointer">
                    <?= $edit ? "Save Changes" : "Add Note" ?>
                </button>

                <?php if ($edit): ?>
                    <a 
                        href="notes-dashboard.php" 
                        class="bg-gray-500 text-gray-50 px-4 py-2 rounded hover:bg-gray-600 transition duration-300 cursor-pointer">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <hr class="mt-9 mb-4">

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="<?php echo $_SESSION['msgClass']; ?> fixed top-22 right-4 z-50 shadow-md">
            <?= htmlentities($_SESSION['msg']); ?>
        </div>
        <?php
        unset($_SESSION['msg']);
        unset($_SESSION['msgClass']);
        ?>
    <?php endif; ?>

    <h2 class="text-4xl font-semibold text-center mb-9">Your Notes 📝</h2>

    <?php 
$stmt = $conn->prepare("SELECT * FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='flex justify-center items-center p-4 rounded bg-gray-100'>";
    echo "<h1 class='text-xl text-gray-500'>No User Notes Found</h1>";
    echo "</div>";
} else {
    echo "<div class='m-6 columns-1 sm:columns-2 md:columns-3 gap-y-4 gap-x-4'>";
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class='bg-gray-200 p-6 mb-4 rounded shadow-md space-y-4 break-inside-avoid'>
            <p><?= nl2br(htmlentities($row['note'])); ?> </p>

            <div class='flex space-x-2 mt-4'>
                <a 
                    href='?edit=<?= htmlentities($row['id']); ?>' 
                    class='bg-blue-500 text-gray-50 px-4 py-2 rounded hover:bg-blue-600 transition duration-300 cursor-pointer'
                >Edit</a>

                <a 
                    href='?delete=<?= htmlentities($row['id']); ?>' 
                    onclick='return confirm("Are you sure?")'
                    class='bg-red-500 text-gray-50 px-4 py-2 rounded hover:bg-red-600 transition duration-300 cursor-pointer'
                >Delete</a>
            </div>
        </div>
        <?php
    }
    echo "</div>";
}
?>

    <?php include 'about.php'; ?>
</body>
</html>