<?PHP
session_start();

include("database.php");
if( !verifyAdmin($con) ) 
{
	header( "Location: index.php" );
	return false;
}
?>
<?PHP
$id_cafe	= (isset($_REQUEST['id_cafe'])) ? trim($_REQUEST['id_cafe']) : '0';
$act 		= (isset($_REQUEST['act'])) ? trim($_REQUEST['act']) : '';	

$cafe_name	= (isset($_POST['cafe_name'])) ? trim($_POST['cafe_name']) : '';
$location	= (isset($_POST['location'])) ? trim($_POST['location']) : '';
$cuisine	= (isset($_POST['cuisine'])) ? trim($_POST['cuisine']) : '';
$price_range= (isset($_POST['price_range'])) ? trim($_POST['price_range']) : '';
$type_cafe	= (isset($_POST['type_cafe'])) ? trim($_POST['type_cafe']) : '';

$cafe_name	=	mysqli_real_escape_string($con, $cafe_name);
$cuisine	=	mysqli_real_escape_string($con, $cuisine);

$success = "";

if($act == "add")
{	
	$SQL_insert = " 
	INSERT INTO `cafe`(`id_cafe`, `cafe_name`, `location`, `cuisine`, `photo`, `price_range`, `type_cafe`) 
	VALUES (NULL, '$cafe_name', '$location', '$cuisine', '', '$price_range', '$type_cafe')";		
										
	$result = mysqli_query($con, $SQL_insert);
	
	$id_cafe = mysqli_insert_id($con);
	
	// -------- Photo -----------------
	if(isset($_FILES['photo'])){
		 
		  $file_name = $_FILES['photo']['name'];
		  $file_size = $_FILES['photo']['size'];
		  $file_tmp = $_FILES['photo']['tmp_name'];
		  $file_type = $_FILES['photo']['type'];
		  
		  $fileNameCmps = explode(".", $file_name);
		  $file_ext = strtolower(end($fileNameCmps));
		  
		  if(empty($errors)==true) {
			 move_uploaded_file($file_tmp,"upload/".$file_name);
			 
			$query = "UPDATE `cafe` SET `photo`='$file_name' WHERE `id_cafe` = '$id_cafe'";			
			$result = mysqli_query($con, $query) or die("Error in query: ".$query."<br />".mysqli_error($con));
		  }else{
			 print_r($errors);
		  }  
	}
	// -------- End Photo -----------------
	
	$success = "Successfully Add";
	
	//print "<script>self.location='a-cafe.php';</script>";
}

if($act == "edit")
{	
	$SQL_update = " UPDATE
						`cafe`
					SET
						`cafe_name` = '$cafe_name',
						`location` = '$location',
						`cuisine` = '$cuisine',
						`price_range` = '$price_range',
						`type_cafe` = '$type_cafe'
					WHERE `id_cafe` =  '$id_cafe'";	
										
	$result = mysqli_query($con, $SQL_update) or die("Error in query: ".$SQL_update."<br />".mysqli_error($con));
	
	// -------- Photo -----------------
	if(isset($_FILES['photo'])){
		 
		  $file_name = $_FILES['photo']['name'];
		  $file_size = $_FILES['photo']['size'];
		  $file_tmp = $_FILES['photo']['tmp_name'];
		  $file_type = $_FILES['photo']['type'];
		  
		  $fileNameCmps = explode(".", $file_name);
		  $file_ext = strtolower(end($fileNameCmps));
		  
		  if(empty($errors)==true) {
			 move_uploaded_file($file_tmp,"upload/".$file_name);
			
			$query = "UPDATE `cafe` SET `photo`='$file_name' WHERE `id_cafe` = '$id_cafe'";		
			$result = mysqli_query($con, $query) or die("Error in query: ".$query."<br />".mysqli_error($con));
		  }else{
			 print_r($errors);
		  }  
	}
	// -------- End Photo -----------------
	
	$success = "Successfully Update";
	//print "<script>alert('Successfully Update'); self.location='a-cafe.php';</script>";
}

