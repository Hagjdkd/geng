<?php include 'guest/header.php'; ?>
<!-- Hero Section -->
<section id="hero">
    <div class="hero-content">
        <h2>Welcome to the Art Gallery</h2>
        <p>Discover stunning artworks from talented artists</p>
        <a href="#portfolio" class="btn">Explore Our Gallery</a>
        <p>Explore stunning artworks and be part of our community.</p>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn">Register</a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="section-padding">
    <div class="container">
        <h2>About Our Gallery</h2>
        <p>We feature exquisite art pieces created by both emerging and established artists. Our gallery is dedicated to bringing you the finest art from all around the world.</p>
    </div>
</section>

<!-- Portfolio Section -->
<section id="portfolio" class="section-padding">
    <div class="container">
        <h2>Our Portfolio</h2>
        <p>Explore some of our curated works of art from different artists.</p>
        <div class="gallery">
            <div class="gallery-item">
                <img src="uploads/great.jpg" alt="Artwork 1" data-title="Greatness" data-artist="William shakesphere" data-date="January 1, 1824" onclick="zoomImage(this)">
            </div>
            <div class="gallery-item">
                <img src="uploads/mona.jpg" alt="Artwork 2" data-title="Mona Lisa" data-artist="Jhon jonard" data-date="February 14, 2024" onclick="zoomImage(this)">
            </div>
            <div class="gallery-item">
                <img src="uploads/nc.jpg" alt="Artwork 3" data-title="Nature's Charm" data-artist="Jerome garcia" data-date="March 3, 2024" onclick="zoomImage(this)">
            </div>
            <div class="gallery-item">
                <img src="uploads/sasa.jpg" alt="Artwork 4" data-title="Sassy Dreams" data-artist="Dreamers" data-date="April 22, 2024" onclick="zoomImage(this)">
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section-padding">
    <div class="container">
        <h2>Follow Us</h2>
        <p>Stay connected with us on our social media platforms:</p>
        <div class="social-media-icons">
            <a href="https://www.facebook.com" target="_blank" aria-label="Facebook">
                <img src="images/facebook.svg" alt="Facebook Logo" class="social-icon">
            </a>
            <a href="https://www.instagram.com" target="_blank" aria-label="Instagram">
                <img src="images/Instagram_icon.png" alt="Instagram Logo" class="social-icon">
            </a>
            <a href="https://t.me" target="_blank" aria-label="Telegram">
                <img src="images/Telegram_logo.svg" alt="Telegram Logo" class="social-icon">
            </a>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2024 Art Gallery | All Rights Reserved</p>
</footer>

<!-- Zoom Modal -->
 
<div class="zoom-modal" id="zoomModal" onclick="closeZoom()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <img id="zoomedImage" src="" alt="Zoomed Artwork">
        <div class="modal-details">
            <p id="artTitle"></p>
            <p id="artistName"></p>
            <p id="publishDate"></p>
        </div>
        <span class="close-button" onclick="closeZoom()">&times;</span>
    </div>
</div>


<style>
<style>
    /* General styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    section {
        padding: 50px 15px;
        text-align: center;
    }

    h2 {
        font-size: 2.5em;
        margin-bottom: 20px;
    }

    p {
        font-size: 1.2em;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin: 5px;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .container {
        max-width: 1200px;
        margin: auto;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .gallery-item {
        flex: 1 1 calc(25% - 30px);
        max-width: calc(25% - 30px);
    }

    .gallery-item img {
        width: 100%;
        border-radius: 8px;
    }

    footer {
        text-align: center;
        padding: 10px;
        background-color: #222;
        color: white;
    }

    /* Social Media Icons */
    .social-media-icons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
    }
    .modal-content{
        width: 400px;
        height: 340px;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 19px;
        box-shadow: 1px 4px 8px black;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        h2 {
            font-size: 2em;
        }

        p {
            font-size: 1em;
        }

        .gallery-item {
            flex: 1 1 calc(50% - 15px);
            max-width: calc(50% - 15px);
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.8em;
        }

        p {
            font-size: 0.9em;
        }

        .btn {
            font-size: 0.9em;
            padding: 8px 15px;
        }

        .gallery-item {
            flex: 1 1 100%;
            max-width: 100%;
        }

        .social-icon {
            width: 30px;
            height: 30px;
        }
    }
</style>

</style>

<script>
    // Function to display zoomed image with details
    function zoomImage(image) {
        const modal = document.getElementById('zoomModal');
        const zoomedImage = document.getElementById('zoomedImage');
        const artTitle = document.getElementById('artTitle');
        const artistName = document.getElementById('artistName');
        const publishDate = document.getElementById('publishDate');

        // Set the modal content
        zoomedImage.src = image.src;
        artTitle.textContent = `Title: ${image.getAttribute('data-title')}`;
        artistName.textContent = `Artist: ${image.getAttribute('data-artist')}`;
        publishDate.textContent = `Published: ${image.getAttribute('data-date')}`;

        // Display the modal
        modal.style.display = 'flex';
    }

    // Function to close the zoomed image modal
    function closeZoom() {
        const modal = document.getElementById('zoomModal');
        modal.style.display = 'none';
    }

    // Prevent modal content click from closing the modal
    document.querySelector('.modal-content').addEventListener('click', function(event) {
        event.stopPropagation();
    });
</script>
</body>
</html>
