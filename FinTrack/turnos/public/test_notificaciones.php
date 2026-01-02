<?php
// Panel de prueba para notificaciones
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../security/session.php';

// Verificar que sea admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$accion = $_GET['accion'] ?? null;

if ($accion === 'ejecutar') {
    // Ejecutar el check de notificaciones
    require_once __DIR__ . '/check_and_send_notifications.php';
    exit;
}

// Si no, mostrar la interfaz del panel
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Notificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Panel de Control - Notificaciones de Turnos</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">Este panel permite ejecutar manualmente el proceso de envío de notificaciones.</p>
            
            <h5 class="mt-4">Información del sistema</h5>
            <ul>
                <li><strong>Función:</strong> Envía recordatorios 3 horas antes del turno</li>
                <li><strong>Frecuencia recomendada:</strong> Cada 5 minutos (vía cron job)</li>
                <li><strong>Hora actual del servidor:</strong> <code><?php echo date('Y-m-d H:i:s'); ?></code></li>
            </ul>

            <h5 class="mt-4">Acciones</h5>
            <button class="btn btn-primary" onclick="ejecutarNotificaciones()">
                ▶ Ejecutar proceso de notificaciones ahora
            </button>

            <div id="resultado" class="mt-3"></div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Próximos turnos pendientes</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm" id="proximosTurnos">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>En</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function ejecutarNotificaciones() {
        const btn = event.target;
        btn.disabled = true;
        btn.textContent = '⏳ Ejecutando...';

        fetch('?accion=ejecutar')
            .then(r => r.json())
            .then(data => {
                const resultado = document.getElementById('resultado');
                if (data.ok) {
                    resultado.innerHTML = `
                        <div class="alert alert-success">
                            <strong>✓ Proceso completado</strong><br>
                            Notificaciones enviadas: <strong>${data.enviadas}</strong><br>
                            Hora: ${data.hora_actual}
                        </div>
                    `;
                } else {
                    resultado.innerHTML = `<div class="alert alert-danger">Error: ${data.error}</div>`;
                }
                btn.disabled = false;
                btn.textContent = '▶ Ejecutar proceso de notificaciones ahora';
                cargarProximosTurnos();
            })
            .catch(err => {
                resultado.innerHTML = `<div class="alert alert-danger">Error de conexión: ${err}</div>`;
                btn.disabled = false;
                btn.textContent = '▶ Ejecutar proceso de notificaciones ahora';
            });
    }

    function cargarProximosTurnos() {
        fetch('../../src/actions/admin_load_table.php')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#proximosTurnos tbody');
                tbody.innerHTML = '';

                const ahora = new Date();

                data.forEach(t => {
                    const turnoTime = new Date(`${t.fecha}T${t.hora}`);
                    const diff = turnoTime - ahora;

                    if (diff > 0) {
                        const horas = Math.floor(diff / 3600000);
                        const minutos = Math.floor((diff % 3600000) / 60000);
                        const tiempo = `${horas}h ${minutos}m`;

                        tbody.innerHTML += `
                            <tr>
                                <td>${t.nombre} ${t.apellido}</td>
                                <td>${t.servicio}</td>
                                <td>${t.fecha}</td>
                                <td>${t.hora}</td>
                                <td><span class="badge ${Math.abs(diff - 3*3600000) < 5*60000 ? 'bg-danger' : 'bg-secondary'}">${tiempo}</span></td>
                            </tr>
                        `;
                    }
                });

                if (tbody.innerHTML === '') {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay turnos próximos</td></tr>';
                }
            });
    }

    // Cargar turnos al iniciar
    cargarProximosTurnos();
</script>
</body>
</html>
