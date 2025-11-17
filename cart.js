document.addEventListener('DOMContentLoaded', () => {
    setupCartLogic(); 
    setupParticleAnimation(); // Presupunând că ai funcția de animație definită
});


// --- Funcția de Bază a Logicii Coșului ---

function setupCartLogic() {
    // Verifică existența containerului principal (CartContainer)
    const cartContainer = document.querySelector('.cart-container');
    if (!cartContainer) return;

    // --- Definirea Elementelor DOM (Verifică ID-urile/Clasele din HTML!) ---
    const donationInput = document.getElementById('donation-amount');
    const subtotalDisplay = document.querySelector('.summary-line:nth-child(1) span:last-child');
    const totalDisplay = document.querySelector('.summary-line.total strong:last-child');
    const cartTable = document.querySelector('.cart-items table');

    // Verificare strictă a elementelor critice
    if (!donationInput || !subtotalDisplay || !totalDisplay || !cartTable) {
        console.error("Eroare Critică JS: Nu s-au putut găsi toate elementele de Sumar (ID/Class mismatch). Logica de calcul nu va rula.");
        return; 
    }

    // Constanta de Taxă (trebuie să se potrivească cu ce este afișat în HTML: 5 EUR)
    const PROCESSING_FEE = 5.00; 

    /**
     * Parsează un număr dintr-un input sau text, returnând 0 dacă eșuează.
     */
    function safeParseFloat(value) {
        const cleanValue = String(value).replace(',', '.');
        const num = parseFloat(cleanValue);
        return isNaN(num) ? 0 : num;
    }

    /**
     * Calculează și actualizează totalurile coșului.
     */
    function updateCartTotals() {
        let currentSubtotal = 0;
        let currentDonation = safeParseFloat(donationInput.value); 
        
        const allRows = document.querySelectorAll('.cart-items tbody tr');
        
        allRows.forEach(row => {
            
            // 1. CITIRE PREȚ UNITAR (din atributul data-price-unit al rândului <tr>)
            const unitPrice = safeParseFloat(row.dataset.priceUnit);
            
            // 2. CITIRE CANTITATE (din input-ul cu clasa item-quantity)
            const quantityInput = row.querySelector('.item-quantity');
            const quantity = quantityInput ? safeParseFloat(quantityInput.value) : 0;
            
            // 3. CALCUL
            const rowTotal = unitPrice * quantity;
            currentSubtotal += rowTotal;
            
            // 4. ACTUALIZARE TOTAL RÂND (coloana 4)
            const rowTotalCell = row.querySelector('td:nth-child(4)');
            if (rowTotalCell) {
                // Afișează Totalul formatat
                rowTotalCell.textContent = `${rowTotal.toFixed(2)} EUR`; 
            }
        });
        
        // Calculează Totalul Final
        const finalTotal = currentSubtotal + PROCESSING_FEE + currentDonation;

        // 5. Actualizează Sumarul
        subtotalDisplay.textContent = `${currentSubtotal.toFixed(2)} EUR`;
        totalDisplay.textContent = `${finalTotal.toFixed(2)} EUR`;
    }

    
    /**
     * Funcție pentru ștergerea unui articol
     */
    function removeItem(event) {
        if (event.target.classList.contains('remove-btn')) {
            event.preventDefault(); // Oprirea acțiunii default a butonului, dacă există
            const rowToRemove = event.target.closest('tr');
            
            if (rowToRemove) {
                rowToRemove.remove(); // Șterge rândul
                updateCartTotals();  // Recalculează totalurile
            }
        }
    }
    
    
    /**
     * Inițializarea Ascultătorilor de Evenimente
     */
    function setupListeners() {
        // Ascultă click-urile pe tabel pentru ștergere
        cartTable.addEventListener('click', removeItem);

        // Ascultă schimbările de valoare (Cantitate și Donație)
        cartTable.addEventListener('input', updateCartTotals);
        donationInput.addEventListener('input', updateCartTotals);
    }

    // --- Pornirea Logicii ---
    setupListeners();
    updateCartTotals(); // Execută o dată la încărcarea paginii pentru a inițializa afișajul
}