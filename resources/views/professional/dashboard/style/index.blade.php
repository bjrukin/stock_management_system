<style>
    .timeline-wrapper {
        position: relative;
        padding: 50px 0 40px;
        margin: 0 auto;
        max-width: 1200px;
    }

    .timeline-line {
        position: absolute;
        top: 66px;
        left: 5%;
        right: 5%;
        height: 2px;
        background-color: #e2e8f0;
        z-index: 1;
    }

    .timeline-items {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }

    .timeline-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0 10px;
        position: relative;
    }

    .timeline-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #cbd5e1;
        margin-bottom: 25px;
        position: relative;
        z-index: 3;
        box-shadow: 0 0 0 4px #fff;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-item.active .timeline-circle {
        border-color: #3b82f6;
        border-width: 2px;
        background-color: #3b82f6;
        box-shadow: 0 0 0 4px #fff, 0 0 0 7px rgba(59, 130, 246, 0.3);
    }

    .timeline-item .timeline-circle i {
        color: #10b981;
        font-weight: bold;
    }


    .timeline-box {
        width: 100%;
        border-radius: 8px;
        padding: 24px 20px;
        text-align: center;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .timeline-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border-color: #cbd5e1;
    }

    .timeline-item.active .timeline-box {
        background-color: #fff;
        border-color: #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
    }

    /* Status Typography & Colors */
    .box-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
        margin-bottom: 8px;
        letter-spacing: -0.01em;
    }

    .box-status {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 9999px;
        display: inline-block;
        margin-bottom: 12px;
    }

    .box-details {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
        font-weight: 500;
    }

    .box-divider {
        height: 1px;
        background-color: #f1f5f9;
        margin: 12px auto;
        width: 60%;
    }

    /* Refined Status Palette */
    .status-rejected .box-status {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-rejected .box-title {
        color: #991b1b;
    }

    .status-accepted .box-status {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .status-accepted .box-title {
        color: #166534;
    }

    .status-progress .box-status,
    .status-re-exam .box-status {
        background-color: #dbeafe;
        color: #2563eb;
    }

    .status-progress .box-title,
    .status-re-exam .box-title {
        color: #1e40af;
    }

    .status-pending .box-status,
    .status-onhold .box-status,
    .status--- .box-status {
        background-color: #f1f5f9;
        color: #64748b;
    }

    .status-pending .box-title,
    .status-onhold .box-title,
    .status--- .box-title {
        color: #475569;
    }


    /* Responsive */
    @media (max-width: 991.98px) {
        .timeline-line {
            display: none !important;
        }

        .timeline-items {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0;
            width: 100% !important;
        }

        .timeline-item {
            display: grid !important;
            grid-template-columns: 50px 1fr !important;
            width: 100% !important;
            padding: 0;
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background-color: #e2e8f0;
            z-index: 1;
        }

        .timeline-circle {
            grid-column: 1;
            margin: 8px auto 0;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            z-index: 2;
            box-shadow: 0 0 0 4px #fff;
        }

        .timeline-box {
            grid-column: 2;
            text-align: left;
            min-height: auto;
            padding: 20px;
            width: 100%;
            align-items: flex-start;
        }

        .box-status {
            margin-left: 0;
            margin-bottom: 8px;
        }

        .box-divider {
            margin: 10px 0;
            width: 100%;
        }

        .timeline-item.active .timeline-circle {
            box-shadow: 0 0 0 4px #fff;
        }
    }

    @media (max-width: 575.98px) {
        .timeline-wrapper {
            padding: 30px 0;
        }

        .timeline-item {
            grid-template-columns: 44px 1fr !important;
        }

        .timeline-item:not(:last-child)::before {
            left: 21px;
        }

        .timeline-circle {
            width: 30px;
            height: 30px;
        }
    }

    .content-page {
        padding-bottom: 60px !important;
    }
</style>