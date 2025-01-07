<?php
session_start();

// Ștergem datele din sesiune
session_unset();
session_destroy();

// Dezactivăm caching-ul
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Răspuns pentru frontend
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
?>
