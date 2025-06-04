#!/bin/bash
# Indique que le script est un script Bash.

TIMESTAMP=$(date +"%Y%m%d_%H%M%S") # Crée un horodatage unique (ex: 20250604_094648).
OUTPUT="/media/elysio/ESD-USB/videos/session_$TIMESTAMP.mp4" # Définit le chemin complet du fichier MP4 de sortie sur le stockage externe.
echo $OUTPUT > /tmp/current_video_path.txt # Enregistre le chemin complet du fichier vidéo en cours dans un fichier temporaire.

ffmpeg -rtsp_transport tcp -i "rtsp://admin:Dark_IFun2607@10.10.0.100:554/h264Preview_01_main" \
-vcodec copy -t 3600 "$OUTPUT" > /dev/null 2>&1 &
# Commande FFmpeg :
# - Capture un flux RTSP (via TCP) depuis l'URL de la caméra.
# - '-vcodec copy' : Copie le flux vidéo directement sans ré-encodage (optimisation majeure, préserve la qualité, réduit CPU).
# - '-t 3600' : L'enregistrement s'arrête automatiquement après 1 heure (3600 secondes).
# - Redirige toutes les sorties (`> /dev/null 2>&1`) pour une exécution silencieuse.
# - Le '&' lance le processus en arrière-plan, permettant au script de continuer.

echo $! > /tmp/record_pid.txt # Enregistre l'ID du processus (PID) de FFmpeg dans un fichier temporaire pour permettre l'arrêt.