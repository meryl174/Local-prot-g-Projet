#!/bin/bash
# Indique que le script est un script Bash.

if [ -f /tmp/record_pid.txt ]; then
    # Vérifie si le fichier temporaire /tmp/record_pid.txt existe.
    # Ce fichier contient le PID de l'enregistrement en cours.
    kill $(cat /tmp/record_pid.txt)
    # Lit le PID du fichier et envoie un signal de terminaison au processus FFmpeg correspondant.
    rm /tmp/record_pid.txt
    # Supprime le fichier PID après avoir arrêté le processus.
    # Indique qu'aucun enregistrement n'est plus en cours.
    echo "✅ Enregistrement terminé."
    # Affiche un message de succès à l'utilisateur.
else
    # Si le fichier /tmp/record_pid.txt n'existe pas :
    echo "⚠️ Aucun enregistrement en cours."
    # Affiche un message d'avertissement, car aucun enregistrement n'a été trouvé.
fi