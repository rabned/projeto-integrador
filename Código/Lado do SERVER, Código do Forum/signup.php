<?php
    //signup.php
    include 'header.php';
    include 'connect.php'; 
     
    echo '<h3>Sign up</h3>';
     
    if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        echo false;
    }
    else
    {
        /* so, the form has been posted, we'll process the data in three steps:
            1.  Check the data
            2.  Let the user refill the wrong fields (if necessary)
            3.  Save the data 
        */
        $errors = array(); /* declare the array for later use */
         
        if(isset($_POST['user_name']))
        {
            //the user name exists
            if(!ctype_alnum($_POST['user_name']))
            {
                $errors[] = 'The username can only contain letters and digits.';
            }
            if(strlen($_POST['user_name']) > 30)
            {
                $errors[] = 'The username cannot be longer than 30 characters.';
            }
        }
        else
        {
            $errors[] = 'The username field must not be empty.';
        }
         
         
        if(isset($_POST['user_pass']))
        {
            if($_POST['user_pass'] != $_POST['user_pass_check'])
            {
                $errors[] = 'The two passwords did not match.';
            }
        }
        else
        {
            $errors[] = 'The password field cannot be empty.';
        }
         
        if(!empty($errors)) /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/
        {
            echo 'Uh-oh.. a couple of fields are not filled in correctly..';
            echo '<ul>';
            foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */
            {
                echo '<li>' . $value . '</li>'; /* this generates a nice error list */
            }
            echo '</ul>';
        }
        else
        {
            //the form has been posted without, so save it
            //notice the use of mysql_real_escape_string, keep everything safe!
            //also notice the sha1 function which hashes the password
            $username = $_POST['user_name'];
            $userpass = sha1($_POST['user_pass']);
            $usermat = $_POST['user_mat'];
            $usertype = $_POST['user_type'];
            $sql = "INSERT INTO users(user_name, user_level, user_pass ,user_mat, user_type, user_date) VALUES('$username', 0, '$userpass', '$usermat', '$usertype', NOW()";
                             
            $result = mysql_query($connection, $sql);
            if(!$result)
            {
                //something went wrong, display the error
                echo 'Something went wrong while registering. Please try again later.';
                //echo mysql_error(); //debugging purposes, uncomment when needed
            }
            else
            {
                echo 'Successfully registered. You can now <a href="signin.php">sign in</a> and start posting! :-)';
            }
        }
    }
     
    include 'footer.php';
?>