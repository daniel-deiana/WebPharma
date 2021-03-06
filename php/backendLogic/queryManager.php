<?php

    /*
        ============================= QUERY ====================================

        File che contiene tutte le funzioni che effettuano delle query al database del sito
    
    */ 


    require_once './../backendLogic/dbConnections.php';

    function idFromMed($med)
    {
        global $dbConn;

        // prendo l'id del medicinale
        $queryText = "  SELECT F.id
                                FROM farmaco F
                                WHERE F.nome = \"{$med}\"; 
                            ";

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        return SQLconvertObject($queryResult)[0]['id'];
    }

    // funzione di conversione da oggetto SQL ad array
    function SQLconvertObject($SQLresult) {
        return $SQLresult->fetch_all(MYSQLI_ASSOC);
    }

    // prende un sottoinnsieme dei medicinali in catalogo
    function getMeds($firstID) {
        global $dbConn;

        // debug

        $initial = $firstID * 4;
        $last = $initial + 4 ;

        $queryText = "  SELECT F.nome
                        FROM farmaco F
                        WHERE F.id > {$initial} AND F.id <= {$last};
                    ";
        
        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        return SQLconvertObject($queryResult);
    }

    function getMedImage($name){
        global $dbConn;

        //   prima prendo il nome dell'immagine e poi lo vado a prendere nel file system del server

        $queryText = "  SELECT F.image_path
                        FROM farmaco F
                        WHERE F.nome = \"{$name}\"
                        LIMIT 1;
                    ";
        /* 
            ATTENZIONE: I file si trovano nella directory img dentro la directory "WebPharma", nel 
            db vengono salvati usanndo un path relativo rispetto alla cartella del progetto
        */

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        $arr_result = SQLconvertObject($queryResult);

        // prendo l'immagine e la codifico in un formato standard
        $rawImage = file_get_contents($arr_result[0]['image_path']);
        return base64_encode($rawImage);
    
    }

    /*
        Funzione che ritorna tutte le informazioni relative ad un certo medicinale presente nel db
    */

    function getMedDetail($medName) {
        global $dbConn;

        $queryText = "  SELECT F.* 
                        FROM farmaco F
                        WHERE F.nome = \"{$medName}\"
                        LIMIT 1;
                    ";

        $queryResult = $dbConn->executeQuery($queryText);   
        $dbConn->close();

        return SQLconvertObject($queryResult);
    }

    /* Inserisce una prenotazione per un utente rispetto ad un certo medicinale */

    function putMed($med,$quantity,$user)
    {
        global $dbConn;
        
        $id = idFromMed($med);
        
        $data = date('F j, Y, g:i a');
    
        // inserisco la prenotazione 
        $queryText = "  INSERT INTO prenotazione(farmaco,utente,data,stato,quantita)
                        VALUES($id,\"{$user}\",\"{$data}\",'non ritirato',$quantity);
                    ";
        
        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();  
        
        return $queryResult;
    }

    // storia delle prenotazioni per un utente
    function getBookHistory($user) {
        global $dbConn;

        $queryText = "  SELECT P.codice, F.nome, P.stato, P.data ,P.quantita
                        FROM prenotazione P
                        INNER JOIN farmaco F ON F.id = P.farmaco
                        WHERE P.utente = \"{$user}\"
                        ORDER BY P.data;
                    "; 

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();
    
        return SQLconvertObject($queryResult);
    }

    // inserisce review del farmaco
    function putReview($review,$user,$med) {

        global $dbConn; 

        $review = $dbConn->filter($review);

        $id = idFromMed($med);
        $data = date('F j, Y, g:i a');

        $queryText = "  INSERT INTO review(utente,farmaco,data,testo)
                        VALUES(\"{$user}\",\"$id\",\"{$data}\",\"{$review}\");
                    ";
        
        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        return $queryResult;
    }


    // prende le review per un certo medicinale, position si utilizza per stampare solamente una parte del result set
    function getReviews($med,$position) {
        global $dbConn;

        $init = $position * 2;
        $limit = $position + 2;

        $queryText = "  SELECT T.testo,T.data,T.utente
                        FROM (
                            SELECT R.testo, R.data, R.utente, 
                            ROW_NUMBER() OVER(ORDER BY R.utente) AS R
                            FROM review R
                            INNER JOIN farmaco F ON F.id = R.farmaco
                            WHERE F.nome = \"$med\"
                        ) AS T
                        WHERE T.R > $init AND T.R <= $limit ;
                    ";

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();
        
        return SQLconvertObject($queryResult);
    }

    // funzione che controlla che un determinato utente non abbia gia lasciato un parere su un medicinale
    function checkReview($med,$user) {

        global $dbConn; 

        $queryText = "SELECT R.farmaco FROM review R 
                      INNER JOIN farmaco F ON F.id = R.farmaco    
                      WHERE F.nome = \"{$med}\"
                      AND R.utente = \"{$user}\";
                      ";

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        return SQLconvertObject($queryResult);
    }

    // controlla che un utente abbia almeno una preenootazione per un farmaco in  stato ritirato
    function checkBookHistory($med,$user) {
        
        global $dbConn;

        $queryText = "      SELECT P.utente FROM prenotazione P
                            INNER JOIN farmaco F ON F.id = P.farmaco
                            WHERE P.utente = \"{$user}\"
                            AND F.nome = \"{$med}\"
                            AND P.stato = 'ritirato';
                        ";

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();

        return SQLconvertObject($queryResult);
    }

    // inserisce nuovo medicinale 
    function insertNewMed($name,$desc,$price,$img_name,$prescrizione) {
        global $dbConn;

        $queryText = "SELECT MAX(F.id) as ad FROM farmaco F";

        $queryResult = $dbConn->executeQuery($queryText);
        $max_id = SQLconvertObject($queryResult)[0]['ad'];


        $path = './../../img/'.$img_name;

        $queryText = "INSERT INTO farmaco(id,nome,image_path,descrizione,prezzo,prescrizione)
        VALUES($max_id+1,\"$name\",\"$path\",\"$desc\",$price,$prescrizione);";
        $queryResult = $dbConn->executeQuery($queryText);

        $dbConn->close();     
    } 

    // prende una determinata prenotazione con codice [id]
    function getBookRecords($id) {
        global $dbConn;
    
        $queryText = "  SELECT U.username, F.nome,P.quantita,P.data,P.stato
                        FROM prenotazione P INNER JOIN farmaco F
                        ON P.farmaco = F.id INNER JOIN utente U
                        ON U.username = P.utente
                        WHERE P.codice = $id;
        ";

        $queryResult = $dbConn->executeQuery($queryText);
        $dbConn->close();
        return SQLconvertObject($queryResult);
    }

    // aggiornna lo stato della prenotazione di un utente
    function updateBookState($id,$state) {
        
        global $dbConn; 

        $state_string = ($state == -1) ? 'annullato' : 'ritirato';

        $queryText = "  UPDATE prenotazione
                        SET stato = \"$state_string\"
                        WHERE codice = $id;
                    ";

        $dbConn->executeQuery($queryText);
        $dbConn->close();
    }
?>
