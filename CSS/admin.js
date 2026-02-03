/*
  Archivo: admin.js
  Propósito: Lógica exclusiva del panel de administración
  Incluye:
  - Previsualización de imágenes
  - Cropper.js (imagen principal, galería y editor Quill)
  - Editor Quill
  - Manejo de estado / programación de publicaciones
  - Validaciones defensivas (no rompe si un elemento no existe)
*/
// Toggle de colapso: busca botones con data-bs-toggle="collapse" y alterna la clase .show
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const targetSelector = btn.getAttribute('data-bs-target');
      if (!targetSelector) return;
      const target = document.querySelector(targetSelector);
      if (!target) return;
      target.classList.toggle('show');
    });
  });

  // Toggle de tema: botones con id 'themeToggle'
  const themeBtns = document.querySelectorAll('#themeToggle');
  function applyTheme(theme) {
    // Aplica el atributo en el elemento <html> para que CSS use las variables
    document.documentElement.setAttribute('data-bs-theme', theme);
    // Actualiza texto de los botones (solo visual) — no usar emojis en comentarios
    themeBtns.forEach(b => b.textContent = theme === 'dark' ? '☀️' : '🌙');
  }
  themeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem('theme', next);
    });
  });
  const saved = localStorage.getItem('theme') || 'light';
  applyTheme(saved);

  // Toggle Sidebar (Mobile)
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });
    
    // Close sidebar when clicking outside (optional but good for UX)
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && 
            sidebar.classList.contains('active') && 
            !sidebar.contains(e.target) && 
            e.target !== sidebarToggle &&
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
  }


