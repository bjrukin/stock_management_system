<style>
    .profile-picture {
        position: relative;
    }
    .nhpc_logo img {}
</style>

<script>
    function printDiv() {
        var divContents = document.getElementById("printContent");
        var a = window.open('', 'PRINT ID CARD', 'height=250, width=400');

        a.document.write(divContents.outerHTML);
    }
</script>