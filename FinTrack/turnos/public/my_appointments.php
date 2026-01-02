<?php
include "components/layout_start.php";
?>

<h2 class="mb-4">Mis Turnos</h2>

<div class="card shadow p-4">
    <table class="table table-striped" id="tablaTurnos">
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="alertTurnos" class="alert d-none"></div>
</div>

<?php
include "components/layout_end.php";
?>
