# Sistema de Notificaciones de Turnos

## Descripción
El sistema envía un email de recordatorio **3 horas antes** de cada turno programado.

## Archivos involucrados

### 1. `src/actions/send_appointment_notification.php`
Función `sendAppointmentNotification()` que envía el email de notificación.

### 2. `src/actions/check_and_send_notifications.php`
Script que busca turnos próximos y envía las notificaciones.

### 3. `notification_log` (tabla en BD)
Registra el historial de notificaciones enviadas para evitar duplicados.

## Configuración

### Opción 1: Cron Job (Linux/Servidor)
Ejecutar cada 5 minutos:

```bash
*/5 * * * * curl http://tudominio.com/src/actions/check_and_send_notifications.php
```

O con PHP CLI:
```bash
*/5 * * * * php /ruta/al/proyecto/src/actions/check_and_send_notifications.php
```

### Opción 2: Windows Task Scheduler
1. Abre "Task Scheduler"
2. Crea una tarea que ejecute cada 5 minutos:
   ```
   C:\xampp\php\php.exe C:\xampp\htdocs\turnos\src\actions\check_and_send_notifications.php
   ```

### Opción 3: Llamada desde el frontend
Si no tienes acceso a cron, puedes llamar al script desde JavaScript periódicamente:

```javascript
setInterval(() => {
    fetch('../src/actions/check_and_send_notifications.php')
        .then(r => r.json())
        .then(data => console.log('Notificaciones procesadas:', data.enviadas));
}, 5 * 60 * 1000); // Cada 5 minutos
```

## Configuración de Email
Asegúrate de que en `src/config.php` estén configuradas correctamente las credenciales SMTP:

```php
$config->smtp = (object)[
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'username'   => 'tu_email@gmail.com',
    'password'   => 'tu_contraseña_app',
    'from_email' => 'tu_email@gmail.com',
    'from_name'  => 'FinTrack'
];
```

## Cómo funciona

1. El script se ejecuta cada 5 minutos
2. Busca turnos programados para los próximos 3 horas (± 5 minutos)
3. Verifica que NO haya sido enviada una notificación previamente
4. Envía el email de recordatorio
5. Registra el envío en la tabla `notification_log`

## Prueba manual
Para probar el sistema, accede a:
```
http://localhost/turnos/src/actions/check_and_send_notifications.php
```

Deberías ver una respuesta JSON como:
```json
{
  "ok": true,
  "enviadas": 2,
  "hora_actual": "2025-11-30 14:30:45"
}
```

## Notas
- Solo se envían notificaciones para turnos con status **'pendiente'**
- No se envía más de una notificación por turno
- El email se envía 3 horas antes del turno programado
