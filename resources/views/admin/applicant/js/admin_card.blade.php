<script>
function printDiv() {
    const divContents = document.getElementById("print-content").outerHTML;
    const printWindow = window.open('');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
                <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
                <link rel="stylesheet" href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css') }}">             
            </head>
            <body>
                ${divContents}
            </body>
        </html>
    `);
    // printWindow.focus();
    // printWindow.print();
    // printWindow.close();
}
</script>