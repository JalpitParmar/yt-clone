
<head>
    <style>
         * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f9f9f9;
      color: #333;
    }
    a { text-decoration: none; color: inherit; }

    /* ---------- Navbar ---------- */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
      color: red;
      display: flex;
      align-items: center;
    }
    .navbar .logo i {
      margin-right: 8px;
    }
    .navbar .search {
      flex: 1;
      margin: 0 20px;
      display: flex;
    }
    .navbar .search input {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 20px 0 0 20px;
      outline: none;
      transition: 0.3s;
    }
    .navbar .search input:focus {
      border-color: red;
    }
    .navbar .search button {
      border: none;
      background: red;
      color: #fff;
      padding: 0 15px;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
    }
    .navbar .icons i {
      font-size: 20px;
      margin-left: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    .navbar .icons i:hover {
      color: red;
      transform: scale(1.2);
    }

    /* ---------- Sidebar ---------- */
    .sidebar {
      width: 220px;
      background: #fff;
      height: 100vh;
      position: fixed;
      top: 60px;
      left: -230px;
      transition: 0.4s;
      padding: 15px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
      z-index: 1000;
    }
    .sidebar.active {
      left: 0;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 8px;
      transition: 0.3s;
      font-size: 15px;
    }
    .sidebar a i {
      margin-right: 10px;
      font-size: 18px;
    }
    .sidebar a:hover {
      background: #f0f0f0;
    }

    /* ---------- Content ---------- */
    .content {
      margin-left: 0;
      margin-top: 70px;
      padding: 20px;
      transition: margin-left 0.4s;
    
    }
    .sidebar.active ~ .content {
      margin-left: 230px;
    }
        /* ---------- Category Bar ---------- */
.category-bar {
  display: flex;
  overflow-x: auto;
  white-space: nowrap;
  background: #fff;
  padding: 10px 15px;
  border-bottom: 1px solid #ddd;
  position: sticky;
  top: 60px;
  z-index: 999;
}

.category-bar button {
  background: #f0f0f0;
  border: none;
  outline: none;
  margin-right: 10px;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
}

.category-bar button:hover {
  background: #ddd;
}

.category-bar button.active {
  background: black;
  color: #fff;
}
/* ---------- Responsive ---------- */
@media (max-width: 768px) {
  /* Navbar adjustments */
  .navbar {
    flex-wrap: wrap;
    padding: 8px 12px;
  }

  .navbar .logo {
    font-size: 18px;
  }

  .navbar .search {
    order: 3;
    width: 100%;
    margin: 10px 0 0;
  }

  .navbar .search input {
    font-size: 14px;
    padding: 6px 10px;
  }

  .navbar .icons {
    order: 2;
    margin-left: auto;
  }

  .navbar .icons i {
    font-size: 18px;
    margin-left: 12px;
  }

  /* Sidebar */
  .sidebar {
    width: 70%;
    max-width: 260px;
    top: 55px;
    height: calc(100vh - 55px);
  }

  .sidebar.active ~ .content {
    margin-left: 0; /* don't push content on mobile */
  }

  /* Content */
  .content {
    padding: 15px;
  }

  /* Category bar */
  .category-bar {
    padding: 8px 10px;
  }

  .category-bar button {
    font-size: 13px;
    padding: 5px 12px;
  }
}
 /* ---------- Responsive ---------- */
    @media (max-width: 768px) {
      .navbar {
        padding: 8px 12px;
      }
      .navbar .logo {
        font-size: 18px;
      }
      .navbar .search {
        order: 3;
        width: 100%;
        margin: 10px 0 0;
      }
      .navbar .search input {
        font-size: 14px;
        padding: 6px 10px;
      }
      .navbar .icons {
        order: 2;
        margin-left: auto;
      }
      .navbar .icons i {
        font-size: 18px;
        margin-left: 12px;
      }

      .sidebar {
        width: 70%;
        max-width: 260px;
        top: 80px;
        left: -600px;
        height: calc(100vh - 55px);
      }
      .sidebar.active ~ .content {
        margin-left: 0; /* don't push content on mobile */
      }

      .content {
        padding: 15px;
      }
      .category-bar {
        padding: 8px 10px;
      }
      .category-bar button {
        font-size: 13px;
        padding: 5px 12px;
      }
    }

    @media (max-width: 480px) {
      .navbar .logo {
        font-size: 16px;
      }
      .navbar .search input {
        font-size: 13px;
        padding: 5px 8px;
      }
      .navbar .icons i {
        font-size: 16px;
        margin-left: 10px;
      }
      .sidebar {
        width: 80%;
        z-index: 1000;
      }
      .category-bar button {
        font-size: 12px;
        padding: 4px 10px;
      }
    }
    </style>
</head>
<body>
    <!-- nav.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<!-- Navbar -->
 
   <div class="navbar">
     <div class="logo"><i class="bi bi-play-btn-fill"></i>
     <a style="text-decoration:none; color:inherit;" href="index.php">
      MyTube
     </a>
    </div>
    
     <div class="search">
  <form action="index.php" method="get" style="display:flex; width:100%;">
    <input type="text" name="search" placeholder="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit"><i class="bi bi-search"></i></button>
  </form>
</div>

  
  <div class="icons">
    <a href="upload.php"><i class="bi bi-cloud-upload" title="Upload"></i></a>
    <a href="profile.php"><i class="bi bi-person-circle" title="Profile"></i></a>
    <i class="bi bi-list" id="menu-btn" title="Menu"></i>
  </div>
</div>

<div class="sidebar" id="sidebar">
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>
  <a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a>
  <a href="watch_history.php"><i class="bi bi-clock-history"></i> Watch History</a>
  <a href="addads.php"><i class="bi bi-megaphone"></i> Add Ads</a>
  <a href="managereports.php"><i class="bi bi-flag"></i> Manage Reports</a>
  

  <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] != "admin"): ?>
    <a href="buy_premium.php"><i class="bi bi-star"></i> Buy Premium</a>
    <a href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Video</a>
    <a href="manage_videos.php"><i class="bi bi-collection-play"></i> Manage Videos</a>
    <a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a>
    <a href="watch_history.php"><i class="bi bi-clock-history"></i> Watch History</a>
    <a href="subscriptions.php"><i class="bi bi-play-circle"></i> subscriptions</a>




  
  
  <?php else: ?>
    <!-- If role is not set (user not logged in) -->
    <a href="buy_premium.php"><i class="bi bi-star"></i> Buy Premium</a>
    <a href="upload.php"><i class="bi bi-cloud-upload"></i> Upload Video</a>
    <a href="manage_videos.php"><i class="bi bi-collection-play"></i> Manage Videos</a>
    <a href="profile.php"><i class="bi bi-person-circle"></i> Profile</a>
    <a href="watch_history.php"><i class="bi bi-clock-history"></i> Watch History</a>
    
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
      <?php else: ?>
        <a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
        <a href="signup.php"><i class="bi bi-person-plus"></i> Register</a>
     <?php endif; ?>
</div>

<script>
  const menuBtn = document.getElementById("menu-btn");
  const sidebar = document.getElementById("sidebar");
  const content = document.getElementById("content");

  if(menuBtn){
    menuBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");
      if(content) content.classList.toggle("active");
    });
  }
</script>

</body>
</html>