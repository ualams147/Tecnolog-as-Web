document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. LÓGICA DE CANTIDAD (BTN +/- Y ESCRITURA) ---
    const btnMas = document.querySelector('.btn-mas');
    const btnMenos = document.querySelector('.btn-menos');
    const inputCantidad = document.getElementById('input-cantidad'); 
    const formCarrito = document.getElementById('form-carrito'); // Obtenemos el formulario

    if (btnMas && btnMenos && inputCantidad) {
        
        // Botón Sumar
        btnMas.addEventListener('click', () => {
            let val = parseInt(inputCantidad.value) || 0; 
            val++;
            inputCantidad.value = val;
        });

        // Botón Restar
        btnMenos.addEventListener('click', () => {
            let val = parseInt(inputCantidad.value) || 0;
            if (val > 1) {
                val--;
                inputCantidad.value = val;
            }
        });

        // Evento de cambio manual (Validación al salir del campo)
        inputCantidad.addEventListener('change', () => {
            let val = parseInt(inputCantidad.value);
            // Si escribe letras, 0 o negativos, lo devolvemos a 1
            if (isNaN(val) || val < 1) {
                inputCantidad.value = 1;
            }
        });

        // --- FIX IMPORTANTE: BLOQUEAR ENTER EN EL INPUT ---
        // Esto evita que el formulario se envíe al pulsar Enter en el input
        inputCantidad.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();   // Detiene el envío
                inputCantidad.blur(); // Quita el foco (simula hacer clic fuera)
            }
        });
        
        // ** (Añadido) FIX Adicional: Bloquear Enter en todo el formulario **
        // A veces el evento no se captura en el input, sino en el formulario general.
        if (formCarrito) {
             formCarrito.addEventListener('submit', function(e) {
                // Si el evento de envío NO fue activado por el botón explícito de submit,
                // sino por una pulsación de tecla, lo bloqueamos.
                // Esta es una solución más general.
                 if (document.activeElement === inputCantidad) {
                     e.preventDefault();
                     inputCantidad.blur();
                 }
             });
        }
    }

    // --- 2. LÓGICA DE DESPLEGABLES (Cambio de Producto) ---
    const selectColor = document.getElementById('select-color');
    const selectMedidas = document.getElementById('select-medidas');
    const dataDiv = document.getElementById('data-json');

    if (selectColor && selectMedidas && dataDiv) {
        const variantes = JSON.parse(dataDiv.dataset.variantes);
        const idActual = dataDiv.dataset.actual;

        const cambiarProducto = () => {
            const colorSeleccionado = selectColor.value;
            const medidaSeleccionada = selectMedidas.value;

            const productoEncontrado = variantes.find(prod => 
                prod.color === colorSeleccionado && prod.medidas === medidaSeleccionada
            );

            if (productoEncontrado) {
                if (productoEncontrado.id != idActual) {
                    window.location.href = `infoProducto.php?id=${productoEncontrado.id}`;
                }
            } else {
                console.log("Combinación no disponible");
            }
        };

        selectColor.addEventListener('change', cambiarProducto);
        selectMedidas.addEventListener('change', cambiarProducto);
    }
});