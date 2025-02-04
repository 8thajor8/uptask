(function(){

    obtenerTareas();
    let tareas = [];
    let filtradas = [];

    //Boton mostrar modal nueva tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', function(){
        
        mostrarFormulario(false);
    
    });

    //Filtros de busqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]')
    

    filtros.forEach( radio => {
        radio.addEventListener('input', filtrarTareas);
    })

    function filtrarTareas(e){
        const filtro = e.target.value;

        if(filtro !== ''){
            filtradas = tareas.filter(tarea=> tarea.estado === filtro)
        }else{
            filtradas = [];
        }
        
        mostrarTareas();
    }
    async function obtenerTareas(){

        try {
            const id = obtenerProyecto();
            const url = `/api/tareas?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            tareas = resultado.tareas;
            
            mostrarTareas();


        } catch (error) {
            
        }
    }


    function mostrarTareas(){
        limpiarTareas();
        totalPendientes();
        totalCompletas();

        const arrayTareas = filtradas.length ? filtradas : tareas;

        const contenedorTareas = document.querySelector('#listado-tareas');
        if(arrayTareas.length === 0){
            

            const textoNoTareas = document.createElement('DIV');

            textoNoTareas.textContent = 'No hay tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }
        
        arrayTareas.forEach( tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');
            
            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre
            nombreTarea.ondblclick = function(){
                mostrarFormulario(true, {...tarea});
            }

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //Botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.dataset.estadoTarea = tarea.estado; 
            btnEstadoTarea.textContent = estados[tarea.estado]; 
            btnEstadoTarea.ondblclick = function(){
                cambiarEstadoTarea({...tarea});
            }

            
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id; 
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.onclick = function(){
                confirmarEliminarTarea({...tarea});
            }


            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);
            
            contenedorTareas.appendChild(contenedorTarea);

        })
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === '0');

        const pendientesRadio = document.querySelector('#pendientes');

        if (totalPendientes.length === 0){
            pendientesRadio.disabled = true;
        } else {
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletas(){
        const totalCompletas = tareas.filter(tarea => tarea.estado === '1');

        const completasRadio = document.querySelector('#completadas');

        if (totalCompletas.length === 0){
            completasRadio.disabled = true;
        } else {
            completasRadio.disabled = false;
        }
    }

    function mostrarFormulario(editar = false, tarea = {}){
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>${editar ? 'Editar Tarea' : 'Añade una nueva tarea'}</legend>
                <div class="campo">
                    <label for='tarea'>Tarea</label>
                    <input
                        type="text"
                        name="tarea"
                        placeholder="${tarea.nombre ? 'Edita la tarea' : 'Agregar tarea al proyecto actual'}"
                        id="tarea"
                        value="${tarea.nombre ? tarea.nombre : ''}"
                    />
                </div>

                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nueva-tarea" 
                        value="${editar ? 'Guardar Cambios' : 'Agregar Tarea'}" />

                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(()=>{
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar')
        },100)

        modal.addEventListener('click', (e)=>{
            e.preventDefault();

            if(e.target.classList.contains('cerrar-modal')){
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar')
                setTimeout(()=>{
                    modal.remove();
                },500)
               
            }

            if(e.target.classList.contains('submit-nueva-tarea')){
                const nombreTarea = document.querySelector('#tarea').value.trim();

                if(nombreTarea === ''){
                    //Mostrar alerta de error
                    mostrarAlerta('El nombre de la tarea es obligatorio', 'error', document.querySelector('.formulario legend'));
                    return;
                } 

                if(editar){
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                }else{
                    agregarTarea(nombreTarea);
                }
                
            }

        })
        document.querySelector('.dashboard').appendChild(modal);
    }

    
    function mostrarAlerta(mensaje, tipo, referencia){
            //Previene creacion de alertas previas
            const alertaPrevia = document.querySelector('.alerta');
            if(alertaPrevia){
                alertaPrevia.remove();
            }
            const alerta = document.createElement('DIV');
            alerta.classList.add('alerta', tipo);
            alerta.textContent = mensaje;
            referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

            setTimeout(()=>{
                alerta.remove();
            },2500)
    }

    async function agregarTarea(tarea){
        //Construir la peticion

        const datos = new FormData();
        datos.append('nombre', tarea)
        datos.append('proyectoId', obtenerProyecto())
        
        

        try {
            const url= '/api/tarea';
            const respuesta = await fetch(url,{
                method:'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'))
            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                const formulario = document.querySelector('.formulario');
                setTimeout(()=>{
                    formulario.classList.add('cerrar')
                    setTimeout(()=>{
                        
                        modal.remove();
                    },500)
                },1000)
                //Agregar la tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: 0,
                    proyectoId: resultado.proyectoId
                }
                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }
        } catch (error) {
            
        }
    }

    function cambiarEstadoTarea(tarea){
        
        const nuevoEstado = tarea.estado === '1' ? '0' : '1';
        tarea.estado = nuevoEstado;
        actualizarTarea(tarea);
        
    }

    async function actualizarTarea(tarea){
        
        const{estado, id, nombre} = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        
        datos.append('url', obtenerProyecto());

        try {
            
            const url = '/api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            if(resultado.respuesta.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                if(modal){
                    const formulario = document.querySelector('.formulario');
                    setTimeout(()=>{
                        formulario.classList.add('cerrar')
                        setTimeout(()=>{
                            
                            modal.remove();
                        },200)
                    },200)
                }
                mostrarAlerta(resultado.respuesta.mensaje, resultado.respuesta.tipo, document.querySelector('.contenedor-nueva-tarea'))

                tareas = tareas.map(tareaMemoria => {
                    if(tareaMemoria.id === id){
                        tareaMemoria.estado = estado;
                        tareaMemoria.nombre = nombre;

                    }

                    return tareaMemoria;
                })

                mostrarTareas();
            };
            
        } catch (error) {
            
        }
    }

    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: 'Eliminar Tarea?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    eliminarTarea(tarea);
                } 
            });
    }

    async function eliminarTarea(tarea){

        const{id} = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('url', obtenerProyecto());
        try {

            const url = '/api/tarea/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            
            if(resultado.respuesta.tipo === 'exito'){

                mostrarAlerta(resultado.respuesta.mensaje, resultado.respuesta.tipo, document.querySelector('.contenedor-nueva-tarea'))
                
                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== id);
                    
                mostrarTareas();
            };
            

            
        } catch (error) {
            
        }
    }

    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());
        return proyecto.id; 
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();

