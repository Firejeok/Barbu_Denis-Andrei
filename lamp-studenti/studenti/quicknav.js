window.onload = function() {
    const toggleButton = document.getElementById('quickNavToggle');
    const menu = document.getElementById('quickNavMenu');
    
    // Asigură că funcția se rulează doar dacă elementele există
    if (!toggleButton || !menu) return; 

    // Adaugă un ascultător de evenimente pe buton (CORECTAT: 'toggleButton' în loc de 'toggleton')
    toggleButton.addEventListener('click', () => {
        // Comută clasa 'open' pe buton pentru a schimba starea vizuală (animarea X-ului)
        toggleButton.classList.toggle('open');
        
        // Comută clasa 'active' pe meniul derulant pentru a-l afișa/ascunde
        menu.classList.toggle('active');
        
        // Opțional: Poți adăuga logica pentru a închide meniul la click în afara lui
    });
    
    // Opțional: Închide meniul dacă se dă click pe un link din interior
    const links = menu.querySelectorAll('.quick-nav-link');
    links.forEach(link => {
        link.addEventListener('click', () => {
            toggleButton.classList.remove('open');
            menu.classList.remove('active');
        });
    });
};
