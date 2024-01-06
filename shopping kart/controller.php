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
        $owner_id = $_SESSION['id']; // 獲取當前登陸商家的id
        $order = getOrderListByOwner(owner_id);
        if ($order === false) {
            echo json_encode(["error" => "Error fetching order list."]);
        } else {
            echo json_encode($order);
        }
        return;
    case "checkout":
        $user_id = $_REQUEST['userId'];
        
        // 獲取購物車內容
        $cart = getCartList($user_id);
        
        // 呼叫結帳函數
        $checkoutResult = checkout($cart, $user_id);
        
        // 返回結果
        echo json_encode($checkoutResult);
        return;
    case "submitReview":
    // 確保是 POST 請求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Invalid request method']);
        return;
    }

    // 確保有 POST 數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查數據是否完整
    if (!isset($data['order_id'], $data['owner_id'], $data['client_id'], $data['state'], $data['rating'])) {
        echo json_encode(['error' => 'Incomplete data']);
        return;
    }

    // 可以根據需要檢查其他數據

    // 調用模型中的函數處理提交評價
    $result = submitReviewToModel($data['order_id'], $data['owner_id'], $data['client_id'], $data['state'], $data['rating']);

    if ($result) {
        echo json_encode(['message' => 'OK']);
    } else {
        echo json_encode(['error' => 'Failed to submit review']);
    }
    break;
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