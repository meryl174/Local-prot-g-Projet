#!/usr/bin/env python3
import sys
import time

sys.path.append('/home/elysio/.local/lib/python3.11/site-packages')
from pyfingerprint.pyfingerprint import PyFingerprint

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)
    if not f.verifyPassword():
        raise ValueError('Mot de passe incorrect pour le capteur.')
except Exception as e:
    print(f"[ERREUR] Impossible d'accéder au capteur : {e}")
    sys.exit(1)

print(f"[INFO] Nombre d'empreintes enregistrées : {f.getTemplateCount()}")

try:
    for i in range(0, f.getTemplateCount()):
        if f.deleteTemplate(i):
            print(f"[OK] Empreinte supprimée à la position #{i}")
        else:
            print(f"[ERREUR] Échec suppression position #{i}")
except Exception as e:
    print(f"[ERREUR] Suppression échouée : {e}")
    sys.exit(1)

print("[INFO] Capteur vidé avec succès.")
