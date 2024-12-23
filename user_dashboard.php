<?php
include 'plugin/header.php';
include 'db.php';





// Fetch artworks and their transaction status
$query = "
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
        u.username,
        u.user_img,
        a.artwork_type,  -- Added artwork type for filtering
        t.transaction_id,
        t.is_confirmed
    FROM 
        artworks a
    JOIN 
        users u ON a.user_id = u.user_id
    LEFT JOIN 
        transactions t ON a.artwork_id = t.artwork_id
    WHERE 
        (t.transaction_id IS NULL OR t.is_confirmed = 0)  -- Show only available or pending artworks
";

$artworks = $conn->query($query);

$logged_in_user_id = $_SESSION['user_id'];



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Portfolio Dashboard</title>
   <style>
    body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background:linear-gradient(135deg, #f0d8a8, #9b1c1c); /* Light sky blue */
}

.dashboard-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  padding: 20px;
  width: 100%;
}

.portfolio-header {
  text-align: center;
  margin-bottom: 30px;
}

.art-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.art-card {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
  padding: 0.5rem;
}

.art-card img {
  max-width: 100%;
  border-radius: 8px;
  margin-bottom: 15px;
}

.art-info h3 {
  font-size: 18px;
  color: #333;
}
.artist-profile {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 10px auto;
        }

        .artist-profile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

  .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background: rgba(0, 0, 0, 0.6);
  }

  .modal-content {
    background: linear-gradient(135deg, #ffffff, #f3f4f6);
margin: 10% auto;
padding: 20px;
border-radius: 10px;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
width: 90%;
max-width: 600px;
position: relative;
text-align: left; /* Align the content to the left */
display: flex;
flex-direction: column;
}

  .close {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      position: absolute;
      top: 10px;
      right: 25px;
  }

  .close:hover,
  .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
  }

  .art-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
  }

  .art-card img {
      width: 100%;
      height: auto;
      border-radius: 5px;
  }
  .art-card{
      display: flex; 
      flex-direction: column;
     background: rgba(255, 255, 255, 0.85);.
  }

  .art-info {
      padding: 10px;
      text-align: center;
  }

  #sb {
      width: 60%;
      height: 40px;
  }

.modal-content textarea,
.modal-content select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
}
.modal-content button {
    background: linear-gradient(135deg, #b8860b, #e6c200);
  color: #fff;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s ease;
  margin-bottom: 5px;
  margin-right:80% ;
}
.modal-content button:hover {
  background-color: #b8860b;
}

.modal-content a {
  text-decoration: none;
  color: inherit;
}

#reviewButton {
  margin-top: 20px;
  background-color: #2196F3;
  border: none;
  padding: 10px 20px;
  color: #fff;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}
#reviewButton:hover {
  background-color: #0b7dda;
}

