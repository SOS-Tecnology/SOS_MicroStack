<style>
@media print {
    body {
        background: white;
    }
    a, button {
        display: none;
    }
}
</style>

<script>
window.onload = function() {
    window.print();
}
</script>
<?php include __DIR__ . '/../../../src/Views/Epp/show.php'; ?>