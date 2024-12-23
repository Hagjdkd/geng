<?php 
session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Horizontal Menu</title>
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #2c3e50, #9b1c1c);
  padding: 20px;
  position: relative;
}

.burger-menu {
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 30px;
  height: 25px;
}

.line {
  width: 100%;
  height: 5px;
  background-color: #fff;
  transition: 0.3s;
}

nav {
  display: flex;
  align-items: center;
}

nav ul {
  list-style-type: none;
  padding: 0;
  display: flex;
  justify-content: flex-start;
  gap: 20px;
}

nav ul li {
  padding: 10px;
}

nav ul li a {
  color: #fff;
  text-decoration: none;
  font-size: 18px;
}

/* Initially hide the menu for mobile */
nav#menu {
  display: none;
}

/* Active state of menu for mobile */
nav#menu.active {
  display: flex;
}

/* Media query for mobile screens */
@media (max-width: 768px) {
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .burger-menu {
    display: block;
    cursor: pointer;
  }

  /* Burger icon animation */
  .burger-menu.active .line1 {
    transform: rotate(45deg);
    position: relative;
    top: 9px;
  }

  .burger-menu.active .line2 {
    opacity: 0;
  }

  .burger-menu.active .line3 {
    transform: rotate(-45deg);
    position: relative;
    top: -9px;
  }

  nav {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-grow: 1;
    justify-content: flex-end;
  }

  nav ul {
    display: flex;
    gap: 20px;
  }
}


  </style>
</head>
<body>
  <header>
    <div class="burger-menu" onclick="toggleMenu()">
      <div class="line line1"></div>
      <div class="line line2"></div>
      <div class="line line3"></div>
    </div>
    <nav id="menu">
      <ul>
        <li><a href="user_dashboard.php">Home</a></li>
        <li><a href="product.php">My product</a></li>
        <li><a href="transc.php">Transactions</a></li>
        <li><a href="purchased.php">Purchased</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="about_us.php">About Us</a></li>
        <li><a href="services.php">Services</a></li>    
        <li><a href="logout.php">Log Out</a></li>    
       
      </ul>
    </nav>
  </header>

  <script>
    function toggleMenu() {
      document.getElementById("menu").classList.toggle("active");
      document.querySelector(".burger-menu").classList.toggle("active");
    }
  </script>
</body>
</html>
