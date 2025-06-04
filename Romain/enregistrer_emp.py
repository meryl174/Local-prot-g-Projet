#!/var/www/html/venv/bin/python
import sys
import hashlib
import subprocess
import time

sys.path.append('/home/meryl/.local/lib/python3.11/site-packages')
from pyfingerprint.pyfingerprint import PyFingerprint

try:
    f = PyFingerprint('/dev/serial0', 57600, 0xFFFFFFFF, 0x00000000)
    if not f.verifyPassword():
        raise ValueError('Mot de passe incorrect pour le capteur.')
except Exception:
    sys.exit(1)

# Scan unique
while not f.readImage():
    pass
f.convertImage(0x01)

# Vérifie si déjà existante
result = f.searchTemplate()
positionNumber = result[0]
if positionNumber >= 0:
    sys.exit(1)

# Création et stockage
f.createTemplate()
positionNumber = f.storeTemplate()

# Calcul du hash
template = f.downloadCharacteristics()
template_str = ','.join(map(str, template))
hash_value = hashlib.sha256(template_str.encode('utf-8')).hexdigest()

# Sauvegarde dans un fichier
fichier_local = f'template_{positionNumber}.dat'
try:
    with open(fichier_local, 'w') as f_out:
        f_out.write(template_str)
except Exception:
    sys.exit(1)

# Transfert et import distant
ip_utilisateur = '10.10.0.10'
utilisateur = 'elysio'
fichier_distant = f'/home/{utilisateur}/scripts/empreintes/{fichier_local}'
script_import = f'/home/{utilisateur}/scripts/empreintes/importer_template.py'
cle_privee = '/var/www/html/.ssh/id_rsa'

try:
    subprocess.run([
        'scp', '-i', cle_privee, '-o', 'StrictHostKeyChecking=no',
        fichier_local, f'{utilisateur}@{ip_utilisateur}:{fichier_distant}'
    ], check=True)

    subprocess.run([
        'ssh', '-i', cle_privee, '-o', 'StrictHostKeyChecking=no',
        f'{utilisateur}@{ip_utilisateur}',
        f'python3 {script_import} {fichier_distant}'
    ], check=True)

except Exception:
    sys.exit(1)

# Retour formaté pour le PHP
print(f"position={positionNumber}")
sys.exit(0)
