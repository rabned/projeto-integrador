<?php
	session_start(); 
	//create_cat.php
	include 'connect.php';
	include 'header.php';

	if($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		//the form hasn't been posted yet, display it
		echo "<form method='post' action=''>
		Category name: <input type='text' name='cat_name' />
		Category description: <textarea name='cat_description' /></textarea>
		<input type='submit' value='Add category' />
	 	</form>";
	}
	else
	{
		$name = $_POST['cat_name'];
		$description = $_POST['cat_description'];

		//the form has been posted, so save it
		$sql = "INSERT INTO categories(cat_name, cat_description) VALUES('$name', '$description')";

		$result = mysqli_query($connection, $sql);
		if(!$result)
		{
		    //something went wrong, display the error
		    echo 'Error' . mysqli_error();
		}
		else
		{
		    echo 'New category successfully added.';
		}
	}	

	include 'footer.php';
?>