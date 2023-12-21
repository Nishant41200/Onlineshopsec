<?php
include 'backend/databaseConnect.php';
$deliveryIDforModal= isset($_POST['viewDetailsID']) ? intval($_POST['viewDetailsID']) : 0;
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Delivery details | ShopOnline</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script src="ajax/ajax.js"></script>
	<link rel="stylesheet" href="button.css">




</head>
<body>
    <div class="container">
        <p id="success"></p>
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <!-- ... (rest of the HTML code remains unchanged) ... -->
                </div>
            </div>
            <table id="table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Quantity</th>
                        <th>Cost</th>
                        <th>Product Photo</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $stmt = $conn->prepare("SELECT product.productID, product.productName, product.productPrice, product.productPhoto, cart.quantity, cart.cost FROM product, cart WHERE cart.productID = product.productID AND cart.deliveryID = ?");
                    $stmt->bind_param("i", $deliveryIDforModal);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $i = 1;
                    while ($row = mysqli_fetch_array($result)) {
                    ?>

                        <tr>
                            <td><?php echo $row["productID"]; ?></td>
                            <td><?php echo $row["productName"]; ?></td>
                            <td><?php echo $row["productPrice"]; ?></td>
                            <td><?php echo $row["quantity"]; ?></td>
                            <td><?php echo $row["cost"]; ?></td>
                            <td><img src="<?php echo $row["productPhoto"]; ?>" alt="Photo" style="width:150px;"></td>
                        </tr>

                    <?php
                        $i++;
                    }

                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>


	<!-- Edit Modal HTML -->

	<!-- Delete Modal HTML -->
	<!-- <div id="deleteProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form  action="backend/deleteProduct.php" method="POST">
						
					<div class="modal-header">						
						<h4 class="modal-title">Delete Product</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="id_d" name="id" class="form-control">					
						<p>Are you sure you want to delete these products?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<button type="button" class="btn btn-danger" id="delete">Delete</button>
					</div>
				</form>
			</div>
		</div>
	</div> -->
<br><br><br>
 <div class="text-center">
 	<form action="yourOrders.php" method="POST">
			<input type="hidden" name="email" value="<?php echo $email; ?>">
            <button type="submit" class="btn btn-primary">Go to your orders</button>
    </form>             
 </div>







</body>
</html>   