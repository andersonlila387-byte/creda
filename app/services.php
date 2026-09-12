<?php 
/**
 * Scriptly Escrow - Services & Talent Marketplace Alias
 */
$queryString = $_SERVER['QUERY_STRING'] ?? '';
header('Location: talent.php' . ($queryString ? '?' . $queryString : ''));
exit;
