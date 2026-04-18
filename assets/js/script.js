document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Toggle Logic
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            // Check if we need to adjust content margin if not using flex
            if (window.innerWidth <= 768) {
                sidebar.style.marginLeft = sidebar.classList.contains('active') ? '-280px' : '0';
            }
        });
    }

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Confirmation for deletes
    const deleteButtons = document.querySelectorAll('.btn-outline-danger');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.onclick) return; // Already handled by inline JS
            if (!confirm('Are you sure you want to proceed with this deletion? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
