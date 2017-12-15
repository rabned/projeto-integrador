<?php 
    //create_cat.php
    include 'header.php';
     
    echo '<h2>Faça uma pergunta</h2>';
    if($_SESSION['signed_in'] == false)
    {
        //the user is not signed in
        echo 'Você precisa estar <a href="/forum/signin.php">logado</a> para fazer uma pergunta.';
    }
    else
    {
        //the user is signed in
        if($_SERVER['REQUEST_METHOD'] != 'POST')
        {   
            //the form hasn't been posted yet, display it
            //retrieve the categories from the database for use in the dropdown
            $sql = "SELECT cat_id, cat_name, cat_description FROM `categories`";
             
            $result = mysqli_query($connection, $sql);
             
            if(!$result)
            {
                //the query failed, uh-oh :-(
                echo 'Erro ao pegar categorias, tente novamente.';
            }
            else
            {
                if(mysqli_num_rows($result) == 0)
                {
                    //there are no categories, so a topic can't be posted
                    if($_SESSION['user_level'] == 1)
                    {
                        echo 'You have not created categories yet.';
                    }
                    else
                    {
                        echo 'Antes de fazer uma pergunta, espere que um ADM adicione matérias.';
                    }
                }
                else
                {
             
                    echo '<form id="create_topic_form" method="post" action="">
                            <label for="topic_subject" required>Pergunta:</label>
                            <input type="text" name="topic_subject" />
                            <label for="topic_cat">Matéria:</label>
                        '; 
                     
                    echo '<select name="topic_cat">';
                        while($row = mysqli_fetch_assoc($result))
                        {
                            echo '<option value="' . $row['cat_id'] . '">' . $row['cat_name'] . '</option>';
                        }
                    echo '</select>'; 
                         
                    echo '<label for="post_content">Detalhes:</label>
                        <textarea name="post_content" required></textarea>
                        <input type="submit" value="Fazer pergunta" />
                     </form>';
                }
            }
        }
        else
        {
            //start the transaction
            $query  = "BEGIN WORK;";
            $result = mysqli_query($connection, $query);
             
            if(!$result)
            {
                //Damn! the query failed, quit
                echo 'Ocorreu um erro ao fazer a pergunta, tente novamente.';
            }
            else
            {
         
                //the form has been posted, so save it
                //insert the topic into the topics table first, then we'll save the post into the posts table
                $subject = $_POST['topic_subject'];
                $topiccat = $_POST['topic_cat'];
                $topicby = $_SESSION['user_id'];
                $sql = "INSERT INTO topics(topic_subject, topic_date, topic_cat, topic_by) VALUES('$subject', NOW(), '$topiccat', '$topicby')";
                          
                $result = mysqli_query($connection, $sql);
                if(!$result)
                {
                    //something went wrong, display the error
                    echo 'Ocorreu um erro ao fazer a pergunta, tente novamente.' . mysqli_error();
                    $sql = "ROLLBACK;";
                    $result = mysqli_query($connection, $sql);
                }
                else
                {
                    //the first query worked, now start the second, posts query
                    //retrieve the id of the freshly created topic for usage in the posts query
                    $topicid = mysqli_insert_id($connection);
                    
                    $postcontent = $_POST['post_content'];
                    $postby = $_SESSION['user_id'];
                    $sql = "INSERT INTO posts(post_content, post_date, post_topic, post_by) VALUES ('$postcontent', NOW(), '$topicid', '$postby')";

                    $result = mysqli_query($connection, $sql);
                     
                    if(!$result)
                    {
                        //something went wrong, display the error
                        echo 'Ocorreu um erro ao fazer a pergunta, tente novamente.' . mysqli_error();
                        $sql = "ROLLBACK;";
                        $result = mysqli_query($connection, $sql);
                    }
                    else
                    {
                        $sql = "COMMIT;";
                        $result = mysqli_query($connection, $sql);
                         
                        //after a lot of work, the query succeeded!
                        echo 'Você fez uma pergunta => <a href="topic.php?id='. $topicid . '"> SUA PERGUNTA</a>.';
                    }
                }
            }
        }
    }
     
    include 'footer.php';
?>