#include "ClientTCP.h"
//➡️ Inclut le fichier d’en-tête associé à cette classe (ClientTCP.h), où la classe est déclarée.

#include <iostream>
#include <cstring>
#include <unistd.h>
#include <arpa/inet.h>
//➡️ Inclusion des bibliothèques nécessaires :

//iostream : pour afficher (non utilisé ici, mais souvent utile).

//cstring : gestion de chaînes de caractères C (non utilisé ici non plus, mais peut servir).

//unistd.h : pour close() (fermeture du socket).

//arpa/inet.h : pour fonctions réseau comme inet_pton() et htons().



ClientTCP::ClientTCP(const std::string& ip, int port) : ip(ip), port(port), sockfd(-1) {}
//➡️ Initialise un objet ClientTCP avec :

//ip : adresse IP du serveur,

//port : port TCP du serveur,

//sockfd : initialisé à -1, signifie "socket non connecté".


bool ClientTCP::connecter() {
    sockfd = socket(AF_INET, SOCK_STREAM, 0);
    if (sockfd < 0) return false;
//➡️ Crée un socket TCP (SOCK_STREAM).
//S’il échoue (< 0), la fonction retourne false.

    sockaddr_in serv_addr{};
    serv_addr.sin_family = AF_INET;
    serv_addr.sin_port = htons(port);
    inet_pton(AF_INET, ip.c_str(), &serv_addr.sin_addr);
//➡️ Prépare l’adresse du serveur :

//AF_INET : IPv4,

//htons(port) : convertit le port en format réseau,

//inet_pton() : convertit l’adresse IP en format binaire.



    return connect(sockfd, (sockaddr*)&serv_addr, sizeof(serv_addr)) >= 0;
}

//➡️ Tente de se connecter.
//Retourne true si la connexion réussit (connect() retourne 0 ou plus), sinon false.


void ClientTCP::envoyer(const std::string& message) {
    send(sockfd, message.c_str(), message.size(), 0);
}
//➡️ Envoie un message texte au serveur via le socket.
//Le message est converti en chaîne C (c_str()), et sa taille est précisée.


void ClientTCP::deconnecter() {
    close(sockfd);
}
//➡️ Ferme proprement le socket (connexion TCP).
