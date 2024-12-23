<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery - Explore Beautiful Art</title>
    <style>
      /* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background:linear-gradient(135deg, #f0d8a8, #9b1c1c); /* Light sky blue */
    color: #333;
}

/* Header */
header {
    background: linear-gradient(135deg, #2c3e50, #9b1c1c);
    padding: 20px 0;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.navbar .logo h1 {
    color: white;
}

nav ul {
    list-style-type: none;
}

nav ul li {
    display: inline;
    margin: 0 15px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 18px;
}

/* Hero Section */
#hero {
    background: url('uploads/nc.jpg') no-repeat center center/cover;
    padding: 100px 0;
    color: white;
    text-align: center;
}

.hero-content h2 {
    font-size: 48px;
    margin-bottom: 10px;
}

.hero-content p {
    font-size: 24px;
    margin-bottom: 30px;
}

.btn {
    background-color: #e74c3c;
    padding: 10px 30px;
    color: white;
    text-decoration: none;
    font-size: 18px;
    border-radius: 5px;
}

/* About Section */
#about {
    
    text-align: center;
    padding: 50px 0;
}

#about h2 {
    font-size: 36px;
    margin-bottom: 15px;
}

#about p {
    font-size: 20px;
}
#about #portfolio #contact{
    background:linear-gradient(135deg, #f0d8a8, #9b1c1c); /* Light sky blue */
}
/* Portfolio Section */
#portfolio {
    padding: 50px 0;
}

#portfolio h2 {
    text-align: center;
    font-size: 36px;
    margin-bottom: 30px;
}

#portfolio p {
    text-align: center;
    font-size: 20px;
    margin-bottom: 30px;
}

.gallery {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.gallery-item img {
    width: 250px;
    height: 250px;
    object-fit: cover;
    border-radius: 10px;
}

/* Contact Section */
#contact {
    
    padding: 50px 0;
    text-align: center;
}

#contact h2 {
    font-size: 36px;
    margin-bottom: 15px;
}

#contact p {
    font-size: 20px;
    margin-bottom: 30px;
}

#contact form {
    display: inline-block;
    max-width: 400px;
    text-align: left;
}

#contact form input, #contact form textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#contact form button {
    background-color: #e74c3c;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Footer */
footer {
    background-color: #2c3e50;
    color: white;
    text-align: center;
    padding: 20px 0;
}

/* Section Padding */
.section-padding {
    padding: 50px 0;
}
.social-media-icons {
    display: flex;
    gap: 15px;
    justify-content: center;
    align-items: center;
}

.social-icon {
    width: 40px;
    height: 40px;
    transition: transform 0.3s ease, filter 0.3s ease;
}

.social-icon:hover {
    transform: scale(1.2);
    filter: brightness(1.2);
}
/* Styles for image zoom modal */
.zoom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.zoom-modal img {
    max-width: 90%;
    max-height: 90%;
    border: 5px solid white;
    border-radius: 5px;
}
.zoom-modal.close {
    display: flex;
}
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="navbar">
            <div class="logo">
                <h1>Art Gallery</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="aboutus.php">Developers</a></li>
                    <li><a href="index.php">Portfolio</a></li>
                </ul>
            </nav>
        </div>
    </header>
