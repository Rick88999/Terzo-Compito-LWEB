<?php
$db_name='HillDownGameStore_db';
$users_table='user_table';
$string="";


$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

if(isset($_POST['send'])){
  if ($_POST['send']=='Login') {
    if(!(empty($_POST['email'])) && !(empty($_POST['password']))){
      if(!(preg_match("/^.*@.*/", $_POST['email']))){                       //verifica che nel POST il campo send sia settato (E QUINDI SIA STATA ATTIVATA LA FORM, ALTRIMENTI LO SCRIPT NON VERRA ESEGUITO SI DALL'INIZIO),
                                                                           //che il campo email e password siano compilati e che sia presente la @ nel campo email.
        $string.="<p>Inserire l'email non un nickname</p>";
      }
      else{
        $query="SELECT * FROM `{$users_table}` WHERE email=\"{$_POST['email']}\" AND password=\"{$_POST['password']}\";";    //Query per il match dell'utente con le credenziali fornite
        $return=mysqli_query($sqlConnect, $query);
        $row=mysqli_fetch_array($return);

        if($row){                            //In caso ahffermativo verra inizializzata la sessione
          session_name('HillDownService');
          session_start();
          $_SESSION['id']=$row['id'];
          $_SESSION['email']=$row['email'];
          $_SESSION['nickname']=$row['nickname'];
          $_SESSION['ttk']=100;     //TTK (Time To Kill) è la variabile che mi serve per dare un nuomero di reload limitati ad utente, dopo l'accesso l'utente potra navigare tra le varie pagine solo un centinaio di volte, per poi essere riportato qui

            header('Location: StoreHomePage.php');
          }
        else{
          $string.="<p>Login fallito: email o password errata</p>";

        }
      }
    }
    else{
      $string.="<p>Dati mancanti i uno o entrambi i campi email e/o password</p>";
    }
  }
  elseif ($_POST['send']=='Sign In') {
    /*Ho costruito un apposita pagina per il Sign In, molto semplificata si inetende (niente verifica email o AUTH a 2 fattori)*/
    header('Location: SignIn.php');
  }

  $sqlConnect->close();
}



 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="login__.css" media="screen">
    <script src="resizeInit.js"></script>
  </head>
  <body>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <div class="flexContainer">
      <div class="flexLogin">
        <div class="">
          <img src="logo3.png" alt="">
        </div>
        <div class="login_item">
          <?php if($string!="") echo $string; ?>
          <label for="email">Email:</label>
          <input type="text" name="email" value="">

          <label for="password">Password:</label>
          <input type="password" name="password" value="">
        </div>
        <div class="">
          <input type="submit" name="send" value="Login">
          <input type="submit" name="send" value="Sign In">

        </div>


      </div>

    </div>
  </form>

  </body>
</html>
