<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if a session notification is set
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    echo "<div id='alert-box' class='alert {$notification['type']}'>";
    echo "<span>" . htmlspecialchars($notification['message']) . "</span>";
    echo "<button class='close-btn'>&times;</button>";
    echo "</div>";
    unset($_SESSION['notification']);
}
?>

<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        opacity: 1;
        transition: opacity 0.6s ease-in-out, transform 0.6s ease-in-out;
        transform: translateY(0);
        position: relative;
    }

    .alert.fade-out {
        opacity: 0;
        transform: translateY(-20px);
    }

    .alert.slide-up {
        display: none;
    }

    .success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .error {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }

    .close-btn {
        position: absolute;
        top: 5px;
        right: 10px;
        font-size: 20px;
        font-weight: bold;
        color: #000;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .close-btn:hover {
        color: #777;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alertBox = document.getElementById("alert-box");
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.add("fade-out");
                setTimeout(() => {
                    alertBox.classList.add("slide-up");
                }, 600);
            }, 5000);

            const closeBtn = alertBox.querySelector(".close-btn");
            closeBtn.addEventListener("click", () => {
                alertBox.classList.add("fade-out");
                setTimeout(() => {
                    alertBox.classList.add("slide-up");
                }, 600);
            });
        }
    });
</script>