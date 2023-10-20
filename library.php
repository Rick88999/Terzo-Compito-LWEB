<?php
/*La pagina libreria presenta i giochi acquistati e a lato i propri DLC acquistati*/
session_name('HillDownService');
session_start();
require("utility.php");

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {

  $xmlstream=streamChanger("cache/gamesCache.xml");
  $XMLcache = new DOMDocument();
  $XMLcache->loadXML($xmlstream);
  $rootCache=$XMLcache->documentElement;

  $xmlstream=streamChanger("cache/UserGameTable.xml");
  $XMLuserTable=new DOMDocument();
  $XMLuserTable->loadXML($xmlstream);
  $rootUserTable=$XMLuserTable->documentElement;
  $usersList=$rootUserTable->childNodes;


    if (isset($_GET['send'])) {
      if ($_GET['send']=='logout') {
        unset($_SESSION);
        session_destroy();
        header('Location: login.php');
      }
    }
    if (isset($_GET['game'])) { //Codice simile da StoreHomePage, infatti segue lo stesso principio e ci riporta lla gameList selezionando il codice del gioco che vogliamo visualizzare tramite bottone SEE
      $_SESSION['id_game']=$_GET['game'];
      header('Location: gameList.php');


    }

    for ($i=0; $i < $usersList->length; $i++) {
      $userList=$usersList->item($i);
      if ($userList->getAttribute('id_user')==$_SESSION['id']) $refList=$userList;
    }

    $_SESSION['ttk']--;
  }
  else {
    unset($_SESSION);
    session_destroy();
    header('Location: login.php');
  }



 ?>


  <?xml version="1.0" encoding="UTF-8"?>
  <!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
     <title>DownHill Game Store</title>
     <link rel="stylesheet" href="StoreHomePage_.css" media="screen">
     <link rel="stylesheet" href="Init_Struct__.css" media="screen">
   </head>
   <body>
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
                 <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                     <input type="submit" name="send" value="logout">
                 </form>
               </td>
             </tr>
           </table>
         </div>
       </div>
       <div class="table">
         <form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">


        <?php
        //Si aggiunge una riga lla tabella per ogni gioco posseduto, se non si possiedono giochi la tabella non viene mostrata
        $flag=0;
        $struct="<table>";
        $games=$rootCache->childNodes;
        if(isset($refList)){
          foreach($refList->childNodes as $field) {
            $struct.="<tr>";
            for ($i=0; $i < $games->length; $i++) {
              $game=$games->item($i);
              if ($game->getAttribute('id')==$field->textContent && !($game->hasAttribute('idRef'))) {
                  $struct.="<td id=\"image\"><img src=\"{$game->getAttribute('photo')}\" alt=\"{$game->getAttribute('id')}\"></td>";
                foreach ($game->childNodes as $infoGame) {
                  switch ($infoGame->nodeName) {
                    case 'titolo':
                      $struct.="<td>{$infoGame->textContent}</td>";
                      break;

                    case 'prezzo':
                      $struct.="<td>{$infoGame->textContent}€</td>";
                      break;

                    case 'descrizione':
                      $struct.="<td>{$infoGame->textContent}€</td>";
                      break;
                    default:
                      break;
                  }
                }
                $struct.="<td>";
                $struct.="<button type=\"submit\" name=\"game\" value=\"{$game->getAttribute('id')}\">See</button>";
                $struct.="</td>";


                $dlcs=$games;
                $dlcsRefList=$refList;
                $struct.="<td>";

                foreach ($dlcsRefList->childNodes as $v) {

                  for ($i=0; $i < $dlcs->length; $i++) {

                    $dlc=$dlcs->item($i);
                    if($dlc->getAttribute('id')==$v->textContent && $dlc->hasAttribute('idRef')){
                      if ($dlc->getAttribute('idRef')==$game->getAttribute('id')) {
                        foreach ($dlc->childNodes as $type) {
                          if($type->nodeName=='titolo') $struct.="{$type->textContent} <br>";
                        }
                      }
                    }
                  }
                }
                $struct.="</td>";
              $flag++;
            }
          }
          $struct.="</tr>";
        }
        }


        if ($flag>0) {
          echo $struct;
          echo "</table>";
        }
        else {
          echo "<p>Lista giochi vuota</p>";
        }
         ?>
       </form>
       </div>


     </div>


   </body>
 </html>
