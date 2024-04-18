<?php 
  session_start();

  if (isset($_POST["product-action__addToCart"]) && $_POST['product-action__addToCart']) {
    $productId = $_POST['productId'];
    $amount = 1;

    if (isset($_POST['amount']) && $_POST['amount']) {
      $amount = $_POST['amount'];
    }

    // Khởi tạo session cart
    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
    }

    // Kiểm tra sản phẩm đã tồn tại trong cart chưa
    $isExistProduct = false;
    foreach($_SESSION['cart'] as $key => $product) {
      if ($product['id'] == $productId) {
        $_SESSION['cart'][$key]['amount'] += $amount;
        $isExistProduct = true;
        break;
      }
    }

    // Nếu chưa tồn tại thì add vào cart
    if (!$isExistProduct) {
      $product = [
        'id' => $productId,
        'amount' => $amount
      ];
      $_SESSION['cart'][] = $product;
    }
    
    // Đếm số lượng sản phẩm trong giỏ hàng
    $cartQnt = count($_SESSION['cart']);
    echo $cartQnt;
  }

  // Xoá sản phẩm trong giỏ hàng
  if (isset($_POST['product-action__removeFromCart']) && $_POST['product-action__removeFromCart']) {
    $productIdToRemove = $_POST['product_id'];

    foreach($_SESSION['cart'] as $key => $product) {
      if ($productIdToRemove == $product['id']) {
        unset($_SESSION['cart'][$key]);
      }
    }

    header("location: ../index.php?page=cart");
  }

  // Xử lý lưu các sản phẩm cần thanh toán
  if (isset($_POST['abate']) && $_POST['abate']) {
    if (!isset($_SESSION['cart-selected'])) {
      $_SESSION['cart-selected'] = [];
    } else {
      unset($_SESSION['cart-selected']);
    }

    foreach ($_POST['selectedProducts'] as $product) {
      $_SESSION['cart-selected'][] = $product;
    }
  }

  // Xử lý cập nhật số lượng sản phẩm
  if (isset($_POST["product-action__updateAmount"]) && $_POST['product-action__updateAmount']) {
    $productId = $_POST['productId'];
    $amount = $_POST['amount'];

    foreach($_SESSION['cart'] as $key => $product) {
      if ($product['id'] == $productId) {
        $_SESSION['cart'][$key]['amount'] = $amount;
        break;
      }
    }
  }
?>