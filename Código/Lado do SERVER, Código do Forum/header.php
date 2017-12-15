<?php
    session_start();
    
    include 'connect.php';
    //echo var_dump($_SESSION);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="A short description." />
    <meta name="keywords" content="put, keywords, here" />
    <title>FORURRRRM</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="jquery.min.js"></script>
    <script src="https://use.fontawesome.com/cc419fd8cd.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

</head>
<body>

    <?php
    if(isset($_POST['post_content'])){

        $sql = 'SELECT post_topic, post_id FROM `posts` WHERE post_id = '.$_GET['post_id'];
        $result = mysqli_query($connection, $sql);
        if(!$result){
            echo mysqli_error($result);
        }
        else{
            $fetch = mysqli_fetch_assoc($result);

            $topicid = $fetch['post_topic'];
            $postreplied = $fetch['post_id'];
            $postcontent = $_POST['post_content'];
            
            $postby = $_SESSION['user_id'];

            $sql = "INSERT INTO posts(post_content, post_date, post_topic, post_by, post_replied_to) VALUES ('$postcontent', NOW(), '$topicid', '$postby', '$postreplied')";
            $result = mysqli_query($connection, $sql);
            if(!$result){
                echo mysql_error();
                $sql = "ROLLBACK;";
                $result = mysqli_query($connection, $sql);
            }
            else{
                $sql = "COMMIT;";
                $result = mysqli_query($connection, $sql);

                $_GET['post_id'] = 0;
            }
        }
    }
    else{
        if(isset($_GET['post_id']) && !isset($_SESSION['signed_in'])){
            echo '<script>alert("Não é possivel responder a um post sem antes logar.")</script>';
        }
        else if(isset($_GET['post_id']) && $_GET['post_id'] != 0 && isset($_SESSION['signed_in'])){
            echo '<div id="reply">
                    <div id="reply_center">
                        <div id="reply_inner">';                    
                            $sql = 'SELECT post_content, user_display_name, topic_subject FROM `posts` INNER JOIN `users` ON post_by = user_id INNER JOIN `topics` ON post_topic = topic_id WHERE post_id = '.$_GET['post_id'];

                            $result = mysqli_query($connection, $sql);
                            if(!$result){
                                echo mysqli_error();
                                echo "<script>alert(".$result.");</script>";
                            }
                            else{
                                $fetch = mysqli_fetch_assoc($result);

                                echo '<div id="reply_title">
                                        <p>'.$fetch['topic_subject'].'</p>
                                    </div>';
                                echo '<div id="reply_content_div">
                                        <p id="reply_to">Repondendo: '.$fetch['user_display_name'].'</p>
                                        <p id="reply_content">'.$fetch['post_content'].'</p>
                                    </div>';
                            }
                        echo '<form id="post" method="post" action="">
                                <textarea name="post_content" required></textarea>
                            </form>
                            <div id="reply_buttons">
                                <input form="post" type="submit" value="Responder"/>
                                <a href="?id='.$_GET['id'].'" id="cancel_reply">Cancelar</a>                            
                            </div>
                        </div>
                    </div>
                </div>';
        }
    }

    ?>

    <div id="wrapper">
    <div id="menu">
        <div id="menu-buttons">
            <a class="item" href="/forum/index.php">Inicio</a>
            <a class="item" href="/forum/create_topic.php">Fazer pergunta</a>
        </div>
         
        <div id="userbar">
            <?php
                if($_SESSION['signed_in'])
                {
                    echo 'Olá, ' . $_SESSION['user_name'] . '. <a href="signout.php">Encerrar Sessão</a>';
                }
                else
                {
                    echo '<a href="signin.php">Faça LOGIN</a>';
                }
            ?>
        </div>

    </div>

        <div id="content">