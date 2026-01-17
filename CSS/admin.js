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
  const maxCrops = 3;

  const imageInputMain = document.getElementById("imageInputMain");
  const image = document.getElementById("cropperImage");
  const previewGrid = document.getElementById("previewGrid");

  if (!imageInputMain || !image || !previewGrid) return;

  imageInputMain.addEventListener("change", e => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
      image.src = reader.result;
      galleryCropper?.destroy();
      galleryCropper = new Cropper(image, {
        viewMode: 1,
        autoCropArea: 1
      });
    };
    reader.readAsDataURL(file);
  });

  document.querySelectorAll(".aspect-ratio-controls button").forEach(btn => {
    btn.addEventListener("click", () => {
      if (!galleryCropper) return;
      const ratio = btn.dataset.ratio;
      if (ratio === 'free') {
        galleryCropper.setAspectRatio(NaN);
      } else {
        const [w, h] = ratio.split('/').map(Number);
        galleryCropper.setAspectRatio(w / h);
      }
    });
  });

  document.getElementById("cropAdd")?.addEventListener("click", () => {
    if (!galleryCropper || crops.length >= maxCrops) return;

    const canvas = galleryCropper.getCroppedCanvas({ imageSmoothingQuality: "high" });
    const dataUrl = canvas.toDataURL("image/jpeg", 0.9);
    crops.push(dataUrl);
    updatePreviews();
  });

  document.getElementById("cropReset")?.addEventListener("click", () => {
    crops = [];
    previewGrid.innerHTML = "";
    for (let i = 1; i <= 3; i++) {
      const input = document.getElementById(`crop${i}`);
      if (input) input.value = "";
    }
  });

  function updatePreviews() {
    previewGrid.innerHTML = "";
    crops.forEach((crop, i) => {
      const img = document.createElement("img");
      img.src = crop;
      previewGrid.appendChild(img);
      const input = document.getElementById(`crop${i + 1}`);
      if (input) input.value = crop;
    });
  }
})();

/* ===============================
   EDITOR QUILL
================================ */
// ====== REGISTROS NECESARIOS ======
const Font = Quill.import('formats/font');
Font.whitelist = ['arial', 'times', 'roboto', 'courier'];
Quill.register(Font, true);

const Size = Quill.import('formats/size');
Size.whitelist = ['small', false, 'large', 'huge'];
Quill.register(Size, true);
Quill.register('modules/imageResize', ImageResize);
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
      modules: ['Resize', 'DisplaySize']
    }
  }
});
/* ===============================
   CROP IMAGEN — EDITOR QUILL
================================ */
let editorCropper = null;

const inputEditor = document.getElementById('imageInputEditor');
const cropModal = document.getElementById('cropModal');
const cropImg = document.getElementById('cropImageEditor');
const aspectSelect = document.getElementById('cropAspectEditor');

if (inputEditor && cropModal && cropImg && aspectSelect) {

  inputEditor.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
      cropImg.src = reader.result;
      cropModal.style.display = 'flex'; // ✅ CLAVE

      editorCropper?.destroy();
      editorCropper = new Cropper(cropImg, {
        viewMode: 1,
        autoCropArea: 1
      });
    };
    reader.readAsDataURL(file);

    this.value = '';
  });

  aspectSelect.addEventListener('change', () => {
    if (!editorCropper) return;

    const val = aspectSelect.value;
    if (val === 'free') {
      editorCropper.setAspectRatio(NaN);
    } else {
      const [w, h] = val.split('/').map(Number);
      editorCropper.setAspectRatio(w / h);
    }
  });

  document.getElementById('cropConfirmEditor')?.addEventListener('click', () => {
    if (!editorCropper) return;

    const canvas = editorCropper.getCroppedCanvas({
      imageSmoothingQuality: 'high'
    });

    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    const range = quill.getSelection(true);

    quill.insertEmbed(range.index, 'image', dataUrl);
    quill.setSelection(range.index + 1);

    editorCropper.destroy();
    editorCropper = null;
    cropModal.style.display = 'none';
  });

  document.getElementById('cropCancelEditor')?.addEventListener('click', () => {
    editorCropper?.destroy();
    editorCropper = null;
    cropModal.style.display = 'none';
  });

} else {
  console.error('Elementos del crop del editor NO encontrados');
}

// ====== HANDLER DE IMÁGENES (PREPARADO PARA CROP) ======
function imageHandler() {
  const input = document.getElementById('imageInputEditor');
  if (!input) {
    console.error('imageInputEditor NO existe');
    return;
  }
  input.click();
}

/* ===============================
   PROGRAMACIÓN DE PUBLICACIÓN
================================ */
(() => {
  const estadoSelect = document.getElementById('estadoPublicacion');
  const programacionBox = document.getElementById('programacionBox');
  const fechaInput = document.getElementById('fechaPublicacion');
  if (!estadoSelect || !programacionBox || !fechaInput) return;

  estadoSelect.addEventListener('change', () => {
    if (estadoSelect.value === 'programado') {
      programacionBox.style.display = 'block';
      fechaInput.required = true;
    } else {
      programacionBox.style.display = 'none';
      fechaInput.required = false;
      fechaInput.value = '';
    }
  });
})();
/* verifica el contenido del editor antes de enviar el formulario */
const form = document.getElementById('formPublicacion');
const contenidoInput = document.getElementById('contenido');

if (form && contenidoInput) {
  form.addEventListener('submit', () => {
    contenidoInput.value = quill.root.innerHTML;
  });
}
