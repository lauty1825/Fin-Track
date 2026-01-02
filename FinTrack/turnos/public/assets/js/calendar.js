document.addEventListener('DOMContentLoaded', function () {

  const calendarEl = document.getElementById('calendar');

  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    selectable: true,
    events: '../src/actions/load_appointments.php',
    dateClick: function (info) {
      const fecha = info.dateStr;
      const hoy = new Date().toISOString().split('T')[0];
      if (fecha < hoy) { alert("No se pueden sacar turnos en días pasados."); return; }
      document.getElementById('fechaTurno').value = fecha;
      new bootstrap.Modal(document.getElementById('turnoModal')).show();
    }
  });

  calendar.render();

  const form = document.getElementById('turnoForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const fecha = document.getElementById('fechaTurno').value;
    const hora = document.getElementById('horaTurno').value;
    const servicio = document.getElementById('servicioTurno').value;

    if (!hora || !servicio) {
      const a = document.getElementById('alertTurno');
      a.className = 'alert alert-danger'; a.textContent = 'Complete todos los campos'; a.classList.remove('d-none'); return;
    }

    fetch('../src/actions/create_appointment.php', {
      method: 'POST',
      body: new URLSearchParams({ fecha, hora, servicio })
    }).then(r => r.json()).then(data => {
      const a = document.getElementById('alertTurno');
      if (data.error) {
        a.className = 'alert alert-danger'; a.textContent = data.error; a.classList.remove('d-none'); return;
      }
      a.className = 'alert alert-success'; a.textContent = 'Turno creado.'; a.classList.remove('d-none');
      setTimeout(() => { document.querySelector('.modal.show .btn-close').click(); calendar.refetchEvents(); }, 600);
    });
  });

});
