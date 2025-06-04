<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';
include 'header.php'; ?>

<div class="text-center mt-5">
    <h1 class="mb-4">Gestion du Stock</h1>

    <div class="d-grid gap-4 col-6 mx-auto">
        <a href="entree.php" class="btn btn-success btn-lg py-3 fs-4">
            ➕ Entrée de matériel
        </a>

        <a href="sortie.php" class="btn btn-danger btn-lg py-3 fs-4">
            ➖ Sortie de matériel
        </a>

        <a href="enregistrer_empreinte.php" class="btn btn-primary btn-lg py-3 fs-4">
            👤 Enregistrer une empreinte
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
