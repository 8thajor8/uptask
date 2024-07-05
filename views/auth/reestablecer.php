<div class="contenedor reestablecer">

    <?php include __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo Password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <?php if($mostrar){ ?>

        <form class="formulario" method="POST">

            

            <div class="campo">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    placeholder="Tu password"
                    name="password"
                />
            </div>

            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input
                    type="password"
                    id="password2"
                    placeholder="Repite tu password"
                    name="password2"
                />
            </div>

            <input type="submit" class="boton" value="Guardar Password">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/">Ya tienes una cuenta? Inicia sesion</a>
            <a href="/olvide">Olvidaste tu Password?</a>
        </div>

    </div>
</div>