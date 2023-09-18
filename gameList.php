<?php
require("utility.php");



/*La pagina di game list serve per visualizzare il gioco in maggior dettaglio. Inoltre è la pagina che permette, se non abbiamo già il gioco o DLC, di acquistarlo.
Possiamo arrivarci dalla HOME con il pulsante BUY o dalla Libreria con il pulsante SEE*/
session_name('HillDownService');
session_start();



/*Ogni gioco é contenuto nel file gamesCache.xml e possiede:
ATTRUBUTI=>un id, un img(se non é un dlc), un bool ch dice se é o meno un dlc e nel caso lo sia un attributo idRef del gioco di cui é dlc
CAMPI->titolo, prezzo, versione e nel caso sia un gioco non un dlc anche una descrizione
*/

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {
  $tab=array(); // la Tab ci serve per stampare i vari giochi e dlc
  $xmlstream=streamChanger("cache/gamesCache.xml");
  $XMLlistaGiochi= new DOMDocument();
  $XMLlistaGiochi->loadXML($xmlstream);
  $root=$XMLlistaGiochi->documentElement;
  $elem=$root->childNodes;
  for ($i=0; $i <$elem->length ; $i++) {
    $game=$elem->item($i);
    $id_prodotto=$game->getAttribute('id');
    if ($id_prodotto==$_SESSION['id_game']) { //Prendo le info del gioco e compilo la riga per la tab
      $row=array();
      $row[]=$id_prodotto;
      foreach ($game->childNodes as $field) {
        $text=$field->textContent;
        $row[]=$text;
      }
      $photo=$game->getAttribute('photo');
      $row[]=$photo;
      $tab[]=$row;
    }
  }

  for ($i=0; $i <$elem->length ; $i++) { //Prendo le info di ogni dlc che ha come riferimento il gioco in questione e poi creo un row per la tab
    $game=$elem->item($i);
    $id_ref=$game->getAttribute('idRef');
    $id_prodotto=$game->getAttribute('id');
    if ($id_ref==$_SESSION['id_game']) {
      $row=array();
      $row[]=$id_prodotto;
      foreach ($game->childNodes as $field) {
        $text=$field->textContent;
        $row[]=$text;
      }
      $tab[]=$row;
    }
  }

  $xmlstream=streamChanger("cache/carrello_attivo.xml");
  $XMLcarrelloAttivo=new DOMDocument();
  $XMLcarrelloAttivo->loadXML($xmlstream);
  $rootCarrello=$XMLcarrelloAttivo->documentElement;
  $carts=$rootCarrello->childNodes;

  $xmlstream=streamChanger("cache/UserGameTable.xml");
  $XMLuserTable=new DOMDocument();
  $XMLuserTable->loadXML($xmlstream);
  $rootUserTable=$XMLuserTable->documentElement;
  $elemUserTable=$rootUserTable->childNodes;


  if (isset($_POST['send'])) {
    $flag=0;

    if ($_POST['send']=='Al carrello-->') {
      if (isset($_POST['games_into_cart']) && !(empty($_POST['games_into_cart']))) { //Se ho selzionato giochi da acquistare e vado sul pulsante AL Carrello
        for ($i=0; $i <$elemUserTable->length ; $i++) {
          //controllo prima che l'utente non abbia già i giochi selezionati
          $user=$elemUserTable->item($i);
          if($user->getAttribute('id_user')==$_SESSION['id']){
            foreach ($user->childNodes as $game) {
              foreach ($game->childNodes as $field) {
                foreach ($_POST['games_into_cart'] as $v) {
                  if($field->textContent==$v) $flag++;
                }
              }
            }
          }
        }

        for ($i=0; $i <$carts->length ; $i++) {

          $cart=$carts->item($i);
          if ($cart->getAttribute('id_user')==$_SESSION['id']){
            //e poi controllo che non siano già presenti in un carrello attivo
            foreach ($cart->childNodes as $game) {

              foreach ($_POST['games_into_cart'] as $k) {

                if($game->getAttribute('id_game')==$k) $flag++;
            }
          }
        }
      }
    }
    if($flag==0 && !(empty($_POST['games_into_cart']))){ //In caso è tutto ok vado nel carrello per acquistare altrimenti la pagina si aggiornerà con un messaggio
      $_SESSION['ids_to_cart']=$_POST['games_into_cart'];//che invitera gli utenti a deselezionare il gioco che già hanno acquistato o posto nel carrello attivo
      header('Location: cartPage.php');
    }
  }
  elseif ($_POST['send']=='logout') {
    unset($_SESSION);
    session_destroy();
    header('Location: login.php');
  }
}


  $_SESSION['ttk']--;
}
else {
  unset($_SESSION);
  session_destroy();
  header('Location: login.php');
}

/*Divido poi nel codice HTML la pagina in due parti: una informativa con tutte le info del gioco principale; una di selezione, dove potremmo condermare in prodotti
da voler acquistare e proseguire per il carrello*/
 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="Init_Struct_.css" media="screen">
    <link rel="stylesheet" href="gameList_.css" media="screen">
  </head>
  <body id="bodyGameList">
    <div class="flexContainer">
      <div class="flexNavBar">
        <div>
          <img src="logo2.png" alt="logo">

        </div>
        <div class="navBarStruct">
          <table>
            <tr>
              <td><a href="StoreHomePage.php">Home</a></td>
              <td><a href="profile.php">Profilo</a></td>
              <td><a href="library.php">Libreria</a></td>
              <td><img src="cart.png" alt="cart" usemap="#cart">
                <map name="cart">
                  <area shape="rect" coords="0,82,89,8" href="cartPage.php" alt="cart">
                </map>
              </td>
              <td>
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                    <input type="submit" name="send" value="logout">
                </form>
              </td>
            </tr>
          </table>

        </div>
      </div>

      <div class="flex_showing_box">
        <div class="flex_showing_innerbox_imageAndData">
          <div class="show_image">
            <?php
            if(!(empty($tab))){
              echo "<img src=\"{$tab[0][5]}\" alt=\"{$tab[0][1]} mancante\">";
            }
             ?>
          </div>
          <div class="show_data">
            <?php
            if (!(empty($tab))) {
              echo "<table>";
              echo "<tr>";
              echo "<td>".$tab[0][1]."</td>";
              echo "</tr>";
              echo "<td>".$tab[0][3]."ver.</td>";
              echo "<tr>";
              echo "</tr>";
              echo "<td>".$tab[0][4]."</td>";
              echo "<tr>";
              echo "</tr>";
              echo "</table>";
            }

            ?>

          </div>
        </div>
        <div class="orderContainer">
          <div class="gamesBox">
            <form class="" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <?php
            if(!(empty($tab))){
              $ids_to_cart=array();
              echo "<h3>Seleziona giochi o DLC che vuoi acquistare:</h3>";
              foreach($tab as $row){
                $ids_to_cart[]=$row[0];
                echo "<label>";
                echo "<input type=\"checkbox\" name=\"games_into_cart[]\" value=\"{$row[0]}\"> {$row[1]} | {$row[2]}€";
                echo "</label><br>";

              }
            }

             ?>

          </div>
          <div class="sendBox">
            <div>
              <?php
              if ( isset($flag) && $flag>0) {
                echo "<p>Sei già in possesso di uno o più dei contenuti selezionati, oppure lo hai già messo nel carrello</p>";
              }
               ?>
            </div>
            <div id="button_cart">
              <button type="submit" name="send" value="Al carrello-->">Al carrello--></button>
            </div>


          </form>

          </div>

        </div>

      </div>


    </div>

  </body>
</html>
