<script>
  // ===================== HIASAN RAK BUKU =====================
  (function () {
    const shelf = document.getElementById('shelf');
    const host = document.getElementById('books');
    if (!shelf || !host) return;

    const palette = ['#1F2A5C', '#3D6BFF', '#1E9E8F', '#F2B134', '#E8604C', '#7A4DA8', '#8DB580', '#2F56E0', '#B8C4EE'];

    // Pengacak tetap (seed), supaya susunan buku sama setiap kali halaman dibuka
    function rng(seed) {
      return function () {
        seed = (seed * 1664525 + 1013904223) % 4294967296;
        return seed / 4294967296;
      };
    }

    function build() {
      host.innerHTML = '';
      const rand = rng(11);
      const width = host.clientWidth || window.innerWidth;
      const frag = document.createDocumentFragment();
      let x = 6, i = 0;

      while (x < width) {
        // sesekali ada celah kosong di rak
        if (i > 0 && rand() > 0.92) {
          const gap = document.createElement('span');
          gap.className = 'gap';
          const gw = 14 + Math.floor(rand() * 26);
          gap.style.width = gw + 'px';
          frag.appendChild(gap);
          x += gw;
        }

        const w = 22 + Math.floor(rand() * 20);
        const h = 48 + Math.floor(rand() * 46);
        const c = palette[Math.floor(rand() * palette.length)];
        const book = document.createElement('span');
        book.className = 'book';
        book.style.cssText = 'width:' + w + 'px;height:' + h + '%;background:' + c + ';--i:' + i;

        // satu dari sepuluh buku bersandar miring
        if (rand() > 0.9) book.style.setProperty('--tilt', '-8deg');

        frag.appendChild(book);
        x += w + 2;
        i++;
      }
      host.appendChild(frag);
    }

    build();
    // setelah animasi masuk selesai, jangan diulang ketika ukuran jendela berubah
    setTimeout(function () { shelf.classList.add('settled'); }, 2200);

    let t;
    window.addEventListener('resize', function () {
      clearTimeout(t);
      t = setTimeout(function () { shelf.classList.add('settled'); build(); }, 150);
    });
  })();
</script>