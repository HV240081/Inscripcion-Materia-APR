// Traer tutoría seleccionada
const tutoriaID = localStorage.getItem('selectedTutoria');
const tutoria = TUTORIAS.find(t => t.id === tutoriaID);

if (tutoria) {
  const materia = SUBJECTS.find(s => s.code === tutoria.materia).name;
  const docente = DOCENTES.find(d => d.uid === tutoria.docente).nombre;

  document.getElementById('res-subject').textContent = `${materia} (${tutoria.materia})`;
  document.getElementById('res-tutor').textContent = docente;
  document.getElementById('res-date').textContent = tutoria.fecha;
  document.getElementById('res-slots').textContent = 5 - tutoria.inscritos.length; // cupos simulados
}

// Botones
const btnConfirm = document.getElementById('btnConfirm');
const btnCancel = document.getElementById('btnCancel');

btnConfirm.addEventListener('click', () => {
  // Simulando estudiante actual
  const estudianteUID = 'DP240093';
  if (!tutoria.inscritos.includes(estudianteUID)) {
    tutoria.inscritos.push(estudianteUID);
    alert('¡Cupo reservado con éxito!');
  } else {
    alert('Ya estás inscrito en esta tutoría.');
  }
});

btnCancel.addEventListener('click', () => {
  window.history.back();
});