/* ===============================
   PREVISUALIZACIÓN DE IMÁGENES
================================ */
(() => {
  const input = document.getElementById('imagenes');
  const preview = document.getElementById('preview');
  if (!input || !preview) return;

  let archivosTemporales;
  try {
    archivosTemporales = new DataTransfer();
  } catch {
    archivosTemporales = { files: [], items: { add(){} } };
  }

  input.addEventListener('change', () => {
    Array.from(input.files).forEach(file => {
      // evitar duplicados
      for (let i = 0; i < archivosTemporales.files.length; i++) {
        if (archivosTemporales.files[i].name === file.name) return;
      }

      archivosTemporales.items.add(file);

      const reader = new FileReader();
      reader.onload = e => {
        const div = document.createElement('div');
        div.classList.add('preview-item');
        div.innerHTML = `<img src="${e.target.result}"><span>${file.name}</span>`;
        preview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });

    input.files = archivosTemporales.files;
    input.name = "imagenes[]";
  });
})();

/* ===============================
   PREVISUALIZACIÓN DE VIDEO
================================ */
(() => {
  const videoInput = document.getElementById('video_url');
  const videoPreview = document.getElementById('videoPreview');
  if (!videoInput || !videoPreview) return;

  videoInput.addEventListener('input', () => {
    const url = videoInput.value.trim();
    videoPreview.innerHTML = '';
    if (!url) return;

    // YouTube
    if (url.includes('youtube.com') || url.includes('youtu.be')) {
      const id = url.includes('youtu.be')
        ? url.split('/').pop()
        : new URL(url).searchParams.get('v');

      if (id) {
        videoPreview.innerHTML =
          `<iframe src="https://www.youtube.com/embed/${id}" allowfullscreen></iframe>`;
      }
      return;
    }

    // Vimeo
    if (url.includes('vimeo.com')) {
      const id = url.split('/').pop();
      videoPreview.innerHTML =
        `<iframe src="https://player.vimeo.com/video/${id}" allowfullscreen></iframe>`;
      return;
    }

    // Video directo
    if (url.match(/\.(mp4|webm|ogg)$/)) {
      videoPreview.innerHTML =
        `<video controls><source src="${url}"></video>`;
      return;
    }

    videoPreview.innerHTML = `<p class="error">No se pudo previsualizar el video</p>`;
  });
})();

/* ===============================
   CROP IMAGEN PRINCIPAL (GALERÍA)
================================ */
(() => {
  let galleryCropper = null;
  let crops = [];
  let currentStep = 0;

  const cropSteps = [
    { name: 'Original', ratio: NaN },
    { name: 'Banner', ratio: 21 / 6 },
    { name: 'Miniatura', ratio: 16 / 9 }
  ];

  const imageInputMain = document.getElementById("imageInputMain");
  const image = document.getElementById("cropperImage");
  const previewGrid = document.getElementById("previewGrid");

  const btnAdd = document.getElementById("cropAdd");
  const btnReset = document.getElementById("cropReset");
  const btnDelete = document.getElementById("cropDelete");

  if (!imageInputMain || !image || !previewGrid) return;

  /* ===== CARGA IMAGEN ===== */
  imageInputMain.addEventListener("change", e => {
    const file = e.target.files[0];
    if (!file) return;

    resetAll();

    const reader = new FileReader();
    reader.onload = () => {
      image.src = reader.result;

      galleryCropper?.destroy();
      galleryCropper = new Cropper(image, {
        viewMode: 1,
        autoCropArea: 1,
        aspectRatio: cropSteps[0].ratio,
        cropBoxResizable: false,
        dragMode: 'move',
        responsive: true,
        guides: true,
        background: false
      });
    };
    reader.readAsDataURL(file);
  });

  /* ===== AÑADIR RECORTE ===== */
  btnAdd?.addEventListener("click", () => {
    if (!galleryCropper || currentStep >= cropSteps.length) return;

    const canvas = galleryCropper.getCroppedCanvas({
      imageSmoothingQuality: "high"
    });

    const dataUrl = canvas.toDataURL("image/jpeg", 0.85);
    crops.push(dataUrl);
    updatePreviews();

    currentStep++;

    if (currentStep < cropSteps.length) {
      galleryCropper.setAspectRatio(cropSteps[currentStep].ratio);
    }
  });

  /* ===== RESET TOTAL ===== */
  btnReset?.addEventListener("click", resetAll);

  /* ===== DESHACER ÚLTIMO ===== */
  btnDelete?.addEventListener("click", () => {
    if (crops.length === 0) return;

    crops.pop();
    currentStep = Math.max(0, currentStep - 1);

    galleryCropper.setAspectRatio(cropSteps[currentStep].ratio);
    updatePreviews();
  });

  /* ===== HELPERS ===== */
  function updatePreviews() {
    previewGrid.innerHTML = "";

    for (let i = 0; i < 3; i++) {
      const input = document.getElementById(`crop${i + 1}`);
      if (input) input.value = "";
    }

    crops.forEach((crop, i) => {
      const img = document.createElement("img");
      img.src = crop;
      previewGrid.appendChild(img);

      const input = document.getElementById(`crop${i + 1}`);
      if (input) input.value = crop;
    });
  }

  function resetAll() {
    crops = [];
    currentStep = 0;
    previewGrid.innerHTML = "";

    for (let i = 1; i <= 3; i++) {
      const input = document.getElementById(`crop${i}`);
      if (input) input.value = "";
    }

    if (galleryCropper) {
      galleryCropper.setAspectRatio(cropSteps[0].ratio);
      galleryCropper.reset();
    }
  }
})();


/* ===============================
   EDITOR QUILL
================================ */
// ====== REGISTROS NECESARIOS ======
// Importaciones de Quill
const Font = Quill.import('formats/font');
const Size = Quill.import('formats/size');
const ImageResize = Quill.import('modules/imageResize');
const Parchment = Quill.import('parchment');
// Declaraciones
Font.whitelist = ['arial', 'times', 'roboto', 'courier'];
Quill.register(Font, true);
Size.whitelist = ['small', false, 'large', 'huge'];
Quill.register(Size, true);
Quill.register(ImageResize, true);
const LineHeightStyle = new Parchment.Attributor.Style(
  'lineheight',
  'line-height',
  {
    scope: Parchment.Scope.BLOCK,
    whitelist: ['0', '0.85', '1', '1.5', '2', '2.5', '3']
  }
);
Quill.register(LineHeightStyle, true);

// ====== INICIALIZACIÓN ======
const quill = new Quill('#editor', {
  theme: 'snow',
  placeholder: 'Escribe aquí...',
  modules: {
    toolbar: {
      container: '.editor-toolbar',
      handlers: {
        image: imageHandler
      }
    },
    imageResize: {
      modules: [ 'Resize', 'DisplaySize']
    }
  }
});
// ====== CARGAR CONTENIDO EXISTENTE ======
const editorContent = document.getElementById('editorContent');

if (editorContent && editorContent.textContent.trim().length > 0) {
  quill.clipboard.dangerouslyPasteHTML(editorContent.innerHTML);
}

// ====== HANDLER DE IMÁGENES (PREPARADO PARA CROP) ======
function imageHandler() {
  const input = document.createElement('input');
  input.setAttribute('type', 'file');
  input.setAttribute('accept', 'image/*');
  input.click();

  input.onchange = () => {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
      const range = quill.getSelection(true);
      quill.insertEmbed(range.index, 'image', reader.result);
      quill.setSelection(range.index + 1);
    };
    reader.readAsDataURL(file);
  };
}
/* verifica el contenido del editor antes de enviar el formulario */
const form = 
            document.getElementById('formPublicacion')
            ? document.getElementById('formPublicacion')
            : document.getElementById('formEdicion');
