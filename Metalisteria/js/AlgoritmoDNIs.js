document.addEventListener("DOMContentLoaded", function() {
    
    const inputDNI = document.querySelector('input[name="dni"]');
    const form = document.querySelector('form');

    if (inputDNI) {
        
        // Validar al perder el foco (cuando terminas de escribir)
        inputDNI.addEventListener('blur', function() {
            validarDocumento(this);
        });

        // Validar al enviar el formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                const resultado = validarDocumento(inputDNI);
                if (!resultado) {
                    e.preventDefault(); // Parar envío
                    // Hacemos focus para que el usuario vea dónde está el error
                    inputDNI.focus();
                }
            });
        }
    }

    /**
     * Función principal que decide qué mensaje mostrar
     */
    function validarDocumento(input) {
        const valor = input.value.toUpperCase().trim();
        
        // Limpiamos estilos previos
        limpiarEstilos(input);

        // Permitimos vacío (si no es obligatorio)
        if (valor === '') return true;

        // --- VALIDACIONES DE FORMATO Y LETRA ---

        // 1. DNI (8 números + Letra)
        if (/^\d{8}[A-Z]$/.test(valor)) {
            if (letraDNIesCorrecta(valor)) {
                marcarValido(input);
                return true;
            } else {
                marcarError(input, "La letra del DNI no es correcta.");
                return false;
            }
        }

        // 2. NIE (X/Y/Z + 7 números + Letra)
        else if (/^[XYZ]\d{7}[A-Z]$/.test(valor)) {
            if (letraNIEesCorrecta(valor)) {
                marcarValido(input);
                return true;
            } else {
                marcarError(input, "La letra del NIE no es correcta.");
                return false;
            }
        }

        // 3. CIF (Letra + 7 números + Letra/Número)
        else if (/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/.test(valor)) {
            marcarValido(input); // Damos por bueno el formato CIF
            return true;
        }

        // --- AYUDAS INTELIGENTES CUANDO EL FORMATO FALLA ---

        // Si empieza por X, Y o Z (intento de NIE)
        else if (/^[XYZ]/.test(valor)) {
            marcarError(input, "Formato NIE incorrecto. Debe ser: Letra + 7 números + Letra (Ej: X1234567L)");
            return false;
        }

        // Si empieza por número (intento de DNI)
        else if (/^\d/.test(valor)) {
            marcarError(input, "Formato DNI incorrecto. Debe ser: 8 números + Letra (Ej: 12345678Z)");
            return false;
        }

        // Si empieza por letra de empresa (intento de CIF)
        else if (/^[ABCDEFGHJKLMNPQRSUVW]/.test(valor)) {
            marcarError(input, "Formato CIF incorrecto. (Ej: B12345678)");
            return false;
        }

        // Si no se parece a nada conocido
        else {
            marcarError(input, "Documento no válido. Introduce un DNI, NIE o CIF correcto.");
            return false;
        }
    }

    // --- FUNCIONES MATEMÁTICAS ---

    function letraDNIesCorrecta(dni) {
        const numero = dni.substr(0, 8);
        const letra = dni.substr(8, 1);
        const letrasValidas = "TRWAGMYFPDXBNJZSQVHLCKE";
        return letrasValidas[numero % 23] === letra;
    }

    function letraNIEesCorrecta(nie) {
        let numero = nie.substr(0, 8);
        numero = numero.replace('X', '0').replace('Y', '1').replace('Z', '2');
        
        const letra = nie.substr(8, 1);
        const letrasValidas = "TRWAGMYFPDXBNJZSQVHLCKE";
        return letrasValidas[numero % 23] === letra;
    }

    // --- FUNCIONES VISUALES ---

    function marcarError(input, mensaje) {
        input.classList.add('input-error');
        input.classList.remove('input-success');
        
        // Crear mensaje debajo si no existe
        let span = input.parentNode.querySelector('.msg-error');
        if (!span) {
            span = document.createElement('span');
            span.className = 'msg-error';
            span.style.color = '#e74c3c'; // Rojo
            span.style.fontSize = '13px';
            span.style.marginTop = '5px';
            span.style.fontWeight = '600';
            span.style.display = 'block'; // Para que baje de línea
            input.parentNode.appendChild(span);
        }
        span.innerText = mensaje; // Actualizamos el texto del error
    }

    function marcarValido(input) {
        input.classList.remove('input-error');
        input.classList.add('input-success');
        
        // Borrar mensaje de error si había
        const span = input.parentNode.querySelector('.msg-error');
        if (span) span.remove();
    }

    function limpiarEstilos(input) {
        input.classList.remove('input-error', 'input-success');
        const span = input.parentNode.querySelector('.msg-error');
        if (span) span.remove();
    }
});