<?php
session_start();
unset($_SESSION['produits_scannes']);
unset($_SESSION['id_acces']);
echo "✅ Session nettoyée.";
