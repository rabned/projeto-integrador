<?php
 
	include 'header.php';
	include 'connect.php';

	if(!isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == false){
		if(isset($_POST['login']))
		{
			$user_id = $_POST['user_id'];

			$get_user = "SELECT * FROM `users` WHERE user_id='$user_id'";

			$result = mysqli_query($connection, $get_user);
    		$count = mysqli_num_rows($result);

    		if($count > 0)
    		{
    			$fetch = mysqli_fetch_assoc($result);
    			$_SESSION['signed_in'] = true;
    			$_SESSION['user_id'] = $fetch['user_id'];
    			$_SESSION['user_name'] = $fetch['user_name'];
    			$_SESSION['user_level'] = $fetch['user_level'];
    		}
		}
	}
	
	if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
	{
		?>



		<?php
	}

	$sql = "SELECT cat_id, cat_name, cat_description FROM `categories`";
 
	$result = mysqli_query($connection, $sql);
	 
	if(!$result)
	{
	    echo 'Não foi possivel mostrar as matérias, tente novamente depois';
	}
	else
	{
	    if(mysqli_num_rows($result) == 0)
	    {
	        echo 'Nenhum matéria ainda.';
	    }
	    else
	    {
	        //prepare the table
	        echo '<table border="1">
	              <tr>
	                <th>Matéria</th>
	                <th>Última pergunta</th>
	              </tr>';

	        while($row = mysqli_fetch_assoc($result))
	        {
	        	$topicmsg = "";

	        	$sql1 = 'SELECT topic_id, topic_subject, topic_date FROM `topics` WHERE topic_cat = '.$row['cat_id'].' AND topic_date = (SELECT MAX(topic_date) FROM `topics` WHERE topic_cat = '.$row['cat_id'].')';
	        	$result1 = mysqli_query($connection, $sql1);
	        	if(mysqli_num_rows($result1) == 0){
	        		$topicmsg = "Essa matéria ainda não possui perguntas.";
	        	}else{
	        		$fetch = mysqli_fetch_assoc($result1);
	        		$topicmsg = '<p class="topic_category"><a href="topic.php?id='.$fetch['topic_id'].'">'.$fetch['topic_subject'].'</a> às '.date('H:i', strtotime($fetch['topic_date']) - 10800).'</p>';
	        	}

	            echo '<tr>';
	                echo '<td class="leftpart">';
	                    echo '<h3><a href="category.php?id='.$row['cat_id'].'">' . $row['cat_name'] . '</a></h3>' . $row['cat_description'];
	                echo '</td>';
	                echo '<td class="rightpart">';
	                    echo $topicmsg; 
	                echo '</td>';
	            echo '</tr>';
	        }
	    }
	}

	include 'footer.php';

?>