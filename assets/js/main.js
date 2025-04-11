
document.addEventListener('DOMContentLoaded', function() {
    // Modal işlemleri
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (loginModal) {
        loginModal.addEventListener('show.bs.modal', function () {
            // Modal açıldığında yapılacak işlemler
        });
    }
    
    if (registerModal) {
        registerModal.addEventListener('show.bs.modal', function () {
            // Modal açıldığında yapılacak işlemler
        });
    }
    
    // Form validasyonları
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
