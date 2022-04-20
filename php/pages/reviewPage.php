<?php session_start();

if (!$_SESSION['username']) {
    // non posso accedere a questa pagina se non sono loggato
    echo 'ACCESSO VIETATO AD UTENTI NON LOGGATI';
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo $_GET['medName']; ?></title>
    <link rel='stylesheet' href='./../../css/global.css'>
    <script src='./../../js/showMedDetail.js'></script>
</head>

<body>
    <div id='container-page'>
        <?php require './../templates/navbar.php' ?>
        <div id='container-main'>
            <?php require './../templates/medDetails.php'?>
            <div class='container-form'>
                <form action="putReview.php" method = 'post' id = 'login'>
                    <input class='box-form' type="text" name='review' placeholder="review">
                    <input class='box-form' type="submit" value="submit">
                </form>
            </div>
            <?php require './../templates/rightBar.php' ?>
        </div>
    </div>
</body>

</html>