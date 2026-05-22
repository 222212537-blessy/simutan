require('./bootstrap');

import Alpine from 'alpinejs';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swiper from 'swiper';
import 'swiper/css';
import 'swiper/css/bundle';
import Swal from 'sweetalert2';


// Import jQuery dan DataTables
import $ from 'jquery';
import 'datatables.net-dt';

// Inisialisasi Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Inisialisasi DataTables ketika dokumen siap
$(document).ready(function() {
    $('#example').DataTable(); // Ganti '#example' dengan ID tabel Anda
});

$(document).ready(function() {
    var table = $('#datatable').DataTable({
        // Konfigurasi DataTable lainnya
        initComplete: function() {
            // Ubah kelas tombol setelah DataTable diinisialisasi
            this.api().buttons().container().find('.dt-button.buttons-collection').each(function() {
                $(this).removeClass('dt-button buttons-collection').addClass('form-select');
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Dapatkan semua elemen dengan class .fade-in
    const cards = document.querySelectorAll('.fade-in');

    // Tambahkan class 'show' untuk setiap card setelah halaman dimuat
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('show');
        }, index * 200); // Delay antara satu card dan yang lainnya untuk efek bertahap
    });
});




document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah aksi default
            
            const url = this.getAttribute('href'); // Ambil URL dari atribut href

            // Tampilkan konfirmasi SweetAlert2
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, arahkan ke URL penghapusan
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire(
                                'Dihapus!',
                                'Item telah dihapus.',
                                'success'
                            ).then(() => {
                                window.location.reload(); // Refresh halaman setelah penghapusan
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Penghapusan gagal.',
                                'error'
                            );
                        }
                    }).catch(() => {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan.',
                            'error'
                        );
                    });
                }
            });
        });
    });
});