if($act == "del")
{
	$SQL_delete = " DELETE FROM `cafe` WHERE `id_cafe` =  '$id_cafe' ";
	$result = mysqli_query($con, $SQL_delete);
	
	$success = "Successfully Delete";
	//print "<script>self.location='a-cafe.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<title>Café Recommendation System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<link href="css/table.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

<!-- include summernote css-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- include summernote js-->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<style>
a { text-decoration : none ;}

body,h1,h2,h3,h4,h5,h6 {font-family: "Poppins", sans-serif}

body, html {
  height: 100%;
  line-height: 1.8;
}

/* Full height image header */
.bgimg-1 {
  background-position: top;
  background-size: cover;
  background-attachment:fixed;
  background-image: url(images/banner.jpg);
  min-height:100%;
  background-color: rgba(0, 0, 0, 0.2);
  background-blend-mode: overlay; 
}

.w3-bar .w3-button {
  padding: 16px;
}
</style>

<body class="bgimg-1">

<?PHP include("menu-admin.php"); ?>

<!--- Toast Notification -->
<?PHP 
if($success) { Notify("success", $success, "a-cafe.php"); }
?>	

<div class="" >

	<div class="w3-padding-32"></div>
	
	<div class=" w3-center w3-text-white w3-padding-32">
		<span class="w3-xlarge"><b>CAFE LIST</b></span><br>
	</div>


	<!-- Page Container -->
	<div class="w3-container w3-content" style="max-width:1200px;">    
	  <!-- The Grid -->
	  <div class="w3-row w3-white w3-card w3-padding">
	  
		<a onclick="document.getElementById('add01').style.display='block'; " class="w3-margin-bottom w3-right w3-button w3-amber w3-round "><i class="fa fa-fw fa-lg fa-plus"></i> Add</a>
		
		<div class="w3-row w3-margin ">
		<div class="table-responsive">
		<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th>#</th>
					<th>Photo</th>
					<th>Cafe Name</th>
					<th>Location</th>
					<th>Description / Cuisine</th>
					<th>Price Range</th>
					<th>Type of Cafe</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?PHP
			$bil = 0;
			$SQL_list = "SELECT * FROM `cafe` ";
			$result = mysqli_query($con, $SQL_list) ;
			while ( $data	= mysqli_fetch_array($result) )
			{
				$bil++;
				$photo	= $data["photo"];
				if(!$photo) $photo = "noimage.jpg";
				$id_cafe= $data["id_cafe"];
			?>			
			<tr>
				<td><?PHP echo $bil ;?></td>
				<td><img src="upload/<?PHP echo $photo; ?>" class="w3-round-large w3-image" alt="image" style="width:100%;max-width:60px"></td>
				<td><?PHP echo $data["cafe_name"] ;?></td>
				<td><?PHP echo $data["location"] ;?></td>
				<td><?PHP echo substrwords($data["cuisine"],100) ;?></td>
				<td><?PHP echo $data["price_range"] ;?></td>
				<td><?PHP echo $data["type_cafe"] ;?></td>
				<td>
				<a href="#" onclick="document.getElementById('idEdit<?PHP echo $bil;?>').style.display='block'" class=""><i class="fa fa-fw fa-edit fa-lg"></i></a>
				
				<a title="Delete" onclick="document.getElementById('idDelete<?PHP echo $bil;?>').style.display='block'" class="w3-text-red"><i class="fa fa-fw fa-trash-alt fa-lg"></i></a>
				</td>
			</tr>
			
<div id="idEdit<?PHP echo $bil; ?>" class="w3-modal" style="z-index:10;">
	<div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:800px">
      <header class="w3-container "> 
        <span onclick="document.getElementById('idEdit<?PHP echo $bil; ?>').style.display='none'" 
        class="w3-button w3-large w3-circle w3-display-topright "><i class="fa fa-fw fa-times"></i></span>
      </header>

		<div class="w3-container w3-padding">
		
		<form action="" method="post" enctype="multipart/form-data" >
			<div class="w3-padding"></div>
			<b class="w3-large">Update Cafe</b>
			<hr>

				<div class="w3-section" >
					Photo
					<?PHP if($data["photo"] =="") { ?>
					<div class="custom-file">
						<input type="file" class="w3-input w3-border w3-round" name="photo" id="photo" accept=".jpeg, .jpg,.png,.gif">
					</div>
					<p></p>
					<?PHP } ?>
					
					<?PHP if($data["photo"] <>"") { ?>
					<div class="w3-input w3-border w3-round">
					<a class="w3-tag w3-green w3-round" target="_BLANK" href="upload/<?PHP echo $data["photo"]; ?>"><small>View</small></a>

					<a class="w3-tag w3-red w3-round" href="photo-del.php?id_cafe=<?PHP echo $data["id_cafe"];?>"><small>Remove</small></a>
					</div>
					<?PHP } else { ?><span class="w3-tag w3-round"> <small>None</small></span><?PHP } ?>
					<small>  only JPEG, JPG, PNG or GIF allowed </small>
				</div>
			  
				<div class="w3-section" >
					Cafe Name *
					<input class="w3-input w3-border w3-round" type="text" name="cafe_name" value="<?PHP echo $data["cafe_name"]; ?>" required>
				</div>
				
				<div class="w3-section" >
					Location *
					<input class="w3-input w3-border w3-round" type="text" name="location" value="<?PHP echo $data["location"]; ?>" required>
				</div>
				
				<div class="w3-section" >
					Description / Cuisine *
					<textarea class="w3-input w3-border w3-round" name="cuisine" id="makeMeSummernote" rows="5" required><?PHP echo $data["cuisine"]; ?></textarea>
				</div>
				
				<div class="w3-section" >
					Price Range *
					<input class="w3-input w3-border w3-round" type="text" name="price_range" value="<?PHP echo $data["price_range"]; ?>" required>
				</div>
				
				<div class="w3-section" >
					Type Of Cafe *
					<input class="w3-input w3-border w3-round" type="text" name="type_cafe" value="<?PHP echo $data["type_cafe"]; ?>" required>
				</div>
			  
			<hr class="w3-clear">
			<input type="hidden" name="id_cafe" value="<?PHP echo $data["id_cafe"];?>" >
			<input type="hidden" name="act" value="edit" >
			<button type="submit" class="w3-button w3-amber w3-text-white w3-margin-bottom w3-round">SAVE CHANGES</button>

		</form>
		</div>
	</div>
<div class="w3-padding-24"></div>
</div>

<div id="idDelete<?PHP echo $bil; ?>" class="w3-modal" style="z-index:10;">
	<div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:500px">
      <header class="w3-container "> 
        <span onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'" 
        class="w3-button w3-large w3-circle w3-display-topright "><i class="fa fa-fw fa-times"></i></span>
      </header>

		<div class="w3-container w3-padding">
		
		<form action="" method="post">
			<div class="w3-padding"></div>
			<b class="w3-large">Confirmation</b>
			  
			<hr class="w3-clear">			
			Are you sure to delete this record ?
			<div class="w3-padding-16"></div>
			
			<input type="hidden" name="id_cafe" value="<?PHP echo $data["id_cafe"];?>" >
			<input type="hidden" name="act" value="del" >
			<button type="button" onclick="document.getElementById('idDelete<?PHP echo $bil; ?>').style.display='none'"  class="w3-button w3-gray w3-text-white w3-margin-bottom w3-round">CANCEL</button>
			
			<button type="submit" class="w3-right w3-button w3-red w3-text-white w3-margin-bottom w3-round">YES, CONFIRM</button>
		</form>
		</div>
	</div>
</div>				
			<?PHP } ?>
			</tbody>
		</table>
		</div>
		</div>

		
	  <!-- End Grid -->
	  </div>
	  
	<!-- End Page Container -->
	</div>
	
	<div class="w3-padding-24"></div>
	
</div>



<div id="add01" class="w3-modal" >
    <div class="w3-modal-content w3-round-large w3-card-4 w3-animate-zoom" style="max-width:800px">
      <header class="w3-container "> 
        <span onclick="document.getElementById('add01').style.display='none'" 
        class="w3-button w3-large w3-circle w3-display-topright "><i class="fa fa-fw fa-times"></i></span>
      </header>
	  
      <div class="w3-container w3-padding">
		
		<form action="" method="post" enctype="multipart/form-data" >
			<div class="w3-padding"></div>
			<b class="w3-large">Add Cafe</b>
			<hr>
			  
				
				<div class="w3-section" >
					Photo *
					<input class="w3-input w3-border w3-round" type="file" name="photo" required >
					<small>  only JPEG, JPG, PNG or GIF allowed </small>
				</div>
				
				<div class="w3-section" >
					Cafe Name *
					<input class="w3-input w3-border w3-round" type="text" name="cafe_name"  required>
				</div>
				
				<div class="w3-section" >
					Location *
					<input class="w3-input w3-border w3-round" type="text" name="location" value="" required>
				</div>
				
				<div class="w3-section" >
					Description / Cuisine *
					<textarea class="w3-input w3-border w3-round" name="cuisine" id="makeMeSummernote2" rows="5"  required></textarea>
				</div>
				
				<div class="w3-section" >
					Price Range *
					<input class="w3-input w3-border w3-round" type="text" name="price_range" value="" required>
				</div>
				
				<div class="w3-section" >
					Type Of Cafe *
					<input class="w3-input w3-border w3-round" type="text" name="type_cafe" value="" required>
				</div>
			  
			  <hr class="w3-clear">
			  
			  <div class="w3-section" >
				<input name="act" type="hidden" value="add">
				<button type="submit" class="w3-button w3-amber w3-text-white w3-margin-bottom w3-round">SUBMIT</button>
			  </div>
			</div>  
		</form> 
         
      </div>
<div class="w3-padding-24"></div>
</div>

<!-- Script -->
<script type="text/javascript">
	$('#makeMeSummernote,#makeMeSummernote2').summernote({
		height:200,
	});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<!--<script src="assets/demo/datatables-demo.js"></script>-->


<script>
$(document).ready(function() {

  
	$('#dataTable').DataTable( {
		paging: true,
		
		searching: true
	} );
		
	
});
</script>

 
<script>

// Toggle between showing and hiding the sidebar when clicking the menu icon
var mySidebar = document.getElementById("mySidebar");

function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
  } else {
    mySidebar.style.display = 'block';
  }
}

// Close the sidebar with the close button
function w3_close() {
    mySidebar.style.display = "none";
}
</script>

</body>
</html>
