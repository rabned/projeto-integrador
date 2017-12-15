<?php
    //create_cat.php
     
    include_once 'header.php';
     
    //first select the category based on $_GET['cat_id']
    $id = $_GET['id'];

    $sql = "SELECT topic_id, topic_subject, topic_by FROM topics WHERE topic_id = '$id'";
    
    $result = mysqli_query($connection, $sql);
     
    if(!$result)
    {
        echo 'Não foi possivel carregar os posts, tente novamente mais tarde.';
    }
    else
    {
        if(mysqli_num_rows($result) == 0)
        {
            echo '404 - NADA ENCONTRADO';
        }
        else
        {
            //display category data
            $topicby = 0;
            while($row = mysqli_fetch_assoc($result))
            {
                $topicby = $row['topic_by'];
                echo '<div class="topic_title">
                        <p>'.$row['topic_subject'].'</p>
                    </div>';
            }
         
            //do a query for the topics
            $topicid = $_GET['id'];
            $sql = "SELECT post_id, post_topic, post_content, post_date, post_by, post_replied_to, user_id, user_display_name, user_photo, user_level, user_course, user_type FROM `posts` LEFT JOIN `users` ON post_by = user_id WHERE post_topic = '$topicid'";
             
            $result = mysqli_query($connection, $sql);
             
            if(!$result)
            {
                echo 'Não foi possivel carregar os posts, tente novamente mais tarde.';
            }
            else
            {
                if(mysqli_num_rows($result) == 0)
                {
                    echo 'Nenhum post para essa pergunta.';
                }
                else
                {
                         
                    while($row = mysqli_fetch_assoc($result))
                    {   
                        $day = date('d/m/Y', strtotime($row['post_date']) - 10800);
                        $hour = date('H:i', strtotime($row['post_date']) - 10800);
                        $posted = $day." às ". $hour;

                        $sql1 = 'SELECT user_display_name FROM `users` INNER JOIN `posts` ON user_id = post_by WHERE post_id = '.$row['post_replied_to'];
                        $result1 = mysqli_query($connection, $sql1);
                        $fetch1 = mysqli_fetch_assoc($result1);

                        if(!$result1){
                            echo mysqli_error($result1);
                        }
                        $footer = "";
                        if($topicby == $row['user_id']){
                            $footer = "<p>AUTOR DA QUESTÃO</p>";
                        }

                        echo '<div class="post_div'; if(mysqli_num_rows($result1) == 0){echo ' post_question ';} echo'" id='.$row['post_id'].'>
                                <div class="time_posted"><i class="fa fa-clock-o" aria-hidden="true"></i>
    <p>'.$posted.'</p>'; if(mysqli_num_rows($result1) != 0){echo '<a href="#'.$row['post_replied_to'].'">em resposta à '.$fetch1['user_display_name'].'</a>'; }echo '</div>
                                <div class="user_data">
                                    <div class="image">
                                        <img src="'.$row['user_photo'].'">
                                    </div>
                                    <div class="data">
                                        <p>'."Nome: ".$row['user_display_name'].'</p>';
                                        if($row['user_type'] == "Aluno"){
                                            echo '<p>'."Cursando: ".$row['user_course'].'</p>';
                                        }
                                        else{
                                            echo '<p>'.$row['user_type'].'</p>';
                                        }
                                        //<p>'."Nível: ".$row['user_level'].'</p>
                                    echo '</div>
                                </div>
                                <div class="post_content">
                                    <p>'.$row['post_content'].'</p>
                                </div>
                                <div class="footer_inner_post">
                                    <a href="?id='.$topicid.'&post_id='.$row['post_id'].'"class="reply"><i class="fa fa-reply" aria-hidden="true"></i> responder</a>
                                        '.$footer.'
                                </div>
                            </div>';
                    }
                }
            }
        }
    }
     
    include 'footer.php';
?>