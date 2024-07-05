
<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form class="formulario" method="POST" action="/perfil">
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input
                type="text"
                value="<?php echo $usuario->nombre ; ?>"
                placeholder="Tu Nombre"
                name="nombre"
            />    
        </div>

        <div class="campo">
            <label for="email">Email</label>
            <input
                type="email"
                value="<?php echo $usuario->email ; ?>"
                placeholder="Tu email"
                name="email"
            />    
        </div>

        <input type="submit" value="Guardar Cambios" >
    </form>
</div>
<?php include_once __DIR__ . "/footer-dashboard.php"; ?>