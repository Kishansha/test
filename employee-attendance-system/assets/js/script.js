.eas-admin-wrap {
    max-width: 1200px;
    margin-top: 20px;
}

.eas-filters {
    background: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.eas-filter-row {
    display: flex;
    align-items: flex-end;
    gap: 15px;
    flex-wrap: wrap;
}

.eas-filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.eas-filter-group label {
    font-weight: 500;
}

.eas-filter-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.status-completed {
    color: #155724;
    font-weight: 500;
}

.status-pending {
    color: #856404;
    font-weight: 500;
}

.eas-records-count {
    margin: 10px 0;
    font-style: italic;
    color: #646970;
}

/* Edit Modal Styles */
.eas-admin-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
}

.eas-admin-modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
}

.eas-admin-form-group {
    margin-bottom: 15px;
}

.eas-admin-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.eas-admin-form-group input[type="datetime-local"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.eas-admin-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

/* Responsive adjustments */
@media (max-width: 782px) {
    .eas-filter-row {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .eas-filter-actions {
        margin-left: 0;
        width: 100%;
    }
}