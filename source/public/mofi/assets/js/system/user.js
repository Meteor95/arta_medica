let formValidasi = $("#form_pendaftaran");isedit = false;let user_id_pengguna = "";
$(document).ready(function(){
    $('#tanggal_lahir, #tanggal_diterima').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto",
        templates: {
            leftArrow: '<i class="fa fa-chevron-left"></i>',
            rightArrow: '<i class="fa fa-chevron-right"></i>'
        }
    });
    datatable_penggunaaplikasi(); 
    $.get('/generate-csrf-token', function(response) {
        $('#select2_hak_akses').select2({ 
            dropdownParent: $('#modalTambahPengguna') ,
            placeholder: 'Pilih Role Yang Sesuai',
            ajax: {
                url: baseurlapi + '/role/daftarrole',
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token_ajax') },
                method: 'GET',
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        _token : response.csrf_token,
                        parameter_pencarian : (typeof params.term === "undefined" ? "" : params.term),
                        start : 0,
                        length : 1000,
                    }
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: `Berikan Hak Akses : ${item.name}`,
                                id: item.id,
                            }
                        })
                    }
                    
                },
                error: function(xhr, status, error) {
                    
                }
            },
        }); 
    });
});
function datatable_penggunaaplikasi(){
    $.get('/generate-csrf-token', function(response) {
        $('#datatables_penggunaaplikasi').DataTable({
            dom: 'lfrtip',
            searching: false,
            lengthChange: false,
            ordering: false,
            language: {
                "paginate": {
                    "first": '<i class="fa fa-angle-double-left"></i>',
                    "last": '<i class="fa fa-angle-double-right"></i>',
                    "next": '<i class="fa fa-angle-right"></i>',
                    "previous": '<i class="fa fa-angle-left"></i>',
                },
            },
            scrollCollapse: true,
            scrollX: true,
            bFilter: false,
            bInfo: true,
            ordering: false,
            bPaginate: true,
            bProcessing: true,
            serverSide: true,
            ajax: {
                url: baseurlapi + '/pengguna/daftarpengguna',
                type: "GET",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + localStorage.getItem('token_ajax'));
                },
                data: function(d) {
                    d._token = response.csrf_token;
                    d.parameter_pencarian = $('#kotak_pencarian_penggunaaplikasi').val();
                    d.start = 0;
                    d.length = 100;
                },
                dataSrc: function(json) {
                    let detailData = json.data;
                    let mergedData = detailData.map(item => {
                        return {
                            ...item,
                            recordsFiltered: json.recordsFiltered,
                        };
                    });
                    return mergedData;
                }
            },
            infoCallback: function(settings) {
                if (typeof settings.json !== "undefined") {
                    const currentPage = Math.floor(settings._iDisplayStart / settings._iDisplayLength) + 1;
                    const recordsFiltered = settings.json.recordsFiltered;
                    const infoString = 'Halaman ke: ' + currentPage + ' Ditampilkan: ' + 100 + ' Jumlah Data: ' + recordsFiltered + ' data';
                    return infoString;
                }
            },
            pagingType: "full_numbers",
            columnDefs: [{
                defaultContent: "-",
                targets: "_all"
            }],
            columns: [
                {
                    title: "ID Pengguna",
                    render: function(data, type, row, meta) {
                        return `Nama Pengguna: ${row.username}<br>UUID: ${row.uuid}<br>Email: ${row.email}<br>Status: <span class="badge ${row.status_pegawai === 'Tetap' ? 'bg-success' : row.status_pegawai === 'Non Aktif' ? 'bg-warning' : 'bg-danger'}">${row.status_pegawai}</span><br>Hak Akses: <span class="badge bg-warning">${row.role_name === null ? 'Super Admin' : row.role_name}</span>`;
                    }
                },
                {
                    title: "Informasi Pegawai",
                    render: function(data, type, row, meta) {
                        return `NIP: ${row.nip}<br>Nama Pegawai: ${row.nama_pegawai}<br>Jabatan: ${row.jabatan} (${row.departemen})<br>Jenis Kelamin: ${row.jenis_kelamin}<br>Tanggal Lahir: ${new Date(row.tanggal_lahir).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}).split('/').join('-')}`;
                    }
                },
                {
                    title: "Informasi Kontak",
                    render: function(data, type, row, meta) {
                        return `Alamat: ${row.alamat}<br>No Telepon: ${row.no_telepon}<br>Tanggal Bergabung: ${new Date(row.tanggal_bergabung).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}).split('/').join('-')}`;
                    }
                },
                {
                    title: "Aksi",
                    render: function(data, type, row, meta) {
                        if (type === 'display') {
                            return "<div class=\"d-flex justify-content-between gap-2\"><button class=\"btn btn-primary w-100\" onclick=\"editpengguna('" + row.id + "','" + row.username + "')\"><i class=\"fa fa-edit\"></i> Edit Pengguna</button><button class=\"btn btn-danger w-100\" onclick=\"hapuspengguna('" + row.id + "','" + row.username + "')\"><i class=\"fa fa-trash-o\"></i> Hapus Pengguna</button></div>";
                        }
                        return data;
                    }
                },
            ]
        }); 
    });
}
$('#kotak_pencarian_penggunaaplikasi').on('keyup', debounce(function() {
    $('#datatables_penggunaaplikasi').DataTable().ajax.reload();
}, 500));
$('#tambah_penggunaaplikasi').on('click', function() {
    isedit = false;
    $('#modalTambahPengguna').modal('show');
});
$('#toogleshowpassword').on('click', function() {
    var passwordInput = $('#katasandi');
    var passwordIcon = $('#toogleshowpassword i');
    
    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordInput.attr('type', 'password');
        passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
$('#btnSimpanPengguna').on('click', function(event) {
    event.preventDefault();
    formValidasi.addClass('was-validated');
    if ($("#floatingInputValue").val() == "" || (!isedit && $("#katasandi").val() == "") || $("#select2_hak_akses").val() == "") {
        return createToast('Kesalahan Formulir', 'top-right', 'Silahkan isi semua field pada tab Kredensial terlebih dahulu.', 'error', 3000);
    }
    if ($("#nama_pegawai").val() == "" || $("#nip").val() == "" || $("#jabatan").val() == "" || 
        $("#departemen").val() == "" || $("#tanggal_lahir").val() == "" || $("#tanggal_diterima").val() == "" ||
        $("#jenis_kelamin").val() == "" || $("#alamat").val() == "" || $("#no_telepon").val() == "" || 
        $("#status_pegawai").val() == "") {
        return createToast('Kesalahan Formulir', 'top-right', 'Silahkan isi semua field pada tab Profil terlebih dahulu.', 'error', 3000);
    }
    $.get('/generate-csrf-token', function(response) {
        if (isedit) {
            $("#katasandi").attr('required', false);
            $("#katasandi").removeAttr('minlength');
        }
        $.ajax({
            url: baseurlapi + '/pengguna/' + (isedit ? 'editpengguna' : 'tambahpengguna'),
            method: 'POST',
            data: {
                _token: response.csrf_token,
                id_pengguna: user_id_pengguna,
                username: $("#namapengguna").val(),
                password: $("#katasandi").val(),
                email: $("#email").val(),
                idhakakses: $("#select2_hak_akses").val(),
                nama_pegawai: $("#nama_pegawai").val(),
                nip: $("#nip").val(),
                jabatan: $("#jabatan").val(),
                departemen: $("#departemen").val(),
                tanggal_lahir: $("#tanggal_lahir").val(),
                tanggal_diterima: $("#tanggal_diterima").val(),
                jenis_kelamin: $("#jenis_kelamin").val(),
                alamat: $("#alamat").val(),
                no_telepon: $("#no_telepon").val(),
                status_pegawai: $("#status_pegawai").val(),
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + localStorage.getItem('token_ajax'));
            },
            success: function(response) {
                createToast('Sukses', 'top-right', response.message, 'success', 3000);
                clearformeditpengguna();
                $("#modalTambahPengguna").modal('hide');
                $("#datatables_penggunaaplikasi").DataTable().ajax.reload();
            },
            error: function(xhr, status, error) {
                createToast('Error', 'top-right', xhr.responseJSON.message, 'error', 3000);
            }
        });
    });
});
function hapuspengguna(id, username){
    if(id == "") {
        return createToast('Kesalahan Formulir','top-right', 'Silahkan tentukan ID Pengguna untuk melakukan penghapusan pengguna.', 'error', 3000);
    }  
    isedit = false;
    Swal.fire({ 
        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon><div class="pt-2 fs-15"><h4>Konfirmasi Hapus Pengguna '+username+'</h4><p class="text-muted mx-4 mb-0">Setelah data pengguna ini dihapus, pengguna ini tidak akan dapat mengakses sistem ini dan semua informasi yang terkait dengan pengguna ini akan hilang dari sistem.</p></div></div>',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: 'orange',
        confirmButtonText: 'Hapus Pengguna',
        cancelButtonText: 'Nanti Dulu!!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.get('/generate-csrf-token', function(response) {
                $.ajax({
                    url: baseurlapi + '/pengguna/hapuspengguna',
                    method: 'GET',
                    data: {
                        _token: response.csrf_token,
                        id: id,
                        username: username,
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer " + localStorage.getItem('token_ajax'));
                    },
                    success: function(response) {
                        createToast('Sukses', 'top-right', response.message, 'success', 3000);
                        $("#datatables_penggunaaplikasi").DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        createToast('Error', 'top-right', xhr.responseJSON.message, 'error', 3000);
                    }
                });
            });
        }
    });
}
function editpengguna(id, username){
    $.get('/generate-csrf-token', function(response) {
        $.ajax({
            beforeSend: function(xhr) {
                xhr.setRequestHeader("Authorization", "Bearer " + localStorage.getItem('token_ajax'));
            },
            url: baseurlapi + '/pengguna/detailpengguna',
            method: 'GET',
            data: {
                _token: response.csrf_token,
                id: id,
                username: username,
            },
            success: function(response) {
                isedit = true;
                user_id_pengguna = response.data.id;
                $('#namapengguna').val(response.data.username);
                $('#email').val(response.data.email);
                $('#katasandi').val('').attr('placeholder', 'Kosongkan jika tidak ingin mengubah kata sandi');
                let newOption = new Option(response.data.role_name || 'Super Admin', response.data.id_role || 1, true, false);
                $('#select2_hak_akses').append(newOption).trigger('change');
                $("#select2_hak_akses").val(response.data.id_role || 1).trigger('change');
                $('#nama_pegawai').val(response.data.nama_pegawai);
                $('#nip').val(response.data.nip);
                $('#jabatan').val(response.data.jabatan);
                $('#departemen').val(response.data.departemen);
                $('#tanggal_lahir').val(response.data.tanggal_lahir.split('-').reverse().join('-'));
                $('#tanggal_diterima').val(response.data.tanggal_bergabung.split('-').reverse().join('-'));
                $('#jenis_kelamin').val(response.data.jenis_kelamin);
                $('#alamat').val(response.data.alamat);
                $('#no_telepon').val(response.data.no_telepon);
                $('#status_pegawai').val(response.data.status_pegawai);
                $('#modalTambahPengguna').modal('show');
            },
            error: function(xhr, status, error) {
                createToast('Error', 'top-right', xhr.responseJSON.message, 'error', 3000);
            }
        });
    });
}
function clearformeditpengguna(){
    formValidasi.removeClass('was-validated');
    const fields = ['namapengguna', 'email', 'katasandi', 'nama_pegawai', 'nip', 'jabatan', 
                   'departemen', 'tanggal_lahir', 'tanggal_diterima', 'jenis_kelamin', 
                   'alamat', 'no_telepon', 'status_pegawai'];
    fields.forEach(field => $(`#${field}`).val(''));
    $('#select2_hak_akses').val(null).trigger('change');
    $('#j-pills-web-designer-tab').tab('show');
}