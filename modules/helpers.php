<?php
/**
 * Utility functions for the Education Monitoring System
 */

/**
 * Format date for display
 */
function formatDate($date) {
    return date('d M, Y', strtotime($date));
}

/**
 * Calculate attendance percentage
 */
function calculatePercentage($obtained, $total) {
    if ($total == 0) return 0;
    return round(($obtained / $total) * 100, 1);
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'present': return 'badge-present';
        case 'absent': return 'badge-absent';
        case 'late': return 'badge-late';
        case 'active': return 'bg-success';
        case 'inactive': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data));
}
?>
