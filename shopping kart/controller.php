<?php
require('model.php'); 

$act = $_REQUEST['act'];
switch ($act) {
    case "listProduct":
        $products = getProductList();
		if ($products === false) {
            echo "Error fetching product list.";
        } else {
            echo json_encode($products);
        }
        return;
	case "listUserProduct":
		$user_id = $_REQUEST['userId']; 
		$products = getUserProductList($user_id); 
		if ($products === false) {
			echo "Error fetching product list.";
		} else {
			echo json_encode($products);
		}
		return;
	case "listOrder":
        $owner_id = $_REQUEST['owner_Id']; // 獲取當前登陸商家的id
        $order = getOrderListByOwner($owner_id);
        if ($order === false) {
            echo json_encode(["error" => "Error fetching order list."]);
        } else {
            echo json_encode($order);
        }
        return;
    case "unconfirmOrder":
        $orderId = $_REQUEST['orderId'];
        unconfirmOrder($orderId);
        echo json_encode(['message' => 'Status Updated']);
        break;
    case "confirmOrder":
		$orderId = $_REQUEST['orderId'];
		confirmOrder($orderId);
		echo json_encode(['message' => 'Status Updated']);
		break;
    case "shipOrder":
        $orderId = $_REQUEST['orderId'];
        shipOrder($orderId);
        echo json_encode(['message' => 'Status Updated']);
        break;
    case "deliverOrder":
        $orderId = $_REQUEST['orderId'];
        deliverOrder($orderId);
        echo json_encode(['message' => 'Status Updated']);
        break;
    case "arriveOrder":
        $orderId = $_REQUEST['orderId'];
        arriveOrder($orderId);
        echo json_encode(['message' => 'Status Updated']);
        break;
    case "checkout":
        $user_id = $_REQUEST['userId'];
        
        // 獲取購物車內容
        $cart = getCartList($user_id);
        
        // 呼叫結帳函數
        $checkoutResult = checkout($cart, $user_id);
        
        // 返回結果
        echo json_encode($checkoutResult);
        return;
    case "viewOrders":
        $user_id = $_REQUEST['userId'];
        $order = getUserOrders($user_id); 
        echo json_encode($order);
        return;
    case "viewOrdersInfo":
        $user_id = $_REQUEST['userId'];
        $order = getUserOrdersInfo($user_id); 
        echo json_encode($order);
        return;
    case "addProduct":
        $jsonStr = $_POST['dat'];
        $product = json_decode($jsonStr);
        addProduct($product->name, $product->price, $product->detail, $product->remain, $product->id, $product->userId);
        return;
    case "del":
        $id = (int)$_REQUEST['id'];
        delProduct($id);
        return;
	case "addToCart":
		$productId = (int)$_REQUEST['productId'];
		$userId = $_REQUEST['userId'];
		addToCart($productId, $userId); 
		return;
    case "removeFromCart":
        $id = (int)$_REQUEST['id'];
        removeFromCart($id);
        return;
    case "listCart":
		$user_id = $_REQUEST['userId'];
		$products = getCartList($user_id);
		if ($products === false) {
			echo "Error fetching cart list.";
		} else {
			echo json_encode($products);
		}
		return;
	case "login":
		$id = $_REQUEST['id'];
		$pwd = $_REQUEST['pwd'];

		$role = login($id, $pwd); 
		if ($role > 0) {
			$msg = [
				"msg" => "OK",
				"role" => $role,
				"id" => $id 
			];
		} else {
			$msg = [
				"msg" => "NO",
				"role" => 0,
				"id" => null 
			];
		}
		echo json_encode($msg);
		return;
	case "listLogisticsOrders":
		$orders = getLogisticsOrders();
		if ($orders === false) {
			echo json_encode(["error" => "Error fetching logistics orders."]);
		} else {
			echo json_encode($orders);
		}
		return;
	case "updateOrderStatus":
		$clientId = $_REQUEST['clientId'];
		$ownerId = $_REQUEST['ownerId'];
		updateOrderStatus($clientId, $ownerId);
		echo json_encode(['message' => 'Status Updated']);
		break;
	case "submitReview":
		$ownerId = $_POST['ownerId'];
		$clientId = $_POST['clientId'];
		$rating = $_POST['rating'];
		submitReview($ownerId, $clientId, $rating);
		echo "評價已更新";
		break;
	case "loadReview":
	  $user_id = $_REQUEST['userId'];
	  $sellers = loadReview($user_id);
	  echo json_encode($sellers);
	  break;
	case "logout":
		setcookie('loginRole',0,httponly:true);
		break;
	case "register":
		$id = $_POST['id'];
		$pwd = $_POST['pwd'];
		$role = $_POST['role'];

		$result = register($id, $pwd, $role); 

		if ($result) {
			$msg = ["msg" => "OK"];
		} else {
			$msg = ["msg" => "Error"];
		}

		echo json_encode($msg);
		return;

    default:
}

?>