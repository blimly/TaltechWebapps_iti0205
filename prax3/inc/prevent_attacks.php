<?php
function _e($string) {
    echo htmlspecialchars($string, ENT_QUOTES, "UTF-8");
}
?>