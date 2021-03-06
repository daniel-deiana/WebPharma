<?php
    session_start();

    require_once './../backendLogic/dbConnections.php';
    require_once './../backendLogic/queryManager.php';

    global $dbConn;

    // pulisco input
    $_POST['username'] = $dbConn->filter($_POST['username']);
    $_POST['email'] = $dbConn->filter($_POST['email']);
    $_POST['telefono'] = $dbConn->filter($_POST['telefono']);
    $_POST['codfiscale'] = $dbConn->filter($_POST['codfiscale']);
    $_POST['password'] = $dbConn->filter($_POST['password']);
    $_POST['check'] = $dbConn->filter($_POST['check']);
        
    // controlla se l'utente sia gia registrato
    if (!signupChecker()) {
        // stampo la pagina di user gia esistente ed esco
        $_SESSION['err_msg'] = 'err_signup_1';
        header('location: ./../pages/homePage.php');
        exit;
    }

    // Controllo validità valori passati nel form
    if (signupFormChecker()) {

        
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $tipo = 'cliente';

        // Inserisco l'utente sul db
        $text = "  INSERT INTO utente(username,hashvalue,email,telefono,tipo,codice_fiscale) 
                        VALUES('{$_POST['username']}','$hash','{$_POST['email']}','{$_POST['telefono']}','{$tipo}','{$_POST['codfiscale']}'); 
                    ";
        
        $result = $dbConn->executeQuery($text);
        $dbConn->close();
        
        if ($result == true)
        {
                header('location: ./../pages/loginPage.php');
                exit;
            }   
        else
            {
            $_SESSION['err_msg'] = 'err_signup_2';
            header('location: ./../pages/homePage.php');
            exit;   
            }
    }
    else {

            // valori del form non corretti

            // $_SESSION['err_msg'] = 'err_signup_3';
            // header('location: ./../pages/homePage.php');
            exit;   

    }

    function signupChecker() {

        global $dbConn; 
        
        $queryText =    "   SELECT username 
                            FROM utente
                            WHERE username = \"{$_POST['username']}\";
                        ";

        $queryResult = $dbConn->executeQuery($queryText);
        $queryResult = mysqli_num_rows($queryResult);
        $dbConn->close();

        // controllo se vi sono risultati che fanno match con l'user passato in ingresso
        
        if ($queryResult != 0)
            return false;
        else
            return true;
    }

    // Esegue un checking dei valori passati in ingresso dall'utente

    function signupFormChecker() {  



        if ($_POST['username'] == "" || !preg_match("/^[A-Za-z ,.'-]/",$_POST['username'])) 
            return false;

        echo 'user ok';
    
        if ($_POST['codfiscale'] == "" || !preg_match("/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/",$_POST['codfiscale']))
            return false;

    echo 'cod ok';
    
        if (strlen($_POST['telefono']) != 10 || !preg_match("/^[0-9]/",$_POST['telefono']))
            return false;


    echo 'tel ok';

        if ( (strlen($_POST['password']) < 8) || $_POST['password'] != $_POST['check']) return false;

    echo 'pass ok';

        return true;
    }
?>