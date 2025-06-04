#!/usr/bin/env python3
from pyfingerprint.pyfingerprint import PyFingerprint
import sys
import time

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)

    if not f.verifyPassword():
        print('Erreur : mot de passe du capteur invalide.', file=sys.stderr)
        sys.exit(1)

except Exception as e:
    print('Erreur capteur :', str(e), file=sys.stderr)
    sys.exit(1)

try:
    print("Attente de l'empreinte...", file=sys.stderr)
    while not f.readImage():
        time.sleep(0.2)

    f.convertImage(0x01)
    result = f.searchTemplate()
    positionNumber = result[0]

    if positionNumber == -1:
        # Rien trouvé, renvoyer vide pour que le C++ ignore
        print('', end='')
    else:
        # C'est la seule ligne qui doit aller dans stdout
        print(str(positionNumber))

        try:
            with open("/var/www/html/Utilisateur/.empreinte.txt", "w") as fichier_out:
                fichier_out.write(str(positionNumber))
        except Exception as e:
            print("Erreur écriture fichier :", str(e), file=sys.stderr)
            sys.exit(1)

except Exception as e:
    print("Erreur pendant lecture :", str(e), file=sys.stderr)
    sys.exit(1)
