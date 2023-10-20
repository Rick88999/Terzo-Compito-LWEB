<?php
function sqlHomeToXML($db_name, $add_on_table){

  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);           //ho messo il doppio fattore isset() e >0 per una doppia sicurezza di esecuzione dello script nel modo corretto
  if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
  }

    $query="SELECT * FROM `{$add_on_table}` WHERE titolo NOT LIKE 'DLC%';"; //Nel catalogo mostro solo i giochi non i DLC
    $return=mysqli_query($sqlConnect, $query);
    $sqlConnect->close();


    $doc=new DOMDocument();
    $doc->load('cache/gamesCache.xml');


    while ($row=mysqli_fetch_array($return)) {
      $game=$doc->createElement('gioco');

      $title=$doc->createElement('titolo');
      $text=$doc->createTextNode($row['titolo']);
      $title->appendChild($text);
      $game->appendChild($title);

      $prize=$doc->createElement('prezzo');
      $text=$doc->createTextNode($row['prezzo']);
      $prize->appendChild($text);
      $game->appendChild($prize);

      $ver=$doc->createElement('versione');
      $text=$doc->createTextNode($row['versione']);
      $ver->appendChild($text);
      $game->appendChild($ver);

      $desc=$doc->createElement('descrizione');
      $text=$doc->createTextNode($row['descrizione']);
      $desc->appendChild($text);
      $game->appendChild($desc);

      $root->appendChild($game);
      $game->setAttribute("id", $row['id_prodotto']);
      $game->setAttribute("photo", $row['img']);
    }
    $doc->appendChild($root);
    $doc->saveXML();
}
 ?>
