// Inisialisasi peta
var map = L.map('map').setView([-7.961357, 112.617876], 17);

// Tambahkan tile layer
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 20,
  minZoom: 16,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Variabel penampung marker sementara
var tempMarker = null;

// Load data GeoJSON garis jalan
fetch('jalan.geojson')
  .then(response => response.json())
  .then(data => {
    L.geoJSON(data, {
      style: function (feature) {
        return {
          color: 'red',
          weight: 0.75,
          fillOpacity: 0.1
        };
      }
    }).addTo(map);
  });

// Fungsi untuk mengisi daftar gedung
function populateBuildingList() {
  const listContainer = document.getElementById('building-list');
  listContainer.innerHTML = '';

  for (const key in buildings) {
    const item = document.createElement('div');
    item.className = 'building-item';
    item.innerHTML = `<strong>${key}</strong> - ${buildings[key].info.split('<br>')[0].replace('<b>', '').replace('</b>', '')}`;

    item.onclick = function () {
      if (tempMarker) {
        map.removeLayer(tempMarker);
      }
      map.setView(buildings[key].coords, 18);
      tempMarker = L.marker(buildings[key].coords).addTo(map)
        .bindPopup(buildings[key].info)
        .openPopup();
    };

    listContainer.appendChild(item);
  }
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded',  () => {
  populateBuildingList();
});

// Fungsi Sidebar
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebar-toggle');
  const searchBox = document.getElementById('search-container');

  sidebar.classList.toggle('hidden');

  if (sidebar.classList.contains('hidden')) {
    toggleBtn.style.left = '60px'; // Ujung navbar
    toggleBtn.innerHTML = '❯';
  } else {
    toggleBtn.style.left = '310px'; // 60 (navbar) + 250 (sidebar)
    toggleBtn.innerHTML = '❮';
  }
}

// Fungsi pencarian gedung
function searchBuilding() {
  const input = document.getElementById("search-input").value.toUpperCase();
  if (buildings[input]) {
    if (tempMarker) {
      map.removeLayer(tempMarker);
    }

    const building = buildings[input];
    map.setView(building.coords, 18);
    tempMarker = L.marker(building.coords).addTo(map)
      .bindPopup(building.info)
      .openPopup();
  } else {
    alert("Gedung tidak ditemukan!");
  }
}