#reviewSection div {
  text-align: left;
  padding: 10px;
  background: #f9f9f9;
  margin: 10px 0;
  border-radius: 5px;
}
.modal-content button {
  background-color: #4CAF50;
  color: #fff;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}
.modal-content img {
width: 100%;
max-width: 100%; /* Ensures the image does not exceed container width */
height: auto;
max-height: 500px; /* Optional: Set a maximum height for the image */
object-fit: contain; /* Ensures the image scales properly within the container */
}
.modal-content h2,
.modal-content p {
margin: 5px 0;
}
.category-filter {
  margin-top: 10px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.category-filter button {
  padding: 8px 15px;
  background: linear-gradient(135deg, #b8860b, #e6c200); /* Gradient background */
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.category-filter button:hover {
    background:  #e6c200;
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background: rgba(0, 0, 0, 0.6);
  padding-top: 60px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
.search-container {
    display: flex;
    align-items: center;
    width: 500px;
    height: 40px;
    background: white;
    border: 1px solid #ddd; /* Subtle border */
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.sc {
    flex: 1;
    border: none;
    padding: 0 12px;
    font-size: 16px;
    color: #333;
    outline: none;
}
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #f0d8a8, #9b1c1c);
}

.dashboard-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  padding: 1rem;
  width: 100%;
}

.portfolio-header {
  text-align: center;
  margin-bottom: 2rem;
}

.art-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.art-card {
  background-color: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.art-card img {
  max-width: 100%;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.artist-profile {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin: 0 auto 0.5rem auto;
}

.artist-profile img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.search-container {
  display: flex;
  align-items: center;
  width: 90%;
  max-width: 500px;
  height: 2.5rem;
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 auto 1rem auto;
}

.sc {
  flex: 1;
  border: none;
  padding: 0 0.5rem;
  font-size: 1rem;
  color: #333;
  outline: none;
}

.search-button {
  background: linear-gradient(135deg, #b8860b, #e6c200);
  color: white;
  border: none;
  padding: 0 1rem;
  font-size: 1rem;
  cursor: pointer;
}

.modal-content {
  background: linear-gradient(135deg, #ffffff, #f3f4f6);
  margin: 5% auto;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 500px;
}

@media (max-width: 768px) {
  .portfolio-header h2 {
    font-size: 1.5rem;
  }

  .art-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.5rem;
  }

  .art-card {
    padding: 0.5rem;
  }

  .search-container {
    width: 100%;
  }

  .modal-content {
    margin: 10% auto;
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .portfolio-header h2 {
    font-size: 1.25rem;
  }

  .art-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  }

  .search-container {
    flex-direction: column;
    height: auto;
    padding: 0.5rem;
  }

  .search-button {
    width: 100%;
    margin-top: 0.5rem;
  }
}

   </style>
</head>
<body>
    <section class="dashboard-container">
        <div class="main-content">
            <div class="portfolio-header">
                <h2>Art Portfolio</h2>
                <p>Explore my collection of artworks.</p>
                
                <div class="search-container">
        <input type="search" id="sb" name="search-bar" class="sc" placeholder="Search for art">
        <button class="search-button">🔍</button>
    </div>
                
                <!-- Category Filter -->
                <div class="category-filter">
                    <button onclick="filterArtworks('Painting')">Painting</button>
                    <button onclick="filterArtworks('Sculpture')">Sculpture</button>
                    <button onclick="filterArtworks('Carving')">Carving</button>
                    <button onclick="filterArtworks('Furniture')">Furniture</button>
                    <button onclick="filterArtworks('')">All</button> <!-- Show all artworks -->
                </div>
            </div>

            <div class="art-grid">
                <?php while ($artwork = $artworks->fetch_assoc()): ?>
                    <!-- Check if the logged-in user is the artist or the one who posted the artwork -->
                    <?php if ($artwork['user_id'] != $logged_in_user_id): ?>
                        <div class="art-card" data-category="<?php echo htmlspecialchars($artwork['artwork_type']); ?>">
                            <img src="<?php echo htmlspecialchars($artwork['image_path']); ?>" alt="<?php echo htmlspecialchars($artwork['title']); ?>" onclick="openModal(<?php echo htmlspecialchars(json_encode($artwork), ENT_QUOTES, 'UTF-8'); ?>)">
                            <div class="art-info">
                                <h3><strong>Art-Name:</strong> <?php echo htmlspecialchars($artwork['title']); ?></h3>
                            </div>
                            <p><strong>Artist: <?php echo htmlspecialchars($artwork['username']); ?></strong></p>
                            <div class="artist-profile">
                                <img src="<?php echo htmlspecialchars($artwork['user_img']); ?>" alt="uploads/dd.jpg">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="art-card" data-category="<?php echo htmlspecialchars($artwork['artwork_type']); ?>">
                            <img src="<?php echo htmlspecialchars($artwork['image_path']); ?>" alt="<?php echo htmlspecialchars($artwork['title']); ?>">
                            <div class="art-info">
                                <h3><strong>Art-Name:</strong> <?php echo htmlspecialchars($artwork['title']); ?></h3>
                            </div>
                            <p><strong>Artist: <?php echo htmlspecialchars($artwork['username']); ?></strong></p>
                            <p><em>Your product</em></p>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div id="artwork-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modal-title"></h2>
            <img id="modal-image" src="" alt="Artwork Image">
            <p id="modal-description"></p>
            <p id="modal-year"></p>
            <p id="modal-medium"></p>
            <p id="modal-dimensions"></p>
            <p id="modal-price"></p>
            <p id="modal-availability"></p>
            <p id="modal-artwork-type"></p> <!-- Added artwork type here -->

            <!-- Review Section -->
            <button id="reviewButton" onclick="toggleReviews()">Show Reviews</button>
            <div id="reviewSection" style="display: none; margin-top: 20px;"></div>

            <!-- Comment and Rating Form -->
            <form action="submit_comment.php" method="POST" onsubmit="return showSuccessAlert()">
                <input type="hidden" id="artwork_id" name="artwork_id">
                <select name="rating" required>
                    <option value="" disabled selected>Rate this artwork</option>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
                <textarea name="comment" placeholder="Write your comment here" required></textarea>
                <button type="submit">Submit</button>
            </form>

            <button>
                <a id="buyNowLink" href="#">Buy Now</a>
            </button>
        </div>
    </div>

    <script>
        function openModal(artwork) {
            document.getElementById('modal-title').innerText = artwork.title;
            document.getElementById('modal-description').innerText = artwork.description;
            document.getElementById('modal-year').innerText = "Year: " + artwork.year;
            document.getElementById('modal-medium').innerText = "Medium: " + artwork.medium;
            document.getElementById('modal-dimensions').innerText = "Dimensions: " + artwork.dimensions;
            document.getElementById('modal-price').innerText = "Price: $" + artwork.price;
            document.getElementById('modal-availability').innerText = "Availability: " + (artwork.is_available ? 'Available' : 'Not Available');
            document.getElementById('modal-artwork-type').innerText = "Artwork Type: " + artwork.artwork_type; // Display artwork type
            document.getElementById('modal-image').src = artwork.image_path;
            document.getElementById('artwork_id').value = artwork.artwork_id;
            document.getElementById('buyNowLink').href = `buy_now.php?artwork_id=${artwork.artwork_id}`;
            document.getElementById('artwork-modal').style.display = 'block';

            loadReviews(artwork.artwork_id);
        }

        function loadReviews(artworkId) {
            fetch(`fetch_reviews.php?artwork_id=${artworkId}`)
                .then(response => response.json())
                .then(data => {
                    const reviewSection = document.getElementById('reviewSection');
                    reviewSection.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(review => {
                            const reviewDiv = document.createElement('div');
                            reviewDiv.innerHTML = `
                                <p><strong>Username:</strong> ${review.username}</p>
                                <p><strong>Comment:</strong> ${review.comment}</p>
                                <p><strong>Rating:</strong> ${review.rating} / 5</p>
                                <hr>
                            `;
                            reviewSection.appendChild(reviewDiv);
                        });
                    } else {
                        reviewSection.innerHTML = '<p>No reviews yet.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching reviews:', error);
                    document.getElementById('reviewSection').innerHTML = '<p>Unable to load reviews at this time.</p>';
                });
        }

        function toggleReviews() {
            const reviewSection = document.getElementById('reviewSection');
            reviewSection.style.display = reviewSection.style.display === 'none' ? 'block' : 'none';
        }

        function closeModal() {
            document.getElementById('artwork-modal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('artwork-modal')) {
                closeModal();
            }
        }

        document.getElementById('sb').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const artCards = document.querySelectorAll('.art-card');

            artCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                if (title.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        function filterArtworks(category) {
            const artCards = document.querySelectorAll('.art-card');

            artCards.forEach(card => {
                const artworkCategory = card.dataset.category.toLowerCase();
                if (category === '' || artworkCategory.includes(category.toLowerCase())) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function showSuccessAlert() {
            alert('Rating submitted successfully!');
            return true;
        }
    </script>
</body>
</html>
