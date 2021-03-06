<?php


    // ==================== GESTIONE ERRORI ==========================
    
    /*
        - file dove vengono salvate tutte le associazioni fra codice di errore e messaggio di errore associato
        
        
        - Vengono inoltre definite in questo file delle funzioni di controlli privilegi usate in tutto il sito per garantire
        che gli utenti ed i farmacisti possano svolgere solamente le operazioni definite in fase di progettazione
    */


    // ERRORI SU PERMESSI VARI
    $error_list['err_permessi'] = "Non hai i permessi necessari per accedere a questo contenuto";        
    $error_list['err_carrello_agg'] = "Errore con l'aggiunta del medicinale al carrello";
    $error_list['err_not_log'] = "Un utente non loggato non puo accedere a questa sezione del sito";
    
    // ERRORI PRENOTAZIONE
    $error_list['err_book'] = 'errore nella procedura di prenotazione';     

    // ERRORI REVIEW FARMACI
    $error_list['err_review_1'] = 'Stai provando a lasciare un opinione su un farmaco che non hai ritirato in farmacia';
    $error_list['err_review_2'] = 'Esiste gia un tuo parere su questo farmaco';

    
    $error_list['err_signup_1'] = 'Hai inserito un username relativo ad un utente gia esistente';
    $error_list['err_signup_2'] = 'Le password non coincidono oppure hai inserito una password troppo piccola (min 8 carattaeri)';
    $error_list['err_signup_3'] = 'Non hai compilato il form rispettando le regole';
    

    $error_list['err_login_1'] = 'Username non esistente/Hai inserito una password sbagliata';

    // ERRORI GESTIONE PRENOTAZIONI
    $error_list['err_handle_book'] = 'Stai provando a modificare una prenotazione non esistente';

    $error_list['err_img_type'] = 'il file passato non era un immagine, i formati ammessi sono png e jpeg';
    $error_list['err_img_load'] = 'errore caricamento immagine';
    $error_list['err_img_size'] = 'immagine troppo grande';

    $error_list['book_update_ok'] = 'Prenotazione aggiornata con successo';



    // controlla se utente è loggato, se no redirect alla homepage con messaggio di errore
    function check_login()
    {
        if(!isset($_SESSION['username']))
        {
            // se un utente prova ad accedervi quando non è loggato ho un errore
            $_SESSION['err_msg'] = 'err_not_log';
            header('location: ./../pages/homePage.php');
            exit;
        }
    }

    // controlla se si hanno i privilegi necessari per accedere ad un contenuto/operazione 
    function check_privilege($level)
    {
        if($_SESSION['usrtype'] != $level)
        {
            $_SESSION['err_msg'] = 'err_permessi';
            header('location: ./../pages/homePage.php');
            exit;
        }
    }

?>
