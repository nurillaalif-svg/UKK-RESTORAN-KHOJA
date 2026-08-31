// Script Interaktif Restoran Modern
document.addEventListener('DOMContentLoaded', function () {

    // 1. Toggle Tipe Pesanan (Dine In vs Take Away)
    const tipePesananDineIn = document.getElementById('tipe_dine_in');
    const tipePesananTakeAway = document.getElementById('tipe_take_away');
    const containerMeja = document.getElementById('container_nomor_meja');
    const containerAlamat = document.getElementById('container_alamat');
    const inputMeja = document.getElementById('nomor_meja');
    const inputAlamat = document.getElementById('alamat');

    function toggleTipePesanan() {
        if (!containerMeja || !containerAlamat) return;

        if (tipePesananDineIn && tipePesananDineIn.checked) {
            containerMeja.style.display = 'block';
            containerAlamat.style.display = 'none';
            if (inputMeja) inputMeja.required = true;
            if (inputAlamat) {
                inputAlamat.required = false;
                inputAlamat.value = '';
            }
        } else if (tipePesananTakeAway && tipePesananTakeAway.checked) {
            containerMeja.style.display = 'none';
            containerAlamat.style.display = 'block';
            if (inputMeja) {
                inputMeja.required = false;
                inputMeja.value = '';
            }
            if (inputAlamat) inputAlamat.required = true;
        }
    }

    if (tipePesananDineIn && tipePesananTakeAway) {
        tipePesananDineIn.addEventListener('change', toggleTipePesanan);
        tipePesananTakeAway.addEventListener('change', toggleTipePesanan);
        toggleTipePesanan(); // Initial call
    }

    // 2. Validasi Form Checkout (Frontend)
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            const nama = document.getElementById('nama_pelanggan').value.trim();
            const noHp = document.getElementById('no_hp').value.trim();

            if (!nama || !noHp) {
                e.preventDefault();
                alert('Silakan lengkapi Nama Pelanggan dan Nomor HP terlebih dahulu!');
                return false;
            }

            if (tipePesananDineIn && tipePesananDineIn.checked && inputMeja && !inputMeja.value.trim()) {
                e.preventDefault();
                alert('Silakan masukkan Nomor Meja untuk pemesanan Dine In!');
                inputMeja.focus();
                return false;
            }

            if (tipePesananTakeAway && tipePesananTakeAway.checked && inputAlamat && !inputAlamat.value.trim()) {
                e.preventDefault();
                alert('Silakan masukkan Alamat Pengiriman untuk pemesanan Take Away!');
                inputAlamat.focus();
                return false;
            }
        });
    }

    // 3. Auto Close Alert Toast
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 4000);
    });

});

// Helper Fungsi Format Rupiah di JS
function formatRupiahJS(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}
