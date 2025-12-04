document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos los elementos del DOM
    const btnMas = document.querySelector('.btn-mas');
    const btnMenos = document.querySelector('.btn-menos');
    const qtyNumber = document.querySelector('.qty-number');

    // Verificamos que los elementos existan para evitar errores
    if(btnMas && btnMenos && qtyNumber) {
        
        // Función para el botón +
        btnMas.addEventListener('click', () => {
            let val = parseInt(qtyNumber.innerText);
            qtyNumber.innerText = val + 1;
        });

        // Función para el botón -
        btnMenos.addEventListener('click', () => {
            let val = parseInt(qtyNumber.innerText);
            // Evitamos que baje de 1
            if(val > 1) {
                qtyNumber.innerText = val - 1;
            }
        });
    }
});