</main>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
  <div>FinTrack</div>
  <div>Lautaro Roche 7°5</div>
  <div class="small">Todos los derechos reservados ©</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<?php
// =============================
//   CARGA DE SCRIPTS POR ROL
// =============================
if (isset($_SESSION['usuario'])) {

    // ===== ADMIN =====
    if ($_SESSION['usuario']['role'] === 'admin') {

        // Carga el archivo principal del admin
        echo '<script src="assets/js/admin.js"></script>';

    // ===== USUARIO NORMAL =====
    } else {

        // Calendario del usuario
        echo '<script src="assets/js/calendar.js"></script>';

        // Tabla de Turnos del Usuario
        echo '<script src="assets/js/my_appointments.js"></script>';
    }
}
?>
</body>
</html>
