#!/usr/bin/env python3
import sys
import hashlib
from pyfingerprint.pyfingerprint import PyFingerprint

# Ajoute le chemin au module si nécessaire
sys.path.append('/home/meryl/.local/lib/python3.11/site-packages')

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)
    if not f.verifyPassword():
        raise ValueError('Mot de passe incorrect pour le capteur.')
except Exception as e:
    print('Erreur lors de l\'initialisation :', str(e))
    exit(1)

if len(sys.argv) != 2:
    print("Usage: supprimer_emp.py <empreinteHashHex>")
    exit(1)

empreinteHex = sys.argv[1]
empreinteBytes = bytes.fromhex(empreinteHex)

# Comparer chaque empreinte enregistrée
for i in range(0, f.getStorageCapacity()):
    if f.loadTemplate(i, 0x01):
        characteristics = f.downloadCharacteristics(0x01)
        temp_str = ''.join(map(str, characteristics))
        hash_val = hashlib.sha256(temp_str.encode('utf-8')).hexdigest()
        if hash_val == empreinteHex:
            if f.deleteTemplate(i):
                print(f"Empreinte supprimée à la position {i}")
                exit(0)
            else:
                print("Erreur lors de la suppression.")
                exit(1)

print("Empreinte non trouvée.")
exit(1)
