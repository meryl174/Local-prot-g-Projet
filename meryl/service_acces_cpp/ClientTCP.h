#ifndef CLIENTTCP_H
#define CLIENTTCP_H

//➡️ Directive de préprocesseur pour éviter les inclusions multiples du même fichier.
//Cela empêche des erreurs de compilation si ClientTCP.h est inclus plusieurs fois.

#include <string>
//➡️ On inclut la classe std::string, utilisée pour gérer les IPs et les messages.

class ClientTCP {
//➡️ Début de la déclaration de la classe.

public:
    ClientTCP(const std::string& ip, int port);
//➡️ Constructeur : prend l’IP et le port du serveur à contacter.

    bool connecter();
//➡️ Essaie de se connecter au serveur. Retourne true si ça réussit.

    void envoyer(const std::string& message);
//➡️ Envoie un message au serveur via le socket.

    void deconnecter();
//➡️ Ferme proprement le socket (met fin à la connexion).

private:
    std::string ip;
    int port;
    int sockfd;
};
//➡️ Données internes à la classe :

//ip : adresse IP du serveur cible.

//port : port TCP du serveur.

//sockfd : le socket (identifiant système du canal de communication).

#endif
//➡️ Fin du bloc #ifndef, qui évite l’inclusion multiple.