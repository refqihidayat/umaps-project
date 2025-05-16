<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <title>UMaps - Peta Digital Universitas Negeri Malang</title>
    <style>
      body {
        padding: 0;
        margin: 0;
      }
      #map { 
        height: 100vh; 
        width: 100%;
      }
      .search-container {
        position: absolute;
        top: 80px;
        left: 320px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        width: 300px;
      }
      .sidebar {
        position: absolute;
        top: 0;
        left: 0;
        width: 300px;
        height: 100vh;
        background: white;
        z-index: 1000;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        overflow-y: auto;
        padding-top: 70px;
      }
      .building-item {
        padding: 8px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
      }
      .building-item:hover {
        background-color: #f5f5f5;
      }
      .navbar {
        position: relative;
        z-index: 2000;
      }
    </style>
  </head>

  <body>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">
            <img src="https://www.um.ac.id/wp-content/uploads/2021/12/logo-um.png" alt="UM Logo" height="30" class="d-inline-block align-top me-2">
            UMaps
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link active" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Daftar Gedung</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Tentang</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- Sidebar Daftar Gedung -->
      <div class="sidebar">
        <div class="p-3">
          <h5>KAMPUS I UNIVERSITAS NEGERI MALANG</h5>
          <hr>
          <div id="building-list">
            <!-- Daftar gedung akan diisi oleh JavaScript -->
          </div>
        </div>
      </div>

      <!-- Search Fitur -->
      <div class="search-container">
        <div class="input-group">
          <input type="text" id="search-input" class="form-control" placeholder="Cari Gedung/Lokasi (contoh: B11)">
          <button class="btn btn-primary" onclick="searchBuilding()">Cari</button>
        </div>
      </div>    

      <!-- Map -->
      <div id="map"></div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

      <script>
          // Inisialisasi peta
          var map = L.map('map').setView([-7.961357, 112.617876], 17);
          L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', 
          {
              maxZoom: 20,
              minZoom: 16,
              attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          }).addTo(map);

          // contoh data geojson
          var geojson = 
          {
            "type": "FeatureCollection",
            "features": [
              {
                "type": "Feature",
                "properties": {},
                "geometry": {
                  "coordinates": [
                    [
                      112.6208292926438,
                      -7.964299355934742
                    ],
                    [
                      112.62038093529543,
                      -7.963930289576993
                    ],
                    [
                      112.62023245331574,
                      -7.963817839605284
                    ],
                    [
                      112.61981612149151,
                      -7.9635698728929185
                    ],
                    [
                      112.61967346233536,
                      -7.9635122061933
                    ]
                  ],
                  "type": "LineString"
                }
              }
            ]
          }
          
          // Database gedung lengkap sesuai gambar
          var buildings = {
            "A1": {
              coords: [-7.965054439658924, 112.61762801147361],
              info: "<b>Fakultas Psikologi</b><br>No Data"
            },
            "A2": {
              coords: [-7.965535625342612, 112.61788533809612],
              info: "<b>Bank BNI Cabang Pembantu UM</b><br>No Data"
            },
            "A3": {
              coords: [-7.964477686736595, 112.61818160230813],
              info: "<b>Gedung UKM 2</b><br>No Data"
            },
            "A4": {
              coords: [-7.964648700767665, 112.61793951212385],
              info: "<b>Panggung Trapesium</b><br>No Data"
            },
            "A5": {
              coords: [-7.9648012720488355, 112.61831534443787],
              info: "<b>Sekolah Autis</b><br>No Data"
            },
            "A6": {
              coords: [-7.963922728930959, 112.6180021508443],
              info: "<b>Fakultas Ilmu Sosial</b><br>No Data"
            },
            "A7": {
              coords: [-7.963706446084548, 112.6181240424056],
              info: "<b>Fakultas Ilmu Sosial</b><br>No Data"
            },
            "A8": {
              coords: [-7.963538784884723, 112.61818837406295],
              info: "<b>Fakultas Ilmu Sosial</b><br>No Data"
            },
            "A9": {
              coords: [-7.963366093772602, 112.61829502917807],
              info: "<b>Fakultas Ilmu Sosial</b><br>No Data"
            },
            "A10": {
              coords: [-7.9630978355936035, 112.61846432301239],
              info: "<b>Fakultas Ilmu Sosial</b><br>No Data"
            },
             "A11": {
              coords: [-7.964278371743252, 112.61840472141046],
              info: "<b>Gedung UKM 1</b><br>No Data"
            },
             "A12": {
              coords: [-7.964104696929791, 112.61850443947485],
              info: "<b>Koperasi Mahasiswa (KOPMA)</b><br>No Data"
            },
             "A13": {
              coords: [-7.9637005905321, 112.61881620169602],
              info: "<b>Sasana Budaya</b><br>No Data"
            },
             "A14": {
              coords: [-7.963330537696128, 112.61886778000431],
              info: "<b>Museum Pendidikan</b><br>No Data"
            },
             "A15": {
              coords: [-7.9636899427337555, 112.6192284170801],
              info: "<b>Posko Keamanan</b><br>No Data"
            },
             "A": {
              coords: [-7.962560418803771, 112.6189125968195],
              info: "<b>Graha Rektorat</b><br>No Data"
            },
             "A16": {
              coords: [-7.961929807972241, 112.61913255888099],
              info: "<b>Poliklinik UM</b><br>No Data"
            }, 
             "A17": {
              coords: [-7.961679184991351, 112.61895466718165],
              info: "<b>Green House FMIPA</b><br>No Data"
            }, 
             "A18": {
              coords: [-7.961318196589559, 112.62007888547082],
              info: "<b>Outdoor Learning Space</b><br>No Data"
            }, 
             "A19": {
              coords: [-7.9616479356605625, 112.61980792438503],
              info: "<b>Gedung Kuliah Bersama 1</b><br>No Data"
            }, 
             "A20.": {
              coords: [-7.960892485957043, 112.62035540688329],
              info: "<b>Gedung Kuliah Bersama 2</b><br>No Data"
            }, 
             "A21": {
              coords: [-7.960884373132178, 112.61951154249175],
              info: "<b>Sekolah Pascasarjana</b><br>No Data"
            }, 
             "A22": {
              coords: [-7.960096878212493, 112.62002529612369],
              info: "<b>SMP Labolatorium UM</b><br>No Data"
            }, 
             "A23": {
              coords: [-7.960250706394513, 112.62024525920556],
              info: "<b>SMP Labolatorium UM</b><br>No Data"
            }, 
             "A24": {
              coords: [-7.959836038989251, 112.62021921094588],
              info: "<b>SMP Labolatorium UM</b><br>No Data"
            }, 
             "A25": {
              coords: [-7.959962159074377, 112.62042663227135],
              info: "<b>SMP Labolatorium UM</b><br>No Data"
            },
             "A26": {
              coords: [-7.960143038318388, 112.62071556285562],
              info: "<b>Gedung Probis</b><br>No Data"
            }, 
             "A27": {
              coords: [-7.960632324440032, 112.62081080077843],
              info: "<b>SD Labolatorium UM</b><br>No Data"
            },
             "A28": {
              coords: [-7.960471685386895, 112.62093133627873],
              info: "<b>SD Labolatorium UM</b><br>No Data"
            }, 
             "A29": {
              coords: [-7.960225283298583, 112.62109609501688],
              info: "<b>Gedung Probis</b><br>No Data"
            }, 
             "A30": {
              coords: [-7.9600689051132845, 112.62114713327476],
              info: "<b>Gedung Probis</b><br>No Data"
            },
             "A31": {
              coords: [-7.959904628974017, 112.62071171313734],
              info: "<b>Fakultas Vokasi</b><br>No Data"
            },
             "B1": {
              coords: [-7.964324070307153, 112.61678172842721],
              info: "<b>Gedung Ujian</b><br>No Data"
            }, 
             "B2": {
              coords: [-7.964562473775462, 112.61716158876597],
              info: "<b>Fakultas Kedokteran</b><br>No Data"
            }, 
             "B3": {
              coords: [-7.964216898065127, 112.61653989873484],
              info: "<b>Fakultas Kedokteran</b><br>No Data"
            }, 
             "B4": {
              coords: [-7.963683471751509, 112.617337137567],
              info: "<b>Fakultas Psikologi</b><br>No Data"
            }, 
             "B5": {
              coords: [-7.963308473504927, 112.61687274755056],
              info: "<b>UPT. PTIK UM</b><br>No Data"
            }, 
             "B6": {
              coords: [-7.963394899999102, 112.61743463802891],
              info: "<b>Kuliah Bersama Pascasarjana</b><br>No Data"
            }, 
             "B7": {
              coords: [-7.963139890911504, 112.61758752394402],
              info: "<b>Kuliah Bersama Pascasarjana</b><br>No Data"
            }, 
             "B8": {
              coords: [-7.962911070215016, 112.617076451631],
              info: "<b>Departemen Teknik Mesin dan Industri, Fakultas Teknik</b><br>No Data"
            }, 
             "B9": {
              coords: [-7.962678926443486, 112.61720190721027],
              info: "<b>Departemen Teknik Sipil dan Perencanaan, Fakultas Teknik</b><br>No Data"
            }, 
             "B10": {
              coords: [-7.9628489472461945, 112.61776315584852],
              info: "<b>Departemen Pendidikan Tata Boga dan Busana, Fakultas Teknik</b><br>No Data"
            }, 
             "B11": {
              coords: [-7.962370354112535, 112.61800720876865],
              info: "<b>Gedung Kuliah Bersama, Fakultas Teknik</b><br>No Data"
            }, 
             "B12": {
              coords: [-7.9622259071451555, 112.61744124897096],
              info: "<b>Departemen Teknik Elektro dan Informatika, Fakultas Teknik</b><br>No Data"
            }, 
             "B13": {
              coords: [-7.961796269706778, 112.61768184421477],
              info: "<b>Departemen Teknik Mesin dan Industri, Fakultas Teknik</b><br>No Data"
            }, 
             "B14": {
              coords: [-7.962367884932329, 112.6183014081326],
              info: "<b>KPRI</b><br>No Data"
            }, 
             "B15": {
              coords: [-7.961991335039933, 112.61812563647639],
              info: "<b>Pusat Sumber Belajar</b><br>No Data"
            }, 
             "B16": {
              coords: [-7.961518486639979, 112.61784265657825],
              info: "<b>Departemen Tata Boga dan Busana, Fakultas Teknik</b><br>No Data"
            }, 
             "B17": {
              coords: [-7.961763686984915, 112.6183203959311],
              info: "<b>Lembaga Pengembangan Pendidikan dan Pembelajaran UM</b><br>No Data"
            }, 
             "B18": {
              coords: [-7.961266464486801, 112.61873323416165],
              info: "<b>Labolatorium Bersama, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B19": {
              coords: [-7.960770180255449, 112.61833284857506],
              info: "<b></b>Departemen Kimia, Fakultas Matematika dan IPA<br>No Data"
            }, 
             "B20": {
              coords: [-7.960423916473823, 112.61854645157949],
              info: "<b>Gedung Administrasi Umum, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B21": {
              coords: [-7.960974295791405, 112.61888605461353],
              info: "<b>Departemen Biologi, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B22": {
              coords: [-7.960656784111648, 112.61905273086312],
              info: "<b></b>Departemen Fisika, Fakultas Matematika dan IPA<br>No Data"
            }, 
             "B23": {
              coords: [-7.9599552178435955, 112.61871852706923],
              info: "<b>Gedung Kuliah Bersama, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B24": {
              coords: [-7.960337819396734, 112.61918574822157],
              info: "<b>Departemen Matematika, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B25": {
              coords: [-7.960030557523353, 112.61930965049295],
              info: "<b>Gedung Kuliah dan Kegiatan Mahasiswa, Fakultas Matematika dan IPA</b><br>No Data"
            }, 
             "B26": {
              coords: [-7.959636713656841, 112.61900333611621],
              info: "<b>Penerbit - Percetakan UM</b><br>No Data"
            }, 
             "B27": {
              coords: [-7.959238258228194, 112.61934934107963],
              info: "<b>Garasi UM</b><br>No Data"
            }, 
             "B28": {
              coords: [-7.9586845010641785, 112.6194210165718],
              info: "<b>Asrama Putra Soka UM </b><br>No Data"
            }, 
             "B29": {
              coords: [-7.958850524429063, 112.61973617613366],
              info: "<b>Asrama Putra Soka UM</b><br>No Data"
            }, 
             "B30": {
              coords: [-7.959115497300856, 112.62008572025573],
              info: "<b>UPT. P2LP UM</b><br>No Data"
            }, 
             "B31": {
              coords: [-7.959471875642474, 112.62025917362759],
              info: "<b>UPT. LAB Pancila & UPT. LSP UM</b><br>No Data"
            }, 
             "B32": {
              coords: [-7.959315149821965, 112.62000637542782],
              info: "<b>Aula Kenanga</b><br>No Data"
            }, 
             "B33": {
              coords: [-7.959258086090064, 112.61969760162255],
              info: "<b>Asrama Putra Soka UM</b><br>No Data"
            }, 
             "B34": {
              coords: [-7.95908522204915, 112.61947057048607],
              info: "<b>Asrama Putra Soka UM</b><br>No Data"
            }, 
             "B35": {
              coords: [-7.958838618672641, 112.61940587881928],
              info: "<b>Asrama Putra Soka UM</b><br>No Data"
            }, 
             "B36": {
              coords: [-7.958976426458438, 112.6193179957987],
              info: "<b>Asrama Putra Soka UM</b><br>No Data"
            }, 
             "B37": {
              coords: [],
              info: "<b>Lapangan Badminton</b><br>No Data"
            }, 
             "C1": {
              coords: [-7.963703300400858, 112.61645147908891],
              info: "<b>HOTMA</b><br>No Data"
            }, 
             "C2": {
              coords: [-7.963585823137695, 112.61597228522186],
              info: "<b>Rusunawa Putra Lili UM</b><br>No Data"
            }, 
             "C3": {
              coords: [-7.962195682650317, 112.6165862961568],
              info: "<b>UPT. Perpustakaan UM</b><br>No Data"
            }, 
             "C4": {
              coords: [],
              info: "<b></b><br>No Data"
            }, 
             "C5": {
              coords: [],
              info: "<b></b><br>No Data"
            }, 
             "D7": {
              coords: [],
              info: "<b>Gedung Dekanat Fakultas Ekonomi dan Bisnis</b><br>No Data"
            }, 
             "D8": {
              coords: [],
              info: "<b>Gedung Kuliah Bersama, Fakultas Ekonomi dan Bisnis</b><br>No Data"
            }, 
             "D9": {
              coords: [],
              info: "<b></b>Gedung Kuliah Bersama, Fakultas Ekonomi dan Bisnis<br>No Data"
            }, 
             "D10": {
              coords: [],
              info: "<b>Gedung Kuliah Bersama, Fakultas Ekonomi dan Bisnis</b><br>No Data"
            }, 
             "D11": {
              coords: [],
              info: "<b>Gedung Kuliah Bersama, Fakultas Ekonomi dan Bisnis</b><br>No Data"
            }, 
             "D12": {
              coords: [],
              info: "<b></b><br>No Data"
            }, 
             "D13": {
              coords: [],
              info: "<b>Gedung ORMAWA, Fakultas Ekonomi dan Bisnis</b><br>No Data"
            }, 

             "D": {
              coords: [],
              info: "<b></b><br>No Data"
            }, 

            "STADION": {
              coords: [-7.958, 112.618],
              info: "<b>Stadion</b>"
            }
          };

          // Variabel untuk menyimpan marker sementara
          var tempMarker = null;

          // Fungsi untuk menampilkan daftar gedung di sidebar
          function populateBuildingList() {
            var buildingList = document.getElementById('building-list');
            buildingList.innerHTML = '';
            
            for (var key in buildings) {
              var item = document.createElement('div');
              item.className = 'building-item';
              item.innerHTML = `<strong>${key}</strong> - ${buildings[key].info.split('<br>')[0].replace('<b>','').replace('</b>','')}`;
              
              item.onclick = (function(bldgKey) {
                return function() {
                  // Hapus marker sebelumnya jika ada
                  if (tempMarker) {
                    map.removeLayer(tempMarker);
                  }
                  
                  // Focus ke gedung yang dipilih
                  map.setView(buildings[bldgKey].coords, 18);
                  
                  // Tambahkan marker sementara
                  tempMarker = L.marker(buildings[bldgKey].coords).addTo(map)
                    .bindPopup(buildings[bldgKey].info)
                    .openPopup();
                };
              })(key);
              
              buildingList.appendChild(item);
            }
          }

          // Panggil fungsi untuk mengisi daftar gedung
          populateBuildingList();

          // Fungsi pencarian
          function searchBuilding() {
            var input = document.getElementById("search-input").value.toUpperCase();
            if (buildings[input]) {
              // Hapus marker sebelumnya jika ada
              if (tempMarker) {
                map.removeLayer(tempMarker);
              }
              
              var building = buildings[input];
              map.setView(building.coords, 18);
              
              // Tambahkan marker sementara
              tempMarker = L.marker(building.coords).addTo(map)
                .bindPopup(building.info)
                .openPopup();
            } else {
              alert("Gedung tidak ditemukan! Coba: " + Object.keys(buildings).join(", "));
            }
          }
      </script>
  </body>
</html>