/* js/AlgoritmoDNIs.js - VERSIÓN CORREGIDA GLOBAL */

// 1. EVENTOS AUTOMÁTICOS (Para validar visualmente mientras escribes)
document.addEventListener("DOMContentLoaded", function() {
    const inputDNI = document.getElementById('dni') || document.querySelector('input[name="dni"]');
    
    if (inputDNI) {
        inputDNI.addEventListener('blur', function() {
            validarDocumento(this); // Llama a la función global de abajo
        });
        
        // Quitar rojo al escribir
        inputDNI.addEventListener('input', function() {
            limpiarEstilos(this);
        });
    }
});

// =============================================================
// 2. FUNCIONES GLOBALES (FUERA del EventListener)
// IMPORTANTE: Tienen que estar aquí fuera para que el botón de Guardar las vea
// =============================================================

function validarDocumento(input) {
    if (!input) return false;
    const valor = input.value.toUpperCase().trim();
    
    // Limpiamos estilos previos
    limpiarEstilos(input);

    // Si es obligatorio y está vacío, falla
    if (valor === '' && input.hasAttribute('required')) return false;
    if (valor === '') return true; // Si no es obligatorio, pasa

    // 1. DNI (8 números + Letra)
    if (/^\d{8}[A-Z]$/.test(valor)) {
        if (letraDNIesCorrecta(valor)) {
            marcarValido(input);
            return true;
        }
        marcarError(input, "La letra del DNI no es correcta.");
        return false;
    }

    // 2. NIE (X/Y/Z + 7 números + Letra)
    else if (/^[XYZ]\d{7}[A-Z]$/.test(valor)) {
        if (letraNIEesCorrecta(valor)) {
            marcarValido(input);
            return true;
        }
        marcarError(input, "La letra del NIE no es correcta.");
        return false;
    }

    // 3. CIF
    else if (/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/.test(valor)) {
        marcarValido(input); 
        return true;
    }

    // Errores de formato
    else {
        marcarError(input, "Formato no válido. Revisa DNI, NIE o CIF.");
        return false;
    }
}

// --- AYUDAS ---
function letraDNIesCorrecta(dni) {
    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    return letras[dni.substr(0, 8) % 23] === dni.substr(8, 1);
}

function letraNIEesCorrecta(nie) {
    let num = nie.substr(0, 8).replace('X','0').replace('Y','1').replace('Z','2');
    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    return letras[num % 23] === nie.substr(8, 1);
}

// --- ESTILOS ---
function marcarError(input, txt) {
    input.style.borderColor = 'red';
    // (Opcional) Puedes añadir lógica para mostrar texto debajo
}

function marcarValido(input) {
    input.style.borderColor = 'green';
}

function limpiarEstilos(input) {
    input.style.borderColor = '';
}