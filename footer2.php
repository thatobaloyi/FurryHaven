<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> FurryHaven. All rights reserved.</p>
</footer>
