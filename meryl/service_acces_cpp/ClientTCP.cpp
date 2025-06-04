#include "ClientTCP.h"
#include <iostream>
#include <cstring>
#include <unistd.h>
#include <arpa/inet.h>

ClientTCP::ClientTCP(const std::string& ip, int port) : ip(ip), port(port), sockfd(-1) {}

bool ClientTCP::connecter() {
    sockfd = socket(AF_INET, SOCK_STREAM, 0);
    if (sockfd < 0) return false;

    sockaddr_in serv_addr{};
    serv_addr.sin_family = AF_INET;
    serv_addr.sin_port = htons(port);
    inet_pton(AF_INET, ip.c_str(), &serv_addr.sin_addr);

    return connect(sockfd, (sockaddr*)&serv_addr, sizeof(serv_addr)) >= 0;
}

void ClientTCP::envoyer(const std::string& message) {
    send(sockfd, message.c_str(), message.size(), 0);
}

void ClientTCP::deconnecter() {
    close(sockfd);
}