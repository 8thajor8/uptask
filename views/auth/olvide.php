<div class="contenedor olvide">

    <?php include __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">


        <p class="descripcion-pagina">Recupera tu acceso a UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <form class="formulario" method="POST" action="/olvide" novalidate>

            <div class="campo">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Tu e-mail"
                    name="email"
                    
                />
            </div>

            
            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">Ya tienes una cuenta? Inicia sesion</a>
            <a href="/crear">Aun no tienes una cuenta? Crear una</a>
        </div>

    </div>
</div>