// schedule-with-subjects.js
// Minimal UI: selector de materia + fecha (datePicker) -> muestra tabla Lunes-Sábado (07:00-19:00).
// Ahora incluye un ejemplo de tutoría (Álgebra) con botón Reservar que muestra alert de éxito.

(function(){
  const datePicker = document.getElementById('datePicker');
  const prevBtn = document.getElementById('prevDay');
  const nextBtn = document.getElementById('nextDay');
  const prettyDate = document.getElementById('prettyDate');
  const scheduleTable = document.getElementById('scheduleTable');
  const subjectSelect = document.getElementById('subjectSelect');

  if(!datePicker || !scheduleTable || !prettyDate || !subjectSelect) return;

  // Lista de materias (solo siglas y nombre).
  const subjects = [
    { code: 'ALG501', name: 'Álgebra Vectorial y Matrices' },
    { code: 'ANF231', name: 'Antropología Filosófica' },
    { code: 'LME404', name: 'Lenguajes de Marcado y Estilo Web' },
    { code: 'PAL404', name: 'Programación de Algoritmos' },
    { code: 'REC404', name: 'Redes de Comunicación' },
    { code: 'ASB404', name: 'Análisis y Diseño de Sistemas y Base de Datos' },
    { code: 'DAW404', name: 'Desarrollo de Aplic. Web con Soft. Interpret. en el Cliente' },
    { code: 'DSP404', name: 'Desarrollo de Aplicaciones con Software Propietario' },
    { code: 'POO404', name: 'Programación Orientada a Objetos' },
    { code: 'PSC231', name: 'Pensamiento Social Cristiano' },
    { code: 'ASN441', name: 'Administración de Servicios en la Nube' },
    { code: 'DPS441', name: 'Diseño y Programación de Software Multiplataforma' },
    { code: 'DSS404', name: 'Desarrollo de Aplic. Web con Soft. Interpret. en el Servidor' },
    { code: 'DWF404', name: 'Desarrollo de Aplicaciones con Web Frameworks' },
    { code: 'SPP404', name: 'Servidores en Plataformas Propietarias' },
    { code: 'APR404', name: 'Administración de Proyectos' },
    { code: 'DSM441', name: 'Desarrollo de Software para Móviles' },
    { code: 'EAI441', name: 'Electrónica Aplicada al Internet de las Cosas' },
    { code: 'SDR404', name: 'Seguridad de Redes' },
    { code: 'SPL404', name: 'Servidores en Plataformas Libres' }
  ];

  // --- DEMO: tutoría concreta de Álgebra ---
  // Se mostrará en la tabla si la celda coincide en fecha y hora (se coloca en la fila de la hora 11)
  const demoTutoria = {
    id: 't_alg_001',
    materiaCode: 'ALG501',
    materiaName: 'Álgebra Vectorial y Matrices',
    date: '2025-09-29',        // <-- cambiado a 29 de septiembre de 2025
    hora: '11:15',             // formato HH:MM (se colocará en la fila de la hora 11)
    cupos: 30,
    modalidad: 'Virtual',
    descripcion: 'Seguimiento de teoría y ejercicios - espacio para dudas en modalidad remota.'
  };
  // ---------------------------------------

  // Rellena el selector de materias
  function populateSubjects() {
    subjectSelect.innerHTML = '';
    const optAll = document.createElement('option');
    optAll.value = '';
    optAll.textContent = '-- Seleccione materia (o deje vacío) --';
    subjectSelect.appendChild(optAll);

    subjects.forEach(s=>{
      const opt = document.createElement('option');
      opt.value = s.code;
      opt.textContent = `${s.code} — ${s.name}`;
      subjectSelect.appendChild(opt);
    });
  }

  // Horas
  const startHour = 7;
  const endHour = 19;

  // Inicializa fecha hoy
  const today = new Date();
  datePicker.value = today.toISOString().slice(0,10);

  // Dado una fecha (YYYY-MM-DD) devuelve el lunes de esa semana (ISO week starting Monday)
  function getMonday(dStr) {
    const d = new Date(dStr + 'T00:00:00');
    const day = (d.getDay() + 6) % 7; // 0 = Monday, ..., 6 = Sunday
    const monday = new Date(d);
    monday.setDate(d.getDate() - day);
    return monday;
  }

  function addDays(d, n) {
    const r = new Date(d);
    r.setDate(d.getDate() + n);
    return r;
  }

  function formatDayHeader(dt) {
    // "Lun 15 Sep"
    return dt.toLocaleDateString('es-ES', { weekday:'short', day:'numeric', month:'short' });
  }

  function formatPretty(d) {
    const dt = new Date(d);
    return dt.toLocaleDateString('es-ES', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
  }

  // Construye la tabla Lunes-Sábado (6 columnas)
  function buildTable() {
    scheduleTable.innerHTML = '';

    // Header
    const thead = document.createElement('thead');
    const trh = document.createElement('tr');

    const thTime = document.createElement('th');
    thTime.textContent = 'Hora';
    thTime.className = 'time-col';
    trh.appendChild(thTime);

    // calcular lunes de la semana seleccionada
    const monday = getMonday(datePicker.value);

    // columnas: Lunes..Sábado
    const weekDays = [];
    for(let i=0;i<6;i++){
      const dayDt = addDays(monday, i);
      weekDays.push(dayDt);
      const th = document.createElement('th');
      th.textContent = formatDayHeader(dayDt);
      trh.appendChild(th);
    }
    thead.appendChild(trh);
    scheduleTable.appendChild(thead);

    // Body
    const tbody = document.createElement('tbody');
    for(let h = startHour; h <= endHour; h++){
      const tr = document.createElement('tr');
      const tdTime = document.createElement('td');
      tdTime.textContent = (h<10? '0'+h: h) + ':00';
      tdTime.className = 'time-col';
      tr.appendChild(tdTime);

      // una celda por cada día (lunes->sábado)
      weekDays.forEach((dayDt, cIdx) => {
        const td = document.createElement('td');
        td.className = 'cell';
        td.dataset.hour = h;
        td.dataset.weekday = cIdx; // 0..5
        td.dataset.date = dayDt.toISOString().slice(0,10);
        td.dataset.subject = subjectSelect.value || '';

        // Si la celda coincide con la demoTutoria (mismo día y hora en la fila aproximada),
        // mostramos los datos y añadimos botón "Reservar".
        // Nota: demoTutoria.hora es "11:15" -> colocamos en la fila h=11
        if (td.dataset.date === demoTutoria.date) {
          // Si el selector está vacío o coincide con la materia demo, mostramos
          if (!subjectSelect.value || subjectSelect.value === demoTutoria.materiaCode) {
            // Comprobamos hora: colocamos la tarjeta en la fila correspondiente
            const hourOfDemo = parseInt(demoTutoria.hora.split(':',1)[0], 10); // 11
            if (h === hourOfDemo) {
              // construir contenido con solo los datos solicitados
              td.innerHTML = `
                <div style="font-weight:700;margin-bottom:6px">${escapeHtml(demoTutoria.materiaName)}</div>
                <div style="font-size:13px;margin-bottom:6px"><strong>Hora:</strong> ${escapeHtml(demoTutoria.hora)}</div>
                <div style="font-size:13px;margin-bottom:6px"><strong>Modalidad:</strong> ${escapeHtml(demoTutoria.modalidad)}</div>
                <div style="font-size:13px;margin-bottom:8px"><strong>Cupos:</strong> ${demoTutoria.cupos}</div>
                <div style="font-size:13px;margin-bottom:8px;color:#444">${escapeHtml(demoTutoria.descripcion)}</div>
              `;

              const btn = document.createElement('button');
              btn.type = 'button';
              btn.textContent = 'Reservar';
              btn.style.marginTop = '6px';
              btn.style.padding = '6px 8px';
              btn.style.borderRadius = '8px';
              btn.style.border = 'none';
              btn.style.backgroundColor = '#0f4db6';
              btn.style.color = '#fff';
              btn.style.cursor = 'pointer';
              btn.addEventListener('click', (ev) => {
                ev.stopPropagation();
                // Acción al reservar: alert y marcar visualmente
                alert('Tutoría reservada con éxito ✅');
                td.classList.add('reserved');
                // opcional: deshabilitar el botón para evitar doble reserva visual
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.textContent = 'Reservado';
              });

              td.appendChild(btn);
            }
          }
        }

        // toggle visual al click (si la celda no tiene reserva ya creada)
        td.addEventListener('click', ()=> {
          // si ya contiene la tarjeta de la tutoría demo, no toggle simple (evitamos borrar contenido)
          if (td.querySelector('button') && td.querySelector('div')) {
            // dejamos que el botón maneje la acción
            return;
          }
          td.classList.toggle('reserved');
        });

        tr.appendChild(td);
      });

      tbody.appendChild(tr);
    }
    scheduleTable.appendChild(tbody);
  }

  function updatePrettyDate() {
    // Mostramos la semana (lunes - sábado) basada en la fecha
    const monday = getMonday(datePicker.value);
    const saturday = addDays(monday, 5);
    const pretty = monday.toLocaleDateString('es-ES', { day:'numeric', month:'short' }) + ' — ' +
                   saturday.toLocaleDateString('es-ES', { day:'numeric', month:'short', year:'numeric' });
    prettyDate.textContent = `Semana: ${pretty}` + (subjectSelect.value ? ` — Materia: ${subjectSelect.value}` : '');
  }

  // Eventos
  prevBtn.addEventListener('click', ()=>{
    const d = new Date(datePicker.value);
    d.setDate(d.getDate() - 7); // retrocede 7 días (una semana)
    datePicker.value = d.toISOString().slice(0,10);
    buildTable();
    updatePrettyDate();
  });
  nextBtn.addEventListener('click', ()=>{
    const d = new Date(datePicker.value);
    d.setDate(d.getDate() + 7); // avanza 7 días (una semana)
    datePicker.value = d.toISOString().slice(0,10);
    buildTable();
    updatePrettyDate();
  });
  datePicker.addEventListener('change', ()=>{
    buildTable();
    updatePrettyDate();
  });
  subjectSelect.addEventListener('change', ()=>{
    // Recontruimos tabla para que aparezca/oculte la demo según selección
    buildTable();
    updatePrettyDate();
  });

  // Inicialización
  populateSubjects();
  buildTable();
  updatePrettyDate();

  // Utilidades
  function escapeHtml(text){
    if(!text) return '';
    return String(text).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
  }

})();
