<script>
    function printDiv() {
        // Create a new window for printing
        var printWindow = window.open('', '', 'height=800,width=800');

        // Get the content to print
        var content = document.getElementById('print-content').innerHTML;

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Admit Card - Print</title>
                <meta charset="UTF-8">
                <style>
                    /* A4 Page Setup - 210mm x 297mm */
                    @page {
                        size: A4;
                        margin: 5mm 5mm; /* Reduced margins to fit larger text */
                    }
                    
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body {
                        font-family: 'Noto Sans Devanagari', 'Kalimati', Arial, sans-serif;
                        font-size: 13pt; /* Increased from 11pt */
                        line-height: 1.3; /* Increased from 1.1 */
                        color: #000;
                        background: white;
                        width: 100%;
                    }
                    
                    /* Container styling */
                    .container {
                        width: 100%;
                        max-width: 100%;
                        padding: 0 5px;
                        margin: 0;
                    }
                    
                    /* Reset margins and paddings - compact top section */
                    .mt-3 { margin-top: 3px !important; }
                    .mt-4 { margin-top: 4px !important; }
                    .mt-5 { margin-top: 5px !important; }
                    .mb-1 { margin-bottom: 1px !important; }
                    .mb-3 { margin-bottom: 3px !important; }
                    .mb-5 { margin-bottom: 5px !important; }
                    .pt-3 { padding-top: 3px !important; }
                    .ps-3, .px-3 { padding-left: 5px !important; }
                    .pe-3, .px-3 { padding-right: 5px !important; }
                    .ps-5 { padding-left: 10px !important; }
                    
                    /* Page breaks - First page has student card + rules, Second page has official copy */
                    .container:first-child {
                        page-break-after: avoid;
                    }
                    
                    .container:nth-child(2) {
                        page-break-before: avoid;
                        page-break-after: always;
                    }
                    
                    .container:nth-child(3) {
                        page-break-before: always;
                    }
                    
                    /* Header styling */
                    h1, h2, h3, h4, h5 {
                        margin: 0;
                        padding: 0;
                    }
                    
                    h1 {
                        font-size: 16pt !important;
                        font-weight: bold;
                        line-height: 1.0 !important;
                    }
                    
                    h3 {
                        font-size: 8pt !important;
                        line-height: 1.0 !important;
                    }
                    
                    h4 {
                        font-size: 10pt !important;
                        line-height: 1.0 !important;
                    }
                    
                    h5 {
                        font-size: 7.5pt !important;
                        font-weight: bold;
                        line-height: 1.1 !important;
                    }
                    
                    /* Row and column layout */
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        width: 100%;
                        margin: 0;
                    }
                    
                    .col-md-2 { width: 16.66%; flex: 0 0 16.66%; }
                    .col-md-5 { width: 41.66%; flex: 0 0 41.66%; }
                    .col-md-6 { width: 50%; flex: 0 0 50%; }
                    .col-md-7 { width: 58.33%; flex: 0 0 58.33%; }
                    .col-md-8 { width: 66.66%; flex: 0 0 66.66%; }
                    
                    /* Text alignment */
                    .text-center { text-align: center !important; }
                    .align-content-center { align-content: center; }
                    .float-end { float: right; }
                    
                    /* Paragraph styling - compact for A4 */
                    p {
                        margin: 1px 0;
                        font-size: 11pt; /* Increased from 6.5pt */
                        line-height: 1.35;
                    }
                    
                    strong {
                        font-weight: bold;
                    }
                    
                    /* Image styling */
                    img {
                        max-width: 100%;
                        height: auto;
                        display: block;
                    }
                    
                    /* Table styling - compact */
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 6pt;
                    }
                    
                    table td {
                        border: 1px solid #000;
                        padding: 1px;
                        text-align: center;
                    }
                    
                    /* Specific adjustments for admit card sections */
                    .box-title {
                        margin: 0 !important;
                    }
                    
                    /* Dashed border for photo/signature areas */
                    [style*="border-style: dashed"],
                    [style*="border-style:dashed"] {
                        border: 1px dashed #000 !important;
                    }
                    
                    /* Hide elements that shouldn't print */
                    .btn, button, .no-print {
                        display: none !important;
                    }
                    
                    /* Rules section - increased font for readability */
                    .container:nth-child(2) {
                        margin-top: 30px; /* Increased spacing above rules */
                    }
                    
                    .container:nth-child(2) .row {
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                    
                    .container:nth-child(2) p {
                        font-size: 11pt; /* Increased from 7pt */
                        line-height: 1.4;
                        margin: 2px 0;
                    }
                    
                    .container:nth-child(2) h5 {
                        font-size: 8pt !important;
                        margin-bottom: 2px;
                    }
                    
                    /* Center elements */
                    center {
                        text-align: center;
                        display: block;
                        margin: 3px 0;
                    }
                    
                    .container:nth-child(2) center h4 {
                        font-size: 11pt !important; /* Reduced from 14pt */
                        line-height: 1.3;
                        margin-top: 5px;
                    }
                    
                    /* Color preservation */
                    [style*="color: #003893"],
                    [style*="color:#003893"] {
                        color: #003893 !important;
                    }
                    
                    [style*="color: red"],
                    [style*="color:red"] {
                        color: red !important;
                    }
                    
                    /* Ensure images print */
                    img {
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    /* Signature and photo positioning */
                    [style*="position: absolute"] {
                        position: absolute !important;
                    }
                    
                    [style*="z-index"] {
                        z-index: 99 !important;
                    }
                    
                    /* Dotted line separator - Hidden in print as requested */
                    [style*="border: 1px dashed"][style*="width: 90%"] {
                        display: none !important;
                    }
                    
                    /* Font size overrides - Maximum Increase */
                    [style*="font-size:41px"],
                    [style*="font-size: 41px"] {
                        font-size: 24pt !important;
                    }
                    
                    [style*="font-size:35px"],
                    [style*="font-size: 35px"] {
                        font-size: 20pt !important;
                    }
                    
                    [style*="font-size:26px"],
                    [style*="font-size: 26px"] {
                        font-size: 16pt !important;
                    }
                    
                    [style*="font-size:20px"],
                    [style*="font-size: 20px"] {
                        font-size: 14pt !important; 
                    }
                    
                    [style*="font-size:18px"],
                    [style*="font-size: 18px"] {
                        font-size: 13pt !important;
                    }
                    
                    [style*="font-size: 16px"],
                    [style*="font-size:16px"] {
                        font-size: 12pt !important;
                    }
                    
                    [style*="font-size: 14px"],
                    [style*="font-size:14px"] {
                        font-size: 11.5pt !important;
                    }
                    
                    [style*="font-size: 12px"],
                    [style*="font-size:12px"] {
                        font-size: 11pt !important;
                    }
                    
                    /* Line height adjustments */
                    /* Line height adjustments - increased for readability */
                    [style*="line-height: 0.4"],
                    [style*="line-height:0.4"] {
                        line-height: 1.3 !important; /* increased for better spacing */
                        padding-top: 5px !important;
                        padding-bottom: 5px !important;
                    }
                    
                    [style*="line-height: 1.3"] {
                        line-height: 1.4 !important;
                    }
                    
                    [style*="line-height:1.5"] {
                        line-height: 1.5 !important;
                    }
                    
                    /* Ensure proper display of flex containers */
                    [style*="display:flex"],
                    [style*="display: flex"] {
                        display: flex !important;
                    }
                    
                    [style*="justify-content:space-between"],
                    [style*="justify-content: space-between"] {
                        justify-content: space-between !important;
                    }
                    
                    /* Width and height preservation - scaled for A4 */
                    [style*="width:180px"],
                    [style*="width: 180px"] {
                        width: 110px !important;
                    }
                    
                    [style*="height:180px"],
                    [style*="height: 180px"] {
                        height: 110px !important;
                    }
                    
                    [style*="width:170px"],
                    [style*="width: 170px"] {
                        width: 105px !important;
                    }
                    
                    [style*="width:155px"],
                    [style*="width: 155px"] {
                        width: 95px !important;
                    }
                    
                    [style*="width:150px"],
                    [style*="width: 150px"] {
                        width: 90px !important;
                    }
                    
                    [style*="height:150px"],
                    [style*="height: 150px"] {
                        height: 90px !important;
                    }
                    
                    [style*="width:140px"],
                    [style*="width: 140px"] {
                        width: 85px !important;
                    }
                    
                    [style*="height:140px"],
                    [style*="height: 140px"] {
                        height: 85px !important;
                    }
                    
                    [style*="height:120px"],
                    [style*="height: 120px"] {
                        height: 70px !important;
                    }
                    
                    [style*="width:300px"],
                    [style*="width: 300px"] {
                        width: 180px !important;
                    }
                    
                    [style*="height: 85px"],
                    [style*="height:85px"] {
                        height: 50px !important;
                    }
                    
                    [style*="height: 50px"],
                    [style*="height:50px"] {
                        height: 30px !important;
                    }
                    
                    /* Object fit for images */
                    [style*="object-fit:contain"],
                    [style*="object-fit: contain"] {
                        object-fit: contain !important;
                    }
                    
                    [style*="object-position:center"],
                    [style*="object-position: center"] {
                        object-position: center !important;
                    }
                    
                    /* Margin adjustments - scaled */
                    [style*="margin-top: -45px"],
                    [style*="margin-top:-45px"] {
                        margin-top: -32px !important;
                    }
                    
                    [style*="margin-top: -55px"],
                    [style*="margin-top:-55px"] {
                        margin-top: -40px !important;
                    }
                    
                    [style*="margin-left:-20px"],
                    [style*="margin-left: -20px"] {
                        margin-left: -15px !important;
                    }
                    
                    [style*="margin-top: -5px"],
                    [style*="margin-top:-5px"] {
                        margin-top: 2px !important; /* Reset caused overlap */
                    }
                    
                    [style*="margin-top: -10px"],
                    [style*="margin-top:-10px"] {
                        margin-top: 0px !important; /* Reset caused overlap */
                    }
                    
                    [style*="margin-top: 20px"],
                    [style*="margin-top:20px"] {
                        margin-top: 12px !important;
                    }
                    
                    /* Padding adjustments - scaled */
                    [style*="padding: 20px"],
                    [style*="padding:20px"] {
                        padding: 8px !important;
                    }
                    
                    [style*="padding:10px"],
                    [style*="padding: 10px"] {
                        padding: 6px !important;
                    }
                    
                    [style*="padding:5px"],
                    [style*="padding: 5px"] {
                        padding: 3px !important;
                    }
                    
                    [style*="padding:3px"],
                    [style*="padding: 3px"] {
                        padding: 2px !important;
                    }
                    
                    /* Text transform */
                    [style*="text-transform:capitalize"],
                    [style*="text-transform: capitalize"] {
                        text-transform: capitalize !important;
                    }
                    
                    /* Word spacing */
                    [style*="word-spacing: 2px"],
                    [style*="word-spacing:2px"] {
                        word-spacing: 1px !important;
                    }
                    
                    /* Ensure borders print */
                    [style*="border: 1px solid"],
                    [style*="border:1px solid"],
                    [style*="border-style: solid"],
                    [style*="border-style:solid"] {
                        border: 1px solid #000 !important;
                    }
                    
                    [style*="border-width: 1px"],
                    [style*="border-width:1px"] {
                        border-width: 1px !important;
                    }
                    
                    /* Compact the main card border box */
                    [style*="border: 1px solid #000"][style*="padding: 20px"] {
                        padding: 6px !important;
                    }
                    
                    /* Print media query */
                    @media print {
                        body {
                            margin: 0;
                            padding: 0;
                        }
                        
                        /* Page 1: Student card + Rules */
                        .container:nth-child(2) {
                            page-break-after: always;
                        }
                        
                        /* Page 2: Official copy */
                        .container:nth-child(3) {
                            page-break-before: always;
                        }
                        
                        /* Ensure colors print */
                        * {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                    }
                </style>
            </head>
            <body>
                ${content}
            </body>
            </html>
        `);

        printWindow.document.close();

        // Wait for content to load before printing
        printWindow.onload = function () {
            setTimeout(function () {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 500);
        };
    }
</script>