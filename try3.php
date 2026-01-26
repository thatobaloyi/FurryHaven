<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');

require_once "./core/functions.php";

// Get all animals from the database
$query = "SELECT a.*, b.filePath
          FROM animal a
          LEFT JOIN (
              SELECT animalID, MIN(filePath) AS filePath
              FROM animalmedia
              GROUP BY animalID
          ) b ON a.Animal_ID = b.animalID
          WHERE a.isDeleted = 0";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

  $boardingHref = isset($_SESSION['username'])
      ? 'boarding.php'
      : 'login.php?redirect=boarding.php';

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

include_once __DIR__ . '/models/Donation.php';
$donationModel = new Donation();
$topDonations = $donationModel->leaderboard(10); // Top 10 donations
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurryHaven - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
/*General Styles and CSS Variables*/
        :root {
            --primary-color: #003366; /* Dark Blue */
            --secondary-color: #FF8C00; /* Orange */
            --background-color: #f8f4e9; /* Light Beige */
            --light-text: #f8f8f8; /* Off-White */
        }
h1 {
  text-align: center;
  color: var(--primary-color);   /* system blue */
  font-family: var(--font-family, Arial, Helvetica, sans-serif);
  font-size: 2.2rem;
  font-weight: bold;
  margin-top: 20px;
    margin-bottom: 0;
    font-size: clamp(36px, 5vw, 60px);
  font-weight: 800;
  display: block;
  
  
}


body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

     /* --- Start of Header --- */
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 75px;                 /* <-- shorter (was 72px) */
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 24px;            /* <-- reduced padding */
  z-index: 9999;
  transition: background-color 300ms ease, box-shadow 300ms ease,
              backdrop-filter 300ms ease, border-bottom 300ms ease, color 200ms;
  background: rgba(255, 248, 240, 0.0);
  border-bottom: none;
  margin: 0;
  padding: 0;
  width: 100%;


}

  


     /* Animal Gallery Styles */
        .animal-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
            justify-items: center;
            margin-top: 2rem;
            padding: 0 1rem;
        }
        
        .animal-item {
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .animal-item:hover {
            transform: translateY(-5px);
        }
        
        .animal-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }
        
        .animal-item:hover .animal-image {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .animal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .animal-item:hover .animal-image img {
            transform: scale(1.05);
        }
        
        .animal-name {
            margin-top: 15px;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .animal-breed {
            color: var(--secondary-color);
            font-size: rem;
            margin-top: 5px;
        }
        
        .view-more {
            text-align: center;
            margin-top: 50px;
        }
        
        .btn-secondary {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #e67e00;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .animal-gallery {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
            }
            
            .animal-image {
                height: 250px;
            }
            
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .animal-gallery {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .animal-image {
                height: 280px;
                max-width: 300px;
            }
        }
        /* Slider Container */
        .slider-container {
            position: relative;
            overflow: hidden;
            margin: 30px 0;
            padding: 0 40px;
        }
        
        /* Animal Slider */
        .animal-slider {
            display: flex;
            transition: transform 0.5s ease;
        }
        
        .slide {
            flex: 0 0 25%;
            padding: 0 15px;
            text-align: center;
        }
        
        .animal-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        
        .animal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .animal-name {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .animal-breed {
            color: var(--secondary-color);
            font-size: 1rem;
            margin-top: 5px;
        }
        
        /* Slider Controls */
        .slider-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
        }
        
        .slider-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s ease;
            margin: 0 10px;
        }
        
        .slider-btn:hover {
            background: var(--secondary-color);
        }
        
        .slider-dots {
            display: flex;
            justify-content: center;
            margin: 0 15px;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            background: #ccc;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .dot.active {
            background: var(--primary-color);
        }
        
        .view-more {
            text-align: center;
            margin-top: 50px;
        }
        
        .btn-secondary {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #e67e00;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .slide {
                flex: 0 0 33.33%;
            }
        }
        
        @media (max-width: 768px) {
            .slide {
                flex: 0 0 50%;
            }
            
            .animal-image {
                height: 250px;
            }
            
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .section-header p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .slide {
                flex: 0 0 100%;
            }
            
            .slider-container {
                padding: 0 20px;
            }
            
            .animal-image {
                height: 280px;
            }
        }



 

</style>
      </head>
      <body>

  




<!-- End of 3 R's of Hope section -->

