
const detaliiDiv = document.getElementById('detalii');
const btnDetalii = document.getElementById('btnDetalii');
const dataProdusSpan = document.getElementById('dataProdus');


const luni = [
    "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
    "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
];


document.addEventListener('DOMContentLoaded', () => {

    detaliiDiv.classList.add('ascuns');


    const dataCurenta = new Date();
    
    const zi = dataCurenta.getDate();
    const lunaText = luni[dataCurenta.getMonth()];
    const an = dataCurenta.getFullYear();


    const dataFormatata = `${zi} ${lunaText} ${an}`;


    dataProdusSpan.textContent = dataFormatata;
});


function comutaDetalii() {
    detaliiDiv.classList.toggle('ascuns');


    if (detaliiDiv.classList.contains('ascuns')) {
        btnDetalii.textContent = 'Afiseaza detalii';
    } else {
        btnDetalii.textContent = 'Ascunde detalii';
    }
}

btnDetalii.addEventListener('click', comutaDetalii);