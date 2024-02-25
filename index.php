<?php
  // Header
  require_once("view/header.php");

  // Content
  if (isset($_GET['page']) && $_GET['page'] != '') {
    $page = $_GET['page'];

    switch ($page) {
      case 'product':
        require_once('view/pages/product.php');
        break;
      case 'cart':
        require_once('view/pages/cart.php');
        break;
      case 'checkout':
        require_once('view/pages/checkout.php');
        break;
      case 'login':
        require_once('view/pages/login.php');
        break;
      case 'register':
        require_once('view/pages/register.php');
        break;
      default:
        // Xử lý trường hợp không khớp với bất kỳ trang nào
        require_once('view/pages/notFound.php');
        break;
    }
  } else {
    // Trang chủ
    require_once('view/pages/home.php');
  }

  // Footer
  require_once("view/footer.php");
?>