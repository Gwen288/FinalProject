const overlay = document.getElementById('lightboxOverlay');
const lightboxImage = document.getElementById('lightboxImage');
const closeBtn = document.getElementById('lightboxClose');

document.querySelectorAll('.cv-attachment').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const id = link.dataset.attachmentId;
    const filePath = `../php/get_attachment.php?id=${id}`;
    lightboxImage.src = filePath;
    overlay.style.display = 'flex';
  });
});

closeBtn.addEventListener('click', () => overlay.style.display = 'none');
overlay.addEventListener('click', e => {
  if (e.target === overlay) overlay.style.display = 'none';
});
