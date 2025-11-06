// Base de datos simulada
const USUARIOS = [
  { uid: 'DP240093', nombre: 'Salvador Enrique Delgado Peñate', rol: 'estudiante', carnet: 'MV238901', password: 'ElSecretoDelPirata105' },
  { uid: 'HV240081', nombre: 'Cristian Alexander Hernández Valiente', rol: 'estudiante', carnet: 'LS221234', password: 'LasSombrasMalditas106' },
  { uid: 'PG240099', nombre: 'Mario Antonio Pacheco Guerrero', rol: 'estudiante', carnet: 'AB165632', password: 'ElRelojSinFin107' },
  { uid: 'CM240108', nombre: 'Fátima Gisselle Cornejo Melendez', rol: 'estudiante', carnet: 'AB165632', password: 'LaLlamaAzul108' },
  { uid: 'RG240109', nombre: 'Bryan Alexander Realegeño García', rol: 'estudiante', carnet: 'AB165632', password: 'ElCazadorDeDemonios109' },
];

const DOCENTES = [
  { uid: 'AB165632', nombre: 'Mario Alexander Alvarado Bernal', rol: 'docente' },
  { uid: 'QH181812', nombre: 'Aida Lissette Quintanilla Hernández', rol: 'docente' },
  { uid: 'BH128563', nombre: 'Nelson Stanley Belloso Huezo', rol: 'docente' },
  { uid: 'DN059812', nombre: 'Freddy Ernesto Durán Navarro', rol: 'docente' },
  { uid: 'TT179517', nombre: 'René Mauricio Tejada Tobar', rol: 'docente' },
];

const SUBJECTS = [
  { code: 'ALG501', name: 'Álgebra Vectorial y Matrices' },
  { code: 'ANF231', name: 'Antropología Filosófica' },
  { code: 'LME404', name: 'Lenguajes de Marcado y Estilo Web' },
  { code: 'PAL404', name: 'Programación de Algoritmos' },
  { code: 'REC404', name: 'Redes de Comunicación' },
  { code: 'ASB404', name: 'Análisis y Diseño de Sistemas y Base de Datos' },
  { code: 'DAW404', name: 'Desarrollo de Aplic. Web con Soft. Interpret. en el Cliente' },
  { code: 'POO404', name: 'Programación Orientada a Objetos' },
  { code: 'PSC231', name: 'Pensamiento Social Cristiano' },
  { code: 'DWF404', name: 'Desarrollo de Aplicaciones con Web Frameworks' },
  { code: 'SPP404', name: 'Servidores en Plataformas Propietarias' },
  { code: 'SPL404', name: 'Servidores en Plataformas Libres' },
  { code: 'DSP404', name: 'Desarrollo de Aplicaciones con Software Propietario' },
  { code: 'SDR404', name: 'Seguridad de Redes' },
  { code: 'EAI441', name: 'Electrónica Aplicada al Internet de las Cosas' },
  { code: 'APR404', name: 'Administración de Proyectos' },
  { code: 'DSM441', name: 'Desarrollo de Software para Móviles' },
  { code: 'ASN441', name: 'Administración de Servicios en la Nube' },
  { code: 'DPS441', name: 'Diseño y Programación de Software Multiplataforma' },
  { code: 'DSS404', name: 'Desarrollo de Aplic. Web con Soft. Interpret. en el Servidor' },
];

let TUTORIAS = []; // lista vacía al inicio

// --- FUNCIONES ---

function crearTutoria(docenteUID, materiaCode, fecha) {
    const docente = DOCENTES.find(d => d.uid === docenteUID);
    const materia = SUBJECTS.find(m => m.code === materiaCode);

    if (!docente) return console.log("Docente no encontrado");
    if (!materia) return console.log("Materia no encontrada");

    const tutoria = {
        id: `TUT${TUTORIAS.length + 1}`,
        materia: materia.code,
        docente: docenteUID,
        fecha,
        inscritos: []
    };

    TUTORIAS.push(tutoria);
    console.log(`✅ Tutoria creada: ${materia.name} por ${docente.nombre} el ${fecha}`);
}

function inscribirseTutoria(estudianteUID, tutoriaID) {
    const estudiante = USUARIOS.find(u => u.uid === estudianteUID);
    const tutoria = TUTORIAS.find(t => t.id === tutoriaID);

    if (!estudiante) return console.log("Estudiante no encontrado");
    if (!tutoria) return console.log("Tutoria no encontrada");

    if (!tutoria.inscritos.includes(estudianteUID)) {
        tutoria.inscritos.push(estudianteUID);
        console.log(`✅ ${estudiante.nombre} se inscribió a la tutoría de ${SUBJECTS.find(m => m.code === tutoria.materia).name}`);
    } else {
        console.log(`⚠️ ${estudiante.nombre} ya está inscrito en esta tutoría`);
    }
}

function verTutorias(uid) {
    const usuario = [...USUARIOS, ...DOCENTES].find(u => u.uid === uid);
    if (!usuario) return console.log("Usuario no encontrado");

    console.log(`\n📋 Tutorías de ${usuario.nombre}:`);
    TUTORIAS.forEach(t => {
        const materia = SUBJECTS.find(m => m.code === t.materia).name;
        if (usuario.rol === 'docente' && t.docente === uid) {
            console.log(`- [Docente] ${materia} el ${t.fecha} | Inscritos: ${t.inscritos.length}`);
        } 
        if (usuario.rol === 'estudiante' && t.inscritos.includes(uid)) {
            console.log(`- [Estudiante] ${materia} el ${t.fecha}`);
        }
    });
}

// --- SIMULACIÓN ---

crearTutoria('DN059812', 'ALG501', '2025-10-30');
crearTutoria('BH128563', 'SDR404', '2025-11-01');
crearTutoria('QH181812', 'LME404', '2025-11-05');

inscribirseTutoria('DP240093', 'TUT1');
inscribirseTutoria('HV240081', 'TUT1');
inscribirseTutoria('PG240099', 'TUT2');
inscribirseTutoria('CM240108', 'TUT3');
inscribirseTutoria('RG240109', 'TUT3');

verTutorias('DP240093'); // estudiante
verTutorias('BH128563'); // docente
verTutorias('HV240081'); // estudiante
