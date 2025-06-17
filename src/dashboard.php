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
    $name = $_POST['name'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $course = $_POST['course'];
    $status = $_POST['status'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $userId = $_SESSION['id']; // <- Currently authenticated user's id

    $stmt = $conn->prepare("INSERT INTO studentdetails (name, age, dob, address, course, status, religion, nationality, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $name, $age, $dob, $address, $course, $status, $religion, $nationality, $userId);
    $stmt->execute();
    $stmt->close();

    $msg = "Student added successfully.";
    $msgClass = "bg-green-100 border border-green-400 text-green-900 px-4 py-2 rounded mb-4";

}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM studentdetails WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");

    exit();
}


if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name']; // string
    $age = $_POST['age']; // string
    $dob = $_POST['dob']; // string
    $address = $_POST['address']; // string
    $course = $_POST['course']; // string
    $status = $_POST['status']; // string
    $religion = $_POST['religion']; // string
    $nationality = $_POST['nationality']; // string
    $userId = (int)$_SESSION['id']; // integer

    // First make sure this record belongs to the current user
    $stmt = $conn->prepare("SELECT id FROM studentdetails WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();

    if ($stmt->get_result()->num_rows == 1) {
        // Update
        $stmt = $conn->prepare("UPDATE studentdetails SET name = ?, age = ?, dob = ?, address = ?, course = ?, status = ?, religion = ?, nationality = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssssssii", $name, $age, $dob, $address, $course, $status, $religion, $nationality, $id, $userId);
        $stmt->execute();
        $msg = "Record updated successfully.";
    } else {
        $msg = "Not authorized.";
    }
    $stmt->close();

    header("Location: dashboard.php");

    exit();
}

// If we are in edit mode, fetch the record first
$edit = false;
$editId = 0;
$name = '';
$age = '';
$dob = '';
$address = '';
$course = '';
$status = '';
$religion = '';
$nationality = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $editId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM studentdetails WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $age = $row['age'];
        $name = $row['name'];
        $dob = $row['dob'];
        $address = $row['address'];
        $course = $row['course'];
        $status = $row['status'];
        $religion = $row['religion'];
        $nationality = $row['nationality'];
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
    <style>
        .dark {
            background-color: black;
        }
    </style>
</head>
<body class="bg-gray-100 pt-15">
    
    <?php include 'navbar.php'; ?>
        <br> <br> <br>
        <h2 class="text-4xl font-semibold text-center">
            <?= $edit ? "Edit Record 📒" : "Add Record 📒" ?>
        </h2>

        <?php
        // Sample option arrays (update with your actual data sources)
        $barangays = [
            // Calbayog District
            "Acedillo", "Aguit-itan", "Alibaba", "Anislag", "Awang East", "Awang West",
            "Bagacay", "Bagong Lipunan", "Balud", "Basud", "Bontay", "Buenavista",
            "Cacaransan", "Cagbanayacao", "Cagboborac", "Cagsalaosao", "Cahumpan",
            "Calocnayan", "Canhumadac", "Capoocan", "Carayman", "Carmen", "Central",
            "Cogon", "Dagum", "Dinawacan", "Esperanza", "Gabay", "Gadgaran",
            "Gasdo", "Geraga-an", "Guimbaoyan Norte", "Guimbaoyan Sur", "Guin-on",
            "Hamorawon", "Helino", "Hibabngan", "Hibatang", "Higasaan", "Himalandrog",
            "Jacinto", "Jimautan", "Kalilihan", "Kilikili", "La Paz", "Langoyon",
            "Lonoy", "Looc", "Mabini I", "Mancol", "Matobato", "Maybog", "Maysalong",
            "Migara", "Naga", "Naguma", "Navarro", "Nijaga", "Obrero", "Olera",
            "Osmeña", "Pagbalican", "Palanas", "Palanogan", "Panonongan", "Patong",
            "Payahan", "Pinamorotan", "Rawis", "Rizal I", "Roxas I", "Salvacion",
            "San Antonio", "San Isidro", "San Jose", "San Policarpio", "Saputan",
            "Sinantan", "Tabawan", "Tanval", "Tapa-e", "Tigbe", "Trinidad",
            "Victory", "Villahermosa",

            // Tinambacan District
            "Amampacang", "Ba-ay", "Bante", "Bantian", "Binaliw", "Bugtong",
            "Caglanipao Sur", "Cagmanipes Norte", "Cagmanipes Sur",
            "Cagnipa", "Cag-olango", "Cangomaod", "Danao I", "Danao II",
            "Malaga", "Malajog", "Malayog", "Malopalo", "Manguino-o",
            "Marcatubig", "Peña", "Saljag", "San Joaquin",
            "Tinambacan Norte", "Tinambacan Sur", "Tinaplacan",
            "Tomaliguez",

            // Oquendo District
            "Baja", "Bayo", "Begaho", "Cabacungan", "Cabatuan",
            "Cabicahan", "Cabugawan", "Cag-anahaw", "Cag-anibong",
            "Cagbayang", "Cagbilwang", "Capacuhan", "Catabunan",
            "Caybago", "Dawo", "De Victoria", "Dinabongan",
            "Dinagan", "Hugon Rosales", "Jose A. Roño", "Lapaan",
            "Libertad", "Limarayon", "Longsob", "Mabini II",
            "Macatingog", "Mag-ubay", "Mantaong", "Manuel Barral Sr.",
            "Mawacat", "Nabang", "Obo-ob", "Oquendo", "Panlayahan",
            "Panoypoy", "Pilar", "Quezon", "Rizal II", "Roxas II",
            "San Rufino", "Sigo", "Sinidman Occidental",
            "Sinidman Oriental", "Talahiban", "Tarabucan"
        ];

        $courses = [
            "BS in Computer Engineering",
            "BS in Information Technology",
            "BS in Civil Engineering",
            "BS in Electronics and ComEng",
            "BS in Mechanical Engineering",
            "BS in Architectural Design",
            "BS in Business Administration",
            "BS in Accountancy",
            "BS in Finance",
            "BS in Marketing",
            "BS in Tourism Management",
            "BS in Nursing",
            "BS in Midwifery",
            "BS in Psychology",
            "BS in Sociology",
            "BS in Criminology",
            "BS in Public Administration",
            "BS in Agriculture",
            "BS in Fisheries",
            "BS in Forestry",
            "BS in Secondary Education", 
            "BS in Elementary Education",
            "BS in Special Education",
            "BS in Guidance and Counseling",
            "BS in Biology",
            "BS in Chemistry",
            "BS in Math",
            "BS in Arts and Design",
            "BS in Communication Arts",
            "BS in Media Studies",
            "BS in Liberal Arts",
            "BS in Philosophy",
            "BS in History",
            "BS in Foreign Service",
            "BS in Social Work",
            "BS in Entrepreneurship",
            "BS in Operations Management"
        ];

        $statuses = [
            "Single",
            "Taken",
            "Married",
            "Widowed",
            "Separated",
            "Divorced",
            "Dead",
            "Alumni",
            "Suspended",
            "On Vacation",
            "Probation",
            "Other"
        ];

        $religions = [
            "Roman Catholic",
            "Christian",
            "Catholic",
            "Protestant",
            "Buddhist",
            "Muslim",
            "Hindu",
            "Atheist",
            "Agnostic",
            "Other"
        ];

        $nationalities = [
            "Filipino",
            "American",
            "Chinese",
            "Japanese",
            "Korean",
            "Australian",
            "British",
            "Canadian",
            "German",
            "French",
            "Indian",
            "Mexican",
            "Russian",
            "Other"
        ];
        ?>

        <div class="bg-gray-50 p-6 rounded shadow-md space-y-4 max-w-md mx-auto mt-6">
        <form method="POST" class="space-y-4">
            <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?= htmlentities($editId) ?>">
            <?php endif; ?>

            <?php
            $fields = [
                ['id'=>'name','label'=>'Name','type'=>'text','value'=>$name],
                ['id'=>'age','label'=>'Age','type'=>'select','options'=>range(1, 100),'value'=>$age],
                ['id'=>'dob','label'=>'Date of Birth','type'=>'date','value'=>$dob],
                ['id'=>'address','label'=>'Address','type'=>'select','options'=>$barangays,'value'=>$address],
                ['id'=>'course','label'=>'Course','type'=>'select','options'=>$courses,'value'=>$course],
                ['id'=>'status','label'=>'Status','type'=>'select','options'=>$statuses,'value'=>$status],
                ['id'=>'religion','label'=>'Religion','type'=>'select','options'=>$religions,'value'=>$religion],
                ['id'=>'nationality','label'=>'Nationality','type'=>'select','options'=>$nationalities,'value'=>$nationality]
            ];

            foreach ($fields as $f): ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                <label for="<?= $f['id'] ?>" class="font-semibold sm:w-32 mb-2 sm:mb-0"><?= $f['label'] ?></label>
                <?php if ($f['type'] === 'select'): ?>
                    <select id="<?= $f['id'] ?>" name="<?= $f['id'] ?>" required class="p-2 border rounded flex-grow max-w-md">
                    <option value=""><?= "Select {$f['label']}" ?></option>
                    <?php foreach ($f['options'] as $opt): ?>
                        <?php
                        $val = is_int($opt) ? $opt : $opt;
                        $sel = ($f['value'] == $val) ? 'selected' : '';
                        ?>
                        <option value="<?= htmlentities($val) ?>" <?= $sel ?>><?= htmlentities($opt) ?></option>
                    <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input 
                    id="<?= $f['id'] ?>" 
                    type="<?= $f['type'] ?>" 
                    name="<?= $f['id'] ?>" 
                    placeholder="<?= "Enter {$f['label']}" ?>" 
                    required 
                    class="p-2 border rounded flex-grow max-w-md" 
                    value="<?= htmlentities($f['value']) ?>">
                <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-end">
            <button 
                type="submit" 
                name="<?= $edit ? 'update' : 'add' ?>" 
                class="bg-blue-500 text-white px-4 py-2 mr-2 rounded hover:bg-blue-600 transition duration-300"
            ><?= $edit ? 'Save Changes' : 'Add Record' ?></button>

            <?php if ($edit): ?>
                <a href="dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition duration-300">
                Cancel
                </a>
            <?php endif; ?>
            </div>
        </form>
        </div>

        <hr class="mt-9 mb-4">

        <?php if (isset($msg)): ?>
            <div class="<?php echo $msgClass; ?>"> 
                <?= htmlentities($msg) ?>
            </div>
        <?php endif; ?>

    <div class="p-4">
        
        <h2 class="text-4xl font-semibold text-center mb-9">Archive 🏦</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-4">
        <?php
        $stmt = $conn->prepare("SELECT studentdetails.*, credentials.name AS user_name FROM studentdetails JOIN credentials ON studentdetails.user_id = credentials.id WHERE studentdetails.user_id = ?");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "<h1 class='text-xl col-span-3 text-center text-gray-500 p-4 rounded bg-gray-100'>No User Notes Found</h1>";
        } else {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class='bg-gray-50 p-4 rounded shadow-md space-y-4 relative'>
                    <ul class='space-y-2'>
                        <li><strong>Name:</strong> <?= htmlentities($row['name']); ?> </li>
                        <li><strong>Age:</strong> <?= htmlentities($row['age']); ?> </li>
                        <li><strong>Date of Birth:</strong> <?= htmlentities($row['dob']); ?> </li>
                        <li><strong>Address:</strong> <?= htmlentities($row['address']); ?> </li>
                        <li><strong>Course:</strong> <?= htmlentities($row['course']); ?> </li>
                        <li><strong>Status:</strong> <?= htmlentities($row['status']); ?> </li>
                        <li><strong>Religion:</strong> <?= htmlentities($row['religion']); ?> </li>
                        <li><strong>Nationality:</strong> <?= htmlentities($row['nationality']); ?> </li>
                    </ul>

                    <div class='flex space-x-2 mt-4'>
                        <a 
                            href='?edit=<?= htmlentities($row['id']); ?>' 
                            class='bg-blue-500 text-gray-50 px-4 py-2 rounded hover:bg-blue-600 transition duration-300'
                        >Edit</a>

                        <a 
                            href='?delete=<?= htmlentities($row['id']); ?>' 
                            onclick='return confirm("Are you sure?")'
                            class='bg-red-500 text-gray-50 px-4 py-2 rounded hover:bg-red-600 transition duration-300'
                        >Delete</a>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        </div>

    </div>
    <?php include 'about.php'; ?>
    
</body>
</html>
