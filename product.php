<?php
include 'plugin/header.php';
include 'db.php';

// Get the logged-in user's ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Redirect to the login page if the user is not logged in
if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Fetch artworks including the artwork type
$result = $conn->query("
    SELECT 
        a.artwork_id, 
        a.user_id, 
        a.title, 
        a.description, 
        a.image_path, 
        a.year, 
        a.medium, 
        a.dimensions, 
        a.price, 
        a.is_available, 
        a.artwork_type, 
        u.username 
    FROM 
        artworks a
    JOIN 
        users u ON a.user_id = u.user_id
    WHERE 
        a.user_id = $user_id
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $medium = $_POST['medium'];
    $dimensions = $_POST['dimensions'];
    $price = !empty($_POST['price']) ? $_POST['price'] : null;
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $artwork_type = $_POST['artwork_type']; // Added field for artwork type

    // Handle file upload
    $upload_dir = './uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = time() . '-' . basename($_FILES['file']['name']);
        $dest_path = $upload_dir . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image_path = $dest_path;

            // Insert artwork data into the database
            $stmt = $conn->prepare("INSERT INTO artworks (user_id, title, description, image_path, year, medium, dimensions, price, is_available, artwork_type) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isssissdis", $user_id, $title, $description, $image_path, $year, $medium, $dimensions, $price, $is_available, $artwork_type);

                if (!$stmt->execute()) {
                    die("Error executing query: " . $stmt->error);
                }
                $stmt->close();
            } else {
                die("Error preparing query: " . $conn->error);
            }
        } else {
            die("File upload failed. Please try again.");
        }
    } else {
        die("File upload error: " . $_FILES['file']['error']);
    }

    // Redirect to the same page to display the new artwork
    header("Location: product.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Artworks</title>
    <style>
body {
font-family: Arial, sans-serif;
background: #f0d8a8;
margin: 0;
padding: 0;
}

h1 {
text-align: center;
color: #333;
margin-top: 20px;
}

.art-container {
width: 10px;
display: grid;
grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
gap: 20px; /* Space between grid items */
padding: 20px;
}

.art-card {
background-color: #fff;
border: 1px solid #ddd;
border-radius: 8px;
padding: 15px;
box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
display: flex;
flex-direction: column;
justify-content: space-between;
transition: transform 0.2s ease;
}

.art-card:hover {
transform: scale(1.05);
}

.art-image {
width: 100%;
height: 150px;
object-fit: cover;
border-radius: 8px;
}

.buttonss {
display: flex;
justify-content: space-between;
margin-top: 10px;
}

.buttonss a {
text-decoration: none;
padding: 8px 12px;
color: white;
border-radius: 5px;
font-size: 14px;
text-align: center;
}

.buttonss a:first-child {
background-color: #007bff;
}

.buttonss a:last-child {
background-color: #dc3545;
}

#openModal {
display: block;
width: 150px;
margin: 20px auto;
padding: 10px;
background: linear-gradient(135deg, #b8860b, #e6c200);
color: white;
border-radius: 5px;
text-align: center;
font-size: 16px;
border: none;
cursor: pointer;
}

#openModal:hover {
background: linear-gradient(135deg, #b8860b, #e6c200);
}

.modal {
display: none;
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background-color: rgba(0, 0, 0, 0.5);
align-items: center;
justify-content: center;
}

.modal-content {
background: linear-gradient(135deg, #2c3e50, #9b1c1c);
padding: 20px;
border-radius: 8px;
width: 90%;
max-width: 700px;
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
display: flex;
flex-direction: column;
gap: 10px;
}

.modal-content input,
.modal-content select,
.modal-content textarea {
width: 100%;
padding: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}

.modal-content textarea {
height: 100px;
}

.modal-content button {
padding: 10px 15px;
background: linear-gradient(135deg, #b8860b, #e6c200);
color: white;
border: none;
border-radius: 5px;
cursor: pointer;
width: 100%;
}

.modal-content button:hover {
background-color: #0056b3;
}

button {
padding: 10px 20px;
font-size: 16px;
font-weight: bold;
color: #fff;
background-color: #007BFF;
border: none;
border-radius: 5px;
cursor: pointer;
margin-top: 20px;
}

button:hover {
background-color: #0056b3;
}

label, h2 {
color: #e6c200;
}

@media (max-width: 768px) {
.modal-content {
padding: 15px;
}

.art-container {
grid-template-columns: 1fr; /* Single column on smaller screens */
}
}

    </style>
</head>
<body>
    <h1>Your Artworks</h1>
    <button id="openModal">Add Artwork</button>

    <div class="art-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="art-card">
                <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Artwork" class="art-image">
                <strong >
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p>Description: <?= htmlspecialchars($row['description']) ?></p>
                <p>Year: <?= htmlspecialchars($row['year']) ?></p>
                <p>Medium: <?= htmlspecialchars($row['medium']) ?></p>
                <p>Dimensions: <?= htmlspecialchars($row['dimensions']) ?></p>
                <p>Price: <?= $row['price'] ? '₱' . number_format($row['price'], 2) : 'Not available' ?></p>
                <p>Status: <?= $row['is_available'] ? 'Available' : 'Sold' ?></p>
                <p><strong>Artist:</strong> <?= htmlspecialchars($row['username']) ?></p>
                <p><strong>Artwork Type:</strong> <?= htmlspecialchars($row['artwork_type']) ?></p> <!-- Added artwork type -->
                </strong>
                <div class="buttonss">
                    <a href="manage_art/edit_arts.php?id=<?= $row['artwork_id'] ?>">Edit</a>
                    <a href="manage_art/delete_arts.php?id=<?= $row['artwork_id'] ?>" onclick="return confirm('Are you sure you want to delete this beautiful art?')">Delete</a>    
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No artworks uploaded yet.</p>
    <?php endif; ?>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <h2>Add Artwork</h2>
        <form action="product.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-row">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-row">
                <label for="year">Year:</label>
                <select id="year" name="year" required>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                </select>
            </div>

            <div class="form-row">
                <label for="medium">Medium:</label>
                <select id="medium" name="medium" required>
                    <option value="Oil on Canvas">Oil on Canvas</option>
                    <option value="Watercolor">Watercolor</option>
                    <option value="Acrylic">Acrylic</option>
                    <option value="Digital Art">Digital Art</option>
                    <option value="Charcoal">Charcoal</option>
                </select>
            </div>

            <div class="form-row">
                <label for="dimensions">Dimensions (inches):</label>
                <input type="text" id="dimensions" name="dimensions" placeholder="e.g. 24 x 36" required>
            </div>

            <div class="form-row">
                <label for="price">Price (optional):</label>
                <input type="number" id="price" name="price" step="0.01">
            </div>

            <div class="form-row">
                <label for="isAvailable">Available for Sale:</label>
                <input type="checkbox" id="isAvailable" name="is_available">
            </div>

            <div class="form-row">
                <label for="artwork_type">Artwork Type:</label>
                <select id="artwork_type" name="artwork_type" required>
                    <option value="Painting">Painting</option>
                    <option value="Sculpture">Sculpture</option>
                    <option value="Carving">Carving</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <div class="form-row">
                <label for="file">Upload Image:</label>
                <input type="file" id="file" name="file" accept="image/*" required>
            </div>

            <button type="submit">Add Artwork</button>
        </form>
    </div>
</div>


    <!-- Modal for full-size image -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span id="closeImageModal" style="font-size: 30px; cursor: pointer;">&times;</span>
            <img id="fullSizeImage" src="" alt="Full Size Artwork" style="max-width: 100%; max-height: 90vh; object-fit: contain;">
        </div>
    </div>

    <script>
        // Open the modal to add artwork
        const modal = document.getElementById('modal');
        const openModalBtn = document.getElementById('openModal');
        openModalBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });

        // Close the modal when clicking outside of it
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Full-screen image modal functionality
        const imageModal = document.getElementById('imageModal');
        const fullSizeImage = document.getElementById('fullSizeImage');
        const artworkImages = document.querySelectorAll('.art-image');
        const closeImageModal = document.getElementById('closeImageModal');

        // Show the full-size image in the modal when clicking on an image
        artworkImages.forEach(img => {
            img.addEventListener('click', function() {
                fullSizeImage.src = this.src; // Set the clicked image's src to the modal's image
                imageModal.style.display = 'flex'; // Show the modal
            });
        });

        // Close the full-size image modal when clicking the close button
        closeImageModal.addEventListener('click', () => {
            imageModal.style.display = 'none'; // Close the modal
        });

        // Close the full-size image modal when clicking outside of the image
        imageModal.addEventListener('click', (event) => {
            if (event.target === imageModal) {
                imageModal.style.display = 'none'; // Close the modal
            }
        });
    </script>
</body>
</html>
