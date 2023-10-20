function resizeContent() {
  var windowHeight = window.innerHeight;
  var windowWidth = window.innerWidth;

  // Modifica le dimensioni dei tuoi elementi CSS in base alle dimensioni della finestra
  // Esempio: Modifica l'altezza del body al 100% della finestra
  var body = document.querySelector('body');
  body.style.height = windowHeight + 'px';

  // Aggiungi altre operazioni di ridimensionamento se necessario

}

// Aggiungi un listener per l'evento di ridimensionamento della finestra del browser
window.addEventListener('resize', resizeContent);

// Chiama la funzione di ridimensionamento iniziale
resizeContent();
