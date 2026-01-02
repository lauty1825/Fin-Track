document.addEventListener("DOMContentLoaded", cargarTurnos);

function cargarTurnos() {
    fetch("../src/actions/load_my_appointments.php")
        .then(r => r.json())
        .then(turnos => {
            const tbody = document.querySelector("#tablaTurnos tbody");
            tbody.innerHTML = "";

            if (turnos.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="5" class="text-center">No tenés turnos registrados.</td></tr>
                `;
                return;
            }

            turnos.forEach(t => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${t.servicio}</td>
                    <td>${t.fecha}</td>
                    <td>${t.hora}</td>
                    <td><span class="badge bg-${colorEstado(t.status)}">${t.status}</span></td>
                    <td>${botonesAccion(t)}</td>
                `;

                tbody.appendChild(row);
            });
        });
}

function colorEstado(status) {
    switch (status) {
        case "pendiente": return "warning";
        case "asistido": return "success";
        case "cancelado": return "danger";
        default: return "secondary";
    }
}

function botonesAccion(t) {
    if (t.status === "pendiente") {
        return `
            <button class="btn btn-sm btn-danger me-2" onclick="cancelar(${t.id})">Cancelar</button>
            <button class="btn btn-sm btn-success" onclick="asistir(${t.id})">Asistido</button>
        `;
    }
    return `<small class="text-muted">Sin acciones</small>`;
}

function cancelar(id) {
    fetch("../src/actions/cancel_appointment.php", {
        method: "POST",
        body: new URLSearchParams({ id })
    })
    .then(r => r.json())
    .then(() => cargarTurnos());
}

function asistir(id) {
    fetch("../src/actions/attend_appointment.php", {
        method: "POST",
        body: new URLSearchParams({ id })
    })
    .then(r => r.json())
    .then(() => cargarTurnos());
}