const contenidoInput = document.getElementById('contenido');

if (form && contenidoInput) {
  form.addEventListener('submit', () => {
    contenidoInput.value = quill.root.innerHTML;
  });
}
// modal
document.addEventListener("DOMContentLoaded", () => {
    // Abrir modal
    const deleteButtons = document.querySelectorAll(".btn-delete");
    const modalOverlay = document.getElementById("modalOverlay");
    const modalTitle = document.getElementById("modalTitle");
    const modalIdInput = document.getElementById("modalId");

    deleteButtons.forEach(button => {
        button.addEventListener("click", () => {
            const noticiaId = button.dataset.id;
            const noticiaTitulo = button.dataset.titulo;

            modalTitle.textContent = `¿Eliminar la noticia "${noticiaTitulo}"?`;
            modalIdInput.value = noticiaId;

            modalOverlay.style.display = "flex";
        });
    });

    // Cerrar modal
    const closeModalButtons = document.querySelectorAll(".btn-cancel, .modal-overlay");
    closeModalButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            // Evitar cerrar si hace click dentro del modal
            if (e.target === modalOverlay || btn.classList.contains("btn-cancel")) {
                modalOverlay.style.display = "none";
            }
        });
    });

    // Evitar cerrar modal al hacer click dentro del contenido
    const modalContent = document.querySelector(".crop-modal-content");
    if (modalContent) {
        modalContent.addEventListener("click", e => e.stopPropagation());
    }
});
// modal validacion
document.addEventListener("DOMContentLoaded", () => {

    const modalTime = document.getElementById("timeModalOverlay");
    const autoAdjustBtn = document.getElementById("autoAdjustBtn");
    const manualAdjustBtn = document.getElementById("manualAdjustBtn");
    const fechaInput = document.getElementsByName("fecha_publicacion")[0];
    const guardarNoticiaBtns = document.getElementsByName("guardarNoticia");
    const modalForm = document.getElementById("formPublicacion");
    
    function getLocalDatetimeString(date = new Date()) {
      const offset = date.getTimezoneOffset();
      const local = new Date(date.getTime() - offset * 60000);
      return local.toISOString().slice(0,16);
    } 
    guardarNoticiaBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            const ahora = getLocalDatetimeString();
            const fechaSeleccionada = fechaInput.value;

            if (fechaSeleccionada < ahora) {
                modalTime.style.display = "flex";
            } else {
                modalForm.requestSubmit();
            }
        });
    });

    autoAdjustBtn.addEventListener("click", () => {
        fechaInput.value = getLocalDatetimeString();
        modalTime.style.display = "none";
        modalForm.requestSubmit();
    });

    manualAdjustBtn.addEventListener("click", () => {
        modalTime.style.display = "none";
    });

    // Evitar cerrar modal al hacer click dentro
    const modalContent = document.querySelector(".crop-modal-content");
    modalContent?.addEventListener("click", e => e.stopPropagation());
});
