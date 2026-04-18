<?php
/**
 * Success/Error Alert template
 */
function showAlert($message, $type = 'success') {
    echo '
    <div class="alert alert-' . $type . ' alert-dismissible fade show shadow-sm border-0" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas ' . ($type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') . ' me-3 fs-4"></i>
            <div>
                <strong class="d-block">' . ($type == 'success' ? 'Success!' : 'Attention') . '</strong>
                <span class="small">' . $message . '</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>
