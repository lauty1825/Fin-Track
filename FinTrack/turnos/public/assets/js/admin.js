let adminCalendarInstance = null;

document.addEventListener("DOMContentLoaded", () => {
    initAdminCalendar();
    cargarTurnosDelDia();
    cargarStats();
});

// ===========================
// Calendario Admin
// ===========================
function initAdminCalendar() {
    const calendarEl = document.getElementById("adminCalendar");

    adminCalendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "es",
        events: {
            url: "../src/actions/admin_load_calendar.php",
            method: "GET",
            failure: function() {
                console.error("Error cargando eventos del calendario");
            }
        }
    });

    adminCalendarInstance.render();
}

// ===========================
// Tabla diaria
// ===========================
function cargarTurnosDelDia() {
    fetch("../src/actions/admin_load_table.php")
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector("#tablaTurnosAdmin tbody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">No hay turnos hoy.</td></tr>`;
                return;
            }

            data.forEach(t => {
                const botonesAcciones = `
                    <button class="btn btn-sm btn-primary" onclick="abrirEditarTurno(${t.id}, '${t.fecha}', '${t.hora}', '${t.servicio}')">Editar</button>
                    ${t.status === 'pendiente' ? `<button class="btn btn-sm btn-success" onclick="marcarAsistido(${t.id})">Asistido</button>` : ''}
                    ${t.status !== 'cancelado' ? `<button class="btn btn-sm btn-danger" onclick="cancelarTurnoAdmin(${t.id})">Cancelar</button>` : ''}
                `;
                tbody.innerHTML += `
                    <tr>
                        <td>${t.nombre} ${t.apellido}</td>
                        <td>${t.servicio}</td>
                        <td>${t.hora}</td>
                        <td><span class="badge bg-${colorEstado(t.status)}">${t.status}</span></td>
                        <td>${botonesAcciones}</td>
                    </tr>
                `;
            });

            // Agregar listener al formulario de editar
            const form = document.getElementById('editarTurnoForm');
            if (form) {
                form.removeEventListener('submit', manejarEditarTurno);
                form.addEventListener('submit', manejarEditarTurno);
            }
        });
}

function colorEstado(s) {
    switch (s) {
        case "pendiente": return "warning";
        case "asistido": return "success";
        case "cancelado": return "danger";
        default: return "secondary";
    }
}

// ===========================
// Estadísticas
// ===========================
function cargarStats() {
    fetch("../src/actions/admin_load_stats.php", { credentials: 'same-origin' })
        .then(r => {
            if (!r.ok) throw new Error('Respuesta no OK: ' + r.status);
            return r.json();
        })
        .then(stats => {
            const container = document.getElementById("adminStatsText");
            container.innerHTML = "";

            // Texto principal
            const lines = [];
            lines.push(`<div><strong>Total de turnos:</strong> ${stats.total}</div>`);
            lines.push(`<div><strong>Turnos pendientes:</strong> ${stats.total_pendientes}</div>`);
            lines.push(`<div><strong>Porcentaje asistidos:</strong> ${stats.pct_attended}%</div>`);
            lines.push(`<div><strong>Hora promedio:</strong> ${stats.avg_time || 'N/A'}</div>`);
            if (stats.peak_hour && stats.peak_hour.hora !== null) {
                lines.push(`<div><strong>Hora pico (pendientes):</strong> ${stats.peak_hour.hora}:00 — ${stats.peak_hour.c} turnos</div>`);
            }

            // Pendientes por servicio
            lines.push('<div class="mt-2"><strong>Turnos pendientes por servicio:</strong></div>');
            if (stats.pending_by_service && stats.pending_by_service.length > 0) {
                lines.push('<ul class="mb-0">');
                stats.pending_by_service.forEach(p => {
                    lines.push(`<li>${p.servicio}: ${p.total}</li>`);
                });
                lines.push('</ul>');
            } else {
                lines.push('<div class="small text-muted">No hay turnos pendientes por servicio.</div>');
            }

            // Últimos 7 días
            lines.push('<div class="mt-2"><strong>Turnos últimos 7 días:</strong></div>');
            if (stats.daily_last_7 && stats.daily_last_7.length > 0) {
                lines.push('<ul class="mb-0">');
                stats.daily_last_7.forEach(d => {
                    lines.push(`<li>${d.day}: ${d.total}</li>`);
                });
                lines.push('</ul>');
            } else {
                lines.push('<div class="small text-muted">No hay datos para los últimos 7 días.</div>');
            }

            container.innerHTML = lines.join('');
        })
        .catch(err => {
            const container = document.getElementById("adminStatsText");
            container.innerHTML = `<div class="text-danger">Error cargando estadísticas: ${err.message}</div>`;
            console.error('Error cargando estadísticas:', err);
        });
}

// ===========================
// Editar Turno
// ===========================
function abrirEditarTurno(id, fecha, hora, servicio) {
    document.getElementById('editarTurnoId').value = id;
    document.getElementById('editarTurnoFecha').value = fecha;
    document.getElementById('editarTurnoHora').value = hora;
    document.getElementById('editarTurnoServicio').value = servicio;
    document.getElementById('alertEditarTurno').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('editarTurnoModal')).show();
}

function manejarEditarTurno(e) {
    e.preventDefault();
    const id = document.getElementById('editarTurnoId').value;
    const fecha = document.getElementById('editarTurnoFecha').value;
    const hora = document.getElementById('editarTurnoHora').value;
    const servicio = document.getElementById('editarTurnoServicio').value;

    fetch('../src/actions/edit_appointment.php', {
        method: 'POST',
        body: new URLSearchParams({ id, fecha, hora, servicio })
    }).then(r => r.json()).then(data => {
        const a = document.getElementById('alertEditarTurno');
        if (data.error) {
            a.className = 'alert alert-danger';
            a.textContent = data.error;
            a.classList.remove('d-none');
            return;
        }
        a.className = 'alert alert-success';
        a.textContent = 'Turno actualizado correctamente.';
        a.classList.remove('d-none');
        setTimeout(() => {
            document.querySelector('#editarTurnoModal .btn-close').click();
            cargarTurnosDelDia();
            if (adminCalendarInstance) {
                adminCalendarInstance.refetchEvents();
            }
        }, 600);
    }).catch(err => {
        const a = document.getElementById('alertEditarTurno');
        a.className = 'alert alert-danger';
        a.textContent = 'Error al actualizar: ' + err.message;
        a.classList.remove('d-none');
    });
}

// ===========================
// Cancelar Turno (Admin)
// ===========================
function cancelarTurnoAdmin(id) {
    if (!confirm('¿Estás seguro de que deseas cancelar este turno?')) {
        return;
    }

    fetch('../src/actions/admin_cancel_appointment.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    }).then(r => r.json()).then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
            return;
        }
        alert('Turno cancelado correctamente');
        cargarTurnosDelDia();
        if (adminCalendarInstance) {
            adminCalendarInstance.refetchEvents();
        }
    }).catch(err => {
        alert('Error al cancelar el turno: ' + err.message);
    });
}

// ===========================
// Marcar como Asistido
// ===========================
function marcarAsistido(id) {
    if (!confirm('¿Marcar este turno como asistido?')) {
        return;
    }

    fetch('../src/actions/attend_appointment.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    }).then(r => r.json()).then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
            return;
        }
        alert('Turno marcado como asistido');
        cargarTurnosDelDia();
        if (adminCalendarInstance) {
            adminCalendarInstance.refetchEvents();
        }
    }).catch(err => {
        alert('Error al marcar como asistido: ' + err.message);
    });
}
