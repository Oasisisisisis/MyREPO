<table width="200" border="1">
  <tr>
    <td>id</td>
    <td>Product name</td>
    <td>Price</td>
    <td>Detail</td>
    <td>Remain number</td>
    <td>-</td>
  </tr>
<?php
require("dbconfig.php");
$sql = "select * from product;";
$stmt = mysqli_prepare($db, $sql ); //precompile sql指令，建立statement 物件，以便執行SQL
mysqli_stmt_execute($stmt); //執行SQL
$result = mysqli_stmt_get_result($stmt); //取得查詢結果

while (	$rs = mysqli_fetch_assoc($result)) { //用迴圈逐筆取出

	echo "<tr><td>" , $rs['id'] ,
	"</td><td>" , $rs['name'],
	"</td><td>" , $rs['price'], 
	"</td><td>", $rs['remain'],
    "</td><td>", $rs['detail'],
	"</td></tr>";
}
?>
</table>