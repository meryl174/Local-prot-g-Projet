#include "DBManager.h"
#include "ClientTCP.h"
#include <iostream>
#include <iomanip>
#include <openssl/sha.h>
#include <sstream>
#include <fstream>
#include <cstdlib>
#include <wiringPi.h>
#include <unistd.h>
#include <algorithm>

std::string sha256(const std::string& data) {
    unsigned char hash[SHA256_DIGEST_LENGTH];
    SHA256(reinterpret_cast<const unsigned char*>(data.c_str()), data.length(), hash);
    std::ostringstream oss;
    for (int i = 0; i < SHA256_DIGEST_LENGTH; ++i)
        oss << std::hex << std::setw(2) << std::setfill('0') << (int)hash[i];
    return oss.str();
}

std::string lireEmpreinteDepuisCapteur() {
    std::string result;
    FILE* pipe = popen("python3 /var/www/html/Utilisateur/scanner_fingerprint.py", "r");
    if (!pipe) {
        std::cerr << "Erreur lors de l'exécution du script Python." << std::endl;
        return "";
    }

    char buffer[128];
    while (fgets(buffer, sizeof(buffer), pipe) != nullptr) {
        result += buffer;
    }
    pclose(pipe);

    result.erase(std::remove(result.begin(), result.end(), '\n'), result.end());
    return result;
}

int main() {
    wiringPiSetup();
    pinMode(0, OUTPUT);
    digitalWrite(0, LOW);

    DBManager db;
    if (!db.connecter()) {
        std::cerr << "❌ Erreur connexion BDD" << std::endl;
        return 1;
    }

    std::cout << "==> Lecture directe du capteur biométrique...\n";

    while (true) {
        std::string strPosition = lireEmpreinteDepuisCapteur();

        if (!strPosition.empty()) {
            int position = std::stoi(strPosition);
            int idUtilisateur = db.verifierPositionCapteur(position);

            if (idUtilisateur == -1) {
                std::cout << "❌ Empreinte inconnue." << std::endl;
            } else {
                std::cout << "👤 Utilisateur reconnu. ID : " << idUtilisateur << std::endl;

                int idAccesOuvert = db.getDernierAccesOuvert(idUtilisateur);
                ClientTCP client("127.0.0.1", 9000);

                if (idAccesOuvert != -1) {
                    std::cout << "🚪 Sortie détectée. Fermeture de session...\n";
                    db.cloturerAcces(idAccesOuvert);

                    if (client.connecter()) {
                        client.envoyer("STOP");
                        client.deconnecter();
                    }

                    std::string curl = "curl -s -X POST http://localhost:8888/cloturer_session.php -d 'id_acces=" + std::to_string(idAccesOuvert) + "'";
                    system(curl.c_str());

                    system("bash /home/elysio/scripts/stop_record.sh");

                    digitalWrite(0, HIGH);
                    sleep(5);
                    digitalWrite(0, LOW);

                    std::cout << "✅ Session fermée.\n";
                } else {
                    std::cout << "✅ Accès autorisé. Ouverture de la gâche...\n";
                    digitalWrite(0, HIGH);
                    sleep(5);
                    digitalWrite(0, LOW);

                    system("bash /home/elysio/scripts/start_record.sh &");

                    int idAcces = db.enregistrerAcces(idUtilisateur);
                    std::ofstream file("/var/www/html/Utilisateur/.current_acces", std::ios::out | std::ios::trunc);
                    if (file.is_open()) {
                        file << idAcces << std::endl;
                        file.close();
                        std::cout << "📝 ID Accès #" << idAcces << " enregistré dans .current_acces\n";
                    } else {
                        std::cerr << "❌ Impossible d'écrire .current_acces\n";
                    }

                    if (client.connecter()) {
                        client.envoyer("START");
                        client.deconnecter();
                    }

                    std::cout << "🎥 Session commencée. L'utilisateur pourra rescanner pour sortir.\n";
                }
            }
        }

        sleep(1);
    }

    return 0;
}
