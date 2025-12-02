<?php
require_once __DIR__ . '/debug.php'; // Dont remove it!
session_start();
include('database.php'); // Dont remove it!
include('config.php'); // Dont remove it!

// Login check
if (!isset($_SESSION['id']) || !isset($_SESSION['group'])) {
  error_log($_SESSION['id']);
  error_log($_SESSION["group"]);
  session_destroy();
  header("Location: https://" . $website_config['site_domain'] . "/login");
  exit;
}

// Maintenance Function
function is_maintenance(): bool
{
  global $link;
  $stmt = mysqli_prepare($link, "SELECT maintenance FROM `system` WHERE maintenance = ? LIMIT 1");
  $maintenance_value = 1;
  mysqli_stmt_bind_param($stmt, "i", $maintenance_value);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  return (mysqli_num_rows($result) > 0);
}

// Maintenance Check
if (is_maintenance() && !($_SESSION["group"] == "admin")) {
  header('Location: https://' . $website_config['site_domain'] . '/maintenance.php');
}

// Ban Function
function is_banned(int $user_id): bool
{
  global $link;
  $stmt = mysqli_prepare($link, "SELECT userid FROM panelbans WHERE userid = ?");
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  return (mysqli_num_rows($result) > 0);
}

// Ban Check
if (is_banned($_SESSION["id"])) {
  header('Location: https://' . $website_config['site_domain'] . '/banned.php');
}

// Avatar 
$stmt = $conn->prepare("SELECT avatarurl FROM users WHERE userid = ? LIMIT 1");
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
  $avatar = $row['avatarurl'];
}

// Admin check
function isAdmin()
{
  return $_SESSION["group"] == "admin";
}

/**
 * Encrypts the given data using OpenSSL and the specified encryption method and key.
 *
 * @param string $data The data to be encrypted.
 * @param string $encryptionMethod The encryption method to be used (e.g. "AES-256-CBC").
 * @param string $secretKey The secret key to be used for encryption.
 *
 * @return string The encrypted data.
 */
function encrypt_string($data)
{
  return openssl_encrypt('' . $data . '', 'aes-256-cbc', 'imosec54');
}

/**
 * Decrypts the given data using OpenSSL and the specified encryption method and key.
 *
 * @param string $data The data to be decrypted.
 * @param string $encryptionMethod The encryption method used to encrypt the data (e.g. "AES-256-CBC").
 * @param string $secretKey The secret key used to encrypt the data.
 *
 * @return string The decrypted data.
 */
function decrypt_string($data)
{
  return openssl_decrypt('' . $data . '', 'aes-256-cbc', 'imosec54');
}
?>