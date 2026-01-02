<?php include __DIR__ . '/components/layout_start.php'; ?>

<h3 class="mb-4">Calendario</h3>

<div class="card shadow p-4">
  <div id="calendar"></div>
</div>


<!-- Modal para crear turno -->
<div class="modal fade" id="turnoModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="turnoForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Turno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="alertTurno" class="alert d-none"></div>
        <input type="hidden" id="fechaTurno">
        <label class="form-label">Hora</label>
        <input id="horaTurno" type="time" class="form-control mb-3" required>
        <label class="form-label">Servicio</label>
        <select id="servicioTurno" class="form-control" required>
          <option value="">Seleccionar...</option>
          <option value="Consulta">Consulta</option>
          <option value="Control">Control</option>
          <option value="Estudio">Estudio</option>
        </select>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Confirmar</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/components/layout_end.php'; ?>
