#!/bin/sh

# =====================
# Création de la config
# =====================

SECRET_CSRF=$(php -r "echo bin2hex(random_bytes(32));")
ADMIN_PASSWORD_HASH=$(php -r "echo password_hash('admin', PASSWORD_DEFAULT);")

echo "<?php

declare(strict_types=1);

// Dossier runtime (hors docroot, writable par le user PHP).
const DATA_DIR    = __DIR__ . '/../data';

// Chemin absolu vers le fichier SQLite. Dérivé de DATA_DIR par défaut.
const DB_PATH     = DATA_DIR . '/creneaux.sqlite';

// Secret HMAC pour le CSRF, 64 caractères hexadécimaux.
const SECRET_CSRF = '$SECRET_CSRF';


const MAJ_CHECK = false;
const ADMIN_PASSWORD_HASH = '$ADMIN_PASSWORD_HASH';


// Fuseau horaire (affecte la génération automatique des mois).
date_default_timezone_set('Europe/Paris');

" > app/config.php


# ==============================
# chmod sur le répertoire 'data' 
# ==============================

# php doit pouvoir l'écrire, cf. README
mkdir -p data && chmod 777 data/ # <= 777, config de dev !


# =============
# Config Apache
# =============

# mod rewrite
sudo a2enmod rewrite

# lien symbolique pour servir www
sudo rm -rf /var/www/html && sudo ln -s "$PWD/www" /var/www/html


# =======
# canary
# ======

echo "
setup done.
===========

Le serveur Apache va démarrer automatiquement. (via 'postStartCommand')
Si ce n'est pas le cas, lancer 'apache2ctl start' pour le lancer manuellement.
"