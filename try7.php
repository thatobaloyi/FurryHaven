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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FurryHaven - Home</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* --- Adoptable Animals Section Styling --- */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f4e9;
    color: #222;
}

.adoptable-section {
    padding: 60px 20px;
    background: linear-gradient(135deg,rgb(236, 149, 77),rgb(219, 163, 98));
    
}

.section-header h1 {
    text-align: center;
    font-size: clamp(36px, 5vw, 60px);
    font-weight: 900;
    margin-bottom: 5px;
    letter-spacing: 1px;
    color: white;
}


/* --- Slider Styling --- */
.slider-container {
    position: relative;
    overflow: hidden;
    padding: 0 20px;
    border: none;
}

.animal-slider {
    display: flex;
    gap: 25px;
    transition: transform 0.5s ease-in-out;
    border: none;
}

.slide {
    flex: 0 0 25%;
    display: flex;
    justify-content: center;
    border: none;
}

.animal-btn {
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    width: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
    display: flex;
    flex-direction: column;
}

.animal-btn:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    backdrop-filter: blur(3px);
}

.animal-image {
    width: 100%;
    height: 280px;
    overflow: hidden;
    position: relative;
    border-radius: 20px 20px 0 0;
     border: none;          /* Remove any border */
}

.animal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
     border: none;          /* Remove any border */
}


.animal-info {
    padding: 15px;
    background: #f8f4e9;
    text-align: center;
}

.animal-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f3c74;
    margin-bottom: 5px;
}

.animal-breed {
    font-size: 1rem;
    color: #1f3c74;
}

/* --- Slider Controls --- */
.slider-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 25px;
    gap: 15px;
}

.slider-btn {
    background: #222;
    color: #fff;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.slider-btn:hover {
    background:#da7422 ;
    transform: scale(1.1);
}

.slider-dots {
    display: flex;
    gap: 12px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ccc;
    cursor: pointer;
    transition: transform 0.3s ease, background 0.3s ease;
}

.dot.active {
    background: #da7422;
    transform: scale(1.3);
}

/* --- View More --- */
.btn-secondary {
    display: inline-block;
    background: #da7422;
    color: white;
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: transform 0.3s ease, background 0.3s ease;
    text-decoration: none;
}

.view-more {
    display: flex;
    justify-content: center; /* centers the button horizontally */
    margin-top: 50px;
}

.btn-secondary:hover {
    background: #1f3c74;
    transform: scale(1.05);
}

.cards-section-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px; /* Space between lines and text */
    margin: 50px 0 30px 0;
}

.cards-section-header h2 {
    font-size: clamp(28px, 4vw, 40px);
    font-weight: 800;
    color: #222;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-align: center;
}

.cards-section-header .line {
    flex: 1;
    height: 5px;
    background-color: #FF8C00; /* Orange color */
    border-radius: 5px;
    max-width: 80px; /* Optional: restrict line width */
}


@media (max-width: 768px) {
    .cards-section-header {
        gap: 10px;
    }
    .cards-section-header .line {
        max-width: 50px;
    }
}

/* --- Responsive --- */
@media(max-width: 1024px) { .slide { flex: 0 0 33.33%; } }
@media(max-width: 768px) { .slide { flex: 0 0 50%; } .animal-image { height: 220px; } }
@media(max-width: 480px) { .slide { flex: 0 0 100%; } .animal-image { height: 240px; } }
</style>
</head>
<body>

<div class="cards-section-header">
    <div class="line"></div>
    <h2>You Can Spread Pawsitivity</h2>
    <div class="line"></div>
</div>

<section class="adoptable-section">
    <div class="section-header">
           <div class="line"></div>
        <h1>Find Your Forever Friend</h1>
        <div class="line"></div>
        <br><br>
    </div>


    <div class="slider-container">
        <div class="animal-slider">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($count >= 20) break;
            ?>
                <div class="slide">
                    <form action="adoptable2.php" method="POST">
                        <input type="hidden" name="Animal_ID" value="<?php echo $row['Animal_ID']; ?>">
                        <button type="submit" class="animal-btn">
                            <div class="animal-image">
                                <img src="./images/animals/<?php echo htmlspecialchars($row['filePath']); ?>" alt="<?php echo htmlspecialchars($row['Animal_Name']); ?>">
                            </div>
                            <div class="animal-info">
                                <h2 class="animal-name"><?php echo htmlspecialchars($row['Animal_Name']); ?></h2>
                                <p class="animal-breed"><?php echo htmlspecialchars($row['Animal_Breed']); ?></p>
                            </div>
                        </button>
                    </form>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>

    <div class="slider-controls">
 
        <div class="slider-dots" id="slider-dots"></div>
   
    </div>

    <div class="view-more">
        <a href="adoptable.php" class="btn-secondary">See All Animals</a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.animal-slider');
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.getElementById('slider-dots');
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');

    let currentIndex = 0;
    let slidesToShow = 4;
    let totalSlides = slides.length;
    let autoSlideInterval;

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

    function updateSlidesToShow() {
        if (window.innerWidth >= 1024) slidesToShow = 4;
        else if (window.innerWidth >= 768) slidesToShow = 3;
        else if (window.innerWidth >= 480) slidesToShow = 2;
        else slidesToShow = 1;

        createDots();
        updateSliderPosition();
        updateDots();
    }

    function initSlider() {
        updateSlidesToShow();
        updateSliderPosition();
        startAutoSlide();

        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);
        window.addEventListener('resize', updateSlidesToShow);
    }

    function updateSliderPosition() {
        const slideWidth = 100 / slidesToShow;
        const translateX = -(currentIndex * slideWidth);
        slider.style.transform = `translateX(${translateX}%)`;
    }

    function updateDots() {
        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, index) => dot.classList.toggle('active', index === currentIndex));
    }

    function nextSlide() {
        const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
        currentIndex = currentIndex < maxIndex ? currentIndex + 1 : 0;
        updateSliderPosition();
        updateDots();
    }

    function prevSlide() {
        const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
        currentIndex = currentIndex > 0 ? currentIndex - 1 : maxIndex;
        updateSliderPosition();
        updateDots();
    }

    function goToSlide(index) {
        const maxIndex = Math.ceil(totalSlides / slidesToShow) - 1;
        if (index >= 0 && index <= maxIndex) {
            currentIndex = index;
            updateSliderPosition();
            updateDots();
        }
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 4000);
    }

    initSlider();
});
</script>

</body>
</html>
