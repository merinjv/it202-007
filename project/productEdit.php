<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //only Admin/ShopOwner can edit
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>
<?php
//saving
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$name = $_POST["name"];
	$quantity = $_POST["quantity"];
	$price = $_POST["price"];
	$desc = $_POST["description"];
 $visible = $_POST["visibility"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Products set name=:name, quantity=:quantity, price=:price, description=:desc, visibility=:visibility where id=:id");
		$r = $stmt->execute([
      ":id"=>$id,
			":name"=>$name,
		  ":quantity"=>$quantity,
      ":price"=>$price,
		  ":desc"=>$desc,
        ":visibility"=>$visible
		]);
		if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			flash("Error updating: " . var_export($e, true));
		}
	}
	else{
		flash("ID isn't set, we need an ID in order to update");
	}
}
?>
<?php
//fetching
$result = [];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Products where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Name</label>
	<input name="name" placeholder="Name" value="<?php echo $result["name"];?>"/>
	<label>Quantity</label>
	<input type="number" min="0" name="quantity" value="<?php echo $result["quantity"];?>"/>
	<label>Price</label>
	<input type="number" min="0" name="price" value=<?php echo intval($result["price"]);?> />
	<label>Description</label>
	<input type="text" name="description" value="<?php echo $result["description"];?>"/>
    <label>Visible to Public (Yes:0, No:1):</label>
    <select name="visibility">
            <option> <value="1">1</option>
            <option> <value="0">0</option>
            <option> <value="1">1</option>
    </select>
	<input type="submit" name="save" value="Update"/>
</form>


<?php require(__DIR__ . "/partials/flash.php");
