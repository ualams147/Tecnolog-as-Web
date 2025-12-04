document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos todos los botones de 'mas' y 'menos'
    const botonesMas = document.querySelectorAll('.btn-mas');
    const botonesMenos = document.querySelectorAll('.btn-menos');

    // Función para el botón +
    botonesMas.forEach(boton => {
        boton.addEventListener('click', function() {
            // Buscamos el número que está al lado de este botón
            const contador = this.parentElement.querySelector('.qty-text');
            let numero = parseInt(contador.innerText);
            numero++; // Sumamos 1
            contador.innerText = numero;
            
            // Aquí podrías añadir lógica para actualizar el precio total
            // actualizarTotal(); 
        });
    });

    // Función para el botón -
    botonesMenos.forEach(boton => {
        boton.addEventListener('click', function() {
            // Buscamos el número que está al lado de este botón
            const contador = this.parentElement.querySelector('.qty-text');
            let numero = parseInt(contador.innerText);
            if (numero > 0) { // Solo restamos si es mayor que 0
                numero--; 
                contador.innerText = numero;
                
                // Aquí podrías añadir lógica para actualizar el precio total
                // actualizarTotal();
            }
        });
    });
});