#!/usr/bin/env python3
from pyfingerprint.pyfingerprint import PyFingerprint
import time
import hashlib

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)

    if not f.verifyPassword():
        raise ValueError('Mot de passe du capteur incorrect.')

except Exception as e:
    print('Erreur de connexion au capteur :', str(e))
    exit(1)

print('>>> Place ton doigt sur le capteur...')

# Attente de la première empreinte
while not f.readImage():
    pass

f.convertImage(0x01)

# Vérifie si l’empreinte existe déjà
result = f.searchTemplate()
positionNumber = result[0]

if positionNumber >= 0:
    print('Cette empreinte est déjà enregistrée à la position #' + str(positionNumber))
    exit(0)

print('>>> Retire ton doigt...')
time.sleep(2)

print('>>> Repose ton doigt pour vérification...')

# Attente de la deuxième empreinte
while not f.readImage():
    pass

f.convertImage(0x02)

# Vérifie si les deux empreintes correspondent
if f.compareCharacteristics() == 0:
    print('Les empreintes ne correspondent pas.')
    exit(1)

# Création du modèle et stockage
f.createTemplate()
positionNumber = f.storeTemplate()

print('Empreinte enregistrée à la position #' + str(positionNumber))

# Téléchargement des caractéristiques et hash
characteristics = f.downloadCharacteristics()
template_str = ''.join(map(str, characteristics))
hash_value = hashlib.sha256(template_str.encode('utf-8')).hexdigest()

print('SHA256 :', hash_value)
