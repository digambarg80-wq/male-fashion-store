<?php
echo "Testing footer include:<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Footer path: " . __DIR__ . "/includes/footer.php<br>";

if(file_exists(__DIR__ . "/includes/footer.php")) {
    echo "✓ Footer file exists<br>";
    echo "File size: " . filesize(__DIR__ . "/includes/footer.php") . " bytes<br>";
    echo "First 100 characters:<br>";
    echo "<pre>";
    echo htmlspecialchars(substr(file_get_contents(__DIR__ . "/includes/footer.php"), 0, 200));
    echo "</pre>";
} else {
    echo "✗ Footer file does not exist!<br>";
}

echo "<br><br>Attempting to include footer:<br>";
include 'includes/footer.php';
echo "<br>After footer include";
?>