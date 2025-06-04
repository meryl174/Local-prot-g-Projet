#!/usr/bin/python3
import sys
sys.path.append('/home/meryl/.local/lib/python3.11/site-packages')

from pyfingerprint.pyfingerprint import PyFingerprint

try:
    position = int(sys.argv[1])
except (IndexError, ValueError):
    print("Position invalide.")
    exit(1)

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)
    if not f.verifyPassword():
        raise ValueError('Mot de passe incorrect pour le capteur.')
except Exception as e:
    print('Erreur de connexion au capteur : ' + str(e))
    exit(1)

try:
    if f.deleteTemplate(position):
        print(f"Empreinte à la position {position} supprimée.")
        exit(0)
    else:
        print("Échec de la suppression.")
        exit(1)
except Exception as e:
    print('Erreur lors de la suppression : ' + str(e))
    exit(1)
