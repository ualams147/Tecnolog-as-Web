/* js/ValidadorPassword.js */

/**
 * Función para activar la validación de contraseñas en tiempo real.
 * @param {string} idInputPass - ID del input de la nueva contraseña.
 * @param {string} idInputConfirm - ID del input de confirmar contraseña.
 * @param {string} idListaRequisitos - ID del UL donde se mostrarán los requisitos.
 * @param {string} idBotonSubmit - ID del botón de enviar (para bloquearlo si no es válido).
 */
function activarValidacionPassword(idInputPass, idInputConfirm, idListaRequisitos, idBotonSubmit) {
    
    const inputPass = document.getElementById(idInputPass);
    const inputConfirm = document.getElementById(idInputConfirm);
    const listaReq = document.getElementById(idListaRequisitos);
    const btnSubmit = document.getElementById(idBotonSubmit);

    // Si no existen los elementos, no hacemos nada (evita errores en páginas donde no se usa)
    if (!inputPass || !listaReq) return;

    // Definimos las reglas
    const reglas = [
        { id: 'req-longitud', regex: /.{8,}/, texto: 'Mínimo 8 caracteres' },
        { id: 'req-mayus', regex: /[A-Z]/, texto: 'Al menos una mayúscula' },
        { id: 'req-minus', regex: /[a-z]/, texto: 'Al menos una minúscula' },
        { id: 'req-num', regex: /[0-9]/, texto: 'Al menos un número' }
    ];

    // Generamos el HTML de la lista dinámicamente la primera vez
    listaReq.innerHTML = reglas.map(regla => 
        `<li id="${regla.id}" class="requisito-pendiente"><i class="fas fa-circle"></i> ${regla.texto}</li>`
    ).join('') + `<li id="req-coinciden" class="requisito-pendiente"><i class="fas fa-circle"></i> Las contraseñas coinciden</li>`;

    // Función que comprueba todo
    function validar() {
        const valor = inputPass.value;
        const valorConfirm = inputConfirm ? inputConfirm.value : '';
        let todoValido = true;

        // 1. Comprobar reglas de complejidad
        reglas.forEach(regla => {
            const item = document.getElementById(regla.id);
            const cumple = regla.regex.test(valor);
            
            actualizarEstilo(item, cumple);
            if (!cumple) todoValido = false;
        });

        // 2. Comprobar que coincidan (solo si existe el campo confirmar y la pass no está vacía)
        const itemCoinciden = document.getElementById('req-coinciden');
        if (inputConfirm) {
            const coinciden = (valor === valorConfirm) && valor.length > 0;
            actualizarEstilo(itemCoinciden, coinciden);
            if (!coinciden) todoValido = false;
        }

        // 3. Controlar el botón de envío
        // Si el campo está vacío (caso editar perfil), no bloqueamos a menos que empiece a escribir
        if (btnSubmit) {
            if (valor.length === 0 && (!inputConfirm || inputConfirm.value.length === 0)) {
                // Si están vacíos (en editar perfil es válido no cambiar pass)
                // OJO: En registro esto debería ser false, pero lo controlas con 'required' en HTML
                btnSubmit.disabled = false; 
                listaReq.style.display = 'none'; // Ocultamos la lista si no escribe
            } else {
                listaReq.style.display = 'block'; // Mostramos lista
                btnSubmit.disabled = !todoValido;
            }
        }
    }

    // Helper visual
    function actualizarEstilo(elemento, cumple) {
        if (cumple) {
            elemento.classList.remove('requisito-pendiente', 'requisito-mal');
            elemento.classList.add('requisito-bien');
            elemento.querySelector('i').className = 'fas fa-check-circle';
        } else {
            elemento.classList.remove('requisito-bien');
            elemento.classList.add('requisito-pendiente'); // O 'requisito-mal' si quieres rojo
            elemento.querySelector('i').className = 'far fa-circle';
        }
    }

    // Listeners (Escuchan cuando escribes)
    inputPass.addEventListener('input', validar);
    if (inputConfirm) inputConfirm.addEventListener('input', validar);
    
    // Iniciar oculto
    listaReq.style.display = 'none';
}