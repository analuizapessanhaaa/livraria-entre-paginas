<?php

echo "PHP funcionando!<br>";

if (extension_loaded('pdo_sqlite')) {
    echo "SQLite: LIGADO";
} else {
    echo "SQLite: DESLIGADO";
}