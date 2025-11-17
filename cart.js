document.addEventListener('DOMContentLoaded', () => {
    // --- Selectori Cheie ---
    const cartTableBody = document.querySelector('.cart-items table tbody');
    const donationInput = document.getElementById('donation-amount');
    
    // Selectăm elementele de afișare. Vom adăuga o verificare de existență mai jos.
    const subtotalDisplay = document.querySelector('.summary-line:nth-child(1) span:last-child');
    const taxDisplay = document.querySelector('.summary-line:nth-child(3) span:last-child');
    const totalDisplay = document.querySelector('.summary-line.total strong:last-child');

    // --- Constante ---
    let TAX_PROCESSING = 0; 
    
    // Extragerea taxei într-un mod sigur pentru a evita eroarea 'null' (TypeError: Cannot read properties of null)
    if (taxDisplay) {
        // Dacă elementul taxDisplay este găsit, extrage valoarea
        const taxProcessingText = taxDisplay.textContent; 
        TAX_PROCESSING = parseFloat(taxProcessingText.replace(' EUR', '').trim());
    } else {
        // Dacă elementul nu este găsit (de exemplu, selector greșit), setăm o valoare implicită de 5 EUR, 
        // conform datelor inițiale din HTML.
        TAX_PROCESSING = 5.00; 
        console.warn("Atenție: Elementul pentru afișarea taxei de procesare nu a fost găsit. S-a folosit valoarea implicită de 5.00 EUR.");
    }

    // --- Funcții Utilitare ---

    // Funcție pentru a extrage valoarea numerică dintr-un text (ex: "150 EUR" -> 150)
    function extractPrice(text) {
        // Înlocuiește " EUR", spațiile și convertește în float.
        return parseFloat(text.replace(' EUR', '').trim());
    }

    // Funcție pentru a formata un număr ca preț (ex: 475 -> "475.00 EUR")
    function formatPrice(number) {
        if (isNaN(number)) return "0.00 EUR";
        return `${number.toFixed(2)} EUR`;
    }

    // --- Funcția Principală de Calcul ---

    function calculateCart() {
        let itemsSubtotal = 0;

        // 1. Iterarea prin fiecare rând de produs din tabel
        cartTableBody.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            
            // Verificăm dacă rândul are un număr suficient de celule
            if (cells.length < 5) return; 

            // Extrage Prețul Unitar (index 1)
            const unitPrice = extractPrice(cells[1].textContent);
            
            // Extrage Cantitatea (index 2)
            const quantityInput = cells[2].querySelector('input[type="number"]');
            // Verifică dacă input-ul există și extrage valoarea, asigurând că nu e NaN
            const quantity = (quantityInput && parseInt(quantityInput.value, 10)) || 0; 

            // Calculează Totalul pe Produs și adaugă la subtotal
            const productTotal = unitPrice * quantity;
            itemsSubtotal += productTotal;

            // Actualizează celula 'Total' a rândului curent (index 3)
            cells[3].textContent = formatPrice(productTotal);
        });

        // 2. Calculul Totalului General
        const donation = parseFloat(donationInput.value) || 0; // Extrage valoarea donației
        const grandTotal = itemsSubtotal + donation + TAX_PROCESSING;

        // 3. Afișarea Rezultatelor în Sumar (Verificăm din nou existența elementelor de afișare)
        if (subtotalDisplay) subtotalDisplay.textContent = formatPrice(itemsSubtotal);
        if (totalDisplay) totalDisplay.textContent = formatPrice(grandTotal);
    }
    
    // --- Funcția de Ștergere a Rândului ---
    
    function removeProduct(button) {
        const row = button.closest('tr'); // Găsește cel mai apropiat element <tr>
        if (row) {
            row.remove(); // Șterge rândul din DOM
            calculateCart(); // Recalculează totalul după ștergere
        }
    }

    // --- Atașarea Evenimentelor (Metoda robustă cu Event Delegation) ---

    // 1. Evenimente pentru Cantitate (input) și Ștergere (click)
    // Evenimentul 'input' asigură actualizarea în timp real a cantității.
    cartTableBody.addEventListener('input', (e) => {
        if (e.target.matches('input[type="number"]')) {
            calculateCart();
        }
    });
    
    // Eveniment pentru Butonul de ștergere
    cartTableBody.addEventListener('click', (e) => {
        if (e.target.matches('.remove-btn')) {
            removeProduct(e.target);
        }
    });

    // 2. Eveniment pentru Donație
    if (donationInput) {
        // Folosim 'input' pentru actualizare în timp real când utilizatorul tastează donația
        donationInput.addEventListener('input', calculateCart);
    }

    // Inițializează coșul de cumpărături la încărcarea paginii
    calculateCart();
});