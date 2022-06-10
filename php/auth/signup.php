<?php
    // registration logic for the web app 

    session_start();

    require_once './../backendLogic/dbConnections.php';
    require_once './../backendLogic/queryManager.php';
        
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

        // Inserisco l'utente sul db
        $text = "  INSERT INTO utente(username,hashvalue,email,telefono) 
                        VALUES('{$_POST['username']}','$hash','{$_POST['email']}','{$_POST['telefono']}'); 
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
    }

    function signupChecker() {

        global $dbConn;
        // this function queries the db and checks if the username passed is already existing     
        
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
        if ($_POST['username'] == "" || !preg_match("/^[A-Za-z ,.'-]/",$_POST['username'])) return false;
        echo 'Usenarname corretto';   
        
        if ($_POST['email'] == "" || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) return false;
        echo 'E-Mail corretta';
        
        if ($_POST['codfiscale'] == "" || !preg_match("/^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$/",$_POST['codfiscale']))
            echo 'codice fiscale corretto'

        if (strlen($_POST['telefono']) != 10 || !preg_match("/^[0-9]/",$_POST['telefono'])) return false; 
        echo 'Numero di telefono corretto';
        
        if (strlen($_POST['password']) < 8 || $_POST['password'] != $_POST['checkPassword']) return false;
        echo 'Password corretta';
        
        return true;
    }
?>