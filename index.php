<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_top">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Case 4 - Kelompok 11</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        #map {
            width: 100%;
            height: 80%;
        }
    </style>

    
</head>
<body>

<div id="map"></div>

<!-- Modal untuk konfirmasi penghapusan -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Penghapusan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus lokasi ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk input data POI -->
<div class="modal fade" id="poiModal" tabindex="-1" role="dialog" aria-labelledby="poiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="poiModalLabel">Tambah Informasi Lokasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="poiForm">
          <div class="form-group">
            <label for="lokasi">Lokasi</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi">
          </div>
          <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <input type="text" class="form-control" id="deskripsi" name="deskripsi">
          </div>
    <!-- Hidden input fields for latitude, longitude, and id -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <input type="hidden" id="id" name="id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="savePOI">Simpan perubahan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
$(document).ready(function(){
    var map = L.map('map').setView([-7.952593, 112.613935], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var marker;

    // Event saat map diklik
    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng, { draggable: true }).addTo(map);
        $('#latitude').val(e.latlng.lat);
        $('#longitude').val(e.latlng.lng);
        $('#poiModal').modal('show');
    });

    // Event saat marker dipindahkan
    map.on('moveend', function(event){
        var markerPos = marker.getLatLng();
        $('#latitude').val(markerPos.lat);
        $('#longitude').val(markerPos.lng);
    });

    // Event untuk menyimpan data POI baru
    $('#savePOI').click(function(){
        var lokasi = $('#lokasi').val();
        var deskripsi = $('#deskripsi').val();
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();

        $.ajax({
            url: "create.php",
            type: "POST",
            data: {lokasi: lokasi, deskripsi: deskripsi, latitude: latitude, longitude: longitude},
            success: function(response) {
                alert(response);
                $('#poiModal').modal('hide');
            }
        });
    });

    // Event untuk menghapus marker dan data POI saat kanan mouse ditekan pada marker
    map.on('contextmenu', function(event) {
        $('#deleteConfirmationModal').modal('show');

        $('#confirmDeleteBtn').click(function() {
            var latitude = marker.getLatLng().lat;
            var longitude = marker.getLatLng().lng;

            $.ajax({
                url: "delete.php",
                type: "POST",
                data: {latitude: latitude, longitude: longitude},
                success: function(response) {
                    alert(response);
                    map.removeLayer(marker);
                }
            });
        });
    });

    // Load existing POIs from database
    $.ajax({
        url: "read.php",
        type: "GET",
        dataType: "json",
        success: function(data) {
            data.forEach(function(poi) {
                var marker = L.marker([poi.Latitude, poi.Longitude], { draggable: true }).addTo(map)
                    .bindPopup('<b>' + poi.Lokasi + '</b><br />' + poi.Deskripsi + '<br/><button type="button" class="btn btn-primary btn-sm" onclick="editMarker(' + poi.Latitude + ',' + poi.Longitude + ')">Edit</button>&nbsp;<button type="button" class="btn btn-danger btn-sm" onclick="deleteMarker(' + poi.Latitude + ',' + poi.Longitude + ')">Delete</button>');

                // Event saat marker dipindahkan
                marker.on('moveend', function(event){
                    var markerPos = marker.getLatLng();
                    var latitude = markerPos.lat;
                    var longitude = markerPos.lng;

                    $.ajax({
                        url: "update.php",
                        type: "POST",
                        data: {latitude: latitude, longitude: longitude},
                        success: function(response) {
                            alert(response);
                        }
                    });
                });
            });
        }
    });

});

function editMarker(latitude, longitude) {
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
    $('#poiModal').modal('show');
}

function deleteMarker(latitude, longitude) {
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
    $('#deleteConfirmationModal').modal('show');

    $('#confirmDeleteBtn').off('click').on('click', function() {
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();

        $.ajax({
            url: "delete.php",
            type: "POST",
            data: {latitude: latitude, longitude: longitude},
            success: function(response) {
                alert(response);
                location.reload(); // Refresh halaman setelah penghapusan berhasil
            }
        });
    });
}
</script>
</body>
</html>
