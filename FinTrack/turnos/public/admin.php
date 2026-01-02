<?php
include "components/layout_start.php";

// Solo admin
if ($_SESSION['usuario']['role'] !== "admin") {
    header("Location: home.php");
    exit;
}
?>

<h2 class="mb-4">Panel Administrativo</h2>

<div class="row g-4">

    <!-- Calendario -->
    <div class="col-12">
        <div class="card shadow p-4">
            <h4>Calendario</h4>
            <div id="adminCalendar"></div>
        </div>
    </div>

    <!-- Tabla diaria -->
    <div class="col-lg-6 col-12">
        <div class="card shadow p-4">
            <h4>Turnos del Día</h4>
            <table class="table table-striped" id="tablaTurnosAdmin">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Servicio</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="col-lg-6 col-12">
        <div class="card shadow p-4">
            <h4>Estadísticas</h4>
            <div class="d-flex justify-content-between mb-3">
                <div id="adminStatsText" class="flex-grow-1"></div>
                <div class="ms-3">
                    <a href="../src/actions/admin_stats_pdf.php" target="_blank" class="btn btn-outline-primary">Descargar PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar turno -->
<div class="modal fade" id="editarTurnoModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editarTurnoForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Turno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="alertEditarTurno" class="alert d-none"></div>
        <input type="hidden" id="editarTurnoId">
        <label class="form-label">Fecha</label>
        <input id="editarTurnoFecha" type="date" class="form-control mb-3" required>
        <label class="form-label">Hora</label>
        <input id="editarTurnoHora" type="time" class="form-control mb-3" required>
        <label class="form-label">Servicio</label>
        <select id="editarTurnoServicio" class="form-control" required>
          <option value="">Seleccionar...</option>
          <option value="Consulta">Consulta</option>
          <option value="Control">Control</option>
          <option value="Estudio">Estudio</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<?php
include "components/layout_end.php";
?>
