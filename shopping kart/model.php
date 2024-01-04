<?php
require('dbconfig.php');

function getProductList()
{
    global $db;
    $sql = "select * from product;";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}

function getOrderList()
{
    global $db;
    $sql = "SELECT id, client_id, owner_id, state FROM `order`;"; // 注意这里的``来转义order关键字
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}

function getUserProductList($user_id)
{
    global $db;
    $sql = "SELECT * FROM product WHERE user_id = ?"; 
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}


function addProduct($name, $price, $detail, $remain, $id, $user_id)
{
    global $db;
    if ($id > 0) {
        $sql = "UPDATE product SET name=?, price=?, detail=?, `remain`=?, user_id=? WHERE id=?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $price, $detail, $remain, $user_id, $id);
    } else {
        $sql = "INSERT INTO product (name, price, detail, remain, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $price, $detail, $remain, $user_id);
    }
    mysqli_stmt_execute($stmt);
    return true;
}

function checkout($cart, $user_id)
{
    global $db;

    // 檢查購物車是否為空
    if (empty($cart)) {
        return ["error" => "購物車為空"];
    }

    // 開始事務
    mysqli_begin_transaction($db);

    try {
        // 這裡可以進行進一步的購物車內容檢查，確保商品存在、庫存充足等

        // 假設這裡是一個簡單的結帳邏輯，將購物車內容插入訂單表中
        $insertOrderSql = "INSERT INTO `order` (product_id, quantity, client_id, owner_id, state) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $insertOrderSql);

        foreach ($cart as $cartItem) {
            $product_id = $cartItem['product_id'];
            $quantity = $cartItem['quantity'];
            $client_id = $user_id; // 假設 client_id 為使用者 ID
            $owner_id = $cartItem['user_id']; // 假設 owner_id 為商品擁有者的 ID
            $state = 1; // 假設 state=1 表示未處理訂單

            // 綁定參數
            mysqli_stmt_bind_param($stmt, "iiiii", $product_id, $quantity, $client_id, $owner_id, $state);
            
            // 執行準備好的語句
            mysqli_stmt_execute($stmt);
        }

        // 清空購物車（這裡需要根據實際情況實現清空購物車的邏輯）
        clearCart($user_id);
        

        // 提交事務
        mysqli_commit($db);

        return ["message" => "結帳成功"];
        
    } catch (Exception $e) {
        // 如果發生錯誤，回滾事務
        mysqli_rollback($db);
        return ["error" => "結帳時發生錯誤：" . $e->getMessage()];
    }
}


function clearCart($user_id)
{
    global $db;
    $clearCartSql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($db, $clearCartSql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
}

function updateProduct($id, $name, $price, $detail)
{
	echo $id, $name, $price, $detail;
	return;
}

function delProduct($id)
{
    global $db;
    $sql = "delete from product where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return true;
}
function addToCart($product_id, $user_id) 
{
    global $db;
    // 查詢是否有一樣的商品
    $selectSql = "SELECT * FROM cart WHERE product_id=? AND user_id=?";
    $selectStmt = mysqli_prepare($db, $selectSql);
    mysqli_stmt_bind_param($selectStmt, "ii", $product_id, $user_id);
    mysqli_stmt_execute($selectStmt);
    $selectResult = mysqli_stmt_get_result($selectStmt);

    if (mysqli_num_rows($selectResult) == 0) {
        // 新加入一筆資料
        $insertSql = "INSERT INTO cart (product_id, quantity, user_id) VALUES (?, 1, ?)";
        $insertStmt = mysqli_prepare($db, $insertSql);
        mysqli_stmt_bind_param($insertStmt, "is", $product_id, $user_id);
        mysqli_stmt_execute($insertStmt);
        echo json_encode(["message" => "成功加入購物車"]);
    } else {
        // 獲取 product 資料表中的庫存數量
        $productSql = "SELECT remain FROM product WHERE id=?";
        $productStmt = mysqli_prepare($db, $productSql);
        mysqli_stmt_bind_param($productStmt, "i", $product_id);
        mysqli_stmt_execute($productStmt);
        $productResult = mysqli_stmt_get_result($productStmt);
        
        if ($productRow = mysqli_fetch_assoc($productResult)) {
            $productRemain = $productRow['remain'];
        } else {
            echo "Error fetching product information.";
            return;
        }

        // 查詢 cart 中的數量
        $cartSql = "SELECT quantity FROM cart WHERE product_id=?";
        $cartStmt = mysqli_prepare($db, $cartSql);
        mysqli_stmt_bind_param($cartStmt, "i", $product_id);
        mysqli_stmt_execute($cartStmt);
        $cartResult = mysqli_stmt_get_result($cartStmt);

        if ($cartRow = mysqli_fetch_assoc($cartResult)) {
            $cartQuantity = $cartRow['quantity'];
            // 檢查是否超過庫存上限
            if (($cartQuantity + 1) > $productRemain) {
                echo json_encode(["error" => "已達購買上限"]);
                return;
            }
        }
        // 更新 cart 資料表
        $updateSql = "UPDATE cart SET quantity=quantity+1 WHERE product_id=? AND user_id=?";
        $updateStmt = mysqli_prepare($db, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "is", $product_id, $user_id);
        mysqli_stmt_execute($updateStmt);
        echo json_encode(["message" => "成功加入購物車"]);
    }
}



function removeFromCart($id)
{
    global $db;
    $sql = "delete from cart where id=?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return true;
}

function getCartList($user_id)
{
    global $db;
    $sql = "SELECT product.user_id, product.name, product.price, cart.quantity, cart.id, cart.product_id , cart.quantity, cart.user_id FROM cart LEFT JOIN product ON product.id = cart.product_id WHERE cart.user_id = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $rows = array();
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
    }
    return $rows;
}


function login($id, $pwd) {
	global $db;
	//verify with DB
	//dangerous way
	//$sql = "select role from user where id='$id' and pwd='$pwd';";
	//$stmt = mysqli_prepare($db, $sql );

	//safer way
	
	$sql = "select role from user where id=? and pwd=?;";
	$stmt = mysqli_prepare($db, $sql );
	mysqli_stmt_bind_param($stmt, "ss", $id, $pwd);
	

	mysqli_stmt_execute($stmt); //執行SQL
	$result = mysqli_stmt_get_result($stmt); //取得查詢結果
	if($r = mysqli_fetch_assoc($result)) {		
		return $r['role'];
	} else {
		return 0;
	}
}

function register($id, $pwd, $role) {
    global $db;
    $sql = "INSERT INTO user (id, pwd, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $id, $pwd, $role);
    mysqli_stmt_execute($stmt);
    return true;
}

?>
