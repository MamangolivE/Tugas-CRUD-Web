var modal = document.getElementById("modalHapus");
var btnKonfirmasiHapus = document.getElementById("btnKonfirmasiHapus");
var btnBatal = document.getElementById("btnBatal");
var hewanNamaSpan = document.getElementById("hewanNama");
    
var buttons = document.querySelectorAll(".btn-modal-hapus");

buttons.forEach(function(btn) {
    btn.addEventListener("click", function() {
        var hewanId = this.getAttribute("data-bs-id");
        var hewanNama = this.getAttribute("data-bs-nama");

        hewanNamaSpan.textContent = hewanNama;
        
        btnKonfirmasiHapus.href = "hapus.php?id=" + hewanId;
            
        modal.style.display = "block";
    });
});

btnBatal.onclick = function() {
    modal.style.display = "none";
}
