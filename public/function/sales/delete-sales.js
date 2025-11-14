$(document).on('click', '.btn-delete', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data penjualan dan item terkait akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/sales/${id}/destroy`,
                type: 'DELETE',
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success');
                    loadSales(); // panggil ulang datatable / refresh data
                },
                error: function(err) {
                    Swal.fire('Gagal!', 'Tidak dapat menghapus data.', 'error');
                }
            });
        }
    });
});
