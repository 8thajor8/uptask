
<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver</a>

    <form class="formulario" method="POST" action="/cambiar-password">
        <div class="campo">
            <label for="password_actual">Password Actual</label>
            <input
                type="password"
                
                placeholder="Tu Password Actual"
                name="password_actual"
            />    
        </div>

        <div class="campo">
            <label for="password_nuevo">Nuevo Password</label>
            <input
                type="password"
                
                placeholder="Tu nuevo password"
                name="password_nuevo"
            />    
        </div>

        <input type="submit" value="Guardar Cambios" >
    </form>
</div>
<?php include_once __DIR__ . "/footer-dashboard.php"; ?>