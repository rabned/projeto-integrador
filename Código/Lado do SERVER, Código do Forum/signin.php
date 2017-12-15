<?php
    //signin.php
    include 'header.php';

    require '../projetointegrador/vendor/autoload.php';
    use Ivmelo\SUAP\SUAP;

    $suap = new SUAP();

    //first, check if the user is already signed in. If that is the case, there is no need to display this page
    if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
    {
        echo '<script>window.location.replace("index.php");</script>';
    }
    else
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            /*the form hasn't been posted yet, display it
              note that the action="" will cause the form to post to the same page it is on */
            echo '<h2>Login</h2>';
            echo '<form id="create_topic_form" method="post" action="">
                    <label>Matricula:</label>
                    <input type="text" name="username" />
                    <label>Senha:</label>
                    <input type="password" name="password">
                    <input type="submit" value="Logar" />
                </form>';
        }
        else
        {
            /* so, the form has been posted, we'll process the data in three steps:
                1.  Check the data
                2.  Let the user refill the wrong fields (if necessary)
                3.  Varify if the data is correct and return the correct response
            */
            $errors = array(); /* declare the array for later use */
             
            if(!isset($_POST['username']))
            {
                $errors[] = 'The username field must not be empty.';
            }
             
            if(!isset($_POST['password']))
            {
                $errors[] = 'The password field must not be empty.';
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
                //the form has been posted without errors, so save it
                //notice the use of mysql_real_escape_string, keep everything safe!
                //also notice the sha1 function which hashes the password

                $usermat = $_POST['username'];
                $userpass = $_POST['password'];
                $userpasssha = sha1($_POST['password']);

                $sql = "SELECT user_id, user_name, user_display_name, user_level, user_type, user_mat FROM `users` WHERE user_mat = '$usermat' AND user_pass = '$userpasssha'";
                             
                $result = mysqli_query($connection, $sql);
                if(!$result)
                {
                    //something went wrong, display the error
                    echo 'Algo deu errado. Tente novamente.';
                    //echo mysql_error(); //debugging purposes, uncomment when needed
                }
                else
                {
                    //the query was successfully executed, there are 2 possibilities
                    //1. the query returned data, the user can be signed in
                    //2. the query returned an empty result set, the credentials were wrong
                    if(mysqli_num_rows($result) == 0)
                    {                        
                        try{
                            $token = $suap->autenticar($usermat, $userpass);
                            $suap->setToken($token['token']);
                    
                            $dados = $suap->getMeusDados();
                            $jd = json_encode($dados);
                            $je = json_decode($jd);

                            $pl = $suap->getMeusPeriodosLetivos();
                            $pld = json_encode($pl);
                            $ple = json_decode($pld);

                            $ano_letivo = $ple[count($ple)-1]->ano_letivo;

                            $turmas = $suap->getTurmasVirtuais($ano_letivo, 1);
                            $turmasd = json_encode($turmas);
                            $turmase = json_decode($turmasd);

                            $nome = $je->vinculo->nome;
                            $vinculo = $je->tipo_vinculo;
                            $photo = $je->url_foto_75x100;
                            $photo = "https://suap.ifrn.edu.br".$photo;
                            $nome_usual = $je->nome_usual;
                            $curso = $je->vinculo->curso;

                            //cria categorias
                            foreach ($turmase as $key=>$value) {
                                $cn = $value->descricao;
                                $cat_name = explode("(", $cn)[0];
                                $cat_description = $value->sigla;

                                $cat_select_sql = "SELECT cat_id FROM `categories` WHERE cat_name = '$cat_name'";
                                $cat_select_result = mysqli_query($connection, $cat_select_sql);
                                if(!$cat_select_result){
                                    echo mysqli_error($cat_select_result);
                                }else{
                                    if(mysqli_num_rows($cat_select_result) > 0){
                                        
                                    }else{
                                        $cat_insert_sql = "INSERT INTO categories(cat_name, cat_description) VALUES('$cat_name', '$cat_description')";
                                        $cat_insert_result = mysqli_query($connection, $cat_insert_sql);                                  
                                    }
                                }
                            }

                            $signup = "INSERT INTO users(user_name, user_level, user_pass ,user_mat, user_type, user_date, user_photo, user_display_name, user_course) VALUES('$nome', 0, '$userpasssha', '$usermat', '$vinculo', NOW(), '$photo', '$nome_usual', '$curso')";

                            $ressignup = mysqli_query($connection, $signup);

                            if(!$ressignup){
                                echo mysqli_error($ressignup);
                            }else{
                                //set the $_SESSION['signed_in'] variable to TRUE
                                $_SESSION['signed_in'] = true;
                                
                                $result = mysqli_query($connection, $sql);
                                if(!$result)
                                {
                                    //something went wrong, display the error
                                    echo 'Something went wrong while signing in. Please try again later.';
                                    //echo mysql_error(); //debugging purposes, uncomment when needed
                                }
                                else{
                                    //we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
                                    while($row = mysqli_fetch_assoc($result))
                                    {
                                        $_SESSION['user_id']   = $row['user_id'];
                                        $_SESSION['user_name'] = $row['user_display_name'];
                                        $_SESSION['user_level']= $row['user_level'];
                                        $_SESSION['user_type'] = $row['user_type'];
                                        $_SESSION['user_mat']  = $row['user_mat'];
                                    }
                                     
                                    echo '<script>window.location.replace("index.php");</script>';
                                }
                            }
                        }catch(Exception $e){
                            echo 'Você informou a senha ou usuário errados. Tente de novo.';
                        }
                    }
                    else
                    {
                        //set the $_SESSION['signed_in'] variable to TRUE
                        $_SESSION['signed_in'] = true;
                         
                        //we also put the user_id and user_name values in the $_SESSION, so we can use it at various pages
                        while($row = mysqli_fetch_assoc($result))
                        {
                            $_SESSION['user_id']    = $row['user_id'];
                            $_SESSION['user_name']  = $row['user_display_name'];
                            $_SESSION['user_level'] = $row['user_level'];
                            $_SESSION['user_type'] = $row['user_type'];
                            $_SESSION['user_mat'] = $row['user_mat'];
                        }
                         
                        //echo 'Welcome, ' . $_SESSION['user_name'] . '. <a href="index.php">Proceed to the forum overview</a>.';
                        echo '<script>window.location.replace("index.php");</script>';
                    }
                }
            }
        }
    }
     
    include 'footer.php';
?>