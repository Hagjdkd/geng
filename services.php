<?php
include 'plugin/header.php';
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Redirect to the login page if the user is not logged in
if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Hardcoded services for now
$services = [
    [
        'service_name' => 'Art Exhibitions & Showcases',
        'description' => 'Participate in online exhibitions and showcase your art to a global audience.'
    ],
    [
        'service_name' => 'Commissioned Artworks',
        'description' => 'Get custom artwork tailored to your needs or preferences through commissioned work.'
    ],
    [
        'service_name' => 'Art Marketing & Promotion',
        'description' => 'Boost your presence with professional marketing and promotional strategies.'
    ],
    [
        'service_name' => 'Artist Profile & Add arts',
        'description' => 'Create and manage a professional artist profile and portfolio to showcase your work.'
    ],
    [
        'service_name' => 'Art Consultation & Interior Design',
        'description' => 'Consult with our experts for personalized art recommendations and interior design solutions.'
    ],
    [
        'service_name' => 'Art Workshops & Classes',
        'description' => 'Learn new techniques and improve your skills with our online workshops and classes.'
    ],
    [
        'service_name' => 'Purchasable Artworks',
        'description' => 'Purchase art directly from the artist or through exclusive platforms.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - My Art Portfolio</title>
    <link rel="stylesheet" href="des/styles.css">
    <style>
        /* Add CSS */
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .services-container {
            padding: 20px;
        }

        .services-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .services-header h2 {
            font-size: 32px;
            color: #333;
        }

        .services-header p {
            font-size: 18px;
            color: #777;
        }

        .service-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .service-item h3 {
            font-size: 24px;
            color: #333;
        }

        .service-item p {
            font-size: 16px;
            color: #555;
        }

        footer {
            background-color: #333;
            padding: 20px;
            color: white;
            text-align: center;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <section id="services" class="services-container">
        <div class="main-content">
            <div class="services-header">
                <h2>Our Services</h2>
                <p>Explore the range of services we offer to help you showcase, sell, and promote your art.</p>
            </div>

            <?php
            // Loop through the hardcoded services and display them
            foreach ($services as $service) {
                echo '<div class="service-item">';
                echo '<h3>' . htmlspecialchars($service['service_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($service['description']) . '</p>';
                echo '</div>';
            }
            ?>

        </div>
    </section>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 My Art Portfolio. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
