<?php
  if (session('sid')) {
    require_once 'admin/main.blade.php';
  } else {
    require_once 'user/horizontal/main.blade.php';
  }
?>