<!-- Adoptable Animals Section -->
                <br><br><section>
    <div class="section-header">
        <h1>Meet Your Furry Friends</h1>
    </div>
    
    <!-- Slider Container -->
    <div class="slider-container">
        <div class="animal-slider">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($count >= 20) break; // Limit to 5 animals for the slider
            ?>
                <div class="slide">
                    <form action="adoptable2.php" method="POST">
                        <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                        <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0; width: 100%;">
                            <div class="animal-image">
                                <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                            </div>
                            <h1 class="animal-name"><?php echo htmlspecialchars($row['Animal_Name']); ?></h1>
                            <p class="animal-breed"><?php echo htmlspecialchars($row['Animal_Breed']); ?></p>
                        </button>
                    </form>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
    
   <!-- Slider Controls -->
            <div class="slider-controls">
                <button class="slider-btn" id="prev-slide"><i class="fas fa-chevron-left"></i><</button>
                
                <div class="slider-dots" id="slider-dots">
                    <!-- Dots will be generated by JavaScript -->
                </div>
                
                <button class="slider-btn" id="next-slide"><i class="fas fa-chevron-right">></i></button>
            </div>
            
            <div class="view-more">
                <a href="adoptable.php" class="btn-secondary">View All Animals</a>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('.animal-slider');
            const slides = document.querySelectorAll('.slide');
            const dotsContainer = document.getElementById('slider-dots');
            const prevBtn = document.getElementById('prev-slide');
            const nextBtn = document.getElementById('next-slide');
            
            let currentIndex = 0;
            let slidesToShow = 4; // Default number of slides to show
            let totalSlides = slides.length;
            let autoSlideInterval;
            
            // Create dots based on number of slides
            function createDots() {
                dotsContainer.innerHTML = '';
                const dotsNeeded = Math.ceil(totalSlides / slidesToShow);
                
                for (let i = 0; i < dotsNeeded; i++) {
                    const dot = document.createElement('div');
                    dot.classList.add('dot');
                    if (i === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                }
            }
            
            // Determine how many slides to show based on screen width
            function updateSlidesToShow() {
                if (window.innerWidth >= 1024) {
                    slidesToShow = 4;
                } else if (window.innerWidth >= 768) {
                    slidesToShow = 3;
                } else if (window.innerWidth >= 480) {
                    slidesToShow = 2;
                } else {
                    slidesToShow = 1;
                }
                
                // Recreate dots when slidesToShow changes
                createDots();
                updateSliderPosition();
                updateDots();
            }
            
            // Initialize slider
            function initSlider() {
                updateSlidesToShow();
                updateSliderPosition();
                startAutoSlide();
                
                // Add event listeners
                prevBtn.addEventListener('click', prevSlide);
                nextBtn.addEventListener('click', nextSlide);
                
                // Update on window resize
                window.addEventListener('resize', function() {
                    updateSlidesToShow();
                });
            }
            
            // Update slider position
            function updateSliderPosition() {
                const slideWidth = 100 / slidesToShow;
                const translateX = -(currentIndex * slideWidth);
                slider.style.transform = `translateX(${translateX}%)`;
            }
            
            // Update dots
            function updateDots() {
                const dots = document.querySelectorAll('.dot');
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }
            
            // Go to next slide
            function nextSlide() {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (currentIndex < maxIndex) {
                    currentIndex++;
                } else {
                    currentIndex = 0;
                }
                updateSliderPosition();
                updateDots();
            }
            
            // Go to previous slide
            function prevSlide() {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (currentIndex > 0) {
                    currentIndex--;
                } else {
                    currentIndex = maxIndex;
                }
                updateSliderPosition();
                updateDots();
            }
            
            // Go to specific slide
            function goToSlide(index) {
                const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
                
                if (index >= 0 && index <= maxIndex) {
                    currentIndex = index;
                    updateSliderPosition();
                    updateDots();
                }
            }
            
            // Start auto slide
            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 4000);
            }
            
            // Initialize the slider
            initSlider();
        });
    </script><br>
<!-- End of Adoptable Animals Section -->



</body>
</html>









<!-- Adoptable Animals Section -->
                <br><br><section>
    <div class="section-header">
        <h1>Meet Your Furry Friends</h1>
    </div>
    
    <!-- Slider Container -->
    <div class="slider-container">
        <div class="animal-slider">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($count >= 20) break; // Limit to 5 animals for the slider
            ?>
                <div class="slide">
                    <form action="adoptable2.php" method="POST">
                        <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                        <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0; width: 100%;">
                            <div class="animal-image">
                                <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                            </div>
                            <h1 class="animal-name"><?php echo htmlspecialchars($row['Animal_Name']); ?></h1>
                            <p class="animal-breed"><?php echo htmlspecialchars($row['Animal_Breed']); ?></p>
                        </button>
                    </form>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
    
   <!-- Slider Controls -->
            <div class="slider-controls">
                <button class="slider-btn" id="prev-slide"><i class="fas fa-chevron-left"></i><</button>
                
                <div class="slider-dots" id="slider-dots">
                    <!-- Dots will be generated by JavaScript -->
                </div>
                
                <button class="slider-btn" id="next-slide"><i class="fas fa-chevron-right">></i></button>
            </div>
            
            <div class="view-more">
                <a href="adoptable.php" class="btn-secondary">View All Animals</a>
            </div>
        </section>
    </div>
  -->
