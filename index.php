<?php
	// Database Connection
		require_once('config.php');
	// End Database Connection

	//Insert Task name and task date SQL query
	$task = $_GET['task'] ?? '';
	if(isset($_POST['addtasks'])){
		$task_name = trim($_POST['task']);
		$task_date = $_POST['date'];
		$insert = "INSERT INTO task(task_name, task_date) values('$task_name', '$task_date')";
		$result = mysqli_query($db, $insert);
		
		if($result){
			header("location: index.php?task=add");
		}else{
			$error = "Error!";
		}
		mysqli_close($db);
	}
	//End Insert Task name and task date SQL query
	

	// Upcoming Task Query
	$uQuery = "SELECT * FROM task WHERE complete=0";
	$uResult = mysqli_query($db, $uQuery);	

	// Complete Task Query
	$cQuery = "SELECT * FROM task WHERE complete=1";
	$cResult = mysqli_query($db, $cQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Todo/Tasks</title>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/milligram.min.css">
	<style>
		body {
			margin-top: 30px;
		}

		#main {
			padding: 0px 150px 0px 150px;;
		}

		#action {
			width: 150px;
		}

		.success{
			color: green;
		}
	</style>
</head>
<body>
	<div class="container" id="main">
		<h1><a href="index.php">Tasks Manager</a></h1>
		<p>This is a sample project for managing our daily tasks. We're going to use HTML, CSS, PHP, JavaScript and MySQL
		for this project</p>


		<!-- if complete task is empty then this section wouldn't show -->
		<?php 
			$rows = mysqli_num_rows($cResult); 
			if($rows == 0 ){
				echo "<p>No Complete task Found</p>";

			}else{
		?>		
		<h4>Complete Tasks</h4>
		<table>
			<thead>
				<tr>
					<th></th>
					<th>Id</th>
					<th>Task</th>
					<th>Date</th>
					<th>Action</th>
				</tr>
			</thead> 

			<tbody>
				<?php  

					// Start Task Mark as Complete
					$taskcomplete = $_POST['incmarkid'] ?? '';

					// var_dump($taskcomplete);
					if($taskcomplete){
						$updateQuery = "UPDATE task SET complete=0 WHERE id={$taskcomplete} ";
						$updateResult = mysqli_query($db, $updateQuery);
						header("location: index.php");

					}
					//End Task Mark as Complete
					$i = 1;
					while($data2 = mysqli_fetch_assoc($cResult)):
				?>
				<tr>
					<td><input class="label-inline" type="checkbox"></td>
					<td><?php echo $i ?></td>
					<td><?php echo $data2['task_name'] ?></td>
					<td><?php echo $data2['task_date'] ?></td>
					<!-- <td>18th August 2020</td> -->
					<td><a class="deleteTask" data-cdelete="<?php echo $data2['id'] ?>" href='#'>Delete</a> | <a class="inmarkTask" data-markin="<?php echo $data2['id'] ?>" href='#'>Mark Incomplete</a>
					</td>
				</tr>

				<?php 
					$i++;
					endwhile; 
				?>
			</tbody>
		</table>
		<?php } ?>
		<!--End Complete Task  -->


		<!-- if upcoming task is empty then this section wouldn't show -->
		<?php 
			$rows = mysqli_num_rows($uResult); 
			if($rows == 0 ){
				echo "No task Found";
			}else{
		?>
		<form method="POST">
			<h4>Upcoming Task</h4>
			<table>
				<thead>
					<tr>
						<th></th>
						<th>Id</th>
						<th>Task</th>
						<th>Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>

					<!-- Upcoming SQL Query with PHP -->
					<?php 	

						// Delete Task From DataBase 
						$taskdelete = $_POST['cdeleteid'] ?? '';
						if($taskdelete){
							$delete = "DELETE FROM task WHERE id={$taskdelete}";
							$result = mysqli_query($db, $delete);
							header("location: index.php");
						}
						// End Delete Task DataBase


						// Start Task Mark as Complete
						$taskcomplete = $_POST['cmarkid'] ?? '';
						if($taskcomplete){
							$updateQuery = "UPDATE task SET complete=1 WHERE id={$taskcomplete} ";
							$updateResult = mysqli_query($db, $updateQuery);
							header("location: index.php");
						}
						//End Task Mark as Complete

						$j=1;
						while($data = mysqli_fetch_assoc($uResult)):
					?>
					<tr>
						<td><input class="label-inline" type="checkbox" name="bulkcheck[]" value="<?php echo $data['id'] ?>"></td>
						<td><?php echo $j ?></td>
						<td><?php echo $data['task_name'] ?></td>
						<td><?php echo $data['task_date'] ?></td>
						<td><a class="deleteTask" data-cdelete="<?php echo $data['id'] ?>" href="#"> Delete</a> | <a class="markTask" data-markc="<?php echo $data['id'] ?>" href='#'>Mark complete</a>
						</td>
					</tr>
					<?php 
						$j++;
						endwhile;
					?>
					<!--End Upcoming SQL Query with PHP -->


				</tbody>
			</table>

			<?php 
				$action = $_POST['action'] ?? "";
				if($action == "bulkcomplete"){
					$bulkcomplete = $_POST['bulkcheck'] ?? "";
					$_bulk = join(",", $bulkcomplete);
					$update = "UPDATE task SET complete=1 WHERE id in($_bulk)";
					$result = mysqli_query($db, $update);
					header("location: index.php");
					// var_dump($delete);

				}elseif($action == "bulkdelete"){
					$bulkdelete = $_POST['bulkcheck'] ?? "";
					$_bulk = join(",", $bulkdelete);
					$delete = "DELETE FROM task WHERE id in($_bulk)";
					$result = mysqli_query($db, $delete);
					header("location: index.php");
				}
			?>


			<select id="action" name="action">
				<option value="0">With Selected</option>
				<option value="bulkdelete">Delete</option>
				<option value="bulkcomplete">Mark As Complete</option>
			</select>
			<input class="button-primary" id="bulksubmit" type="submit" value="Submit">
		</form>
		<?php } ?>
		<!--End if upcoming task is empty then this section wouldn't show -->

		<p>...</p>
		<?php 
			if($task){
				$success = "Task has been Added successfully.";
				echo '<p class="success">'.$success.'</p>';
			}
		?>

		<h4>Add Tasks</h4>
		<form method="post">
			<fieldset>
				<label for="task">Task</label>
				<input type="text" placeholder="Task Details" id="task" name="task" required>
				<label for="date">Date</label>
				<input type="text" placeholder="Task Date" id="date" name="date" required>

				<input class="button-primary" type="submit" value="Add Task" name="addtasks">
			</fieldset>
		</form>
	</div>



	<!--complete Task as Mark Complete  -->
	<form method="POST" id="incompleteform">
		<input type="hidden" id="incmarkid" name="incmarkid">
	</form>
	<!--End complete Task as Mark Complete  -->	


	<!-- Upcoming Task as Delete -->
	<form method="POST" id="completeform">
		<input type="hidden" id="delete" name="cdeleteid">
	</form>
	<!-- End pcoming Task as Delete -->


	<!-- Upcoming Task as Mark Complete  -->
	<form method="POST" id="mcompleteform">
		<input type="hidden" id="cmarkid" name="cmarkid">
	</form>
	<!-- Upcoming Task as Mark Complete  -->	


	<!-- Bulk task complete as select -->
	<form method="POST" id="bulkform">
		<input type="hidden" id="bulkcomplete" name="bulkcomplete">
	</form>
	<!-- End Bulk task complete as select  -->




	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script>
		;(function ($){
			$(document).ready(function (){
				$(".deleteTask").on('click', function(){
					if(confirm("are you sure want to delete this task?")){
						var id= $(this).data("cdelete");
						$("#delete").val(id);
						$("#completeform").submit();
					}
				});

				$(".markTask").on('click', function(){
					var id= $(this).data("markc");
					$("#cmarkid").val(id);
					$('#mcompleteform').submit();
				});

				$(".inmarkTask").on('click', function(){
					var id= $(this).data("markin");
					$("#incmarkid").val(id);
					$('#incompleteform').submit();
				});
			});
		})(jQuery);
	</script>
</body>
</html>
