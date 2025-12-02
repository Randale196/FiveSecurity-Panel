<?php
include('../config.php'); // Dont remove it!

function redirect_discord_invite()
{
  header('Location: ' . $website_config['discord_url'] . '');
  exit();
}

redirect_discord_invite();
?>