// Get the menu, button, and modal elements
const menu = document.getElementById('menu');
const menuBtn = document.getElementById('menu-btn');
const homeArtLink = document.getElementById('home-art-link');
const aboutUsLink = document.getElementById('about-us-link');
const modal = document.getElementById('modal');
const closeBtn = document.getElementById('close-btn');
const modalTitle = document.getElementById('modal-title');
const modalDescription = document.getElementById('modal-description');

// Toggle the dropdown menu when the logo is clicked
menuBtn.addEventListener('click', () => {
  menu.classList.toggle('show'); // Toggles the menu visibility with the sliding effect
});

// Show modal for Home Art
homeArtLink.addEventListener('click', (event) => {
  event.preventDefault();  // Prevent default link behavior
  modal.style.display = 'block';
  modalTitle.textContent = 'Home Art';
  modalDescription.textContent = 'This section features home art collections, offering unique designs for your living spaces.';
});

// Show modal for About Us
aboutUsLink.addEventListener('click', (event) => {
  event.preventDefault();  // Prevent default link behavior
  modal.style.display = 'block';
  modalTitle.textContent = 'About Us';
  modalDescription.textContent = 'This is the About Us section where we talk about our company and mission.';
});

// Close modal
closeBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Close modal if clicked outside
window.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
});
